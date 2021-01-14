<?php


namespace App\Http\Controllers\API\Tracking\Traits;


trait Products
{

    public function getProductList()
    {
        $result = $this->trackingProjectsRepo->getProducts();
        return self::showResponse($result);
    }
}
