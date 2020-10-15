<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\RelationalEventStore\Setup;

use ilDBInterface;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\RelationalQuestionEventStore;

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
    const QUESTION_TYPES = [

    ];

    /**
     * @var ilDBInterface
     */
    private $db;

    public function __construct(ilDBInterface $db)
    {
        $this->db = $db;
    }

    public function setup() : void
    {
        $this->setupBase();
        $this->setupQuestionData();
        $this->setupHint();

        foreach (self::QUESTION_TYPES as $type) {
            $type_setup = new $type($this->db);
            $type_setup->setup();
        }
    }

    private function setupBase() : void
    {
        $this->db->createTable(
            RelationalQuestionEventStore::TABLE_NAME,
            [
                'id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'event_id' => ['type' => 'text','length' => 36,'notnull' => true],
                'event_version' => ['type' => 'integer','length' => 4,'notnull' => true],
                'question_id' => ['type' => 'text','length' => 36,'notnull' => true],
                'event_name' => ['type' => 'text','length' => 36,'notnull' => true],
                'occurred_on' => ['type' => 'integer', 'length' => 4, 'notnull' => true],
                'initiating_user_id' => ['type' => 'integer', 'length' => 4,'notnull' => true]
            ]
        );
        $this->db->addPrimaryKey(RelationalQuestionEventStore::TABLE_NAME,['id']);
        $this->db->createSequence(RelationalQuestionEventStore::TABLE_NAME);
        $this->db->addIndex(RelationalQuestionEventStore::TABLE_NAME, ['id'], 'i1');
        $this->db->addIndex(RelationalQuestionEventStore::TABLE_NAME, ['question_id'], 'i2');

        $this->db->createTable(
            RelationalQuestionEventStore::TABLE_NAME_QUESTION_INDEX,
            [
                'question_id' => ['type' => 'text','length' => 36,'notnull' => true],
                'question_type' => ['type' => 'text','length' => 36,'notnull' => true]
            ]
        );
        $this->db->addIndex(RelationalQuestionEventStore::TABLE_NAME_QUESTION_INDEX, ['question_id'], 'i1');
    }

    private function setupQuestionData() : void
    {
        $this->db->createTable(
            RelationalQuestionEventStore::TABLE_NAME_QUESTION_DATA,
            [
                'id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'event_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'title' => ['type' => 'text','length' => 64],
                'text' => ['type' => 'text'],
                'author' => ['type' => 'text','length' => 64],
                'description' => ['type' => 'text'],
                'working_time' => ['type' => 'integer','length' => 4],
                'lifecycle' => ['type' => 'integer','length' => 4]
            ]
        );
        $this->db->addPrimaryKey(RelationalQuestionEventStore::TABLE_NAME_QUESTION_DATA,['id']);
        $this->db->createSequence(RelationalQuestionEventStore::TABLE_NAME_QUESTION_DATA);
        $this->db->addIndex(RelationalQuestionEventStore::TABLE_NAME_QUESTION_DATA, ['id'], 'i1');
        $this->db->addIndex(RelationalQuestionEventStore::TABLE_NAME_QUESTION_DATA, ['event_id'], 'i2');
    }

    private function setupHint(): void
    {
        $this->db->createTable(
            RelationalQuestionEventStore::TABLE_NAME_QUESTION_HINT,
            [
                'id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'event_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'hint_id' => ['type' => 'text','length' => 64],
                'content' => ['type' => 'text'],
                'deduction' => ['type' => 'float']
            ]
            );
        $this->db->addPrimaryKey(RelationalQuestionEventStore::TABLE_NAME_QUESTION_HINT,['id']);
        $this->db->createSequence(RelationalQuestionEventStore::TABLE_NAME_QUESTION_HINT);
        $this->db->addIndex(RelationalQuestionEventStore::TABLE_NAME_QUESTION_HINT, ['id'], 'i1');
        $this->db->addIndex(RelationalQuestionEventStore::TABLE_NAME_QUESTION_HINT, ['event_id'], 'i2');
    }
}