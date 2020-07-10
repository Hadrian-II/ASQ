<?php
declare(strict_types=1);

namespace srag\asq\Questions\Cloze\Editor\Data;

use srag\asq\Domain\Model\Scoring\TextScoring;

/**
 * Class TextGapConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class TextGapConfiguration extends ClozeGapConfiguration
{
    /**
     * @var ?ClozeGapItem[]
     */
    protected $items;

    /**
     * @var ?int
     */
    protected $field_length;

    /**
     * @var ?int
     */
    protected $matching_method;

    /**
     * @param array $items
     * @param int $field_length
     * @param int $matching_method
     * @return TextGapConfiguration
     */
    public static function Create(
        ?array $items = [],
        ?int $field_length = self::DEFAULT_FIELD_LENGTH,
        ?int $matching_method = TextScoring::TM_CASE_SENSITIVE
    ) : TextGapConfiguration {
        $object = new TextGapConfiguration();
        $object->items = $items;
        $object->field_length = $field_length;
        $object->matching_method = $matching_method;
        return $object;
    }

    /**
     * @return ?array
     */
    public function getItems() : ?array
    {
        return $this->items;
    }

    /**
     * @return ?int
     */
    public function getFieldLength() : int
    {
        return $this->field_length ?? self::DEFAULT_FIELD_LENGTH;
    }

    /**
     * @return ?int
     */
    public function getMatchingMethod() : ?int
    {
        return $this->matching_method;
    }

    /**
     * @return array
     */
    public function getItemsArray() : array
    {
        $var_array = [];

        if (!is_null($this->items)) {
            foreach ($this->items as $variable) {
                $var_array[] = $variable->getAsArray();
            }
        }

        return $var_array;
    }

    /**
     * @return float
     */
    public function getMaxPoints() : float
    {
        $gap_max = 0;

        /** @var $gap ClozeGapItem */
        foreach ($this->items as $gap_item) {
            if ($gap_item->getPoints() > $gap_max) {
                $gap_max = $gap_item->getPoints();
            }
        }

        return $gap_max;
    }

    /**
     * @return bool
     */
    public function isComplete() : bool
    {
        if (count($this->getItems()) < 1) {
            return false;
        }

        foreach ($this->getItems() as $gap_item) {
            if (!$gap_item->isComplete()) {
                return false;
            }
        }

        return true;
    }
}
