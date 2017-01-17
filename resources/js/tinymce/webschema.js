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

            /**
             *
             * @param template
             * @param node node is a clone of original element
             * @returns {Element}
             */
            function render(template, node) {
                load();

                let parentSchema = null;

                if (!isNew(node)) {
                    parentSchema = getNodeSchema(node.parentNode);
                }
                else {
                    //new selected content/text with no schema
                    parentSchema = getNodeSchema(node);
                    node = null;
                }

                let container = document.createElement('div');
                container.innerHTML = template;

                renderParentInfo(container, parentSchema, node);

                return container;
            }

            function renderInfo(container, schema) {
                let schemas = Object.keys(data.types).map(function (schema) {
                    return data.types[schema];
                });

                addOptions(container.querySelector('.schema'), schemas, schema);

                let message = container.querySelector('#message p');
                message.textContent = messages.select_schema;
            }

            function renderParentInfo(container, parentSchema, node) {
                let schema = getNodeSchema(node);

                if (parentSchema) {
                    let property = getNodeProperty(node);

                    //if schema is a DataType then get it from property's ranges as its not added to the element attribute
                    if (!schema && property) {
                        schema = data.properties[property]['ranges'][0];
                    }

                    let properties = getSchemaProperties(parentSchema);
                    let parentProperty = container.querySelector('.parent-property');

                    addOptions(parentProperty, properties, property);

                    parentSchema = data.types[parentSchema]['label'];

                    container.querySelector('.parent-info').style.display = 'block';
                    container.querySelector('.parent').textContent = '[Parent: ' + parentSchema + ']';

                    let message = container.querySelector('#message p');
                    message.textContent = messages.select_property.replace(':parent', parentSchema);

                    changeSchema(property, schema, container);
                }
                else {
                    renderInfo(container, schema);
                }

                changeMetaProperty(schema, container);
                addMetas(node, container);
            }

            function getNodeSchema(node) {
                let schema = null;

                if (node) {
                    let type = node.getAttribute('itemtype');

                    if (type) {
                        let regexp = new RegExp('/(\\w+)$');
                        schema = regexp.exec(type)[1];
                    }
                }

                return schema;
            }

            function getNodeProperty(node) {
                return (node && node.hasAttribute('itemprop')) ? node.getAttribute('itemprop') : null;
            }

            function registerEvents() {
                dialog.querySelector('.schema').addEventListener('change', function (e) {
                    changeMetaProperty(e.target.value);
                });

                dialog.querySelector('.parent-property').addEventListener('change', function (e) {
                    changeSchema(e.target.value);
                });

                dialog.querySelector('.metas a').addEventListener('click', function (e) {
                    e.preventDefault();
                    addMeta();
                });
            }

            function changeMetaProperty(schema, window = dialog) {
                let properties = getSchemaProperties(schema);
                let elements = window.querySelectorAll('.metas .property');

                //only add URL or Text meta properties
                properties = properties.filter(isDataType);

                elements.forEach((element) => {
                    removeOptions(element);
                    addOptions(element, properties);
                });
            }

            function addMetas(node, window = dialog) {
                if (node) {
                    let metas = node.querySelectorAll('meta');

                    metas.forEach((meta) => {
                        addMeta(meta.getAttribute('itemprop'), meta.getAttribute('content'), window);
                    });
                }
            }

            function addMeta(property = null, value = '', window = dialog) {
                let metas = window.querySelector('.metas');
                let meta = metas.querySelector('.hide').cloneNode(true);

                meta.classList.remove('hide');
                meta.classList.add('meta');

                if (property && value) {
                    meta.querySelector('select option[value=' + property + ']').setAttribute('selected', 'selected');
                    meta.querySelector('.value').setAttribute('value', value);
                }

                meta.querySelector('.remove').addEventListener('click', function (e) {
                    e.preventDefault();

                    removeMeta(e.target.parentNode);
                });

                metas.appendChild(meta);
            }

            function removeMeta(element) {
                element.remove();
            }

            function changeSchema(property, schema = null, window = dialog) {
                let element = window.querySelector('.schema');
                let data = getPropertySchemas(property);
                let schemas = [];

                data.forEach((schema) => {
                    let data = getSchemaFlatTree(locateSchema(schema));
                    Array.prototype.push.apply(schemas, data);
                });

                schemas.sort((a, b) => {
                    return a['label'].localeCompare(b['label']);
                });

                removeOptions(element);
                addOptions(element, schemas, schema);
            }

            function getSchemaFlatTree(tree) {
                let schemas = [tree];
                let children = Object.keys(tree['children']);

                if (children.length) {
                    for (let index of children) {
                        let data = getSchemaFlatTree(tree['children'][index]);
                        Array.prototype.push.apply(schemas, data);
                    }
                }

                return schemas;
            }

            function locateSchema(schema) {
                //clone objects to reuse
                let ancestors = data.types[schema]['ancestors'].slice(0);
                let tree = JSON.parse(JSON.stringify(data.tree));

                if (ancestors.length) {
                    tree = tree[ancestors[0]];

                    ancestors.push(schema);
                    ancestors.splice(0, 1);
                }
                else {
                    tree = tree[schema];
                }

                ancestors.forEach(function (ancestor) {
                    tree = tree['children'][ancestor];
                });

                return tree;
            }

            function getPropertySchemas(property) {
                let schemas = [];

                if (data.properties[property]) {
                    schemas = data.properties[property]['ranges'];
                }

                return schemas;
            }

            function addOptions(element, items, selected = null) {
                for (let item of items) {
                    let option = document.createElement('option');
                    option.setAttribute('value', item['id']);
                    option.textContent = item['label'];

                    if (selected == item['id']) {
                        option.setAttribute('selected', 'selected');
                    }

                    element.appendChild(option);
                }
            }

            function removeOptions(element) {
                for (let option of element.querySelectorAll('option')) {
                    if (option.getAttribute('value') == '') {
                        continue;
                    }

                    option.remove();
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

            function isNew(node) {
                return (node.textContent != editor.selection.getContent({format: 'text'}));
            }

            function save() {
                let node = editor.selection.getNode();
                let schema = dialog.querySelector('.schema').value;
                let property = dialog.querySelector('.parent-property').value;
                let metas = dialog.querySelectorAll('.metas .meta');

                //new schema added
                let fresh = isNew(node);

                if (fresh) {
                    node = document.createElement('span');
                    node.innerHTML = editor.selection.getContent();
                }

                if (schema) {
                    let url = data.types[schema]['url'];

                    node.setAttribute('itemscope', '');
                    node.setAttribute('itemtype', url);

                    if (property) {

                        if (isDataType(data.properties[property])) {
                            node.removeAttribute('itemscope');
                            node.removeAttribute('itemtype');
                        }

                        node.setAttribute('itemprop', property);
                    }

                    if (fresh) {
                        editor.selection.setContent(node.outerHTML);
                    }
                    else {
                        node.querySelectorAll('meta').forEach((element) => {
                            element.remove();
                        });
                    }

                    metas.forEach((meta) => {
                        let element = document.createElement('meta');
                        let name = meta.querySelector('select').value;
                        let value = meta.querySelector('.value').value;

                        if (name && value) {
                            element.setAttribute('name', meta.querySelector('select').value);
                            element.setAttribute('content', meta.querySelector('.value').value);

                            node.appendChild(element);
                        }
                    });
                }
            }

            function isDataType(property) {
                for (let type of property['ranges']) {
                    if (data.types[type]['ancestors'][0] == 'DataType') {
                        return true;
                    }
                }

                return false;
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
                            let link = url + '/css/webschema.css?';
                            let css = document.createElement('link');
                            css.setAttribute('rel', 'stylesheet');
                            css.setAttribute('href', link + tinymce.Env.cacheSuffix);

                            document.querySelector('head').appendChild(css);

                            editor.contentCSS.push(link);
                        });

                        editor.on('NodeChange', function (e) {
                            if (e.selectionChange && e.element.tagName == 'BR' && e.element.hasAttribute('data-mce-bogus')
                                && e.element.parentNode.hasAttribute('itemscope')) {
                                e.element.parentNode.removeAttribute('itemtype');
                                e.element.parentNode.removeAttribute('itemscope');
                                e.element.parentNode.removeAttribute('itemprop');
                            }
                        })
                    }
                });

                tinymce.PluginManager.add('webschema', tinymce.plugins.WebSchema);
            };
        };
    }

    (new Schema()).init();
})();