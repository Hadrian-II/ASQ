<?php
declare(strict_types=1);

use ILIAS\DI\UIServices;
use srag\asq\AsqGateway;
use srag\asq\Application\Service\AuthoringContextContainer;

/**
 * Class AsqQuestionAuthoringGUI
 *
 * Main Question Authoring GUI, draws Tabs and calls correct sub GUI
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 * @author  Björn Heyser <bh@bjoernheyser.de>
 * @author  Martin Studer <ms@studer-raimann.ch>
 *
 * @ilCtrl_Calls AsqQuestionAuthoringGUI: AsqQuestionCreationGUI
 * @ilCtrl_Calls AsqQuestionAuthoringGUI: AsqQuestionPreviewGUI
 * @ilCtrl_Calls AsqQuestionAuthoringGUI: AsqQuestionPageGUI
 * @ilCtrl_Calls AsqQuestionAuthoringGUI: AsqQuestionConfigEditorGUI
 * @ilCtrl_Calls AsqQuestionAuthoringGUI: AsqQuestionFeedbackEditorGUI
 * @ilCtrl_Calls AsqQuestionAuthoringGUI: AsqQuestionHintEditorGUI
 * @ilCtrl_Calls AsqQuestionAuthoringGUI: AsqQuestionVersionGUI
 * @ilCtrl_Calls AsqQuestionAuthoringGUI: ilCommonActionDispatcherGUI
 */
class AsqQuestionAuthoringGUI
{
    const TAB_ID_PREVIEW = 'qst_preview_tab';
    const TAB_ID_PAGEVIEW = 'qst_pageview_tab';
    const TAB_ID_CONFIG = 'qst_config_tab';
    const TAB_ID_FEEDBACK = 'qst_feedback_tab';
    const TAB_ID_HINTS = 'qst_hints_tab';
    const TAB_ID_RECAPITULATION = 'qst_recapitulation_tab';
    const TAB_ID_STATISTIC = 'qst_statistic_tab';
    const TAB_ID_VERSIONS = 'qst_versions_tab';

    const VAR_QUESTION_ID = "question_id";

    const CMD_REDRAW_HEADER_ACTION_ASYNC = '';

    /**
     * @var AuthoringContextContainer
     */
    protected $authoring_context_container;
    /**
     * @var string
     */
    protected $question_id;
    /**
     * @var string
     */
    protected $lng_key;

    /**
     * @var ilLanguage
     */
    private $language;

    /**
     * @var UIServices
     */
    private $ui;

    /**
     * @var ilCtrl
     */
    private $ctrl;

    /**
     * @var ilTabsGUI
     */
    private $tabs;

    /**
     * @var ilAccessHandler
     */
    private $access;

    /**
     * @param AuthoringContextContainer $authoring_context_container
     * @param ilLanguage $language
     * @param UIServices $ui
     * @param ilCtrl $ctrl
     * @param ilTabsGUI $tabs
     */
    public function __construct(
        AuthoringContextContainer $authoring_context_container,
        ilLanguage $language,
        UIServices $ui,
        ilCtrl $ctrl,
        ilTabsGUI $tabs,
        ilAccessHandler $access
    ) {
        $this->authoring_context_container = $authoring_context_container;
        $this->language = $language;
        $this->ui = $ui;
        $this->ctrl = $ctrl;
        $this->tabs = $tabs;
        $this->access = $access;

        //we could use this in future in constructer
        $this->lng_key = $this->language->getDefaultLanguage();

        if (isset($_GET[\AsqQuestionAuthoringGUI::VAR_QUESTION_ID])) {
            $this->question_id = $_GET[\AsqQuestionAuthoringGUI::VAR_QUESTION_ID];
        }

        $this->language->loadLanguageModule('asq');
    }

    /**
     * @throws ilCtrlException
     */
    public function executeCommand() : void
    {
        $this->ctrl->setParameter($this, self::VAR_QUESTION_ID, $this->question_id);

        switch ($this->ctrl->getNextClass()) {
            case strtolower(AsqQuestionCreationGUI::class):

                $gui = new AsqQuestionCreationGUI(
                    $this->authoring_context_container,
                    $this->language,
                    $this->ui,
                    $this->ctrl
                );

                $this->ctrl->forwardCommand($gui);

                break;

            case strtolower(AsqQuestionPreviewGUI::class):

                $this->initHeaderAction();
                $this->initAuthoringTabs();
                $this->tabs->activateTab(self::TAB_ID_PREVIEW);

                $gui = new AsqQuestionPreviewGUI(
                    $this->question_id,
                    $this->language,
                    $this->ui,
                    $this->ctrl
                );

                $this->ctrl->forwardCommand($gui);

                break;

            case strtolower(AsqQuestionPageGUI::class):

                $this->initHeaderAction();
                $this->initAuthoringTabs();
                $this->tabs->activateTab(self::TAB_ID_PAGEVIEW);

                $gui = AsqGateway::get()->ui()->getQuestionPage(
                    AsqGateway::get()->question()->getQuestionByQuestionId($this->question_id)
                );

                if (strlen($this->ctrl->getCmd()) == 0 && !$this->isPostVarSet("editImagemapForward_x")) {
                    // workaround for page edit imagemaps, keep in mind

                    $this->ctrl->setCmdClass(strtolower(get_class($gui)));
                    $this->ctrl->setCmd('preview');
                }

                $html = $this->ctrl->forwardCommand($gui);
                $this->ui->mainTemplate()->setContent($html);

                break;

            case strtolower(AsqQuestionConfigEditorGUI::class):

                $this->initHeaderAction();
                $this->initAuthoringTabs();
                $this->tabs->activateTab(self::TAB_ID_CONFIG);

                $gui = new AsqQuestionConfigEditorGUI(
                    $this->authoring_context_container,
                    $this->question_id,
                    $this->language,
                    $this->ui,
                    $this->ctrl
                );

                $this->ctrl->forwardCommand($gui);

                break;

            case strtolower(AsqQuestionFeedbackEditorGUI::class):

                $this->initHeaderAction();
                $this->initAuthoringTabs();
                $this->tabs->activateTab(self::TAB_ID_FEEDBACK);

                $gui = new AsqQuestionFeedbackEditorGUI(
                    AsqGateway::get()->question()->getQuestionByQuestionId($this->question_id),
                    $this->language,
                    $this->ui,
                    $this->ctrl
                );
                $this->ctrl->forwardCommand($gui);

                break;

            case strtolower(AsqQuestionHintEditorGUI::class):

                $this->initHeaderAction();
                $this->initAuthoringTabs();
                $this->tabs->activateTab(self::TAB_ID_HINTS);

                $gui = new AsqQuestionHintEditorGUI(
                    AsqGateway::get()->question()->getQuestionByQuestionId($this->question_id),
                    $this->language,
                    $this->ui
                );

                $this->ctrl->forwardCommand($gui);

                break;

            case strtolower(AsqQuestionVersionGUI::class):

                $this->initHeaderAction();
                $this->initAuthoringTabs();
                $this->tabs->activateTab(self::TAB_ID_VERSIONS);

                $gui = new AsqQuestionVersionGUI(
                    $this->question_id,
                    $this->language,
                    $this->ui
                );

                $this->ctrl->forwardCommand($gui);

                break;

            case strtolower(ilCommonActionDispatcherGUI::class):

                $gui = ilCommonActionDispatcherGUI::getInstanceFromAjaxCall();
                $this->ctrl->forwardCommand($gui);

                break;

            case strtolower(self::class):
            default:

                $cmd = $this->ctrl->getCmd();
                $this->{$cmd}();
        }
    }


    protected function redrawHeaderAction() : void
    {
        echo $this->getHeaderAction() . $this->ui->mainTemplate()->getOnLoadCodeForAsynch();
        exit;
    }


    protected function initHeaderAction() : void
    {
        $this->ui->mainTemplate()->setVariable(
            'HEAD_ACTION',
            $this->getHeaderAction()
        );

        $notesUrl = $this->ctrl->getLinkTargetByClass(
            array('ilCommonActionDispatcherGUI', 'ilNoteGUI'),
            '',
            '',
            true,
            false
        );

        ilNoteGUI::initJavascript($notesUrl, IL_NOTE_PUBLIC, $this->ui->mainTemplate());

        $redrawActionsUrl = $this->ctrl->getLinkTarget(
            $this,
            self::CMD_REDRAW_HEADER_ACTION_ASYNC,
            '',
            true
        );

        $this->ui->mainTemplate()->addOnLoadCode("il.Object.setRedrawAHUrl('$redrawActionsUrl');");
    }


    protected function getHeaderAction() : string
    {
        $dispatcher = new ilCommonActionDispatcherGUI(
            ilCommonActionDispatcherGUI::TYPE_REPOSITORY,
            $this->access,
            $this->authoring_context_container->getObjType(),
            $this->authoring_context_container->getRefId(),
            $this->authoring_context_container->getObjId()
        );

        $ha = $dispatcher->initHeaderAction();
        $ha->enableComments(true, false);

        return $ha->getHeaderAction($this->ui->mainTemplate());
    }


    protected function initAuthoringTabs() : void
    {
        $this->tabs->clearTargets();

        $this->tabs->setBackTarget(
            $this->authoring_context_container->getBackLink()->getLabel(),
            $this->authoring_context_container->getBackLink()->getAction()
        );

        /* TODO fix page
        $page_link = AsqGateway::get()->link()->getEditPageLink($this->question_id);
        $this->tabs->addTab(self::TAB_ID_PAGEVIEW, $page_link->getLabel(), $page_link->getAction());
        */

        $preview_link = AsqGateway::get()->link()->getPreviewLink($this->question_id);
        $this->tabs->addTab(self::TAB_ID_PREVIEW, $preview_link->getLabel(), $preview_link->getAction());

        $edit_link = AsqGateway::get()->link()->getEditLink($this->question_id);
        $this->tabs->addTab(self::TAB_ID_CONFIG, $edit_link->getLabel(), $edit_link->getAction());

        $feedback_link = AsqGateway::get()->link()->getEditFeedbacksLink($this->question_id);
        $this->tabs->addTab(self::TAB_ID_FEEDBACK, $feedback_link->getLabel(), $feedback_link->getAction());

        $hint_link = AsqGateway::get()->link()->getEditHintsLink($this->question_id);
        $this->tabs->addTab(self::TAB_ID_HINTS, $hint_link->getLabel(), $hint_link->getAction());

        $revisions_link = AsqGateway::get()->link()->getRevisionsLink($this->question_id);
        $this->tabs->addTab(self::TAB_ID_VERSIONS, $revisions_link->getLabel(), $revisions_link->getAction());
    }
}
