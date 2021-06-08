<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Choice\Form\Editor\MultipleChoice;

use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Questions\Generic\Data\ImageAndTextDisplayDefinition;
use ILIAS\UI\Implementation\Component\Input\Field\RealTextInputImageProcessor;

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
            $stripped_options = array_map(
                function ($option) {
                    $processor = new RealTextInputImageProcessor($option->getDisplayDefinition()->getText());
                    $processor->process();

                    return new AnswerOption(
                        $option->getOptionId(),
                        new ImageAndTextDisplayDefinition($processor->getProcessedMarkup(), ''),
                        $option->getScoringDefinition()
                    );
                },
                $question->getAnswerOptions()
            );

            $question->setAnswerOptions($stripped_options);
        }

        return $question;
    }
}
