<?php
declare(strict_types=1);

use srag\asq\AsqGateway;
use srag\asq\Domain\Model\QuestionInfo;
use srag\asq\UserInterface\Web\PathHelper;

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
    const CMD_SHOW_VERSIONS = 'showVersions';
    const COL_NAME = 'REVISION_NAME';
    const COL_DATE = 'REVISION_DATE';
    const COL_CREATOR = 'REVISION_CREATOR';
    const COL_ACTIONS = 'REVISION_ACTIONS';
    const PREVIEW_LINK = 'PREVIEW_LINK';
    const PREVIEW_LABEL = 'PREVIEW_LABEL';

    /**
     * @var string
     */
    protected $question_id;

    /**
     * @param string $question_id
     */
    public function __construct(string $question_id)
    {
            $this->question_id = $question_id;
    }

    public function executeCommand() : void
    {
        global $DIC;

        $question_table = new ilTable2GUI($this);
        $question_table->setRowTemplate("tpl.versions_row.html", PathHelper::getBasePath(__DIR__));
        $question_table->addColumn($DIC->language()->txt('asq_header_revision_name'), self::COL_NAME);
        $question_table->addColumn($DIC->language()->txt('asq_header_revision_date'), self::COL_DATE);
        $question_table->addColumn($DIC->language()->txt('asq_header_revision_creator'), self::COL_CREATOR);
        $question_table->addColumn($DIC->language()->txt('asq_header_revision_actions'), self::COL_ACTIONS);
        $question_table->setData($this->getRevisionsAsAssocArray());

        $DIC->ui()->mainTemplate()->setContent($question_table->getHTML());
    }

    /**
     * Gets values to display in table from Question
     *
     * @return string[]
     */
    private function getRevisionsAsAssocArray() : array
    {
        /** @var $question QuestionInfo */
        return array_map(function($question) {
            $preview = AsqGateway::get()->link()->getPreviewLink($this->question_id, $question->getRevisionName());

            return [
                self::COL_NAME => $question->getRevisionName(),
                self::COL_DATE => $question->getCreated()->get(IL_CAL_DATETIME),
                self::COL_CREATOR => $question->getAuthor(),
                self::PREVIEW_LINK => $preview->getAction(),
                self::PREVIEW_LABEL => $preview->getLabel()
            ];
        }, AsqGateway::get()->question()->getAllRevisionsOfQuestion($this->question_id));
    }
}
