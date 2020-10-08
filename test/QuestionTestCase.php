<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace ILIAS\AssessmentQuestion\Test;

use ILIAS\Data\UUID\Factory;
use ILIAS\UI\Implementation\DefaultRenderer;
use PHPUnit\Framework\TestCase;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\QuestionData;
use srag\asq\Domain\Model\Answer\Option\AnswerOptions;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Infrastructure\Persistence\QuestionType;
use srag\asq\Application\Service\ASQServices;
use srag\asq\UserInterface\Web\Component\Renderer;
use Exception;
use srag\asq\Domain\Model\Scoring\AbstractScoring;
use srag\asq\Application\Exception\AsqException;

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
    /**
     * @var ASQServices
     */
    protected static $asq;

    /**
     * @return array
     */
    abstract public function getQuestions() : array;

    /**
     * @return array
     */
    abstract public function getAnswers() : array;

    /**
     * @return array
     */
    abstract public function getMaxScores() : array;

    /**
     * @return array
     */
    abstract public function getExpectedScores() : array;

    /**
     * @return QuestionType
     */
    abstract public function getTypeDefinition() : QuestionType;

    public static function setUpBeforeClass() : void
    {
        global $ASQDIC;

        self::$asq = $ASQDIC->asq();
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
        $question->setId($factory->uuid4());
        $question->setData($data);
        $question->setPlayConfiguration($play);
        $question->setAnswerOptions($options);
        $question->setType($this->getTypeDefinition());
        return $question;
    }

    /**
     * @return array
     */
    public function questionMaxScoreProvider() : array
    {
        $max_scores = $this->getMaxScores();

        $mapping = [];

        foreach ($this->getQuestions() as $question_id => $question) {
            $mapping[sprintf('Question "%s"', $question_id)] =
            [
                $question,
                $max_scores[$question_id]
            ];
        }

        return $mapping;
    }

    /**
     * @return array
     */
    public function questionAnswerProvider() : array
    {
        $expected_scores = $this->getExpectedScores();

        $mapping = [];

        foreach ($this->getQuestions() as $question_id => $question) {
            foreach ($this->getAnswers() as $answer_id => $answer) {
                $mapping[sprintf('Question "%s" with Answer "%s"', $question_id, $answer_id)] =
                    [
                        $question,
                        $answer,
                        $expected_scores[$question_id][$answer_id]
                    ];
            }
        }

        return $mapping;
    }

    /**
     * @dataProvider questionAnswerProvider
     *
     * @param QuestionDto $question
     * @param AbstractValueObject $answer
     * @param mixed $expected_score
     */
    public function testComponentRendering(QuestionDto $question, AbstractValueObject $answer, $expected_score)
    {
        global $DIC;

        $q = self::$asq->ui()->getQuestionComponent($question);
        $q = $q->withAnswer($answer);

        $renderer = new Renderer(
            $DIC['ui.factory'],
            $DIC['ui.template_factory'],
            $DIC['lng'],
            $DIC['ui.javascript_binding'],
            $DIC['refinery']);

        $default_renderer = new class() extends DefaultRenderer {
            public function __construct() {}
        };

        $output = $renderer->render($q, $default_renderer);

        $this->assertTrue(strlen($output) > 0);
    }

    /**
     * @dataProvider questionAnswerProvider
     *
     * @param QuestionDto $question
     * @param AbstractValueObject $answer
     * @param mixed $expected_score
     */
    public function testAnswers(QuestionDto $question, AbstractValueObject $answer, $expected_score)
    {
        if ($expected_score instanceof Exception) {
            $this->expectException(get_class($expected_score));
            $this->expectExceptionMessage($expected_score->getMessage());
            self::$asq->answer()->getScore($question, $answer);
        } else {
            $this->assertEquals($expected_score, self::$asq->answer()->getScore($question, $answer));
        }
    }

    /**
     * @dataProvider questionMaxScoreProvider
     *
     * @param QuestionDto $question
     * @param float $expected_score
     */
    public function testMaxScore(QuestionDto $question, float $expected_score)
    {
        $this->assertEquals($expected_score, self::$asq->answer()->getMaxScore($question));
    }

    /**
     * @dataProvider questionMaxScoreProvider
     *
     * @param QuestionDto $question
     * @param float $expected_score
     */
    public function testBestAnswer(QuestionDto $question, float $expected_score)
    {
        try {
            $best_answer = self::$asq->answer()->getBestAnswer($question);
            $score = self::$asq->answer()->getScore($question, $best_answer);
            $this->assertEquals($expected_score, $score);
        }
        catch (AsqException $e) {
            $this->assertEquals(AbstractScoring::BEST_ANSWER_CREATION_IMPOSSIBLE_ERROR, $e->getMessage());
        }
    }
}
