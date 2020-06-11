<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Kprim\Form;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\Kprim\KprimChoiceEditorConfiguration;
use srag\asq\Questions\Kprim\KprimChoiceScoringDefinition;
use srag\asq\UserInterface\Web\Form\AbstractAnswerOptionFactory;
use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\UserInterface\Web\Fields\AsqTableInputFieldDefinition;
use srag\asq\Questions\Kprim\KprimChoiceEditor;

/**
 * Class KprimScoringConfigurationFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class KprimChoiceScoringDefinitionFactory extends AbstractAnswerOptionFactory
{
    const VAR_KPSD_CORRECT = 'kpsd_correct';

    /**
     * @param $definition KprimChoiceScoringDefinition
     */
    public function getValues(AbstractValueObject $definition): array
    {
        return [ self::VAR_KPSD_CORRECT => $definition->isCorrectValue() ? KprimChoiceEditor::STR_TRUE : KprimChoiceEditor::STR_FALSE ];
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\IAnswerOptionFactory::getTableColumns()
     */
    public function getTableColumns(?QuestionPlayConfiguration $play): array
    {
        /** @var $conf KprimChoiceEditorConfiguration */
        if (is_null($play) || is_null($play->getEditorConfiguration()))
        {
            $label_true = $this->language->txt('asq_label_right');
            $label_false = $this->language->txt('asq_label_wrong');
        }
        else
        {
            $conf = $play->getEditorConfiguration();
            $label_true = $conf->getLabelTrue();
            $label_false = $conf->getLabelFalse();
        }

        $fields = [];
        $fields[] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_label_options'),
            AsqTableInputFieldDefinition::TYPE_RADIO,
            self::VAR_KPSD_CORRECT,
            [
                $label_true => KprimChoiceEditor::STR_TRUE,
                $label_false => KprimChoiceEditor::STR_FALSE
            ]
        );

        return $fields;
    }

    /**
     * @return KprimChoiceScoringDefinition
     */
    public function readObjectFromValues(array $values): AbstractValueObject
    {
        return KprimChoiceScoringDefinition::create($values[self::VAR_KPSD_CORRECT] === KprimChoiceEditor::STR_TRUE);
    }

    /**
     * @return KprimChoiceScoringDefinition
     */
    public function getDefaultValue(): AbstractValueObject
    {
        return KprimChoiceScoringDefinition::create(false);
    }
}