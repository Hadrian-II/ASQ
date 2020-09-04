<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence;

use ActiveRecord;

/**
 * Class QuestionType
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
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
     * @param string $title_key
     * @param string $factory_class
     * @param string $editor_class
     * @param string $scoring_class
     * @return QuestionType
     */
    public static function createNew(
        string $title_key,
        string $factory_class,
        string $editor_class,
        string $scoring_class
    ) : QuestionType {
        $object = new QuestionType();
        $object->title_key = $title_key;
        $object->factory_class = $factory_class;
        $object->editor_class = $editor_class;
        $object->scoring_class = $scoring_class;
        return $object;
    }

    /**
     * @return string
     */
    public function getTitleKey() : string
    {
        return $this->title_key;
    }

    /**
     * @return string
     */
    public function getFactoryClass() : string
    {
        return $this->factory_class;
    }

    /**
     * @return string
     */
    public function getEditorClass() : string
    {
        return $this->editor_class;
    }

    /**
     * @return string
     */
    public function getScoringClass() : string
    {
        return $this->scoring_class;
    }

    /**
     * @return string
     */
    public static function returnDbTableName()
    {
        return self::STORAGE_NAME;
    }

    const KEY = 'key';
    const FORM_FACOTRY = 'form_factory';
    const EDITOR = 'editor';
    const SCORING = 'scoring';

    /**
     * @return array
     */
    public function serialize() : array
    {
        $data = [];
        $data[self::KEY] = $this->getTitleKey();
        $data[self::FORM_FACOTRY] = $this->getFactoryClass();
        $data[self::EDITOR] = $this->getEditorClass();
        $data[self::SCORING] = $this->getScoringClass();
        return $data;
    }

    /**
     * @param array $data
     * @return QuestionType
     */
    public static function deserialize(array $data) : QuestionType
    {
        return self::createNew(
            $data[self::KEY],
            $data[self::FORM_FACOTRY],
            $data[self::EDITOR],
            $data[self::SCORING]
        );
    }
}
