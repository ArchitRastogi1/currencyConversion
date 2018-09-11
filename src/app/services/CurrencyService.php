<?php

namespace services;
use models\requests\ConversionRequest;
use validators\RequestValidator;
use Monolog\Logger;
use services\conversionClients\Conversion;
use models\tables\CurrencyRate;
use services\CurrencyDbManager;
use models\Currency;
use services\cacheClients\CacheService;
use utils\CacheUtils;
use Slim\Exception\NotFoundException;


class CurrencyService {
    
    private $logger;
    private $conversionClient;
    private $currencyDbManager;
    private $cacheService;
    
    public function __construct(Logger $logger, Conversion $conversionClient, CurrencyDbManager $currencyDbManager, CacheService $cacheService) {
        $this->logger = $logger;
        $this->conversionClient = $conversionClient;
        $this->currencyDbManager = $currencyDbManager;
        $this->cacheService = $cacheService;
    }
    
    /* This function converts one currency to other using redis and db */
    public function convertCurrency(ConversionRequest $conversionRequest) {
        $currencyCodesList = $this->getCurrencyCodesList();
        RequestValidator::validateConversionRequest($conversionRequest, $currencyCodesList);
        
        $sourceCurrency = $conversionRequest->getFrom();
        $targetCurrency = $conversionRequest->getTo();
        $amount = $conversionRequest->getAmount();

        $exchangeData = $this->getExchageDataForConversion($sourceCurrency, $targetCurrency);
        $exchangeRate = $exchangeData['exchangeRate'];
        $historyLogId = $exchangeData['historyLogId'];
        
        // This case will probably never happen
        if(empty($exchangeRate) || empty($historyLogId)) {
            throw new NotFoundException("Request Data is Not Found", 400);
        }
        $convertedAmount = $exchangeRate*$amount;
        return array("from" => $sourceCurrency, "to" => $targetCurrency, "amount" => $amount, 
            "convertedAmount" => $convertedAmount, "historyLogId" => $historyLogId);
    }
    
    private function getExchageDataForConversion($sourceCurrency, $targetCurrency) {
         // check if conversion rates are already present in cache 
        $key = CacheUtils::getConversionKey($sourceCurrency, $targetCurrency);
        $exchangeRateData = $this->cacheService->getData($key);
        if(count($exchangeRateData)) {
            $exchangeRate = $exchangeRateData['exchangeRates'];
            $historyLogId = $exchangeRateData['historyLogId'];
        } else {
            $currencyRates = $this->currencyDbManager->getLatestExchangeRates($sourceCurrency, $targetCurrency);
            if($currencyRates[0]['targetCurrency'] == $sourceCurrency) {
                $exchangeRate = $currencyRates[1]['exchangeRates']/$currencyRates[0]['exchangeRates'];
            } else  {
                $exchangeRate = $currencyRates[0]['exchangeRates']/$currencyRates[1]['exchangeRates'];
            }
            $historyLogId = $currencyRates[0]['historyLogId'];    
            //store data in cache
            $this->cacheService->setData($key, array('exchangeRates' => $exchangeRate,'historyLogId' => $historyLogId));
        }
        return array('exchangeRate' => $exchangeRate, 'historyLogId' => $historyLogId);
    }
    
    public function getCurrencyCodesList() {
        $key = CacheUtils::getCurrencyListKey();
        // check if list already present in cache
        $currencyCodeList = $this->cacheService->getData($key);
        if(!count($currencyCodeList)) {
            $currencyCodeList = $this->currencyDbManager->getCurrencyCodesList();
            //store list in cache
            $this->cacheService->setData($key, $currencyCodeList);
        }
        return $currencyCodeList;
    }
    
    /* fetches currency exchnage rates from conversion service */
    public function getExchangeRatesFromClient($sourceCurrency, $targetCurrencyList) {
        $response = $this->conversionClient->getExchangeRates($sourceCurrency, $targetCurrencyList);
        $currencyRateList = array();
        $historyLogId = strtotime($response['timestamp']);
        foreach($response['to'] as $currencyRates) {
            $currencyRateObj = new CurrencyRate($sourceCurrency,$currencyRates['quotecurrency'],$currencyRates['mid'],
                    $response['timestamp'], $historyLogId);
            $currencyRateList[] = $currencyRateObj;
        }
        unset($targetCurrencyList);
        return $currencyRateList;
    }
    
    public function insertExchangeRatesInLatestCurrencyRates($currencyRatesList) {
        $this->currencyDbManager->insertExchangeRatesInLatestCurrencyRates($currencyRatesList);
        // flush all cache data when new currency rates have been inserted in database
        $this->cacheService->flushData();
    }
    
    public function insertExchangeRatesInConversionHistoryLog($exchangeRatesList) {
        return $this->currencyDbManager->insertExchangeRatesInConversionHistoryLog($exchangeRatesList);
    }
    
    public function getCurrencyListFromClient() {
        return $this->conversionClient->getCurrencyList();
    }
    
    public function insertCurrencyList($currencyList) {
        $this->currencyDbManager->insertCurrencyList($currencyList);
        $this->cacheService->flushData();
    }
    
    public function getCurrencyRateList($sourceCurrency) {
        $currencyCodesList = $this->getCurrencyCodesList();
        RequestValidator::validateCurrencySource($sourceCurrency, $currencyCodesList);
        $responseArray = array();
        $exchangeRatesListForBase = $this->fetchBaseCurrenyExchangeRateList();
        // This case will probably never happen
        if(empty($exchangeRatesListForBase) || count($exchangeRatesListForBase) == 0) {
            throw new NotFoundException("Requested Data is not Found");
        }
        // get currency rates with respect to base currency
        $sourceCurrencyExchangeRate = $this->getSourceCurrencyExchangeRate($sourceCurrency, $exchangeRatesListForBase);
        $i=0;
        // convert rates from base currency to requested currency
        foreach($exchangeRatesListForBase as $exchangeRate) {
            $responseArray[$i]['exchangeRates'] = $exchangeRate['exchangeRates']/$sourceCurrencyExchangeRate;
            $responseArray[$i++]['currency'] = $exchangeRate['targetCurrency'];
        }
        unset($exchangeRatesListForBase);
        return $responseArray;
    }
  
    private function fetchBaseCurrenyExchangeRateList() {
        $key = CacheUtils::getRateListKey();
        $rateList = $this->cacheService->getData($key);
        if(!count($rateList)) {
            $rateList = $this->currencyDbManager->getCurrencyRateList(Currency::BASE_CURRENCY);
            $this->cacheService->setData($key, $rateList);
        }
        return $rateList;
    }
    
    private function getSourceCurrencyExchangeRate($sourceCurrency, $exchangeRatesListForBase) {
        // get indexed exchange rates to be used in loop
        foreach($exchangeRatesListForBase as $exchangeRate) {
            if($exchangeRate['targetCurrency'] == $sourceCurrency) {
                return $exchangeRate['exchangeRates'];
            }
        }
    }
    
}
