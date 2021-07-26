<?php
declare(strict_types=1);

namespace srag\asq\Questions\Numeric\Editor\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class NumericEditorConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class NumericEditorConfiguration extends AbstractValueObject
{
    protected ?int $max_num_of_chars;

    public function __construct(?int $max_num_of_chars = null)
    {
        $this->max_num_of_chars = $max_num_of_chars;
    }

    public function getMaxNumOfChars() : ?int
    {
        return $this->max_num_of_chars;
    }
}
