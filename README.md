# BileMo API
***
Analyse CodeClimate :
https://codeclimate.com/github/Pierre-Ka/API-OP7/maintainability

Repository GitHub :
https://github.com/Pierre-Ka/API-OP7

This project consists of an API in order to provide access to the BileMo product catalog. The API should be made in PHP with the Symfony framework and must follow the rules of Level 1, 2 and 3 of the Richardson Maturity Model. This API had been developed from scratch.

To run the project you will need to have :
* php 8 
* composer
* mysql
* symfony cli
* symfony

Optionnally you can have : 
* make
* postman

***
## Installation
1. Create a new projet and Clone this repository :
```
    git clone https://github.com/Pierre-Ka/API-OP7.git
```
2. Configure Database : 
* Add and replace in the env. files : DATABASE_URL="mysql://username:password@127.0.0.1:3306/dbname"
* Open Apache server
3. Install the dependencies :
```
    composer install
    php bin/console cache:clear
```
4. Run command the following command if you have make :
```
   make prepare
```
or these ones if not
```
   php bin/console doctrine:database:drop --if-exists --force --env=dev
   php bin/console doctrine:database:create --env=dev
   php bin/console doctrine:schema:update --force --env=dev
   php bin/console doctrine:fixtures:load -n --env=dev
```
5. Generate the SSL keys for JWT authentication:
```
    php bin/console lexik:jwt:generate-keypair
```
6. Run server :
```
    symfony server:start
```

You can now connect to the API and start send requests.

***
## Documentation

Lets go to YOUR_LOCAL_URL/api/doc to enjoy the api's documentation ! ⚡
