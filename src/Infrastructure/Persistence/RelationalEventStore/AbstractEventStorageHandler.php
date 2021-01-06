<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\RelationalEventStore;

use ILIAS\Data\UUID\Factory;
use ilDBInterface;
use srag\CQRS\Event\DomainEvent;

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

    /**
     * @param array $data
     * @return DomainEvent[]
     */
    public function loadEvents(array $data) : array
    {
        $res = $this->db->query(
            sprintf(
                $this->getQueryString(),
                $this->getEventIds($data)
                )
            );

        $rows = $this->db->fetchAll($res);

        return $this->mapDataAndRows(
            $data,
            $rows);
    }

    /**
     * @return string
     */
    abstract function getQueryString() : string;

    /**
     * @param array $data
     * @return string
     */
    protected function getEventIds(array $data) : string
    {
        return implode(
            ', ',
            array_map(
                function($item)
                {
                    return $item['id'];
                },
                $data
            )
        );
    }

    /**
     * @param array $data
     * @param array $rows
     * @return array
     */
    protected function mapDataAndRows(array $data, array $rows) : array
    {
        return array_map(
            function($current_data) use ($rows) {
                $current_rows = array_values(array_filter(
                    $rows,
                    function($row) use ($current_data) {
                        return $current_data['id'] === $row['event_id'];
                    }
                ));

                return $this->createEvent($current_data, $current_rows);
            },
            $data
        );
    }

    /**
     * @param array $data
     * @param array $rows
     * @return DomainEvent
     */
    abstract function createEvent(array $data, array $rows) : DomainEvent;
}