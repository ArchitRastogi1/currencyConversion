<?php

namespace exceptions;
use Slim\Http\Request;
use Slim\Http\Response;
use Exception;

class ExceptionHandler {
    
    public function __invoke(Request $request, Response $response, Exception $ex) {
        
        $data = [
            'message' => $ex->getMessage()
        ];  
        
        return $response->withStatus($ex->getCode())
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode($data));
    }
}

