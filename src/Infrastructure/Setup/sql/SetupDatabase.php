<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Setup\sql;

use srag\asq\AsqGateway;
use srag\asq\Infrastructure\Persistence\QuestionType;
use srag\asq\Infrastructure\Persistence\SimpleStoredAnswer;
use srag\asq\Infrastructure\Persistence\EventStore\QuestionEventStoreAr;
use srag\asq\Infrastructure\Persistence\Projection\QuestionAr;
use srag\asq\Infrastructure\Persistence\Projection\QuestionListItemAr;
use srag\asq\Questions\Choice\Form\Editor\ImageMap\ImageMapFormFactory;
use srag\asq\Questions\Choice\Form\Editor\MultipleChoice\MultipleChoiceFormFactory;
use srag\asq\Questions\Choice\Form\Editor\MultipleChoice\SingleChoiceFormFactory;
use srag\asq\Questions\Cloze\Form\ClozeFormFactory;
use srag\asq\Questions\ErrorText\Form\ErrorTextFormFactory;
use srag\asq\Questions\Essay\Form\EssayFormFactory;
use srag\asq\Questions\FileUpload\Form\FileUploadFormFactory;
use srag\asq\Questions\Formula\Form\FormulaFormFactory;
use srag\asq\Questions\Kprim\Form\KprimChoiceFormFactory;
use srag\asq\Questions\Matching\Form\MatchingFormFactory;
use srag\asq\Questions\Numeric\Form\NumericFormFactory;
use srag\asq\Questions\Ordering\Form\OrderingFormFactory;
use srag\asq\Questions\Ordering\Form\Editor\OrderingTextFormFactory;
use srag\asq\Questions\TextSubset\Form\TextSubsetFormFactory;

/**
 * Class SetupDatabase
 *
 * @author Martin Studer <ms@studer-raimann.ch>
 */
class SetupDatabase
{
    private function __construct()
    {
    }


    public static function new() : SetupDatabase
    {
        return new self();
    }


    public function run() : void
    {
        QuestionEventStoreAr::updateDB();
        QuestionListItemAr::updateDB();
        QuestionAr::updateDB();
        SimpleStoredAnswer::updateDB();
        QuestionType::updateDB();
        QuestionType::truncateDB();

        $this->addQuestionTypes();
    }

    private function addQuestionTypes() : void
    {
        AsqGateway::get()->question()->addQuestionType(
            'asq_question_single_answer',
            SingleChoiceFormFactory::class
        );

        AsqGateway::get()->question()->addQuestionType(
            'asq_question_multiple_answer',
            MultipleChoiceFormFactory::class
        );

        AsqGateway::get()->question()->addQuestionType(
            'asq_question_kprim_answer',
            KprimChoiceFormFactory::class
        );

        AsqGateway::get()->question()->addQuestionType(
            'asq_question_error_text',
            ErrorTextFormFactory::class
        );

        AsqGateway::get()->question()->addQuestionType(
            'asq_question_image_map',
            ImageMapFormFactory::class
        );

        AsqGateway::get()->question()->addQuestionType(
            'asq_question_cloze',
            ClozeFormFactory::class
        );

        AsqGateway::get()->question()->addQuestionType(
            'asq_question_numeric',
            NumericFormFactory::class
        );

        AsqGateway::get()->question()->addQuestionType(
            'asq_question_formula',
            FormulaFormFactory::class
        );

        AsqGateway::get()->question()->addQuestionType(
            'asq_question_text_subset',
            TextSubsetFormFactory::class
        );

        AsqGateway::get()->question()->addQuestionType(
            'asq_question_ordering',
            OrderingFormFactory::class
        );

        AsqGateway::get()->question()->addQuestionType(
            'asq_question_matching',
            MatchingFormFactory::class
        );

        AsqGateway::get()->question()->addQuestionType(
            'asq_question_essay',
            EssayFormFactory::class
        );

        AsqGateway::get()->question()->addQuestionType(
            'asq_question_file_upload',
            FileUploadFormFactory::class
        );

        AsqGateway::get()->question()->addQuestionType(
            'asq_question_ordering_text',
            OrderingTextFormFactory::class
        );
    }

    public function uninstall() : void
    {
        global $DIC;

        $DIC->database()->dropTable(QuestionEventStoreAr::STORAGE_NAME, false);
        $DIC->database()->dropTable(QuestionListItemAr::STORAGE_NAME, false);
        $DIC->database()->dropTable(QuestionAr::STORAGE_NAME, false);
        $DIC->database()->dropTable(SimpleStoredAnswer::STORAGE_NAME, false);
        $DIC->database()->dropTable(QuestionType::STORAGE_NAME, false);
    }
}
