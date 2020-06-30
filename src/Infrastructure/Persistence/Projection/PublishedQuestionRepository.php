<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\Projection;

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
    
    private function contentEquals(Question $current, QuestionDto $old)
    {
        return $current->getData()->equals($old->getData()) &&
               $current->getPlayConfiguration()->equals($old->getPlayConfiguration()) &&
               $current->getAnswerOptions()->equals($old->getAnswerOptions());
    }
    
    public function revisionExists(string $question_id, string $name) : bool
    {
        return QuestionAr::where(['revision_name' => $name, 'question_id' => $question_id])->count() > 0;
    }
    
    public function getQuestionRevision(string $question_id, string $name) : QuestionDto
    {
        /** @var QuestionAr $revision */
        $revision = QuestionAr::where(['revision_name' => $name, 'question_id' => $question_id])->first();
        
        return $revision->getQuestion();
    }
    
    /**
     * @param string $question_id
     * @return QuestionDto[]
     */
    public function getAllQuestionRevisions(string $question_id) : array
    {
        /** @var QuestionListItemAr $revision */
        $revisions = QuestionListItemAr::where(['question_id' => $question_id])->get();
        
        return array_map(function ($revision) {
            return new QuestionInfo($revision);
        }, $revisions);
    }
}
