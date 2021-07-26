<?php
declare(strict_types = 1);

namespace srag\asq\Application\Command;

use ILIAS\Data\UUID\Uuid;
use srag\CQRS\Command\AbstractCommand;
use srag\asq\Infrastructure\Persistence\QuestionType;

/**
 * Class CreateQuestionCommand
 *
 * Command to initiate Creation of new Question
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class CreateQuestionCommand extends AbstractCommand
{
    protected Uuid $question_uuid;

    protected QuestionType $question_type;

    public function __construct(
        Uuid $question_uuid,
        QuestionType $question_type,
        int $initiating_user_id
    ) {
        parent::__construct($initiating_user_id);
        $this->question_uuid = $question_uuid;
        $this->question_type = $question_type;
    }

    public function getQuestionUuid() : Uuid
    {
        return $this->question_uuid;
    }

    public function getQuestionType() : QuestionType
    {
        return $this->question_type;
    }
}
