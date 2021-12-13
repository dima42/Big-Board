<?php
use Predis\Client;

class Cache {
    public $redis;
    public $default_max_age;

    public function __construct() {
        $this->redis = new Predis\Client(getenv('REDIS_URL'));
        $this->default_max_age = getenv("MAX_CACHE_AGE");
    }    

    public function add($key, $value) {
        $val = [time(), $value];
        $json_blob = json_encode($val);
        $this->redis->set($key, $json_blob);
    }

    public function existsNoOlderThan($key, $max_age) {
        $now = time();
        if ($this->redis->exists($key)) {
            $json_blob = $this->redis->get($key);
            $val = json_decode($json_blob, true);
            $age = $now - $val[0];
            if ($age < $max_age) {
                return true;
            }
        }
        return false;
    }

    public function get($key, $callable, $max_age=-1) {
        if ($max_age < 0) {
            $max_age=$this->default_max_age;
        }
        // have some randomness in expiration so it gets reset incrementally
        $offset = rand(0, $max_age/2);
        $modified_max_age = $max_age-$offset;
        

        if ($this->existsNoOlderThan($key, $modified_max_age)) {
            $json_blob = $this->redis->get($key);
            $val = json_decode($json_blob, true);
            //error_log("cache hit for $key");
            return $val[1];
        }

        //error_log("cache missed for $key");
        $new_val = $callable();
        $this->add($key, $new_val);
        return $new_val;
    }
}
