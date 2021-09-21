<?php
declare(strict_types = 1);

namespace srag\asq\Questions\TextSubset\Form\Scoring;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\Model\Scoring\TextScoring;
use srag\asq\Questions\TextSubset\Scoring\Data\TextSubsetScoringConfiguration;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

/**
 * Class TextSubsetScoringConfigurationFactory
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class TextSubsetScoringConfigurationFactory extends AbstractObjectFactory
{
    const VAR_TEXT_MATCHING = 'tss_text_matching';

    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $text_scoring = new TextScoring($this->language);
        $text_matching = $text_scoring->getScoringTypeSelectionField($this->factory);

        if ($value !== null) {
            $text_matching = $text_matching->withValue($value->getTextMatching());
        }

        $fields[self::VAR_TEXT_MATCHING] = $text_matching;

        return $fields;
    }

    /**
     * @param $postdata array
     * @return TextSubsetScoringConfiguration
     */
    public function readObjectFromPost(array $postdata) : AbstractValueObject
    {
        return new TextSubsetScoringConfiguration(
            $this->readInt($postdata[self::VAR_TEXT_MATCHING])
        );
    }

    /**
     * @return TextSubsetScoringConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return new TextSubsetScoringConfiguration();
    }
}
