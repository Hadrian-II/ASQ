<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component\Feedback;

use ilTemplate;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\PathHelper;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Scoring\AbstractScoring;

/**
 * Class AnswerFeedbackComponent
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Martin Studer <ms@studer-raimann.ch>
 */
class AnswerFeedbackComponent
{
    use PathHelper;

    const CSS_CLASS_FEEDBACK_TYPE_CORRECT = 'ilc_qfeedr_FeedbackRight';
    const CSS_CLASS_FEEDBACK_TYPE_WRONG = 'ilc_qfeedw_FeedbackWrong';

    /**
     * @var QuestionDto
     */
    private $question;
    /**
     * @var AbstractValueObject
     */
    private $answer;
    /**
     * @var AbstractScoring
     */
    private $scoring;

    /**
     * @param QuestionDto $question
     * @param AbstractValueObject $answer
     */
    public function __construct(QuestionDto $question, AbstractValueObject $answer)
    {
        $this->question = $question;
        $this->answer = $answer;

        $scoring_class = $question->getType()->getScoringClass();
        $this->scoring = new $scoring_class($question);
    }

    /**
     * @return string
     */
    public function getHtml() : string
    {
        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.answer_feedback.html', true, true);

        include_once("./Services/Style/Content/classes/class.ilObjStyleSheet.php");

        $tpl->setCurrentBlock('answer_feedback');

        if ($this->scoring->getAnswerFeedbackType($this->answer) === AbstractScoring::ANSWER_CORRECT) {
            $answer_feedback = $this->question->getFeedback()->getAnswerCorrectFeedback();
            $answer_feedback_css_class = self::CSS_CLASS_FEEDBACK_TYPE_CORRECT;
        } elseif ($this->scoring->getAnswerFeedbackType($this->answer) === AbstractScoring::ANSWER_INCORRECT) {
            $answer_feedback = $this->question->getFeedback()->getAnswerWrongFeedback();
            $answer_feedback_css_class = self::CSS_CLASS_FEEDBACK_TYPE_WRONG;
        }

        $tpl->setVariable('ANSWER_FEEDBACK', $answer_feedback);
        $tpl->setVariable('ILC_FB_CSS_CLASS', $answer_feedback_css_class);

        $tpl->parseCurrentBlock();

        return $tpl->get();
    }
}
