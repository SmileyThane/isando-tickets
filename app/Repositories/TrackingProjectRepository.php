<?php


namespace App\Repositories;

use App\Client;
use App\Http\Controllers\API\Tracking\Filters\Projects\Billable;
use App\Http\Controllers\API\Tracking\Filters\Projects\Clients;
use App\Http\Controllers\API\Tracking\Filters\Projects\Coworkers;
use App\LimitationGroupHasModel;
use App\Permission;
use App\Product;
use App\Ticket;
use App\Tracking;
use App\TrackingProject;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

class TrackingProjectRepository
{

    public function validate($request, $new = true)
    {
        $params = [
            'project' => 'required|string',
//            'department' => 'required|string',
//            'profit_center' => 'required|string',
//            'productId' => 'required_without:product.id|exists:App\Product,id',
//            'product.id' => 'required_without:productId|exists:App\Product,id',
            'clientId' => 'required_without:client.id|exists:App\Client,id',
            'client.id' => 'required_without:clientId|exists:App\Client,id',
            'color' => 'required|string',
            'billableByDefault' => 'boolean',
            'rate' => 'nullable|numeric',
            'rate_from' => 'nullable|numeric',
            'rate_from_date' => 'nullable|date',
        ];
        $validator = Validator::make($request->all(), $params);
        if ($validator->fails()) {
            return $validator->errors();
        }
        return true;
    }

    public function all(Request $request)
    {
        $includeArchives = $request->get('includeArchives', false);
        $trackingProjects = collect([]);
        if (Auth::user()->employee->getPermissionIds(54, 55)) {
            $trackingProjects = TrackingProject::with('Team');
            if ($includeArchives === 'false' || !$includeArchives) {
                $trackingProjects->where(function ($query) {
                    $query->where('status', '<>', TrackingProject::$STATUS_ARCHIVED)
                        ->orWhereNull('status');
                });
            }
            if (Auth::user()->employee->hasPermissionId([Permission::CLIENT_GROUPS_DEPENDENCY])) {
                $assignedClientIds = [];
                $assignedProductIds = [];
                $productGroups = (new LimitationGroupRepository())->getAssignedLimitationGroupByModel(Product::class);
                $clientGroups = (new LimitationGroupRepository())->getAssignedLimitationGroupByModel(Client::class);
                if ($clientGroups) {
                    $assignedClients = LimitationGroupHasModel::query()
                        ->whereIn('limitation_group_id', $clientGroups->pluck('id')->toArray())
                        ->get();
                    $assignedClientIds = $assignedClients ? $assignedClients->pluck('model_id')->toArray() : [];
                }
                if ($productGroups) {
                    $assignedProducts = LimitationGroupHasModel::query()
                        ->whereIn('limitation_group_id', $productGroups->pluck('id')->toArray())
                        ->get();
                    $assignedProductIds = $assignedProducts ? $assignedProducts->pluck('model_id')->toArray() : [];
                }
                $trackingProjects->whereIn('client_id', $assignedClientIds)
                    ->orWhereIn('product_id', $assignedProductIds);
            }

            $trackingProjects->with('Product')
                ->with(['Client' => static function ($query) use ($request) {
                    if ($request->column === 'client.name' && $request->has('column') && $request->has('direction')) {
                        if ((string)$request->direction === 'true') {
                            $request->direction = 'desc';
                        }
                        if ((string)$request->direction === 'false') {
                            $request->direction = 'asc';
                        }
                        return $query->orderBy('project', $request->direction);
                    }
                }]);

//                ->where(function($query) {
//                    $query->where('status', '!=', TrackingProject::$STATUS_ARCHIVED)
//                        ->orWhereNull('status');
//                });
            if (Auth::user()->employee->getPermissionIds(54)) {
                $trackingProjects->MyCompany();
            } elseif (Auth::user()->employee->getPermissionIds(55)) {
                $trackingProjects->MyTeams();
            }

            if ($request->has('search')) {
                $trackingProjects->where('project', 'LIKE', "%{$request->get('search')}%");
            }
            if ($request->has('column') && $request->get('name') === 'name' && $request->has('direction')) {
                if ((string)$request->direction === 'true') {
                    $request->direction = 'desc';
                }
                if ((string)$request->direction === 'false') {
                    $request->direction = 'asc';
                }
                $trackingProjects->orderBy($request->column, $request->direction);
            }
        }

        $trackingProjects = app(Pipeline::class)
            ->send($trackingProjects)
            ->through([
                Billable::class,
                Clients::class,
                Coworkers::class
            ])
            ->thenReturn();
//        dd($trackingProjects->toSql());
        return $trackingProjects
            ->paginate($request->per_page ?? $trackingProjects->count());
    }

    public function find($id)
    {
        return TrackingProject::where('id', $id)->with('client', 'product', 'team')->first();
    }

    public function create(Request $request)
    {
        if (!Auth::user()->employee->getPermissionIds(53)) {
            throw new AccessDeniedException();
        }
        $trackingProject = new TrackingProject();
        $trackingProject->project = $request->project;
//        $trackingProject->department = $request->department;
//        $trackingProject->profit_center = $request->profit_center;
        $trackingProject->product_id = $request->product['id'] ?? $request->productId;
        $trackingProject->client_id = $request->client['id'] ?? $request->clientId;
        $trackingProject->color = $request->color ?? $this->genHexColor();
        $trackingProject->company_id = Auth::user()->employee->companyData->id;
        $trackingProject->team_id = $request->team['id'] ?? $request->teamId;
        if ($request->has('billableByDefault')) {
            $trackingProject->billable_by_default = $request->billable_by_default;
        }
        if ($request->has('rate')) {
            $trackingProject->rate = $request->rate;
        }
        if ($request->has('rate_from')) {
            $trackingProject->rate_from = $request->rate_from;
        }
        if ($request->has('rate_from_date')) {
            $trackingProject->rate_from_date = $request->rate_from_date;
        }
        $trackingProject->save();
        return $trackingProject;
    }

    public function genHexColor()
    {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->employee->getPermissionIds(57, 60)) {
            throw new AccessDeniedException();
        }
        $trackingProject = TrackingProject::find($id);
        if ($request->has('project')) {
            $trackingProject->project = $request->project;
        }
        if ($request->has('department')) {
            $trackingProject->department = $request->department;
        }
        if ($request->has('profit_center')) {
            $trackingProject->profit_center = $request->profit_center;
        }
        if ($request->has('product')) {
            if (is_null($request->product)) {
                $trackingProject->product_id = null;
            } else {
                $trackingProject->product_id = $request->product['id'];
            }
        }
        if ($request->has('client')) {
            $trackingProject->client_id = $request->client['id'];
        }
        if ($request->has('team')) {
            if (is_null($request->team)) {
                $trackingProject->team_id = null;
            } else {
                $trackingProject->team_id = $request->team['id'];
            }
        }
        if ($request->has('color')) {
            $trackingProject->color = $request->color;
        }
        if ($request->has('billable_by_default')) {
            $trackingProject->billable_by_default = $request->billable_by_default;
        }
        if ($request->has('rate')) {
            $trackingProject->rate = $request->rate;
        }
        if ($request->has('rate_from') && $trackingProject->rate_from !== $request->rate_from) {
            $trackingProject->rate_from = $request->rate_from;
        }
        if ($request->has('rate_from_date')) {
            $trackingProject->rate_from_date = $request->rate_from_date;
        }
        if ($request->has('rate_type_from') && $request->get('rate_type_from') === 2) {
            $trackers = $trackingProject->Trackers()->get();
            foreach ($trackers as $tracker) {
                $tracker->rate = $trackingProject->rate_from;
                $tracker->save();
            }
            $trackingProject->rate = $trackingProject->rate_from;
            $trackingProject->rate_from = null;
            $trackingProject->rate_from_date = null;
        }
        $trackingProject->save();
        return $trackingProject;
    }

    public function delete($id)
    {
        if (!Auth::user()->employee->getPermissionIds(58)) {
            throw new AccessDeniedException();
        }
        $result = false;
        $trackingProject = TrackingProject::find($id);
        if ($trackingProject) {
            $trackingProject->delete();
            $result = true;
        }
        return $result;
    }

    public function toggleFavorite($id)
    {
        $project = TrackingProject::find($id);
        if ($project->is_favorite) {
            Auth::user()->favoriteTrackingProjects()->detach($project->id);
        } else {
            Auth::user()->favoriteTrackingProjects()->attach($project);
        }
        return true;
    }

    public function toggleArchive($id)
    {
        if (!Auth::user()->employee->getPermissionIds(58)) {
            throw new AccessDeniedException();
        }
        $result = false;
        $trackingProject = TrackingProject::find($id);
        if ($trackingProject) {
            if ($trackingProject->status === TrackingProject::$STATUS_ARCHIVED) {
                $trackingProject->status = '';
                $status = Tracking::$STATUS_STOPPED;
            } else {
                $trackingProject->status = TrackingProject::$STATUS_ARCHIVED;
                $status = Tracking::$STATUS_ARCHIVED;
            }
            $trackingProject->save();

            Tracking::where([
                ['entity_type', '=', TrackingProject::class],
                ['entity_id', '=', $trackingProject->id]
            ])->update(['status' => $status]);
            $result = true;
        }
        return $result;
    }

    // not used
    public function getClients(Request $request)
    {
        $productIds = Auth::user()
            ->employee
            ->companyData
            ->products
            ->pluck('product_id');
        $products = Product::with('clients')
            ->whereIn('id', $productIds)
            ->whereHas('clients')
            ->get();
        $clientIds = $products->map(function ($product) {
            return $product->clients;
        })->collapse()->pluck('client_id')->all();
        $clients = Client::whereIn('id', $clientIds);
        if ($request->has('search') && $request->search) {
            $clients->where('name', 'LIKE', "%{$request->search}%");
        }
        return $clients->get();
    }

    public function getProducts(Request $request)
    {
        $productIds = Auth::user()
            ->employee
            ->companyData
            ->products
            ->pluck('product_id');
        $products = Product::whereIn('id', $productIds);
        if ($request->has('search') && $request->search) {
            $products->where('name', 'LIKE', "%{$request->search}%");
        }
        return $products->get();
    }

    public function getTickets(Request $request)
    {
        $companyUser = Auth::user()->employee;
        $tickets = Ticket::query();
        $tickets->where(function ($ticketsQuery) use ($companyUser) {
            $ticketsQuery->where('to_company_user_id', $companyUser->id)
                ->orWhere('contact_company_user_id', $companyUser->id);
        });
        if ($request->has('search')) {
            $tickets->where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . trim($request->search) . '%')
                    ->orWhere('description', 'like', '%' . trim($request->search) . '%');
            });
        }
        return $tickets->get();
    }
}
