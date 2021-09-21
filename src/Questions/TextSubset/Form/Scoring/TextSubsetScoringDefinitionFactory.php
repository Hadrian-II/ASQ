<?php
declare(strict_types = 1);

namespace srag\asq\Questions\TextSubset\Form\Scoring;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Questions\TextSubset\Scoring\Data\TextSubsetScoringDefinition;
use srag\asq\UserInterface\Web\Fields\AsqTableInput\AsqTableInputFieldDefinition;
use srag\asq\UserInterface\Web\Form\Factory\AbstractAnswerOptionFactory;

/**
 * Class TextSubsetScoringDefinitionFactory
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class TextSubsetScoringDefinitionFactory extends AbstractAnswerOptionFactory
{
    const VAR_TSSD_POINTS = 'tssd_points';
    const VAR_TSSD_TEXT = 'tsdd_text';

    public function getValues(AbstractValueObject $definition) : array
    {
        return [
            self::VAR_TSSD_POINTS => $definition->getPoints(),
            self::VAR_TSSD_TEXT => $definition->getText()
        ];
    }

    public function getTableColumns(?QuestionPlayConfiguration $play) : array
    {
        $fields = [];

        $fields[] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_label_answer_text'),
            AsqTableInputFieldDefinition::TYPE_TEXT,
            self::VAR_TSSD_TEXT,
            [ AsqTableInputFieldDefinition::OPTION_MAX_LENGTH => 32 ]
        );

        $fields[] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_label_points'),
            AsqTableInputFieldDefinition::TYPE_NUMBER,
            self::VAR_TSSD_POINTS
        );

        return $fields;
    }

    /**
     * @return TextSubsetScoringDefinition
     */
    public function readObjectFromValues(array $values) : AbstractValueObject
    {
        return new TextSubsetScoringDefinition(
            $this->readFloat($values[self::VAR_TSSD_POINTS]),
            $values[self::VAR_TSSD_TEXT]
        );
    }

    /**
     * @return TextSubsetScoringDefinition
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return new TextSubsetScoringDefinition();
    }
}
