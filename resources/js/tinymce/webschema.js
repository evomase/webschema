(function () {
    var Schema = function () {
        var data = [];
        var dataURL = window.ajaxurl + '?action=schema_get_all';

        this.init = function () {
            var self = this;

            tinymce.PluginManager.add('webschema', function (ed) {
                ed.addCommand('webschema.open', function () {
                    self.open(ed);
                });

                ed.addButton('webschema', {
                    title: 'Add/Edit Schema',
                    cmd: 'webschema.open'
                });
            });
        };

        this.open = function (ed) {
            var node = ed.selection.getNode();
        };

        var getElementSchema = function () {

        };

        var getData = function () {
            if (!data.length) {
                load();
            }

            return data;
        };

        var load = function () {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', dataURL, false);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    data = xhr.responseText;
                }
            };
            xhr.send();
        };
    };

    var schema = new Schema();
    schema.init();
})();