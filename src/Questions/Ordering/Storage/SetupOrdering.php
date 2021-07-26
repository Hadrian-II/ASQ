<?php
declare(strict_types=1);

namespace srag\asq\Questions\Ordering\Storage;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\Setup\AbstractQuestionDBSetup;

/**
 * Class SetupOrdering
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class SetupOrdering extends AbstractQuestionDBSetup
{
    const TABLENAME_ORDERING_CONFIGURATION = 'rqes_ordering_config';
    const TABLENAME_ORDERING_ANSWER = 'rqes_ordering_answer';

    public function setup() : void
    {
        $this->db->createTable(
            self::TABLENAME_ORDERING_CONFIGURATION,
            [
                'config_id' => ['type' => 'integer', 'length' => 4,'notnull' => true],
                'event_id' => ['type' => 'integer', 'length' => 4,'notnull' => true],
                'text' => ['type' => 'text'],
                'is_vertical' => ['type' => 'integer', 'length' => 1],
                'points' => ['type' => 'float']
            ]
            );
        $this->db->addPrimaryKey(self::TABLENAME_ORDERING_CONFIGURATION,['config_id']);
        $this->db->createSequence(self::TABLENAME_ORDERING_CONFIGURATION);
        $this->db->addIndex(self::TABLENAME_ORDERING_CONFIGURATION, ['config_id'], 'i1');
        $this->db->addIndex(self::TABLENAME_ORDERING_CONFIGURATION, ['event_id'], 'i2');

        $this->db->createTable(
            self::TABLENAME_ORDERING_ANSWER,
            [
                'answer_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'event_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'text' => ['type' => 'text', 'length' => 64],
                'image' => ['type' => 'text', 'length' => 128]
            ]
            );
        $this->db->addPrimaryKey(self::TABLENAME_ORDERING_ANSWER,['answer_id']);
        $this->db->createSequence(self::TABLENAME_ORDERING_ANSWER);
        $this->db->addIndex(self::TABLENAME_ORDERING_ANSWER, ['answer_id'], 'i1');
        $this->db->addIndex(self::TABLENAME_ORDERING_ANSWER, ['event_id'], 'i2');
    }

    public function drop() : void
    {
        $this->db->dropTable(self::TABLENAME_ORDERING_CONFIGURATION, false);
        $this->db->dropTable(self::TABLENAME_ORDERING_ANSWER, false);
    }
}