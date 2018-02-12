[![Packagist](https://img.shields.io/packagist/v/pluswerk/grumphp-config.svg?style=flat-square)](https://packagist.org/packages/pluswerk/grumphp-config)
[![Packagist](https://img.shields.io/packagist/l/pluswerk/grumphp-config.svg?style=flat-square)](https://opensource.org/licenses/LGPL-3.0)
[![Code Climate](https://img.shields.io/codeclimate/github/pluswerk/grumphp-xliff-task.svg?style=flat-square)](https://codeclimate.com/github/pluswerk/grumphp-xliff-task)

# Install

````bash
composer require pluswerk/grumphp-config --dev
````

pluswerk/grumphp-config will add the required ``extra.grumphp.config-default-path`` automatically to your ``composer.json``.

if pluswerk/grumphp-config should not edit your composer.json than you must add this:
````json
{
  "extra": {
    "pluswerk/grumphp-config": {
      "auto-setting": false
    }
  }
}
````

### You want to override settings?:


Make a new grumphp.yml config file. You can put it in the root folder.
````yaml
imports:
  - { resource: vendor/pluswerk/grumphp-config/grumphp.yml }


parameters:
  convention.xmllint_ignore_pattern:
    - "typo3conf/ext/extension/Resources/Private/Templates/List.xml"
````

there you can override some convention:


| Key                                 | Default                       |
|-------------------------------------|-------------------------------|
| convention.xmllint_ignore_pattern   | []                            |
| convention.yamllint_ignore_pattern  | []                            |
| convention.xlifflint_ignore_pattern | "#typo3conf/l10n/(.*)#"       |
