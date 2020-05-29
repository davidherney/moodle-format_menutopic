// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * JavaScript library for the menutopic course format.
 *
 * @package format_menutopic
 * @copyright 2018 David Herney Bernal - cirano
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 /**
  * @module format_menutopic/menutopictree
  */
define(['jquery', 'core/str', 'core/notification', 'core/modal_factory', 'core/modal_events'],
       function($, Str, Notification, ModalFactory, ModalEvents) {


    var treeController = {
        "codeSelector": '#id_treecode',
        "containerSelector": '#treecontainer'
    };

    var lstr = [];

    var $editWindow = null;

    var $currentNode = null;

    treeController.init = function() {
        var loaded = treeController.loadTreeAria();

        var strings = [
            {'key': 'new'},
            {'key': 'delete'},
            {'key': 'actiondeleteconfirm_sheet_sheetedit', 'component': 'format_menutopic'},
            {'key': 'yes'},
            {'key': 'no'},
            {'key': 'update'}
        ];

        $.each(strings, function(k, v) {
            lstr[v.key] = '...';
        });

        Str.get_strings(strings).done(function(s) {
            $.each(strings, function(k, v) {
                lstr[v.key] = s[k];
            });
        });

        if (loaded) {

            var editBody = $('#editsheetform').html();
            ModalFactory.create({
                type: ModalFactory.types.SAVE_CANCEL,
                title: lstr.update,
                body: editBody
            })
            .done(function(modal) {
                var $modalBody = modal.getBody();
                $modalBody.find('#select_topic').on('change', treeController.changeTopic);
                modal.getRoot().on(ModalEvents.save, function() {
                    $currentNode.find('>input').val($modalBody.find('#name_text').val());
                    $currentNode.data('topic', $modalBody.find('#select_topic').val());
                    $currentNode.data('url', $modalBody.find('#url_text').val());
                    $currentNode.data('target', $modalBody.find('#select_target').val());
                });
                $editWindow = modal;
            });

            $('#id_submitbutton').on('click', treeController.saveTreeConfig);

            return true;
        }

        return false;
    };

    treeController.runAction = function(action, $node) {
        var $group;
        var $prev;
        switch (action) {
            case 'toleft':
                var $parent = $node.parents('li:first');
                $parent.after($node);

                $group = $parent.children('ul');
                if ($group.children() == 0) {
                    $group.remove();
                }

                break;
            case 'toright':
                $prev = $node.prev('li');
                $group = $prev.children('ul');
                if ($group.length == 0) {
                    $group = $('<ul role="group"></ul>');
                    $prev.append($group);
                }

                $group.append($node);

                break;
            case 'todown':
                var $next = $node.next('li');
                $next.after($node);

                break;
            case 'toup':
                $prev = $node.prev('li');
                $prev.before($node);

                break;
            case 'toremove':
                Notification.confirm(lstr.delete, lstr.actiondeleteconfirm_sheet_sheetedit, lstr.yes, lstr.no,
                    function() {
                        $node.remove();
                    }
                );

                break;
            case 'toadd':
                var obj = {
                    'name': lstr.new,
                    'topicnumber': 0,
                    'url': '',
                    'target': ''
                };

                var $newNode = treeController.newTreeNode(obj, '');
                $newNode.find('[data-action]').on('click', function() {
                    var $this = $(this);
                    treeController.runAction($this.attr('data-action'), $this.parent().parent());
                });

                $group = $node.children('ul');
                if ($group.length == 0) {
                    $group = $('<ul role="group"></ul>');
                    $node.append($group);
                }

                $group.append($newNode);

                break;
            case 'toedit':

                $currentNode = $node;

                if ($editWindow) {
                    var $modalBody = $editWindow.getBody();
                    $modalBody.find('#name_text').val($node.find('input').val());
                    $modalBody.find('#select_topic').val($node.data('topic'));
                    $modalBody.find('#url_text').val($node.data('url'));
                    $modalBody.find('#select_target').val($node.data('target'));

                    treeController.changeTopic();

                    $editWindow.setTitle(lstr.update);
                    $editWindow.show();
                }

                break;
        }
    };

    treeController.newTreeNode = function(obj) {
        var $node = $('<li>' +
                        '<input value="' + obj.name + '"/>' +
                        '<div class="operations">' +
                            '<span data-action="toedit">&#9997;</span>' +
                            '<span data-action="toleft">&larr;</span>' +
                            '<span data-action="toright">&rarr;</span>' +
                            '<span data-action="toup">&uarr;</span>' +
                            '<span data-action="todown">&darr;</span>' +
                            '<span data-action="toremove">&#10008;</span>' +
                            '<span data-action="toadd">&#10010;</span>' +
                        '</div>' +
                    '</li>');
        $node.data('topic', obj.topicnumber);
        $node.data('url', obj.url);
        $node.data('target', obj.target);

        return $node;
    };

    treeController.loadTreeAria = function() {
        var $controlContainer = $(treeController.codeSelector);
        if ($controlContainer.length > 0) {
            var jsonString = $controlContainer.val();
            try {
                var jsonObject = JSON.parse(jsonString);
                if (jsonObject.topics && jsonObject.topics.length > 0) {

                    var createNode = function($nodeRoot, obj) {
                        var isparent = false;

                        if (obj.subtopics && obj.subtopics.length > 0) {
                            isparent = true;
                        }

                        var $node = treeController.newTreeNode(obj);

                        $nodeRoot.append($node);

                        if (isparent) {
                            var $group = $('<ul></ul>');
                            $node.append($group);
                            for (var i = 0; i < obj.subtopics.length; i++) {
                                createNode($group, obj.subtopics[i]);
                            }
                        }
                    };

                    var $treeAria = $('<ul class="tree root-level"></ul>');

                    for (var i = 0; i < jsonObject.topics.length; i++) {
                        createNode($treeAria, jsonObject.topics[i]);
                    }

                    var $operations = $('<div class="operations main">' +
                                        '<span data-action="toadd">&#10010;</span>' +
                                    '</div>');
                    $(treeController.containerSelector).append($operations);

                    $(treeController.containerSelector).append($treeAria);

                    $(treeController.containerSelector).find('[data-action]').on('click', function() {
                        var $this = $(this);
                        treeController.runAction($this.attr('data-action'), $this.parent().parent());
                    });
                }

                return true;
            } catch (e) {
                Notification.alert(Str.get_string('error', 'core'), e);
                return false;
            }
        }

        return false;
    };

    treeController.changeTopic = function() {

        var $modalBody = $editWindow.getBody();
        if ($modalBody.find('#select_topic').val() !== "") {
            $modalBody.find('#url_text').attr('disabled', 'disabled');
        } else {
            $modalBody.find('#url_text').removeAttr('disabled');
        }
    };

    treeController.saveTreeConfig = function() {

        var treecode = {
            "topics": []
        };

        treecode.topics = treeController.treeNode2Object($(treeController.containerSelector));

        if (treecode.topics.length > 0) {
            $(treeController.codeSelector).val(JSON.stringify(treecode));
        } else {
            $(treeController.codeSelector).val('');
        }
    };

    treeController.treeNode2Object = function($nodeRoot) {
        var nodes = [];
        $nodeRoot.children('ul').children('li').each(function(key, node) {
            var $node = $(node);

            var onenode = {
                "name": $node.find('input').val(),
                "subtopics": treeController.treeNode2Object($node),
                "topicnumber": $node.data('topic'),
                "url": $node.data('url'),
                "target": $node.data('target')
            };

            nodes[nodes.length] = onenode;

        });

        return nodes;
    };

    return treeController;
});
