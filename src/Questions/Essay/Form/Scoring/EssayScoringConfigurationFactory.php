<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Essay\Form\Scoring;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\Model\Scoring\TextScoring;
use srag\asq\Questions\Essay\Scoring\EssayScoring;
use srag\asq\Questions\Essay\Scoring\Data\EssayScoringConfiguration;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

/**
 * Class EssayScoringConfigurationFactory
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class EssayScoringConfigurationFactory extends AbstractObjectFactory
{
    const VAR_TEXT_MATCHING = 'es_text_matching';
    const VAR_SCORING_MODE = 'es_scoring_mode';
    const VAR_POINTS = 'es_points';

    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $text_scoring = new TextScoring($this->language);
        $text_matching = $text_scoring->getScoringTypeSelectionField($this->factory);

        $scoring_mode = $this->factory->input()->field()->radio($this->language->txt('asq_label_scoring_type'))
            ->withOption(
                strval(EssayScoring::SCORING_MANUAL),
                $this->language->txt('asq_label_manual_scoring'),
                $this->language->txt('asq_info_manual_scoring')
            )
            ->withOption(
                strval(EssayScoring::SCORING_AUTOMATIC_ANY),
                $this->language->txt('asq_label_automatic_any'),
                $this->language->txt('asq_info_automatic_any')
            )
            ->withOption(
                strval(EssayScoring::SCORING_AUTOMATIC_ALL),
                $this->language->txt('asq_label_automatic_all'),
                $this->language->txt('asq_info_automatic_all')
            )
            ->withOption(
                strval(EssayScoring::SCORING_AUTOMATIC_ONE),
                $this->language->txt('asq_label_automatic_one'),
                $this->language->txt('asq_info_automatic_one')
            )
            ->withAdditionalOnLoadCode(function($id) {
                    return "il.ASQ.Essay.setScoringMode($($id));";
            });

        $points = $this->factory->input()->field()->text($this->language->txt('asq_label_points'))
                        ->withAdditionalOnLoadCode(function($id) {
                            return "il.ASQ.Essay.setPointsInput($($id));";
                        });

        if ($value !== null) {
            $text_matching = $text_matching->withValue($value->getMatchingMode());
            $scoring_mode = $scoring_mode->withValue(
                strval($value->getScoringMode() ?? EssayScoring::SCORING_MANUAL)
            );
            $points = $points->withValue(strval($value->getPoints()));
        }

        $fields[self::VAR_TEXT_MATCHING] = $text_matching;
        $fields[self::VAR_SCORING_MODE] = $scoring_mode;
        $fields[self::VAR_POINTS] = $points;

        return $fields;
    }

    /**
     * @param $postdata array
     * @return EssayScoringConfiguration
     */
    public function readObjectFromPost(array $postdata) : AbstractValueObject
    {
        return new EssayScoringConfiguration(
            $this->readInt($postdata[self::VAR_TEXT_MATCHING]),
            $this->readInt($postdata[self::VAR_SCORING_MODE]),
            $this->readFloat($postdata[self::VAR_POINTS])
        );
    }

    /**
     * @return EssayScoringConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return new EssayScoringConfiguration();
    }
}
