# RateLimiter
a php rate limit class realized by redis

RateLimiter provide two method to realize rate limit function.

## 1 simpleLimit
use an auto-increment key of redis to rate limit

## 2 strictLimit
use List key of redis to rate limit

## 3 Usage

```
<?php
require_once('RateLimiter.php');

if (!extension_loaded('redis')) {
	echo 'Redis extension is not loaded.';
	exit;
}

$redis = new \Redis();
$redis->connect('127.0.0.1', 6379);
$redis->auth('123456');

$key    = "localhost";
$limit  = 10;
$expire = 60;

// init
$rateLimiter = new RateLimiter($redis);

// use simpleLimit
$rateLimiterRes = $rateLimiter->simpleLimit($key, $limit, $expire);
echo $rateLimiterRes;

// use strictLimit
$rateLimiterRes = $rateLimiter->strictLimit($key, $limit, $expire);
echo $rateLimiterRes;

```
