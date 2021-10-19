<?php

namespace App\Http\Controllers\API\AxieInfinity;

use App\Http\Controllers\Controller;

use GuzzleHttp\Client as GuzzleClient;

class AxieInfinityController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getAxieApiData($url, $address)
    {
        $client = new GuzzleClient();
        $wallet = $address;
        $address = $url . $wallet;

        $res = $client->get($address);
        $data = $res->getBody();

    	return response()->json($data);
    }
}
