Image library
======
* An image uploading tool, having both a web page and a Rest API developed using Symfony 4.4.7.

## System requirments / dependencies:
* Apache running on a linux server [512MB+ Ram] 
* PHP 7.3 
* Composer
* MySQL  

## Project setup 
* run the command "composer install" 
* run the command "php bin/console cache:clear"
* run the command "php bin/console doctrine:database:create"
* run the command "php bin/console doctrine:schema:update --force" 

## Project running
* run command "bin/console server:run" to run the application in your system
* access url provided from terminal or run locally

## Project API description
**POST API using form-data ( baseurl/api/image/upload )**
* In POST API request body, add 'file[]' as key and select a number of files to be uploaded

**GET API with parameters ( baseurl/api/image?limit=10&page=1)**
* In this GET API, query params 'limit' and 'page' are optional.
* 'limit' sets the number of entries shown per page
* 'page' sets offset for entries shown, returns all entries if 'limit' parameter is not specified

**Web page displaying images and image upload functionality ( baseurl/image-library?limit=10&page=1)**
* In this web page, query params 'limit' and 'page' are optional.
* Query params are added via various buttons present in web page.
* 'limit' sets the number of entries shown per page
* 'page' sets offset for entries shown, returns all entries if 'limit' parameter is not specified.

