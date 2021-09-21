<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Choice\Form\Editor\MultipleChoice;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\Choice\Editor\MultipleChoice\Data\MultipleChoiceEditorConfiguration;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

/**
 * Class MultipleChoiceEditorConfigurationFactory
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class MultipleChoiceEditorConfigurationFactory extends AbstractObjectFactory
{
    const VAR_MCE_SHUFFLE = 'shuffle';
    const VAR_MCE_MAX_ANSWERS = 'max_answers';
    const VAR_MCE_THUMB_SIZE = 'thumbsize';
    const VAR_MCE_IS_SINGLELINE = 'singleline';

    const STR_TRUE = "true";
    const STR_FALSE = "false";

    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $shuffle = $this->factory->input()->field()->checkbox($this->language->txt('asq_label_shuffle'));

        $max_answers = $this->factory->input()->field()->numeric($this->language->txt('asq_label_max_answer'));

        $singleline = $this->factory->input()->field()->select(
            $this->language->txt('asq_label_editor'),
            [
                self::STR_TRUE => $this->language->txt('asq_option_single_line'),
                self::STR_FALSE => $this->language->txt('asq_option_multi_line')
            ]
        )->withAdditionalOnLoadCode(function($id) {
            return "il.ASQ.Choice.setEditorSelect($($id));";
        });

        $thumb_size = $this->factory->input()->field()->numeric(
            $this->language->txt('asq_label_thumb_size'),
            $this->language->txt('asq_description_thumb_size')
        );

        if ($value !== null) {
            $shuffle = $shuffle->withValue($value->isShuffleAnswers() ?? false);
            $max_answers = $max_answers->withValue($value->getMaxAnswers());
            $thumb_size = $thumb_size->withValue($value->getThumbnailSize());
            $singleline = $singleline->withValue($value->isSingleLine() ? self::STR_TRUE : self::STR_FALSE);
        }

        $fields[self::VAR_MCE_SHUFFLE] = $shuffle;
        $fields[self::VAR_MCE_MAX_ANSWERS] = $max_answers;
        $fields[self::VAR_MCE_IS_SINGLELINE] = $singleline;
        $fields[self::VAR_MCE_THUMB_SIZE] = $thumb_size;

        return $fields;
    }

    /**
     * @param array $postvalue
     * @return MultipleChoiceEditorConfiguration
     */
    public function readObjectFromPost(array $postvalue) : AbstractValueObject
    {
        return new MultipleChoiceEditorConfiguration(
            $postvalue[self::VAR_MCE_SHUFFLE],
            $postvalue[self::VAR_MCE_MAX_ANSWERS],
            $postvalue[self::VAR_MCE_THUMB_SIZE],
            $postvalue[self::VAR_MCE_IS_SINGLELINE] === self::STR_TRUE
        );
    }

    /**
     * @return MultipleChoiceEditorConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return new MultipleChoiceEditorConfiguration();
    }
}
