<?php
declare(strict_types=1);

namespace srag\asq\Questions\ErrorText\Storage;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\Setup\AbstractQuestionDBSetup;

/**
 * Class SetupErrorText
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class SetupErrorText extends AbstractQuestionDBSetup
{
    const TABLENAME_ERRORTEXT_CONFIGURATION = 'rqes_errtxt_config';
    const TABLENAME_ERRORTEXT_ANSWER = 'rqes_errtxt_answer';

    public function setup() : void
    {
        $this->db->createTable(
            self::TABLENAME_ERRORTEXT_CONFIGURATION,
            [
                'errtxt_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'event_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'errortext' => ['type' => 'text'],
                'txt_size' => ['type' => 'integer', 'length' => 4],
                'points_wrong' => ['type' => 'float']
            ]
            );
        $this->db->addPrimaryKey(self::TABLENAME_ERRORTEXT_CONFIGURATION,['errtxt_id']);
        $this->db->createSequence(self::TABLENAME_ERRORTEXT_CONFIGURATION);
        $this->db->addIndex(self::TABLENAME_ERRORTEXT_CONFIGURATION, ['errtxt_id'], 'i1');
        $this->db->addIndex(self::TABLENAME_ERRORTEXT_CONFIGURATION, ['event_id'], 'i2');

        $this->db->createTable(
            self::TABLENAME_ERRORTEXT_ANSWER,
            [
                'answer_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'event_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'wrong_index' => ['type' => 'integer', 'length' => 4],
                'wrong_length' => ['type' => 'integer', 'length' => 4],
                'correct_text' => ['type' => 'text', 'length' => 64],
                'points' => ['type' => 'float']
            ]
        );
        $this->db->addPrimaryKey(self::TABLENAME_ERRORTEXT_ANSWER,['answer_id']);
        $this->db->createSequence(self::TABLENAME_ERRORTEXT_ANSWER);
        $this->db->addIndex(self::TABLENAME_ERRORTEXT_ANSWER, ['answer_id'], 'i1');
        $this->db->addIndex(self::TABLENAME_ERRORTEXT_ANSWER, ['event_id'], 'i2');
    }

    public function drop() : void
    {
        $this->db->dropTable(self::TABLENAME_ERRORTEXT_CONFIGURATION, false);
        $this->db->dropTable(self::TABLENAME_ERRORTEXT_ANSWER, false);
    }
}