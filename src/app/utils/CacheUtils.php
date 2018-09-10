<?php

namespace utils;

class CacheUtils {
    
    const CURRENCY_LIST_KEY = "currency-list";

    public static function getConversionKey($sourceCurrency, $targetCurrency) {
        return $sourceCurrency."_".$targetCurrency;
    }
    
    public static function getRateListKey() {
        return Currency::BASE_CURRENCY;
    }
    
    public static function getCurrencyListKey() {
        return self::CURRENCY_LIST_KEY;
    }
}
