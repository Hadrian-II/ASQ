<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Form\Factory;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\UserInterface\Web\Fields\AsqTableInput\AsqTableInputFieldDefinition;

/**
 * Interface AnswerOption Factory
 *
 * Defines the methods a FormFactory has to implement
 * Methods contain generation of form fields and creating
 * Value objects from POST
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
interface IAnswerOptionFactory
{
    /**
     * Gets field definition to display in QuestionFormGUI
     *
     * @param $play ?QuestionPlayConfiguration
     * @return AsqTableInputFieldDefinition[]
     */
    public function getTableColumns(?QuestionPlayConfiguration $play) : array;

    /**
     * Reads configuration object from values entered in UI Table
     *
     * @param array $values
     * @return AbstractValueObject
     */
    public function readObjectFromValues(array $values) : AbstractValueObject;

    /**
     * Creates new object containing the default values
     *
     * @return AbstractValueObject
     */
    public function getDefaultValue() : AbstractValueObject;

    /**
     * Gets the values of an AnswerOption as an standard Dictionary
     *
     * @param $definition AbstractValueObject
     * @return array
     */
    public function getValues(AbstractValueObject $definition) : array;
}
