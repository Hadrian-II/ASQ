<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Numeric\Form\Editor;

use ilNumberInputGUI;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\Numeric\Editor\Data\NumericEditorConfiguration;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

/**
 * Class NumericEditorConfigurationFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class NumericEditorConfigurationFactory extends AbstractObjectFactory
{
    const VAR_MAX_NR_OF_CHARS = 'ne_max_nr_of_chars';

    /**
     * @param AbstractValueObject $value
     * @return array
     */
    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $max_chars = new ilNumberInputGUI($this->language->txt('asq_label_max_nr_of_chars'), self::VAR_MAX_NR_OF_CHARS);
        $max_chars->setInfo($this->language->txt('asq_description_max_nr_chars'));
        $max_chars->setSize(6);
        $fields[self::VAR_MAX_NR_OF_CHARS] = $max_chars;

        if ($value !== null) {
            $max_chars->setValue($value->getMaxNumOfChars());
        }

        return $fields;
    }

    /**
     * @return NumericEditorConfiguration
     */
    public function readObjectFromPost() : AbstractValueObject
    {
        return NumericEditorConfiguration::create($this->readInt(self::VAR_MAX_NR_OF_CHARS));
    }

    /**
     * @return NumericEditorConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return NumericEditorConfiguration::create();
    }
}
