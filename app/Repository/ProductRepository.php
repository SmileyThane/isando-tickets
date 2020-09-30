<?php


namespace App\Repository;


use App\CompanyProduct;
use App\Product;
use App\ProductCategory;
use App\ProductClient;
use App\ProductCompanyUser;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductRepository
{

    protected $companyRepo;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepo = $companyRepository;
    }

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
        if (!$employee->hasRole(Role::COMPANY_CLIENT)) {
            $productIds = CompanyProduct::where('company_id', $companyId);
        } else {
            $clientIds = $employee->assignedToClients->pluck('client_id')->toArray();
            $productIds = ProductClient::where('client_id', $clientIds);
        }
        if ($request->search !== '') {
            $productIds->whereHas(
                'productData',
                function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('description', 'like', '%' . $request->search . '%');
                }
            );
        }
        $products = Product::whereIn('id', $productIds->get()->pluck('id')->toArray());
        return $products
            ->orderBy($request->sort_by ?? 'id', $request->sort_val === 'false' ? 'asc' : 'desc')
            ->paginate($request->per_page ?? $products->count());
    }

    public function find($id)
    {
        return Product::where('id', $id)->with('employees', 'clients.clientData', 'category')->first();
    }

    public function create(Request $request)
    {
        $product = new Product();
        $product->name = $request->product_name;
        $product->description = $request->product_description;
        $product->photo = $request->product_photo;
        $product->category_id = $request->category_id ?? null;
        $product->save();
        $request->product_id = $product->id;
        $request->company_id = Auth::user()->employee->company_id;
        $this->companyRepo->attachProduct($request);
        return $product;
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        $product->name = $request->product_name;
        $product->description = $request->product_description;
        $product->photo = $request->product_photo;
        $product->category_id = $request->category_id ?? null;
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
