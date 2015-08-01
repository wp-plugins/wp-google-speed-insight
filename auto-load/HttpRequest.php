<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of HttpRequest
 *
 * @author Kien
 */
class HttpRequest {

    static function queryJSON($args) {
        $arrOpt = array();
        $ch = curl_init();
        $url = $args['url'];
        if (isset($args['method']))
            $method = $args['method'];
        else {
            $method = 'GET';
        }
        if (isset($args['data'])) {
            $dataStr = http_build_query($args['data']);
            if ($method == 'GET') {
                $url.='?' . $dataStr;
            } else {
                $arrOpt[CURLOPT_POSTFIELDS] = $dataStr;
            }
        }
        $arrOpt[CURLOPT_URL] = $url;
        $arrOpt[CURLOPT_RETURNTRANSFER] = true;
        $arrOpt[CURLOPT_SSL_VERIFYHOST] = false;
        $arrOpt[CURLOPT_SSL_VERIFYPEER] = false;
        curl_setopt_array($ch, $arrOpt);
        $result = curl_exec($ch);
        if ($result) {
            $json = json_decode($result);
//            print_r($json);
//            die();
            if ($json)
                return $json;
            else
                return false;
        } else
            return FALSE;
    }

}
