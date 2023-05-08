<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Email extends Model
{
    use SoftDeletes;

    protected $table = 'emails';

    protected $fillable = ['entity_id', 'entity_type', 'email', 'email_type'];

    public function emailable()
    {
        return $this->morphTo();
    }

    public function type(): HasOne
    {
        return $this->hasOne(EmailType::class, 'id', 'email_type');
    }

    public function editExistingEmail(): void
    {
        $this->email = "$this->email#edited#" . date('Y-m-d#H-i-s');
        $this->save();
    }
}
