/*
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */
if (window.tinymce && reportDataListJson && reportAvailableFieldsListJson && reportAvailableFieldsGlobalJson) {
    tinymce.PluginManager.add('codeupReport', function (editor) {
        var tinymceCodeupReport = {
            currentConditionPath: null,
            currentNode: null,
            currentPath: null,
            dataListToggleToolbar: [],
            currentClassListPath: '',
            currentField: null
        };
        editor.on('NodeChange', function (e) {$('td').tooltip({ trigger: 'hover' })
            tinymceCodeupReport.currentNode = e;
            var element = $(e.element);
            tinymceCodeupReport.currentPath = $(element);
            if (element.hasClass('field') || element.hasClass('mceNonEditable')) {
                var a = tinymce.activeEditor.selection.getNode();
                tinymce.activeEditor.selection.select($(a).closest('.field')[0]);
                tinymceCodeupReport.currentField = element;

            } else {
                tinymceCodeupReport.currentField = null;
            }
            if (element.attr('report-list')) {
                tinymceCodeupReport.currentClassListPath = element
            } else {
                tinymceCodeupReport.currentClassListPath = null
            }
        });
        if (REPORT_PAGE_WIDTH && REPORT_PAGE_HEIGHT) {
            function initBodyStyle(){
                $(tinymce.activeEditor.dom.doc).find('body')
                    .css('width', REPORT_PAGE_WIDTH + 'cm')
                    .css('height', REPORT_PAGE_HEIGHT + 'cm')
                    .css('margin', '0 auto');
                if (REPORT_MARGINS) {
                    $(tinymce.activeEditor.dom.doc).find('body')
                        .css('padding-left', (REPORT_MARGINS.left ? REPORT_MARGINS.left + 'mm' : '1px'))
                        .css('padding-right', (REPORT_MARGINS.right ? REPORT_MARGINS.right + 'mm' : '1px'))
                        .css('padding-top', (REPORT_MARGINS.top ? REPORT_MARGINS.top + 'mm' : '1px'))
                        .css('padding-bottom', (REPORT_MARGINS.bottom ? REPORT_MARGINS.bottom + 'mm' : '1px'))
                }
            }
            editor.on('init', initBodyStyle);
            editor.addButton('toggleWidth', {
                text: 'Toggle Size',
                active: true,
                onclick: function () {
                    if (this.active()) {
                        $(tinymce.activeEditor.dom.doc).find('body')
                            .css('width', 'auto')
                            .css('height', REPORT_PAGE_HEIGHT + 'cm')
                            .css('margin', 'auto')
                        this.active(false)
                    } else {
                        initBodyStyle();
                        this.active(true);
                    }
                }
            });
        }
        $.each(reportDataListJson, function (v, i) {
            editor.addButton('toggleClassLists_' + v, {
                text: '(LIST) ' + v,
                onclick: function () {
                    if (!tinymceCodeupReport.currentPath)
                        return;
                    if (!this.active()) {
                        tinymceCodeupReport.currentPath.attr('report-list', v);
                        this.active(true);
                    } else {
                        tinymceCodeupReport.currentPath.removeAttr('report-list');
                        this.active(false);
                    }
                },
                onpostrender: function () {
                    var btn = this;
                    editor.on('NodeChange', function (e) {

                        if (tinymceCodeupReport.currentPath.closest('[report-list]').attr('report-list')) {
                            if (tinymceCodeupReport.currentPath.closest('[report-list]').attr('report-list') === v) {
                                btn.active(true);
                            } else {
                                btn.active(false);
                            }
                        } else {
                            btn.active(false);
                        }
                    });
                }
            });

        });

        function processMenuItemsList(fields, sub) {
            var menu = [];
            if (typeof sub === 'undefined')
                sub = '';
            $.each(fields, function (key, val) {
                var _sub = sub + key;
                if (isNaN(key)) {
                    var keys = key.split(':');
                    key = keys[0];
                    if (keys[1]) {
                        _sub = keys[1];
                    }
                }
                if (typeof val === 'string') {
                    menu.push({
                        text: val,
                        icon: 'unlock',
                        onclick: function () {
                            editor.insertContent('&nbsp;<span class="field global-field"><field>{%' + sub + val + '%}</field></span>&nbsp;');
                        }
                    })
                } else if (val.constructor === ([]).constructor) {
                    menu.push({
                        text: key,
                        icon: 'unlock',
                        menu: processMenuItemsList(val, _sub + '.')
                    })
                }
            });
            return menu;
        }

        $.each(reportAvailableFieldsListJson, function (list, fields) {
            editor.addButton('fieldList_' + list.replace(/ /g, '-'), {
                type: 'menubutton',
                text: '(LIST Field) ' + list,
                icon: false,
                menu: processMenuItemsList(fields),
                disabled: true,
                onpostrender: function () {
                    var btn = this;
                    editor.on('NodeChange', function (e) {
                        if (tinymceCodeupReport.currentPath.closest('[report-list]').attr('report-list') === list) {
                            btn.disabled(false);
                        } else {
                            btn.disabled(true);
                        }
                    });
                }
            });
        });

        function processMenuItemsGlobal(fields, sub) {
            var menu = [];
            if (typeof sub === 'undefined')
                sub = '';
            $.each(fields, function (key, val) {
                if (typeof val === 'string') {
                    menu.push({
                        text: val,
                        icon: 'unlock',
                        onclick: function () {
                            editor.insertContent('&nbsp;<span class="field global-field"><field>{' + sub + val + '}</field></span>&nbsp;');
                        },

                    })
                } else if (val.constructor === ([]).constructor) {
                    menu.push({
                        text: key,
                        icon: 'unlock',
                        menu: processMenuItemsGlobal(val, sub + key + '.')
                    })
                }
            });
            return menu;
        }

        $.each(reportAvailableFieldsGlobalJson, function (list, fields) {
            editor.addButton('fieldGlobal_' + list.replace(/ /g, '-'), {
                type: 'menubutton',
                text: '(GLOBAL Field) ' + list,
                icon: false,
                menu: processMenuItemsGlobal(fields, list.replace(/ /g, '-') + '.'),
                onpostrender: function () {
                    editor.on('click', function (e) {

                    })
                }
            });
        });

        function processMenuItemsFormatters(fields, sub) {
            var menu = [];
            if (typeof sub === 'undefined')
                sub = '';
            $.each(fields, function (key, val) {
                if (typeof val === 'string') {
                    menu.push({
                        text: val,
                        icon: 'unlock',
                        onclick: function () {
                            var fieldNode = tinymce.activeEditor.selection.getNode();
                            if (fieldNode.innerHTML.search(':' + sub + val) >= 0) {
                                fieldNode.innerHTML = fieldNode.innerHTML.split(':' + sub + val).join('');
                            } else {
                                fieldNode.innerHTML = fieldNode.innerHTML.replace(/{(.*?)}/gi, function (x) {
                                    var lastLi = x.substr(x.length - 2);
                                    if (lastLi === '%}') {
                                        return x.substr(0, x.length - 2) + ':' + sub + val + '%}';
                                    } else {
                                        return x.substr(0, x.length - 1) + ':' + sub + val + '}';
                                    }
                                });
                            }
                            tinymce.activeEditor.selection.setNode(fieldNode);
                        }
                    })
                } else if (val.constructor === ([]).constructor || val.constructor === ({}).constructor) {
                    menu.push({
                        text: key,
                        icon: 'unlock',
                        menu: processMenuItemsFormatters(val, sub + key + '.')
                    })
                }
            });
            return menu;
        }

        editor.addButton('codeup_formatters', {
            type: 'menubutton',
            text: '(Formatters)',
            icon: false,
            menu: processMenuItemsFormatters(reportFormatters),
            disabled: true,
            onpostrender: function () {
                var _self = this;
                editor.on('NodeChange', function (e) {
                    var node = tinymce.activeEditor.selection.getNode();
                    if ($(node).hasClass('field'))
                        _self.disabled(false);
                    else
                        _self.disabled(true)
                    // if(path0.prop("tagName") !=='HTML' && path0.prop("tagName") !== 'BODY'){
                    //     _self.disabled(false);
                    //     tinymceCodeupReport.currentConditionPath = path0;
                    // }else{
                    //     _self.disabled(true);
                    // }
                });
            }
        });

        function processMenuItemsConditions(fields, sub) {
            var menu = [];
            if (typeof sub === 'undefined')
                sub = '';
            $.each(fields, function (key, val) {
                if (typeof val === 'string') {
                    menu.push({
                        text: val,
                        icon: 'unlock',
                        onclick: function () {
                            if (tinymceCodeupReport.currentPath) {
                                if (tinymceCodeupReport.currentPath.attr('condition') && tinymceCodeupReport.currentPath.attr('condition') === val)
                                    tinymceCodeupReport.currentPath.removeAttr('condition');
                                else
                                    tinymceCodeupReport.currentPath.attr('condition', val);
                            }
                        },
                        onpostrender: function () {
                            var _self = this;
                            editor.on('click', function (e) {
                                var closest = tinymceCodeupReport.currentPath.closest('[condition]').attr('condition');
                                if (closest && closest === val) {
                                    _self.active(true);
                                } else {
                                    _self.active(false);
                                }
                            })
                        }
                    })
                } else if (val.constructor === ([]).constructor) {
                    menu.push({
                        text: key,
                        icon: 'unlock',
                        menu: processMenuItemsConditions(val, sub + key + '.')
                    })
                }
            });
            return menu;
        }

        var conditions = processMenuItemsConditions(reportAvailableFieldsConditionJson);
        editor.addButton('codeup_conditions', {
            type: 'menubutton',
            text: '(Conditions)',
            icon: false,
            // disabled:true,
            menu: conditions,
            onpostrender: function () {
                var _self = this;
                editor.on('click', function (e) {
                    var path0 = $(e.path[0]);
                    if (path0.prop("tagName") !== 'HTML' && path0.prop("tagName") !== 'BODY') {
                        _self.disabled(false);
                        tinymceCodeupReport.currentConditionPath = path0;
                    } else {
                        _self.disabled(true);
                    }
                    if (tinymceCodeupReport.currentPath.closest('[condition]').attr('condition')) {
                        _self.active(true);
                    } else {
                        _self.active(false);
                    }
                });
            }
        });

    });
}