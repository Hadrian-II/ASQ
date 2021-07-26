<?php
declare(strict_types=1);

namespace srag\asq\Questions\Essay\Editor\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class EssayEditorConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class EssayEditorConfiguration extends AbstractValueObject
{
    protected ?int $max_length;

    public function __construct(?int $max_length = null)
    {
        $this->max_length = $max_length;
    }

    public function getMaxLength() : ?int
    {
        return $this->max_length;
    }
}
