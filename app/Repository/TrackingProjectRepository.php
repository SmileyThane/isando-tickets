<?php


namespace App\Repository;

use App\Client;
use App\Company;
use App\Product;
use App\Team;
use App\TeamCompanyUser;
use App\TrackingProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TrackingProjectRepository
{

    public function validate($request, $new = true)
    {
        $params = [
            'name' => 'required',
            'productId' => 'required|exists:App\Product,id',
            'clientId' => 'required|exists:App\Client,id',
            'color' => 'required|string',
            'billableByDefault' => 'boolean',
            'rate' => 'numeric',
            'rateFrom' => 'numeric',
        ];
        $validator = Validator::make($request->all(), $params);
        if ($validator->fails()) {
            return $validator->errors();
        }
        return true;
    }

    public function all(Request $request)
    {
        $productIds = Auth::user()
            ->employee
            ->companyData
            ->products
            ->pluck('product_id');
        $trackingProjects = TrackingProject::with('Product', 'Client')
            ->whereIn('product_id', $productIds);
        if ($request->has('search')) {
            $trackingProjects->where('name', 'LIKE', "%{$request->get('search')}%");
        }
        return $trackingProjects
//            ->orderBy($request->sort_by ?? 'id', $request->sort_val === 'false' ? 'asc' : 'desc')
            ->paginate($request->per_page ?? $trackingProjects->count());
    }


    public function find($id)
    {
        return TrackingProject::where('id', $id)->with('client')->first();
    }

    public function create(Request $request)
    {
        $trackingProject = new TrackingProject();
        $trackingProject->name = $request->name;
        $trackingProject->product_id = $request->productId;
        $trackingProject->client_id = $request->clientId;
        $trackingProject->color = $request->color;
        $trackingProject->save();
        return $trackingProject;
    }

    public function update(Request $request, $id)
    {
        $trackingProject = TrackingProject::find($id);
        if ($request->has('name')) {
            $trackingProject->name = $request->name;
        }
        if ($request->has('productId')) {
            $trackingProject->product_id = $request->productId;
        }
        if ($request->has('clientId')) {
            $trackingProject->client_id = $request->clientId;
        }
        if ($request->has('color')) {
            $trackingProject->color = $request->color;
        }
        if ($request->has('billableByDefault')) {
            $trackingProject->billable_by_default = $request->billableByDefault;
        }
        if ($request->has('rate')) {
            $trackingProject->rate = $request->rate;
        }
        if ($request->has('rate_from')) {
            $trackingProject->rate_from = $request->rateFrom;
        }
        $trackingProject->save();
        return $trackingProject;
    }

    public function delete($id)
    {
        $result = false;
        $trackingProject = TrackingProject::find($id);
        if ($trackingProject) {
            $trackingProject->delete();
            $result = true;
        }
        return $result;
    }

    public function getClients() {
        $productIds = Auth::user()
            ->employee
            ->companyData
            ->products
            ->pluck('product_id');
        $products = Product::with('clients')
            ->whereIn('id', $productIds)
            ->whereHas('clients')
            ->get();
        $clientIds = $products->map(function($product) {
            return $product->clients;
        })->collapse()->pluck('client_id')->all();
        return Client::whereIn('id', $clientIds)->get();
    }

    public function getProducts() {
        $productIds = Auth::user()
            ->employee
            ->companyData
            ->products
            ->pluck('product_id');
        $products = Product::whereIn('id', $productIds)
            ->get();
        return $products;
    }

}
