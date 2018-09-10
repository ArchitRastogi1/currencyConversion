<?php

namespace services\cacheClients;

interface CacheService {
    
    public function getData($key);
    public function setData($key, $content);
    public function flushData();
}

