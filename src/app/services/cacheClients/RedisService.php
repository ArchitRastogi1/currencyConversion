<?php   

namespace services\cacheClients;
use Predis\Client;
use services\cacheClients\CacheService;
use Exception;
use Monolog\Logger;

class RedisService implements CacheService {
    
    private $redis;
    private $logger;
    
    public function __construct(Logger $logger) {
        $this->logger = $logger;
        if(empty($this->redis)) {
            // this configuration will be moved to a yaml file.
            $this->redis = new Client('tcp://127.0.0.1:6379'."?read_write_timeout=0");
        }
    }
    
    public function getData($key) {
        try  {
            $data = $this->redis->get($key);
            return json_decode($data, true);
        } catch(Exception $ex) {
            $this->logger->addCritical("Some problem with redis ".$ex->getMessage());
        }
    }
    
    public function setData($key, $content) {
        try {
            return $this->redis->set($key, json_encode($content));
        } catch(Exception $ex) {
            $this->logger->addCritical("Some problem with redis ".$ex->getMessage());            
        }
    }
    
    public function flushData() {
        try {
            $this->redis->flushAll();
        } catch(Exception $ex) {
            $this->logger->addCritical("Some problem with redis ".$ex->getMessage());
        }
    }
}

