<?php

declare(strict_types=1);

use Ssch\TYPO3Rector\Rector\General\InjectMethodToConstructorInjectionRector;
use Ssch\TYPO3Rector\Rector\General\ConvertImplicitVariablesToExplicitGlobalsRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    if (!class_exists(InjectMethodToConstructorInjectionRector::class)) {
        return;
    }

    $rectorConfig->rules(
        [
            InjectMethodToConstructorInjectionRector::class,
            ConvertImplicitVariablesToExplicitGlobalsRector::class,
        ]
    );
};
