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


require_once($CFG->libdir.'/formslib.php');

class jstemplate_menutopic_form extends moodleform {

    function definition() {
        global $USER, $CFG, $course;

		$jscode = '';

		if (is_object($this->_customdata['format_data']) && property_exists($this->_customdata['format_data'], 'js')) {
			$jscode = stripslashes($this->_customdata['format_data']->js);
		}
		
        $mform =& $this->_form;
		
        $mform->addElement('header','general', get_string('jstemplate', 'format_menutopic'));
        $mform->addHelpButton('general', 'jstemplate', 'format_menutopic');

        $mform->addElement('textarea','jscode', get_string('jscode', 'format_menutopic'), array('rows'=> '20', 'cols'=>'65'));
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

//$displaysection, $format_data and $course_cancel_link are loaded in the render function
$display_form = new jstemplate_menutopic_form('view.php', array('format_data' =>$format_data, 'displaysection'=>$displaysection));

if ($display_form->is_cancelled()){
	redirect($course_cancel_link);
}
else if ($data = $display_form->get_data()) {

	$format_data->js = stripcslashes($data->jscode);

	if (!$DB->update_record('format_menutopic', $format_data)){
	    notify (get_string('notsaved', 'format_menutopic'));
	}
	else {
        notify (get_string('savecorrect', 'format_menutopic'));
	}
}

$display_form->display();

