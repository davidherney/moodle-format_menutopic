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
 * This file contains class for render the header in the course format menutopic.
 *
 * @package   format_menutopic
 * @copyright 2023 David Herney - https://bambuco.co
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_menutopic;

/**
 * Class used to render the header content in each course page.
 *
 *
 * @package   format_menutopic
 * @copyright 2016 David Herney - https://bambuco.co
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class header implements \renderable, \templatable {

    /**
     * @var \format_menutopic
     */
    private $format;

    /**
     * Constructor.
     *
     * @param \format_menutopic $format Course format instance.
     */
    public function __construct(\format_menutopic $format) {
        $this->format = $format;
    }

    /**
     * Export this data so it can be used as the context for a mustache template (core/inplace_editable).
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return stdClass data context for a mustache template
     */
    public function export_for_template(\renderer_base $output) {
        global $COURSE, $PAGE, $CFG;

        $this->formatdata = $this->format->load_formatdata();
        $course = course_get_format($COURSE)->get_course();

        // Include JS y CSS information.
        if (!empty($this->formatdata->js)) {
            $jscode = stripcslashes($this->formatdata->js);
            $PAGE->requires->js_init_code($jscode, true);
        }

        $csstemplate = null;
        if (!empty($this->formatdata->css)) {
            $csstemplate = $this->formatdata->css;

            // Clean the CSS template for html tags.
            $csstemplate = preg_replace('/<[^>]*>/', '', $csstemplate);
            $csstemplate = stripcslashes($csstemplate);
        }

        foreach (\format_menutopic::$formatmsgs as $key => $msg) {
            if (is_string($msg)) {
                \format_menutopic::$formatmsgs[$key] = (object)['message' => $msg];
            }
        }

        $coursecontext = \context_course::instance($course->id);
        $canviewhidden = has_capability('moodle/course:viewhiddensections', $coursecontext);

        $data = (object)[
            'baseurl' => $CFG->wwwroot,
            'title' => $this->format->page_title(), // This method should be in the course_format class.
            'format' => $this->format->get_format(),
            'hasmenu' => false,
            'hasformatmsgs' => count(\format_menutopic::$formatmsgs) > 0,
            'formatmsgs' => \format_menutopic::$formatmsgs,
            'hidemenubar' => $this->formatdata->configmenu->menuposition == 'hide',
            'templatetopic' => $this->formatdata->configmenu->templatetopic,
            'withicons' => $this->formatdata->configmenu->icons_templatetopic,
            'csstemplate' => $csstemplate,
            'shownavbarbrand' => get_config('format_menutopic', 'shownavbarbrand'),
            'canviewhidden' => $canviewhidden,
            'isediting' => $this->format->show_editor(),
        ];

        $inpopup = optional_param('inpopup', 0, PARAM_INT);

        $pagesavailable = ['course-view-menutopic'];
        $patternavailable = '/^mod-.*-view$/';

        $initialsection = null;

        if (!$inpopup && (in_array($PAGE->pagetype, $pagesavailable) || preg_match($patternavailable, $PAGE->pagetype))) {

            // General section if non-empty and course_display is multiple.
            if ($course->realcoursedisplay == COURSE_DISPLAY_MULTIPAGE) {

                // Load the section 0 and export data for template.
                $modinfo = $this->format->get_modinfo();
                $section0 = $modinfo->get_section_info(0);
                $sectionclass = $this->format->get_output_classname('content\\section');
                $section = new $sectionclass($this->format, $section0);

                $sectionoutput = new \format_menutopic\output\renderer($PAGE, null);
                $initialsection = $section->export_for_template($sectionoutput);

            }

            $data->initialsection = $initialsection;

            $data->menuinfo = new \stdClass();
            $data->menuinfo->withstyles = $this->formatdata->configmenu->cssdefault;
            $data->menuinfo->menuposition = $this->formatdata->configmenu->menuposition;
            $data->menuinfo->linkinparent = $this->formatdata->configmenu->linkinparent;
            $data->menuinfo->menuitems = $this->formatdata->menu->get_list($course->id);
            $data->menuinfo->courseid = $course->id;
            $data->menuinfo->enableedition = $this->format->show_editor();

            $globalstyletype = get_config('format_menutopic', 'globalstyle');
            switch ($globalstyletype) {
                case \format_menutopic::STYLE_BASIC:
                case \format_menutopic::STYLE_BOOTS:
                    $globalstyle = $globalstyletype;
                    break;
                case \format_menutopic::STYLE_BOOTSDARK:
                    $globalstyle = \format_menutopic::STYLE_BOOTS;
                    $data->menuinfo->darkstyle = true;
                    break;
            }
            $globalstyle = 'style' . $globalstyle;
            $data->menuinfo->{$globalstyle} = true;

            $data->hasmenu = $this->formatdata->menu->has_items();
        }

        // Load the JS module.
        $PAGE->requires->js_call_amd('format_menutopic/main', 'init');

        return $data;
    }
}
