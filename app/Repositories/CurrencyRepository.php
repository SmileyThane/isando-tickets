<?php


namespace App\Repositories;

use App\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CurrencyRepository
{
    private $rules = [
        'create' => [
            'name' => 'required|string',
            'slug' => 'required|string',
            'symbol' => 'required|string',
        ],
        'update' => [
            'name' => 'string',
            'slug' => 'string',
            'symbol' => 'string',
        ],
    ];

    public function validate($request, $new = true)
    {
        $params = $this->rules['create'];
        if (!$new) {
            $params = $this->rules['update'];
        }
        $validator = Validator::make($request->all(), $params);
        if ($validator->fails()) {
            return $validator->errors();
        }
        return true;
    }

    public function all(Request $request)
    {
        return Currency::all();
    }

    public function find(Request $request, Currency $currency)
    {
        return $currency;
    }

    public function create(Request $request)
    {
        $currency = new Currency();
        $currency->name = $request->get('name');
        $currency->slug = $request->get('slug');
        $currency->symbol = $request->get('symbol');
        $currency->save();
        return $currency;
    }

    public function update(Request $request, Currency $currency)
    {
        if ($request->has('name')) {
            $currency->name = $request->get('name');
        }
        if ($request->has('slug')) {
            $currency->slug = $request->get('slug');
        }
        if ($request->has('symbol')) {
            $currency->symbol = $request->get('symbol');
        }
        $currency->save();
        return $currency;
    }

    public function delete(Request $request, Currency $currency)
    {
        $currency->delete();
        return null;
    }
}
