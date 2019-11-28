<?php

/**
 * Created by PhpStorm.
 * User: sayed
 * Date: 9/9/18
 * Time: 3:17 PM
 */


namespace simple_curl;

/**
 * Class curl
 * @package simple_curl
 */
class curl
{

    protected static $url;
    protected static $query;
    protected static $responses;

    /**
     * @param $url
     * @param $query
     */
    public static function prepare($url, $query)
    {
        self::$url = $url;
        self::$query = $query;
    }

    // /**
    //  *  Execute post method curl request
    //  */
    // public static function exec_post() {

    //     $curl = curl_init();
    //     curl_setopt($curl, CURLOPT_URL,self::$url);
    //     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($curl, CURLOPT_POST, 1);
    //     curl_setopt($curl, CURLOPT_POSTFIELDS, self::$query);
    //     self::$responses = curl_exec($curl);
    //     curl_close($curl);
    // }

    /**
     *  Execute get method curl request
     */
    public static function exec_get()
    {

        $full_url = self::$url . self::$query;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $full_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        self::$responses = curl_exec($curl);
        curl_close($curl);
    }

    /**
     * @return mixed
     */
    public static function get_response()
    {
        return self::$responses;
    }

    // /**
    //  * @return mixed
    //  */
    // public static function get_response_assoc() {
    //     return json_decode(self::$responses, true);
    // }


}
