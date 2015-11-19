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

class config_menutopic_form extends moodleform {

    function definition() {
        global $USER, $CFG, $course, $format_data;

        $config = new object();
        $config->cssdefault = true;
        $config->menuposition = 'middle';
        $config->linkinparent = false;
        $config->templatetopic = false;
        $config->icons_templatetopic = false;
        $config->displaynousedmod = false;
        $config->displaynavigation = 'nothing';
        $config->nodesnavigation = '';

        if (is_object($this->_customdata['format_data']) && property_exists($this->_customdata['format_data'], 'config') && !empty($this->_customdata['format_data']->config)) {
            $config_saved = @unserialize($this->_customdata['format_data']->config);
            
            if (!is_object($config_saved)) {
                $config_saved = new object();
            }
            
            if (isset($config_saved->cssdefault)) { $config->cssdefault = $config_saved->cssdefault; }

            if (isset($config_saved->menuposition)) { $config->menuposition = $config_saved->menuposition; }
            
            if (isset($config_saved->linkinparent)) { $config->linkinparent = $config_saved->linkinparent; }
            
            if (isset($config_saved->templatetopic)) { $config->templatetopic = $config_saved->templatetopic; }

            if (isset($config_saved->icons_templatetopic)) { $config->icons_templatetopic = $config_saved->icons_templatetopic; }

            if (isset($config_saved->displaynousedmod)) { $config->displaynousedmod = $config_saved->displaynousedmod; }

            if (isset($config_saved->displaynavigation)) { $config->displaynavigation = $config_saved->displaynavigation; }

            if (isset($config_saved->nodesnavigation)) { $config->nodesnavigation = $config_saved->nodesnavigation; }
        }

        $mform =& $this->_form;
        
        $mform->addElement('header','general', get_string('config_editmenu_title', 'format_menutopic'));

        $mform->addElement('selectyesno', 'cssdefault', get_string('cssdefault', 'format_menutopic'));
        $mform->addHelpButton('cssdefault', 'cssdefault', 'format_menutopic');
        $mform->setDefault('cssdefault', $config->cssdefault);

        $choices = array();
        $choices['hide'] = get_string('menuposition_hide', 'format_menutopic');
        $choices['left'] = get_string('menuposition_left', 'format_menutopic');
        $choices['middle'] = get_string('menuposition_middle', 'format_menutopic');
        $choices['right'] = get_string('menuposition_right', 'format_menutopic');
        $mform->addElement('select', 'menuposition', get_string('menuposition', 'format_menutopic'), $choices);
        $mform->addHelpButton('menuposition', 'menuposition', 'format_menutopic');
        $mform->setDefault('menuposition', $config->menuposition);

        $mform->addElement('selectyesno', 'linkinparent', get_string('linkinparent', 'format_menutopic'));
        $mform->addHelpButton('linkinparent', 'linkinparent', 'format_menutopic');
        $mform->setDefault('linkinparent', $config->linkinparent);

        $choices = array();
        $choices['top'] = get_string('navigationposition_top', 'format_menutopic');
        $choices['bottom'] = get_string('navigationposition_bottom', 'format_menutopic');
        $choices['both'] = get_string('navigationposition_both', 'format_menutopic');
        $choices['nothing'] = get_string('navigationposition_nothing', 'format_menutopic');
        $mform->addElement('select', 'displaynavigation', get_string('displaynavigation', 'format_menutopic'), $choices);
        $mform->addHelpButton('displaynavigation', 'displaynavigation', 'format_menutopic');
        $mform->setDefault('displaynavigation', $config->displaynavigation);

        $mform->addElement('text', 'nodesnavigation', get_string('nodesnavigation', 'format_menutopic'));
        $mform->addHelpButton('nodesnavigation', 'nodesnavigation', 'format_menutopic');
        $mform->setDefault('nodesnavigation', $config->nodesnavigation);
        $mform->setType('nodesnavigation', PARAM_RAW);

        $mform->addElement('header','template_topic', get_string('config_template_topic_title', 'format_menutopic'));

        $mform->addElement('selectyesno', 'templatetopic', get_string('templatetopic', 'format_menutopic'));
        $mform->addHelpButton('templatetopic', 'templatetopic', 'format_menutopic');
        $mform->setDefault('templatetopic', $config->templatetopic);

        $mform->addElement('selectyesno', 'icons_templatetopic', get_string('icons_templatetopic', 'format_menutopic'));
        $mform->addHelpButton('icons_templatetopic', 'icons_templatetopic', 'format_menutopic');
        $mform->setDefault('icons_templatetopic', $config->icons_templatetopic);

        $mform->addElement('selectyesno', 'displaynousedmod', get_string('displaynousedmod', 'format_menutopic'));
        $mform->addHelpButton('displaynousedmod', 'displaynousedmod', 'format_menutopic');
        $mform->setDefault('displaynousedmod', $config->displaynousedmod);

        $mform->addElement('hidden', 'id', $course->id);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'section', $this->_customdata['displaysection']);
        $mform->setType('section', PARAM_INT);

        $mform->addElement('hidden', 'editmenumode', 'true');
        $mform->setType('editmenumode', PARAM_BOOL);

        $mform->addElement('hidden', 'menuaction', 'config');
        $mform->setType('menuaction', PARAM_ALPHA);

        $this->add_action_buttons(false);
    }
}

//$displaysection, $format_data and $course_cancel_link are loaded in the render function
$display_form = new config_menutopic_form('view.php', array('format_data' =>$format_data, 'displaysection'=>$displaysection));

if ($display_form->is_cancelled()){
    redirect($course_cancel_link);
}
else if ($data = $display_form->get_data()) {
    //$data->wwwroot = $CFG->wwwroot;
    //$data->courseid = $course->id;
    $values = serialize($data);

    $format_data->config = $values;

    if (!$DB->update_record('format_menutopic', $format_data)){
        notify (get_string('notsaved', 'format_menutopic'));
    }
    else {
        notify (get_string('savecorrect', 'format_menutopic'));
    }
}

$display_form->display();
