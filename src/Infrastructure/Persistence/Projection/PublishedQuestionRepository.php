<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\Projection;

use ILIAS\Data\UUID\Uuid;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Question;
use srag\asq\Domain\Model\QuestionInfo;

/**
 * Class PublishedQuestionRepository
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class PublishedQuestionRepository
{
    public function saveNewQuestionRevision(QuestionDto $question) : void
    {
        $question_ar = QuestionAr::createNew($question);
        $question_ar->create();

        $question_list = QuestionListItemAr::createNew($question);
        $question_list->create();
    }

    public function saveBasicQuestion(QuestionDto $question) : void
    {
        $question_list =
            QuestionListItemAr::where(
                ['question_id' => $question->getId()->toString()]
            )->first();

        if ($question_list === null) {
            $question_list = QuestionListItemAr::createNew($question);
            $question_list->create();
        }
        else {
            $question_list->updateQuestion($question);
            $question_list->save();
        }
    }

    public function revisionExists(Uuid $question_id, string $name) : bool
    {
        return QuestionAr::where(['revision_name' => $name, 'question_id' => $question_id->toString()])->count() > 0;
    }

    public function deleteQuestionRevision(Uuid $question_id, string $name) : void
    {
        QuestionAr::where(['revision_name' => $name, 'question_id' => $question_id->toString()])->first()->delete();
        QuestionListItemAr::where(['revision_name' => $name, 'question_id' => $question_id->toString()])->first()->delete();
    }

    public function getQuestionRevision(Uuid $question_id, string $name) : QuestionDto
    {
        /** @var QuestionAr $revision */
        $revision = QuestionAr::where(['revision_name' => $name, 'question_id' => $question_id->toString()])->first();

        return $revision->getQuestion();
    }

    public function getAllQuestionRevisions(Uuid $question_id) : array
    {
        /** @var QuestionListItemAr $revision */
        $revisions = QuestionListItemAr::where(['question_id' => $question_id->toString()])->get();

        return array_map(function ($revision) {
            return new QuestionInfo($revision);
        }, $revisions);
    }
}
