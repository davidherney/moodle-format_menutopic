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
 * Settings for format.
 *
 * @package format_menutopic
 * @copyright 2023 David Herney Bernal - cirano. https://bambuco.co
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot. '/course/format/menutopic/lib.php');

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configcheckbox('format_menutopic/anchortomenutree',
                                                    get_string('enableanchorposition', 'format_menutopic'),
                                                    get_string('enableanchorposition_help', 'format_menutopic'), 1));

    $fields = [
        \format_menutopic::SECTIONSNAVIGATION_SUPPORT => new lang_string('navigationposition_support', 'format_menutopic'),
        \format_menutopic::SECTIONSNAVIGATION_NOT => new lang_string('navigationposition_nothing', 'format_menutopic'),
        \format_menutopic::SECTIONSNAVIGATION_BOTTOM => new lang_string('navigationposition_bottom', 'format_menutopic'),
        \format_menutopic::SECTIONSNAVIGATION_TOP => new lang_string('navigationposition_top', 'format_menutopic'),
        \format_menutopic::SECTIONSNAVIGATION_BOTH => new lang_string('navigationposition_both', 'format_menutopic'),
        \format_menutopic::SECTIONSNAVIGATION_SLIDES => new lang_string('navigationposition_slide', 'format_menutopic'),
    ];
    $settings->add(new admin_setting_configselect('format_menutopic/defaultsectionsnavigation',
                                                    get_string('defaultsectionsnavigation', 'format_menutopic'),
                                                    get_string('defaultsectionsnavigation_help', 'format_menutopic'),
                                                    \format_menutopic::SECTIONSNAVIGATION_SUPPORT,
                                                    $fields));

    $fields = [
        \format_menutopic::STYLE_BOOTS => new lang_string('style_boots', 'format_menutopic'),
        \format_menutopic::STYLE_BOOTSDARK => new lang_string('style_bootsdark', 'format_menutopic'),
        \format_menutopic::STYLE_BASIC => new lang_string('style_basic', 'format_menutopic')
    ];
    $settings->add(new admin_setting_configselect('format_menutopic/globalstyle',
                                                    get_string('globalstyle', 'format_menutopic'),
                                                    get_string('globalstyle_help', 'format_menutopic'),
                                                    \format_menutopic::STYLE_BOOTS,
                                                    $fields));

    $settings->add(new admin_setting_configcheckbox('format_menutopic/shownavbarbrand',
                                                    get_string('shownavbarbrand', 'format_menutopic'),
                                                    get_string('shownavbarbrand_help', 'format_menutopic'), 1));
}
