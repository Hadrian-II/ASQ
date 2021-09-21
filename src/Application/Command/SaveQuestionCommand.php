<?php
declare(strict_types=1);

namespace srag\asq\Application\Command;

use Fluxlabs\CQRS\Command\AbstractCommand;
use srag\asq\Domain\Model\Question;

/**
 * Class SaveQuestionCommand
 *
 * Save Question command
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class SaveQuestionCommand extends AbstractCommand
{
    private Question $question;

    public function __construct(Question $question, int $issuing_user_id)
    {
        parent::__construct($issuing_user_id);
        $this->question = $question;
    }

    public function GetQuestion() : Question
    {
        return $this->question;
    }
}
