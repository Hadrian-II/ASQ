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
    public function getValues(AbstractValueObject $definition) : array
    {
        return [];
    }

    public function getTableColumns(?QuestionPlayConfiguration $play) : array
    {
        return [];
    }

    /**
     * @return EmptyDefinition
     */
    public function readObjectFromValues(array $values) : AbstractValueObject
    {
        return new EmptyDefinition();
    }

    /**
     * @return EmptyDefinition
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return new EmptyDefinition();
    }
}
