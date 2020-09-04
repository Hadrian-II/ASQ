<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model\Scoring;

use ilLanguage;
use ILIAS\UI\Factory;
use ILIAS\UI\Component\Input\Field\Select;

/**
 * Class TextScoring
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class TextScoring
{
    const TM_CASE_INSENSITIVE = 1;
    const TM_CASE_SENSITIVE = 2;
    const TM_LEVENSHTEIN_1 = 3;
    const TM_LEVENSHTEIN_2 = 4;
    const TM_LEVENSHTEIN_3 = 5;
    const TM_LEVENSHTEIN_4 = 6;
    const TM_LEVENSHTEIN_5 = 7;

    /**
     * @var ilLanguage
     */
    private $language;

    /**
     * @param ilLanguage $language
     */
    public function __construct(ilLanguage $language)
    {
        $this->language = $language;
    }

    /**
     *
     * @param Factory $factory
     * @return Select
     */
    public function getScoringTypeSelectionField(Factory $factory) : Select
    {
        return $factory->input()->field()->select(
            $this->language->txt('asq_label_text_matching'),
            [self::TM_CASE_INSENSITIVE => $this->language->txt('asq_option_case_insensitive'),
                self::TM_CASE_SENSITIVE => $this->language->txt('asq_option_case_sensitive'),
                self::TM_LEVENSHTEIN_1 => $this->language->txt('asq_option_levenshtein_1'),
                self::TM_LEVENSHTEIN_2 => $this->language->txt('asq_option_levenshtein_2'),
                self::TM_LEVENSHTEIN_3 => $this->language->txt('asq_option_levenshtein_3'),
                self::TM_LEVENSHTEIN_4 => $this->language->txt('asq_option_levenshtein_4'),
                self::TM_LEVENSHTEIN_5 => $this->language->txt('asq_option_levenshtein_5')]
        );
    }

    /**
     * @param string $a
     * @param string $b
     * @param int $matching_type
     * @return bool
     */
    public function isMatch(string $a, string $b, int $matching_type) : bool
    {
        switch ($matching_type) {
            case self::TM_CASE_INSENSITIVE:
                return (strtoupper($a) === strtoupper($b));
            case self::TM_CASE_SENSITIVE:
                return $a === $b;
            case self::TM_LEVENSHTEIN_1:
            case self::TM_LEVENSHTEIN_2:
            case self::TM_LEVENSHTEIN_3:
            case self::TM_LEVENSHTEIN_4:
            case self::TM_LEVENSHTEIN_5:
                return levenshtein($a, $b) < $matching_type - 1;
        }
    }
}
