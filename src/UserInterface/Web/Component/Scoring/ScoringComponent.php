<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component\Scoring;

use ilTemplate;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Answer\Answer;
use srag\asq\Domain\Model\Scoring\AbstractScoring;
use srag\asq\UserInterface\Web\PathHelper;

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
    /**
     * @var Answer
     */
    private $answer;
    /**
     * @var AbstractScoring
     */
    private $scoring;


    //QuestionDto $question_dto, QuestionConfig $question_config, QuestionCommands $question_commands
    public function __construct(QuestionDto $question_dto, Answer $answer)
    {
        $scoring_class = $question_dto->getPlayConfiguration()->getScoringConfiguration()->configurationFor();
        $this->scoring = new $scoring_class($question_dto);
        
        $this->answer = $answer;
    }


    public function getHtml() : string
    {
        global $DIC;
        $DIC->language()->loadLanguageModule('assessment');
        $tpl = new ilTemplate(PathHelper::getBasePath(__DIR__) . 'templates/default/tpl.answer_scoring.html', true, true);

        $tpl->setCurrentBlock('answer_scoring');
        $tpl->setVariable('ANSWER_SCORE', 
            sprintf($DIC->language()->txt("you_received_a_of_b_points"), 
                                          $this->scoring->score($this->answer), 
                                          $this->scoring->getMaxScore()));
        $tpl->parseCurrentBlock();

        return $tpl->get();
    }
}