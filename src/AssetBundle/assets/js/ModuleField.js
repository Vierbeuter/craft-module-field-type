;(function($, window, document, undefined) {
  let pluginName = 'ModuleField',
      defaults   = {};

  // Plugin constructor
  function Plugin(element, options) {
    this.element = element;

    this.options = $.extend({}, defaults, options);

    this._defaults = defaults;
    this._name = pluginName;

    this.init();
  }

  Plugin.prototype = {

    init: function(id) {
      var _this = this;

      $(function() {

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
         * @param hiddenField
         */
        const updateHidden = function(updatedKey, updatedValue, hiddenField) {
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
          //  trigger change event to notify any listeners (like an outer group)
          hiddenField.change();
        };

        // init field with empty string
        const initTextEmpty = function(fieldData, hiddenField) {
          if (_this.options.init) {
            updateHidden(fieldData.key, '', hiddenField);
          }
        };

        // init field with bool false
        const initBoolFalse = function(fieldData, hiddenField) {
          if (_this.options.init) {
            updateHidden(fieldData.key, false, hiddenField);
          }
        };

        // init field with empty array
        const initArrayEmpty = function(fieldData, hiddenField) {
          if (_this.options.init) {
            updateHidden(fieldData.key, [], hiddenField);
          }
        };

        // init field with null
        const initNull = function(fieldData, hiddenField) {
          if (_this.options.init) {
            updateHidden(fieldData.key, null, hiddenField);
          }
        };

        /**
         * Initializes the given subfield, like binding change events on it to update the hidden module field.
         *
         * @param fieldData
         * @param hiddenField
         *
         * @see updateHidden()
         */
        const initSubfield = function(fieldData, hiddenField) {

          let subfield = $('#' + fieldData.id);
          let subfieldContainer = $('#' + fieldData.id + '-field');

          //  bind change events to the subfields (respectively to their actual inputs)
          switch (fieldData.type) {

            case 'autosuggestField':
              initTextEmpty(fieldData, hiddenField);

              //  TODO: implement me!
              alert('Field type "' + fieldData.type + '" needs to be implemented in ModuleField.js! Please do so before using that field type.');
              break;

            case 'checkboxField':
              initBoolFalse(fieldData, hiddenField);

              //  on de-/selected checkbox
              subfield.change(function(event) {
                updateHidden(fieldData.key, subfield.is(':checked'), hiddenField);
              });
              break;

            case 'checkboxSelectField':
              initArrayEmpty(fieldData, hiddenField);

              //	on de/-selected checkboxes
              subfieldContainer.change(function(event) {
                let elements = [];

                $(subfieldContainer.find('input[type=checkbox]')).each(function(index) {
                  if ($(this).is(':checked')) {
                    elements.push($(this).val());
                  }
                });

                updateHidden(fieldData.key, elements, hiddenField);
              });
              break;

            case 'colorField':
              initTextEmpty(fieldData, hiddenField);

              let colorTextFields = subfieldContainer.find('input[type=text]');
              if (colorTextFields.length) {
                let colorTextField = $(colorTextFields.get(0));

                //  on changed color by using the color-picker
                subfieldContainer.change(function(event) {
                  updateHidden(fieldData.key, colorTextField.val(), hiddenField);
                });
                //	on anything typed into the color field's text input
                colorTextField.keyup(function(event) {
                  updateHidden(fieldData.key, colorTextField.val(), hiddenField);
                });
              }
              break;

            case 'dateField':
              initTextEmpty(fieldData, hiddenField);

              let dateFields = subfieldContainer.find('input[type=text]');
              if (dateFields.length) {
                let dateField = $(dateFields.get(0));

                //  on date-select
                subfieldContainer.change(function(event) {
                  updateHidden(fieldData.key, dateField.val(), hiddenField);
                });
                //	on anything typed into the date field's text input
                dateField.keyup(function(event) {
                  updateHidden(fieldData.key, dateField.val(), hiddenField);
                });
              }
              break;

            case 'dateTimeField':
              initTextEmpty(fieldData, hiddenField);

              let dateTimeFields = subfieldContainer.find('input[type=text]');
              if (dateTimeFields.length) {
                let dateField = $(dateTimeFields.get(0));
                let timeField = $(dateTimeFields.get(1));

                //  on date/time-select
                subfieldContainer.change(function(event) {
                  updateHidden(fieldData.key, dateField.val() + ' ' + timeField.val(), hiddenField);
                });
                //	on anything typed into the date field's text input
                dateField.keyup(function(event) {
                  updateHidden(fieldData.key, dateField.val() + ' ' + timeField.val(), hiddenField);
                });
                //	on anything typed into the time field's text input
                timeField.keyup(function(event) {
                  updateHidden(fieldData.key, dateField.val() + ' ' + timeField.val(), hiddenField);
                });
              }
              break;

            case 'editableTableField':
              initArrayEmpty(fieldData, hiddenField);

              const onTableChangeUpdateHidden = function() {
                //  determine data of table-rows
                let tableData = [];
                $('tr', subfieldContainer).each(function() {
                  let rowData = [];
                  let rowInputs = $(this).find('td :input');

                  //  find values in current row
                  if (rowInputs.length) {
                    rowInputs.each(function() {
                      rowData.push($(this).val());
                    });
                    tableData.push(rowData);
                  }
                });

                updateHidden(fieldData.key, tableData, hiddenField);
              };

              const dragEnd = function(callback) {
                $(window).on('mouseup', function() {
                  callback();
                  $(window).unbind('mouseup');
                });
              };

              const updateBindingsLastRow = function() {
                $('.delete', $('tr', subfieldContainer).last()).on('click', function() {
                  onTableChangeUpdateHidden();
                });
                $('.move', $('tr', subfieldContainer).last()).on('mousedown', function() {
                  dragEnd(onTableChangeUpdateHidden);
                });
              };

              //	on changed within the table
              subfieldContainer.find('table').change(function() {
                onTableChangeUpdateHidden();
              });
              //	on anything typed into any field in the table
              subfieldContainer.find('table').keyup(function() {
                onTableChangeUpdateHidden();
              });
              //	on added, moved or removed table row
              $('.add, .delete', subfieldContainer).on('click', function() {
                onTableChangeUpdateHidden();
              });
              $('.move', subfieldContainer).on('mousedown', function() {
                dragEnd(onTableChangeUpdateHidden);
              });

              $('.add', subfieldContainer).on('click', function() {
                updateBindingsLastRow();
              });
              break;

            case 'elementSelectField':
              if ('limit' in fieldData.config && fieldData.config.limit === 1) {
                initNull(fieldData, hiddenField);
              } else {
                initArrayEmpty(fieldData, hiddenField);
              }

              let onElementChangeUpdateHidden = function(event) {
                let elementHiddenFields = subfield.find('input[type=hidden]');
                let elements = [];

                $(elementHiddenFields).each(function(index) {
                  let elementId = parseInt($(this).val());
                  if (event.removedNodes === 0 || parseInt(event.target.dataset.id) !== elementId) {
                    elements.push(elementId);
                  }
                });

                //  if element selection is limited to 1 element only
                if ('limit' in fieldData.config && fieldData.config.limit === 1) {
                  //  either single element or NULL
                  updateHidden(fieldData.key, elements.length ? elements.pop() : null, hiddenField);
                } else {
                  updateHidden(fieldData.key, elements, hiddenField);
                }
              };

              //	on changed element selection
              const selectFieldObserver = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                  onElementChangeUpdateHidden(mutation, true);
                });
              });
              selectFieldObserver.observe(subfield[0], {
                childList: true,
                subtree: true,
              });
              break;

            case 'groupField':
              initNull(fieldData, hiddenField);

              subfield.change(function(event) {
                updateHidden(fieldData.key, JSON.parse(subfield.val()), hiddenField);
              });
              break;

            case 'lightswitchField':
              initBoolFalse(fieldData, hiddenField);

              //  on switched lightswitch
              subfield.change(function(event) {
                updateHidden(fieldData.key, subfield.hasClass('on'), hiddenField);
              });
              break;

            case 'multiplyField':
              initNull(fieldData, hiddenField);

              subfield.change(function(event) {
                updateHidden(fieldData.key, JSON.parse(subfield.val()), hiddenField);
              });
              break;

            case 'multiselectField':
              initArrayEmpty(fieldData, hiddenField);

              //	on selection-change
              subfield.change(function(event) {
                updateHidden(fieldData.key, subfield.val(), hiddenField);
              });
              break;

            case 'passwordField':
              initTextEmpty(fieldData, hiddenField);

              //	on anything typed into the password field
              subfield.keyup(function(event) {
                updateHidden(fieldData.key, subfield.val(), hiddenField);
              });
              break;

            case 'radioGroupField':
              initTextEmpty(fieldData, hiddenField);

              //	on selection-change
              subfieldContainer.change(function(event) {
                let selectedRadio = $(subfieldContainer.find('input[type=radio]:checked')).get(0);
                updateHidden(fieldData.key, $(selectedRadio).val(), hiddenField);
              });
              break;

            case 'redactorField':
              initTextEmpty(fieldData, hiddenField);

              //	on anything typed into the richtext editor
              subfieldContainer.keyup(function (event) {
                updateHidden(fieldData.key, $($(event.target).closest('.redactor-in')).html(), hiddenField);
              });

              //	on changed element
              const redactorObserver = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                  updateHidden(fieldData.key, $($(mutation.target).closest('.redactor-in')).html(), hiddenField);
                });
              });
              redactorObserver.observe(subfieldContainer[0], {
                childList: true,
                subtree: true,
              });
              break;

            case 'selectField':
              initTextEmpty(fieldData, hiddenField);

              //	on selection-change
              subfield.change(function(event) {
                updateHidden(fieldData.key, subfield.val(), hiddenField);
              });
              break;

            case 'textareaField':
              initTextEmpty(fieldData, hiddenField);

              //	on anything typed into the textarea
              subfield.keyup(function(event) {
                updateHidden(fieldData.key, subfield.val(), hiddenField);
              });
              break;

            case 'textField':
              initTextEmpty(fieldData, hiddenField);

              //	on anything typed into the textfield
              subfield.keyup(function(event) {
                updateHidden(fieldData.key, subfield.val(), hiddenField);
              });
              break;

            case 'timeField':
              initTextEmpty(fieldData, hiddenField);

              let timeFields = subfieldContainer.find('input[type=text]');
              if (timeFields.length) {
                let timeField = $(timeFields.get(0));

                //  on time-select
                timeField.change(function(event) {
                  updateHidden(fieldData.key, timeField.val(), hiddenField);
                });
                //	on anything typed into the time field's text input
                timeField.keyup(function(event) {
                  updateHidden(fieldData.key, timeField.val(), hiddenField);
                });
              }
              break;

          }

          //  recursively init subfields of current field
          if (fieldData.subfields !== undefined && fieldData.subfields.length > 0) {
            for (let i = 0; i < fieldData.subfields.length; i++) {
              /**
               * @var subfieldData is the result of Vierbeuter\Craft\Field\Subfield->toArray()
               */
              let subfieldData = fieldData.subfields[i];

              initSubfield(subfieldData, subfield);
            }
          }
        };

        //  determine all subfields for current module field
        for (let i = 0; i < _this.options.subfields.length; i++) {
          /**
           * @var subfieldData is the result of Vierbeuter\Craft\Field\Subfield->toArray()
           */
          let subfieldData = _this.options.subfields[i];

          initSubfield(subfieldData, hiddenField);
        }
      });
    },
  };

  // A really lightweight plugin wrapper around the constructor,
  // preventing against multiple instantiations
  $.fn[pluginName] = function(options) {
    return this.each(function() {
      if (!$.data(this, 'plugin_' + pluginName)) {
        $.data(this, 'plugin_' + pluginName,
          new Plugin(this, options));
      }
    });
  };

})(jQuery, window, document);
