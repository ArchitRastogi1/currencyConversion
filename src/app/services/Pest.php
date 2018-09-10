<?php

namespace services;
use exceptions\CurlException;
use models\configs\XeConfiguration;
use Exception;
use Monolog\Logger;

class Pest {
    
    private $logger;
    
    public function __construct(Logger $logger) {
        $this->logger = $logger;
    }
    
    private $curlOpts = array(
        CURLOPT_RETURNTRANSFER => true, // return result instead of echoing
        CURLOPT_USERPWD => XeConfiguration::API_KEY.":".XeConfiguration::API_SECRET,
        CURLOPT_FOLLOWLOCATION => false, // follow redirects, Location: headers
        CURLOPT_MAXREDIRS => 3, // but dont redirect more than 10 times
        CURLOPT_HTTPHEADER => array(),
        CURLOPT_HTTPAUTH => CURLAUTH_BASIC
    );
    
    public function get($api, $data) {
        if (!empty($data)) {
            $pos = strpos($api, '?');
            if ($pos !== false) {
                $api = substr($api, 0, $pos);
            }
            $api .= '?' . http_build_query($data, '', '&');

        }
        $curlOpts = $this->curlOpts;
        $curlHandle = $this->prepareRequest($curlOpts, $api);
        try {
            $response = curl_exec($curlHandle);
            return json_decode($response,true);
        } catch(Exception $ex) {
            $this->logger->addCritical($ex->getMessage());
            throw new CurlException("Exception in fetching data",500, $ex);
        }
        curl_close($curlHandle);
    }
    
    private function prepareRequest($opts, $url) {
        $url = trim($url);
        $curlHandle = curl_init($url);
        if($curlHandle === false) {
            throw new CurlException("Curl init error");
        }
        foreach ($opts as $opt => $val) {
            curl_setopt($curlHandle, $opt, $val);
        }
        return $curlHandle;
    }
}

