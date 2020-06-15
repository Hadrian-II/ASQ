<?php
declare(strict_types=1);

use srag\asq\AsqGateway;
use srag\asq\Domain\QuestionDto;
use srag\asq\UserInterface\Web\Component\Hint\HintComponent;
use srag\asq\UserInterface\Web\Component\Scoring\ScoringComponent;
use srag\asq\UserInterface\Web\PathHelper;

/**
 * Class AsqQuestionPreviewGUI
 *
 * GUI displaying preview of Question
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 * @author  Björn Heyser <bh@bjoernheyser.de>
 * @author  Martin Studer <ms@studer-raimann.ch>
 */
class AsqQuestionPreviewGUI
{
    use PathHelper;

    const CMD_SHOW_PREVIEW = 'showPreview';
    const CMD_SHOW_FEEDBACK = 'showFeedback';
    const CMD_SHOW_HINTS = 'showHints';
    const CMD_SHOW_SCORE = 'showScore';
    const PARAM_REVISON_NAME = 'revisionName';

    /**
     * @var string
     */
    protected $question_id;

    /**
     * @var ?string
     */
    private $revision_name;

    /**
     * @var bool
     */
    private $show_feedback;

    /**
     * @var bool
     */
    private $show_hints;

    /**
     * @var bool
     */
    private $show_score;

    /**
     * @param string $question_id
     */
    public function __construct(string $question_id)
    {
        global $DIC;

        $this->question_id = $question_id;

        if (isset($_GET[self::PARAM_REVISON_NAME])) {
            $this->revision_name = $_GET[self::PARAM_REVISON_NAME];

            $DIC->ctrl()->setParameter(
                $this, self::PARAM_REVISON_NAME, $this->revision_name
            );
        }
    }

    public function executeCommand() : void
    {
        global $DIC;
        /* @var ILIAS\DI\Container $DIC */
        switch ($DIC->ctrl()->getNextClass()) {
            case strtolower(self::class):
            default:
                switch ($DIC->ctrl()->getCmd()) {
                    case self::CMD_SHOW_HINTS:
                        $this->show_hints = true;
                        break;
                    case self::CMD_SHOW_FEEDBACK:
                        $this->show_feedback = true;
                        break;
                    case self::CMD_SHOW_SCORE:
                        $this->show_score = true;
                        break;
                }

                $this->showQuestion();
        }
    }

    public function showQuestion() : void
    {
        global $DIC;

        if (is_null($this->revision_name)) {
            $question_dto = AsqGateway::get()->question()->getQuestionByQuestionId($this->question_id);
        } else {
            $question_dto = AsqGateway::get()->question()->getQuestionRevision($this->question_id, $this->revision_name);
        }

        if (! $question_dto->isComplete()) {
            $DIC->ui()->mainTemplate()->setContent($DIC->language()->txt('asq_no_preview_of incomplete_questions'));
            return;
        }

        $this->renderQuestion($question_dto);
    }

    /**
     * @param QuestionDto $question_dto
     */
    private function renderQuestion(QuestionDto $question_dto)
    {
        global $DIC;

        $question_component = AsqGateway::get()->ui()->getQuestionComponent($question_dto);

        if ($this->show_feedback) {
            $question_component->setRenderFeedback(true);
        }

        $question_tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.question_preview_container.html', true, true, 'Services/AssessmentQuestion');
        $question_tpl->setVariable('FORMACTION', $DIC->ctrl()->getFormAction($this, self::CMD_SHOW_PREVIEW));
        $question_tpl->setVariable('QUESTION_OUTPUT', $question_component->renderHtml());

        if ($this->show_hints) {
            $hint_component = new HintComponent($question_dto->getQuestionHints());
            $question_tpl->setVariable('HINTS', $hint_component->getHtml());
        }

        if ($this->show_score) {
            $score_component = new ScoringComponent($question_dto, $question_component->readAnswer());
            $question_tpl->setVariable('SCORE', $score_component->getHtml());
        }

        $question_tpl->setVariable('SCORE_BUTTON_TITLE', $DIC->language()->txt('asq_score_button_title'));

        if ($question_dto->hasFeedback()) {
            $question_tpl->setCurrentBlock('feedback_button');
            $question_tpl->setVariable('FEEDBACK_BUTTON_TITLE', $DIC->language()->txt('asq_feedback_button_title'));
            $question_tpl->parseCurrentBlock();
        }

        if ($question_dto->hasHints()) {
            $question_tpl->setCurrentBlock('hint_button');
            $question_tpl->setVariable('HINT_BUTTON_TITLE', $DIC->language()->txt('asq_hint_button_title'));
            $question_tpl->parseCurrentBlock();
        }

        $DIC->ui()->mainTemplate()->setContent($question_tpl->get());
    }
}
