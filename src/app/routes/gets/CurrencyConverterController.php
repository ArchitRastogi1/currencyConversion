<?php

namespace routes\gets;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use services\CurrencyService;
use models\requests\ConversionRequest;
use Monolog\Logger;
use exceptions\ValidationException;

class CurrencyConverterController {
    
    private $logger;
    private $currencyService;
    
    public function __construct(Logger $logger, CurrencyService $currencyService) {
        $this->logger = $logger;
        $this->currencyService = $currencyService;
    }
    
    public function convertCurrency(Request $request, Response $response) {
        $args = $this->getArguments($request);
        $conversionRequest = new ConversionRequest($args);
        $this->logger->addInfo("Request for :".$conversionRequest);
        try {
            $data = $this->currencyService->convertCurrency($conversionRequest);
            $response->getBody()->write(json_encode($data));
            return $response;
        } catch(ValidationException $ex) {
            
        }
    }
    
    public function currencyRateList(Request $request, Response $response) {
        $args = $this->getArguments($request);
        $sourceCurrency = $args['sourceCurrency'];
        $this->logger->addInfo("Request for :".$sourceCurrency);
        
        $data = $this->currencyService->getCurrencyRateList($sourceCurrency);
        $response->getBody()->write(json_encode($data));
        return $response;
    }
    
    private function getArguments(Request $request) {
        return $request->getAttributes()['route']->getArguments();
    }
}
