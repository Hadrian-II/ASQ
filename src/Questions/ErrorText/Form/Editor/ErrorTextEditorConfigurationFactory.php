<?php
declare(strict_types = 1);

namespace srag\asq\Questions\ErrorText\Form;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\UserInterface\Web\Form\AbstractObjectFactory;
use srag\asq\Questions\ErrorText\ErrorTextEditorConfiguration;
use ilTextAreaInputGUI;
use ilNumberInputGUI;

/**
 * Class ErrorTextEditorConfigurationFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ErrorTextEditorConfigurationFactory extends AbstractObjectFactory
{
    const DEFAULT_TEXTSIZE_PERCENT = 100;

    const VAR_ERROR_TEXT = 'ete_error_text';
    const VAR_TEXT_SIZE = 'ete_text_size';

    /**
     * @param $value ErrorTextEditorConfiguration
     * @return array
     */
    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $error_text = new ilTextAreaInputGUI($this->language->txt('asq_label_error_text'), self::VAR_ERROR_TEXT);
        $error_text->setInfo('<input type="button" id="process_error_text" value="' .
            $this->language->txt('asq_label_process_error_text') .
            '" class="btn btn-default btn-sm" /><br />' .
            $this->language->txt('asq_description_error_text'));
        $error_text->setRequired(true);
        $fields[self::VAR_ERROR_TEXT] = $error_text;


        $text_size = new ilNumberInputGUI($this->language->txt('asq_label_text_size'), self::VAR_TEXT_SIZE);
        $text_size->setSize(6);
        $text_size->setSuffix('%');
        $fields[self::VAR_TEXT_SIZE] = $text_size;

        if ($value !== null) {
            $error_text->setValue($value->getErrorText());
            $text_size->setValue($value->getTextSize());
        } else {
            $text_size->setValue(self::DEFAULT_TEXTSIZE_PERCENT);
        }

        return $fields;
    }

    /**
     * @return ErrorTextEditorConfiguration
     */
    public function readObjectFromPost() : AbstractValueObject
    {
        return ErrorTextEditorConfiguration::create(
            $this->readString(self::VAR_ERROR_TEXT),
            $this->readInt(self::VAR_TEXT_SIZE)
        );
    }

    /**
     * @return ErrorTextEditorConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return ErrorTextEditorConfiguration::create('', self::DEFAULT_TEXTSIZE_PERCENT);
    }
}
