[![Packagist](https://img.shields.io/packagist/v/pluswerk/grumphp-config.svg?style=flat-square)](https://packagist.org/packages/pluswerk/grumphp-config)
[![Packagist](https://img.shields.io/packagist/l/pluswerk/grumphp-config.svg?style=flat-square)](https://opensource.org/licenses/LGPL-3.0)
[![Code Climate](https://img.shields.io/codeclimate/maintainability/pluswerk/grumphp-xliff-task.svg?style=flat-square)](https://codeclimate.com/github/pluswerk/grumphp-xliff-task)

# Install

````bash
composer require pluswerk/grumphp-config --dev
````

pluswerk/grumphp-config will create `grumphp.yml`, `rector.php` and require some project specific resources if necessary 

### You want to override settings?:

Look into your generated grumphp.yml

### Upgrade to grumphp-config 5

if you upgrade and not start a new Project you should set the `convention.phpstan_level` to `0` or `1`  
so the upgrade is not that painfull for now  
you should gradually increase the phpstan level until you reach the `max` level
