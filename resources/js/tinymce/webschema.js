(() => {
    "use strict";

    class WebSchema {
        constructor() {
            let data = null,
                template = '',
                dialog = null,
                editor = null,
                tooltip = null,
                valid_elements = ['p', 'span', 'ul', 'ol', 'li', 'h1', 'h2', 'h3', 'h4', 'pre'],
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
                let schemas = Object.keys(data.types).map((schema) => {
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

            function eventChangeMetaProperty(e) {
                changeMetaProperty(e.target.value);
            }

            function eventChangeSchema(e) {
                changeSchema(e.target.value);
            }

            function eventAddMeta(e) {
                e.preventDefault();
                addMeta();
                resizeDialog();
            }

            function eventRemoveMeta(e) {
                e.preventDefault();
                removeMeta(e.target.parentNode);
                resizeDialog();
            }

            function resizeDialog() {
                //recalculate the window dimensions etc
                let window = editor.windowManager.getWindows()[0];

                let rect = dialog.querySelector('.web-schema').getBoundingClientRect();
                dialog.querySelector('.mce-window-body').style.height = rect.height + 'px';
                dialog.style.height = 'auto';

                window.initLayoutRect();
                window.reflow();
            }

            function registerEvents() {
                dialog.querySelector('.schema').addEventListener('change', eventChangeMetaProperty);
                dialog.querySelector('.parent-property').addEventListener('change', eventChangeSchema);
                dialog.querySelector('.metas h3 a').addEventListener('click', eventAddMeta);

                let metas = dialog.querySelectorAll('.metas .meta a');

                metas.forEach((element) => {
                    element.addEventListener('click', eventRemoveMeta);
                })
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
                    meta.querySelector('.value').textContent = value;
                }

                meta.querySelector('.remove').addEventListener('click', eventRemoveMeta);

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

                ancestors.forEach((ancestor) => {
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
                            element.setAttribute('itemprop', meta.querySelector('select').value);
                            element.setAttribute('content', meta.querySelector('.value').value);

                            node.insertBefore(element, node.firstChild);
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

            function removeSchemaAttributes(element) {
                if (element && element.hasAttribute('itemscope')) {
                    element.removeAttribute('itemtype');
                    element.removeAttribute('itemscope');
                    element.removeAttribute('itemprop');
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

            function renderTooltip(e) {
                let element = e.target;

                if (element.hasAttribute('itemscope') || element.hasAttribute('itemprop')) {
                    e.stopPropagation();

                    if (!tooltip) {
                        generateTooltip();
                    }

                    tooltip.show(element);
                }
                else {
                    if (tooltip) {
                        tooltip.hide();
                    }
                }
            }

            function generateTooltip() {
                tooltip = new tinymce.ui.WebSchemaToolTip();
                tooltip.renderTo();
            }

            function load() {
                if (data == null) {
                    data = JSON.parse(ajax('schema_get_all', false));
                }
            }

            function addValidElements() {
                let elements, children;

                elements = valid_elements.map((element) => {
                    return element + '[itemscope|itemtype|itemprop]';
                });

                elements = elements.join(',') + ',meta[itemprop|content]';

                //add valid elements
                editor.schema.addValidElements(elements);

                children = valid_elements.map((element) => {
                    return '+' + element + '[meta]';
                });

                children = children.join(',');

                //add valid children
                editor.schema.addValidChildren(children);
            }

            function open() {
                let node = editor.selection.getNode();
                let container = render(getTemplate(), node);

                let window = editor.windowManager.open({
                    title: 'Web Schema',
                    html: container.innerHTML,
                    width: 730,
                    classes: 'web-schema-container',
                    buttons: [
                        {
                            text: 'OK',
                            subtype: 'primary',
                            onclick: () => {
                                save();
                                close();

                                window.close();
                            }
                        },
                        {
                            text: 'Cancel',
                            onclick: () => {
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
                dialog.querySelector('.schema').removeEventListener('change', eventChangeMetaProperty);
                dialog.querySelector('.parent-property').removeEventListener('change', eventChangeSchema);
                dialog.querySelector('.metas h3 a').removeEventListener('click', eventAddMeta);
            }

            this.init = () => {
                tinymce.create('tinymce.plugins.WebSchema', {
                    WebSchema: function (ed, url) {
                        editor = ed;

                        editor.addCommand('webschema.open', () => {
                            open();
                        });

                        editor.addButton('webschema', {
                            title: 'Add/Edit Schema',
                            cmd: 'webschema.open',
                            onPostRender: function () {
                                this.getEl().querySelector('i').classList.add('mce-i-dashicon');
                            }
                        });

                        editor.on('init', () => {
                            let link = url + '/css/webschema.css?';
                            let css = document.createElement('link');
                            css.setAttribute('rel', 'stylesheet');
                            css.setAttribute('href', link + tinymce.Env.cacheSuffix);

                            //load css to head of top document
                            document.querySelector('head').appendChild(css);

                            //load css to content
                            editor.contentCSS.push(link);

                            load();
                        });

                        editor.on('newblock', (e) => {
                            removeSchemaAttributes(e.newBlock);
                        });

                        editor.on('mouseover', (e) => {
                            renderTooltip(e);
                        });

                        editor.on('preinit', () => {
                            addValidElements();
                        });
                    }
                });

                //add plugin
                tinymce.PluginManager.add('webschema', tinymce.plugins.WebSchema);

                //add tooltip
                tinymce.ui.WebSchemaToolTip = tinymce.ui.Tooltip.extend({
                    element: null,

                    renderHtml: function () {
                        return '<div id="' + this._id + '" class="mce-panel mce-inline-toolbar-grp mce-container webschema-tooltip">' +
                            '<div class="mce-container-body"><p></p></div></div>';
                    },

                    show: function (element) {
                        this.setContent(element);
                        this.moveRel(element, ['bc-tc']);

                        let rect = editor.container.querySelector('iframe').getBoundingClientRect();
                        this.moveBy(rect.x, rect.y);

                        this._super();
                    },

                    postRender: function () {
                        this._super();

                        this.registerEvents();
                    },

                    setContent: function (element) {
                        this.element = element;

                        let content = [];
                        let schema = getNodeSchema(element);

                        if (schema) {
                            Array.prototype.push.apply(content, this.setSchemaContent(element, schema));
                        }

                        Array.prototype.push.apply(content, this.setPropertyContent(element, (schema)));

                        content = content.filter((e) => {
                            return e;
                        });

                        content.push('<a href="#">[edit]</a>');

                        this.getEl().querySelector('p').innerHTML = content.join('<br />');
                    },

                    setSchemaContent: function (element, schema) {
                        let parent = getNodeSchema(element.parentNode);
                        let text = 'The schema type for this content is "' + data.types[schema]['label'] + '".';

                        if (parent) {
                            text += ' It also belongs within "' + data.types[parent]['label'] + '".';
                        }

                        return [text];
                    },

                    setPropertyContent: function (element, hideSchema) {
                        let property = getNodeProperty(element);
                        let text = null;

                        if (property) {
                            text = 'The schema property for this content is "' + data.properties[property]['label'] + '".';

                            if (!hideSchema) {
                                let schema = getNodeSchema(element.parentNode);
                                text += ' It also belongs within "' + schema + '".';
                            }
                        }

                        return [text];
                    },

                    registerEvents: function () {
                        this.getEl().querySelector('p').addEventListener('click', (e) => {
                            if (e.target.nodeName === 'A') {
                                e.preventDefault();

                                editor.selection.select(this.element);
                                editor.execCommand('webschema.open');

                                this.hide();
                            }
                        });
                    }
                });
            };
        };
    }

    (new WebSchema()).init();
})();