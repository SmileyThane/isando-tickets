<?php


namespace App\Repositories;

use App\CompanyProduct;
use App\LimitationGroupHasModel;
use App\Permission;
use App\Product;
use App\ProductClient;
use App\ProductCompanyUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductRepository
{
    public function validate($request)
    {
        $params = [
            'product_name' => 'required',
            'product_description' => 'required',
        ];
        $validator = Validator::make($request->all(), $params);
        if ($validator->fails()) {
            return $validator->errors();
        }
        return true;
    }

    public function all($request)
    {
        $employee = Auth::user()->employee()->with('assignedToClients')->first();
        $companyId = $employee->company_id;
        $productIds = null;
        if ($employee->hasPermissionId(Permission::CLIENT_GROUPS_DEPENDENCY)) {
            $productGroups = (new LimitationGroupRepository())->getAssignedLimitationGroupByModel(Product::class);

            if ($productGroups) {
                $assignedClients = LimitationGroupHasModel::query()
                    ->whereIn('limitation_group_id', $productGroups->pluck('id')->toArray())
                    ->get();
                $productIds = $assignedClients->pluck('model_id')->toArray();
            }

        } elseif ($employee->hasPermissionId(Permission::EMPLOYEE_CLIENT_ACCESS)) {
            $clientIds = $employee->assignedToClients->pluck('client_id')->toArray();
            if ($clientIds) {
                $productIds = ProductClient::where('client_id', $clientIds)->get()->pluck('product_id')->toArray();
            }
        } else {
            $productIds = CompanyProduct::where('company_id', $companyId)->get()->pluck('product_id')->toArray();
        }

        $productsData = Product::whereIn('id', $productIds);
        if (!empty($request->search)) {
            $productsData
                ->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        $products = $productsData->with('category')->get();

        $orderFunc = function ($item, $key) use ($request) {
            switch ($request->sort_by ?? 'name') {
                case 'id':
                    return $item->id; //'product_code', 'name', 'photo', 'description'
                case 'product_code':
                    $item->product_code;
                case 'name':
                    return mb_strtolower($item->name);
                case 'photo':
                    return $item->mb_strtolower($item->photo);
                case 'description':
                    return $item->mb_strtolower($item->description);
                case 'full_name':
                    return mb_strtolower($item->full_name);
            }
        };

        if ($request->sort_val === 'false') {
            $products = $products->sortBy($orderFunc);
        } else {
            $products = $products->sortByDesc($orderFunc);
        }

        return $products->paginate($request->per_page ?? $products->count() + 1);
    }

    public function find($id)
    {
        return Product::where('id', $id)->with('employees', 'clients.clientData', 'category', 'attachments')->first();
    }

    public function create(Request $request)
    {
        $product = $this->save(new Product(), $request);
        $request->product_id = $product->id;
        $request->company_id = Auth::user()->employee->company_id;
        (new CompanyRepository())->attachProduct($request);
        return $product;
    }

    private function save($product, $request)
    {
        $product->name = $request->product_name;
        $product->description = $request->product_description;
        $product->photo = $request->product_photo;
        $product->category_id = $request->category_id ?? $product->category_id;
        $product->product_code = $request->product_code;
        $product->save();
        $files = array_key_exists('files', $request->all()) ? $request['files'] : [];
        foreach ($files as $file) {
            (new FileRepository())->store($file, $product->id, Product::class);
        }
        return $product;
    }

    public function update(Request $request, $id)
    {
        return $this->save(Product::find($id), $request);
    }

    public function delete($id)
    {
        $result = false;
        CompanyProduct::where('product_id', $id)->delete();
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            $result = true;
        }
        return $result;
    }

    public function attachEmployee(Request $request)
    {
        ProductCompanyUser::firstOrCreate(
            ['client_id' => $request->product_id,
                'company_user_id' => $request->company_user_id]
        );
        return true;
    }

    public function detachEmployee($id)
    {
        $result = false;
        $client = ProductCompanyUser::find($id);
        if ($client) {
            $client->delete();
            $result = true;
        }
        return $result;
    }

    public function attachClient(Request $request)
    {
        ProductClient::firstOrCreate(
            ['client_id' => $request->client_id,
                'product_id' => $request->product_id]
        );
        return true;
    }

    public function detachClient($id)
    {
        $result = false;
        $client = ProductClient::find($id);
        if ($client) {
            $client->delete();
            $result = true;
        }
        return $result;
    }
}
