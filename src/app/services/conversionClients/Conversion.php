<?php

namespace services\conversionClients;
use models\requests\ConversionRequest;

interface Conversion {
    
    public function getCurrencyList();
    public function getExchangeRates($sourceCurrency, $targetCurrency);
    
}

