<?php
declare(strict_types = 1);

namespace srag\asq\Questions\ErrorText\Form\Scoring;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Questions\ErrorText\Scoring\Data\ErrorTextScoringDefinition;
use srag\asq\UserInterface\Web\Fields\AsqTableInput\AsqTableInputFieldDefinition;
use srag\asq\UserInterface\Web\Form\Factory\AbstractAnswerOptionFactory;

/**
 * Class ErrorTextScoringDefinitionFactory
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class ErrorTextScoringDefinitionFactory extends AbstractAnswerOptionFactory
{
    const VAR_WRONG_TEXT = 'etsd_wrong_text';
    const VAR_WORD_INDEX = 'etsd_word_index';
    const VAR_WORD_LENGTH = 'etsd_word_length';
    const VAR_CORRECT_TEXT = 'etsd_correct_text' ;
    const VAR_POINTS = 'etsd_points';

    /**
     * @param $definition ErrorTextScoringDefinition
     */
    public function getValues(AbstractValueObject $definition) : array
    {
        return [
            self::VAR_WORD_INDEX => $definition->getWrongWordIndex(),
            self::VAR_WORD_LENGTH => $definition->getWrongWordLength(),
            self::VAR_CORRECT_TEXT => $definition->getCorrectText(),
            self::VAR_POINTS => $definition->getPoints()
        ];
    }

    public function getTableColumns(?QuestionPlayConfiguration $play) : array
    {
        $fields = [];
        $fields[] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_label_wrong_text'),
            AsqTableInputFieldDefinition::TYPE_LABEL,
            self::VAR_WRONG_TEXT
        );

        $fields[] = new AsqTableInputFieldDefinition(
            '',
            AsqTableInputFieldDefinition::TYPE_HIDDEN,
            self::VAR_WORD_INDEX
        );

        $fields[] = new AsqTableInputFieldDefinition(
            '',
            AsqTableInputFieldDefinition::TYPE_HIDDEN,
            self::VAR_WORD_LENGTH
        );

        $fields[] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_label_correct_text'),
            AsqTableInputFieldDefinition::TYPE_TEXT,
            self::VAR_CORRECT_TEXT,
            [ AsqTableInputFieldDefinition::OPTION_MAX_LENGTH => 64 ]
        );

        $fields[] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_label_points'),
            AsqTableInputFieldDefinition::TYPE_NUMBER,
            self::VAR_POINTS
        );

        return $fields;
    }

    /**
     * @return ErrorTextScoringDefinition
     */
    public function readObjectFromValues(array $values) : AbstractValueObject
    {
        return new ErrorTextScoringDefinition(
            $this->readInt($values[self::VAR_WORD_INDEX]),
            $this->readInt($values[self::VAR_WORD_LENGTH]),
            $values[self::VAR_CORRECT_TEXT],
            $this->readFloat($values[self::VAR_POINTS])
        );
    }

    /**
     * @return ErrorTextScoringDefinition
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return new ErrorTextScoringDefinition();
    }
}
