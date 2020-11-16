<?php
declare(strict_types=1);

namespace srag\asq\Questions\Numeric\Editor\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class NumericEditorConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class NumericEditorConfiguration extends AbstractValueObject
{
    /**
     * @var ?int
     */
    protected $max_num_of_chars;

    /**
     * @param int $max_num_of_chars
     * @return \srag\asq\Questions\Numeric\Editor\Data\NumericEditorConfiguration
     */
    public function __construct(?int $max_num_of_chars = null)
    {
        $this->max_num_of_chars = $max_num_of_chars;
    }

    /**
     * @return int|NULL
     */
    public function getMaxNumOfChars() : ?int
    {
        return $this->max_num_of_chars;
    }
}
