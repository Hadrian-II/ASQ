<?php
declare(strict_types = 1);

namespace srag\asq\Application\Command;

use ILIAS\Data\UUID\Uuid;
use srag\CQRS\Command\AbstractCommand;
use srag\asq\Infrastructure\Persistence\QuestionType;

/**
 * Class CreateQuestionCommand
 *
 * Command to initiate Createion of new Question
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 * @package srag/asq
 * @author Adrian Lüthi <al@studer-raimann.ch>
 */
class CreateQuestionCommand extends AbstractCommand
{

    /**
     * @var Uuid
     */
    protected $question_uuid;

    /**
     * @var QuestionType
     */
    protected $question_type;

    /**
     * @param Uuid $question_uuid
     * @param QuestionType $question_type
     * @param int $initiating_user_id
     * @param int $container_id
     */
    public function __construct(
        Uuid $question_uuid,
        QuestionType $question_type,
        int $initiating_user_id
    ) {
        parent::__construct($initiating_user_id);
        $this->question_uuid = $question_uuid;
        $this->question_type = $question_type;
    }

    /**
     * @return Uuid
     */
    public function getQuestionUuid() : Uuid
    {
        return $this->question_uuid;
    }

    /**
     * @return QuestionType
     */
    public function getQuestionType() : QuestionType
    {
        return $this->question_type;
    }
}
