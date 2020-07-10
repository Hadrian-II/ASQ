<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Matching\Form\Editor;

use ilNumberInputGUI;
use ilRadioGroupInputGUI;
use ilRadioOption;
use ilSelectInputGUI;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\Matching\Editor\Data\MatchingEditorConfiguration;
use srag\asq\Questions\Matching\Editor\Data\MatchingItem;
use srag\asq\Questions\Matching\Editor\Data\MatchingMapping;
use srag\asq\UserInterface\Web\Fields\AsqTableInput;
use srag\asq\UserInterface\Web\Fields\AsqTableInputFieldDefinition;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

/**
 * Class MatchingEditorConfigurationFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class MatchingEditorConfigurationFactory extends AbstractObjectFactory
{
    const VAR_SHUFFLE = 'me_shuffle';
    const VAR_THUMBNAIL = 'me_thumbnail';
    const VAR_MATCHING_MODE = 'me_matching';

    const VAR_DEFINITIONS = 'me_definitions';
    const VAR_DEFINITION_TEXT = 'me_definition_text';
    const VAR_DEFINITION_IMAGE = 'me_definition_image';

    const VAR_TERMS = 'me_terms';
    const VAR_TERM_TEXT = 'me_term_text';
    const VAR_TERM_IMAGE = 'me_term_image';

    const VAR_MATCHES = 'me_matches';
    const VAR_MATCH_DEFINITION = 'me_match_definition';
    const VAR_MATCH_TERM = 'me_match_term';
    const VAR_MATCH_POINTS = 'me_match_points';

    /**
     * @param AbstractValueObject $value
     * @return array
     */
    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];
        /** @var MatchingEditorConfiguration $config */

        $shuffle_answers = new ilSelectInputGUI(
            $this->language->txt('asq_label_shuffle_answers'),
            self::VAR_SHUFFLE
        );

        $shuffle_answers->setOptions([
            MatchingEditorConfiguration::SHUFFLE_NONE
                => $this->language->txt('asq_option_shuffle_none'),
            MatchingEditorConfiguration::SHUFFLE_DEFINITIONS
                => $this->language->txt('asq_option_shuffle_definitions'),
            MatchingEditorConfiguration::SHUFFLE_TERMS
                => $this->language->txt('asq_option_shuffle_terms'),
            MatchingEditorConfiguration::SHUFFLE_BOTH
                => $this->language->txt('asq_option_shuffle_both')
        ]);
        $fields[] = $shuffle_answers;

        $thumbnail = new ilNumberInputGUI($this->language->txt('asq_label_thumbnail'), self::VAR_THUMBNAIL);
        $thumbnail->setRequired(true);
        $fields[] = $thumbnail;

        $matching_mode = new ilRadioGroupInputGUI($this->language->txt('asq_label_matching_mode'), self::VAR_MATCHING_MODE);

        $matching_mode->addOption(new ilRadioOption(
            $this->language->txt('asq_option_one_to_one'),
            MatchingEditorConfiguration::MATCHING_ONE_TO_ONE
        ));

        $matching_mode->addOption(new ilRadioOption(
            $this->language->txt('asq_option_many_to_one'),
            MatchingEditorConfiguration::MATCHING_MANY_TO_ONE
        ));

        $matching_mode->addOption(new ilRadioOption(
            $this->language->txt('asq_option_many_to_many'),
            MatchingEditorConfiguration::MATCHING_MANY_TO_MANY
        ));

        $fields[] = $matching_mode;

        if (!is_null($value)) {
            $shuffle_answers->setValue($value->getShuffle());
            $thumbnail->setValue($value->getThumbnailSize());
            $matching_mode->setValue($value->getMatchingMode());
        }

        $fields[] = $this->createDefinitionsTable($value);

        $fields[] = $this->createTermsTable($value);

        $fields[] = $this->createMatchTable($value);

        return $fields;
    }

    /**
     * @param MatchingEditorConfiguration $config
     */
    private function createDefinitionsTable(?MatchingEditorConfiguration $config) : AsqTableInput
    {
        $columns = [];

        $columns[] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_header_definition_text'),
            AsqTableInputFieldDefinition::TYPE_TEXT,
            self::VAR_DEFINITION_TEXT
        );

        $columns[] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_header_definition_image'),
            AsqTableInputFieldDefinition::TYPE_IMAGE,
            self::VAR_DEFINITION_IMAGE
        );

        return new AsqTableInput(
            $this->language->txt('asq_label_definitions'),
            self::VAR_DEFINITIONS,
            !is_null($config) ? $this->getItemValues($config->getDefinitions(), self::VAR_DEFINITION_TEXT, self::VAR_DEFINITION_IMAGE) : [],
            $columns
        );
    }

    /**
     * @param MatchingEditorConfiguration $config
     */
    private function createTermsTable(?MatchingEditorConfiguration $config) : AsqTableInput
    {
        $columns = [];

        $columns[] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_header_term_text'),
            AsqTableInputFieldDefinition::TYPE_TEXT,
            self::VAR_TERM_TEXT
        );

        $columns[] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_header_term_image'),
            AsqTableInputFieldDefinition::TYPE_IMAGE,
            self::VAR_TERM_IMAGE
        );

        return new AsqTableInput(
            $this->language->txt('asq_label_terms'),
            self::VAR_TERMS,
            !is_null($config) ? $this->getItemValues($config->getTerms(), self::VAR_TERM_TEXT, self::VAR_TERM_IMAGE) : [],
            $columns
        );
    }

    /**
     * @param MatchingItem[] $items
     * @return array
     */
    private function getItemValues(array $items, string $text_name, string $image_name) : array
    {
        return array_map(
            function ($item) use ($text_name, $image_name) {
                return [
                    $text_name => $item->getText(),
                    $image_name => $item->getImage()
                ];
            },
            $items
        );
    }

    /**
     * @param MatchingEditorConfiguration $config
     */
    private function createMatchTable(?MatchingEditorConfiguration $config) : AsqTableInput
    {
        $columns = [];

        $defs = [];

        foreach ($config->getDefinitions() as $definition) {
            $defs[$definition->getId()] = $definition->getText();
        }

        $columns[] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_header_matches_definition'),
            AsqTableInputFieldDefinition::TYPE_DROPDOWN,
            self::VAR_MATCH_DEFINITION,
            $defs
        );

        $terms = [];

        foreach ($config->getTerms() as $term) {
            $terms[$term->getId()] = $term->getText();
        }

        $columns[] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_header_matches_term'),
            AsqTableInputFieldDefinition::TYPE_DROPDOWN,
            self::VAR_MATCH_TERM,
            $terms
        );

        $columns[] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_header_points'),
            AsqTableInputFieldDefinition::TYPE_NUMBER,
            self::VAR_MATCH_POINTS
        );

        return new AsqTableInput(
            $this->language->txt('asq_label_matches'),
            self::VAR_MATCHES,
            !is_null($config) ? $this->getMatchesValues($config) : [],
            $columns
        );
    }

    /**
     * @param MatchingEditorConfiguration $config
     * @return array
     */
    private function getMatchesValues(MatchingEditorConfiguration $config) : array
    {
        return array_map(
            function ($match) {
                return [
                    self::VAR_MATCH_DEFINITION => $match->getDefinitionId(),
                    self::VAR_MATCH_TERM => $match->getTermId(),
                    self::VAR_MATCH_POINTS => $match->getPoints()
                ];
            },
            $config->getMatches()
        );
    }

    /**
     * @return MatchingEditorConfiguration
     */
    public function readObjectFromPost() : AbstractValueObject
    {
        $id = -1;

        $def = array_map(
            function ($value) use (&$id) {
                $id += 1;

                return MatchingItem::create(
                    strval($id),
                    $value[self::VAR_DEFINITION_TEXT],
                    $value[self::VAR_DEFINITION_IMAGE]
                );
            },
            $this->createDefinitionsTable($this->getDefaultValue())->readValues()
        );

        $id = -1;

        $term = array_map(
            function ($value) use (&$id) {
                $id += 1;

                return MatchingItem::create(
                    strval($id),
                    $value[self::VAR_TERM_TEXT],
                    $value[self::VAR_TERM_IMAGE]
                );
            },
            $this->createTermsTable($this->getDefaultValue())->readValues()
        );

        $match = array_map(
            function ($value) {
                return MatchingMapping::create(
                    $value[self::VAR_MATCH_DEFINITION],
                    $value[self::VAR_MATCH_TERM],
                    floatval($value[self::VAR_MATCH_POINTS])
                );
            },
            $this->createMatchTable($this->getDefaultValue())->readValues()
        );

        return MatchingEditorConfiguration::create(
            $this->readInt(self::VAR_SHUFFLE),
            $this->readInt(self::VAR_THUMBNAIL),
            $this->readInt(self::VAR_MATCHING_MODE),
            $def,
            $term,
            $match
        );
    }

    /**
     * @return MatchingEditorConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return MatchingEditorConfiguration::create();
    }
}
