<?php

namespace services\conversionClients;
use services\Pest;
use models\configs\XeConfiguration;
use models\CurrencyList;
use Monolog\Logger;
use exceptions\CurlException;

class XeConversion implements Conversion {
    
    private $pestService;
    private $logger;
    
    public function __construct(Pest $pestService, Logger $logger) {
        $this->pestService = $pestService;
        $this->logger = $logger;
    }
    
    public function getCurrencyList() {
        $response = NULL;
        $count = 1;
        // this function will retry 3 times in case of exception from service
        while(empty($response) && $count <= 3) {
            try {
                $response = $this->pestService->get(XeConfiguration::CURRENCY_LIST_API_URL);
                $currencyList = array();
                foreach($response['currencies'] as $currency) {
                    $currencyList[] = new CurrencyList($currency);
                }
                return $currencyList;
            } catch(CurlException $ex) {
                $this->logger->addDebug("Retrying currency list api");
                $count++;
            }
        }
        $this->logger->addEmergency("Maximum retry done");
        throw new CurlException("Maximum Retry done", 500);
        // put the data in failed queeu for retry
    }
    
    public function getExchangeRates($sourceCurrency, $targetCurrency) {
        $response = NULL;
        $requestData['from'] = $sourceCurrency;
        $requestData['to'] = $targetCurrency;
        $count = 1;
        // this function will retry 3 times in case of exception from service
        while(empty($response) && $count <= 3) {
            try {
                $response = $this->pestService->get(XeConfiguration::CURRENCY_CONVERTION_API_URL, $requestData);
                return $response;
            } catch (CurlException $ex) {
                $this->logger->addDebug("Retrying conversion api");
                $count++;
            }
        }
        $this->logger->addEmergency("Maximum retry done");
        throw new CurlException("Maximum Retry done", 500);
        // put the data in failed queeu for retry
    }
    
}

