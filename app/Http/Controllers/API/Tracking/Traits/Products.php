<?php


namespace App\Http\Controllers\API\Tracking\Traits;


use Illuminate\Http\Request;

trait Products
{

    public function getProductList(Request $request)
    {
        $result = $this->trackingProjectsRepo->getProducts($request);
        return self::showResponse((bool)$result, $result);
    }
}
