/**
 * Created by David on 21/12/2016.
 */
(function () {
    var Schema = function () {
        var data = [];

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
            var selection = getSelection(ed);
        };

        var getData = function () {
            if (!data) {
                load();
            }

            return data;
        };

        var load = function () {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    
                }
            };
            xhr.send();
        };

        var getSelection = function (ed) {
            var node = ed.selection.getNode();
            var content = ed.selection.getContent();

            if (node.innerText != content) {
                return '<span>' + content + '</span>';
            }

            return node;
        };
    };

    var schema = new Schema();
    schema.init();
})();