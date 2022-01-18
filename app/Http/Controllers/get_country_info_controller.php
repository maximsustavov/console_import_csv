<?php

namespace App\Http\Controllers;

class get_country_info_controller extends Controller
{

    /**
     * get iso 3 list width country name
     * @return array
     */
    public static function get_country_info_iso_3(){
        $iso_3_country_list = array_combine(static::download_iso_3_list(),static::download_country_list());
        return $iso_3_country_list;
    }

    /**
     * get iso 2 list width country name
     * @return array
     */
    public static function download_country_list(){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://country.io/names.json',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response,true);
    }

    /**
     * get iso 3 list
     * @return array
     */
    public static function download_iso_3_list(){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://country.io/iso3.json',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response,true);
    }
}
