<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Kprim\Scoring\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class KprimChoiceScoringDefinition
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class KprimChoiceScoringDefinition extends AbstractValueObject
{
    protected ?bool $correct_value;

    public function __construct(?bool $correct_value = null)
    {
        $this->correct_value = $correct_value;
    }

    public function isCorrectValue() : ?bool
    {
        return $this->correct_value;
    }
}
