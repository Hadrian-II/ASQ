<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace ILIAS\AssessmentQuestion\Test;

use ILIAS\Data\UUID\Factory;
use PHPUnit\Framework\TestCase;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\QuestionData;
use srag\asq\Domain\Model\Answer\Option\AnswerOptions;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Infrastructure\Persistence\QuestionType;
use srag\asq\UserInterface\Web\Component\QuestionComponent;
use srag\asq\Application\Service\ASQServices;

/**
 * Class QuestionTestCase
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
abstract class QuestionTestCase extends TestCase
{
    const TEST_CONTAINER = -1;
    const DONT_TEST = -1;

    private static $ids = [];

    /**
     * @var ASQServices
     */
    protected $asq;

    abstract public function getQuestions() : array;

    public function __create()
    {
        global $ASQDIC;

        $this->asq = $ASQDIC->asq();
    }

    /**
     * @param QuestionData $data
     * @param QuestionPlayConfiguration $play
     * @param AnswerOptions $options
     * @return QuestionDto
     */
    protected function createQuestion(QuestionData $data, QuestionPlayConfiguration $play, ?AnswerOptions $options) : QuestionDto
    {
        $factory = new Factory();

        $question = new QuestionDto();
        $question->setId($factory->uuid4AsString());
        $question->setData($data);
        $question->setPlayConfiguration($play);
        $question->setAnswerOptions($options);
        return $question;
    }

    abstract public function getAnswers() : array;

    abstract public function getExpectedScore(string $question_id, string $answer_id) : float;

    abstract public function getTypeDefinition() : QuestionType;

    public function setUp() : void
    {
    }

    public function questionAnswerProvider() : array
    {
        $mapping = [];

        foreach ($this->getQuestions() as $question_id => $question) {
            foreach ($this->getAnswers() as $answer_id => $answer) {
                $mapping[sprintf('Question "%s" with Answer "%s"', $question_id, $answer_id)] =
                    [
                        $question,
                        $answer,
                        $this->getExpectedScore($question_id, $answer_id)
                    ];
            }
        }

        return $mapping;
    }

    /**
     * @param QuestionDto $question
     */
    public function testQuestionCreation()
    {
        foreach ($this->getQuestions() as $question) {
            $created = $this->asq->question()->createQuestion($this->getTypeDefinition());
            self::$ids[] = sprintf('"%s"', $created->getId());
            $created->setData($question->getData());
            $created->setAnswerOptions($question->getAnswerOptions());
            $created->setPlayConfiguration($question->getPlayConfiguration());
            $this->asq->question()->saveQuestion($created);

            $loaded_created = $this->asq->question()->getQuestionByQuestionId($created->getId());

            $this->assertTrue($question->getData()->equals($loaded_created->getData()));
            if (!is_null($question->getAnswerOptions())) {
                $this->assertTrue($question->getAnswerOptions()->equals($loaded_created->getAnswerOptions()));
            }
            $this->assertTrue($question->getPlayConfiguration()->equals($loaded_created->getPlayConfiguration()));
        }
    }

    /**
     * @depends testQuestionCreation
     * @dataProvider questionAnswerProvider
     *
     * @param QuestionDto $question
     * @param AbstractValueObject $answer
     * @param float $expected_score
     */
    public function testComponentRendering(QuestionDto $question, AbstractValueObject $answer, float $expected_score)
    {
        global $DIC;

        $q = new QuestionComponent($question, $DIC->ui(), $DIC->language());
        $q->setAnswer($answer);
        $output = $q->renderHtml();

        $this->assertTrue(strlen($output) > 0);
    }

    /**
     * @depends testQuestionCreation
     * @dataProvider questionAnswerProvider
     *
     * @param QuestionDto $question
     * @param AbstractValueObject $answer
     * @param float $expected_score
     */
    public function testAnswers(QuestionDto $question, AbstractValueObject $answer, float $expected_score)
    {
        $this->assertEquals($expected_score, $this->asq->answer()->getScore($question, $answer));
    }

    public static function TearDownAfterClass() : void
    {
        global $DIC;

        if (count(self::$ids) > 0) {
            $DIC->database()->manipulate(sprintf('DELETE FROM asq_qst_event_store WHERE aggregate_id in (%s);', implode(', ', self::$ids)));
        }
    }
}
