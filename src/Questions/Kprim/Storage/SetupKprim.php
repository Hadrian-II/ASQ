<?php
declare(strict_types=1);

namespace srag\asq\Questions\Kprim\Storage;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\Setup\AbstractQuestionDBSetup;

/**
 * Class SetupKprim
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class SetupKprim extends AbstractQuestionDBSetup
{
    const TABLENAME_KPRIM_CONFIGURATION = 'rqes_kprim_config';
    const TABLENAME_KPRIM_ANSWER = 'rqes_kprim_answer';

    public function setup() : void
    {
        $this->db->createTable(
            self::TABLENAME_KPRIM_CONFIGURATION,
            [
                'config_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'event_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'shuffle' => ['type' => 'integer', 'length' => 1],
                'thumbnail_size' => ['type' => 'integer', 'length' => 4],
                'label_true' => ['type' => 'text', 'length' => 32],
                'label_false' => ['type' => 'text', 'length' => 32],
                'points' => ['type' => 'float'],
                'half_points_at' => ['type' => 'integer', 'length' => 4]
            ]
            );
        $this->db->addPrimaryKey(self::TABLENAME_KPRIM_CONFIGURATION,['config_id']);
        $this->db->createSequence(self::TABLENAME_KPRIM_CONFIGURATION);
        $this->db->addIndex(self::TABLENAME_KPRIM_CONFIGURATION, ['config_id'], 'i1');
        $this->db->addIndex(self::TABLENAME_KPRIM_CONFIGURATION, ['event_id'], 'i2');

        $this->db->createTable(
            self::TABLENAME_KPRIM_ANSWER,
            [
                'answer_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'event_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'correct_answer' => ['type' => 'integer','length' => 1],
                'text' => ['type' => 'text'],
                'image' => ['type' => 'text', 'length' => 128]
            ]
        );
        $this->db->addPrimaryKey(self::TABLENAME_KPRIM_ANSWER,['answer_id']);
        $this->db->createSequence(self::TABLENAME_KPRIM_ANSWER);
        $this->db->addIndex(self::TABLENAME_KPRIM_ANSWER, ['answer_id'], 'i1');
        $this->db->addIndex(self::TABLENAME_KPRIM_ANSWER, ['event_id'], 'i2');
    }

    public function drop() : void
    {
        $this->db->dropTable(self::TABLENAME_KPRIM_CONFIGURATION, false);
        $this->db->dropTable(self::TABLENAME_KPRIM_ANSWER, false);
    }
}