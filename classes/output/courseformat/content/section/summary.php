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
 * Contains the default section summary (used for multipage format).
 *
 * @package   format_menutopic
 * @copyright 2022 David Herney Bernal - cirano. https://bambuco.co
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_menutopic\output\courseformat\content\section;

use context_course;
use core\output\named_templatable;
use core_courseformat\base as course_format;
use core_courseformat\output\local\courseformat_named_templatable;
use core_courseformat\output\local\content\cm as cm_base;
use core_courseformat\output\local\content\section\summary as summary_base;
use renderable;
use section_info;
use stdClass;

/**
 * Base class to render a course section summary.
 *
 * @package   format_menutopic
 * @copyright 2022 David Herney Bernal - cirano. https://bambuco.co
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class summary extends summary_base {

    use courseformat_named_templatable;

    /** @var course_format the course format class */
    protected $format;

    /** @var section_info the course section class */
    private $section;

    /** @var renderer_base the renderer output class */
    private $output;

    /** @var string Text to search */
    public $tplstringsearch;

    /** @var string Text to replace */
    public $tplstringreplace;

    /** @var string Temporal key */
    public $tpltagstring = '{label_tag_replace}';

    /**
     * Constructor.
     *
     * @param course_format $format the course format
     * @param section_info $section the section info
     */
    public function __construct(course_format $format, section_info $section) {
        $this->format = $format;
        $this->section = $section;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return array data context for a mustache template
     */
    public function export_for_template(\renderer_base $output): stdClass {

        $section = $this->section;
        $this->output = $output;

        $data = new stdClass();

        if ($section->uservisible || $section->visible) {
            $data->summarytext = $this->format_summary_text();
        }
        return $data;
    }

    /**
     * Generate html for a section summary text
     *
     * @return string HTML to output.
     */
    public function format_summary_text(): string {
        $section = $this->section;
        $context = $this->format->get_context();

        $summarytext = $section->summary;
        if (\format_menutopic::$formatdata->configmenu->templatetopic) {
            $summarytext = $this->replace_resources($section);
        }

        $summarytext = file_rewrite_pluginfile_urls($summarytext, 'pluginfile.php',
            $context->id, 'course', 'section', $section->id);

        $options = new stdClass();
        $options->noclean = true;
        $options->overflowdiv = true;
        return format_text($summarytext, $section->summaryformat, $options);
    }

    /**
     * Process the Menutopic summary template.
     *
     * @param section_info $section the section info
     * @return string HTML to output.
     */
    private function replace_resources(section_info $section) {

        global $USER, $DB, $PAGE;

        $course = $this->format->get_course();

        $completioninfo = new \completion_info($course);

        $labelformatoptions = new \stdclass();
        $labelformatoptions->noclean = true;

        // Casting $course->modinfo to string prevents one notice when the field is null.
        $modinfo = $this->format->get_modinfo();

        $summary = $section->summary;

        if (!empty($section->sequence)) {
            $sectionmods = explode(",", $section->sequence);

            $completioninfo = new \completion_info($course);

            foreach ($sectionmods as $modnumber) {

                if (empty($modinfo->cms[$modnumber])) {
                    continue;
                }

                $mod = $modinfo->cms[$modnumber];

                if ($mod->modname == "label") {
                    continue;
                }

                $instancename = format_string($modinfo->cms[$modnumber]->name, true, $course->id);

                $displayoptions = [];
                $cm = new cm_base($this->format, $section, $mod, $displayoptions);

                // Display the link to the module (or do nothing if module has no url).
                $cmdata = $cm->export_for_template($this->output);
                $cmdata->modinline = true;
                $cmdata->hideicons = !\format_menutopic::$formatdata->configmenu->icons_templatetopic;
                $cmdata->uniqueid = 'cm_' . $mod->id . '_' . time() . '_' . rand(0, 1000);
                $cmdata->singlename = $instancename;

                // ToDo: implement show course badges, completion, etc, when template is active.
                $cmdata->hascompletion = isset($cmdata->completion) && $cmdata->completion;
                $hasavailability = isset($cmdata->modavailability) ? $cmdata->modavailability->hasmodavailability : false;

                $cmdata->showinlinehelp = false;
                if ($cmdata->hascompletion
                        || (isset($cmdata->hasdates) && $cmdata->hasdates)
                        || $hasavailability){
                    $cmdata->showinlinehelp = true;
                }

                $url = $mod->url;
                if (empty($url)) {
                    // If there is content but NO link (like label), then don't display it.
                    continue;
                }

                $template = 'format_menutopic/courseformat/content/cminline';

                if ($completioninfo->is_enabled($mod) !== COMPLETION_TRACKING_NONE) {
                    $completion = $DB->get_record('course_modules_completion',
                                                ['coursemoduleid' => $mod->id, 'userid' => $USER->id, 'completionstate' => 1]);

                    $template = 'format_menutopic/courseformat/content/cminlinecompletion';

                    $showcompletionconditions = $course->showcompletionconditions == COMPLETION_SHOW_CONDITIONS;

                    if ($completion) {
                        $completedclass = 'complete';
                    } else {
                        $completedclass = 'incomplete';
                    }

                    if ($showcompletionconditions) {
                        $completedclass .= ' hascompletionconditions';
                    }

                    $cmdata->completedclass = $completedclass;
                    $cmdata->showcompletionconditions = $showcompletionconditions;

                }

                $renderer = $this->format->get_renderer($PAGE);
                $htmlresource = $renderer->render_from_template($template, $cmdata);

                // Replace the link in pattern: [[resource name]].
                $this->tplstringreplace = $htmlresource;
                $this->tplstringsearch = $instancename;

                $newsummary = preg_replace_callback("/(\[\[)(([<][^>]*>)*)((" . preg_quote($this->tplstringsearch, '/') .
                    ")(:?))([^\]]*)\]\]/i", [$this, "replace_tag_in_expresion"], $summary);

                if ($newsummary != $summary) {
                    $this->format->tplcmsused[] = $modnumber;
                }

                if ($cmdata->showinlinehelp) {
                    $newsummary .= $renderer->render_from_template('format_menutopic/courseformat/content/cm/cmhelpinfo', $cmdata);
                }

                $summary = $newsummary;

            }
        }

        return $summary;

    }

    /**
     * Replace a tag into the summary.
     *
     * @param array $match
     * @return array
     */
    public function replace_tag_in_expresion($match) {

        $term = $match[0];
        $term = str_replace("[[", '', $term);
        $term = str_replace("]]", '', $term);

        $text = strip_tags($term);

        if (strpos($text, ':') > -1) {

            $pattern = '/([^:])+:/i';
            $text = preg_replace($pattern, '', $text);

            // Change text for alternative text.
            $newreplace = str_replace($this->tplstringsearch, $text, $this->tplstringreplace);

            // Posible html tags position.
            $pattern = '/([>][^<]*:[^<]*[<])+/i';
            $term = preg_replace($pattern, '><:><', $term);

            $pattern = '/([>][^<]*:[^<]*$)+/i';
            $term = preg_replace($pattern, '><:>', $term);

            $pattern = '/(^[^<]*:[^<]*[<])+/i';
            $term = preg_replace($pattern, '<:><', $term);

            $pattern = '/(^[^<]*:[^<]*$)/i';
            $term = preg_replace($pattern, '<:>', $term);

            $pattern = '/([>][^<^:]*[<])+/i';
            $term = preg_replace($pattern, '><', $term);

            $term = str_replace('<:>', $newreplace, $term);
        } else {
            // Change tag for resource or mod name.
            $newreplace = str_replace($this->tpltagstring, $this->tplstringsearch, $this->tplstringreplace);
            $term = str_replace($this->tplstringsearch, $newreplace, $term);
        }
        return $term;
    }
}
