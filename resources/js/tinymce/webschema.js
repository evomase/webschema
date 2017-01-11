(function () {
    class Schema {
        constructor() {
            let data = null,
                template = '',
                dialog = null,
                editor = null,
                messages = {
                    select_schema: 'Please select a schema from the list below',
                    select_property: 'Due to this selection being directly within ":parent", please select a property below and add its schema'
                };

            function render(template, node) {
                load();

                let schema = null;
                let parentSchema = null;

                if (node.innerHTML == editor.selection.getContent()) {
                    schema = getNodeSchema(node);
                    parentSchema = getNodeSchema(node.parentNode);
                    console.log('reached');
                }
                else {
                    parentSchema = getNodeSchema(node);
                }

                let container = document.createElement('div');
                container.innerHTML = template;

                renderParentInfo(container, parentSchema);

                if (!parentSchema) {
                    renderInfo(container, schema);
                }

                return container;
            }

            function renderInfo(container, schema) {
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

                let message = container.querySelector('#message p');
                message.textContent = messages.select_schema;
            }

            function renderParentInfo(container, parentSchema) {
                if (parentSchema) {
                    let properties = getSchemaProperties(parentSchema);
                    let parentProperty = container.querySelector('.parent-property');

                    addPropertyOptions(parentProperty, properties);

                    parentSchema = data.types[parentSchema]['label'];

                    container.querySelector('.parent-info').style.display = 'block';
                    container.querySelector('.parent').textContent = '[Parent: ' + parentSchema + ']';

                    let message = container.querySelector('#message p');
                    message.textContent = messages.select_property.replace(':parent', parentSchema);
                }
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

                dialog.querySelector('.parent-property').addEventListener('change', function (e) {
                    eventChangeParentProperty(e);
                });
            }

            function eventChangeSchema(e) {
                let properties = getSchemaProperties(e.target.value);
                let element = dialog.querySelector('.dummy-property .property');

                for (let option of element.querySelectorAll('option')) {
                    if (option.getAttribute('value') == '') {
                        continue;
                    }

                    option.remove();
                }

                addPropertyOptions(element, properties);
            }

            function eventChangeParentProperty(e) {
                let types = getPropertyTypes(e.target.value);
            }

            function getPropertyTypes(property) {
                let types = [];

                if (data.properties[property]) {

                }
            }

            function addPropertyOptions(element, properties) {
                for (let property of properties) {
                    let option = document.createElement('option');
                    option.setAttribute('value', property['id']);
                    option.textContent = property['label'];

                    element.appendChild(option);
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

            function save() {
                let node = editor.selection.getNode();
                let schema = dialog.querySelector('.schema').value;
                let url = data.types[schema]['url'];

                if (node.innerHTML == editor.selection.getContent()) {
                    node.setAttribute('itemscope', '');
                    node.setAttribute('itemtype', url);
                }
                else {
                    editor.selection.setContent('<span itemscope itemtype="' + url + '">' + editor.selection.getContent() + '</span>');
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

            function open() {
                let node = editor.selection.getNode();
                let container = render(getTemplate(), node);

                let window = editor.windowManager.open({
                    title: 'Web Schema',
                    html: container.innerHTML,
                    width: 680,
                    buttons: [
                        {
                            text: 'OK',
                            subtype: 'primary',
                            onclick: function () {
                                save();
                                close();

                                window.close();
                            }
                        },
                        {
                            text: 'Cancel',
                            onclick: function () {
                                close();

                                window.close();
                            }
                        }
                    ]
                });

                dialog = document.querySelector('#' + window._id);

                registerEvents();
            }

            function close() {
                //deregisterEvents();
            }

            this.init = function () {
                tinymce.create('tinymce.plugins.WebSchema', {
                    WebSchema: function (ed, url) {
                        editor = ed;

                        editor.addCommand('webschema.open', function () {
                            open();
                        });

                        editor.addButton('webschema', {
                            title: 'Add/Edit Schema',
                            cmd: 'webschema.open'
                        });

                        editor.on('init', function () {
                            let css = document.createElement('link');
                            css.setAttribute('rel', 'stylesheet');
                            css.setAttribute('href', url + '/css/webschema.css?' + tinymce.Env.cacheSuffix);

                            document.querySelector('head').appendChild(css);

                            //add itemscope and itemtype to valid element attribute
                            //ed.schema.addValidElements('@[itemscope|itemtype],span');
                            // console.log(ed.schema);
                        });
                    }
                });

                tinymce.PluginManager.add('webschema', tinymce.plugins.WebSchema);
            };
        };
    }

    (new Schema()).init();
})();