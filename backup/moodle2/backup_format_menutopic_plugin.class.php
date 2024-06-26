<?php
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
 * Specialised backup for format_menutopic.
 *
 * @since 2.3
 * @package format_menutopic
 * @copyright 2012 David Herney - cirano
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Specialised backup for format_menutopic.
 *
 * @package format_menutopic
 * @category backup
 * @copyright 2012 David Herney - cirano
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_format_menutopic_plugin extends backup_format_plugin {

    /**
     * Returns the course format information to attach to course element.
     */
    protected function define_course_plugin_structure() {
        // Define virtual plugin element.
        $plugin = $this->get_plugin_element(null, $this->get_format_condition(), 'menutopic');

        // Create plugin container element with standard name.
        $pluginwrapper = new backup_nested_element($this->get_recommended_name());

        // Add wrapper to plugin.
        $plugin->add_child($pluginwrapper);

        // Set up fromat's own structure and add to wrapper.
        $menutopic = new backup_nested_element('menutopic', ['id'], ['config', 'css', 'js', 'html', 'tree']);
        $pluginwrapper->add_child($menutopic);

        // Use database to get source.
        $menutopic->set_source_table('format_menutopic', ['course' => backup::VAR_COURSEID]);

        return $plugin;
    }
}
