<?php

declare(strict_types=1);

namespace PLUS\GrumPHPConfig;

use Composer\Semver\VersionParser;

use function json_decode;
use function getcwd;

final class VersionUtility
{
    /**
     * @var array{versions?: array<string, mixed>}
     */
    private static array $installed = [];

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
        return self::readJson(getcwd() . '/composer.json');
    }

    /**
     * we can not use \Composer\InstalledVersions::isInstalled because rector has its own vendor dir with different dependencies.
     */
    public static function isInstalled(string $packageName): bool
    {
        self::$installed = self::$installed ?: require getcwd() . '/vendor/composer/installed.php';
        return isset(self::$installed['versions'][$packageName]) && empty(self::$installed['versions'][$packageName]['dev_requirement']);
    }

    /**
     * we can not use \Composer\InstalledVersions::getVersion because rector has its own vendor dir with different dependencies.
     */
    public static function getVersion(string $packageName): ?string
    {
        self::$installed = self::$installed ?: require getcwd() . '/vendor/composer/installed.php';
        return self::$installed['versions'][$packageName]['version'] ?? null;
    }

    /**
     * @return array<string, string>
     */
    public static function getRequire(string $requireSectionName): array
    {
        $file = dirname(__DIR__) . '/composer.json';
        $composerJsonData = self::readJson($file);
        return $composerJsonData['require-' . $requireSectionName] ?? [];
    }

    /**
     * @return array<string, mixed>
     */
    private static function readJson(string $file): array
    {
        return json_decode((string)(file_get_contents($file)), true, 512, JSON_THROW_ON_ERROR) ?: [];
    }
}
