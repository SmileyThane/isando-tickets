<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MailCache extends Model
{
    protected $table = 'mail_cache';
    public const CONTACT_FORM_ADDRESSES = ['contactforms@inax247.com', 'contactforms@inax.ch', 'contactforms@inax247.ch' , 'support@inax247.com'];
}
