var tweetApp = angular.module('tweetApp', ['ngResource', 'ui.bootstrap']);


tweetApp.factory('Tweets',function($resource){
	return $resource('/api/tweets/');

});



tweetApp.controller('tweetController', function ($scope, Tweets) {
	
	$scope.radioModel = 'Date';
	$scope.tweets={};
	$scope.updateTweets = function(lat, lng){
		Tweets.get({geocode: lat + ',' + lng + ',1km'},function(data){
			$scope.tweets = data.statuses;
		});	
	};
	
	$scope.predicate='-twitter_name';
	
});

// inspired by: http://jsfiddle.net/austinnoronha/nukRe/light/
function invokeCtrlUpdate(e){
	var scope = angular.element(document.getElementById('tweets')).scope();
	scope.$apply(function(){
		scope.updateTweets(e.latlng.lat, e.latlng.lng);
	});
}

function initMap(){

	var map = L.map('map').setView([-33.8719638, 151.20932819999996], 13);

	L.tileLayer('https://{s}.tiles.mapbox.com/v3/{id}/{z}/{x}/{y}.png', {
		maxZoom: 18,
		attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
			'<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
			'Imagery © <a href="http://mapbox.com">Mapbox</a>',
		id: 'examples.map-i875mjb7'
	}).addTo(map);


	var m;
	var c;
	map.on('click', function(e){
		
		console.log(e.latlng);
		
		if(c)
			map.removeLayer(c);
		c = new L.circle(e.latlng, 1000, {
			color: 'red',
			fillColor: '#f03',
			fillOpacity: 0.25
		});
		map.addLayer(c);
		
		invokeCtrlUpdate(e);
		
	});

}