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


// get tweets
$app->get('/tweets/:handle', function($handle) use($app) {
	$data = getTweets($handle, 10);
	echo toJSON($app, $data);
});



// make sure things work
$app->get('/test/', function() use($app){
	$data = array(
		"name" => 2,
		"phone" => array(
			"work" 	 => "8842853",
			"mobile" => "09471988697"
		)
	);
	echo toJSON($app, $data);
});



// start listening for request
//$app->run();

header('Content-Type: application/json');
echo getTweets('ghelobytes',10);


function toJSON($app, $content){
	$response = $app->response;
    $response['Content-Type'] = 'application/json';
    $response->body( json_encode($content) );
};


// seen from: http://stackoverflow.com/questions/20733963/using-oauth2-in-php-for-accessing-twitter-moving-it-to-google-app-engine
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
			'header'  => "Authorization: Bearer " . $bearerToken . "\r\n".
						 "\r\n".
						 "grant_type=client_credentials",
		),
	));

	$encodedData = file_get_contents('https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name='."$screen_name".'&count='."$count", false, $context);
	return $encodedData;
};

