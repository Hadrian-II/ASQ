<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Numeric\Form\Editor;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\Numeric\Editor\Data\NumericEditorConfiguration;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

/**
 * Class NumericEditorConfigurationFactory
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class NumericEditorConfigurationFactory extends AbstractObjectFactory
{
    const VAR_MAX_NR_OF_CHARS = 'ne_max_nr_of_chars';

    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $max_chars = $this->factory->input()->field()->numeric(
            $this->language->txt('asq_label_max_nr_of_chars'),
            $this->language->txt('asq_description_max_nr_chars')
        );

        if ($value !== null) {
            $max_chars = $max_chars->withValue($value->getMaxNumOfChars());
        }

        $fields[self::VAR_MAX_NR_OF_CHARS] = $max_chars;

        return $fields;
    }

    /**
     * @param $postdata array
     * @return NumericEditorConfiguration
     */
    public function readObjectFromPost(array $postdata) : AbstractValueObject
    {
        return new NumericEditorConfiguration($postdata[self::VAR_MAX_NR_OF_CHARS]);
    }

    /**
     * @return NumericEditorConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return new NumericEditorConfiguration();
    }
}
