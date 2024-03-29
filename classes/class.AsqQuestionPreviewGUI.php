<?php
declare(strict_types=1);

use ILIAS\DI\HTTPServices;
use ILIAS\DI\UIServices;
use ILIAS\Data\UUID\Uuid;
use srag\asq\Application\Service\AsqServices;
use srag\asq\Domain\QuestionDto;
use srag\asq\Infrastructure\Helpers\PathHelper;
use srag\asq\UserInterface\Web\Component\Hint\HintComponent;
use srag\asq\UserInterface\Web\Component\Scoring\ScoringComponent;

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

    protected Uuid $question_id;

    private ?string $revision_name = null;

    private bool $show_feedback = false;

    private bool $show_hints = false;

    private bool $show_score = false;

    private ilLanguage $language;

    private UIServices $ui;

    private ilCtrl $ctrl;

    private HTTPServices $http;

    private ASQServices $asq;

    public function __construct(
        Uuid $question_id,
        ilLanguage $language,
        UIServices $ui,
        ilCtrl $ctrl,
        HTTPServices $http,
        ASQServices $asq)
    {
        $this->question_id = $question_id;
        $this->language = $language;
        $this->ui = $ui;
        $this->ctrl = $ctrl;
        $this->http = $http;
        $this->asq = $asq;

        $query = $this->http->request()->getQueryParams();
        if (isset($query[self::PARAM_REVISON_NAME])) {
            $this->revision_name = $query[self::PARAM_REVISON_NAME];

            $this->ctrl->setParameter(
                $this,
                self::PARAM_REVISON_NAME,
                $this->revision_name
            );
        }
    }

    public function executeCommand() : void
    {
        switch ($this->ctrl->getNextClass()) {
            case strtolower(self::class):
            default:
                switch ($this->ctrl->getCmd()) {
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
        if (is_null($this->revision_name)) {
            $question_dto = $this->asq->question()->getQuestionByQuestionId($this->question_id);
        } else {
            $question_dto = $this->asq->question()->getQuestionRevision($this->question_id, $this->revision_name);
        }

        if (!$question_dto->isComplete()) {
            $this->ui->mainTemplate()->setContent($this->language->txt('asq_no_preview_of incomplete_questions'));
            return;
        }

        $this->renderQuestion($question_dto);
    }

    private function renderQuestion(QuestionDto $question_dto) : void
    {
        $question_component = $this->asq->ui()->getQuestionComponent($question_dto);

        if ($this->show_feedback) {
            $question_component = $question_component->withShowFeedback(true);
        }

        if ($this->http->request()->getMethod() === 'POST') {
            $question_component = $question_component->withAnswerFromPost();
        }

        $question_tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.question_preview_container.html', true, true, 'Services/AssessmentQuestion');
        $question_tpl->setVariable('FORMACTION', $this->ctrl->getFormAction($this, self::CMD_SHOW_PREVIEW));
        $question_tpl->setVariable('QUESTION_OUTPUT', $this->ui->renderer()->render($question_component));

        if ($this->show_hints) {
            $hint_component = new HintComponent($question_dto->getQuestionHints());
            $question_tpl->setVariable('HINTS', $hint_component->getHtml());
        }

        if ($this->show_score) {
            $score_component = new ScoringComponent($question_dto, $question_component->getAnswer());
            $question_tpl->setVariable('SCORE', $this->ui->renderer()->render($score_component));
        }

        $question_tpl->setVariable('SCORE_BUTTON_TITLE', $this->language->txt('asq_score_button_title'));

        if ($question_dto->hasFeedback()) {
            $question_tpl->setCurrentBlock('feedback_button');
            $question_tpl->setVariable('FEEDBACK_BUTTON_TITLE', $this->language->txt('asq_feedback_button_title'));
            $question_tpl->parseCurrentBlock();
        }

        if ($question_dto->hasHints()) {
            $question_tpl->setCurrentBlock('hint_button');
            $question_tpl->setVariable('HINT_BUTTON_TITLE', $this->language->txt('asq_hint_button_title'));
            $question_tpl->parseCurrentBlock();
        }

        $this->ui->mainTemplate()->setContent($question_tpl->get());
    }
}
