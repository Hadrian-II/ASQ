<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence;

use ActiveRecord;

/**
 * Class QuestionType
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class QuestionType extends ActiveRecord
{
    const STORAGE_NAME = "asq_question_type";
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
     * @con_length     32
     * @con_is_notnull true
     */
    protected $title_key;
    /**
     * @var string
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_length     128
     * @con_is_notnull true
     */
    protected $factory_class;
    /**
     * @var string
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_length     128
     * @con_is_notnull true
     */
    protected $editor_class;
    /**
     * @var string
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_length     128
     * @con_is_notnull true
     */
    protected $scoring_class;
    /**
     * @var string
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_length     128
     * @con_is_notnull true
     */
    protected $storage_class;

    /**
     * @param string $title_key
     * @param string $factory_class
     * @param string $editor_class
     * @param string $scoring_class
     * @return QuestionType
     */
    public static function createNew(
        string $title_key = '',
        string $factory_class = '',
        string $editor_class = '',
        string $scoring_class = '',
        string $storage_class = ''
    ) : QuestionType {
        $object = new QuestionType();
        $object->title_key = $title_key;
        $object->factory_class = $factory_class;
        $object->editor_class = $editor_class;
        $object->scoring_class = $scoring_class;
        $object->storage_class = $storage_class;
        return $object;
    }

    public function getTitleKey() : string
    {
        return $this->title_key;
    }

    public function getFactoryClass() : string
    {
        return $this->factory_class;
    }

    public function getEditorClass() : string
    {
        return $this->editor_class;
    }

    public function getScoringClass() : string
    {
        return $this->scoring_class;
    }

    public function getStorageClass() : string
    {
        return $this->storage_class;
    }

    public static function returnDbTableName()
    {
        return self::STORAGE_NAME;
    }

    const KEY = 'key';
    const FORM_FACTORY = 'form_factory';
    const EDITOR = 'editor';
    const SCORING = 'scoring';
    const STORAGE = 'storage';

    public function serialize() : array
    {
        $data = [];
        $data[self::KEY] = $this->getTitleKey();
        $data[self::FORM_FACTORY] = $this->getFactoryClass();
        $data[self::EDITOR] = $this->getEditorClass();
        $data[self::SCORING] = $this->getScoringClass();
        $data[self::STORAGE] = $this->getStorageClass();
        return $data;
    }

    public static function deserialize(array $data) : QuestionType
    {
        return self::createNew(
            $data[self::KEY],
            $data[self::FORM_FACTORY],
            $data[self::EDITOR],
            $data[self::SCORING],
            $data[self::STORAGE]
        );
    }
}
