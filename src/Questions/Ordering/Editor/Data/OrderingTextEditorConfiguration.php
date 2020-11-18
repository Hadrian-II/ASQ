<?php
declare(strict_types=1);

namespace srag\asq\Questions\Ordering\Editor\Data;

/**
 * Class OrderingTextEditorConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class OrderingTextEditorConfiguration extends OrderingEditorConfiguration
{
    /**
     * @var ?string
     */
    protected $text;

    /**
     * @param string $text
     */
    public function __construct(?string $text = null)
    {
        $this->vertical = false;
        $this->text = $text;
    }

    /**
     * @return ?string
     */
    public function getText() : ?string
    {
        return $this->text;
    }
}
