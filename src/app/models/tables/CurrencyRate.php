<?php

namespace models\tables;

class CurrencyRate {
    
    private $sourceCurrency;
    private $targetCurrency;
    private $exchangeRates;
    private $pollingTime;
    private $historyLogId;
    
    const PATH = 'models\tables\CurrencyRate';
    
    public function __construct($sourceCurrency, $targetCurrency, $exchangeRates, $pollingTime, $historyLogId) {
        $this->sourceCurrency = $sourceCurrency;
        $this->targetCurrency = $targetCurrency;
        $this->exchangeRates = $exchangeRates;
        $this->pollingTime = $pollingTime;
        $this->historyLogId = $historyLogId;
    }
    
    public function toArray() {
        return get_object_vars($this);
    }
    
    public function getSourceCurrency() {
        return $this->sourceCurrency;
    }

    public function getTargetCurrency() {
        return $this->targetCurrency;
    }

    public function getExchangeRates() {
        return $this->exchangeRates;
    }

    public function getPollingTime() {
        return $this->pollingTime;
    }

    public function setSourceCurrency($sourceCurrency) {
        $this->sourceCurrency = $sourceCurrency;
    }

    public function setTargetCurrency($targetCurrency) {
        $this->targetCurrency = $targetCurrency;
    }

    public function setExchangeRates($exchangeRates) {
        $this->exchangeRates = $exchangeRates;
    }

    public function setPollingTime($dateApplicable) {
        $this->pollingTime = $dateApplicable;
    }

    public function getHistoryLogId() {
        return $this->historyLogId;
    }

    public function setHistoryLogId($historyLogId) {
        $this->historyLogId = $historyLogId;
    }
}

