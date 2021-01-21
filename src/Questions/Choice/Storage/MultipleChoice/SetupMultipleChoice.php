<?php
declare(strict_types=1);

namespace srag\asq\Questions\Choice\Storage\MultipleChoice;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\Setup\AbstractQuestionDBSetup;

/**
 * Class SetupMultiplechoice
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class SetupMultipleChoice extends AbstractQuestionDBSetup
{
    const TABLENAME_MULTIPLE_CHOICE_CONFIGURATION = 'rqes_choice_config';
    const TABLENAME_MULTIPLE_CHOICE_ANSWER = 'rqes_choice_answer';

    public function setup() : void
    {
        $this->db->createTable(
            self::TABLENAME_MULTIPLE_CHOICE_CONFIGURATION,
            [
                'choice_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'event_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'shuffle' => ['type' => 'integer', 'length' => 1],
                'max_answers' => ['type' => 'integer', 'length' => 4],
                'thumbnail' => ['type' => 'integer', 'length' => 4],
                'singleline' => ['type' => 'integer', 'length' => 1]
            ]
            );
        $this->db->addPrimaryKey(self::TABLENAME_MULTIPLE_CHOICE_CONFIGURATION,['choice_id']);
        $this->db->createSequence(self::TABLENAME_MULTIPLE_CHOICE_CONFIGURATION);
        $this->db->addIndex(self::TABLENAME_MULTIPLE_CHOICE_CONFIGURATION, ['choice_id'], 'i1');
        $this->db->addIndex(self::TABLENAME_MULTIPLE_CHOICE_CONFIGURATION, ['event_id'], 'i2');

        $this->db->createTable(
            self::TABLENAME_MULTIPLE_CHOICE_ANSWER,
            [
                'answer_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'event_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'points_selected' => ['type' => 'float'],
                'points_unselected' => ['type' => 'float'],
                'text' => ['type' => 'text'],
                'image' => ['type' => 'text', 'length' => 128]
            ]
            );
        $this->db->addPrimaryKey(self::TABLENAME_MULTIPLE_CHOICE_ANSWER,['answer_id']);
        $this->db->createSequence(self::TABLENAME_MULTIPLE_CHOICE_ANSWER);
        $this->db->addIndex(self::TABLENAME_MULTIPLE_CHOICE_ANSWER, ['answer_id'], 'i1');
        $this->db->addIndex(self::TABLENAME_MULTIPLE_CHOICE_ANSWER, ['event_id'], 'i2');
    }

    public function drop() : void
    {
        $this->db->dropTable(self::TABLENAME_MULTIPLE_CHOICE_CONFIGURATION, false);
        $this->db->dropTable(self::TABLENAME_MULTIPLE_CHOICE_ANSWER, false);
    }
}