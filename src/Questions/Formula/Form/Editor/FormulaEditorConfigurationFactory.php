<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Formula\Form\Editor;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\Formula\Editor\Data\FormulaEditorConfiguration;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

/**
 * Class FormulaEditorConfigurationFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class FormulaEditorConfigurationFactory extends AbstractObjectFactory
{
    /**
     * @param AbstractValueObject $value
     * @return array
     */
    public function getFormfields(?AbstractValueObject $value) : array
    {
        return [];
    }

    /**
     * @param array $postdata
     * @return FormulaEditorConfiguration
     */
    public function readObjectFromPost(array $postdata) : AbstractValueObject
    {
        return FormulaEditorConfiguration::create();
    }

    /**
     * @return FormulaEditorConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return FormulaEditorConfiguration::create();
    }
}
