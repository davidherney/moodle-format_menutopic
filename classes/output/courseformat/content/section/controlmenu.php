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
 * Contains the default section controls output class.
 *
 * @package   format_menutopic
 * @copyright 2022 David Herney - cirano. https://bambuco.co
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_menutopic\output\courseformat\content\section;

use format_topics\output\courseformat\content\section\controlmenu as controlmenu_format_topics;

/**
 * Base class to render a course section menu.
 *
 * @package   format_menutopic
 * @copyright 2024 David Herney - cirano. https://bambuco.co
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class controlmenu extends controlmenu_format_topics {

    /** @var course_format the course format class */
    protected $format;

    /** @var section_info the course section class */
    protected $section;

    /**
     * Generate the edit control items of a section.
     *
     * This method must remain public until the final deprecation of section_edit_control_items.
     *
     * @return array of edit control items
     */
    public function section_control_items() {
        global $USER;

        $format = $this->format;
        $section = $this->section;
        $course = $format->get_course();
        $sectionreturn = $format->get_sectionnum();

        $coursecontext = $this->format->get_context();
        $numsections = $format->get_last_section_number();
        $isstealth = $section->section > $numsections;

        if ($sectionreturn) {
            $url = course_get_url($course, $section->section);
        } else {
            $url = course_get_url($course);
        }
        $url->param('sesskey', sesskey());

        $othercontrols = [];

        $movecontrols = [];
        if ($section->section && !$isstealth && has_capability('moodle/course:movesections', $coursecontext, $USER)) {
            $baseurl = course_get_url($course);
            $baseurl->param('sesskey', sesskey());
            $rtl = right_to_left();

            // Legacy move up and down links.
            $url = clone($baseurl);
            if ($section->section > 1) { // Add a arrow to move section up.
                $url->param('section', $section->section);
                $url->param('move', -1);
                $strmoveup = get_string('moveleft');
                $movecontrols['moveup'] = [
                    'url' => $url,
                    'icon' => $rtl ? 't/right' : 't/left',
                    'name' => $strmoveup,
                    'pixattr' => ['class' => ''],
                    'attr' => ['class' => 'icon'],
                ];
            }

            $url = clone($baseurl);
            if ($section->section < $numsections) { // Add a arrow to move section down.
                $url->param('section', $section->section);
                $url->param('move', 1);
                $strmovedown = get_string('moveright');
                $movecontrols['movedown'] = [
                    'url' => $url,
                    'icon' => ($rtl ? 't/left' : 't/right'),
                    'name' => $strmovedown,
                    'pixattr' => ['class' => ''],
                    'attr' => ['class' => 'icon'],
                ];
            }
        }

        $parentcontrols = parent::section_control_items();

        // ToDo: reload the page is a temporal solution. We need control the delete tab action with JS.
        if (array_key_exists("delete", $parentcontrols)) {
            $url = new \moodle_url('/course/editsection.php', [
                'id' => $section->id,
                'sr' => $section->section - 1,
                'delete' => 1,
                'sesskey' => sesskey(), ]);
            $parentcontrols['delete']['url'] = $url;
            unset($parentcontrols['delete']['attr']['data-action']);
        }

        // Create the permalink according to the Menutopic format.
        if (array_key_exists("permalink", $parentcontrols)) {
            $sectionlink = new \moodle_url(
                '/course/view.php',
                ['id' => $course->id, 'sectionid' => $section->id],
                'menu-tree-start');

            $parentcontrols['permalink']['url'] = $sectionlink;
        }

        // If the edit key exists, we are going to insert our controls after it.
        $merged = [];
        $editcontrolexists = array_key_exists("edit", $parentcontrols);
        $visibilitycontrolexists = array_key_exists("visibility", $parentcontrols);

        if (!$editcontrolexists) {
            $merged = array_merge($merged, $othercontrols);

            if (!$visibilitycontrolexists) {
                $merged = array_merge($merged, $movecontrols);
            }
        }

        // We can't use splice because we are using associative arrays.
        // Step through the array and merge the arrays.
        foreach ($parentcontrols as $key => $action) {
            $merged[$key] = $action;
            if ($key == "edit") {
                // If we have come to the edit key, merge these controls here.
                $merged = array_merge($merged, $othercontrols);
            }

            if (($key == "edit" && !$visibilitycontrolexists) || $key == "visibility") {
                $merged = array_merge($merged, $movecontrols);
            }
        }

        return $merged;
    }
}
