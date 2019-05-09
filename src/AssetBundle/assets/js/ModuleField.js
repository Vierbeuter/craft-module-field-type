;(function ($, window, document, undefined) {

    let pluginName = "ModuleField",
        defaults = {};

    // Plugin constructor
    function Plugin(element, options) {
        this.element = element;

        this.options = $.extend({}, defaults, options);

        this._defaults = defaults;
        this._name = pluginName;

        this.init();
    }

    Plugin.prototype = {

        init: function (id) {
            var _this = this;

            $(function () {

                /* -- _this.options gives us access to the $jsonVars that our FieldType passed down to us */

                //  FYI: this snippet is gonna be called for all "Module Field" matrix blocks
                //  --> on page load for each existing/loaded module and
                //  --> on module-add for the new matrix block

                let hiddenField = $('#' + _this.options.namespace);

                /**
                 * Updates the module field's actual form input (which is hidden) with given subfield key and value.
                 *
                 * @param updatedKey
                 * @param updatedValue
                 */
                let updateHidden = function (updatedKey, updatedValue) {
                    //  get module's current value
                    let value = hiddenField.val();

                    //  init data in case of field is new (--> module has just been added but has not been saved yet)
                    if (value === '') {
                        value = '{}';   //  make it an empty object as JSON
                    }

                    //  update the JSON-decoded object
                    value = JSON.parse(value);
                    value[updatedKey] = updatedValue;

                    //  update the actual input with new JSON value
                    hiddenField.val(JSON.stringify(value));
                };

                //  determine all subfields for current module field
                for (let i = 0; i < _this.options.subfields.length; i++) {
                    /**
                     * @var subfieldData is the result of Vierbeuter\Craft\Field\Subfield->toArray()
                     */
                    let subfieldData = _this.options.subfields[i];

                    //  init field data with NULL
                    if (_this.options.init) {
                        //  FIXME: some field types require special init values
                        //  --> such as selectField where either the very first option or the one selected by default should be used instead of NULL
                        //  --> or an elementSelectField's init value should rather be an empty array than NULL
                        //  --> check also initial/default values in config objects passed to Craft's form field macros
                        updateHidden(subfieldData.key, null);
                    }

                    let subfield = $('#' + subfieldData.id);
                    let subfieldContainer = $('#' + subfieldData.id + '-field');

                    //  bind change events to the subfields (respectively to their actual inputs)
                    switch (subfieldData.type) {

                        case 'autosuggestField':
                            //  TODO: implement me!
                            alert('Field type "' + subfieldData.type + '" needs to be implemented in ModuleField.js! Please do so before using that field type.');
                            break;

                        case 'checkboxField':
                            //  on de-/selected checkbox
                            subfield.change(function (event) {
                                updateHidden(subfieldData.key, subfield.is(':checked'));
                            });
                            break;

                        case 'checkboxGroupField':
                            //  TODO: implement me!
                            alert('Field type "' + subfieldData.type + '" needs to be implemented in ModuleField.js! Please do so before using that field type.');
                            break;

                        case 'checkboxSelectField':
                            //	on de/-selected checkboxes
                            subfieldContainer.change(function (event) {
                                let elements = [];

                                $(subfieldContainer.find('input[type=checkbox]')).each(function (index) {
                                    if ($(this).is(':checked')) {
                                        elements.push($(this).val());
                                    }
                                });

                                updateHidden(subfieldData.key, elements);
                            });
                            break;

                        case 'colorField':
                            let colorTextFields = subfieldContainer.find('input[type=text]');
                            if (colorTextFields.length) {
                                let colorTextField = $(colorTextFields.get(0));

                                //  on changed color by using the color-picker
                                subfieldContainer.change(function (event) {
                                    updateHidden(subfieldData.key, colorTextField.val());
                                });
                                //	on anything typed into the color field's text input
                                colorTextField.keyup(function (event) {
                                    updateHidden(subfieldData.key, colorTextField.val());
                                });
                            }
                            break;

                        case 'dateField':
                            let dateFields = subfieldContainer.find('input[type=text]');
                            if (dateFields.length) {
                                let dateField = $(dateFields.get(0));

                                //  on date-select
                                subfieldContainer.change(function (event) {
                                    updateHidden(subfieldData.key, dateField.val());
                                });
                                //	on anything typed into the date field's text input
                                dateField.keyup(function (event) {
                                    updateHidden(subfieldData.key, dateField.val());
                                });
                            }
                            break;

                        case 'dateTimeField':
                            let dateTimeFields = subfieldContainer.find('input[type=text]');
                            if (dateTimeFields.length) {
                                let dateField = $(dateTimeFields.get(0));
                                let timeField = $(dateTimeFields.get(1));

                                //  on date/time-select
                                subfieldContainer.change(function (event) {
                                    updateHidden(subfieldData.key, dateField.val() + ' ' + timeField.val());
                                });
                                //	on anything typed into the date field's text input
                                dateField.keyup(function (event) {
                                    updateHidden(subfieldData.key, dateField.val() + ' ' + timeField.val());
                                });
                                //	on anything typed into the time field's text input
                                timeField.keyup(function (event) {
                                    updateHidden(subfieldData.key, dateField.val() + ' ' + timeField.val());
                                });
                            }
                            break;

                        case 'editableTableField':
                            //  TODO: implement me!
                            alert('Field type "' + subfieldData.type + '" needs to be implemented in ModuleField.js! Please do so before using that field type.');
                            break;

                        case 'elementSelectField':
                            let onElementChangeUpdateHidden = function (event, add) {
                                let elementHiddenFields = subfield.find('input[type=hidden]');
                                let elements = [];

                                $(elementHiddenFields).each(function (index) {
                                    let elementId = parseInt($(this).val());
                                    if (add || parseInt(event.target.dataset.id) !== elementId) {
                                        elements.push(elementId);
                                    }
                                });

                                //  if element selection is limited to 1 element only
                                if ('limit' in subfieldData.config && subfieldData.config.limit === 1) {
                                    //  either single element or NULL
                                    updateHidden(subfieldData.key, elements.length ? elements.pop() : null);
                                } else {
                                    updateHidden(subfieldData.key, elements);
                                }
                            };

                            //	on changed element selection
                            //  FIXME: mutation events are deprecated --> use MutationObservers instead!
                            subfield.on('DOMNodeInserted', function (event) {
                                onElementChangeUpdateHidden(event, true);
                            });
                            subfield.on('DOMNodeRemoved', function (event) {
                                onElementChangeUpdateHidden(event, false);
                            });
                            break;

                        case 'lightswitchField':
                            //  on switched lightswitch
                            subfield.change(function (event) {
                                updateHidden(subfieldData.key, subfield.hasClass('on'));
                            });
                            break;

                        case 'multiselectField':
                            //	on selection-change
                            subfield.change(function (event) {
                                updateHidden(subfieldData.key, subfield.val());
                            });
                            break;

                        case 'passwordField':
                            //	on anything typed into the password field
                            subfield.keyup(function (event) {
                                updateHidden(subfieldData.key, subfield.val());
                            });
                            break;

                        case 'radioGroupField':
                            //	on selection-change
                            subfieldContainer.change(function (event) {
                                let selectedRadio = $(subfieldContainer.find('input[type=radio]:checked')).get(0);
                                updateHidden(subfieldData.key, $(selectedRadio).val());
                            });
                            break;

                        case 'selectField':
                            //	on selection-change
                            subfield.change(function (event) {
                                updateHidden(subfieldData.key, subfield.val());
                            });
                            break;

                        case 'textareaField':
                            //	on anything typed into the textarea
                            subfield.keyup(function (event) {
                                updateHidden(subfieldData.key, subfield.val());
                            });
                            break;

                        case 'textField':
                            //	on anything typed into the textfield
                            subfield.keyup(function (event) {
                                updateHidden(subfieldData.key, subfield.val());
                            });
                            break;

                        case 'timeField':
                            let timeFields = subfieldContainer.find('input[type=text]');
                            if (timeFields.length) {
                                let timeField = $(timeFields.get(0));

                                //  on time-select
                                timeField.change(function (event) {
                                    updateHidden(subfieldData.key, timeField.val());
                                });
                                //	on anything typed into the time field's text input
                                timeField.keyup(function (event) {
                                    updateHidden(subfieldData.key, timeField.val());
                                });
                            }
                            break;

                    }
                }
            });
        }
    };

    // A really lightweight plugin wrapper around the constructor,
    // preventing against multiple instantiations
    $.fn[pluginName] = function (options) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName,
                    new Plugin(this, options));
            }
        });
    };

})(jQuery, window, document);
