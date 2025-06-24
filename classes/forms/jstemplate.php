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
 * JS template options.
 *
 * @package   format_menutopic
 * @copyright 2023 David Herney - https://bambuco.co
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_menutopic\forms;

/**
 * Class to manage the custom javascript.
 *
 * @package   format_menutopic
 * @copyright 2012 David Herney - https://bambuco.co
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class jstemplate extends \moodleform {

    /**
     * Defines the form fields.
     */
    public function definition() {
        global $USER, $CFG, $course;

        $jscode = '';

        if (is_object($this->_customdata['format_data']) && property_exists($this->_customdata['format_data'], 'js')) {
            $jscode = stripslashes($this->_customdata['format_data']->js ?? '');
        }

        $mform =& $this->_form;

        $mform->addElement('header', 'general', get_string('jstemplate', 'format_menutopic'));
        $mform->addHelpButton('general', 'jstemplate', 'format_menutopic');

        $mform->addElement('textarea', 'jscode', get_string('jscode', 'format_menutopic'), ['rows' => '20', 'cols' => '65']);
        $mform->setType('jscode', PARAM_RAW);
        $mform->setDefault('jscode', $jscode);

        $mform->addElement('hidden', 'id', $course->id);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'section', $this->_customdata['displaysection']);
        $mform->setType('section', PARAM_INT);

        $mform->addElement('hidden', 'editmenumode', 'true');
        $mform->setType('editmenumode', PARAM_BOOL);

        $mform->addElement('hidden', 'menuaction', 'jstemplate');
        $mform->setType('menuaction', PARAM_ALPHA);

        $this->add_action_buttons(false);
    }
}
