<?php
declare(strict_types=1);

namespace ILIAS\AssessmentQuestion\Gateway;

use ILIAS\UI\Component\Link\Standard as UiStandardLink;
use ilAsqQuestionAuthoringGUI;
use ilAsqQuestionCreationGUI;
use ilAsqQuestionConfigEditorGUI;
use ilAsqQuestionPreviewGUI;

/**
 * Class QuestionAuthoring
 *
 * @package ILIAS\Services\AssessmentQuestion\PublicApi
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 * @author  Björn Heyser <bh@bjoernheyser.de>
 * @author  Martin Studer <ms@studer-raimann.ch>
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class LinkService
{
    public function getCreationLink(array $ctrl_stack) :UiStandardLink
    {
        global $DIC;

        array_push($ctrl_stack, ilAsqQuestionAuthoringGUI::class);
        array_push($ctrl_stack, ilAsqQuestionCreationGUI::class);
        
        $DIC->language()->loadLanguageModule('asq');
        return $DIC->ui()->factory()->link()->standard(
            $DIC->language()->txt('asq_authoring_create_question_link'),
            $DIC->ctrl()->getLinkTargetByClass($ctrl_stack)
        );
    }

    /**
     * @return UiStandardLink
     */
    public function getEditLink(string $question_id, array $ctrl_stack = []) :UiStandardLink
    {
        global $DIC;
        
        array_push($ctrl_stack, ilAsqQuestionAuthoringGUI::class);
        array_push($ctrl_stack, ilAsqQuestionConfigEditorGUI::class);

        self::setQuestionUidParameter($question_id);

        return $DIC->ui()->factory()->link()->standard(
            $DIC->language()->txt('asq_authoring_tab_config'),
            $DIC->ctrl()->getLinkTargetByClass($ctrl_stack));
    }


    /**
     * @return UiStandardLink
     */
    public function getPreviewLink(string $question_id, array $ctrl_stack = []) : UiStandardLink
    {
        global $DIC;
        array_push($ctrl_stack, ilAsqQuestionAuthoringGUI::class);
        array_push($ctrl_stack, ilAsqQuestionPreviewGUI::class);

        self::setQuestionUidParameter($question_id);

        return $DIC->ui()->factory()->link()->standard(
            $DIC->language()->txt('asq_authoring_tab_preview'),
            $DIC->ctrl()->getLinkTargetByClass($ctrl_stack)
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
                [ilAsqQuestionAuthoringGUI::class, \ilAsqQuestionPageGUI::class], 'edit'
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
                ilAsqQuestionAuthoringGUI::class, \ilAsqQuestionFeedbackEditorGUI::class
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
                ilAsqQuestionAuthoringGUI::class, \AsqQuestionHintEditorGUI::class
            ])
        );
    }

    /**
     * sets the question uid parameter for the ctrl hub gui ilAsqQuestionAuthoringGUI
     */
    protected function setQuestionUidParameter(string $question_id)
    {
        global $DIC; /* @var \ILIAS\DI\Container $DIC */

        $DIC->ctrl()->setParameterByClass(
            ilAsqQuestionAuthoringGUI::class,
            ilAsqQuestionAuthoringGUI::VAR_QUESTION_ID,
            $question_id
        );
    }
}