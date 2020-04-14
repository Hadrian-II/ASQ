<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Form;

use ilPropertyFormGUI;
use ilSelectInputGUI;
use srag\asq\Domain\Model\QuestionTypeDefinition;
use srag\asq\AsqGateway;

/**
 * Class QuestionTypeSelectForm
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionTypeSelectForm extends ilPropertyFormGUI {
	const VAR_QUESTION_TYPE = "question_type";

	/**
	 * @var QuestionTypeDefinition[]
	 */
	private $question_types;
	
    /**
     * QuestionTypeSelectForm constructor.
     */
	public function __construct( ) {
		$this->initForm();

		parent::__construct();
	}

	/**
	 * Init settings property form
	 *
	 * @access private
	 */
	private function initForm() {

	    global $DIC; /* @var \ILIAS\DI\Container $DIC */

	    $this->question_types = AsqGateway::get()->question()->getAvailableQuestionTypes();
	    
	    $this->setTitle($DIC->language()->txt('asq_create_question_form'));

		$select = new ilSelectInputGUI(
		    $DIC->language()->txt('asq_input_question_type'), self::VAR_QUESTION_TYPE
        );

		$options = [];

		foreach ($this->question_types as $ix => $type) {
            $options[$ix] = $type->getTitle();
		}

		$select->setOptions($options);
		$this->addItem($select);
	}

    /**
     * @return QuestionTypeDefinition
     */
	public function getQuestionType() : QuestionTypeDefinition {
		return $this->question_types[intval($_POST[self::VAR_QUESTION_TYPE])];
	}
}
