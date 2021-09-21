<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Matching\Form\Scoring;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\Matching\Scoring\Data\MatchingScoringConfiguration;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

/**
 * Class MatchingScoringConfigurationFactory
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class MatchingScoringConfigurationFactory extends AbstractObjectFactory
{
    const VAR_WRONG_DEDUCTION = 'ms_wrong_deduction';

    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $wrong_deduction = $this->factory->input()->field()->text($this->language->txt('asq_label_wrong_deduction'));

        if (!is_null($value)) {
            $wrong_deduction = $wrong_deduction->withValue(strval($value->getWrongDeduction()));
        }

        $fields[self::VAR_WRONG_DEDUCTION] = $wrong_deduction;

        return $fields;
    }

    /**
     * @param $postdata array
     * @return MatchingScoringConfiguration
     */
    public function readObjectFromPost(array $postdata) : AbstractValueObject
    {
        return new MatchingScoringConfiguration($this->readFloat($postdata[self::VAR_WRONG_DEDUCTION]));
    }

    /**
     * @return MatchingScoringConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return new MatchingScoringConfiguration();
    }
}
