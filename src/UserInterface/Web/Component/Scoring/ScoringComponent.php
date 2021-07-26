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

    private AbstractValueObject $answer;

    private QuestionDto $question;

    public function __construct(QuestionDto $question_dto, AbstractValueObject $answer)
    {
        $this->question = $question_dto;
        $this->answer = $answer;
    }

    public function getQuestion() : QuestionDto
    {
        return $this->question;
    }

    public function getAnswer() : AbstractValueObject
    {
        return $this->answer;
    }

    public function getCanonicalName() : string
    {
        return ScoringComponent::class;
    }
}
