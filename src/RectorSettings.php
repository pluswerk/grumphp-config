<?php

declare(strict_types=1);

namespace PLUS\GrumPHPConfig;

use Composer\InstalledVersions;
use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector;
use Rector\CodeQuality\Rector\If_\SimplifyIfElseToTernaryRector;
use Rector\CodeQuality\Rector\Isset_\IssetOnPropertyObjectToPropertyExistsRector;
use Rector\CodingStyle\Rector\ClassMethod\UnSpreadOperatorRector;
use Rector\CodingStyle\Rector\FuncCall\CountArrayToEmptyArrayComparisonRector;
use Rector\CodingStyle\Rector\If_\NullableCompareToNullRector;
use Rector\CodingStyle\Rector\PostInc\PostIncDecToPreIncDecRector;
use Rector\CodingStyle\Rector\Switch_\BinarySwitchToIfElseRector;
use Rector\Php70\Rector\Assign\ListSwapArrayOrderRector;
use Rector\Php74\Rector\LNumber\AddLiteralSeparatorToNumberRector;
use Rector\Privatization\Rector\Property\PrivatizeFinalClassPropertyRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\BooleanAnd\BinaryOpNullableToInstanceofRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictGetterMethodReturnTypeRector;
use Ssch\TYPO3Rector\Rector\Migrations\RenameClassMapAliasRector;
use Ssch\TYPO3Rector\Set\Typo3LevelSetList;
use Ssch\TYPO3Rector\Set\Typo3SetList;
use Rector\DeadCode\Rector\MethodCall\RemoveEmptyMethodCallRector;
use Rector\Php73\Rector\ConstFetch\SensitiveConstantNameRector;

final class RectorSettings
{
    /**
     * @return array<int,string>
     */
    public static function sets(bool $entirety = false): array
    {
        $phpVersion = VersionUtility::getMinimalPhpVersion() ?? PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;
        [$major, $minor] = explode('.', $phpVersion, 3);
        $phpFile = constant(SetList::class . '::PHP_' . $major . $minor);
        if ($entirety) {
            $phpFile = constant(LevelSetList::class . '::UP_TO_PHP_' . $major . $minor);
        }

        assert(is_string($phpFile));

        return array_filter(
            [
                // SetList::ACTION_INJECTION_TO_CONSTRUCTOR_INJECTION, // NO
                SetList::CODE_QUALITY, // YES
                SetList::CODING_STYLE, // YES
                SetList::DEAD_CODE, // YES
                //SetList::GMAGICK_TO_IMAGICK, // NO
                //SetList::MONOLOG_20, // no usage
                //SetList::MYSQL_TO_MYSQLI, // no usage
                //SetList::NAMING, //NO is not good
                $phpFile,
                //SetList::PHP_52, // YES, included in LevelSetList::class . '::UP_TO_PHP_' ...
                //SetList::PHP_53, // YES, included in LevelSetList::class . '::UP_TO_PHP_' ...
                //SetList::PHP_54, // YES, included in LevelSetList::class . '::UP_TO_PHP_' ...
                //SetList::PHP_55, // YES, included in LevelSetList::class . '::UP_TO_PHP_' ...
                //SetList::PHP_56, // YES, included in LevelSetList::class . '::UP_TO_PHP_' ...
                //SetList::PHP_70, // YES, included in LevelSetList::class . '::UP_TO_PHP_' ...
                //SetList::PHP_71, // YES, included in LevelSetList::class . '::UP_TO_PHP_' ...
                //SetList::PHP_72, // YES, included in LevelSetList::class . '::UP_TO_PHP_' ...
                //SetList::PHP_73, // YES, included in LevelSetList::class . '::UP_TO_PHP_' ...
                //SetList::PHP_74, // YES, included in LevelSetList::class . '::UP_TO_PHP_' ...
                //SetList::PHP_80, // YES, included in LevelSetList::class . '::UP_TO_PHP_' ...
                //SetList::PHP_81, // YES, included in LevelSetList::class . '::UP_TO_PHP_' ...
                //SetList::PHP_82, // YES, included in LevelSetList::class . '::UP_TO_PHP_' ...
                SetList::PRIVATIZATION, // some things may be bad
                SetList::TYPE_DECLARATION, // YES
                SetList::EARLY_RETURN,  //YES
                SetList::INSTANCEOF,
            ]
        );
    }

    /**
     * @return array<int, string>
     */
    public static function setsTypo3(bool $entirety = false): array
    {
        $setList = null;
        $minimalTypo3Version = VersionUtility::getMinimalTypo3Version();
        if (!$minimalTypo3Version) {
            return [];
        }

        [$major] = explode('.', $minimalTypo3Version, 2);

        switch ($major) {
            case 7:
                $setList = $entirety ? Typo3LevelSetList::UP_TO_TYPO3_7 : Typo3SetList::TYPO3_76;
                break;
            case 8:
                $setList = $entirety ? Typo3LevelSetList::UP_TO_TYPO3_8 : Typo3SetList::TYPO3_87;
                break;
            case 9:
                $setList = $entirety ? Typo3LevelSetList::UP_TO_TYPO3_9 : Typo3SetList::TYPO3_95;
                break;
            case 10:
                $setList = $entirety ? Typo3LevelSetList::UP_TO_TYPO3_10 : Typo3SetList::TYPO3_104;
                break;
            case 11:
                $setList = $entirety ? Typo3LevelSetList::UP_TO_TYPO3_11 : Typo3SetList::TYPO3_11;
                break;
            case 12:
            case 'dev-main':
                $setList = $entirety ? Typo3LevelSetList::UP_TO_TYPO3_12 : Typo3SetList::TYPO3_12;
                break;
        }

        assert(is_string($setList));
        return [
            $setList,
            __DIR__ . '/../rector-typo3-rule-set.php',
        ];
    }

    /**
     * @return array<string|string[]>
     */
    public static function skip(): array
    {
        return array_filter([
            /**
             * FROM: if($object) {
             * TO:   if($object !== null) {
             */
            NullableCompareToNullRector::class,
            /**
             * FROM: if ($dateTime === null) {
             * TO:   if (! $dateTime instanceof DateTime) {
             */
            FlipTypeControlToUseExclusiveTypeRector::class,
            /**
             * FROM: if ($someClass && $someClass->someMethod()) {
             * TO:   if ($someClass instanceof SomeClass && $someClass->someMethod()) {
             */
            BinaryOpNullableToInstanceofRector::class,
            /**
             * FROM: $i++
             * TO:   ++$i
             */
            PostIncDecToPreIncDecRector::class,
            /**
             * FROM: if(count($array)) {
             * TO:   if($array !== []) {
             */
            CountArrayToEmptyArrayComparisonRector::class,
            /**
             * FROM: switch($x) {
             * TO:   if($X === '...') {
             */
            BinarySwitchToIfElseRector::class,
            /**
             * FROM: ->select('a', 'b')
             * TO:   ->select(['a', 'b'])
             */
            UnSpreadOperatorRector::class,
            /**
             * FROM: protected string $name;
             * TO:   private string  $name;
             *
             * ignore for models so the protected attributes are not made private
             */
            PrivatizeFinalClassPropertyRector::class => [
                '/*/Model/*',
            ],
            /**
             * DOCS: be careful, run this just once, since it can keep swapping order back and forth
             * => we don't do it once!
             */
            ListSwapArrayOrderRector::class,
            /**
             * FROM: 1305630314
             * TO:   1_305_630_314
             */
            AddLiteralSeparatorToNumberRector::class,
            /**
             * Maybe to a later date?
             *
             * FROM: if($x) {
             * TO:   $x ? $abcde + $xyz : $trsthjzuj - $gesrtdnzmf
             */
            SimplifyIfElseToTernaryRector::class,
            /**
             * FROM: if ($timeInMinutes % 60) {
             * TO:   if ($timeInMinutes % 60 !== 0) {
             */
            ExplicitBoolCompareRector::class,
            /**
             * FROM: isset($this->x);
             * TO:   property_exists($this, 'x') && $this->x !== null;
             */
            IssetOnPropertyObjectToPropertyExistsRector::class,
            /**
             * FROM: * @ var ObjectStorage<Moption>
             * TO:   * @ var ObjectStorage
             */
            TypedPropertyFromStrictGetterMethodReturnTypeRector::class,
            /**
             * perfomance issues in many projects
             * @deprecated remove if rector 0.17.0 is not supported anymore:
             */
            class_exists(RemoveEmptyMethodCallRector::class) ? RemoveEmptyMethodCallRector::class : null,
        ]);
    }

    /**
     * @return array<int, string>
     */
    public static function skipTypo3(): array
    {
        if (!InstalledVersions::isInstalled('typo3/cms-core')) {
            return [];
        }

        return [
            /**
             * not used:
             */
            RenameClassMapAliasRector::class,
            /**
             * in combination with ConstantsToEnvironmentApiCallRector not the best rule
             */
            SensitiveConstantNameRector::class,
        ];
    }
}
