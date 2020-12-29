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
 * @since 2.4
 * @package format_menutopic
 * @copyright 2012 David Herney Bernal - cirano
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

M.course = M.course || {};

M.course.format = M.course.format || {};

M.course.format.showInfo = function(id) {

    new M.core.dialogue({
                draggable: true,
                headerContent: '<span>' + M.util.get_string('info', 'moodle') + '</span>',
                bodyContent: Y.Node.one('#' + id),
                centered: true,
                width: '480px',
                modal: true,
                visible: true
            });

    Y.Node.one('#' + id).show();

};

M.course.format.dialogueinitloaded = false;

M.course.format.dialogueinit = function() {

    if (M.course.format.dialogueinitloaded) {
        return;
    }

    M.course.format.dialogueinitloaded = true;
    Y.all('[data-infoid]').each(function(node) {
        node.on('click', function() {
            M.course.format.showInfo(node.getAttribute('data-infoid'));
        });
    });
};

M.course.format.moveMenuLeft = function(Y) {

    if (Y.one('#nav-drawer')) {
        Y.one('#nav-drawer').prepend(Y.one('#format_menutopic_menu'));
    } else if (Y.one('#block-region-side-pre')) {
        Y.one('#block-region-side-pre').prepend(Y.one('#format_menutopic_menu'));
        if (Y.one('body.empty-region-side-pre')) {
            Y.one('body.empty-region-side-pre').removeClass('empty-region-side-pre');
        }
    }
};

M.course.format.moveMenuRight = function(Y) {

    if (Y.one('#block-region-side-post')) {
        Y.one('#block-region-side-post').prepend(Y.one('#format_menutopic_menu'));
        if (Y.one('body.empty-region-side-post')) {
            Y.one('body.empty-region-side-post').removeClass('empty-region-side-post');
        }
    } else if (Y.one('#nav-drawer')) {
        Y.one('#nav-drawer').prepend(Y.one('#format_menutopic_menu'));
    }
};
