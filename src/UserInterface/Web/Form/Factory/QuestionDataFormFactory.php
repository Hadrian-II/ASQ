<?php
declare(strict_types = 1);

namespace srag\asq\UserInterface\Web\Form\Factory;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\Model\QuestionData;

/**
 * Class AbstractQuestionFormFactory
 *
 * Form Factory for QuestionData object
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionDataFormFactory extends AbstractObjectFactory
{
    const VAR_TITLE = 'title';
    const VAR_AUTHOR = 'author';
    const VAR_DESCRIPTION = 'description';
    const VAR_QUESTION = 'question';
    const VAR_WORKING_TIME = 'working_time';
    const VAR_LIFECYCLE = 'lifecycle';

    /**
     * Gets field definition to display in QuestionFormGUI
     *
     * @param $values QuestionData
     * @return array
     */
    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $title = $this->factory->input()->field()
                    ->text($this->language->txt('asq_label_title'))
                    ->withRequired(true);

        $author = $this->factory->input()->field()
                     ->text($this->language->txt('asq_label_author'))
                     ->withRequired(true);

        $description = $this->factory->input()->field()->text($this->language->txt('asq_label_description'));

        $lifecycle = $this->factory->input()->field()->select(
            $this->language->txt('asq_label_lifecycle'),
            [
                 QuestionData::LIFECYCLE_DRAFT => $this->language->txt('asq_lifecycle_draft'),
                 QuestionData::LIFECYCLE_TO_BE_REVIEWED => $this->language->txt('asq_lifecycle_to_be_reviewed'),
                 QuestionData::LIFECYCLE_REJECTED => $this->language->txt('asq_lifecycle_rejected'),
                 QuestionData::LIFECYCLE_FINAL => $this->language->txt('asq_lifecycle_final'),
                 QuestionData::LIFECYCLE_SHARABLE => $this->language->txt('asq_lifecycle_sharable'),
                 QuestionData::LIFECYCLE_OUTDATED => $this->language->txt('asq_lifecycle_outdated')
            ]);

        $question_text = $this->factory->input()->field()
                            ->textarea($this->language->txt('asq_label_question'))
                            ->withRequired(true);

        $working_time = $this->factory->input()->field()->numeric($this->language->txt('asq_label_working_time'));

        if ($value !== null) {
            $title = $title->withValue($value->getTitle());
            $author = $author->withValue($value->getAuthor());
            $description = $description->withValue($value->getDescription());
            $lifecycle = $lifecycle->withValue($value->getLifecycle());
            $question_text = $question_text->withValue($value->getQuestionText());
            $working_time = $working_time->withValue($value->getWorkingTime());
        } else {
            global $DIC;
            $author = $author->withValue($DIC->user()->fullname);
            $working_time = $working_time->withValue(60);
        }

        $fields[self::VAR_TITLE] = $title;
        $fields[self::VAR_AUTHOR] = $author;
        $fields[self::VAR_DESCRIPTION] = $description;
        $fields[self::VAR_LIFECYCLE] = $lifecycle;
        $fields[self::VAR_QUESTION] = $question_text;
        $fields[self::VAR_WORKING_TIME] = $working_time;

        return $fields;
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\Factory\IObjectFactory::readObjectFromPost()
     */
    public function readObjectFromPost(array $postdata) : AbstractValueObject
    {
        return QuestionData::create(
            $this->readString($postdata[self::VAR_TITLE]),
            $this->readString($postdata[self::VAR_QUESTION]),
            $this->readString($postdata[self::VAR_AUTHOR]),
            $this->readString($postdata[self::VAR_DESCRIPTION]),
            $this->readInt($postdata[self::VAR_WORKING_TIME]),
            $this->readInt($postdata[self::VAR_LIFECYCLE])
        );
    }

    /**
     * @return AbstractValueObject
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return null;
    }
}
