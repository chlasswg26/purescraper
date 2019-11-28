<?php

namespace chlassScraper;

error_reporting(0);
set_time_limit(0);

require_once 'vendor/autoload.php';

use MiladRahimi\Jwt\Generator;
use MiladRahimi\Jwt\Cryptography\Algorithms\Hmac\HS256;
use simple_curl\curl;

/**
 * @package Apkpure
 * @param $secretKey (Use HMAC SHA256)
 */
class Apkpure
{
    protected static $url = 'https://apkpure.com';
    protected static $language;
    protected static $type;
    protected static $query;
    protected static $responses;
    public static $secretKey = '12345678901234567890123456789012';
    protected static $array;

    public static function prepare($language, $type, $query)
    {
        self::$language = $language;
        self::$type = $type;
        self::$query = $query;
    }

    public static function getLanguage()
    {
        if (self::$type == 'fetchLanguage') {
            curl::prepare(self::$url, '/');
            curl::exec_get();
            preg_match_all('/<a class="flag-icon flag-country flag_country_[a-z]{2}" href=".*?" hreflang="([a-z]{2})"/', curl::get_response(), $ids);
            preg_match_all('/<a class="flag-icon flag-country flag_country_[a-z]{2}" href=".*?" hreflang="[a-z]{2}">(.*?)<\/a><\/li>/', curl::get_response(), $names);
            $array = array();
            for ($i = 0; $i < count($ids[1]); $i++) {
                $array[] = array(
                    'id' => $ids[1][$i],
                    'name' => $names[1][$i],
                );
            }
            self::$array = $array;
            self::$responses = array(
                'status' => TRUE
            );
        } else {
            self::$responses = array(
                'status' => FALSE,
                'reasons' => 'Something is Wrong!'
            );
        }
    }

    public static function getPublisher()
    {
        if (self::$type == 'fetchPublisher' && !empty(self::$query)) {
            $data = base64_decode(self::$query);
            preg_match('/\/[a-z]{2}\/developer\//', $data, $match1);
            preg_match('/\\/developer\/.*?/', $data, $match2);
            if ($match1 == TRUE || $match2 == TRUE) {
                if (self::$language == 'en') {
                    $filled = preg_replace('/(\/[a-z]{2}\/[a-z]{9}\/)/', '/developer/', $data);
                    $developers = str_replace('/developer/', '', preg_replace('/(\/[a-z]{2}\/[a-z]{9}\/)/', '/developer/', $data));
                } else if (empty(self::$language)) {
                    $filled = $data;
                    $developers = str_replace('/developer/', '', $data);
                } else {
                    $filled = preg_replace('/\/([a-z]{2})\/[a-z]{9}\//', '/' . self::$language . '/developer/', $data);
                    $developers = str_replace('/' . self::$language . '/developer/', '', preg_replace('/\/([a-z]{2})\/[a-z]{9}\//', '/' . self::$language . '/developer/', $data));
                }
            } else {
                if (self::$language == 'en' || empty(self::$language)) {
                    $filled = '/developer/' . $data;
                } else {
                    $filled = '/' . self::$language . '/developer/' . $data;
                }
                $developers = $data;
            }
            curl::prepare(self::$url, $filled);
            curl::exec_get();
            preg_match_all('/<dt><a title=".*?" href="(.*?)"><img alt=".*?" src=".*?"><\/a><\/dt>/', curl::get_response(), $urls);
            preg_match_all('/<dt><a title="(.*?)" href=".*?"><img alt=".*?" src=".*?"><\/a><\/dt>/', curl::get_response(), $titles);
            preg_match_all('/<dt><a title=".*?" href=".*?"><img alt=".*?" src="(.*?)"><\/a><\/dt>/', curl::get_response(), $images);
            preg_match_all('/<span class="star">(.*?)<\/span>/', curl::get_response(), $ratings);
            preg_match_all('/class="developer-image" data-srcset=".*?" data-original="(.*?)" alt=/', curl::get_response(), $img_developers);
            preg_match_all('/<p>([0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]))<\/p>/', curl::get_response(), $dates);
            $array = array();
            for ($i = 0; $i < count($urls[1]); $i++) {
                $array[] = array(
                    'app' => array(
                        'code' => base64_encode($urls[1][$i]),
                        'name' => $titles[1][$i],
                        'image' => $images[1][$i],
                        'rating' => $ratings[1][$i],
                    ),
                    'developer' => array(
                        'name' => $developers,
                        'image' => $img_developers[1][0],
                    ),
                    'latest_update' => $dates[1][$i],
                );
            }
            self::$array = $array;
            self::$responses = array(
                'status' => TRUE
            );
        } else {
            self::$responses = array(
                'status' => FALSE,
                'reasons' => 'Something is Wrong!'
            );
        }
    }

    public static function getSearch()
    {
        if (self::$type == 'fetchSearch' && !empty(self::$query)) {
            if (self::$language == 'en') {
                $filled = '/search?q=' . self::$query;
            } else if (empty(self::$language)) {
                $filled = '/search?q=' . self::$query;
            } else {
                $filled = '/' . self::$language . '/search?q=' . self::$query;
            }
            curl::prepare(self::$url, $filled);
            curl::exec_get();
            preg_match_all('/<dt><a title=".*?" target="_blank" href="(.*?)"><img title=".*?" src=".*?"><\/a>/', curl::get_response(), $urls);
            preg_match_all('/<dt><a title="(.*?)" target="_blank" href=".*?"><img title=".*?" src=".*?"><\/a>/', curl::get_response(), $titles);
            preg_match_all('/<dt><a title=".*?" target="_blank" href=".*?"><img title=".*?" src="(.*?)"><\/a>/', curl::get_response(), $images);
            preg_match_all('/<span class="star">(.*?)<\/span>/', curl::get_response(), $ratings);
            preg_match_all('/<p>\D+: <a href=".*?">(.*?)<\/a><\/p>/', curl::get_response(), $developers);
            preg_match_all('/<p>\D+: <a href="(.*?)">.*?<\/a><\/p>/', curl::get_response(), $linked_developers);
            // $array = array(
            //     "iss" => "example.org",
            //     "data" => array()
            // );
            $array = array();
            for ($i = 0; $i < count($urls[1]); $i++) {
                // $array['data'][] = array(
                $array[] = array(
                    'app' => array(
                        'code' => base64_encode($urls[1][$i]),
                        'name' => $titles[1][$i],
                        'image' => $images[1][$i],
                        'rating' => $ratings[1][$i],
                    ),
                    'developer' => array(
                        'code' => base64_encode($linked_developers[1][$i]),
                        'name' => $developers[1][$i],
                    ),
                );
            }
            self::$array = $array;
            self::$responses = array(
                'status' => TRUE
            );
        } else {
            self::$responses = array(
                'status' => FALSE,
                'reasons' => 'Something is Wrong!'
            );
        }
    }

    public static function getDetail()
    {
        if (self::$type == 'fetchDetail' && !empty(self::$query)) {
            $data = base64_decode(self::$query);
            preg_match('/\/[a-z]{2}\//', $data, $match);
            if ($match == TRUE) {
                if (self::$language == 'en') {
                    $filled = preg_replace('/(\/[a-z]{2}\/)/', '', $data);
                } else if (empty(self::$language)) {
                    $filled = $data;
                } else {
                    $filled = preg_replace('/\/([a-z]{2})\/.*?\/.*?\//', '/' . self::$language . '/', $data);
                }
            } else {
                if (self::$language == 'en' || empty(self::$language)) {
                    $filled = $data;
                } else {
                    $filled = $data;
                }
            }
            curl::prepare(self::$url, $filled);
            curl::exec_get();
            preg_match_all('/<a class=" da" title=".*?" href="(.*?)">/', curl::get_response(), $urls);
            preg_match_all('/<span itemprop="name">(.*?)<\/span>/', curl::get_response(), $titles);
            preg_match_all('/<div class="icon"><img title=".*?" alt=".*?" srcset=".*?" src="(.*?)"><\/div>/', curl::get_response(), $images);
            preg_match_all('/<div class="details-sdk"><span itemprop="version">(.*?)<\/span>for Android<\/div>/', curl::get_response(), $versions);
            preg_match_all('/<span class="average" itemprop="ratingValue">(.*?)<\/span>/', curl::get_response(), $rating_value);
            preg_match_all('/<meta itemprop="ratingCount" content="(.*?)"/', curl::get_response(), $rating_count);
            preg_match_all('/<meta itemprop="bestRating" content="(.*?)"/', curl::get_response(), $rating_positive);
            preg_match_all('/<meta itemprop="worstRating" content="(.*?)"/', curl::get_response(), $rating_negative);
            preg_match_all('/data-type="reviews">(.*?)<\/a>/', curl::get_response(), $reviews);
            preg_match_all('/<div class="content" itemprop="description">([^"]*?)<\/div>/', curl::get_response(), $desc);
            preg_match_all('/<div class="describe-whatnew" id="whatsnew">\r\n                    ([^"]*?)\r\n                <\/div>\r\n                \r\n            <\/div>\r\n\t\t\t<div class="clear">/', curl::get_response(), $whatsnew);
            preg_match_all('/<p itemprop="datePublished">(.*?)<\/p>/', curl::get_response(), $latest_updates);
            preg_match_all('/<p>Android (.*?)<\/p>/', curl::get_response(), $requirements);
            preg_match_all('/data-src="(.*?)" style="background-image: .*?"/', curl::get_response(), $trailers);
            preg_match_all('/<a class="mpopup" data-fancybox=".*?" style=".*?" target=".*?" href="(.*?)" title=/', curl::get_response(), $desc_images);
            $array = array(
                'app' => array(
                    'code' => base64_encode($urls[1][0]),
                    'name' => $titles[1][0],
                    'image' => $images[1][0],
                    'version' => $versions[1][0],
                    'description' =>  htmlentities($desc[1][0]),
                    'rating' => array(
                        'value' => $rating_value[1][0],
                        'count' => $rating_count[1][0],
                        'best' => $rating_positive[1][0],
                        'worst' => $rating_negative[1][0],
                    ),
                    'screenshot' => array(),
                ),
                'review' => $reviews[1][0],
                'update' => $latest_updates[1][0],
                'whatsnew' => $whatsnew[1][0],
                'requirement' => 'Android ' . $requirements[1][0],
                'trailer' => $trailers[1][0],
            );

            for ($i = 0; $i < count($desc_images[1]); $i++) {
                $array['app']['screenshot'][] = array(
                    $desc_images[1][$i],
                );
            }
            self::$array = $array;
            self::$responses = array(
                'status' => TRUE
            );
        } else {
            self::$responses = array(
                'status' => FALSE,
                'reasons' => 'Something is Wrong!'
            );
        }
    }

    public static function getFile()
    {
        if (self::$type == 'loadFile' && !empty(self::$query)) {
            $data = base64_decode(self::$query);
            preg_match('/\/[a-z]{2}\//', $data, $match);
            if ($match == TRUE) {
                if (self::$language == 'en') {
                    $filled = preg_replace('/(\/[a-z]{2}\/)/', '', $data);
                } else if (empty(self::$language)) {
                    $filled = $data;
                } else {
                    $filled = preg_replace('/\/([a-z]{2})\/.*?\/.*?\//', '/' . self::$language . '/', $data);
                }
            } else {
                if (self::$language == 'en' || empty(self::$language)) {
                    $filled = $data;
                } else {
                    $filled = $data;
                }
            }
            curl::prepare(self::$url, $filled);
            curl::exec_get();
            preg_match_all('/<iframe id="iframe_download" src="(.*?)"><\/iframe>/', curl::get_response(), $load);
            self::$array = array($load[1][0]);
            self::$responses = array(
                'status' => TRUE
            );
        } else {
            self::$responses = array(
                'status' => FALSE,
                'reasons' => 'Something is Wrong!'
            );
        }
    }

    public static function verifySigner()
    {
        $sign = new HS256(self::$secretKey);
        return $sign;
    }

    public static function getResponses()
    {
        $signer = new HS256(self::$secretKey);
        $generator = new Generator($signer);
        $jwt = $generator->generate(self::$array);
        $explode = explode('.', $jwt);
        $parse = array($explode[0], base64_encode($explode[1]), $explode[2]);
        $implode = implode('.', $parse);
        header("Accept: application/json");
        header("Content-Type: application/x-www-form-urlencoded");
        header("Cache-Control: no-cache, must-revalidate, max-age=1000");
        header("Authorization: Bearer " . $implode);
        return json_encode(self::$responses, JSON_PRETTY_PRINT);
    }
}
