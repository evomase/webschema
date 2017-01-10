(function () {
    class Schema {
        constructor() {
            let data = null,
                template = '',
                dialog = null;

            function render(template, node) {
                load();

                let schema = getNodeSchema(node);
                let container = document.createElement('div');
                container.innerHTML = template;

                console.log(schema);

                for (let type of Object.keys(data.types)) {
                    type = data.types[type];

                    let id = type['id'];

                    let option = document.createElement('option');
                    option.innerText = type['label'];
                    option.setAttribute('value', id);

                    if (id == schema) {
                        option.setAttribute('selected', 'selected');
                    }

                    container.querySelector('.schema').appendChild(option);
                }

                return container;
            }

            function getNodeSchema(node) {
                let schema = null;
                let type = node.getAttribute('itemtype');

                if (type) {
                    let regexp = new RegExp('/(\\w+)$');
                    schema = regexp.exec(type)[1];
                }

                return schema;
            }

            function registerEvents() {
                dialog.querySelector('.schema').addEventListener('change', function (e) {
                    eventChangeSchema(e);
                });
            }

            function eventChangeSchema(e) {
                let properties = getSchemaProperties(e.target.value);

                let dummyProperty = dialog.querySelector('.dummy-property .property');
                let options = dummyProperty.querySelectorAll('option');

                for (let option of options) {
                    if (option.getAttribute('value') == '') {
                        continue;
                    }

                    option.remove();
                }

                for (let property of properties) {
                    let option = document.createElement('option');
                    option.setAttribute('value', property['id']);
                    option.textContent = property['label'];

                    dummyProperty.appendChild(option);
                }
            }

            function getSchemaProperties(schema) {
                let properties = [];

                if (data.types.hasOwnProperty(schema)) {
                    schema = data.types[schema];

                    for (let property of schema['properties']) {
                        properties.push(data.properties[property]);
                    }
                }

                return properties;
            }

            function save(ed) {
                let node = ed.selection.getNode();
                let schema = dialog.querySelector('.schema').value;
                let url = data.types[schema]['url'];

                if (node.textContent != ed.selection.getContent()) {
                    ed.selection.setContent('<span itemscope itemtype="' + url + '">' + ed.selection.getContent() + '</span>');
                }
                else {
                    node.setAttribute('itemscope', '');
                    node.setAttribute('itemtype', url);
                }
            }

            function getTemplate() {
                if (!template.length) {
                    template = ajax('schema_get_template', false);
                }

                return template;
            }

            function ajax(action, async) {
                let xhr = new XMLHttpRequest();
                let data = null;

                xhr.open('GET', window.ajaxurl + '?action=' + action, async);
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                        data = xhr.responseText;
                    }
                };

                xhr.send();

                return data;
            }

            function load() {
                if (data == null) {
                    data = JSON.parse(ajax('schema_get_all', false));
                }
            }

            function open(ed) {
                let node = ed.selection.getNode();
                let container = render(getTemplate(), node);

                let window = ed.windowManager.open({
                    title: 'Web Schema',
                    html: container.innerHTML,
                    width: 680,
                    buttons: [
                        {
                            text: 'OK',
                            subtype: 'primary',
                            onclick: function () {
                                save(ed);
                                window.close();
                            }
                        },
                        {
                            text: 'Cancel',
                            onclick: function () {
                                window.close();
                            }
                        }
                    ]
                });

                dialog = document.querySelector('#' + window._id);

                registerEvents();
            }

            this.init = function () {
                tinymce.create('tinymce.plugins.WebSchema', {
                    WebSchema: function (ed, url) {
                        ed.addCommand('webschema.open', function () {
                            open(ed);
                        });

                        ed.addButton('webschema', {
                            title: 'Add/Edit Schema',
                            cmd: 'webschema.open'
                        });

                        ed.on('init', function () {
                            let css = document.createElement('link');
                            css.setAttribute('rel', 'stylesheet');
                            css.setAttribute('href', url + '/css/webschema.css?' + tinymce.Env.cacheSuffix);

                            document.querySelector('head').appendChild(css);

                            //add itemscope and itemtype to valid element attribute
                            //ed.schema.addValidElements('@[itemscope|itemtype],span');
                            //console.log(ed.schema);
                        });
                    }
                });

                tinymce.PluginManager.add('webschema', tinymce.plugins.WebSchema);
            };
        };
    }

    (new Schema()).init();
})();