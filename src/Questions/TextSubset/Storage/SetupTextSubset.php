<?php
declare(strict_types=1);

namespace srag\asq\Questions\TextSubset\Storage;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\Setup\AbstractQuestionDBSetup;

/**
 * Class SetupTextSubset
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class SetupTextSubset extends AbstractQuestionDBSetup
{
    const TABLENAME_TEXT_SUBSET_CONFIGURATION = 'rqes_subset_config';
    const TABLENAME_TEXT_SUBSET_ANSWER = 'rqes_subset_answer';

    public function setup() : void
    {
        $this->db->createTable(
            self::TABLENAME_TEXT_SUBSET_CONFIGURATION,
            [
                'config_id' => ['type' => 'integer', 'length' => 4,'notnull' => true],
                'event_id' => ['type' => 'integer', 'length' => 4,'notnull' => true],
                'answers' => ['type' => 'integer', 'length' => 4],
                'matching' => ['type' => 'integer', 'length' => 4]
            ]
            );
        $this->db->addPrimaryKey(self::TABLENAME_TEXT_SUBSET_CONFIGURATION,['config_id']);
        $this->db->createSequence(self::TABLENAME_TEXT_SUBSET_CONFIGURATION);
        $this->db->addIndex(self::TABLENAME_TEXT_SUBSET_CONFIGURATION, ['config_id'], 'i1');
        $this->db->addIndex(self::TABLENAME_TEXT_SUBSET_CONFIGURATION, ['event_id'], 'i2');

        $this->db->createTable(
            self::TABLENAME_TEXT_SUBSET_ANSWER,
            [
                'answer_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'event_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'text' => ['type' => 'text', 'length' => 32],
                'points' => ['type' => 'float']
            ]
            );
        $this->db->addPrimaryKey(self::TABLENAME_TEXT_SUBSET_ANSWER,['answer_id']);
        $this->db->createSequence(self::TABLENAME_TEXT_SUBSET_ANSWER);
        $this->db->addIndex(self::TABLENAME_TEXT_SUBSET_ANSWER, ['answer_id'], 'i1');
        $this->db->addIndex(self::TABLENAME_TEXT_SUBSET_ANSWER, ['event_id'], 'i2');
    }

    public function drop() : void
    {
        $this->db->dropTable(self::TABLENAME_TEXT_SUBSET_CONFIGURATION, false);
        $this->db->dropTable(self::TABLENAME_TEXT_SUBSET_ANSWER, false);
    }
}