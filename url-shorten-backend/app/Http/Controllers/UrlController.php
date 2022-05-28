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
		 //return Url::where('short_url', $url)->count();
    }

    public function index(Request $request)
    {
        $request->validate([
            'url' => 'required|string|url'
        ]);

        if ($short = $this->transform($request->url)) {

            if ($short != 'Error') {
                return response([
                    'short_url' => env('APP_URL') . "/" . $short
                ], 200);
            }
        }

        return response('Error: something unexpected happened, please try again', 503);
    }

    public function showTop()
    {
        $top = Url::orderByDesc('number_of_visits')
            ->limit(100)
            ->get();

        return response($top, 200);
    }

	public function showReal(Request $request)
    {
        $request->validate([
            'url' => 'required|string|url'
        ]);

        $short = $request->url;

        $real = Url::select('real_url')
            ->where('short_url', '=', $short)
            ->first();

        if ($real) {
            return response($real, 200);
        }

        return response('Error: Url not found', 404);
    }


    private function transform(string $url, int $lenght = 3)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $shortURL = '';

        for ($i = 0; $i < $lenght; $i++) {

            $index = rand(0, strlen($characters) - 1);
            $shortURL .= $characters[$index];
        }

        if ($this->checkURL($shortURL) != 0) {

            $lenght += 1;
            $this->transform($url, $lenght);
        }

        if (!$this->storeURL($url, $shortURL)) {
            return 'Error';
        }

        return $shortURL;
    }

    private function storeURL(string $url, string $shortURL)
    {
        if (Url::create([
            'real_url' => $url,
            'short_url' => env('APP_URL') . "/" . $shortURL,
            'number_of_visits' => 0,
            'nsfw' => 0
        ])) {
            return true;
        }
        return false;
    }
}
