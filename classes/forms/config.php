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
 * Menu configurations options.
 *
 * @package   format_menutopic
 * @copyright 2023 David Herney - https://bambuco.co
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_menutopic\forms;

/**
 * Class to control the menu configurations.
 *
 * @package   format_menutopic
 * @copyright 2016 David Herney - https://bambuco.co
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class config extends \moodleform {

    /**
     * Defines the form fields.
     */
    public function definition() {
        global $USER, $CFG, $course, $formatdata;

        $config = new \stdClass();
        $config->cssdefault = true;
        $config->menuposition = 'middle';
        $config->linkinparent = false;
        $config->templatetopic = false;
        $config->icons_templatetopic = false;
        $config->displaynousedmod = false;
        $config->displaynavigation = 'nothing';
        $config->nodesnavigation = '';

        if (is_object($this->_customdata['format_data'])
                && property_exists($this->_customdata['format_data'], 'config')
                && !empty($this->_customdata['format_data']->config)) {

            $configsaved = @unserialize($this->_customdata['format_data']->config);

            if (!is_object($configsaved)) {
                $configsaved = new \stdClass();
            }

            if (isset($configsaved->cssdefault)) {
                $config->cssdefault = $configsaved->cssdefault;
            }

            if (isset($configsaved->menuposition)) {
                $config->menuposition = $configsaved->menuposition;
            }

            if (isset($configsaved->linkinparent)) {
                $config->linkinparent = $configsaved->linkinparent;
            }

            if (isset($configsaved->templatetopic)) {
                $config->templatetopic = $configsaved->templatetopic;
            }

            if (isset($configsaved->icons_templatetopic)) {
                $config->icons_templatetopic = $configsaved->icons_templatetopic;
            }

            if (isset($configsaved->displaynousedmod)) {
                $config->displaynousedmod = $configsaved->displaynousedmod;
            }

            if (isset($configsaved->displaynavigation)) {
                $config->displaynavigation = $configsaved->displaynavigation;
            }

            if (isset($configsaved->nodesnavigation)) {
                $config->nodesnavigation = $configsaved->nodesnavigation;
            }
        }

        $mform =& $this->_form;

        $mform->addElement('header', 'general', get_string('config_editmenu_title', 'format_menutopic'));

        $mform->addElement('selectyesno', 'cssdefault', get_string('cssdefault', 'format_menutopic'));
        $mform->addHelpButton('cssdefault', 'cssdefault', 'format_menutopic');
        $mform->setDefault('cssdefault', $config->cssdefault);

        $choices = [];
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

        $choices = [];
        $choices[''] = get_string('navigationposition_site', 'format_menutopic');
        $choices[\format_menutopic::SECTIONSNAVIGATION_NOT] = get_string('navigationposition_nothing', 'format_menutopic');
        $choices[\format_menutopic::SECTIONSNAVIGATION_TOP] = get_string('navigationposition_top', 'format_menutopic');
        $choices[\format_menutopic::SECTIONSNAVIGATION_BOTTOM] = get_string('navigationposition_bottom', 'format_menutopic');
        $choices[\format_menutopic::SECTIONSNAVIGATION_BOTH] = get_string('navigationposition_both', 'format_menutopic');
        $choices[\format_menutopic::SECTIONSNAVIGATION_SLIDES] = get_string('navigationposition_slide', 'format_menutopic');
        $mform->addElement('select', 'displaynavigation', get_string('displaynavigation', 'format_menutopic'), $choices);
        $mform->addHelpButton('displaynavigation', 'displaynavigation', 'format_menutopic');
        $mform->setDefault('displaynavigation', $config->displaynavigation);

        // ToDo: Remove in future versions.
        $mform->addElement('hidden', 'nodesnavigation', get_string('nodesnavigation', 'format_menutopic'));
        $mform->addHelpButton('nodesnavigation', 'nodesnavigation', 'format_menutopic');
        $mform->setDefault('nodesnavigation', $config->nodesnavigation);
        $mform->setType('nodesnavigation', PARAM_RAW);

        $mform->addElement('header', 'template_topic', get_string('config_template_topic_title', 'format_menutopic'));

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
