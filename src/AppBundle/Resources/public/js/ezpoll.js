(function (global, document) {
    global.addEventListener('load', () => {
        const SELECTOR_REMOVE_ANSWER = '.ez-btn--remove-answer';
        const SELECTOR_ANSWER = '.ez-data-source__answer';
        const SELECTOR_FIELD = '.ez-field-edit--ezpoll';

        class EzPollValidator extends global.eZ.BaseFieldValidator {

            /**
             * Validates the input
             *
             * @method validateInput
             * @param {Event} event
             * @returns {Object}
             * @memberof EzStringValidator
             */
            validateInput(event) {
                const isRequired = event.target.required;
                const isEmpty = !event.target.value;
                const isTooShort = event.target.value.length < parseInt(event.target.dataset.min, 10);
                const isTooLong = event.target.value.length > parseInt(event.target.dataset.max, 10);
                const isError = (isEmpty && isRequired) || isTooShort || isTooLong;
                const label = event.target.closest(SELECTOR_FIELD).querySelector('.ez-field-edit__label').innerHTML;
                const result = {isError};

                if (isEmpty) {
                    result.errorMessage = global.eZ.errors.emptyField.replace('{fieldName}', label);
                } else if (isTooShort) {
                    result.errorMessage = global.eZ.errors.tooShort.replace('{fieldName}', label).replace('{minLength}', event.target.dataset.min);
                } else if (isTooLong) {
                    result.errorMessage = global.eZ.errors.tooLong.replace('{fieldName}', label).replace('{maxLength}', event.target.dataset.max);
                }

                return result;
            }

            /**
             * Sets an index to template.
             *
             * @method setIndex
             * @param {HTMLElement} parentNode
             * @param {String} template
             * @returns {String}
             * @memberof EzPollValidator
             */
            setIndex(parentNode, template) {
                return template.replace(/__index__/g, parentNode.querySelectorAll(SELECTOR_ANSWER).length)
            }

            /**
             * Updates the disable state.
             *
             * @method updateDisabledState
             * @param {HTMLElement} parentNode
             * @memberof EzPollValidator
             */
            updateDisabledState(parentNode) {
                const isEnabled = parentNode.querySelectorAll(SELECTOR_ANSWER).length > 1;

                [...parentNode.querySelectorAll(SELECTOR_REMOVE_ANSWER)].forEach(btn => {
                    if (isEnabled) {
                        btn.removeAttribute('disabled');
                    } else {
                        btn.setAttribute('disabled', true);
                    }
                });
            }

            /**
             * Removes an item.
             *
             * @method removeItem
             * @param {Event} event
             * @memberof EzPollValidator
             */
            removeItem(event) {
                const answerNode = event.target.closest(SELECTOR_FIELD);

                event.target.closest(SELECTOR_ANSWER).remove();

                this.updateDisabledState(answerNode);
                this.reinit();
            }

            /**
             * Adds an item.
             *
             * @method addItem
             * @param {Event} event
             * @memberof EzPollValidator
             */
            addItem(event) {
                console.log(event);
                const answerNode = event.target.closest(SELECTOR_FIELD);
                const template = answerNode.dataset.template;
                const node = event.target.closest('.ez-field-edit__data .ez-data-source');

                node.insertAdjacentHTML('beforeend', this.setIndex(answerNode, template));

                this.reinit();
                this.updateDisabledState(answerNode);
            }

            /**
             * Attaches event listeners based on a config.
             *
             * @method init
             * @memberof EzPollValidator
             */
            init() {
                super.init();

                [...document.querySelectorAll(this.fieldSelector)].forEach(field => this.updateDisabledState(field));
            }
        }

        const validator = new EzPollValidator({
            classInvalid: 'is-invalid',
            fieldSelector: SELECTOR_FIELD,
            eventsMap: [
                {
                    isValueValidator: false,
                    selector: SELECTOR_REMOVE_ANSWER,
                    eventName: 'click',
                    callback: 'removeItem',
                },
                {
                    isValueValidator: false,
                    selector: '.ez-btn--add-answer',
                    eventName: 'click',
                    callback: 'addItem',
                },
                {
                    selector: '.ez-field-edit--ezpoll input',
                    eventName: 'blur',
                    callback: 'validateInput',
                    errorNodeSelectors: ['.ez-field-edit__label-wrapper'],
                    invalidStateSelectors: ['.ez-data-source__input']
                },
            ],
        });

        validator.init();

        global.eZ.fieldTypeValidators = global.eZ.fieldTypeValidators ?
            [...global.eZ.fieldTypeValidators, validator] :
            [validator];
    });
})(window, document);
