<?php
require_once('RateLimiter.php');

if (!extension_loaded('redis')) {
	echo 'Redis extension is not loaded.';
	exit;
}

	$redis = new \Redis();
	$redis->connect('127.0.0.1', 6379);
	$redis->auth('123456');

	$rateLimiter = new RateLimiter($redis);

	// use simpleLimitCall
	$key    = "localhost";
	$limit  = 10;
	$expire = 60;

	$rateLimiterRes = $rateLimiter->simpleLimitCall($key, $limit, $expire);

	echo $rateLimiterRes;

	// use strictLimitCall
	$key    = "localhost";
	$limit  = 10;
	$expire = 60;

	$rateLimiterRes = $rateLimiter->simpleLimitCall($key, $limit, $expire);

	echo $rateLimiterRes;
