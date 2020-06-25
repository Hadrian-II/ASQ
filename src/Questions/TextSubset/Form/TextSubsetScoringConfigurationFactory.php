<?php
declare(strict_types = 1);

namespace srag\asq\Questions\TextSubset\Form;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\Model\Scoring\TextScoring;
use srag\asq\Questions\TextSubset\TextSubsetScoringConfiguration;
use srag\asq\UserInterface\Web\Form\AbstractObjectFactory;

/**
 * Class TextSubsetScoringConfigurationFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class TextSubsetScoringConfigurationFactory extends AbstractObjectFactory
{
    const VAR_TEXT_MATCHING = 'tss_text_matching';

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\IObjectFactory::getFormfields()
     */
    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $text_matching = TextScoring::getScoringTypeSelectionField(self::VAR_TEXT_MATCHING);
        $fields[self::VAR_TEXT_MATCHING] = $text_matching;

        if ($value !== null) {
            $text_matching->setValue($value->getTextMatching());
        }

        return $fields;
    }

    /**
     * @return TextSubsetScoringConfiguration
     */
    public function readObjectFromPost() : AbstractValueObject
    {
        return TextSubsetScoringConfiguration::create(
            $this->readInt(self::VAR_TEXT_MATCHING));
    }

    /**
     * @return TextSubsetScoringConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return TextSubsetScoringConfiguration::create();
    }
}