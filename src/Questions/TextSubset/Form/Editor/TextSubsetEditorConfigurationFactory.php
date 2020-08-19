<?php
declare(strict_types = 1);

namespace srag\asq\Questions\TextSubset\Form\Editor;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\TextSubset\Editor\Data\TextSubsetEditorConfiguration;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

/**
 * Class TextSubsetEditorConfigurationFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class TextSubsetEditorConfigurationFactory extends AbstractObjectFactory
{
    const VAR_REQUESTED_ANSWERS = 'tse_requested_answers';

    /**
     * @param AbstractValueObject $value
     * @return array
     */
    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $requested_answers = $this->factory->input()->field()->text($this->language->txt('asq_label_requested_answers'));

        if ($value !== null) {
            $requested_answers = $requested_answers->withValue(strval($value->getNumberOfRequestedAnswers()));
        }

        $fields[self::VAR_REQUESTED_ANSWERS] = $requested_answers;

        return $fields;
    }

    /**
     * @param $postdata array
     * @return TextSubsetEditorConfiguration
     */
    public function readObjectFromPost(array $postdata) : AbstractValueObject
    {
        return TextSubsetEditorConfiguration::create($this->readInt($postdata[self::VAR_REQUESTED_ANSWERS]));
    }

    /**
     * @return TextSubsetEditorConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return TextSubsetEditorConfiguration::create();
    }
}
