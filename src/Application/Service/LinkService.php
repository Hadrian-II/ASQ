<?php
declare(strict_types=1);

namespace srag\asq\Application\Service;

use ILIAS\DI\UIServices;
use ILIAS\Data\UUID\Uuid;
use ILIAS\UI\Component\Link\Standard as UiStandardLink;
use AsqQuestionAuthoringGUI;
use AsqQuestionConfigEditorGUI;
use AsqQuestionCreationGUI;
use AsqQuestionFeedbackEditorGUI;
use AsqQuestionHintEditorGUI;
use AsqQuestionPreviewGUI;
use AsqQuestionVersionGUI;
use ilCtrl;
use ilLanguage;

/**
 * Class QuestionAuthoring
 *
 * Service providing links to Asq GUIs
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class LinkService
{
    private UIServices $ui;

    private ilLanguage $lng;

    private ilCtrl $ctrl;

    public function __construct(UIServices $ui, ilLanguage $lng, ilCtrl $ctrl)
    {
        $this->ui = $ui;
        $this->lng = $lng;
        $this->ctrl = $ctrl;
    }

    public function getCreationLink() : UiStandardLink
    {
        $this->lng->loadLanguageModule('asq');
        return $this->ui->factory()->link()->standard(
            $this->lng->txt('asq_authoring_create_question_link'),
            $this->ctrl->getLinkTargetByClass([AsqQuestionAuthoringGUI::class, AsqQuestionCreationGUI::class])
        );
    }

    public function getEditLink(Uuid $question_id, ?string $revision_name = null) : UiStandardLink
    {
        self::setQuestionUidParameter($question_id);

        if (!is_null($revision_name)) {
            $this->ctrl->setParameterByClass(
                AsqQuestionConfigEditorGUI::class,
                AsqQuestionConfigEditorGUI::PARAM_REVISON_NAME,
                $revision_name
            );
        }

        return $this->ui->factory()->link()->standard(
            $this->lng->txt('asq_authoring_tab_config'),
            $this->ctrl->getLinkTargetByClass([AsqQuestionAuthoringGUI::class, AsqQuestionConfigEditorGUI::class])
        );
    }

    public function getPreviewLink(Uuid $question_id, ?string $revision_name = null) : UiStandardLink
    {
        self::setQuestionUidParameter($question_id);

        if (!is_null($revision_name)) {
            $this->ctrl->setParameterByClass(
                AsqQuestionPreviewGUI::class,
                AsqQuestionPreviewGUI::PARAM_REVISON_NAME,
                $revision_name
            );
        }

        return $this->ui->factory()->link()->standard(
            $this->lng->txt('asq_authoring_tab_preview'),
            $this->ctrl->getLinkTargetByClass([AsqQuestionAuthoringGUI::class, AsqQuestionPreviewGUI::class])
        );
    }

    public function getEditFeedbacksLink(Uuid $question_id) : UiStandardLink
    {
        self::setQuestionUidParameter($question_id);

        return $this->ui->factory()->link()->standard(
            $this->lng->txt('asq_authoring_tab_feedback'),
            $this->ctrl->getLinkTargetByClass([
                AsqQuestionAuthoringGUI::class, AsqQuestionFeedbackEditorGUI::class
            ])
        );
    }

    public function getEditHintsLink(Uuid $question_id) : UiStandardLink
    {
        self::setQuestionUidParameter($question_id);

        return $this->ui->factory()->link()->standard(
            $this->lng->txt('asq_authoring_tab_hints'),
            $this->ctrl->getLinkTargetByClass([
                AsqQuestionAuthoringGUI::class, AsqQuestionHintEditorGUI::class
            ])
        );
    }

    public function getRevisionsLink(Uuid $question_id) : UiStandardLink
    {
        self::setQuestionUidParameter($question_id);

        return $this->ui->factory()->link()->standard(
            $this->lng->txt('asq_authoring_tab_versions'),
            $this->ctrl->getLinkTargetByClass([
                AsqQuestionAuthoringGUI::class, AsqQuestionVersionGUI::class
            ])
        );
    }

    /**
     * sets the question uid parameter for the ctrl hub gui ilAsqQuestionAuthoringGUI
     *
     * @param $question_id Uuid
     */
    protected function setQuestionUidParameter(Uuid $question_id) : void
    {
        $this->ctrl->setParameterByClass(
            AsqQuestionAuthoringGUI::class,
            AsqQuestionAuthoringGUI::VAR_QUESTION_ID,
            $question_id->toString()
        );
    }
}
