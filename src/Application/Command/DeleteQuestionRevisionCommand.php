<?php
declare(strict_types=1);

namespace srag\asq\Application\Command;

use ILIAS\Data\UUID\Uuid;
use Fluxlabs\CQRS\Command\AbstractCommand;

/**
 * Class DeleteQuestionRevisionCommand
 *
 * Command to create new question Revision
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2021 ILIAS open source
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class DeleteQuestionRevisionCommand extends AbstractCommand
{
    private Uuid $question_id;

    private string $revision_name;

    public function __construct(Uuid $question_id, string $revision_name)
    {
        parent::__construct();
        $this->question_id = $question_id;
        $this->revision_name = $revision_name;
    }

    public function getQuestionId() : Uuid
    {
        return $this->question_id;
    }

    public function getRevisionName() : string
    {
        return $this->revision_name;
    }
}
