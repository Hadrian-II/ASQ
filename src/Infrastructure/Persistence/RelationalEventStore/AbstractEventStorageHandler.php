<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\RelationalEventStore;

use ILIAS\Data\UUID\Factory;
use ilDBInterface;

/**
 * Abstract Class AbstractEventStorageHandler
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
abstract class AbstractEventStorageHandler implements IEventStorageHandler
{
    /**
     * @var ilDBInterface
     */
    protected $db;

    /**
     * @var Factory
     */
    protected $factory;

    public function __construct(ilDBInterface $db)
    {
        $this->db = $db;

        $this->factory = new Factory();
    }
}