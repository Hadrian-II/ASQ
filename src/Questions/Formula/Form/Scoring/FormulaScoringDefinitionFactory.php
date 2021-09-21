<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Formula\Form\Scoring;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Questions\Formula\Scoring\Data\FormulaScoringDefinition;
use srag\asq\UserInterface\Web\Fields\AsqTableInput\AsqTableInputFieldDefinition;
use srag\asq\UserInterface\Web\Form\Factory\AbstractAnswerOptionFactory;

/**
 * Class FormulaScoringDefinitionFactory
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class FormulaScoringDefinitionFactory extends AbstractAnswerOptionFactory
{
    const VAR_FORMULA = 'fsd_formula';
    const VAR_UNIT = 'fsd_unit';
    const VAR_POINTS = 'fsd_points';

    public function getValues(AbstractValueObject $definition) : array
    {
        return [
            self::VAR_FORMULA => $definition->getFormula(),
            self::VAR_UNIT => $definition->getUnit(),
            self::VAR_POINTS => $definition->getPoints()
        ];
    }

    public function getTableColumns(?QuestionPlayConfiguration $play) : array
    {
        $fields = [];

        $fields[] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_header_formula'),
            AsqTableInputFieldDefinition::TYPE_TEXT,
            self::VAR_FORMULA,
            [ AsqTableInputFieldDefinition::OPTION_MAX_LENGTH => 128 ]
        );

        $fields[] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_header_unit'),
            AsqTableInputFieldDefinition::TYPE_TEXT,
            self::VAR_UNIT,
            [ AsqTableInputFieldDefinition::OPTION_MAX_LENGTH => 16 ]
        );

        $fields[] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_label_points'),
            AsqTableInputFieldDefinition::TYPE_TEXT,
            self::VAR_POINTS
        );

        return $fields;
    }

    /**
     * @return FormulaScoringDefinition
     */
    public function readObjectFromValues(array $values) : AbstractValueObject
    {
        return new FormulaScoringDefinition(
            $values[self::VAR_FORMULA],
            empty($values[self::VAR_UNIT]) ? null : $values[self::VAR_UNIT],
            $this->readFloat($values[self::VAR_POINTS])
        );
    }

    /**
     * @return FormulaScoringDefinition
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return new FormulaScoringDefinition();
    }
}
