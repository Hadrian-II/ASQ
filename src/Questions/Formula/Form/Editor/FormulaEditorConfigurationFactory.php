<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Formula\Form\Editor;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\Formula\Editor\Data\FormulaEditorConfiguration;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

/**
 * Class FormulaEditorConfigurationFactory
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class FormulaEditorConfigurationFactory extends AbstractObjectFactory
{
    public function getFormfields(?AbstractValueObject $value) : array
    {
        return [];
    }

    /**
     * @return FormulaEditorConfiguration
     */
    public function readObjectFromPost(array $postdata) : AbstractValueObject
    {
        return new FormulaEditorConfiguration();
    }

    /**
     * @return FormulaEditorConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return new FormulaEditorConfiguration();
    }
}
