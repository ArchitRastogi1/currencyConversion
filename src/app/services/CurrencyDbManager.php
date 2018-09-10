<?php

namespace services;
use daos\CurrencyDao;

class CurrencyDbManager {
    
    private $currencyDao;
    
    public function __construct(CurrencyDao $currencyDao) {
       $this->currencyDao = $currencyDao;
    }
    
    public function getCurrencyCodesList() {
        return $this->currencyDao->getCurrencyCodesList();
    }
    
    public function insertExchangeRatesInLatestCurrencyRates($currencyRatesList) {
        return $this->currencyDao->insertExchangeRatesInLatestCurrencyRates($currencyRatesList);
    }
    
    public function getLatestExchangeRates($sourceCurrency, $targetCurrency) {
        return  $this->currencyDao->getLatestExchangeRates($sourceCurrency, $targetCurrency);
    }
    
    public function getCurrencyRateList($sourceCurrency) {
        return $this->currencyDao->getCurrencyRateList($sourceCurrency);
    }
    
    public function insertExchangeRatesInConversionHistoryLog($exchangeRatesList) {
        return $this->currencyDao->insertExchangeRatesInConversionHistoryLog($exchangeRatesList);
    }
    
    public function insertCurrencyList($currencyList) {
        return $this->currencyDao->insertCurrencyList($currencyList);
    }
}

