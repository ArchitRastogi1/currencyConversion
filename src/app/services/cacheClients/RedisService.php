<?php

namespace services\cacheClients;
use Predis\Client;
use services\cacheClients\CacheService;

class RedisService implements CacheService {
    
    private $redis;
    
    public function __construct() {
        if(empty($this->redis)) {
            $this->redis = new Client('tcp://127.0.0.1:6379'."?read_write_timeout=0");
        }
    }
    
    public function getData($key) {
        $data = $this->redis->get($key);
        return json_decode($data, true);
    }
    
    public function setData($key, $content) {
        $this->redis->set($key, json_encode($content));
    }
    
    public function flushData() {
        $this->redis->flushAll();
    }
}

