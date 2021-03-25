<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductClient extends Model
{
    use SoftDeletes;

    protected $fillable = ['client_id', 'product_id'];

    public function productData(): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function clientData(): HasOne
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }
}
