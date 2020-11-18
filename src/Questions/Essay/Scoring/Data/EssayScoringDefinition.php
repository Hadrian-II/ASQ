<?php
declare(strict_types=1);

namespace srag\asq\Questions\Essay\Scoring\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class EssayScoringDefinition
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class EssayScoringDefinition extends AbstractValueObject
{
    /**
     * @var ?float
     */
    protected $points;

    /**
     * @var ?string;
     */
    protected $text;

    /**
     * @param string $text
     * @param float $points
     */
    public function __construct(?string $text = null, ?float $points = null)
    {
        $this->points = $points;
        $this->text = $text;
    }

    /**
     * @return float|NULL
     */
    public function getPoints() : ?float
    {
        return $this->points;
    }

    /**
     * @return string|NULL
     */
    public function getText() : ?string
    {
        return $this->text;
    }
}
