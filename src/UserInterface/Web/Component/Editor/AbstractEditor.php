<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component\Editor;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\AbstractConfiguration;
use srag\asq\Domain\Model\Question;
use srag\asq\Domain\Model\Answer\Answer;

/**
 * Abstract Class AbstractEditor
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
abstract class AbstractEditor {
	const EDITOR_DEFINITION_SUFFIX = 'DisplayDefinition';

	/**
	 * @var QuestionDto
	 */
	protected $question;
	/**
	 * @var AbstractValueObject
	 */
	protected $answer;
	/**
	 * @var bool
	 */
	protected $render_feedback;
	
	/**
	 * AbstractEditor constructor.
	 *
	 * @param QuestionDto   $question
	 * @param array|null $configuration
	 */
	public function __construct(QuestionDto $question) {
		$this->question = $question;
		
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		    $this->setAnswer($this->readAnswer());
		}
	}

	public function setRenderFeedback(bool $render_feedback) {
	   $this->render_feedback = $render_feedback;
	}
	
	/**
	 * @return string
	 */
	abstract public function generateHtml(): string;

	/**
	 * @return Answer
	 */
	abstract public function readAnswer() : AbstractValueObject;

	/**
	 * @param AbstractValueObject $answer
	 */
	public function setAnswer(AbstractValueObject $answer) : void {
	    $this->answer = $answer;
	}

    /**
     * @param AbstractConfiguration|null $config
     *
     * @return array|null
     */
	public static function generateFields(?AbstractConfiguration $config): ?array {
		return [];
	}

	public static abstract function readConfig();
	
	public static abstract function isComplete(Question $question): bool;
	/**
	 * @return string
	 */
	static function getDisplayDefinitionClass() : string {
		return get_called_class() . self::EDITOR_DEFINITION_SUFFIX;
	}
}