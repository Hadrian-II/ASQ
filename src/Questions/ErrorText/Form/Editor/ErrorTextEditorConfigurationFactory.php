<?php
declare(strict_types = 1);

namespace srag\asq\Questions\ErrorText\Form\Editor;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\ErrorText\Editor\Data\ErrorTextEditorConfiguration;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

/**
 * Class ErrorTextEditorConfigurationFactory
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class ErrorTextEditorConfigurationFactory extends AbstractObjectFactory
{
    const DEFAULT_TEXTSIZE_PERCENT = 100;

    const VAR_ERROR_TEXT = 'ete_error_text';
    const VAR_TEXT_SIZE = 'ete_text_size';

    /**
     * @param $value ?ErrorTextEditorConfiguration
     * @return array
     */
    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $error_text = $this->factory->input()->field()->textarea(
            $this->language->txt('asq_label_error_text'),
            $this->createErrorTextInfo()
        );

        $text_size = $this->factory->input()->field()->numeric($this->language->txt('asq_label_text_size'));

        if ($value !== null) {
            $error_text = $error_text->withValue($value->getErrorText());
            $text_size = $text_size->withValue($value->getTextSize());
        } else {
            $text_size = $text_size->withValue(self::DEFAULT_TEXTSIZE_PERCENT);
        }

        $fields[self::VAR_ERROR_TEXT] = $error_text;
        $fields[self::VAR_TEXT_SIZE] = $text_size;

        return $fields;
    }

    private function createErrorTextInfo() : string
    {
        return sprintf(
            '<input type="button" id="process_error_text" value="%s" class="btn btn-default btn-sm" /><br />%s',
            $this->language->txt('asq_label_process_error_text'),
            $this->language->txt('asq_description_error_text')
        );
    }

    /**
     * @return ErrorTextEditorConfiguration
     */
    public function readObjectFromPost(array $postvalues) : AbstractValueObject
    {
        return new ErrorTextEditorConfiguration(
            $this->readString($postvalues[self::VAR_ERROR_TEXT]),
            $postvalues[self::VAR_TEXT_SIZE]
        );
    }

    /**
     * @return ErrorTextEditorConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return new ErrorTextEditorConfiguration('', self::DEFAULT_TEXTSIZE_PERCENT);
    }
}
