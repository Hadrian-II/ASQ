<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component\Scoring;

use ILIAS\UI\Component\Component;
use ilLanguage;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Infrastructure\Helpers\PathHelper;

/**
 * Class ScoringComponent
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Martin Studer <ms@studer-raimann.ch>
 */
class ScoringComponent implements Component
{
    use PathHelper;

    /**
     * @var AbstractValueObject
     */
    private $answer;

    /**
     * @var QuestionDto
     */
    private $question;


    /**
     * @param QuestionDto $question_dto
     * @param AbstractValueObject $answer
     * @param ilLanguage $language
     */
    public function __construct(QuestionDto $question_dto, AbstractValueObject $answer)
    {
        $this->question = $question_dto;
        $this->answer = $answer;
    }

    /**
     * @return QuestionDto
     */
    public function getQuestion() : QuestionDto
    {
        return $this->question;
    }

    /**
     * @return AbstractValueObject
     */
    public function getAnswer() : AbstractValueObject
    {
        return $this->answer;
    }

    /**
     * {@inheritDoc}
     * @see \ILIAS\UI\Component\Component::getCanonicalName()
     */
    public function getCanonicalName()
    {
        return ScoringComponent::class;
    }
}
