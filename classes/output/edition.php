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
 * Contains the default content output class.
 *
 * @package   format_menutopic
 * @copyright 2022 David Herney Bernal - cirano. https://bambuco.co
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_menutopic\output;

use renderable;
use renderer_base;
use templatable;

/**
 * Base class to render the configuration forms.
 *
 * @package   format_menutopic
 * @copyright 2022 David Herney Bernal - cirano. https://bambuco.co
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edition implements renderable, templatable {

    /**
     * @var object $course
     */
    protected $course;

    /**
     * Constructor method, calls the parent constructor.
     *
     * @param object $course
     */
    public function __construct(object $course) {

        $this->course = $course;
    }

    /**
     * Returns the output class template path.
     *
     * This method redirects the default template when the course content is rendered.
     *
     * @param \renderer_base $renderer typically, the renderer that's calling this function
     * @return string format template name
     */
    public function get_template_name(renderer_base $renderer): string {
        return 'format_menutopic/edition';
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return array Context variables for the template
     */
    public function export_for_template(renderer_base $output) {
        global $CFG, $DB, $format;

        $msgs = [];

        $displaysection = \format_menutopic::$displaysection;

        if (!($formatdata = $DB->get_record('format_menutopic', ['course' => $this->course->id]))) {
            $formatdata = new \stdClass();
            $formatdata->course = $this->course->id;

            if (!($formatdata->id = $DB->insert_record('format_menutopic', $formatdata))) {
                debugging('Not is possible save the course format data in menutopic format.', DEBUG_DEVELOPER);
            }
        }

        $url = (string)(new \moodle_url('/course/view.php', ['id' => $this->course->id,
                                                            'editmenumode' => 'true',
                                                            'section' => $displaysection]));

        $menuaction = optional_param('menuaction', 'config', PARAM_ALPHA);

        $options = ['config', 'tree', 'jstemplate', 'csstemplate'];

        if (!in_array($menuaction, $options)) {
            $menuaction = 'config';
        }

        $menu = new \format_menutopic\menu();

        foreach ($options as $option) {
            $item = new \format_menutopic\menuitem($url . '&menuaction=' . $option,
                                                    get_string($option . '_editmenu', 'format_menutopic'));

            if ($menuaction == $option) {
                $item->current = true;
            }

            $menu->add($item);
        }

        $formclass = '\\format_menutopic\\forms\\' . $menuaction;

        // Variables $displaysection, $formatdata and $coursecancellink are loaded in the render function.
        $displayform = new $formclass('view.php', ['format_data' => $formatdata, 'displaysection' => $displaysection]);

        if ($displayform->is_cancelled()) {
            redirect($coursecancellink);
        } else if ($data = $displayform->get_data()) {

            // Save the data according to the action form.
            switch($menuaction) {
                case 'config':
                    $values = serialize($data);
                    $formatdata->config = $values;
                    break;
                case 'tree':
                    $formatdata->tree = addslashes($data->treecode);
                    break;
                case 'jstemplate':
                    $formatdata->js = addslashes($data->jscode);
                    break;
                case 'csstemplate':
                    $formatdata->css = addslashes($data->csscode);
                    break;
            }

            if (!$DB->update_record('format_menutopic', $formatdata)) {
                $msgs[] = (object)[
                    'type' => 'error',
                    'message' => get_string('notsaved', 'format_menutopic')
                ];
            } else {
                $msgs[] = (object)[
                    'type' => 'success',
                    'message' => get_string('savecorrect', 'format_menutopic')
                ];
            }
        }

        // Load specific data according to the action form.
        switch($menuaction) {
            case 'tree':

                $modinfo = get_fast_modinfo($this->course);
                $sections = $modinfo->get_section_info_all();

                $formatdata->sections = [];
                foreach ($sections as $key => $section) {
                    if ($key == 0 && $this->course->coursedisplay == COURSE_DISPLAY_MULTIPAGE) {
                        continue;
                    }

                    $formatdata->sections[] = (object)[
                        'number' => $key,
                        'name' => $format->get_section_name($section),
                    ];
                }

                break;
        }

        $defaultvariables = [
            'baseurl' => $CFG->wwwroot,
            'courseid' => $this->course->id,
            'menuitems' => $menu->get_list($this->course->id),
            'withstyles' => true,
            'view' . $menuaction => true,
            'formatdata' => $formatdata,
            'displayform' => $displayform->render(),
            'hasmsgs' => count($msgs) > 0,
            'msgs' => $msgs,
            'styleboots' => true,
            'darkstyle' => true
        ];

        if ($displaysection >= 0) {
            $defaultvariables['displaysection'] = $displaysection;
        }

        return $defaultvariables;
    }
}
