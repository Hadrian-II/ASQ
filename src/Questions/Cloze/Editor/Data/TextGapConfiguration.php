<?php
declare(strict_types=1);

namespace srag\asq\Questions\Cloze\Editor\Data;

use srag\asq\Domain\Model\Scoring\TextScoring;

/**
 * Class TextGapConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class TextGapConfiguration extends ClozeGapConfiguration
{
    /**
     * @var ?ClozeGapItem[]
     */
    protected ?array $items;

    protected ?int $field_length;

    protected ?int $matching_method;

    public function __construct(
        ?array $items = [],
        ?int $field_length = self::DEFAULT_FIELD_LENGTH,
        ?int $matching_method = TextScoring::TM_CASE_SENSITIVE
    ) {
        $this->items = $items;
        $this->field_length = $field_length;
        $this->matching_method = $matching_method;
    }

    public function getItems() : ?array
    {
        return $this->items;
    }

    public function getFieldLength() : int
    {
        return $this->field_length ?? self::DEFAULT_FIELD_LENGTH;
    }

    public function getMatchingMethod() : ?int
    {
        return $this->matching_method;
    }

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
