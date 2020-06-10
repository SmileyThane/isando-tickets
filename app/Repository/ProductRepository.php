<?php


namespace App\Repository;


use App\Client;
use App\ClientCompanyUser;
use App\Company;
use App\CompanyProduct;
use App\Product;
use App\ProductClient;
use App\ProductCompanyUser;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductRepository
{

    public function validate($request, $new = true)
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

    public function all(Request $request)
    {
        $employee = Auth::user()->employee;
        $companyId = $employee->company_id;
        $products = null;
        if (!$employee->hasRole(Role::COMPANY_CLIENT)) {
            $products = CompanyProduct::where('company_id', $companyId)->with('productData')->paginate();
        }
        return $products;
    }

    public function find($id)
    {
        return Product::where('id', $id)->with('employees', 'clients')->first();
    }

    public function create(Request $request)
    {
        $product = new Product();
        $product->name = $request->product_name;
        $product->description = $request->product_description;
        $product->photo = $request->product_photo;
        $product->save();
        return $product;
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        $product->name = $request->product_name;
        $product->description = $request->product_description;
        $product->photo = $request->product_photo;
        $product->save();
        return $product;
    }

    public function delete($id)
    {
        $result = false;
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            $result = true;
        }
        return $result;
    }

    public function attachEmployee(Request $request)
    {
        $clientCompanyUser = ProductCompanyUser::firstOrCreate(
            ['client_id' => $request->product_id],
            ['company_user_id' => $request->company_user_id]
        );
        return true;
    }

    public function detachEmployee(Request $request, $id)
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
        $clientCompanyUser = ProductClient::firstOrCreate(
            ['client_id' => $request->client_id],
            ['product_id' => $request->product_id]
        );
        return true;
    }

    public function detachClient(Request $request, $id)
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
