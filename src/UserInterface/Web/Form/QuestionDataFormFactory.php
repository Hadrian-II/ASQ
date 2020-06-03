<?php

namespace srag\asq\UserInterface\Web\Form;

use srag\CQRS\Aggregate\AbstractValueObject;
use ilLanguage;
use srag\asq\Domain\Model\QuestionData;
use ilTextInputGUI;
use ilSelectInputGUI;
use ilTextAreaInputGUI;
use ilDurationInputGUI;
use ilObjAdvancedEditing;

/**
 * Class AbstractQuestionFormFactory
 *
 * Form Factory for QuestionData object
 *S
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionDataFormFactory extends AbstractQuestionFormFactory
{
    /**
     * @var ilLanguage;
     */
    private $language;

    /**
     * @param ilLanguage $language
     */
    public function __construct(ilLanguage $language)
    {
        $this->language = $language;
    }

    /**
     * Gets field definition to display in QuestionFormGUI
     *
     * @param $values QuestionData
     * @return array
     */
    public function getFormfields(AbstractValueObject $value) : array
    {
        $title = new ilTextInputGUI($this->lang->txt('asq_label_title'), self::VAR_TITLE);
        $title->setRequired(true);
        $this->addItem($title);

        $author = new ilTextInputGUI($this->lang->txt('asq_label_author'), self::VAR_AUTHOR);
        $author->setRequired(true);
        $this->addItem($author);

        $description = new ilTextInputGUI($this->lang->txt('asq_label_description'), self::VAR_DESCRIPTION);
        $this->addItem($description);

        $lifecycle = new ilSelectInputGUI($this->lang->txt('asq_label_lifecycle'), self::VAR_LIFECYCLE);
        $lifecycle->setOptions([
            QuestionData::LIFECYCLE_DRAFT => $this->lang->txt('asq_lifecycle_draft'),
            QuestionData::LIFECYCLE_TO_BE_REVIEWED => $this->lang->txt('asq_lifecycle_to_be_reviewed'),
            QuestionData::LIFECYCLE_REJECTED => $this->lang->txt('asq_lifecycle_rejected'),
            QuestionData::LIFECYCLE_FINAL => $this->lang->txt('asq_lifecycle_final'),
            QuestionData::LIFECYCLE_SHARABLE => $this->lang->txt('asq_lifecycle_sharable'),
            QuestionData::LIFECYCLE_OUTDATED => $this->lang->txt('asq_lifecycle_outdated')
        ]);
        $this->addItem($lifecycle);

        $question_text = new ilTextAreaInputGUI($this->lang->txt('asq_label_question'), self::VAR_QUESTION);
        $question_text->setRequired(true);
        $question_text->setRows(10);
        $question_text->setUseRte(true);
        $question_text->setRteTags(ilObjAdvancedEditing::_getUsedHTMLTags("assessment"));
        $question_text->addPlugin("latex");
        $question_text->addButton("latex");
        $question_text->addButton("pastelatex");
        $this->addItem($question_text);

        $working_time = new ilDurationInputGUI($this->lang->txt('asq_label_working_time'), self::VAR_WORKING_TIME);
        $working_time->setShowHours(true);
        $working_time->setShowMinutes(true);
        $working_time->setShowSeconds(true);
        $this->addItem($working_time);

        if ($value !== null) {
            $title->setValue($value->getTitle());
            $author->setValue($value->getAuthor());
            $description->setValue($value->getDescription());
            $lifecycle->setValue($value->getLifecycle());
            $question_text->setValue($value->getQuestionText());
            $working_time->setHours(floor($value->getWorkingTime() / self::SECONDS_IN_HOUR));
            $working_time->setMinutes(floor($value->getWorkingTime() / self::SECONDS_IN_MINUTE));
            $working_time->setSeconds($value->getWorkingTime() % self::SECONDS_IN_MINUTE);
        } else {
            global $DIC;
            $author->setValue($DIC->user()->fullname);
            $working_time->setMinutes(1);
        }
    }

    /**
     * @return AbstractValueObject
     */
    public function readObjectFromPost(): AbstractValueObject
    {}

    /**
     * @return AbstractValueObject
     */
    public function getDefaultValue(): AbstractValueObject
    {}

}