<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Setup\sql;

use srag\asq\Infrastructure\Persistence\QuestionType;
use srag\asq\Infrastructure\Persistence\SimpleStoredAnswer;
use srag\asq\Infrastructure\Persistence\EventStore\QuestionEventStoreAr;
use srag\asq\Infrastructure\Persistence\Projection\QuestionAr;
use srag\asq\Infrastructure\Persistence\Projection\QuestionListItemAr;
use srag\asq\Questions\Choice\Editor\ImageMap\ImageMapEditor;
use srag\asq\Questions\Choice\Editor\MultipleChoice\MultipleChoiceEditor;
use srag\asq\Questions\Choice\Form\Editor\ImageMap\ImageMapFormFactory;
use srag\asq\Questions\Choice\Form\Editor\MultipleChoice\MultipleChoiceFormFactory;
use srag\asq\Questions\Choice\Form\Editor\MultipleChoice\SingleChoiceFormFactory;
use srag\asq\Questions\Choice\Scoring\MultipleChoiceScoring;
use srag\asq\Questions\Cloze\Editor\ClozeEditor;
use srag\asq\Questions\Cloze\Form\ClozeFormFactory;
use srag\asq\Questions\Cloze\Scoring\ClozeScoring;
use srag\asq\Questions\ErrorText\Editor\ErrorTextEditor;
use srag\asq\Questions\ErrorText\Form\ErrorTextFormFactory;
use srag\asq\Questions\ErrorText\Scoring\ErrorTextScoring;
use srag\asq\Questions\Essay\Editor\EssayEditor;
use srag\asq\Questions\Essay\Form\EssayFormFactory;
use srag\asq\Questions\Essay\Scoring\EssayScoring;
use srag\asq\Questions\FileUpload\Editor\FileUploadEditor;
use srag\asq\Questions\FileUpload\Form\FileUploadFormFactory;
use srag\asq\Questions\FileUpload\Scoring\FileUploadScoring;
use srag\asq\Questions\Formula\Editor\FormulaEditor;
use srag\asq\Questions\Formula\Form\FormulaFormFactory;
use srag\asq\Questions\Formula\Scoring\FormulaScoring;
use srag\asq\Questions\Kprim\Editor\KprimChoiceEditor;
use srag\asq\Questions\Kprim\Form\KprimChoiceFormFactory;
use srag\asq\Questions\Kprim\Scoring\KprimChoiceScoring;
use srag\asq\Questions\Kprim\Storage\KprimStorage;
use srag\asq\Questions\Matching\Editor\MatchingEditor;
use srag\asq\Questions\Matching\Form\MatchingFormFactory;
use srag\asq\Questions\Matching\Scoring\MatchingScoring;
use srag\asq\Questions\Numeric\Editor\NumericEditor;
use srag\asq\Questions\Numeric\Form\NumericFormFactory;
use srag\asq\Questions\Numeric\Scoring\NumericScoring;
use srag\asq\Questions\Ordering\Editor\OrderingEditor;
use srag\asq\Questions\Ordering\Form\OrderingFormFactory;
use srag\asq\Questions\Ordering\Scoring\OrderingScoring;
use srag\asq\Questions\TextSubset\Editor\TextSubsetEditor;
use srag\asq\Questions\TextSubset\Form\TextSubsetFormFactory;
use srag\asq\Questions\TextSubset\Scoring\TextSubsetScoring;
use srag\asq\Questions\Ordering\Form\OrderingTextFormFactory;
use srag\asq\Application\Service\ASQServices;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\Setup\SetupRQES;
use ilDBInterface;
use srag\asq\Questions\Cloze\Storage\ClozeStorage;
use srag\asq\Questions\Choice\Storage\MultipleChoice\MultipleChoiceStorage;
use srag\asq\Questions\Choice\Storage\ImageMap\ImageMapStorage;
use srag\asq\Questions\ErrorText\Storage\ErrorTextStorage;
use srag\asq\Questions\Numeric\Storage\NumericStorage;
use srag\asq\Questions\Formula\Storage\FormulaStorage;
use srag\asq\Questions\TextSubset\Storage\TextSubsetStorage;
use srag\asq\Questions\Ordering\Storage\OrderingStorage;
use srag\asq\Questions\Matching\Storage\MatchingStorage;
use srag\asq\Questions\Essay\Storage\EssayStorage;
use srag\asq\Questions\FileUpload\Storage\FileUploadStorage;

/**
 * Class SetupDatabase
 *
 * @author Martin Studer <ms@studer-raimann.ch>
 */
class SetupDatabase
{
    const SINGLE_CHOICE = 'asq_question_single_answer';
    const MULTIPLE_CHOICE = 'asq_question_multiple_answer';
    const KPRIM = 'asq_question_kprim_answer';
    const ERROR_TEXT = 'asq_question_error_text';
    const IMAGE_MAP = 'asq_question_image_map';
    const CLOZE = 'asq_question_cloze';
    const NUMERIC = 'asq_question_numeric';
    const FORMULA = 'asq_question_formula';
    const TEXT_SUBSET = 'asq_question_text_subset';
    const ORDERING = 'asq_question_ordering';
    const MATCHING = 'asq_question_matching';
    const ESSAY = 'asq_question_essay';
    const FILE_UPLOAD = 'asq_question_file_upload';
    const ORDERING_TEXT = 'asq_question_ordering_text';

    /**
     * @var ASQServices
     */
    private $asq;

    /**
     * @var ilDBInterface
     */
    private $db;

    private function __construct()
    {
        global $ASQDIC;
        global $DIC;

        $this->asq = $ASQDIC->asq();
        $this->db = $DIC->database();
    }


    public static function new() : SetupDatabase
    {
        return new self();
    }


    public function run() : void
    {
        QuestionListItemAr::updateDB();
        QuestionAr::updateDB();
        SimpleStoredAnswer::updateDB();
        QuestionType::updateDB();
        QuestionType::truncateDB();

        $this->addQuestionTypes();

        $rqes_setup = new SetupRQES($this->db);
        $rqes_setup->setup();
    }

    private function addQuestionTypes() : void
    {
        $this->asq->question()->addQuestionType(
            self::SINGLE_CHOICE,
            SingleChoiceFormFactory::class,
            MultipleChoiceEditor::class,
            MultipleChoiceScoring::class,
            MultipleChoiceStorage::class
        );

        $this->asq->question()->addQuestionType(
            self::MULTIPLE_CHOICE,
            MultipleChoiceFormFactory::class,
            MultipleChoiceEditor::class,
            MultipleChoiceScoring::class,
            MultipleChoiceStorage::class
        );

        $this->asq->question()->addQuestionType(
            self::KPRIM,
            KprimChoiceFormFactory::class,
            KprimChoiceEditor::class,
            KprimChoiceScoring::class,
            KprimStorage::class
        );

        $this->asq->question()->addQuestionType(
            self::ERROR_TEXT,
            ErrorTextFormFactory::class,
            ErrorTextEditor::class,
            ErrorTextScoring::class,
            ErrorTextStorage::class
        );

        $this->asq->question()->addQuestionType(
            self::IMAGE_MAP,
            ImageMapFormFactory::class,
            ImageMapEditor::class,
            MultipleChoiceScoring::class,
            ImageMapStorage::class
        );

        $this->asq->question()->addQuestionType(
            self::CLOZE,
            ClozeFormFactory::class,
            ClozeEditor::class,
            ClozeScoring::class,
            ClozeStorage::class
        );

        $this->asq->question()->addQuestionType(
            self::NUMERIC,
            NumericFormFactory::class,
            NumericEditor::class,
            NumericScoring::class,
            NumericStorage::class
        );

        $this->asq->question()->addQuestionType(
            self::FORMULA,
            FormulaFormFactory::class,
            FormulaEditor::class,
            FormulaScoring::class,
            FormulaStorage::class
        );

        $this->asq->question()->addQuestionType(
            self::TEXT_SUBSET,
            TextSubsetFormFactory::class,
            TextSubsetEditor::class,
            TextSubsetScoring::class,
            TextSubsetStorage::class
        );

        $this->asq->question()->addQuestionType(
            self::ORDERING,
            OrderingFormFactory::class,
            OrderingEditor::class,
            OrderingScoring::class,
            OrderingStorage::class
        );

        $this->asq->question()->addQuestionType(
            self::MATCHING,
            MatchingFormFactory::class,
            MatchingEditor::class,
            MatchingScoring::class,
            MatchingStorage::class
        );

        $this->asq->question()->addQuestionType(
            self::ESSAY,
            EssayFormFactory::class,
            EssayEditor::class,
            EssayScoring::class,
            EssayStorage::class
        );

        $this->asq->question()->addQuestionType(
            self::FILE_UPLOAD,
            FileUploadFormFactory::class,
            FileUploadEditor::class,
            FileUploadScoring::class,
            FileUploadStorage::class
        );

        $this->asq->question()->addQuestionType(
            self::ORDERING_TEXT,
            OrderingTextFormFactory::class,
            OrderingEditor::class,
            OrderingScoring::class,
            OrderingStorage::class
        );
    }

    public function uninstall() : void
    {
        $this->db->dropTable(QuestionEventStoreAr::STORAGE_NAME, false);
        $this->db->dropTable(QuestionListItemAr::STORAGE_NAME, false);
        $this->db->dropTable(QuestionAr::STORAGE_NAME, false);
        $this->db->dropTable(SimpleStoredAnswer::STORAGE_NAME, false);
        $this->db->dropTable(QuestionType::STORAGE_NAME, false);

        $rqes_setup = new SetupRQES($this->db);
        $rqes_setup->drop();
    }
}
