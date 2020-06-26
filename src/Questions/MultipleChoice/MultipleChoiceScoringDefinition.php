<?php
declare(strict_types = 1);
namespace srag\asq\Questions\MultipleChoice;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class MultipleChoiceScoringDefinition
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 * @package srag/asq
 * @author Adrian Lüthi <al@studer-raimann.ch>
 */
class MultipleChoiceScoringDefinition extends AbstractValueObject
{
    /**
     * @var ?float
     */
    protected $points_selected;

    /**
     * @var ?float
     */
    protected $points_unselected;

    /**
     * @param float $points_selected
     * @param float $points_unselected
     * @return MultipleChoiceScoringDefinition
     */
    public static function create(?float $points_selected, ?float $points_unselected) : MultipleChoiceScoringDefinition
    {
        $object = new MultipleChoiceScoringDefinition();
        $object->points_selected = $points_selected;
        $object->points_unselected = $points_unselected;
        return $object;
    }

    /**
     * @return int
     */
    public function getPointsSelected() : ?float
    {
        return $this->points_selected;
    }

    /**
     * @return int
     */
    public function getPointsUnselected() : ?float
    {
        return $this->points_unselected;
    }

    /**
     * @var string
     */
    private static $error_message;

    /**
     * @param string $index
     * @return bool
     */
    public static function checkInput(int $count) : bool
    {
        //TODO fix input checking
        global $DIC;

        $points_found = false;

        for ($i = 1; $i <= $count; $i ++) {
            $str_i = strval($i);
            // unselected key does not exist in singlechoicequestion legacyform
            if (! is_numeric($_POST[self::getPostKey($str_i, self::VAR_MCSD_SELECTED)]) || (array_key_exists(self::getPostKey($str_i, self::VAR_MCSD_UNSELECTED), $_POST) && ! is_numeric($_POST[self::getPostKey($str_i, self::VAR_MCSD_UNSELECTED)]))) {
                self::$error_message = $DIC->language()->txt('asq_error_numeric');
                return false;
            }

            if (intval($_POST[self::getPostKey($str_i, self::VAR_MCSD_SELECTED)]) > 0 || (array_key_exists(self::getPostKey($str_i, self::VAR_MCSD_UNSELECTED), $_POST) && intval($_POST[self::getPostKey($str_i, self::VAR_MCSD_UNSELECTED)]) > 0)) {
                $points_found = true;
            }
        }

        if (! $points_found) {
            self::$error_message = $DIC->language()->txt('asq_error_points');

            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public static function getErrorMessage() : string
    {
        return self::$error_message;
    }
}