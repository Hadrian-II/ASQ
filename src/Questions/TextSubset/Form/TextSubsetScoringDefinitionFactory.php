<?php
declare(strict_types = 1);

namespace srag\asq\Questions\TextSubset\Form;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\Questions\TextSubset\TextSubsetScoringDefinition;
use srag\asq\UserInterface\Web\Fields\AsqTableInputFieldDefinition;
use srag\asq\UserInterface\Web\Form\AbstractAnswerOptionFactory;

/**
 * Class TextSubsetScoringDefinitionFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class TextSubsetScoringDefinitionFactory extends AbstractAnswerOptionFactory
{
    const VAR_TSSD_POINTS = 'tssd_points';
    const VAR_TSSD_TEXT = 'tsdd_text';

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\IAnswerOptionFactory::getValues()
     */
    public function getValues(AbstractValueObject $definition): array
    {
        return [
            self::VAR_TSSD_POINTS => $definition->getPoints(),
            self::VAR_TSSD_TEXT => $definition->getText()
        ];
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\IAnswerOptionFactory::getTableColumns()
     */
    public function getTableColumns(?QuestionPlayConfiguration $play): array
    {
        $fields = [];

        $fields[] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_label_answer_text'),
            AsqTableInputFieldDefinition::TYPE_TEXT,
            self::VAR_TSSD_TEXT);

        $fields[] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_label_points'),
            AsqTableInputFieldDefinition::TYPE_NUMBER,
            self::VAR_TSSD_POINTS);

        return $fields;
    }

    /**
     * @return TextSubsetScoringDefinition
     */
    public function readObjectFromValues(array $values): AbstractValueObject
    {
        return TextSubsetScoringDefinition::create(
            floatval($values[self::VAR_TSSD_POINTS]),
            $values[self::VAR_TSSD_TEXT]);
    }

    /**
     * @return TextSubsetScoringDefinition
     */
    public function getDefaultValue(): AbstractValueObject
    {
        return TextSubsetScoringDefinition::create();
    }
}