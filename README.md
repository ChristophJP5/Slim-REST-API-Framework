# Slim-REST-API-Framework
Ein PHP Framework zum schnellen und sauberen erstellen von REST API's 

## Installation
If you want to use a DB connection change the settings `/Config/Config.php`
In `Core/Controller.php` is an uservalidation method
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
