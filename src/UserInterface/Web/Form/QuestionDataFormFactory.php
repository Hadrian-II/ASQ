<?php
declare(strict_types = 1);

namespace srag\asq\UserInterface\Web\Form;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\Model\QuestionData;
use ilTextInputGUI;
use ilSelectInputGUI;
use ilTextAreaInputGUI;
use ilDurationInputGUI;
use ilObjAdvancedEditing;
use Exception;
use srag\asq\UserInterface\Web\PostAccess;

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
class QuestionDataFormFactory extends AbstractObjectFactory
{
    use PostAccess;

    const VAR_TITLE = 'title';
    const VAR_AUTHOR = 'author';
    const VAR_DESCRIPTION = 'description';
    const VAR_QUESTION = 'question';
    const VAR_WORKING_TIME = 'working_time';
    const VAR_LIFECYCLE = 'lifecycle';

    const SECONDS_IN_MINUTE = 60;
    const SECONDS_IN_HOUR = 3600;

    /**
     * Gets field definition to display in QuestionFormGUI
     *
     * @param $values QuestionData
     * @return array
     */
    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $title = new ilTextInputGUI($this->language->txt('asq_label_title'), self::VAR_TITLE);
        $title->setRequired(true);
        $fields[] = $title;

        $author = new ilTextInputGUI($this->language->txt('asq_label_author'), self::VAR_AUTHOR);
        $author->setRequired(true);
        $fields[] = $author;

        $description = new ilTextInputGUI($this->language->txt('asq_label_description'), self::VAR_DESCRIPTION);
        $fields[] = $description;

        $lifecycle = new ilSelectInputGUI($this->language->txt('asq_label_lifecycle'), self::VAR_LIFECYCLE);
        $lifecycle->setOptions([
            QuestionData::LIFECYCLE_DRAFT => $this->language->txt('asq_lifecycle_draft'),
            QuestionData::LIFECYCLE_TO_BE_REVIEWED => $this->language->txt('asq_lifecycle_to_be_reviewed'),
            QuestionData::LIFECYCLE_REJECTED => $this->language->txt('asq_lifecycle_rejected'),
            QuestionData::LIFECYCLE_FINAL => $this->language->txt('asq_lifecycle_final'),
            QuestionData::LIFECYCLE_SHARABLE => $this->language->txt('asq_lifecycle_sharable'),
            QuestionData::LIFECYCLE_OUTDATED => $this->language->txt('asq_lifecycle_outdated')
        ]);
        $fields[] = $lifecycle;

        $question_text = new ilTextAreaInputGUI($this->language->txt('asq_label_question'), self::VAR_QUESTION);
        $question_text->setRequired(true);
        $question_text->setRows(10);
        $question_text->setUseRte(true);
        $question_text->setRteTags(ilObjAdvancedEditing::_getUsedHTMLTags("assessment"));
        $question_text->addPlugin("latex");
        $question_text->addButton("latex");
        $question_text->addButton("pastelatex");
        $fields[] = $question_text;

        $working_time = new ilDurationInputGUI($this->language->txt('asq_label_working_time'), self::VAR_WORKING_TIME);
        $working_time->setShowHours(true);
        $working_time->setShowMinutes(true);
        $working_time->setShowSeconds(true);
        $fields[] = $working_time;

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

        return $fields;
    }

    /**
     * @return AbstractValueObject
     */
    public function readObjectFromPost() : AbstractValueObject
    {
        return QuestionData::create(
            $this->readString(self::VAR_TITLE),
            $this->readString(self::VAR_QUESTION),
            $this->readString(self::VAR_AUTHOR),
            $this->readString(self::VAR_DESCRIPTION),
            $this->readWorkingTime(self::VAR_WORKING_TIME),
            $this->readInt(self::VAR_LIFECYCLE)
        );
    }

    /**
     * @param string $post_name
     * @throws Exception
     * @return int
     */
    private function readWorkingTime(string $post_name) : int
    {
        $HOURS = 'hh';
        $MINUTES = 'mm';
        $SECONDS = 'ss';

        $postval = $this->getPostValue($post_name);

        if (
            is_array($postval) &&
            array_key_exists($HOURS, $postval) &&
            array_key_exists($MINUTES, $postval) &&
            array_key_exists($SECONDS, $postval)) {
            return $postval[$HOURS] * self::SECONDS_IN_HOUR + $postval[$MINUTES] * self::SECONDS_IN_MINUTE + $postval[$SECONDS];
        } else {
            throw new Exception("Variable is not an ilDurationInput");
        }
    }

    /**
     * @return AbstractValueObject
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return null;
    }
}
