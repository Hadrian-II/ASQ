<?php
declare(strict_types=1);

namespace srag\asq\Questions\Choice\Storage\ImageMap;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\Setup\AbstractQuestionDBSetup;

/**
 * Class SetupImageMap
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class SetupImageMap extends AbstractQuestionDBSetup
{
    const TABLENAME_IMAGEMAP_CONFIGURATION = 'rqes_image_config';
    const TABLENAME_IMAGEMAP_ANSWER = 'rqes_image_answer';

    public function setup() : void
    {
        $this->db->createTable(
            self::TABLENAME_IMAGEMAP_CONFIGURATION,
            [
                'image_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'event_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'image' => ['type' => 'text', 'length' => 128],
                'is_multi' => ['type' => 'integer', 'length' => 1],
                'max_answers' => ['type' => 'integer', 'length' => 4]
            ]
            );
        $this->db->addPrimaryKey(self::TABLENAME_IMAGEMAP_CONFIGURATION,['image_id']);
        $this->db->createSequence(self::TABLENAME_IMAGEMAP_CONFIGURATION);
        $this->db->addIndex(self::TABLENAME_IMAGEMAP_CONFIGURATION, ['image_id'], 'i1');
        $this->db->addIndex(self::TABLENAME_IMAGEMAP_CONFIGURATION, ['event_id'], 'i2');

        $this->db->createTable(
            self::TABLENAME_IMAGEMAP_ANSWER,
            [
                'answer_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'event_id' => ['type' => 'integer','length' => 4,'notnull' => true],
                'points_selected' => ['type' => 'float'],
                'points_unselected' => ['type' => 'float'],
                'tooltip' => ['type' => 'text', 'length' => 64],
                'type' => ['type' => 'integer', 'length' => 4],
                'coordinates' => ['type' => 'text', 'length' => 128]
            ]
            );
        $this->db->addPrimaryKey(self::TABLENAME_IMAGEMAP_ANSWER,['answer_id']);
        $this->db->createSequence(self::TABLENAME_IMAGEMAP_ANSWER);
        $this->db->addIndex(self::TABLENAME_IMAGEMAP_ANSWER, ['answer_id'], 'i1');
        $this->db->addIndex(self::TABLENAME_IMAGEMAP_ANSWER, ['event_id'], 'i2');
    }

    public function drop() : void
    {
        $this->db->dropTable(self::TABLENAME_IMAGEMAP_CONFIGURATION, false);
        $this->db->dropTable(self::TABLENAME_IMAGEMAP_ANSWER, false);
    }
}