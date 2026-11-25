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

use core\output\action_menu\link_secondary;
use core\output\pix_icon;
use core\url;
use format_topics\output\courseformat\content\section\controlmenu as controlmenu_format_topics;

/**
 * Base class to render a course section menu.
 *
 * @package   format_menutopic
 * @copyright 2024 David Herney - cirano. https://bambuco.co
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class controlmenu extends controlmenu_format_topics {
    /** @var \course_format the course format class */
    protected $format;

    /** @var \section_info the course section class */
    protected $section;

    /**
     * Generate the edit control items of a section.
     *
     * This method must remain public until the final deprecation of section_edit_control_items.
     *
     * @return array of edit control items
     */
    public function section_control_items() {
        $format = $this->format;
        $section = $this->section;
        $course = $format->get_course();

        $parentcontrols = parent::section_control_items();

        // ToDo: reload the page is a temporal solution. We need control the delete tab action with JS.
        if (array_key_exists("delete", $parentcontrols)) {
            $url = new \moodle_url('/course/editsection.php', [
                'id' => $section->id,
                'sr' => $section->section - 1,
                'delete' => 1,
                'sesskey' => sesskey(),
            ]);

            $parentcontrols['delete'] = new link_secondary(
                url: $url,
                icon: new pix_icon('i/delete', ''),
                text: get_string('delete'),
                attributes: ['class' => 'editing_delete text-danger'],
            );
        }

        // Create the permalink according to the Menutopic format.
        if (array_key_exists("permalink", $parentcontrols)) {
            $sectionlink = new \moodle_url(
                '/course/view.php',
                [
                    'id' => $course->id,
                    'sectionid' => $section->id,
                ],
                'tabs-tree-start'
            );

            $parentcontrols['permalink'] = new link_secondary(
                url: $sectionlink,
                icon: new pix_icon('i/link', ''),
                text: get_string('sectionlink', 'course'),
                attributes: [
                    'class' => 'icon',
                    'data-action' => 'permalink',
                ],
            );
        }

        return $parentcontrols;
    }
}
