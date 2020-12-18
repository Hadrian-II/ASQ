<?php
declare(strict_types=1);

namespace srag\asq\Questions\Numeric\Storage;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\Setup\AbstractQuestionDBSetup;

/**
 * Class SetupNumeric
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class SetupNumeric extends AbstractQuestionDBSetup
{
    const TABLENAME_NUMERIC_CONFIGURATION = 'rqes_numeric_config';

    public function setup() : void
    {
        $this->db->createTable(
            self::TABLENAME_NUMERIC_CONFIGURATION,
            [
                'config_id' => ['type' => 'integer', 'length' => 4,'notnull' => true],
                'event_id' => ['type' => 'integer', 'length' => 4,'notnull' => true],
                'points' => ['type' => 'float'],
                'lower_bound' => ['type' => 'float'],
                'upper_bound' => ['type' => 'float'],
                'max_chars' => ['type' => 'integer', 'length' => 4],
            ]
            );
        $this->db->addPrimaryKey(self::TABLENAME_NUMERIC_CONFIGURATION,['config_id']);
        $this->db->createSequence(self::TABLENAME_NUMERIC_CONFIGURATION);
        $this->db->addIndex(self::TABLENAME_NUMERIC_CONFIGURATION, ['config_id'], 'i1');
        $this->db->addIndex(self::TABLENAME_NUMERIC_CONFIGURATION, ['event_id'], 'i2');
    }

    public function drop() : void
    {
        $this->db->dropTable(self::TABLENAME_NUMERIC_CONFIGURATION, false);
    }
}