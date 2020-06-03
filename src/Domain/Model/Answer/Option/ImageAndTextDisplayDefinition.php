<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model\Answer\Option;

use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\UserInterface\Web\AsqHtmlPurifier;
use srag\asq\UserInterface\Web\ImageUploader;
use srag\asq\UserInterface\Web\Fields\AsqTableInputFieldDefinition;
use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class ImageAndTextDisplayDefinition
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ImageAndTextDisplayDefinition extends AbstractValueObject {

	const VAR_MCDD_TEXT = 'mcdd_text' ;
	const VAR_MCDD_IMAGE = 'mcdd_image';

	/**
	 * @var string
	 */
	protected $text;
	/**
	 * @var string
	 */
	protected $image;

	public static function create(string $text, string $image) : ImageAndTextDisplayDefinition {
	    $object = new ImageAndTextDisplayDefinition();
	    $object->text = $text;
	    $object->image = $image;
		return $object;
	}

	/**
	 * @return string
	 */
	public function getText(): string {
		return $this->text;
	}

	public function getImage(): string {
		return $this->image;
	}

	public static function getFields(QuestionPlayConfiguration $play): array {
	    global $DIC;

	    $fields = [];

	    $fields[self::VAR_MCDD_TEXT] = new AsqTableInputFieldDefinition(
            $DIC->language()->txt('asq_label_answer_text'),
            AsqTableInputFieldDefinition::TYPE_TEXT,
            self::VAR_MCDD_TEXT
            );

	    $fields[self::VAR_MCDD_IMAGE] = new AsqTableInputFieldDefinition(
            $DIC->language()->txt('asq_label_answer_image'),
            AsqTableInputFieldDefinition::TYPE_IMAGE,
            self::VAR_MCDD_IMAGE
            );

		return $fields;
	}

	public static function getValueFromPost(string $index) {
		return ImageAndTextDisplayDefinition::create(
		    AsqHtmlPurifier::getInstance()->purify($_POST[self::getPostKey($index, self::VAR_MCDD_TEXT)]),
		    ImageUploader::getInstance()->processImage(self::getPostKey($index, self::VAR_MCDD_IMAGE))
		);
	}

	public function getValues(): array {
		return [self::VAR_MCDD_TEXT => $this->text,
		        self::VAR_MCDD_IMAGE => $this->image];
	}

	/**
	 * @var string
	 */
	private static $error_message;

	/**
	 * @param string $index
	 * @return bool
	 */
	public static function checkInput(int $count) : bool {
	    global $DIC;

	    for ($i = 1; $i <= $count; $i++) {
	        if ($_POST[self::getPostKey(strval($i), self::VAR_MCDD_TEXT)] == null)
	        {
	            self::$error_message = $DIC->language()->txt('msg_input_is_required');
	            return false;
	        }
	    }

	    return true;
	}

	/**
	 * @return string
	 */
	public static function getErrorMessage() : string {
	    return self::$error_message;
	}
}