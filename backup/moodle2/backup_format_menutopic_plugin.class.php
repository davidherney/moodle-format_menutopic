<?php
//
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
 *
 * @since 2.3
 * @package contribution
 * @copyright 2012 David Herney Bernal - cirano
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class backup_format_menutopic_plugin extends backup_format_plugin {

    /**
     * Returns the course format information to attach to course element
     */
    protected function define_course_plugin_structure() {
        // Define virtual plugin element
        $plugin = $this->get_plugin_element(null, $this->get_format_condition(), 'menutopic');

        // Create plugin container element with standard name
        $pluginwrapper = new backup_nested_element($this->get_recommended_name());

        // Add wrapper to plugin
        $plugin->add_child($pluginwrapper);

        // Set up fromat's own structure and add to wrapper
        $menutopic = new backup_nested_element('menutopic', array('id'), array(
            'config', 'css', 'js', 'html', 'tree'));
        $pluginwrapper->add_child($menutopic);

        // Use database to get source
        $menutopic->set_source_table('format_menutopic',
                array('course' => backup::VAR_COURSEID));

        return $plugin;
    }
}