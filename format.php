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
 * Display the whole course as "menu"
 * Included from "view.php"
 * It is based of the "topics" format.
 *
 * @since 2.4
 * @package format_menutopic
 * @copyright 2012 David Herney Bernal - cirano
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/completionlib.php');

// Horrible backwards compatible parameter aliasing..
if ($topic = optional_param('topic', 0, PARAM_INT)) {
    $url = $PAGE->url;
    $url->param('section', $topic);
    debugging('Outdated topic param passed to course/view.php', DEBUG_DEVELOPER);
    redirect($url);
}
// End backwards-compatible aliasing.

$context = context_course::instance($course->id);

if (($marker >= 0) && has_capability('moodle/course:setcurrentsection', $context) && confirm_sesskey()) {
    $course->marker = $marker;
    course_set_marker($course->id, $marker);
}

$course = course_get_format($course)->get_course();

// Menutopic format is always multipage.

$renderer = $PAGE->get_renderer('format_menutopic');

$section = optional_param('section', -1, PARAM_INT);
$course->editmenumode = optional_param('editmenumode', false, PARAM_BOOL);

if (isset($section) && $section >= 0) {
     $USER->display[$course->id] = $section;
     $displaysection = $section;
} else {
    if (isset($USER->display[$course->id])) {
        $displaysection = $USER->display[$course->id];
    } else {
        $USER->display[$course->id] = 0;
        $displaysection = 0;
    }
}

if ($course->editmenumode) {
    $renderer->print_edition_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection);
} else {
    $renderer->print_single_section_page($course, null, $mods, $modnames, $modnamesused, $displaysection);
}

// Include course format js module.
$PAGE->requires->js('/course/format/topics/format.js');
$PAGE->requires->js('/course/format/menutopic/format.js');
