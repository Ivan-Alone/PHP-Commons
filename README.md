# PHP-Commons
PHP Commons for simple development

## Main data
This is very simple packet manager. You must include main injector `import.php` in your code and all scripts will accessible. Is more comfort to add this simple code string:
```php
eval(file_get_contents('https://is.gd/AXa2Ej'));
```
All will works. It can be unsafe, so you must replace it to:
```php
include 'import.php';
```
in production. Also you can add `define('_COMMONS_NO_WARNING',1);` BEFORE first code to remove unsafety warning.

## Examples

Using Network
```php
<?php
  eval(file_get_contents('https://is.gd/AXa2Ej'));
  import('Network.class.php');
  
  $net = new Network('cookies.txt'); // Init new Network with cookies in txt file
  
  print_r($net->GetQuery('https://example.com/test.json')); // Print test json as array (like in parser)
  
```

Using keyboard commons
```php
<?php
  eval(file_get_contents('https://is.gd/AXa2Ej'));
  import('KeyBoard.class.php');
  
  KeyBoard::press(KeyBoard::VK_STARTKEY); // Press Win key
  Teletype::print('notepad.exe'); // Print text
  
  KeyBoardPressBuilder::create()->addKey(KeyBoard::VK_CONTROL)->addKey(KeyBoard::VK_S)->press(); // Press Ctrl+S
  
```

