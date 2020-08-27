<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component;

use ILIAS\DI\UIServices;
use ilLanguage;
use ilTemplate;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\PathHelper;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Definitions\IAsqQuestionEditor;
use srag\asq\UserInterface\Web\Component\Feedback\FeedbackComponent;
use srag\asq\UserInterface\Web\Component\Presenter\AbstractPresenter;
use srag\asq\UserInterface\Web\Component\Presenter\DefaultPresenter;

/**
 * Class QuestionComponent
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionComponent
{
    use PathHelper;

    /**
     * @var QuestionDto
     */
    private $question_dto;
    /**
     * @var AbstractPresenter
     */
    private $presenter;
    /**
     * @var IAsqQuestionEditor
     */
    private $editor;
    /**
     * @var bool
     */
    private $show_feedback = false;

    /**
     * @var ilLanguage
     */
    private $language;

    /**
     * @param QuestionDto $question_dto
     * @param UIServices $ui
     * @param ilLanguage $language
     */
    public function __construct(QuestionDto $question_dto, UIServices $ui, ilLanguage $language)
    {
        $this->question_dto = $question_dto;
        $this->language = $language;

        $presenter_class = DefaultPresenter::class;
        $presenter = new $presenter_class($question_dto, $ui);

        $editor_class = $question_dto->getType()->getEditorClass();
        $editor = new $editor_class($question_dto);

        $this->presenter = $presenter;
        $this->editor = $editor;
    }

    /**
     * @param bool $show_feedback
     */
    public function setRenderFeedback(bool $show_feedback) : void
    {
        $this->show_feedback = $show_feedback;
        $this->editor->setRenderFeedback($show_feedback);
    }

    /**
     * @param bool $show_feedback
     * @return string
     */
    public function renderHtml(bool $show_feedback = false) : string
    {
        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.question_view.html', true, true);

        $tpl->setCurrentBlock('question');
        $tpl->setVariable('QUESTION_OUTPUT', $this->presenter->generateHtml($this->editor));
        $tpl->parseCurrentBlock();

        if ($this->show_feedback && !is_null($this->question_dto->getFeedback())) {
            $feedback_component = new FeedbackComponent($this->question_dto, $this->editor->readAnswer(), $this->language);
            $tpl->setCurrentBlock('feedback');
            $tpl->setVariable('QUESTION_FEEDBACK', $feedback_component->getHtml());
            $tpl->parseCurrentBlock();
        }

        return $tpl->get();
    }


    /**
     * @return ?AbstractValueObject
     */
    public function readAnswer() : ?AbstractValueObject
    {
        return $this->editor->readAnswer();
    }

    /**
     * @param AbstractValueObject $answer
     */
    public function setAnswer(AbstractValueObject $answer) : void
    {
        $this->editor->setAnswer($answer);
    }
}
