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
import {get_string as getString} from 'core/str';

/**
 * Component initialization.
 *
 * @method init
 */
export const init = () => {

    var infotitle = '';
    getString('aboutresource', 'format_menutopic').then(str => {
        infotitle = str;
    });

    $('.format-menutopic .menutopic .iconwithhelp').each(function() {
        var $node = $(this);
        $node.on('click', function(e) {
            e.preventDefault();
            var $content = $node.find( '.iconwithhelp-content');

            if ($content.data('modal')) {
                $content.data('modal').show();
                return;
            }

            var title = $content.data('title');

            if (!title) {
                title = infotitle;
            }

            // Show the content in a modal window.
            ModalFactory.create({
                'title': title,
                'body': ''
            }).done(function(modal) {

                var contenthtml = $content.html();

                // Uncomment html in contenthtml. The comment is used in order to load content with tags not inline.
                contenthtml = contenthtml.replace(/<!--([\s\S]*?)-->/g, function (match, p1) {
                    return p1;
                  }
                );

                var $modalBody = modal.getBody();
                $modalBody.append(contenthtml);
                modal.show();
                $content.data('modal', modal);
            });
        });
    });
};