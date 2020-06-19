<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Formula\Form;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\UserInterface\Web\Fields\AsqTableInputFieldDefinition;
use srag\asq\UserInterface\Web\Form\AbstractAnswerOptionFactory;
use srag\asq\Questions\Formula\FormulaScoringDefinition;

/**
 * Class FormulaScoringDefinitionFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class FormulaScoringDefinitionFactory extends AbstractAnswerOptionFactory
{
    const VAR_FORMULA = 'fsd_formula';
    const VAR_UNIT = 'fsd_unit';
    const VAR_POINTS = 'fsd_points';

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\IAnswerOptionFactory::getValues()
     */
    public function getValues(AbstractValueObject $definition) : array
    {
        return [
            self::VAR_FORMULA => $definition->getFormula(),
            self::VAR_UNIT => $definition->getUnit(),
            self::VAR_POINTS => $definition->getPoints()
        ];
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\IAnswerOptionFactory::getTableColumns()
     */
    public function getTableColumns(?QuestionPlayConfiguration $play) : array
    {
        $fields = [];

        $fields[] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_header_formula'),
            AsqTableInputFieldDefinition::TYPE_TEXT,
            self::VAR_FORMULA);

        $fields[] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_header_unit'),
            AsqTableInputFieldDefinition::TYPE_TEXT,
            self::VAR_UNIT);

        $fields[] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_label_points'),
            AsqTableInputFieldDefinition::TYPE_TEXT,
            self::VAR_POINTS);

        return $fields;
    }

    /**
     * @return FormulaScoringDefinition
     */
    public function readObjectFromValues(array $values) : AbstractValueObject
    {
        return FormulaScoringDefinition::create(
            $values[self::VAR_FORMULA],
            empty($values[self::VAR_UNIT]) ? null : $values[self::VAR_UNIT],
            floatval($values[self::VAR_POINTS]));
    }

    /**
     * @return FormulaScoringDefinition
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return FormulaScoringDefinition::create(null, null, null);
    }
}