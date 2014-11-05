Tweet GeoFinder
================================

An app for Cloudsherpas coding exam. Geolocate tweets within 1 kilometer of desired location in the map.
Live demo at [http://ghelobytes-cloudsherpas.appspot.com/](http://ghelobytes-cloudsherpas.appspot.com/).

### Tools
- PHP
- Slimframework
- AngularJS
- Twitter Bootstrap (css)
- LeafletJS


### Test
    dev_appserver.py c:\users\ghelo\desktop\cloudsherpas\ghelobytes-cloudsherpas

### Authenticate (before deployment)
    gcloud auth login
	
### Deploy
    appcfg.py update c:\users\ghelo\desktop\cloudsherpas\ghelobytes-cloudsherpas
