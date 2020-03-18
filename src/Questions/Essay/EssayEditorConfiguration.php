<?php
declare(strict_types=1);

namespace srag\asq\Questions\Essay;

use srag\asq\Domain\Model\AbstractConfiguration;

/**
 * Class EssayEditorConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class EssayEditorConfiguration extends AbstractConfiguration
{
    /**
     * @var ?int
     */
    protected $max_length;
    
    public static function create(?int $max_length = null) {
        $object = new EssayEditorConfiguration();
        $object->max_length = $max_length;
        return $object;
    }
    
    /**
     * @return int
     */
    public function getMaxLength() : ?int
    {
        return $this->max_length;
    }
}