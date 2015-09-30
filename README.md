# phalcon-boilerplate
This is a boilerplate template written in PHP Phalcon Framework. Its purpose is to get you up and running for your web application development. It includes:

* Authentication and ACL
    * Uses security plugin for authentication and roles based ACL management
* Session
* Database Abstraction Layer
    * Uses Phalcon MySQL Adaptor
* CRUD (Create, Read, Update and Delete)
    * Provides sample for CRUD actions on a Model
* Pagination
* Third party libraries using Composer
    * Provides example on how to integrate third party libraries (PHPExcel, PHPMailer) in your application
* Logging
    * Provides application and database logging
* Grunt Integration
    * Uses Grunt.js to concat and minify javascript and css files for production deployment

## Get Started
### What do I need?
On my local machine I have the following versions installed.
* Apache 2.4.10
* PHP 5.5.20
* Phalcon 2.0.0
* MySQL 5.5.31

### Phalcon Framework
I assume you already have Phalcon framework installed. If not, then follow instructions at [Phalcon Website](http://phalconphp.com).

### Apache
Here is sample configuration to get the site running on port 9003.

    Listen 9003
    <VirtualHost *:9003>
        ServerName localhost:9003
        ServerAdmin <your-email-address>
        DocumentRoot "/Library/WebServer/Documents/phalcon-boilerplate/"
        DirectoryIndex "index.html" "index.php"
        <Directory "/Library/WebServer/Documents/phalcon-boilerplate/">
            Options +Indexes +FollowSymLinks +MultiViews +Includes
            AllowOverride All
            Require all granted
        </Directory>
    </VirtualHost>

After the above configuration, you should be able to load the page using [http://localhost:9003](http://localhost:9003)

### MySQL
This example uses MySQL database. Use the included sql file to setup and initailize your database.

### Supporting folders
```
cd phalcon-boilerplate/app
mkdir cache cache/volt cache/security
mkdir logs
touch logs/app.log
chmod -R 777 cache logs
```

### Testing Accounts

The following accounts are setup in database for testing.

| User Type | Email | Password | Notes |
| :-------- | :---- | :------- | :---- |
| Administrators | admin@test.com | admin12345 | Access to all sections of the application |
| Users | user@test.com | user12345 | Access to only certain sections of the application |

## Demo Video
[![Phalcon Boilerplate Setup Demo](http://img.youtube.com/vi/o77tm09LJwM/0.jpg)](http://www.youtube.com/watch?v=o77tm09LJwM)

## Screen shots

### Sign In
![Sign In](https://raw.github.com/enajeeb/phalcon-boilerplate/master/public/img/app/screenshot-login-page.png)

### Dashboard
![Dashboard](https://raw.github.com/enajeeb/phalcon-boilerplate/master/public/img/app/screenshot-dashboard.png)

### List Page
![List Page](https://raw.github.com/enajeeb/phalcon-boilerplate/master/public/img/app/screenshot-list-page.png)

## Disclaimer
I am using minimal features of a template from [bootstraphunter](https://bootstraphunter.com). You are required to purchase your own template.

## Release Notes
### Version 1.0.1 (Date: May 30, 2015)

#### What's New
* Upgraded to Phalcon 2.0.0
* Added support for Composer (Dependency Manager for PHP)
* Cleaned up code to comply with [PSR-1](http://www.php-fig.org/psr/psr-1/) and [PSR-2](http://www.php-fig.org/psr/psr-2/)

### Version 1.0.2 (Date: Sep 07, 2015)

#### What's New
* Added support for Composer Autoloading