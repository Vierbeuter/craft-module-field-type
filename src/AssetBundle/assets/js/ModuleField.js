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
                        updateHidden(subfieldData.key, null);
                    }

                    let subfield = $('#' + subfieldData.id);

                    //  bind change events to the subfields (respectively to their actual inputs)
                    switch (subfieldData.type) {

                        case 'autosuggestField':
                            //  TODO: implement me!
                            alert('Field type "' + subfieldData.type + '" needs to be implemented in ModuleField.js! Please do so before using that field type.');
                            break;

                        case 'checkboxField':
                            //  TODO: implement me!
                            alert('Field type "' + subfieldData.type + '" needs to be implemented in ModuleField.js! Please do so before using that field type.');
                            break;

                        case 'checkboxGroupField':
                            //  TODO: implement me!
                            alert('Field type "' + subfieldData.type + '" needs to be implemented in ModuleField.js! Please do so before using that field type.');
                            break;

                        case 'checkboxSelectField':
                            //  TODO: implement me!
                            alert('Field type "' + subfieldData.type + '" needs to be implemented in ModuleField.js! Please do so before using that field type.');
                            break;

                        case 'colorField':
                            //  TODO: implement me!
                            alert('Field type "' + subfieldData.type + '" needs to be implemented in ModuleField.js! Please do so before using that field type.');
                            break;

                        case 'dateField':
                            //  TODO: implement me!
                            alert('Field type "' + subfieldData.type + '" needs to be implemented in ModuleField.js! Please do so before using that field type.');
                            break;

                        case 'dateTimeField':
                            //  TODO: implement me!
                            alert('Field type "' + subfieldData.type + '" needs to be implemented in ModuleField.js! Please do so before using that field type.');
                            break;

                        case 'editableTableField':
                            //  TODO: implement me!
                            alert('Field type "' + subfieldData.type + '" needs to be implemented in ModuleField.js! Please do so before using that field type.');
                            break;

                        case 'elementSelectField':
                            //	on changed entry selection
                            //  FIXME: mutation events are deprecated --> use MutationObservers instead!
                            subfield.on('DOMNodeInserted', function (event) {
                                let entryHiddenField = subfield.find('input[type=hidden]');
                                updateHidden(
                                    subfieldData.key,
                                    entryHiddenField.length ? $(entryHiddenField.get(0)).val() : ''
                                );
                            });
                            //  TODO: also updateHidden() on removed elements
                            break;

                        case 'fileField':
                            //  TODO: implement me!
                            alert('Field type "' + subfieldData.type + '" needs to be implemented in ModuleField.js! Please do so before using that field type.');
                            break;

                        case 'lightswitchField':
                            //  TODO: implement me!
                            alert('Field type "' + subfieldData.type + '" needs to be implemented in ModuleField.js! Please do so before using that field type.');
                            break;

                        case 'multiselectField':
                            //  TODO: implement me!
                            alert('Field type "' + subfieldData.type + '" needs to be implemented in ModuleField.js! Please do so before using that field type.');
                            break;

                        case 'passwordField':
                            //	on anything typed into the password field
                            subfield.keyup(function (event) {
                                updateHidden(subfieldData.key, subfield.val());
                            });
                            break;

                        case 'radioGroupField':
                            //  TODO: implement me!
                            alert('Field type "' + subfieldData.type + '" needs to be implemented in ModuleField.js! Please do so before using that field type.');
                            break;

                        case 'selectField':
                            //  TODO: implement me!
                            alert('Field type "' + subfieldData.type + '" needs to be implemented in ModuleField.js! Please do so before using that field type.');
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
                            //  TODO: implement me!
                            alert('Field type "' + subfieldData.type + '" needs to be implemented in ModuleField.js! Please do so before using that field type.');
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
