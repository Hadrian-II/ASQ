<?php
declare(strict_types=1);

namespace srag\asq\Questions\Cloze\Storage;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\Setup\AbstractQuestionDBSetup;

/**
 * Class SetupCloze
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class SetupCloze extends AbstractQuestionDBSetup
{
    const TABLENAME_CLOZE_CONFIGURATION = 'rqes_cloze_config';
    const TABLENAME_CLOZE_GAP = 'rqes_cloze_gap';
    const TABLENAME_CLOZE_GAP_ITEM = 'rqes_cloze_item';

    public function setup() : void
    {
        $this->db->createTable(
            self::TABLENAME_CLOZE_CONFIGURATION,
            [
                'cloze_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'event_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'text' => ['type' => 'text']
            ]
            );
        $this->db->addPrimaryKey(self::TABLENAME_CLOZE_CONFIGURATION,['cloze_id']);
        $this->db->createSequence(self::TABLENAME_CLOZE_CONFIGURATION);
        $this->db->addIndex(self::TABLENAME_CLOZE_CONFIGURATION, ['cloze_id'], 'i1');
        $this->db->addIndex(self::TABLENAME_CLOZE_CONFIGURATION, ['event_id'], 'i2');

        $this->db->createTable(
            self::TABLENAME_CLOZE_GAP,
            [
                'gap_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'cloze_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'gap_type' => ['type' => 'text','length' => 16,'notnull' => true],
                'field_length' => ['type' => 'integer', 'length' => 4],
                'text_match_method' => ['type' => 'integer', 'length' => 4],
                'value' => ['type' => 'float'],
                'upper' => ['type' => 'float'],
                'lower' => ['type' => 'float'],
                'gap_points' => ['type' => 'float']
            ]
            );
        $this->db->addPrimaryKey(self::TABLENAME_CLOZE_GAP,['gap_id']);
        $this->db->createSequence(self::TABLENAME_CLOZE_GAP);
        $this->db->addIndex(self::TABLENAME_CLOZE_GAP, ['gap_id'], 'i1');
        $this->db->addIndex(self::TABLENAME_CLOZE_GAP, ['cloze_id'], 'i2');

        $this->db->createTable(
            self::TABLENAME_CLOZE_GAP_ITEM,
            [
                'gap_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'item_text' => ['type' => 'text', 'length' => 128],
                'item_points' => ['type' => 'float']
            ]
            );
        $this->db->addIndex(self::TABLENAME_CLOZE_GAP_ITEM, ['gap_id'], 'i2');
    }

    public function drop() : void
    {
        $this->db->dropTable(self::TABLENAME_CLOZE_CONFIGURATION, false);
        $this->db->dropTable(self::TABLENAME_CLOZE_GAP, false);
        $this->db->dropTable(self::TABLENAME_CLOZE_GAP_ITEM, false);
    }
}