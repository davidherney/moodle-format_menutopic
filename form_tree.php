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
 *
 * @since 2.3
 * @package format_menutopic
 * @copyright 2012 David Herney Bernal - cirano
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


require_once($CFG->libdir.'/formslib.php');

/**
 * Custom tree control.
 *
 * @package format_menutopic
 * @copyright 2012 David Herney Bernal - cirano
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tree_menutopic_form extends moodleform {

    public function definition() {
        global $USER, $CFG, $course, $PAGE;

        $PAGE->requires->js_call_amd('format_menutopic/menutopictree', 'init');

        $treecode = '';

        if (is_object($this->_customdata['format_data']) && property_exists($this->_customdata['format_data'], 'tree')
                && !empty($this->_customdata['format_data']->tree)) {
            $treecode = stripslashes($this->_customdata['format_data']->tree);
        } else {
            $treecode = '{"topics": [';
            for ($i = 0; $i < ($course->numsections + 1); $i++) {
                $treecode .= '{"name" : "' . get_string('template_namemenutopic', 'format_menutopic', $i) . '",';
                $treecode .= '         "subtopics": [],';
                $treecode .= '         "topicnumber": ' . $i . ',';
                $treecode .= '         "url": "",';
                $treecode .= '         "target": ""';
                $treecode .= "},\n";
            }
            $treecode = rtrim($treecode, ",\n");
            $treecode .= ']}';
        }

        $mform =& $this->_form;

        $mform->addElement('header','treeeditgeneral', get_string('tree_editmenu_title', 'format_menutopic'));
        $mform->addHelpButton('treeeditgeneral', 'tree_struct', 'format_menutopic');

        $mform->addElement('textarea', 'treecode', '', array('style' => 'display: none'));
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

// Variables $displaysection, $format_data and $course_cancel_link are loaded in the render function.
$display_form = new tree_menutopic_form('view.php', array('format_data' => $format_data, 'displaysection' => $displaysection));

if ($display_form->is_cancelled()) {
    redirect($course_cancel_link);
}
else if ($data = $display_form->get_data()) {
    $format_data->tree = $data->treecode;

    if (!$DB->update_record('format_menutopic', $format_data)) {
        echo $OUTPUT->notification (get_string('notsaved', 'format_menutopic'), 'notifyproblem');
    } else {
        // ToDo: Delete html cache if exists.
        echo $OUTPUT->notification (get_string('savecorrect', 'format_menutopic'), 'notifysuccess');
    }
}

$display_form->display();

?>

<div id="editsheetform" style="display: none;">
    <div class="treeeditform">
        <table>
            <tr>
                <th><?php print_string('name_sheet_sheetedit', 'format_menutopic'); ?></th>
                <td><input id="name_text" size="40" /></td>
            </tr>
            <tr>
                <th><?php print_string('topic_sheet_sheetedit', 'format_menutopic'); ?></th>
                <td>
                    <select id="select_topic">
                        <option></option>
                    <?php
                        for ($i = 0; $i < ($course->numsections + 1); $i++) {
                            echo '<option>' . $i . '</option>';
                        }
                    ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><?php print_string('url_sheet_sheetedit', 'format_menutopic'); ?></th>
                <td><input id="url_text" size="40" /></td>
            </tr>
            <tr>
                <th><?php print_string('target_sheet_sheetedit', 'format_menutopic'); ?></th>
                <td>
                    <select id="select_target">
                        <option></option>
                        <option value="_blank"><?php print_string('targetblank_sheet_sheetedit', 'format_menutopic'); ?></option>
                        <option value="_self"><?php print_string('targetself_sheet_sheetedit', 'format_menutopic'); ?></option>
                    </select>
                </td>
            </tr>
        </table>
    </div>
</div>