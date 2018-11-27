# pjlv
an employee leave management system.

# getting started
in other to get pjlv running on your local enviroment, you have to get the following prerequisites installed

php 7.1.9 or upwards https://windows.php.net/download#php-7.1

composer 1.6.3 or upwards https://getcomposer.org/download/

git 2.13.2 or upwards https://git-scm.com/downloads/

mysql 5.7.19 or upwards https://dev.mysql.com/downloads/

# installing
1. clone the project to your local enviroment using git*

`git clone https://codehub.aiti-kace.com.gh/princeba/pjlv.git`


2. change directory to the pjlv*

`cd pjlv`


3. run composer to install the various dependences*

`composer install`


4. set up your local enviroment

`rename .env.example file to .env`


4.1 set up your database

open .env file in your favourite text editor and configure your database to match your credentials*

DB_DATABASE="database_name"

DB_USERNAME="database_username"

DB_PASSWORD="database_password"


5. migrate the changes to your database*

`php artisan migrate --seed`


6. generate an application key, required by laravel framework*

`php artisan key:generate`


# running
after installing and configuring, you are now set to run the project*

`php artisan serve`

visit your browser on http://localhost:8000

boom! boom!! boom!!! 
enjoy :)


# credits
[dorothy]
[mighty]
[mawuli](https://codehub.aiti-kace.com.gh/mawuli)
[masare](https://codehub.aiti-kace.com.gh/masare)
[owuraku](https://codehub.aiti-kace.com.gh/owuraku)
[princeba](https://codehub.aiti-kace.com.gh/princeba)

# source
Laravel framework https://laravel.com/

Template by creativetim material dashboard https://www.creative-tim.com/product/material-dashboard
