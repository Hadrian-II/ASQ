<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\Projection;

use ILIAS\Data\UUID\Uuid;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Question;
use srag\asq\Domain\Model\QuestionInfo;

class PublishedQuestionRepository
{
    /**
    * @param Question $question
    */
    public function saveNewQuestionRevision(QuestionDto $question)
    {
        $question_ar = QuestionAr::createNew($question);
        $question_ar->create();

        $question_list = QuestionListItemAr::createNew($question);
        $question_list->create();
    }

    /**
     * @param Question $current
     * @param QuestionDto $old
     * @return boolean
     */
    private function contentEquals(Question $current, QuestionDto $old)
    {
        return $current->getData()->equals($old->getData()) &&
               $current->getPlayConfiguration()->equals($old->getPlayConfiguration()) &&
               $current->getAnswerOptions()->equals($old->getAnswerOptions());
    }

    /**
     * @param Uuid $question_id
     * @param string $name
     * @return bool
     */
    public function revisionExists(Uuid $question_id, string $name) : bool
    {
        return QuestionAr::where(['revision_name' => $name, 'question_id' => $question_id->toString()])->count() > 0;
    }

    /**
     * @param Uuid $question_id
     * @param string $name
     * @return QuestionDto
     */
    public function getQuestionRevision(Uuid $question_id, string $name) : QuestionDto
    {
        /** @var QuestionAr $revision */
        $revision = QuestionAr::where(['revision_name' => $name, 'question_id' => $question_id->toString()])->first();

        return $revision->getQuestion();
    }

    /**
     * @param Uuid $question_id
     * @return QuestionDto[]
     */
    public function getAllQuestionRevisions(Uuid $question_id) : array
    {
        /** @var QuestionListItemAr $revision */
        $revisions = QuestionListItemAr::where(['question_id' => $question_id->toString()])->get();

        return array_map(function ($revision) {
            return new QuestionInfo($revision);
        }, $revisions);
    }
}
