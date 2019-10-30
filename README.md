[![Packagist](https://img.shields.io/packagist/v/pluswerk/grumphp-config.svg?style=flat-square)](https://packagist.org/packages/pluswerk/grumphp-config)
[![Packagist](https://img.shields.io/packagist/l/pluswerk/grumphp-config.svg?style=flat-square)](https://opensource.org/licenses/LGPL-3.0)
[![Code Climate](https://img.shields.io/codeclimate/maintainability/pluswerk/grumphp-xliff-task.svg?style=flat-square)](https://codeclimate.com/github/pluswerk/grumphp-xliff-task)

# Install

````bash
composer require pluswerk/grumphp-config --dev
````

pluswerk/grumphp-config will add the required ``extra.grumphp.config-default-path`` automatically to your ``composer.json``.

If pluswerk/grumphp-config should not edit your composer.json then you must add this:
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

There you can override some convention:


| Key                                 | Default                       |
|-------------------------------------|-------------------------------|
| convention.process_timeout          | 60                            |
| convention.jsonlint_ignore_pattern  | []                            |
| convention.xmllint_ignore_pattern   | []                            |
| convention.yamllint_ignore_pattern  | []                            |
| convention.phpcslint_ignore_pattern | []                            |
| convention.xlifflint_ignore_pattern | ["#typo3conf/l10n/(.*)#"]     |
