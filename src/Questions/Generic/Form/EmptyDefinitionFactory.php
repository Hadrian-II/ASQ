<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Generic\Form;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Questions\Generic\Data\EmptyDefinition;
use srag\asq\UserInterface\Web\Form\Factory\AbstractAnswerOptionFactory;

/**
 * Class EmptyDefinitionFactory
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
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
