<?php
declare(strict_types=1);

namespace srag\asq\Questions\Essay\Scoring\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class EssayScoringDefinition
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class EssayScoringDefinition extends AbstractValueObject
{
    protected ?float $points;

    protected ?string $text;

    public function __construct(?string $text = null, ?float $points = null)
    {
        $this->points = $points;
        $this->text = $text;
    }

    public function getPoints() : ?float
    {
        return $this->points;
    }

    public function getText() : ?string
    {
        return $this->text;
    }
}
