<?php
declare(strict_types=1);

namespace srag\asq\Questions\Formula\Storage;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\Setup\AbstractQuestionDBSetup;

/**
 * Class SetupFormula
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class SetupFormula extends AbstractQuestionDBSetup
{
    const TABLENAME_FORMULA_CONFIGURATION = 'rqes_formula_config';
    const TABLENAME_FORMULA_VARIABLE = 'rqes_formula_variable';
    const TABLENAME_FORMULA_ANSWER = 'rqes_formula_answer';

    public function setup() : void
    {
        $this->db->createTable(
            self::TABLENAME_FORMULA_CONFIGURATION,
            [
                'config_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'event_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'formula' => ['type' => 'text', 'length' => 128],
                'units' => ['type' => 'text', 'length' => 32],
                'precision' => ['type' => 'integer', 'length' => 4],
                'tolerance' => ['type' => 'float'],
                'result_type' => ['type' => 'integer', 'length' => 4]
            ]
            );
        $this->db->addPrimaryKey(self::TABLENAME_FORMULA_CONFIGURATION,['config_id']);
        $this->db->createSequence(self::TABLENAME_FORMULA_CONFIGURATION);
        $this->db->addIndex(self::TABLENAME_FORMULA_CONFIGURATION, ['config_id'], 'i1');
        $this->db->addIndex(self::TABLENAME_FORMULA_CONFIGURATION, ['event_id'], 'i2');

        $this->db->createTable(
            self::TABLENAME_FORMULA_VARIABLE,
            [
                'config_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'min' => ['type' => 'float'],
                'max' => ['type' => 'float'],
                'unit' => ['type' => 'text', 'length' => 16],
                'multiple_of' => ['type' => 'float']
            ]
            );
        $this->db->addIndex(self::TABLENAME_FORMULA_VARIABLE, ['config_id'], 'i1');

        $this->db->createTable(
            self::TABLENAME_FORMULA_ANSWER,
            [
                'answer_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'event_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'formula' => ['type' => 'text', 'length' => 128],
                'unit' => ['type' => 'text', 'length' => 16],
                'points' => ['type' => 'float'],
            ]
        );
        $this->db->addPrimaryKey(self::TABLENAME_FORMULA_ANSWER,['answer_id']);
        $this->db->createSequence(self::TABLENAME_FORMULA_ANSWER);
        $this->db->addIndex(self::TABLENAME_FORMULA_ANSWER, ['answer_id'], 'i1');
        $this->db->addIndex(self::TABLENAME_FORMULA_ANSWER, ['event_id'], 'i2');
    }

    public function drop() : void
    {
        $this->db->dropTable(self::TABLENAME_FORMULA_CONFIGURATION, false);
        $this->db->dropTable(self::TABLENAME_FORMULA_VARIABLE, false);
        $this->db->dropTable(self::TABLENAME_FORMULA_ANSWER, false);
    }
}