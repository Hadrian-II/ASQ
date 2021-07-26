<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence;

use ActiveRecord;
use srag\CQRS\Aggregate\AbstractValueObject;
use ILIAS\Data\UUID\Factory;

/**
 * Class SimpleStoredAnswer
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class SimpleStoredAnswer extends ActiveRecord
{
    const STORAGE_NAME = "asq_stored_answer";

    /**
     * @var int
     *
     * @con_is_primary true
     * @con_is_unique  true
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     8
     * @con_sequence   true
     */
    protected $id;
    /**
     * @var string
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_length     40
     * @con_index      true
     * @con_is_notnull true
     */
    protected $uuid;
    /**
     * @var int
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_is_notnull true
     */
    protected $version;

    /**
     * @var string
     *
     * @con_has_field  true
     * @con_fieldtype  clob
     * @con_is_notnull true
     */
    protected $answer;

    public static function createNew(AbstractValueObject $answer, ?string $uuid = null) : SimpleStoredAnswer
    {
        $object = new SimpleStoredAnswer();

        if (is_null($uuid)) {
            $uuid_factory = new Factory();
            $uuid = $uuid_factory->uuid4AsString();
        }

        $object->uuid = $uuid;

        $history = SimpleStoredAnswer::where(['uuid' => $object->uuid])->get();

        if (count($history) > 0) {
            $object->version = array_reduce($history, function ($max, SimpleStoredAnswer $item) {
                return max($max, $item->getVersion() + 1);
            }, 0);
        } else {
            $object->version = 1;
        }

        $object->answer = json_encode($answer);

        return $object;
    }

    public function getUuid() : string
    {
        return $this->uuid;
    }

    public function getVersion() : int
    {
        return $this->version;
    }

    public function getAnswer() : AbstractValueObject
    {
        return AbstractValueObject::deserialize($this->answer);
    }

    public static function returnDbTableName() : string
    {
        return self::STORAGE_NAME;
    }
}
