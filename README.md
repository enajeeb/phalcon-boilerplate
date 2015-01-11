# phalcon-boilerplate
This is a Boilerplate template written in PHP Phalcon Framework. Its purpose is to get you up and running for your application development. It includes:

* Authentication and ACL
    ** Uses security plugin for authentication and roles based ACL management.
* Session
* Database Abstraction Layer
    ** Uses Phalcon MySQL Adaptor.
* CRUD (Create, Read, Update and Delete)
    ** Provides sample for CRUD actions on a Model.
* Pagination
* Third party libraries
    ** Provides example on how to integrate third party libraries in your application.
* Logging
    ** Provides application and database logging.
* Grunt Integration
    ** Uses Grunt.js to concat and minify javascript and css files for production deployment.

## Get Started
### Phalcon Framework
I assume you already have Phalcon framework installed. If not, then follow instructions at [Phalcon Website](http://phalconphp.com). As of this writing I am using phalcon verion 1.3.3.

### Apache
My current Apache is on version 2.4.9. Here is sample configuration to get the site running on port 9003.

    Listen 9003
    <VirtualHost *:9003>
        ServerName localhost:9003
        ServerAdmin <your-email-address>
        DocumentRoot "/Library/WebServer/Documents/phalcon-boilerplate/"
        DirectoryIndex "index.html" "index.php"
        AddType application/x-httpd-php html
        <Directory "/Library/WebServer/Documents/phalcon-boilerplate/">
            Options +Indexes +FollowSymLinks +MultiViews +Includes
            AllowOverride All
            Require all granted
        </Directory>
    </VirtualHost>

### Setup Database
This example uses MySQL database. Use the included sql file to setup and initailize your database.

### Testing Accounts

| User Type | Email | Password | Notes |
| :-------- | :---- | :------- | :---- |
| Adminstrators | admin@test.com | admin12345 | Access to all sections for the site |
| Users | user@test.com | user12345 | Access to only certain sections for the site |

