<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Generic\Form;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Questions\Generic\Data\EmptyDefinition;
use srag\asq\UserInterface\Web\Form\Factory\AbstractAnswerOptionFactory;

/**
 * Class EmptyDefinitionFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class EmptyDefinitionFactory extends AbstractAnswerOptionFactory
{
    /**
     * @param AbstractValueObject $definition
     * @return array
     */
    public function getValues(AbstractValueObject $definition) : array
    {
        return [];
    }

    /**
     * @param QuestionPlayConfiguration $play
     * @return array
     */
    public function getTableColumns(?QuestionPlayConfiguration $play) : array
    {
        return [];
    }

    /**
     * @param array $values
     * @return AbstractValueObject
     */
    public function readObjectFromValues(array $values) : AbstractValueObject
    {
        return EmptyDefinition::create();
    }

    /**
     * @return AbstractValueObject
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return EmptyDefinition::create();
    }
}
