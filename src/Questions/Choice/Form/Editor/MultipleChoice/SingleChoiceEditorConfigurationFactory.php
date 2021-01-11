<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Choice\Form\Editor\MultipleChoice;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\Choice\Editor\MultipleChoice\Data\MultipleChoiceEditorConfiguration;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

/**
 * Class SingleChoiceEditorConfigurationFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class SingleChoiceEditorConfigurationFactory extends AbstractObjectFactory
{
    const VAR_MCE_SHUFFLE = 'shuffle';
    const VAR_MCE_MAX_ANSWERS = 'max_answers';
    const VAR_MCE_THUMB_SIZE = 'thumbsize';
    const VAR_MCE_IS_SINGLELINE = 'singleline';

    const SINGLE_CHOICE = 1;
    const STR_TRUE = "true";
    const STR_FALSE = "false";

    /**
     * @param AbstractValueObject $value
     * @return array
     */
    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $shuffle = $this->factory->input()->field()->checkbox($this->language->txt('asq_label_shuffle'));

        $singleline = $this->factory->input()->field()->select(
            $this->language->txt('asq_label_editor'),
            [
                self::STR_TRUE => $this->language->txt('asq_option_single_line'),
                self::STR_FALSE => $this->language->txt('asq_option_multi_line')
            ]
        );

        $thumb_size = $this->factory->input()->field()->text(
            $this->language->txt('asq_label_thumb_size'),
            $this->language->txt('asq_description_thumb_size')
        );

        if ($value !== null) {
            $shuffle = $shuffle->withValue($value->isShuffleAnswers() ?? false);
            $thumb_size = $thumb_size->withValue(strval($value->getThumbnailSize()));
            $singleline = $singleline->withValue($value->isSingleLine() ? self::STR_TRUE : self::STR_FALSE);
        }

        $fields[self::VAR_MCE_SHUFFLE] = $shuffle;
        $fields[self::VAR_MCE_IS_SINGLELINE] = $singleline;
        $fields[self::VAR_MCE_THUMB_SIZE] = $thumb_size;

        return $fields;
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\Factory\IObjectFactory::readObjectFromPost()
     */
    public function readObjectFromPost(array $postvalue) : AbstractValueObject
    {
        return new MultipleChoiceEditorConfiguration(
            $postvalue[self::VAR_MCE_SHUFFLE],
            self::SINGLE_CHOICE,
            $this->readInt($postvalue[self::VAR_MCE_THUMB_SIZE]),
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
