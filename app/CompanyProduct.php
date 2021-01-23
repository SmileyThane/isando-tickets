<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyProduct extends Model
{
    use SoftDeletes;

    protected $fillable = ['company_id', 'product_id'];

    public function productData(): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
