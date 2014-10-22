<?php

$config = array(
	'oauth_access_token' 		=> "69195175-c7otAu5F3itQpekt9hssEZdxG7Ht6NnL5Sz9aOokE",
	'oauth_access_token_secret' => "GbRqnT4aXnuEu8wK7I8A9VcgjODufSGs1mkYaRj8Hpu4M",
	'consumer_key' 				=> "OKsXLbdNUviMhG1a0TaPW6nLT",
	'consumer_secret' 			=> "QUhccGzseNtaeFd74F6VweoL8ZwbR09pNcLzK8HxrXNRIEv4jP",
	'server_port'				=> 80
);



// apparently this must be set for Slim to work on GAE
$_SERVER['SERVER_PORT'] = $config['server_port'];

// Slim 
require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();


// create instance
$app = new \Slim\Slim();


// make sure things work
$app->get('/api/test/', function() use($app){
	$data = array(
		"now" => (new \DateTime())->format('Y-m-d H:i:s'),
		"php_version" => phpversion()
	);
	echo toJSON($app, $data);
});


// get tweets
$app->get('/api/tweets/:screen_name/:count', function($screen_name, $count) use($app) {
	$data = getTweets($screen_name, $count);
	echo toJSON($app, $data);
});



// start listening for request
$app->run();









// ================================================================= //



function toJSON($app, $content){
	$r = $app->response;
    $r['Content-Type'] = 'application/json';
    $r->body( json_encode($content) );
};


// seen from: http://stackoverflow.com/questions/20733963/using-oauth2-in-php-for-accessing-twitter-moving-it-to-google-app-engine
// wasted hours on "grant_type=client_credentials" line on 2nd phase of authorization (bearer)
// good thing I checked https://dev.twitter.com/oauth/application-only
function getTweets($screen_name, $count){
	global $config;

	$authContext = stream_context_create(array(
		'http' => array(
			'method'  => 'POST',
			'header'  => "Authorization: Basic " . base64_encode(($config['consumer_key']).':'.($config['consumer_secret'])) . "\r\n".
						 "Content-type: application/x-www-form-urlencoded;charset=UTF-8",
			'content' => 'grant_type=client_credentials',
		),
	));
	$authResponse = file_get_contents("https://api.twitter.com/oauth2/token", false, $authContext);
	
	$decodedAuth = json_decode($authResponse, true);
	$bearerToken = $decodedAuth["access_token"];

	$context = stream_context_create(array(
		'http' => array(
			'method'  => 'GET',
			'header'  => "Authorization: Bearer " . $bearerToken
						 //"\r\n".
						 //"grant_type=client_credentials",
		),
	));

	$encodedData = file_get_contents('https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name='."$screen_name".'&count='."$count", false, $context);
	return json_decode($encodedData);
};

