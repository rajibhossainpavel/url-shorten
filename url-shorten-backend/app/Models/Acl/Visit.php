<?php

namespace App\Models\Acl;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visit extends Model
{
    use SoftDeletes;

    protected $table = 'short_url_visits';
	 
    protected $fillable = [
       'url_key'
    ];
	
    protected $hidden = [];
	
	protected $dateformat='U';
}