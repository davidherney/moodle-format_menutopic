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

namespace format_menutopic\output\courseformat;

use core_courseformat\output\local\content as content_base;
use core\output\named_templatable;
use core_courseformat\base as course_format;
use course_modinfo;
use renderable;

/**
 * Base class to render a course content.
 *
 * @package   format_menutopic
 * @copyright 2022 David Herney Bernal - cirano. https://bambuco.co
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class content extends content_base {

    /** @var stdClass Local format data */
    private static $formatdata;

    /**
     * @var bool Topic format has add section after each topic.
     *
     * The responsible for the buttons is core_courseformat\output\local\content\section.
     */
    protected $hasaddsection = false;

    /**
     * Returns the output class template path.
     *
     * This method redirects the default template when the course content is rendered.
     *
     * @param \renderer_base $renderer typically, the renderer that's calling this function
     * @return string format template name
     */
    public function get_template_name(\renderer_base $renderer): string {
        return 'format_menutopic/local/content';
    }


    /**
     * Export this data so it can be used as the context for a mustache template (core/inplace_editable).
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return stdClass data context for a mustache template
     */
    public function export_for_template(\renderer_base $output) {
        global $PAGE;
        $format = $this->format;

        $currentsection = $format->get_sectionnum();

        // If the section 0 is above the menu remove from the list.
        $sections = $this->export_sections($output);

        $data = (object)[
            'title' => $format->page_title(), // This method should be in the course_format class.
            'format' => $format->get_format(),
            'sectionreturn' => 0,
        ];

        $data->hasnavigation = true;

        // The current section format has extra navigation.
        if ($currentsection || $currentsection === 0) {

            $usessectionsnavigation = isset($format::$formatdata->configmenu->displaynavigation) ?
                                            $format::$formatdata->configmenu->displaynavigation : null;

            if (empty($usessectionsnavigation)) {
                $usessectionsnavigation = get_config('format_menutopic', 'defaultsectionsnavigation');
            }

            if ($usessectionsnavigation != \format_menutopic::SECTIONSNAVIGATION_NOT) {
                if ($usessectionsnavigation != \format_menutopic::SECTIONSNAVIGATION_SUPPORT ||
                        !$PAGE->theme->usescourseindex) {

                    $sectionnavigation = new $this->sectionnavigationclass($format, $currentsection);

                    // Not show navigation in top section if is not both or top.
                    if ($usessectionsnavigation == \format_menutopic::SECTIONSNAVIGATION_BOTH ||
                            $usessectionsnavigation == \format_menutopic::SECTIONSNAVIGATION_TOP) {
                        $data->sectionnavigation = $sectionnavigation->export_for_template($output);
                    }

                    if ($usessectionsnavigation != \format_menutopic::SECTIONSNAVIGATION_TOP) {
                        $sectionselector = new $this->sectionselectorclass($format, $sectionnavigation);
                        $data->sectionselector = $sectionselector->export_for_template($output);

                        if ($usessectionsnavigation == \format_menutopic::SECTIONSNAVIGATION_SLIDES) {
                            $data->sectionclasses = ' sectionsnavigation-slides';
                        }
                    }
                }
            }

            $data->sectionreturn = $currentsection;
        }

        $data->showsection = array_shift($sections);

        if ($this->hasaddsection) {
            $addsection = new $this->addsectionclass($format);
            $data->numsections = $addsection->export_for_template($output);
        }

        if ($format->show_editor()) {
            $bulkedittools = new $this->bulkedittoolsclass($format);
            $data->bulkedittools = $bulkedittools->export_for_template($output);
        }

        return $data;
    }

    /**
     * Export sections array data.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return array data context for a mustache template
     */
    protected function export_sections(\renderer_base $output): array {

        $format = $this->format;
        $course = $format->get_course();
        $modinfo = $this->format->get_modinfo();

        // Generate section list.
        $sections = [];
        $stealthsections = [];
        $numsections = $format->get_last_section_number();
        foreach ($this->get_sections_to_display($modinfo) as $sectionnum => $thissection) {
            // The course/view.php check the section existence but the output can be called
            // from other parts so we need to check it.
            if (!$thissection) {
                throw new \moodle_exception('unknowncoursesection', 'error', course_get_url($course),
                    format_string($course->fullname));
            }

            $section = new $this->sectionclass($format, $thissection);

            if ($sectionnum > $numsections) {
                // Activities inside this section are 'orphaned', this section will be printed as 'stealth' below.
                if (!empty($modinfo->sections[$sectionnum])) {
                    $stealthsections[] = $section->export_for_template($output);
                }
                continue;
            }

            if (!$format->is_section_visible($thissection)) {
                continue;
            }

            $sections[] = $section->export_for_template($output);
        }

        if (!empty($stealthsections)) {
            $sections = array_merge($sections, $stealthsections);
        }
        return $sections;
    }

    /**
     * Return an array of sections to display.
     *
     * This method is used to differentiate between display a specific section
     * or a list of them.
     *
     * @param course_modinfo $modinfo the current course modinfo object
     * @return section_info[] an array of section_info to display
     */
    protected function get_sections_to_display(course_modinfo $modinfo): array {

        $format = $this->format;
        $sections = [$modinfo->get_section_info($format->get_sectionnum())];

        return $sections;
    }

}
