<?php
declare(strict_types=1);

namespace srag\asq\Questions\FileUpload\Storage;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\Setup\AbstractQuestionDBSetup;

/**
 * Class SetupFileUpload
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class SetupFileUpload extends AbstractQuestionDBSetup
{
    const TABLENAME_FILEUPLOAD_CONFIGURATION = 'rqes_file_config';

    public function setup() : void
    {
        $this->db->createTable(
            self::TABLENAME_FILEUPLOAD_CONFIGURATION,
            [
                'config_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'event_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'max_size' => ['type' => 'integer', 'length' => 4],
                'allowed_extensions' => ['type' => 'text', 'length' => 32],
                'points' => ['type' => 'float'],
                'completed_by_sub' => ['type' => 'integer', 'length' => 1]
            ]
            );
        $this->db->addPrimaryKey(self::TABLENAME_FILEUPLOAD_CONFIGURATION,['config_id']);
        $this->db->createSequence(self::TABLENAME_FILEUPLOAD_CONFIGURATION);
        $this->db->addIndex(self::TABLENAME_FILEUPLOAD_CONFIGURATION, ['config_id'], 'i1');
        $this->db->addIndex(self::TABLENAME_FILEUPLOAD_CONFIGURATION, ['config_id'], 'i2');
    }

    public function drop() : void
    {
        $this->db->dropTable(self::TABLENAME_FILEUPLOAD_CONFIGURATION, false);
    }
}