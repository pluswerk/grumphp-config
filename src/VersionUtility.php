<?php

declare(strict_types=1);

namespace PLUS\GrumPHPConfig;

use Composer\Semver\VersionParser;

use function json_decode;
use function getcwd;

final class VersionUtility
{
    public static function getMinimalPhpVersion(): ?string
    {
        $phpVersionConstrain = self::getRootComposerJsonData()['require']['php'] ?? false;
        if (!$phpVersionConstrain) {
            return null;
        }

        if (!is_string($phpVersionConstrain)) {
            return null;
        }

        $parser = new VersionParser();
        $lowerPhpVersion = $parser->parseConstraints($phpVersionConstrain)->getLowerBound()->getVersion();
        if (!preg_match('#(?<major>\d)\.(?<minor>\d)\..*#', $lowerPhpVersion, $matches)) {
            return null;
        }

        return $matches['major'] . $matches['minor'];
    }

    /**
     * @return array<mixed>
     */
    private static function getRootComposerJsonData(): array
    {
        $contents = file_get_contents(getcwd() . '/composer.json');
        return json_decode((string)$contents, true, 512, JSON_THROW_ON_ERROR) ?: [];
    }
}
