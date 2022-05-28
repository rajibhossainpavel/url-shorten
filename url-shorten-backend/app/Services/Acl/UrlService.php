<?php
namespace App\Services\Acl;

use Illuminate\Support\Facades\DB;
use Flugg\Responder\Responder;
use Illuminate\Http\Request;

use App\Repositories\Acl\UrlRepository;


class UrlService{
	protected $UrlRepository;

    public function __construct(
		UrlRepository $UrlRepository
	)
    {
        $this->UrlRepository = $UrlRepository;
    }
	
	public function StoreUrl($payload){
		try {
            $StoreUrl = $this->UrlRepository->storeUrl($payload);
		} catch (Exception $Exception) {
            Log::info($Exception->getMessage());
        }
		return responder()->success($StoreUrl)->respond();
	}
	
	public function ShowUrl($payload){
		try {
            $ShowUrl = $this->UrlRepository->showUrl($payload);
		} catch (Exception $Exception) {
            Log::info($Exception->getMessage());
        }
		return responder()->success($ShowUrl)->respond();
	}
}