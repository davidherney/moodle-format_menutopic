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
 * CSS template options.
 *
 * @package   format_menutopic
 * @copyright 2023 David Herney - https://bambuco.co
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_menutopic\forms;

/**
 * Class to manage the custom CSS.
 *
 * @package   format_menutopic
 * @copyright 2012 David Herney - https://bambuco.co
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class csstemplate extends \moodleform {

    /**
     * Defines the form fields.
     */
    public function definition() {
        global $course;

        $csscode = '';

        if (is_object($this->_customdata['format_data']) && property_exists($this->_customdata['format_data'], 'css')) {
            $csscode = stripslashes($this->_customdata['format_data']->css);
        }

        $mform =& $this->_form;

        $mform->addElement('header', 'general', get_string('csstemplate_editmenu_title', 'format_menutopic'));
        $mform->addHelpButton('general', 'csstemplate', 'format_menutopic');

        $mform->addElement('textarea', 'csscode', get_string('csscode', 'format_menutopic'), ['rows' => '20', 'cols' => '65']);
        $mform->setType('csscode', PARAM_RAW);
        $mform->setDefault('csscode', $csscode);

        $mform->addElement('hidden', 'id', $course->id);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'section', $this->_customdata['displaysection']);
        $mform->setType('section', PARAM_INT);

        $mform->addElement('hidden', 'editmenumode', 'true');
        $mform->setType('editmenumode', PARAM_BOOL);

        $mform->addElement('hidden', 'menuaction', 'csstemplate');
        $mform->setType('menuaction', PARAM_ALPHA);

        $this->add_action_buttons(false);
    }
}
