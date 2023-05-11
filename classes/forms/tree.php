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
 * Menu tree control.
 *
 * @package   format_menutopic
 * @copyright 2023 David Herney - https://bambuco.co
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_menutopic\forms;

/**
 * Custom tree control.
 *
 * @package   format_menutopic
 * @copyright 2012 David Herney - https://bambuco.co
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tree extends \moodleform {

    /**
     * Defines the form fields.
     */
    public function definition() {
        global $USER, $CFG, $course, $PAGE;

        $PAGE->requires->js_call_amd('format_menutopic/menutopictree', 'init');

        $treecode = '';

        if (is_object($this->_customdata['format_data']) && property_exists($this->_customdata['format_data'], 'tree')
                && !empty($this->_customdata['format_data']->tree)) {
            $treecode = stripslashes($this->_customdata['format_data']->tree);
        } else {
            $treecodeobject = new \stdClass();
            $treecodeobject->topics = [];

            $init = 0;
            if ($course->coursedisplay == COURSE_DISPLAY_MULTIPAGE) {
                $init = 1;
            }

            for ($i = 0; $i < ($course->numsections + 1 - $init); $i++) {
                $treecodeobject->topics[$i] = new \stdClass();
                $treecodeobject->topics[$i]->name = get_string('template_namemenutopic', 'format_menutopic', $i + $init);
                $treecodeobject->topics[$i]->subtopics = [];
                $treecodeobject->topics[$i]->topicnumber = $i + $init;
                $treecodeobject->topics[$i]->url = '';
                $treecodeobject->topics[$i]->target = '';
            }
            $treecode = json_encode($treecodeobject);
        }

        $mform =& $this->_form;

        $mform->addElement('header', 'treeeditgeneral', get_string('tree_editmenu_title', 'format_menutopic'));
        $mform->addHelpButton('treeeditgeneral', 'tree_struct', 'format_menutopic');

        $mform->addElement('textarea', 'treecode', '', ['style' => 'display: none']);
        $mform->setType('treecode', PARAM_RAW);
        $mform->setDefault('treecode', $treecode);

        $mform->addElement('static', 'treecontainer', get_string('tree_struct', 'format_menutopic'),
            '<div id="treecontainer"></div>');

        $mform->addElement('hidden', 'id', $course->id);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'section', $this->_customdata['displaysection']);
        $mform->setType('section', PARAM_INT);

        $mform->addElement('hidden', 'editmenumode', 'true');
        $mform->setType('editmenumode', PARAM_BOOL);

        $mform->addElement('hidden', 'menuaction', 'tree');
        $mform->setType('menuaction', PARAM_ALPHA);

        $this->add_action_buttons(false);
    }
}
