// You can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// It is distributed in the hope that it will be useful,
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
 * @package contribution
 * @copyright 2012 David Herney Bernal - cirano
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

M.format_menutopic = M.format_menutopic || {};

M.format_menutopic.init_tree = function (Y) {
    if (!load_tree('id_treecode')) {
        alert(M.str.format_menutopic.error_jsontree);
        return;
    }

    // Instantiate a Panel from markup
    YUI.tree_admin.panel_edit_sheet = new Y.Panel({
        srcNode      : "#panel_edit_sheet", 
        visible      : false,
        draggable    : true,
        headerContent: M.str.format_menutopic.title_panel_sheetedit,
        plugins      : [Y.Plugin.Drag]
    });
    YUI.tree_admin.panel_edit_sheet.render();

    Y.one('#id_submitbutton').on('click', save_tree_config);
};
