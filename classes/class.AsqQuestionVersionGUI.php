<?php
declare(strict_types=1);

use ILIAS\DI\HTTPServices;
use ILIAS\DI\UIServices;
use ILIAS\Data\UUID\Uuid;
use ILIAS\UI\Component\Input\Container\Form\Standard;
use srag\asq\Application\Service\AsqServices;
use srag\asq\Domain\Model\QuestionInfo;
use srag\asq\Domain\QuestionDto;
use srag\asq\Infrastructure\Helpers\PathHelper;
use srag\asq\Application\Exception\AsqException;

/**
 * Class AsqQuestionVersionGUI
 *
 * GUI to display list of Versions of Question
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class AsqQuestionVersionGUI
{
    use PathHelper;

    const CMD_DELETE_VERSION = 'deleteVersion';
    const COL_INDEX = 'REVISION_INDEX';
    const COL_NAME = 'REVISION_NAME';
    const COL_DATE = 'REVISION_DATE';
    const COL_CREATOR = 'REVISION_CREATOR';
    const COL_ACTIONS = 'REVISION_ACTIONS';
    const PREVIEW_LINK = 'PREVIEW_LINK';
    const PREVIEW_LABEL = 'PREVIEW_LABEL';
    const ROLLBACK_LINK = 'ROLLBACK_LINK';
    const ROLLBACK_LABEL = 'ROLLBACK_LABEL';
    const PARAM_SELECTED_VERSION = 'selVersion';
    const DELETE_LINK = 'DELETE_LINK';
    const DELETE_LABEL = 'DELETE_LABEL';

    protected Uuid $question_id;

    private ilLanguage $language;

    private UIServices $ui;

    private ASQServices $asq;

    private HTTPServices $http;

    private ilCtrl $ctrl;

    private QuestionDto $question;

    public function __construct(
        Uuid $question_id,
        ilLanguage $language,
        UIServices $ui,
        ASQServices $asq,
        HTTPServices $http,
        ilCtrl $ctrl)
    {
        $this->question_id = $question_id;
        $this->question = $asq->question()->getQuestionByQuestionId($question_id);
        $this->language = $language;
        $this->ui = $ui;
        $this->asq = $asq;
        $this->http = $http;
        $this->ctrl = $ctrl;
    }

    public function executeCommand() : void
    {
        if ($this->ctrl->getCmd() === self::CMD_DELETE_VERSION) {
            $this->deleteVersion();
        }

        $this->displayVersions();
    }

    private function deleteVersion() : void
    {
        $name = $_GET[self::PARAM_SELECTED_VERSION];

        $this->asq->question()->deleteQuestionRevision($name, $this->question_id);
    }

    private function displayVersions() : void
    {
        $creation_form = $this->createCreationForm();

        if ($this->http->request()->getMethod() === 'POST') {
            $name = $creation_form->withRequest($this->http->request())->getData()[0];

            try {
                $this->asq->question()->createQuestionRevision($name, $this->question_id);
                ilutil::sendInfo($this->language->txt('asq_revision_created'));
            }
            catch (AsqException $ex) {
                ilUtil::sendFailure($ex->getMessage());
            }
        }
        else if ($this->question->hasUnrevisedChanges()) {
            ilutil::sendInfo($this->language->txt('asq_question_has_changes'));
        }

        $question_table = $this->createVersionTable();

        $this->ui->mainTemplate()->setContent(
            $this->ui->renderer()->render($creation_form) .
            $question_table->getHTML()
        );
    }

    private function createCreationForm() : Standard
    {
        $name = $this->ui->factory()->input()->field()->text('Name')
                    ->withMaxLength(32)
                    ->withRequired(true);

        return $this->ui->factory()->input()->container()->form()->standard('', [ $name ])
                    ->withSubmitCaption("Create"); //TODO translate if accepted to core
    }

    private function createVersionTable() : ilTable2GUI
    {
        $question_table = new ilTable2GUI($this);
        $question_table->setRowTemplate("tpl.versions_row.html", $this->getBasePath(__DIR__));
        $question_table->addColumn('', self::COL_INDEX);
        $question_table->addColumn($this->language->txt('asq_header_revision_name'), self::COL_NAME);
        $question_table->addColumn($this->language->txt('asq_header_revision_date'), self::COL_DATE);
        $question_table->addColumn($this->language->txt('asq_header_revision_creator'), self::COL_CREATOR);
        $question_table->addColumn($this->language->txt('asq_header_revision_actions'), self::COL_ACTIONS);
        $question_table->setData($this->getRevisionsAsAssocArray());
        return $question_table;
    }


    /**
     * Gets values to display in table from Question
     *
     * @return string[]
     */
    private function getRevisionsAsAssocArray() : array
    {
        $revisions = $this->asq->question()->getAllRevisionsOfQuestion($this->question_id);

        if ($revisions === []) {
            return [];
        }

        $rollback_label = $this->language->txt('asq_label_revision_rollback');
        $delete_label = $this->language->txt('asq_label_revision_delete');

        /** @var $question QuestionInfo */
        return array_map(function ($question, $ix) use ($rollback_label, $delete_label) {
            $preview = $this->asq->link()->getPreviewLink($this->question_id, $question->getRevisionName());
            $edit = $this->asq->link()->getEditLink($this->question_id, $question->getRevisionName());
            $this->ctrl->setParameter($this, self::PARAM_SELECTED_VERSION, $question->getRevisionName());
            $delete_link = $this->ctrl->getLinkTarget($this, self::CMD_DELETE_VERSION);

            return [
                self::COL_INDEX => $ix,
                self::COL_NAME => $question->getRevisionName(),
                self::COL_DATE => $question->getCreated()->get(IL_CAL_DATETIME),
                self::COL_CREATOR => $question->getAuthor(),
                self::PREVIEW_LINK => $preview->getAction(),
                self::PREVIEW_LABEL => $preview->getLabel(),
                self::ROLLBACK_LINK => $edit->getAction(),
                self::ROLLBACK_LABEL => $rollback_label,
                self::DELETE_LINK => $delete_link,
                self::DELETE_LABEL => $delete_label,
            ];
        }, $revisions, range(1, count($revisions)));
    }
}
