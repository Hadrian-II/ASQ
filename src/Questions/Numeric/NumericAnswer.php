<?php
declare(strict_types = 1);
namespace srag\asq\Questions\Numeric;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class NumericAnswer
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class NumericAnswer extends AbstractValueObject
{
    protected ?float $value;

    public function __construct(?float $value = null)
    {
        $this->value = $value;
    }

    public function getValue() : ?float
    {
        return $this->value;
    }
}
