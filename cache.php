<?php
use Predis\Client;

class Cache {
    public $redis;

    public function __construct() {
        $this->redis = new Predis\Client(getenv('REDIS_URL'));
    }    

    public function add($key, $value) {
        $val = [time(), $value];
        $json_blob = json_encode($val);
        $this->redis->set($key, $json_blob);
    }

    public function get($key, $callable, $max_age=180) {
        $now = time();

        // have some randomness in expiration so it gets reset incrementally
        $offset = rand(0, $max_age/2);
        $modified_max_age = $max_age-$offset;
        
        if ($this->redis->exists($key)) {
            $json_blob = $this->redis->get($key);
            $val = json_decode($json_blob, true);
            $age = $now - $val[0];
            if ($age < $modified_max_age) {
                return $val[1];
            }
        } 
                
        $new_val = $callable();
        $this->add($key, $new_val);
        return $new_val;
    }
}
