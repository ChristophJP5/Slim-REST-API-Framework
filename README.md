# Slim-REST-API-Framework
Do you hate spending time setting up framworks? Do you want to take controll? Do you enjoy the task of programming?
This is a framework to create clean REST API's in a fast way. Easy to setup


## Installation
If you want to use a DB connection change the settings `/Config/Config.php`
In `Core/Controller.php` is an uservalidation method.
All Default Error-messages are defined in `Config/Errors.php`

## DB handling
For easy and secure Database handling you can use Models like the usermodel

### Url to method mapping

[Method] GET
`/Users/1`

calls
```
/METHODES/GET/Users.php 
class Users {

  //... 
  public function index(id = -1){
  }
}
```

more explained in the .htaccess

I have a passion to make sharing information fast and easy for everybody and love to help others period.

Any trouble setting this up or some questions?
Feel free to contact me developer@christophjp.de
