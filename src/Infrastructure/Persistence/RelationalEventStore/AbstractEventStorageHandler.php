<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\RelationalEventStore;

use ILIAS\Data\UUID\Factory;
use ilDBInterface;
use srag\CQRS\Event\DomainEvent;
use srag\asq\UserInterface\Web\Form\InputHandlingTrait;

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
    use InputHandlingTrait;

    protected ilDBInterface $db;

    protected Factory $factory;

    public function __construct(ilDBInterface $db)
    {
        $this->db = $db;

        $this->factory = new Factory();
    }

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

    abstract function getQueryString() : string;

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

    abstract function createEvent(array $data, array $rows) : DomainEvent;
}