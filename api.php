<?php
require_once 'apkpureClass.php';

use chlassScraper\Apkpure;
use MiladRahimi\Jwt\Parser;

if ($_GET['type'] == 'fetchSearch' && isset($_GET['query'])) {
    Apkpure::prepare($_GET['language'], $_GET['type'], $_GET['query']);
    Apkpure::getSearch();
    $data = Apkpure::getResponses();
    if ($data['status'] == TRUE) {
        header('Content-Type: application/json');
        preg_match('/Bearer\s(\S+)",/', json_encode(headers_list()), $token);
        $explode = explode('.', $token[1]);
        $parse = array($explode[0], base64_decode($explode[1]), $explode[2]);
        $implode = implode('.', $parse);
        $verify = new Parser(Apkpure::verifySigner());
        $claims = $verify->parse($implode);
        die(json_encode($claims, JSON_PRETTY_PRINT));
    } else {
        die($data);
    }
} else if ($_GET['type'] == 'fetchPublisher' && isset($_GET['query'])) {
    Apkpure::prepare($_GET['language'], $_GET['type'], $_GET['query']);
    Apkpure::getPublisher();
    $data = Apkpure::getResponses();
    if ($data['status'] == TRUE) {
        header('Content-Type: application/json');
        preg_match('/Bearer\s(\S+)",/', json_encode(headers_list()), $token);
        $explode = explode('.', $token[1]);
        $parse = array($explode[0], base64_decode($explode[1]), $explode[2]);
        $implode = implode('.', $parse);
        $verify = new Parser(Apkpure::verifySigner());
        $claims = $verify->parse($implode);
        die(json_encode($claims, JSON_PRETTY_PRINT));
    } else {
        die($data);
    }
} else if ($_GET['type'] == 'fetchLanguage') {
    Apkpure::prepare('', $_GET['type'], '');
    Apkpure::getLanguage();
    $data = Apkpure::getResponses();
    if ($data['status'] == TRUE) {
        header('Content-Type: application/json');
        preg_match('/Bearer\s(\S+)",/', json_encode(headers_list()), $token);
        $explode = explode('.', $token[1]);
        $parse = array($explode[0], base64_decode($explode[1]), $explode[2]);
        $implode = implode('.', $parse);
        $verify = new Parser(Apkpure::verifySigner());
        $claims = $verify->parse($implode);
        die(json_encode($claims, JSON_PRETTY_PRINT));
    } else {
        die($data);
    }
} else if ($_GET['type'] == 'fetchDetail' && isset($_GET['query'])) {
    Apkpure::prepare($_GET['language'], $_GET['type'], $_GET['query']);
    Apkpure::getDetail();
    $data = Apkpure::getResponses();
    if (isset($data)) {
        header('Content-Type: application/json');
        preg_match('/Bearer\s(\S+)",/', json_encode(headers_list()), $token);
        $explode = explode('.', $token[1]);
        $parse = array($explode[0], base64_decode($explode[1]), $explode[2]);
        $implode = implode('.', $parse);
        $verify = new Parser(Apkpure::verifySigner());
        $claims = $verify->parse($implode);
        die(json_encode($claims, JSON_PRETTY_PRINT));
    } else {
        die($data);
    }
} else if ($_GET['type'] == 'loadFile' && isset($_GET['query'])) {
    Apkpure::prepare($_GET['language'], $_GET['type'], $_GET['query']);
    Apkpure::getFile();
    $data = Apkpure::getResponses();
    if ($data['status'] == TRUE) {
        header('Content-Type: application/json');
        preg_match('/Bearer\s(\S+)",/', json_encode(headers_list()), $token);
        $explode = explode('.', $token[1]);
        $parse = array($explode[0], base64_decode($explode[1]), $explode[2]);
        $implode = implode('.', $parse);
        $verify = new Parser(Apkpure::verifySigner());
        $claims = $verify->parse($implode);
        set_time_limit(0);
        header('Referrer-Policy: no-referrer');
        header('Location: ' . $claims[0], TRUE, 307);
        die();
    } else {
        die($data);
    }
} else {
    die();
}
