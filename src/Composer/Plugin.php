<?php

declare(strict_types=1);

namespace PLUS\GrumPHPConfig\Composer;

use Composer\Composer;
use Composer\DependencyResolver\Operation\InstallOperation;
use Composer\DependencyResolver\Operation\UninstallOperation;
use Composer\DependencyResolver\Operation\UpdateOperation;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\ScriptEvents;
use GrumPHP\Configuration\Configuration;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

final class Plugin implements PluginInterface, EventSubscriberInterface
{
    private Composer $composer;

    private IOInterface $io;

    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    /**
     * {@inheritdoc}
     */
    public function deactivate(Composer $composer, IOInterface $io): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function uninstall(Composer $composer, IOInterface $io): void
    {
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PackageEvents::POST_PACKAGE_UPDATE => 'heavyProcessing',
            PackageEvents::POST_PACKAGE_INSTALL => 'heavyProcessing',

            ScriptEvents::POST_INSTALL_CMD => 'heavyProcessing',
            ScriptEvents::POST_UPDATE_CMD => 'heavyProcessing',

            ScriptEvents::POST_AUTOLOAD_DUMP => 'simpleProcessing',
        ];
    }

    public function heavyProcessing(): void
    {
        $this->removeOldConfigPath();
        $this->createGrumphpConfig();

        $this->simpleProcessing();
    }

    public function simpleProcessing(): void
    {
        $this->createRectorConfig();
    }

    private function removeOldConfigPath(): void
    {
        $rootPackage = $this->composer->getPackage();
        $extra = $rootPackage->getExtra();
        $configSource = $this->composer->getConfig()->getConfigSource();

        $configDefaultPath = $extra['grumphp']['config-default-path'] ?? '';
        if (in_array($configDefaultPath, ['grumphp.yml', 'vendor/pluswerk/grumphp-config/grumphp.yml'])) {
            unset($extra['grumphp']['config-default-path']);
            $configSource->removeProperty('extra.grumphp.config-default-path');
            $this->message('removed extra.grumphp.config-default-path', 'yellow');
            if (empty($extra['grumphp'])) {
                unset($extra['grumphp']);
                $configSource->removeProperty('extra.grumphp');
                $this->message('removed extra.grumphp', 'yellow');
            }
        }

        if (isset($extra['pluswerk/grumphp-config'])) {
            unset($extra['pluswerk/grumphp-config']);
            $configSource->removeProperty('extra.pluswerk/grumphp-config');
            $this->message('removed extra.pluswerk/grumphp-config', 'yellow');
        }

        $rootPackage->setExtra($extra);
    }

    private function createRectorConfig(): void
    {
        if (!file_exists(getcwd() . '/rector.php')) {
            copy(dirname(__DIR__, 2) . '/rector.php', getcwd() . '/rector.php');
            $this->message('rector.php file created', 'yellow');
        }
    }

    private function createGrumphpConfig(): void
    {
        $grumphpPath = getcwd() . '/grumphp.yml';
        $grumphpTemplatePath = dirname(__DIR__, 2) . '/grumphp.yml';
        if (!file_exists($grumphpPath)) {
            $defaultImport = [
                'imports' => [
                    ['resource' => 'vendor/pluswerk/grumphp-config/grumphp.yml'],
                ],
            ];
            file_put_contents($grumphpPath, Yaml::dump($defaultImport));
            $this->message('grumphp.yml file created', 'yellow');
        }

        $data = Yaml::parseFile($grumphpPath);

        if (($data['imports'][0]['resource'] ?? '') !== 'vendor/pluswerk/grumphp-config/grumphp.yml') {
            return;
        }

        $changed = false;

        $templateData = Yaml::parseFile($grumphpTemplatePath);
        foreach ($templateData['parameters'] ?? [] as $key => $value) {
            if (!str_starts_with((string)$key, 'convention.')) {
                continue;
            }

            if (($data['parameters'][$key] ?? null) === $value) {
                continue;
            }

            $data['parameters'][$key] = $value;
            $changed = true;
        }

        if ($changed) {
            file_put_contents($grumphpPath, Yaml::dump($data));
            $this->message('added some default conventions to grumphp.yml', 'yellow');
        }
    }

    // HELPER:

    private function message(string $message, string $color = null): void
    {
        $colorStart = '';
        $colorEnd = '';
        if ($color) {
            $colorStart = '<fg=' . $color . '>';
            $colorEnd = '</fg=' . $color . '>';
        }

        $this->io->write('pluswerk/grumphp-config' . ': ' . $colorStart . $message . $colorEnd);
    }
}
