<?php

namespace models\requests;

class ConversionRequest {
    
    private $from;
    private $to;
    private $amount;
    
    public function __construct($args) {
        $this->from = $args['from'];
        $this->to = $args['to'];
        $this->amount = $args['amount'];
    }
    
    public function __toString() {
        return "from:".$this->from." to:".$this->to." quantity:".$this->amount;
    }
    
    public function toArray() {
        return get_object_vars($this);
    }
    
    public function getFrom() {
        return $this->from;
    }

    public function getTo() {
        return $this->to;
    }

    public function getAmount() {
        return $this->amount;
    }

    public function setFrom($from) {
        $this->from = $from;
    }

    public function setTo($to) {
        $this->to = $to;
    }

    public function setAmount($amount) {
        $this->amount = $amount;
    }
    
}

