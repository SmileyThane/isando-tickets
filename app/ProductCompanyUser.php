<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCompanyUser extends Model
{
    use SoftDeletes;

    protected $fillable = ['product_id', 'company_user_id'];

    public function productData(): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
