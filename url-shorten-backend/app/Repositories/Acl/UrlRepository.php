<?php

namespace App\Repositories\Acl;


use Illuminate\Http\Request;
use Ariaieboy\LaravelSafeBrowsing\Facades\LaravelSafeBrowsing;
use App\Models\Acl\Url;
use App\Models\Acl\Visit;

//EXCEPTION
use Exception;

class UrlRepository{
	protected $Url;
	protected $Visit;
		
	public function __construct(
		Url $Url,
		Visit $Visit
	)
	{
		$this->Url = $Url;	
		$this->Visit = $Visit;
	}
	
	private function transform(string $url, int $length = 6)
    {
        $characters = '0123456789';
        $shortURL = '';
		for ($i = 0; $i < $length; $i++) {
			$index = rand(0, strlen($characters) - 1);
            $shortURL .= $characters[$index];
        }
		if ($this->Url->where('url_key', $shortURL)->count() != 0) {

            $length += 1;
            $this->transform($url, $length);
        }
		$newUrl=$this->Url->create(['url_key'=>$shortURL]);
		$newUrl->destination_url=$url;
		$newUrl->default_short_url=config('app.name').'/short/'.$shortURL;
		$newUrlSaved=$newUrl->save();
		if($newUrlSaved){
			return true;
		}
		return false;;
    }
	
	public function storeUrl($payload){
		$result=array();
		try{
			if(isset($payload['url']) && !empty($payload['url'])){
				//CHECK WHERE URL IS SAFE OR NOT
				$safetyMeasure = LaravelSafeBrowsing::isSafeUrl($payload['url'],true);
				$is_prcessable=0;
				switch($safetyMeasure){
					case 1: 
						$is_prcessable=1;
						break;
					case 'THREAT_TYPE_UNSPECIFIED':
					case 'MALWARE':
					case 'SOCIAL_ENGINEERING':
					case 'UNWANTED_SOFTWARE':
					case 'POTENTIALLY_HARMFUL_APPLICATION':
						$result['message']='we can not process this url.';
						break;
				}
				if($is_prcessable){
					$builder = new \AshAllenDesign\ShortURL\Classes\Builder();
					$shortURLObject = $builder->destinationUrl($payload['url'])->make();
					$result['short_url']=$shortURLObject['default_short_url'];
					$result['url_key']=$shortURLObject['url_key'];
				}
			}
		}catch(Exception $e){
			$transformed=transform($payload['url']);
			if(!$transformed){
				throw new Exception($e->getMessage());
			}
		}
		return $result;
	}
	
	public function showUrl($payload){
		$result=array();
		try{
			if(isset($payload['url_key']) && !empty($payload['url_key'])){
				$urlFound=Url::where('url_key', '=', $payload['url_key'])->first();
				if(isset($urlFound) && !empty($urlFound)){
					$result['destination_url']=$urlFound['destination_url'];
					$result['short_url']=$payload['short_url'];
				}else{
					$result['destination_url']='';
					$result['short_url']=$payload['short_url'];
				}
			}
		}catch(Exception $e){
			throw new Exception($e->getMessage());
		}
		return $result;
	}
}