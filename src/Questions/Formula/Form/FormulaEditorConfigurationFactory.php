<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Formula\Form;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\UserInterface\Web\Form\AbstractObjectFactory;
use srag\asq\Questions\Formula\FormulaEditorConfiguration;

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
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\IObjectFactory::getFormfields()
     */
    public function getFormfields(?AbstractValueObject $value) : array
    {
        return [];
    }

    /**
     * @return FormulaEditorConfiguration
     */
    public function readObjectFromPost() : AbstractValueObject
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
