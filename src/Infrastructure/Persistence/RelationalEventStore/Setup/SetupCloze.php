<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\RelationalEventStore\Setup;

use ilDBInterface;

/**
 * Class SetupRQES
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class SetupRQES
{
    /**
     * @var ilDBInterface
     */
    private $db;

    /**
     * @param ilDBInterface $db
     */
    public function __construct(ilDBInterface $db)
    {
        $this->db = $db;
    }

    /**
     *
     */
    public function setup()
    {

    }

    public function drop()
    {

    }
}