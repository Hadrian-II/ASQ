<?php
declare(strict_types = 1);

namespace srag\asq\Questions\MultipleChoice\Form;

use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Domain\Model\Answer\Option\AnswerOptions;
use srag\asq\Domain\Model\Answer\Option\ImageAndTextDisplayDefinition;

/**
 * Trait ChoiceQuestionPostProcessing
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
trait ChoiceQuestionPostProcessing
{
    /**
     * @param QuestionDto $question
     * @return QuestionDto
     */
    public function performQuestionPostProcessing(QuestionDto $question) : QuestionDto
    {
        // strip image when multiline is selected
        if (!$question->getPlayConfiguration()->getEditorConfiguration()->isSingleLine()) {
            // remove from question
            $stripped_options = AnswerOptions::create(
                array_map(
                    function($option) {
                        return AnswerOption::create(
                            $option->getOptionId(),
                            ImageAndTextDisplayDefinition::create($option->getDisplayDefinition()->getText(), ''),
                            $option->getScoringDefinition());
                    },
                    $question->getAnswerOptions()->getOptions()
                    )
                );

            $question->setAnswerOptions($stripped_options);
            $this->option_form->setAnswerOptions($stripped_options);
        }

        return $question;
    }
}