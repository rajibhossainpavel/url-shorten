<?php

namespace App\Http\Controllers\Acl;

use App\Http\Controllers\Controller;
use App\Models\Url;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Collection;
use App\Services\Acl\UrlService;
class UrlController extends Controller
{
	protected $UrlService;
		
	public function __construct(
		UrlService $UrlService
	)
	{
		$this->UrlService = $UrlService;	
	}
	
	public function storeUrl(Request $request){
		$Result = ['status' => 200];
		try {
			$payload=array();
			$payload['url']=isset($request->url) && !empty($request->url)? trim($request->url): '';
			$payload['single_use']=isset($request->single_use) && !empty($request->single_use)? trim($request->single_use): 0;
			$payload['valid_for_days']=isset($request->valid_for_days) && !empty($request->valid_for_days)? trim($request->valid_for_days): 0;
			$Result['data'] = $this->UrlService->StoreUrl($payload);
		} catch (Exception $Exception) {
			$Result = [
				'status' => 500,
				'error' => $Exception->getMessage()
			];
		}
		return response()->json($Result, $Result['status']);	
	}
	
	public function showUrl(Request $request){
		$Result = ['status' => 200];
		try {
			$payload=array();
			$payload['short_url']=isset($request->short_url) && !empty($request->short_url)? trim($request->short_url): '';
			$exploded=explode('/', $payload['short_url']);
			$payload['url_key']=$exploded[sizeof($exploded)-1];
			$Result['data'] = $this->UrlService->ShowUrl($payload);
		} catch (Exception $Exception) {
			$Result = [
				'status' => 500,
				'error' => $Exception->getMessage()
			];
		}
		return response()->json($Result, $Result['status']);	
	}
	
	
	public function trackUrl(Request $request){
		$Result = ['status' => 200];
		try {
			$payload=array();
			$payload['short_url']=isset($request->short_url) && !empty($request->short_url)? trim($request->short_url): '';
			$exploded=explode('/', $payload['short_url']);
			$payload['url_key']=$exploded[sizeof($exploded)-1];
			$Result['data'] = $this->UrlService->TrackUrl($payload);
		} catch (Exception $Exception) {
			$Result = [
				'status' => 500,
				'error' => $Exception->getMessage()
			];
		}
		return response()->json($Result, $Result['status']);	
	}
	
	public function visitUrl(Request $request){
		$Result = ['status' => 200];
		try {
			$payload=array();
			$payload['url_key']=$request->route('url_key');
			dd($payload);
			$Result['data'] = $this->UrlService->VisitUrl($payload);
		} catch (Exception $Exception) {
			$Result = [
				'status' => 500,
				'error' => $Exception->getMessage()
			];
		}
		return response()->json($Result, $Result['status']);	
	}
}
