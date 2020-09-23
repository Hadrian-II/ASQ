<?php
declare(strict_types=1);

use ILIAS\DI\UIServices;
use ILIAS\Data\UUID\Uuid;
use srag\asq\Application\Service\AsqServices;
use srag\asq\Domain\Model\QuestionInfo;
use srag\asq\Infrastructure\Helpers\PathHelper;

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

    const CMD_SHOW_VERSIONS = 'showVersions';
    const COL_NAME = 'REVISION_NAME';
    const COL_DATE = 'REVISION_DATE';
    const COL_CREATOR = 'REVISION_CREATOR';
    const COL_ACTIONS = 'REVISION_ACTIONS';
    const PREVIEW_LINK = 'PREVIEW_LINK';
    const PREVIEW_LABEL = 'PREVIEW_LABEL';

    /**
     * @var Uuid
     */
    protected $question_id;

    /**
     * @var ilLanguage
     */
    private $language;

    /**
     * @var UIServices
     */
    private $ui;

    /**
     * @var ASQServices
     */
    private $asq;

    /**
     * @param Uuid $question_id
     * @param ilLanguage $language
     * @param UIServices $ui
     * @param ASQServices $asq
     */
    public function __construct(
        Uuid $question_id,
        ilLanguage $language,
        UIServices $ui,
        ASQServices $asq)
    {
        $this->question_id = $question_id;
        $this->language = $language;
        $this->ui = $ui;
        $this->asq = $asq;
    }

    public function executeCommand() : void
    {
        $question_table = new ilTable2GUI($this);
        $question_table->setRowTemplate("tpl.versions_row.html", $this->getBasePath(__DIR__));
        $question_table->addColumn($this->language->txt('asq_header_revision_name'), self::COL_NAME);
        $question_table->addColumn($this->language->txt('asq_header_revision_date'), self::COL_DATE);
        $question_table->addColumn($this->language->txt('asq_header_revision_creator'), self::COL_CREATOR);
        $question_table->addColumn($this->language->txt('asq_header_revision_actions'), self::COL_ACTIONS);
        $question_table->setData($this->getRevisionsAsAssocArray());

        $this->ui->mainTemplate()->setContent($question_table->getHTML());
    }

    /**
     * Gets values to display in table from Question
     *
     * @return string[]
     */
    private function getRevisionsAsAssocArray() : array
    {
        /** @var $question QuestionInfo */
        return array_map(function ($question) {
            $preview = $this->asq->link()->getPreviewLink($this->question_id, $question->getRevisionName());

            return [
                self::COL_NAME => $question->getRevisionName(),
                self::COL_DATE => $question->getCreated()->get(IL_CAL_DATETIME),
                self::COL_CREATOR => $question->getAuthor(),
                self::PREVIEW_LINK => $preview->getAction(),
                self::PREVIEW_LABEL => $preview->getLabel()
            ];
        }, $this->asq->question()->getAllRevisionsOfQuestion($this->question_id));
    }
}
