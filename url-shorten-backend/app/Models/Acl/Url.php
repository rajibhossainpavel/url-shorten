<?php

namespace App\Models\Acl;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Url extends Model
{
    use SoftDeletes;

    protected $table = 'short_urls';
	 
    protected $fillable = [
       //
    ];
	
    protected $hidden = [];
	
	protected $dateformat='U';
}