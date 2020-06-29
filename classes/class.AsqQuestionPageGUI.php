<?php
declare(strict_types=1);

use srag\asq\UserInterface\Web\PathHelper;
use srag\asq\UserInterface\Web\Component\QuestionComponent;
use ILIAS\DI\UIServices;

/**
 * Class AsqQuestionPageGUI
 *
 * GUI for editing Question in ILIAS page editor
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 * @author  Björn Heyser <bh@bjoernheyser.de>
 * @author  Martin Studer <ms@studer-raimann.ch>
 *
 * @ilCtrl_Calls AsqQuestionPageGUI: ilPageEditorGUI
 * @ilCtrl_Calls AsqQuestionPageGUI: ilEditClipboardGUI
 * @ilCtrl_Calls AsqQuestionPageGUI: ilMDEditorGUI
 * @ilCtrl_Calls AsqQuestionPageGUI: ilPublicUserProfileGUI
 * @ilCtrl_Calls AsqQuestionPageGUI: ilNoteGUI
 * @ilCtrl_Calls AsqQuestionPageGUI: ilInternalLinkGUI
 * @ilCtrl_Calls AsqQuestionPageGUI: ilPropertyFormGUI
 */
class AsqQuestionPageGUI extends ilPageObjectGUI
{
    const PAGE_TYPE = 'asqq';

    const TEMP_PRESENTATION_TITLE_PLACEHOLDER = '___TEMP_PRESENTATION_TITLE_PLACEHOLDER___';

    /**
     * @var string
     */
    public $originalPresentationTitle = '';
    /**
     * @var bool
     */
    public $a_output = false;

    /**
     * @var QuestionComponent
     */
    private $component;

    /**
     * @var UIServices
     */
    private $ui;

    /**
     * @param int $parent_int_id
     * @param int $page_int_id
     * @param UIServices $ui
     */
    function __construct(int $parent_int_id, int $page_int_id, UIServices $ui)
    {
        $this->ui = $ui;

        $this->createPageIfNotExists(self::PAGE_TYPE, $parent_int_id, $page_int_id);

        parent::__construct(self::PAGE_TYPE, $page_int_id, 0, false);

        $this->page_back_title = $this->lng->txt("page");

        // content and syntax styles
        $this->ui->mainTemplate()->setCurrentBlock("ContentStyle");
        $this->ui->mainTemplate()->setVariable("LOCATION_CONTENT_STYLESHEET", ilObjStyleSheet::getContentStylePath(0));
        $this->ui->mainTemplate()->parseCurrentBlock();
        $this->ui->mainTemplate()->setCurrentBlock("SyntaxStyle");
        $this->ui->mainTemplate()->setVariable("LOCATION_SYNTAX_STYLESHEET", ilObjStyleSheet::getSyntaxStylePath());
        $this->ui->mainTemplate()->parseCurrentBlock();
    }

    private function createPageIfNotExists(string $page_type, int $parent_int_id, int $page_int_id)
    {
        if (ilPageObject::_exists($page_type, $page_int_id) === false) {
            include_once(PathHelper::getBasePath(__DIR__)  . "/src/UserInterface/Web/Page/class.AsqPageObject.php");
            $page = new AsqPageObject();
            $page->setParentType($page_type);
            $page->setParentId($parent_int_id);
            $page->setId($page_int_id);

            $page->create();
        }
    }

    public function getOriginalPresentationTitle()
    {
        return $this->originalPresentationTitle;
    }

    public function setOriginalPresentationTitle($originalPresentationTitle)
    {
        $this->originalPresentationTitle = $originalPresentationTitle;
    }

    protected function isPageContainerToBeRendered()
    {
        return $this->getRenderPageContainer();
    }

    public function showPage()
    {
        /**
         * enable page toc as placeholder for info and actions block
         * @see self::insertPageToc
         */

        $config = $this->getPageConfig();
        $config->setEnablePageToc('y');
        $this->setPageConfig($config);

        return parent::showPage();
    }

    /**
     * support the addition of question info and actions below the title
     */

    /**
     * Set the HTML of a question info block below the title (number, status, ...)
     * @param string	$a_html
     */
    public function setQuestionInfoHTML($a_html)
    {
        $this->questionInfoHTML = $a_html;
    }

    /**
     * Set the HTML of a question actions block below the title
     * @param string 	$a_html
     */
    public function setQuestionActionsHTML($a_html)
    {
        $this->questionActionsHTML = $a_html;
    }

    function setQuestionComponent(QuestionComponent $component) {
        $this->component = $component;
        $this->setQuestionHTML([$this->getId() => $component->renderHtml()]);
    }

    function getQuestionComponent() : QuestionComponent {
        return $this->component;
    }
}
