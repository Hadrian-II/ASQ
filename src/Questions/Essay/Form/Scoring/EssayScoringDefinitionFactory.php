<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Essay\Form\Scoring;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Questions\ErrorText\Scoring\Data\ErrorTextScoringDefinition;
use srag\asq\UserInterface\Web\Fields\AsqTableInput\AsqTableInputFieldDefinition;
use srag\asq\UserInterface\Web\Form\Factory\AbstractAnswerOptionFactory;
use srag\asq\Questions\Essay\Scoring\Data\EssayScoringDefinition;

/**
 * Class EssayScoringDefinitionFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class EssayScoringDefinitionFactory extends AbstractAnswerOptionFactory
{
    const VAR_POINTS = 'es_def_points';
    const VAR_TEXT = 'es_def_text';

    /**
     * @param $definition EssayScoringDefinition
     */
    public function getValues(AbstractValueObject $definition) : array
    {
        return [
            self::VAR_POINTS => $definition->getPoints(),
            self::VAR_TEXT => $definition->getText()
        ];
    }

    /**
     * @param QuestionPlayConfiguration $play
     * @return array
     */
    public function getTableColumns(?QuestionPlayConfiguration $play) : array
    {
        $fields = [];

        $fields[] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_label_points'),
            AsqTableInputFieldDefinition::TYPE_NUMBER,
            self::VAR_POINTS
        );

        $fields[] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_label_text'),
            AsqTableInputFieldDefinition::TYPE_TEXT,
            self::VAR_TEXT,
            [ AsqTableInputFieldDefinition::OPTION_MAX_LENGTH => 128 ]
        );

        return $fields;
    }

    /**
     * @return ErrorTextScoringDefinition
     */
    public function readObjectFromValues(array $values) : AbstractValueObject
    {
        return new EssayScoringDefinition(
            $values[self::VAR_TEXT],
            $this->readFloat($values[self::VAR_POINTS])
        );
    }

    /**
     * @return ErrorTextScoringDefinition
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return new EssayScoringDefinition();
    }
}
