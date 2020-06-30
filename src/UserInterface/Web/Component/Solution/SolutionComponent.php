<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component\Solution;

use ilTemplate;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\Domain\Model\Answer\Answer;
use srag\asq\Domain\Model\Scoring\AbstractScoring;
use srag\asq\UserInterface\Web\PathHelper;
use ilLanguage;

/**
 * Class SolutionComponent
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Martin Studer <ms@studer-raimann.ch>
 */
class SolutionComponent
{

    /**
     * @var QuestionDto
     */
    private $question_dto;

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
        $this->question_dto = $question_dto;
        $this->answer = $answer;
        $this->language = $language;

        $scoring_class = QuestionPlayConfiguration::getScoringClass($question_dto->getPlayConfiguration());
        $this->scoring = new $scoring_class($question_dto);
    }

    /**
     * @return string
     */
    public function getHtml() : string
    {
        $tpl = new ilTemplate(PathHelper::getBasePath(__DIR__) . 'templates/default/tpl.solution.html', true, true);

        $score_dto = $this->scoring->score($this->answer);

        $tpl->setCurrentBlock('answer_scoring');
        $tpl->setVariable('ANSWER_SCORE', sprintf($this->language->txt("you_received_a_of_b_points"), $score_dto->getReachedPoints(), $score_dto->getMaxPoints()));
        $tpl->parseCurrentBlock();

        return $tpl->get();
    }
}
