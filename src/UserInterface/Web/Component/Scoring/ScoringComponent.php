<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component\Scoring;

use ilLanguage;
use ilTemplate;
use srag\asq\PathHelper;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Answer\Answer;
use srag\asq\Domain\Model\Scoring\AbstractScoring;

/**
 * Class ScoringComponent
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Martin Studer <ms@studer-raimann.ch>
 */
class ScoringComponent
{
    use PathHelper;

    /**
     * @var Answer
     */
    private $answer;

    /**
     * @var AbstractScoring
     */
    private $scoring;

    /**
     * @var ilLanguage
     */
    private $language;


    /**
     * @param QuestionDto $question_dto
     * @param Answer $answer
     * @param ilLanguage $language
     */
    public function __construct(QuestionDto $question_dto, Answer $answer, ilLanguage $language)
    {
        $this->language = $language;

        $scoring_class = $question_dto->getType()->getScoringClass();
        $this->scoring = new $scoring_class($question_dto);

        $this->answer = $answer;
    }

    /**
     * @return string
     */
    public function getHtml() : string
    {
        $this->language->loadLanguageModule('assessment');
        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.answer_scoring.html', true, true);

        $tpl->setCurrentBlock('answer_scoring');
        $tpl->setVariable(
            'ANSWER_SCORE',
            sprintf(
                $this->language->txt("you_received_a_of_b_points"),
                $this->scoring->score($this->answer),
                $this->scoring->getMaxScore()
            )
        );
        $tpl->parseCurrentBlock();

        return $tpl->get();
    }
}
