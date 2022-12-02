<?php

declare(strict_types=1);

use PLUS\GrumPHPConfig\RectorSettings;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->parallel();
    $rectorConfig->importNames();
    $rectorConfig->importShortClasses();

    $rectorConfig->paths(
        array_filter([
            is_dir(__DIR__ . '/src') ? __DIR__ . '/src' : null,
            is_dir(__DIR__ . '/extensions') ? __DIR__ . '/extensions' : null,
            is_dir(__DIR__ . '/Classes') ? __DIR__ . '/Classes' : null,
        ])
    );

    // define sets of rules
    $rectorConfig->sets(
        [
            ...RectorSettings::sets(),
        ]
    );

    // remove some rules
    // ignore some files
    $rectorConfig->skip(
        [
            ...RectorSettings::skip(),

            /**
             * rector should not touch these files
             */
            //__DIR__ . '/src/Example',
            //__DIR__ . '/src/Example.php',
        ]
    );
};
