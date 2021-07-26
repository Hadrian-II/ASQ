<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Choice\Form\Editor\MultipleChoice;

use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Questions\Generic\Data\ImageAndTextDisplayDefinition;
use ILIAS\UI\Implementation\Component\Input\Field\MarkDownInputImageProcessor;

/**
 * Trait ChoiceQuestionPostProcessing
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
trait ChoiceQuestionPostProcessing
{
    public function performQuestionPostProcessing(QuestionDto $question) : QuestionDto
    {
        // strip image when multiline is selected
        if (!$question->getPlayConfiguration()->getEditorConfiguration()->isSingleLine()) {
            // remove from question
            $stripped_options = array_map(
                function ($option) {
                    $processor = new MarkDownInputImageProcessor($option->getDisplayDefinition()->getText());
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
