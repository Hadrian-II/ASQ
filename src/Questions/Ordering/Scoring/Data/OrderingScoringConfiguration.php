<?php
declare(strict_types=1);

namespace srag\asq\Questions\Ordering\Scoring\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class OrderingScoringConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class OrderingScoringConfiguration extends AbstractValueObject
{
    /**
     * @var ?float
     */
    protected $points;

    /**
     * @param float $points
     * @return OrderingScoringConfiguration
     */
    public static function create(?float $points = null) : OrderingScoringConfiguration
    {
        $object = new OrderingScoringConfiguration();
        $object->points = $points;
        return $object;
    }

    /**
     * @return ?float
     */
    public function getPoints() : ?float
    {
        return $this->points;
    }
}
