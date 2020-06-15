<?php
declare(strict_types = 1);

namespace srag\asq\Domain\Model\Answer\Option;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\UserInterface\Web\Form\AbstractAnswerOptionFactory;
use srag\asq\Domain\Model\QuestionPlayConfiguration;

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
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\IAnswerOptionFactory::getValues()
     */
    public function getValues(AbstractValueObject $definition): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\IAnswerOptionFactory::getTableColumns()
     */
    public function getTableColumns(?QuestionPlayConfiguration $play): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\IAnswerOptionFactory::readObjectFromValues()
     */
    public function readObjectFromValues(array $values): AbstractValueObject
    {
        return EmptyDefinition::create();
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\IAnswerOptionFactory::getDefaultValue()
     */
    public function getDefaultValue(): AbstractValueObject
    {
        return EmptyDefinition::create();
    }
}