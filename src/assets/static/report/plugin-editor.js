/*
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */
if (window.tinymce && reportDataListJson && reportAvailableFieldsListJson && reportAvailableFieldsGlobalJson) {
    tinymce.PluginManager.add('codeupReport', function (editor) {
        var tinymceCodeupReport = {
            currentPath: null,
            dataListToggleToolbar: [],
            currentClassListPath: '',
            currentField: null
        };
        reportDataListJson.forEach(function (v, i) {
            tinymceCodeupReport.dataListToggleToolbar[v] = editor.ui.registry.addToggleButton('toggleClassLists_' + v, {
                text: '(LIST) ' + v,
                onAction: function (api) {
                    if (!tinymceCodeupReport.currentClassListPath)
                        return;
                    if (!api.isActive()) {
                        $(tinymceCodeupReport.currentClassListPath).attr('report-list', v);
                    } else {
                        $(tinymceCodeupReport.currentClassListPath).removeAttr('report-list');
                    }
                },
                onSetup: function (api) {
                    editor.on('click', function (e) {
                        if ($(e.path[0]).hasClass('field')) {
                            tinymceCodeupReport.currentField = $(e.path[0]);
                        } else {
                            tinymceCodeupReport.currentField = null;
                        }
                        if ($(e.path[0]).attr('report-list') && $(e.path[0]).attr('report-list') === v) {
                            tinymceCodeupReport.currentClassListPath = e.path[0];
                            api.setActive(true);
                        } else if ($(e.path[1]).attr('report-list') && $(e.path[1]).attr('report-list') === v) {
                            //                        tinymceCodeupReport.currentClassListPath = e.path[1];
                            api.setActive(true);
                        } else {
                            tinymceCodeupReport.currentClassListPath = e.path[0];
                            api.setActive(false);
                        }
                    })
                },
            });
        });
        $.each(reportAvailableFieldsListJson, function (list, fields) {
            editor.ui.registry.addMenuButton('fieldList_' + list, {
                text: '(LIST Field) ' + list,
                fetch: function (callback) {
                    var menu = [];
                    fields.forEach(function (v, i) {
                        menu.push({
                            type: 'menuitem',
                            text: v,
                            icon: 'unlock',
                            onAction: function () {
                                editor.insertContent('<span class="field global-field">{%' + v + '%}</span>');
                            }
                        })
                    });
                    callback(menu);
                },
                onSetup: function (api) {
                    api.setDisabled(true);
                    editor.on('click', function (e) {
                        if ($(e.path[0]).attr('report-list') && $(e.path[0]).attr('report-list') === list) {
                            api.setDisabled(false);
                        } else if ($(e.path[1]).attr('report-list') && $(e.path[1]).attr('report-list') === list) {
                            api.setDisabled(false);
                        } else {
                            api.setDisabled(true);
                        }
                    })
                }

            });
        });
        $.each(reportAvailableFieldsGlobalJson, function (list, fields) {
            if (typeof fields === 'object') {
                editor.ui.registry.addMenuButton('fieldGlobal_' + list.replace(/ /g, '-'), {
                    text: '(GLOBAL Field) ' + list,
                    fetch: function (callback) {
                        var menu = [];
                        fields.forEach(function (v, i) {
                            menu.push({
                                type: 'menuitem',
                                text: v,
                                icon: 'unlock',
                                onAction: function () {
                                    editor.insertContent('<span class="field global-field">{' + list + '.' + v + '}</span>');
                                }
                            })
                        });
                        callback(menu);
                    },
                    onSetup: function (api) {
                    }

                });
            }
        })
        $.each(reportFormatters, function (list, fields) {
            if (typeof fields === 'object' && fields.constructor === ({}).constructor) {
                editor.ui.registry.addMenuButton('formatters' + list.replace(/ /g, '-'), {
                    text: '(Formatter) ' + list,
                    fetch: function (callback) {
                        var menu = [];
                        Object.keys(fields).forEach(function (v, i) {
                            menu.push({
                                type: 'menuitem',
                                text: v,
                                icon: 'unlock',
                                onAction: function () {
                                    var field = tinymceCodeupReport.currentField.html();
                                    var res = field.replace(/{(.*?)}/gi, function (x) {
                                        var lastLi = x.substr(x.length - 2);
                                        if (lastLi === '%}') {
                                            return x.substr(0, x.length - 2) + ':' + v + '%}';
                                        }else{
                                            return x.substr(0, x.length - 1) + ':' + v + '}';
                                        }
                                    });
                                    tinymceCodeupReport.currentField.html(res);
                                },
                                onSetup: function (api) {
                                }
                            })
                        });
                        callback(menu);
                    },
                    onSetup: function (api) {
                        api.setDisabled(true);
                        editor.on('click', function (e) {
                            if (tinymceCodeupReport.currentField) {
                                api.setDisabled(false);
                            } else {
                                api.setDisabled(true);
                            }
                        })
                    }

                });
            }
        })
    });
}