<?php
declare(strict_types=1);

namespace srag\asq\Questions\Ordering\Editor\Data;

use srag\asq\Questions\Ordering\Editor\OrderingEditor;

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
     * Different name to the usual create, due to php missing method overloading
     *
     * @param string $text
     * @return OrderingTextEditorConfiguration
     */
    public static function createNew(?string $text = null) : OrderingEditorConfiguration
    {
        $object = new OrderingTextEditorConfiguration();
        $object->vertical = false;
        $object->text = $text;
        return $object;
    }

    /**
     * @return ?string
     */
    public function getText() : ?string
    {
        return $this->text;
    }

    public function configurationFor() : string
    {
        return OrderingEditor::class;
    }
}
