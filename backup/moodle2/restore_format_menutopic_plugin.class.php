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

class restore_format_menutopic_plugin extends restore_format_plugin {

    /**
     * Returns the course format information to attach to course element
     */
    protected function define_course_plugin_structure() {
        $paths = array();

        // Because of using get_recommended_name() it is able to find the
        // correct path just by using the part inside the element name (which
        // only has a /menutopic element).
        $elepath = $this->get_pathfor('/menutopic');

        // The 'menutopic' here defines that it will use the process_menutopic function
        // to restore its element.
        $paths[] = new restore_path_element('menutopic', $elepath);

        return $paths;
    }
    
    /**
     * Process the 'menutopic' element
     */
    public function process_menutopic($data) {
        global $DB;

        // Get data record ready to insert in database
        $data = (object)$data;
        $data->course = $this->task->get_courseid();

        // See if there is an existing record for this course
        $existingid = $DB->get_field('format_menutopic', 'id',
                array('course'=>$data->course));
        if ($existingid) {
            $data->id = $existingid;
            $DB->update_record('format_menutopic', data);
        } else {
            $DB->insert_record('format_menutopic', $data);
        }

        // No need to record the old/new id as nothing ever refers to
        // the id of this table.
    }    
}