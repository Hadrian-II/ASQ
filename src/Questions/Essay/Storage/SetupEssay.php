<?php
declare(strict_types=1);

namespace srag\asq\Questions\Essay\Storage;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\Setup\AbstractQuestionDBSetup;

/**
 * Class SetupEssay
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class SetupEssay extends AbstractQuestionDBSetup
{
    const TABLENAME_ESSAY_CONFIGURATION = 'rqes_essay_config';
    const TABLENAME_ESSAY_ANSWER = 'rqes_essay_answer';

    public function setup() : void
    {
        $this->db->createTable(
            self::TABLENAME_ESSAY_CONFIGURATION,
            [
                'essay_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'event_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'matchmode' => ['type' => 'integer', 'length' => 4],
                'scoremode' => ['type' => 'integer', 'length' => 4],
                'points' => ['type' => 'float'],
                'max_length' => ['type' => 'integer', 'length' => 4]
            ]
            );
        $this->db->addPrimaryKey(self::TABLENAME_ESSAY_CONFIGURATION,['essay_id']);
        $this->db->createSequence(self::TABLENAME_ESSAY_CONFIGURATION);
        $this->db->addIndex(self::TABLENAME_ESSAY_CONFIGURATION, ['essay_id'], 'i1');
        $this->db->addIndex(self::TABLENAME_ESSAY_CONFIGURATION, ['event_id'], 'i2');

        $this->db->createTable(
            self::TABLENAME_ESSAY_ANSWER,
            [
                'answer_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'event_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'text' => ['type' => 'text', 'length' => 128],
                'points' => ['type' => 'float'],
            ]
        );
        $this->db->addPrimaryKey(self::TABLENAME_ESSAY_ANSWER,['answer_id']);
        $this->db->createSequence(self::TABLENAME_ESSAY_ANSWER);
        $this->db->addIndex(self::TABLENAME_ESSAY_ANSWER, ['answer_id'], 'i1');
        $this->db->addIndex(self::TABLENAME_ESSAY_ANSWER, ['event_id'], 'i2');
    }

    public function drop() : void
    {
        $this->db->dropTable(self::TABLENAME_ESSAY_CONFIGURATION, false);
        $this->db->dropTable(self::TABLENAME_ESSAY_ANSWER, false);
    }
}