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

                //  TODO: get all field settings from _this.options.subfields!
                console.log(_this.options.subfields);	//	DEBUG: remove me!

                let hiddenField = $('#' + _this.options.namespace);
                let textField = $('#' + _this.options.namespace + 'Text');
                let entryField = $('#' + _this.options.namespace + 'Entry');

                let updateHidden = function () {
                    let entryHiddenField = entryField.find('input[type=hidden]');
                    let value = {
                        text: textField.val(),
                        entry: entryHiddenField.length ? $(entryHiddenField.get(0)).val() : '',
                    };
                    hiddenField.val(JSON.stringify(value));
                };

                //	on anything typed into the textfield
                textField.keyup(function (event) {
                    updateHidden();
                });

                //	on changed entry selection
                //  FIXME: mutation events are deprecated --> use MutationObservers instead!
                entryField.on('DOMNodeInserted', function (event) {
                    updateHidden();
                });
                //  TODO: also updateHidden() on removed elements

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
