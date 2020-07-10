<?php
declare(strict_types=1);

namespace srag\asq\Questions\Numeric\Editor\Data;

use srag\asq\Domain\Model\Configuration\AbstractConfiguration;

/**
 * Class NumericEditorConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class NumericEditorConfiguration extends AbstractConfiguration
{
    /**
     * @var ?int
     */
    protected $max_num_of_chars;

    /**
     * @param int $max_num_of_chars
     *
     * @return NumericEditorConfiguration
     */
    public static function create(?int $max_num_of_chars = null) : NumericEditorConfiguration
    {
        $object = new NumericEditorConfiguration();
        $object->max_num_of_chars = $max_num_of_chars;
        return $object;
    }

    /**
     * @return int|NULL
     */
    public function getMaxNumOfChars() : ?int
    {
        return $this->max_num_of_chars;
    }
}
