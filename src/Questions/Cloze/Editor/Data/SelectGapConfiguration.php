<?php
declare(strict_types=1);

namespace srag\asq\Questions\Cloze\Editor\Data;

/**
 * Class SelectGapConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class SelectGapConfiguration extends ClozeGapConfiguration
{
    /**
     * @var ?ClozeGapItem[]
     */
    protected ?array $items;

    public function __construct(?array $items = [])
    {
        $this->items = $items;
    }

    public function getItems() : ?array
    {
        return $this->items;
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
        if (count($this->getItems()) < 2) {
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
