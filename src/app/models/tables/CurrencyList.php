<?php

namespace models;

class CurrencyList {
    
    private $currencyCode;
    private $currencyName;
    private $isObsolete;
    
    public function __construct($args) {
        $this->currencyCode = $args['iso'];
        $this->currencyName = $args['currency_name'];
        $this->isObsolete = $args['is_obsolete'];
    }

    
    public function getCurrencyCode() {
        return $this->currencyCode;
    }

    public function getCurrencyName() {
        return $this->currencyName;
    }

    public function getIsObsolete() {
        return $this->isObsolete;
    }

    public function setCurrencyCode($currencyCode) {
        $this->currencyCode = $currencyCode;
    }

    public function setCurrencyName($currencyName) {
        $this->currencyName = $currencyName;
    }

    public function setIsObsolete($isObsolete) {
        $this->isObsolete = $isObsolete;
    }

}

