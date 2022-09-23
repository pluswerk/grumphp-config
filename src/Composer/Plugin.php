<?php

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
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use GrumPHP\Event\TaskEvents;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    /** @var string */
    private const PACKAGE_NAME = 'pluswerk/grumphp-config';

    /** @var string */
    private const DEFAULT_CONFIG_PATH = 'vendor/' . self::PACKAGE_NAME . '/grumphp.yml';

    protected Composer $composer;

    protected IOInterface $io;

    /** @var array<string, mixed> */
    protected array $extra = [];

    protected bool $shouldSetConfigPath = false;

    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->composer = $composer;
        $this->io = $io;
        $this->extra = $this->composer->getPackage()->getExtra();
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PackageEvents::POST_PACKAGE_UPDATE => 'postPackageUpdate',
            PackageEvents::POST_PACKAGE_INSTALL => 'postPackageInstall',
            PackageEvents::PRE_PACKAGE_UNINSTALL => 'prePackageUninstall',

            ScriptEvents::POST_INSTALL_CMD => 'runScheduledTasks',
            ScriptEvents::POST_UPDATE_CMD => 'runScheduledTasks',

            ScriptEvents::POST_AUTOLOAD_DUMP => 'postAutoloadDump',
        ];
    }

    public function postPackageUpdate(PackageEvent $event): void
    {
        $operation = $event->getOperation();
        if ($operation instanceof UpdateOperation && $operation->getTargetPackage()->getName() === self::PACKAGE_NAME) {
            $this->shouldSetConfigPath = true;
        }
    }

    public function postPackageInstall(PackageEvent $event): void
    {
        $operation = $event->getOperation();
        if ($operation instanceof InstallOperation && $operation->getPackage()->getName() === self::PACKAGE_NAME) {
            $this->shouldSetConfigPath = true;
        }
    }

    public function prePackageUninstall(PackageEvent $event): void
    {
        $operation = $event->getOperation();
        if ($operation instanceof UninstallOperation && $operation->getPackage()->getName() === self::PACKAGE_NAME) {
            $this->removeConfigPath();
        }
    }

    public function runScheduledTasks(): void
    {
        if ($this->shouldSetConfigPath) {
            $this->setConfigPath();
        }
    }

    //ACTIONS:

    public function setConfigPath(): void
    {
        if ($this->getExtra(self::PACKAGE_NAME . '.auto-setting') === false) {
            $this->message('not setting config path, extra.' . self::PACKAGE_NAME . '.auto-setting is false', 'yellow');
            return;
        }

        $this->setExtra(self::PACKAGE_NAME . '.auto-setting', true);
        if ($this->getExtra('grumphp.config-default-path') !== self::DEFAULT_CONFIG_PATH) {
            $this->setExtra('grumphp.config-default-path', self::DEFAULT_CONFIG_PATH);
            $this->message('auto setting grumphp.config-default-path', 'green');
        }
    }

    public function removeConfigPath(): void
    {
        if ($this->getExtra(self::PACKAGE_NAME . '.auto-setting') === false) {
            $this->message('not removing config path, extra.' . self::PACKAGE_NAME . '.auto-setting is false', 'yellow');
            return;
        }

        unset($this->extra[self::PACKAGE_NAME]);
        $this->removeExtra(self::PACKAGE_NAME);

        $key = null;
        if ($this->getExtra('grumphp')) {
            $key = 'grumphp.config-default-path';
        } elseif ($this->getExtra()) {
            $key = 'grumphp';
        }

        $this->removeExtra($key);
        $this->message('auto removed config path and ' . self::PACKAGE_NAME . ' settings', 'green');
    }

    // HELPER:

    /**
     * @param string|null $name
     * @return mixed
     */
    public function getExtra(string $name = null)
    {
        if ($name === null) {
            return $this->extra;
        }

        $arr = $this->extra;
        $bits = explode('.', $name);
        $last = array_pop($bits);
        foreach ($bits as $bit) {
            $arr[$bit] ??= [];

            if (!is_array($arr[$bit])) {
                return null;
            }

            $arr = &$arr[$bit];
        }

        return $arr[$last] ?? null;
    }

    /**
     * @param string|bool $value
     */
    public function setExtra(string $name, $value): void
    {
        $configSource = $this->composer->getConfig()->getConfigSource();
        $configSource->addProperty('extra.' . $name, $value);
    }

    public function removeExtra(string $name = null): void
    {
        $key = 'extra';
        if ($name !== null) {
            $key .= '.' . $name;
        }

        $configSource = $this->composer->getConfig()->getConfigSource();
        $configSource->removeProperty($key);
    }

    public function message(string $message, string $color = null): void
    {
        $colorStart = '';
        $colorEnd = '';
        if ($color) {
            $colorStart = '<fg=' . $color . '>';
            $colorEnd = '</fg=' . $color . '>';
        }

        $this->io->write(self::PACKAGE_NAME . ': ' . $colorStart . $message . $colorEnd);
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


    // Rector config copy:

    public function postAutoloadDump(): void
    {
        $this->createRectorConfig();
    }

    private function createRectorConfig(): void
    {
        if (!file_exists(getcwd() . '/rector.php')) {
            copy(dirname(__DIR__, 2) . '/rector.php', getcwd() . '/rector.php');
            $this->message('rector.php file created', 'yellow');
        }
    }
}
