<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductClient extends Model
{
    protected $fillable = ['client_id', 'product_id'];

    use SoftDeletes;

    public function productData(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function clientData(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }
}
