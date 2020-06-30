<?php
declare(strict_types=1);

namespace srag\asq\Application\Service;

use ILIAS\UI\Component\Link\Standard as UiStandardLink;
use AsqQuestionAuthoringGUI;
use AsqQuestionConfigEditorGUI;
use AsqQuestionCreationGUI;
use AsqQuestionFeedbackEditorGUI;
use AsqQuestionHintEditorGUI;
use AsqQuestionPageGUI;
use AsqQuestionPreviewGUI;
use AsqQuestionVersionGUI;

/**
 * Class QuestionAuthoring
 *
 * Service providing links to Asq GUIs
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class LinkService
{
    /**
     * @return UiStandardLink
     */
    public function getCreationLink() : UiStandardLink
    {
        global $DIC;

        $DIC->language()->loadLanguageModule('asq');
        return $DIC->ui()->factory()->link()->standard(
            $DIC->language()->txt('asq_authoring_create_question_link'),
            $DIC->ctrl()->getLinkTargetByClass([AsqQuestionAuthoringGUI::class, AsqQuestionCreationGUI::class])
        );
    }

    /**
     * @return UiStandardLink
     */
    public function getEditLink(string $question_id) : UiStandardLink
    {
        global $DIC;

        self::setQuestionUidParameter($question_id);

        return $DIC->ui()->factory()->link()->standard(
            $DIC->language()->txt('asq_authoring_tab_config'),
            $DIC->ctrl()->getLinkTargetByClass([AsqQuestionAuthoringGUI::class, AsqQuestionConfigEditorGUI::class])
        );
    }


    /**
     * @return UiStandardLink
     */
    public function getPreviewLink(string $question_id, ?string $revision_name = null) : UiStandardLink
    {
        global $DIC;

        self::setQuestionUidParameter($question_id);

        if (!is_null($revision_name)) {
            $DIC->ctrl()->setParameterByClass(
                AsqQuestionPreviewGUI::class,
                AsqQuestionPreviewGUI::PARAM_REVISON_NAME,
                $revision_name
            );
        }

        return $DIC->ui()->factory()->link()->standard(
            $DIC->language()->txt('asq_authoring_tab_preview'),
            $DIC->ctrl()->getLinkTargetByClass([AsqQuestionAuthoringGUI::class, AsqQuestionPreviewGUI::class])
        );
    }

    /**
     * @return UiStandardLink
     */
    public function getEditPageLink(string $question_id) : UiStandardLink
    {
        global $DIC; /* @var \ILIAS\DI\Container $DIC */

        self::setQuestionUidParameter($question_id);

        return $DIC->ui()->factory()->link()->standard(
            $DIC->language()->txt('asq_authoring_tab_pageview'),
            $DIC->ctrl()->getLinkTargetByClass(
                [AsqQuestionAuthoringGUI::class, AsqQuestionPageGUI::class],
                'edit'
            )
        );
    }


    /**
     * @return UiStandardLink
     */
    public function getEditFeedbacksLink(string $question_id) : UiStandardLink
    {
        global $DIC; /* @var \ILIAS\DI\Container $DIC */

        self::setQuestionUidParameter($question_id);

        return $DIC->ui()->factory()->link()->standard(
            $DIC->language()->txt('asq_authoring_tab_feedback'),
            $DIC->ctrl()->getLinkTargetByClass([
                AsqQuestionAuthoringGUI::class, AsqQuestionFeedbackEditorGUI::class
            ])
        );
    }


    /**
     * @return UiStandardLink
     */
    public function getEditHintsLink(string $question_id) : UiStandardLink
    {
        global $DIC; /* @var \ILIAS\DI\Container $DIC */

        self::setQuestionUidParameter($question_id);

        return $DIC->ui()->factory()->link()->standard(
            $DIC->language()->txt('asq_authoring_tab_hints'),
            $DIC->ctrl()->getLinkTargetByClass([
                AsqQuestionAuthoringGUI::class, AsqQuestionHintEditorGUI::class
            ])
        );
    }

    /**
     * @return UiStandardLink
     */
    public function getRevisionsLink(string $question_id) : UiStandardLink
    {
        global $DIC; /* @var \ILIAS\DI\Container $DIC */

        self::setQuestionUidParameter($question_id);

        return $DIC->ui()->factory()->link()->standard(
            $DIC->language()->txt('asq_authoring_tab_versions'),
            $DIC->ctrl()->getLinkTargetByClass([
                AsqQuestionAuthoringGUI::class, AsqQuestionVersionGUI::class
            ])
        );
    }

    /**
     * sets the question uid parameter for the ctrl hub gui ilAsqQuestionAuthoringGUI
     *
     * @param $question_id string
     */
    protected function setQuestionUidParameter(string $question_id) : void
    {
        global $DIC; /* @var \ILIAS\DI\Container $DIC */

        $DIC->ctrl()->setParameterByClass(
            AsqQuestionAuthoringGUI::class,
            AsqQuestionAuthoringGUI::VAR_QUESTION_ID,
            $question_id
        );
    }
}
