<?php
declare(strict_types=1);

namespace srag\asq\Questions\Matching\Storage;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\Setup\AbstractQuestionDBSetup;

/**
 * Class SetupMatching
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class SetupMatching extends AbstractQuestionDBSetup
{
    const TABLENAME_MATCHING_CONFIGURATION = 'rqes_matching_config';
    const TABLENAME_MATCHING_ITEM = 'rqes_matching_item';
    const TABLENAME_MATCHING_MAPPING = 'rqes_matching_mapping';

    public function setup() : void
    {
        $this->db->createTable(
            self::TABLENAME_MATCHING_CONFIGURATION,
            [
                'config_id' => ['type' => 'integer', 'length' => 4,'notnull' => true],
                'event_id' => ['type' => 'integer', 'length' => 4,'notnull' => true],
                'shuffle' => ['type' => 'integer', 'length' => 4],
                'thumbnail_size' => ['type' => 'integer', 'length' => 4],
                'matching_mode' => ['type' => 'integer', 'length' => 4],
                'wrong_deduction' => ['type' => 'float']
            ]
            );
        $this->db->addPrimaryKey(self::TABLENAME_MATCHING_CONFIGURATION,['config_id']);
        $this->db->createSequence(self::TABLENAME_MATCHING_CONFIGURATION);
        $this->db->addIndex(self::TABLENAME_MATCHING_CONFIGURATION, ['config_id'], 'i1');
        $this->db->addIndex(self::TABLENAME_MATCHING_CONFIGURATION, ['event_id'], 'i2');

        $this->db->createTable(
            self::TABLENAME_MATCHING_ITEM,
            [
                'item_id' => ['type' => 'integer', 'length' => 4,'notnull' => true],
                'config_id' => ['type' => 'integer', 'length' => 4,'notnull' => true],
                'type' => ['type' => 'integer', 'length' => 1],
                'id' => ['type' => 'text', 'length' => 16],
                'text' => ['type' => 'text'],
                'image' => ['type' => 'text', 'length' => 128]
            ]
        );
        $this->db->addPrimaryKey(self::TABLENAME_MATCHING_ITEM,['item_id']);
        $this->db->createSequence(self::TABLENAME_MATCHING_ITEM);
        $this->db->addIndex(self::TABLENAME_MATCHING_ITEM, ['item_id'], 'i1');
        $this->db->addIndex(self::TABLENAME_MATCHING_ITEM, ['config_id'], 'i2');

        $this->db->createTable(
            self::TABLENAME_MATCHING_MAPPING,
            [
                'config_id' => ['type' => 'integer', 'length' => 4, 'notnull' => true],
                'definition_id' => ['type' => 'text', 'length' => 16],
                'term_id' => ['type' => 'text', 'length' => 16],
                'points' => ['type' => 'float']
            ]
        );
        $this->db->addIndex(self::TABLENAME_MATCHING_MAPPING, ['config_id'], 'i1');
    }

    public function drop() : void
    {
        $this->db->dropTable(self::TABLENAME_MATCHING_CONFIGURATION, false);
        $this->db->dropTable(self::TABLENAME_MATCHING_ITEM, false);
        $this->db->dropTable(self::TABLENAME_MATCHING_MAPPING, false);
    }
}