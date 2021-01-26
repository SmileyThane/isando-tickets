<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    public function attachments(): MorphMany
    {
        return $this->morphMany(File::class, 'model');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(ProductCompanyUser::class, 'product_id', 'id');
    }

    public function clients(): HasMany
    {
        return $this->hasMany(ProductClient::class, 'product_id', 'id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'id', 'parent_id');
    }

}
