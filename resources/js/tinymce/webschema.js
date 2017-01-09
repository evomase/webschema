(function () {
    var Schema = function () {
        var data = null;
        var template = '';
        var dialog = null;

        this.init = function () {
            var self = this;

            tinymce.create('tinymce.plugins.WebSchema', {
                WebSchema: function (ed, url) {
                    ed.addCommand('webschema.open', function () {
                        self.open(ed);
                    });

                    ed.addButton('webschema', {
                        title: 'Add/Edit Schema',
                        cmd: 'webschema.open'
                    });

                    ed.on('init', function () {
                        var css = document.createElement('link');
                        css.setAttribute('rel', 'stylesheet');
                        css.setAttribute('href', url + '/css/webschema.css?' + tinymce.Env.cacheSuffix);

                        document.querySelector('head').appendChild(css);

                        self.open(ed);
                    });
                }
            });

            tinymce.PluginManager.add('webschema', tinymce.plugins.WebSchema);
        };

        this.open = function (ed) {
            var node = ed.selection.getNode();
            var container = render(getTemplate());

            dialog = ed.windowManager.open({
                title: 'Web Schema',
                html: container.innerHTML,
                width: 530
            });

            dialog = document.querySelector('#' + dialog._id);

            registerEvents();
        };

        var render = function (template) {
            load();

            var container = document.createElement('div');
            container.innerHTML = template;

            for (var type in data.types) {
                var option = document.createElement('option');
                option.innerText = data.types[type]['label'];
                option.setAttribute('value', type);

                container.querySelector('.schema').appendChild(option);
            }

            return container;
        };

        var registerEvents = function () {
            dialog.querySelector('.schema').addEventListener('change', function (e) {
                console.log(e);
            });
        };

        var getTemplate = function () {
            if (!template.length) {
                template = ajax('schema_get_template', false);
            }

            return template;
        };

        var ajax = function (action, async) {
            var xhr = new XMLHttpRequest();
            var data = null;

            xhr.open('GET', window.ajaxurl + '?action=' + action, async);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    data = xhr.responseText;
                }
            };

            xhr.send();

            return data;
        };

        var load = function () {
            if (data === null) {
                data = JSON.parse(ajax('schema_get_all', false));
            }
        };
    };

    var schema = new Schema();
    schema.init();
})();