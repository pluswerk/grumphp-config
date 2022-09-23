<?php

declare(strict_types=1);

namespace PLUS\GrumPHPConfig;

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
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Privatization\Rector\Class_\ChangeReadOnlyVariableWithDefaultValueToConstantRector;
use Rector\Privatization\Rector\Class_\FinalizeClassesWithoutChildrenRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

class RectorSettings
{
    /**
     * @return array<int,string>
     */
    public static function sets(): array
    {
        $phpfile = constant(LevelSetList::class . '::UP_TO_PHP_' . PHP_MAJOR_VERSION . PHP_MINOR_VERSION);
        assert(is_string($phpfile));

        return [
            // SetList::ACTION_INJECTION_TO_CONSTRUCTOR_INJECTION, // NO
            SetList::CODE_QUALITY, // YES
            SetList::CODING_STYLE, // YES
            SetList::DEAD_CODE, // YES
            //SetList::GMAGICK_TO_IMAGICK, // NO
            //SetList::MONOLOG_20, // no usage
            //SetList::MYSQL_TO_MYSQLI, // no usage
            //SetList::NAMING, //NO is not good
            $phpfile,
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
            SetList::PSR_4, // dose nothing?
            SetList::TYPE_DECLARATION, // YES
            SetList::TYPE_DECLARATION_STRICT, // YES please
            SetList::EARLY_RETURN,  //YES
            //SetList::RECTOR_CONFIG, // NO
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function skip(): array
    {
        return [
            /**
             * FROM: if($object) {
             * TO:   if($object !== null) {
             */
            NullableCompareToNullRector::class,
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
             * FROM: $domain = 'https://efsrgtdhj';
             * TO:   self::DOMAIN
             */
            ChangeReadOnlyVariableWithDefaultValueToConstantRector::class,
            /**
             * FROM: class XYZ {
             * TO:   final class XYZ {
             */
            FinalizeClassesWithoutChildrenRector::class,
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
             * FROM: public string $username = '';
             * TO:   __construct(public string $username = '')
             */
            ClassPropertyAssignToConstructorPromotionRector::class,
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
        ];
    }
}
