il = il || {};
il.ASQ = il.ASQ || {};
il.ASQ.Matching = (function($) {
    const MATCHING_ONE_TO_ONE = 0;
    const MATCHING_MANY_TO_ONE = 1;
    const MATCHING_MANY_TO_MANY = 2;
    let matchingMode;

    const buildDragHelper = function(event) {
        let draggable = $(event.target);

        if (!draggable.hasClass('draggable')) {
            draggable = $(draggable.parents('div.draggable'));
        }

        const helper = $('<div class="draggableHelper" />');
        helper.html(draggable.html());
        helper.css({
            width: draggable.css('width'),
            height: draggable.css('height'),
            'z-index': 1035,
        });
        return helper;
    }

    const isValidDroppable = function(droppable, draggable) {
        if (droppable.attr('id') === draggable.parents('.droparea').attr('id')) {
            return false;
        }

        const droppedDraggableId = `${droppable.attr('data-type')}_${
            droppable.attr('data-id')}_${draggable.attr('data-type')
        }_${draggable.attr('data-id')}`;

        if (matchingMode === MATCHING_MANY_TO_MANY
                && droppable.find(`#${droppedDraggableId}`).length > 0) {
            return false;
        } if (matchingMode === MATCHING_ONE_TO_ONE
                && droppable.find('.draggable').length > 0) {
            return false;
        }

        return true;
    }

    const startDrag = function() {
        const that = $(this);

        that.addClass('draggableDisabled');

        $('.js_definition').each((index, item) => {
            if (isValidDroppable($(item), that)) {
                $(item).addClass('droppableTarget');
                $(item).droppable('enable');
                $(item).droppable('option', 'hoverClass', 'droppableHover');
            }
        });

        if (that.parents('.droparea').length > 0) {
            const termDroppable = $(`#${that.attr('data-type')}_${
                that.attr('data-id')}`);

            termDroppable.removeClass('draggableDisabled');
            termDroppable.addClass('droppableTarget');
            termDroppable.droppable('enable');
            termDroppable.droppable('option', 'hoverClass', 'droppableHover');
        }
    }

    const isDraggableToBeReactivated = function(draggable) {
        if ($(draggable).parents('.droparea').length > 0) {
            return true;
        }

        if (matchingMode === MATCHING_MANY_TO_MANY) {
            return true;
        }

        let reactivationRequired = true;

        $('.js_definition').each(
            (id, item) => {
                $(item).find('.draggable').each(
                    (key, droppedDraggable) => {
                        if ($(droppedDraggable).attr('data-id') === $(
                            draggable,
                        ).attr('data-id')) {
                            reactivationRequired = false;
                        }
                    },
                );
            },
        );

        return reactivationRequired;
    }

    const stopDrag = function() {
        if (isDraggableToBeReactivated(this)) {
            $(this).removeClass('draggableDisabled');
        }

        $('.js_definition').each((index, element) => {
            $(element).removeClass('droppableTarget');
            $(element).droppable('disable');
            $(element).droppable('option', 'hoverClass', '');
        });

        if ($(this).parents('.droparea').length > 0) {
            const domSelector = `#${$(this).attr('data-type')}_${
                $(this).attr('data-id')}`;

            $(domSelector).removeClass('droppableTarget');
            $(domSelector).droppable('disable');
            $(domSelector).droppable('option', 'hoverClass', '');

            if (matchingMode === MATCHING_ONE_TO_ONE
                    || matchingMode === MATCHING_MANY_TO_ONE) {
                $(domSelector).addClass('draggableDisabled');
            }
        }
    }

    const getAnswerItem = function(item) {
        return item.parents('.answers').find('input[type=hidden].answer').eq(0);
    }

    const removeTermInputFromDefinition = function(draggable, droppable) {
        const inputId = `data_${droppable.attr('data-type')}_${
            droppable.attr('data-id')}_${draggable.attr('data-type')
        }_${draggable.attr('data-id')}`;

        $(`#${inputId}`).remove();

        const answerItem = getAnswerItem(draggable);
        const currentAnswers = answerItem.val().split(';');
        const oldMatch = `${droppable.attr('data-id')}-${
            draggable.attr('data-id')}`;
        const oldIndex = currentAnswers.indexOf(oldMatch);
        if (oldIndex > -1) {
            currentAnswers.splice(oldIndex, 1);
        }
        answerItem.val(currentAnswers.join(';'));
    }

    const buildDroppedDraggableCloneId = function(draggable, droppable) {
        const cloneId = `${droppable.attr('data-type')}_${
            droppable.attr('data-id')}_${draggable.attr('data-type')
        }_${draggable.attr('data-id')}`;

        return cloneId;
    }

    const appendTermInputToDefinition = function(draggable, droppable) {
        const input = $('<input type="hidden" />');

        input.attr('id', `data_${draggable.attr('id')}`);
        input.attr('value', draggable.attr('data-id'));

        droppable.append(input);

        const answerItem = getAnswerItem(draggable);
        const currentAnswers = answerItem.val().split(';');
        currentAnswers.push(`${droppable.attr('data-id')}-${
            draggable.attr('data-id')}`);

        answerItem.val(currentAnswers.join(';'));
    }

    const dropElementHandler = function(event, ui) {
        ui.helper.remove();

        if (ui.draggable.parents('.droparea').length > 0) {
            removeTermInputFromDefinition(ui.draggable, ui.draggable
                .parents('.droparea'));
            ui.draggable.remove();
        } else if (matchingMode === MATCHING_ONE_TO_ONE
                || matchingMode === MATCHING_MANY_TO_ONE) {
            ui.draggable.draggable('disable');
        }

        const draggableOriginalSelector = `#${$(ui.draggable).attr('data-type')
        }_${$(ui.draggable).attr('data-id')}`;

        $(draggableOriginalSelector).removeClass('droppableTarget');

        if ($(this).hasClass('droparea')) {
            if (matchingMode === MATCHING_MANY_TO_MANY) {
                $(draggableOriginalSelector).removeClass('draggableDisabled');
            } else if (matchingMode === MATCHING_ONE_TO_ONE
                    || matchingMode === MATCHING_MANY_TO_ONE) {
                $(draggableOriginalSelector).addClass('draggableDisabled');
            }
        }

        const droppedDraggableClone = ui.draggable.clone();

        if ($(this).attr('data-type') === 'definition') {
            const cloneId = buildDroppedDraggableCloneId(ui.draggable, $(this));

            droppedDraggableClone.attr('id', cloneId);
            droppedDraggableClone.removeClass('draggableDisabled');
            droppedDraggableClone.addClass('droppedDraggable');

            $(this).find('.ilMatchingQuestionTerm').append(
                droppedDraggableClone,
            );

            $(`#${droppedDraggableClone.attr('id')}`).draggable(
                {
                    helper: buildDragHelper,
                    start: startDrag,
                    stop: stopDrag,
                    revert: true,
                    scroll: true,
                    containment: $(`#${droppedDraggableClone.attr('id')}`)
                        .parents('.ilc_question_standard').eq(0),
                },
            );
        } else if ($(this).attr('data-type') === 'term'
                && (matchingMode === MATCHING_ONE_TO_ONE
                        || matchingMode === MATCHING_MANY_TO_ONE)) {
            $(draggableOriginalSelector).draggable('enable');
        }

        if ($(this).hasClass('droparea')) {
            appendTermInputToDefinition(droppedDraggableClone, $(this));
        }

        $('.js_definition').removeClass('droppableTarget');
    }

    const restoreMatches = function() {
        $('input[type=hidden].answer').eq(0).val().split(';')
            .forEach(
                (match) => {
                    const raw = match.split('-');
                    const definition = raw[0];
                    const term = raw[1];

                    const definitionDroppable = $(`#definition_${definition}`);
                    const termDraggable = $(`#term_${term}`);

                    const cloneId = buildDroppedDraggableCloneId(termDraggable,
                        definitionDroppable);

                    const droppedDraggableClone = termDraggable.clone();
                    droppedDraggableClone.attr('id', cloneId);
                    droppedDraggableClone.removeClass('draggableDisabled');

                    definitionDroppable.find('.ilMatchingQuestionTerm').append(
                        droppedDraggableClone,
                    );

                    $(`#${droppedDraggableClone.attr('id')}`).draggable(
                        {
                            helper: buildDragHelper,
                            start: startDrag,
                            stop: stopDrag,
                            revert: true,
                            scroll: true,
                            containment: $(
                                `#${droppedDraggableClone.attr('id')}`,
                            )
                                .parents('.ilc_question_standard')
                                .eq(0),
                        },
                    );

                    if (matchingMode === MATCHING_ONE_TO_ONE ||
                        matchingMode === MATCHING_MANY_TO_ONE) {
                        termDraggable.draggable('disable');
                        termDraggable.addClass('draggableDisabled');
                    }
                },
            );
    }

    $(document).ready(
        () => {
            $('.js_definition, .js_term').each((i, droppable) => {
                $(droppable).droppable({
                    drop: dropElementHandler,
                    disabled: true,
                    tolerance: 'pointer',
                });
            });

            $('.js_term').each(
                (i, draggable) => {
                    $(draggable).draggable(
                        {
                            helper: buildDragHelper,
                            start: startDrag,
                            stop: stopDrag,
                            revert: true,
                            scroll: true,
                            containment: $(draggable).parents(
                                '.ilc_question_standard',
                            ).eq(0),
                        },
                    );
                },
            );

            matchingMode = parseInt($('.js_matching_type').val(), 10);
            restoreMatches();
        },
    );
})($);
