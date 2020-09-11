<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Form;

use ilLanguage;
use ilPropertyFormGUI;
use ilSelectInputGUI;
use srag\asq\Infrastructure\Persistence\QuestionType;
use srag\asq\UserInterface\Web\PostAccess;

/**
 * Class QuestionTypeSelectForm
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionTypeSelectForm extends ilPropertyFormGUI
{
    use PostAccess;

    const VAR_QUESTION_TYPE = "question_type";

    /**
     * @var QuestionType[]
     */
    private $question_types;

    /**
     * @var ilLanguage
     */
    private $language;

    /**
     * QuestionTypeSelectForm constructor.
     */
    public function __construct(ilLanguage $language)
    {
        $this->language = $language;

        $this->initForm();

        parent::__construct();
    }

    /**
     * Init settings property form
     *
     * @access private
     */
    private function initForm() : void
    {
        global $ASQDIC;

        $this->question_types = $ASQDIC->asq()->question()->getAvailableQuestionTypes();

        $this->setTitle($this->language->txt('asq_create_question_form'));

        $select = new ilSelectInputGUI(
            $this->language->txt('asq_input_question_type'),
            self::VAR_QUESTION_TYPE
        );

        $options = [];

        foreach ($this->question_types as $ix => $type) {
            $options[$ix] = $this->language->txt($type->getTitleKey());
        }

        $select->setOptions($options);
        $this->addItem($select);
    }

    /**
     * @return QuestionType
     */
    public function getQuestionType() : QuestionType
    {
        return $this->question_types[intval($this->getPostValue(self::VAR_QUESTION_TYPE))];
    }
}
