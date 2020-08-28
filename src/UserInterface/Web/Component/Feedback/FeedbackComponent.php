<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component\Feedback;

use ilLanguage;
use ilTemplate;
use srag\asq\PathHelper;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Answer\Answer;
use srag\asq\UserInterface\Web\Component\Scoring\ScoringComponent;
use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class FeedbackComponent
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Martin Studer <ms@studer-raimann.ch>
 */
class FeedbackComponent
{
    use PathHelper;

    const FEEDBACK_FOCUS_ANCHOR = 'focus';

    /**
     * @var ScoringComponent
     */
    private $scoring_component;

    /**
     * @var AnswerFeedbackComponent
     */
    private $answer_feedback_component;

    /**
     * @var ilLanguage
     */
    private $language;

    /**
     * @param QuestionDto $question_dto
     * @param AbstractValueObject $answer
     * @param ilLanguage $language
     */
    public function __construct(QuestionDto $question_dto, AbstractValueObject $answer, ilLanguage $language)
    {
        $this->scoring_component = new ScoringComponent($question_dto, $answer, $language);
        $this->answer_feedback_component = new AnswerFeedbackComponent($question_dto, $answer);
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getHtml() : string
    {
        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.feedback.html', true, true);

        $tpl->setCurrentBlock('feedback_header');
        $tpl->setVariable('FEEDBACK_HEADER', $this->language->txt('asq_answer_feedback_header'));
        $tpl->parseCurrentBlock();

        $tpl->setCurrentBlock('answer_feedback');
        $tpl->setVariable('ANSWER_FEEDBACK', $this->answer_feedback_component->getHtml());
        $tpl->parseCurrentBlock();

        $tpl->setCurrentBlock('answer_scoring');
        $tpl->setVariable('ANSWER_SCORING', $this->scoring_component->getHtml());
        $tpl->parseCurrentBlock();

        return $tpl->get();
    }


    public function readAnswer() : string
    {
        return $this->editor->readAnswer();
    }


    public function setAnswer(Answer $answer)
    {
        $this->editor->setAnswer($answer->getValue());
    }
}
