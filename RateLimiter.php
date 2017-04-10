<?php

class RateLimiter
{
	protected $redis;
	
	public function __construct(\Redis $redis)
	{
		$this->redis = $redis;
	}
	
	/**
	 * this function is realized by a auto-increment key of redis
	 * @param  string  $key    [primary key, it can be ip userid cookie and so on]
	 * @param  integer $limit  [limited times]
	 * @param  integer $expire [limited period (seconds)]
	 * @return [boolean]       [true if pass the limit, false if fail limit]
	 */
	public function simpleLimit($key = "", $limit = 10, $expire = 1)
	{
		$current = $this->redis->get($key);
		
		if ($current && $current >= $limit) {
			return false;
		} else {
			$ttl = $this->redis->ttl($key);
			
			$this->redis->multi();
			$this->redis->incr($key);
			
			if ($ttl < 0) {
				$this->redis->expire($key, $expire);
			}
			
			$this->redis->exec();
			
			return true;
		}
	}

	/**
	 * [this function is realized by redis List. It provide more strict rule for rate limit]
	 * @param  string  $key    [primary key, it can be ip userid cookie and so on]
	 * @param  integer $limit  [limited times]
	 * @param  integer $expire [limited period (seconds)]
	 * @return [boolean]       [true if pass the limit, false if fail limit]
	 */
	public function strictLimit($key = "", $limit = 10, $expire = 1)
	{
		$length = $this->redis->lLen($key);

		$now    = time();

		if ($length && $length >= $limit) {
			$time = $this->redis->lIndex($key, -1);

			if ($now - $time < $expire) {
				return false;
			} else {
				$this->redis->multi();

				$this->redis->lPush($key, $now);
				$this->redis->Ltrim($key, 0, $limit - 1);
				$this->redis->expire($key, $expire);

				$this->redis->exec();

				return true;
			}
		} else {
			$this->redis->multi(\Redis::MULTI);

			$this->redis->lPush($key, $now);
			$this->redis->expire($key, $expire);

			$this->redis->exec();

			return true;
		}
	}
}
