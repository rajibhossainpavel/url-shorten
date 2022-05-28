<?php

namespace App\Repositories\Acl;


use Illuminate\Http\Request;
use Carbon\Carbon;
use Event;
use Ariaieboy\LaravelSafeBrowsing\Facades\LaravelSafeBrowsing;
use AshAllenDesign\ShortURL\Events\ShortURLVisited;
use AshAllenDesign\ShortURL\Models\ShortURL;
use AshAllenDesign\ShortURL\Models\ShortURLVisit;

//EXCEPTION
use Exception;

class UrlRepository{
	protected $ShortURL;
	protected $ShortURLVisit;
		
	public function __construct(
		ShortURL $ShortURL,
		ShortURLVisit $ShortURLVisit
	)
	{
		$this->ShortURL = $ShortURL;	
		$this->ShortURLVisit = $ShortURLVisit;
	}
	
	private function transform(string $url, int $length = 6)
    {
        $characters = '0123456789';
        $shortURL = '';
		for ($i = 0; $i < $length; $i++) {
			$index = rand(0, strlen($characters) - 1);
            $shortURL .= $characters[$index];
        }
		if ($this->ShortURL->where('url_key', $shortURL)->count() != 0) {

            $length += 1;
            $this->transform($url, $length);
        }
		$newUrl=$this->ShortURL->create(['url_key'=>$shortURL]);
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
					if(isset($payload['single_use']) && !empty($payload['single_use']) && $payload['single_use']==1){
						if(isset($payload['valid_for_days']) && !empty($payload['valid_for_days']) && $payload['valid_for_days']!=0){
							$shortURLObject = $builder->destinationUrl($payload['url'])
											->singleUse()
											->activateAt(\Carbon\Carbon::now()->addDay())
											->deactivateAt(\Carbon\Carbon::now()->addDays($payload['valid_for_days']))
											->make();
						}else{
							$shortURLObject = $builder->destinationUrl($payload['url'])
											->singleUse()
											->make();
						}
						
					}else{
						if(isset($payload['valid_for_days']) && !empty($payload['valid_for_days']) && $payload['valid_for_days']!=0){
							$shortURLObject = $builder->destinationUrl($payload['url'])
											->activateAt(\Carbon\Carbon::now()->addDay())
											->deactivateAt(\Carbon\Carbon::now()->addDays($payload['valid_for_days']))
											->make();
						}else{
							$shortURLObject = $builder->destinationUrl($payload['url'])
											->make();
						}
					}
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
				$urlFound=$this->ShortURL->where('url_key', '=', $payload['url_key'])->first();
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
	
	public function trackUrl($payload){
		$result=array();
		try{
			if(isset($payload['url_key']) && !empty($payload['url_key'])){
				$shortURL = \AshAllenDesign\ShortURL\Models\ShortURL::findByKey($payload['url_key']);
				if($shortURL->trackingEnabled()){
					$ShortURLFound=$this->ShortURL->where('url_key', '=', $payload['url_key'])->first();
					if(isset($ShortURLFound) && !empty($ShortURLFound)){
						$ShortURLVisitFound=$this->ShortURLVisit->firstOrCreate(['short_url_id'=>$ShortURLFound->id]);
						Event::dispatch(new ShortURLVisited($ShortURLFound, $ShortURLVisitFound));
						if(isset($ShortURLVisitFound) && !empty($ShortURLVisitFound)){
							$result['ip_address']=$ShortURLVisitFound['ip_address'];
							$result['operating_system']=$ShortURLVisitFound['operating_system'];
							$result['operating_system_version']=$ShortURLVisitFound['operating_system_version'];
							$result['browser']=$ShortURLVisitFound['browser'];
							$result['browser_version']=$ShortURLVisitFound['browser_version'];
							$result['referer_url']=$ShortURLVisitFound['referer_url'];
							$result['device_type']=$ShortURLVisitFound['device_type'];
						}
					}
				}
			}
		}catch(Exception $e){
			throw new Exception($e->getMessage());
		}
		return $result;
	}
}