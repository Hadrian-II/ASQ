<?php
declare(strict_types=1);

namespace srag\asq\Application\Command;

use ILIAS\Data\UUID\Uuid;
use srag\CQRS\Command\AbstractCommand;

/**
 * Class DeleteQuestionRevisionCommand
 *
 * Command to create new question Revision
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class DeleteQuestionRevisionCommand extends AbstractCommand
{

    /**
     * @var Uuid
     */
    private $question_id;

    /**
     * @var string
     */
    private $revision_name;

    /**
     * @param Uuid $question_id
     * @param string $revision_name
     * @param int $issuer_id
     */
    public function __construct(Uuid $question_id, string $revision_name, int $issuer_id)
    {
        parent::__construct($issuer_id);
        $this->question_id = $question_id;
        $this->revision_name = $revision_name;
    }


    /**
     * @return Uuid
     */
    public function getQuestionId() : Uuid
    {
        return $this->question_id;
    }

    /**
     * @return string
     */
    public function getRevisionName() : string
    {
        return $this->revision_name;
    }
}
