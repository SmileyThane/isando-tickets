<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KbArticleClient extends Model
{
    protected $fillable = ['kb_article_id', 'client_id'];
}
