<?php

namespace validators;
use models\requests\ConversionRequest;
use models\errors\ValidationError;
use models\Currency;
use exceptions\ValidationException;

class RequestValidator {
    
    public function validateConversionRequest(ConversionRequest $conversionRequest, $currencyList) {
        if(!in_array($conversionRequest->getFrom(), $currencyList)) {
            throw new ValidationException(ValidationError::FROM_CURRENCY_ERROR, 400);
        } 
        if(!in_array($conversionRequest->getTo(), $currencyList)) {
            throw new ValidationException(ValidationError::TO_CURRENCY_ERROR, 400);
        }
        if(!is_numeric($conversionRequest->getAmount())) {
            throw new ValidationException(ValidationError::QUANTITY_ERROR, 400);
        }
    }
    
    public function validateCurrencySource($currencySource, $currencyList) {
        if(!in_array($currencySource, $currencyList)) {
            throw new ValidationException(ValidationError::FROM_CURRENCY_ERROR, 400);
        }
    }
    
}

