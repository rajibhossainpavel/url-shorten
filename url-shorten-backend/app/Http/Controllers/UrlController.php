<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Url;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Collection;
use Ariaieboy\LaravelSafeBrowsing\Facades\LaravelSafeBrowsing;

class UrlController extends Controller
{
	public function checkURL(Request $request)
    {
		try{
			$url=$request->url;
			//CHECK WHERE URL IS SAFE OR NOT
			$result = LaravelSafeBrowsing::isSafeUrl($url,true);
		}catch(Exception $e){
			throw new Exception($e->getMessage());
		}
		return $result;
    }
}
