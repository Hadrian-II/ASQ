<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\RelationalEventStore\Setup;

use ilDBInterface;

/**
 * Abstract Class AbstractQuestionDBSetup
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
abstract class AbstractQuestionDBSetup implements IQuestionDBSetup
{
    /**
     * @var ilDBInterface
     */
    protected $db;


    public function __construct(ilDBInterface $db)
    {
        $this->db = $db;
    }
}