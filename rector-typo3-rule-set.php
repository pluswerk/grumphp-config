<?php

declare(strict_types=1);

use Ssch\TYPO3Rector\CodeQuality\General\ConvertImplicitVariablesToExplicitGlobalsRector;
use Ssch\TYPO3Rector\CodeQuality\General\InjectMethodToConstructorInjectionRector;
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
