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
 * General actions for the menutopic course format.
 *
 * @copyright 2018 David Herney Bernal - cirano
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import $ from 'jquery';
import ModalFactory from 'core/modal_factory';
import {get_strings as getStrings} from 'core/str';
import Log from 'core/log';

// Load strings.
var strings = [
    {key: 'aboutresource', component: 'format_menutopic'},
    {key: 'aboutsection', component: 'format_menutopic'}
];
var s = [];

/**
 * Load strings from server.
 */
function loadStrings() {

    strings.forEach(one => {
        s[one.key] = one.key;
    });

    getStrings(strings).then(function(results) {
        var pos = 0;
        strings.forEach(one => {
            s[one.key] = results[pos];
            pos++;
        });
        return true;
    }).fail(function(e) {
        Log.debug('Error loading strings');
        Log.debug(e);
    });
}
// End of Load strings.

/**
 * Component initialization.
 *
 * @method init
 */
export const init = () => {

    loadStrings();

    $('.format-menutopic .menutopic .iconwithhelp[data-helpwindow]').each(function() {
        var $node = $(this);
        $node.on('click', function(e) {
            e.preventDefault();
            var $content = $('#hw-' + $node.data('helpwindow'));

            if ($content.data('modal')) {
                $content.data('modal').show();
                return;
            }

            var title = $content.data('title');

            if (!title) {
                title = s.aboutresource;
            }

            // Show the content in a modal window.
            ModalFactory.create({
                'title': title,
                'body': '',
            }).done(function(modal) {

                var contenthtml = $content.html();

                // Uncomment html in contenthtml. The comment is used in order to load content with tags not inline.
                contenthtml = contenthtml.replace(/<!--([\s\S]*?)-->/g, function(match, p1) {
                    return p1;
                  }
                );

                var $modalBody = modal.getBody();
                $modalBody.css('min-height', '150px');
                $modalBody.append(contenthtml);
                modal.show();
                $content.data('modal', modal);
            });
        });
    });

    $('.format-menutopic .menuitem [data-infoid]').each(function() {
        var $node = $(this);
        $node.on('click', function(e) {
            e.preventDefault();
            var $content = $($node.data('infoid'));

            if ($content.data('modal')) {
                $content.data('modal').show();
                return;
            }

            var title = $content.data('title');

            if (!title) {
                title = s.aboutsection;
            }

            // Show the content in a modal window.
            ModalFactory.create({
                'title': title,
                'body': $content.html()
            }).done(function(modal) {
                modal.show();
                $content.data('modal', modal);
            });
        });
    });
};