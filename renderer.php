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
 * @since 2.4
 * @package contribution
 * @copyright 2012 David Herney Bernal - cirano
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/course/format/renderer.php');

/**
 * Basic renderer for menutopic format.
 *
 * @copyright 2012 David Herney Bernal - cirano
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_menutopic_renderer extends format_section_renderer_base {

    private $_format_data;
    private $_course;

    /**
     * Generate the starting container html for a list of sections
     * @return string HTML to output.
     */
    protected function start_section_list() {
        return html_writer::start_tag('ul', array('class' => 'topics'));
    }

    /**
     * Generate the closing container html for a list of sections
     * @return string HTML to output.
     */
    protected function end_section_list() {
        return html_writer::end_tag('ul');
    }

    /**
     * Generate the title for this section page
     * @return string the page title
     */
    protected function page_title() {
        return get_string('topicoutline');
    }

    /**
     * Generate next/previous section links for navigation
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections The course_sections entries from the DB
     * @param int $sectionno The section number in the coruse which is being dsiplayed
     * @return array associative array with previous and next section link
     */
    protected function get_nav_links($course, $sections, $sectionno) {
        // FIXME: This is really evil and should by using the navigation API.
        $course = course_get_format($course)->get_course();
        $canviewhidden = has_capability('moodle/course:viewhiddensections', context_course::instance($course->id))
            or !$course->hiddensections;

        $links = array('previous' => '', 'next' => '');
        $back = $sectionno - 1;

        while ((($back > 0 && $course->realcoursedisplay == COURSE_DISPLAY_MULTIPAGE) || ($back >= 0 && $course->realcoursedisplay != COURSE_DISPLAY_MULTIPAGE)) &&
                empty($links['previous'])) {
            if ($canviewhidden || $sections[$back]->uservisible) {
                $params = array();
                if (!$sections[$back]->visible) {
                    $params = array('class' => 'dimmed_text');
                }
                $previouslink = html_writer::tag('span', $this->output->larrow(), array('class' => 'larrow'));
                $previouslink .= get_section_name($course, $sections[$back]);
                $links['previous'] = html_writer::link(course_get_url($course, $back), $previouslink, $params);
            }
            $back--;
        }

        $forward = $sectionno + 1;
        while ($forward <= $course->numsections and empty($links['next'])) {
            if ($canviewhidden || $sections[$forward]->uservisible) {
                $params = array();
                if (!$sections[$forward]->visible) {
                    $params = array('class' => 'dimmed_text');
                }
                $nextlink = get_section_name($course, $sections[$forward]);
                $nextlink .= html_writer::tag('span', $this->output->rarrow(), array('class' => 'rarrow'));
                $links['next'] = html_writer::link(course_get_url($course, $forward), $nextlink, $params);
            }
            $forward++;
        }

        return $links;
    }

    /**
     * Generate next/previous section links for navigation according to menu configuration
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections The course_sections entries from the DB
     * @param int $sectionno The section number in the coruse which is being dsiplayed
     * @param string $nodesnavigation List of section numbers, separated with coma
     * @return array associative array with previous and next section link
     */
    protected function get_custom_nav_links($course, $sections, $sectionno, $nodesnavigation) {
        // FIXME: This is really evil and should by using the navigation API.
        $canviewhidden = has_capability('moodle/course:viewhiddensections', context_course::instance($course->id))
            or !$course->hiddensections;

        $links = array('previous' => '', 'next' => '');
        $current_exists = false;

        $ids_topics = explode(',', $nodesnavigation);

        $pos = 0;
        foreach($ids_topics as $id_topic) {
            if (trim($id_topic) == $sectionno) {
                $current_exists = true;
                break;
            }
            $pos++;
        }

        if($current_exists) {

            if ($pos >= count($ids_topics)) {
                $previous_topic = $next_topic = null;
            }
            else if ($pos == 0) {
                $previous_topic = null;
                $next_topic = $ids_topics[1];
            }
            else if ($pos == (count($ids_topics) - 1)) {
                $previous_topic = $ids_topics[$pos - 1];
                $next_topic = null;
            }
            else {
                $previous_topic = $ids_topics[$pos - 1];
                $next_topic = $ids_topics[$pos + 1];
            }

            if (($canviewhidden || $sections[$previous_topic]->visible) && isset($sections[$previous_topic])) {
                $params = array();
                if (!$sections[$previous_topic]->visible) {
                    $params = array('class' => 'dimmed_text');
                }
                $previouslink = html_writer::tag('span', $this->output->larrow(), array('class' => 'larrow'));
                $previouslink .= get_section_name($course, $sections[$previous_topic]);
                $previous_url = course_get_url($course, $previous_topic);
                /*if ($previous_topic === 0) {
                    str_replace('#section-0', '&section=0', $previous_url);
                }*/
                
                $links['previous'] = html_writer::link($previous_url, $previouslink, $params);
                
            }
            
            if (($canviewhidden || $sections[$next_topic]->visible) && isset($sections[$next_topic])) {
                $params = array();
                if (!$sections[$next_topic]->visible) {
                    $params = array('class' => 'dimmed_text');
                }
                $nextlink = get_section_name($course, $sections[$next_topic]);
                $nextlink .= html_writer::tag('span', $this->output->rarrow(), array('class' => 'rarrow'));
                $links['next'] = html_writer::link(course_get_url($course, $next_topic), $nextlink, $params);
            }
        }
        return $links;
    }

    /**
     * Output the html for a single section page .
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections The course_sections entries from the DB
     * @param array $mods used for print_section()
     * @param array $modnames used for print_section()
     * @param array $modnamesused used for print_section()
     * @param int $displaysection The section number in the course which is being displayed
     */
    public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {
        global $PAGE, $DB;

        $real_course_display = $course->realcoursedisplay;
        $modinfo = get_fast_modinfo($course);
        $course = course_get_format($course)->get_course();
        $course->realcoursedisplay = $real_course_display; 
        $sections = $modinfo->get_section_info_all();

        if ($displaysection === 0 && $course->realcoursedisplay == COURSE_DISPLAY_MULTIPAGE) {
            foreach($sections as $id=>$sec) {
                if ($id > 0 && $sec->visible) {
                    $displaysection = $id;
                    break;
                }
            }
        }

        // Can we view the section in question?
        $context = context_course::instance($course->id);
        $canviewhidden = has_capability('moodle/course:viewhiddensections', $context);

        if (!isset($sections[$displaysection])) {
            // This section doesn't exist
            print_error('unknowncoursesection', 'error', null, $course->fullname);
            return;
        }

        //Load Menu configuration
        $config_menu = new object();
        $config_menu->cssdefault = true;
        $config_menu->menuposition = 'middle';
        $config_menu->linkinparent = false;
        $config_menu->templatetopic = false;
        $config_menu->icons_templatetopic = false;
        $config_menu->displaynousedmod = false;
        $config_menu->displaynavigation = 'nothing';
        $config_menu->nodesnavigation = '';

        //Load configuration data
        if(!($format_data = $DB->get_record('format_menutopic', array('course'=>$course->id)))){
            $format_data = new stdClass();
            $format_data->course    = $course->id;

            if (!($format_data->id = $DB->insert_record('format_menutopic', $format_data))) {
                debugging('Not is possible save the course format data in menutopic format', DEBUG_DEVELOPER);
            }
        }

        if (!is_object($format_data)) {
            $format_data = new stdClass();
        }

        if (!empty($format_data->tree)) {
            $format_data->tree = json_decode(stripslashes($format_data->tree));
            $autobuild_tree = false;
        }
        else {
            $format_data->tree = new stdClass();
            $format_data->tree->topics = array();
            $autobuild_tree = true;
        }


        if (property_exists($format_data, 'config') && !empty($format_data->config)) {
            $config_saved = @unserialize($format_data->config);

            if (!is_object($config_saved)) {
                $config_saved = new object();
            }

            if (isset($config_saved->cssdefault)) { $config_menu->cssdefault = $config_saved->cssdefault; }

            if (isset($config_saved->menuposition)) { $config_menu->menuposition = $config_saved->menuposition; }

            if (isset($config_saved->linkinparent)) { $config_menu->linkinparent = $config_saved->linkinparent; }

            if (isset($config_saved->templatetopic)) { $config_menu->templatetopic = $config_saved->templatetopic; }

            if (isset($config_saved->icons_templatetopic)) { $config_menu->icons_templatetopic = $config_saved->icons_templatetopic; }

            if (isset($config_saved->displaynousedmod)) { $config_menu->displaynousedmod = $config_saved->displaynousedmod; }

            if (isset($config_saved->displaynavigation)) { $config_menu->displaynavigation = $config_saved->displaynavigation; }

            if (isset($config_saved->nodesnavigation)) { $config_menu->nodesnavigation = $config_saved->nodesnavigation; }
        }

        $section = 0;

        while ($section <= $course->numsections) {
            if ($course->realcoursedisplay == COURSE_DISPLAY_MULTIPAGE && $section == 0) {
                $section++;
                continue;
            }

            $thissection = $sections[$section];

            $showsection = true;
            if (!$thissection->visible || !$thissection->available) {
                $showsection = false;
            }
            else if ($section == 0 && !($thissection->summary || $thissection->sequence || $PAGE->user_is_editing())){
                $showsection = false;
            }

            if ($showsection || $canviewhidden || !$course->hiddensections) {
                if ($autobuild_tree) {
                    $topic = new stdClass();
                    $topic->name = get_section_name($course, $thissection);
                    $topic->subtopics = array();
                    $topic->hidden = !$showsection;

                    if ($showsection || $canviewhidden) {
                        $topic->topicnumber = $section;
                    }
                    else {
                        $topic->topicnumber = null;
                    }

                    $topic->url = "";
                    $topic->target = "";
                    $format_data->tree->topics[] = $topic;
                }
                else if (!$showsection) {
                    $this->_remove_topic_in_tree ($format_data->tree->topics, $section, (!$course->hiddensections || $canviewhidden), !$canviewhidden);
                }
            }
            else {
                if (!$autobuild_tree) {
                    $this->_remove_topic_in_tree ($format_data->tree->topics, $section, false, true);
                }
            }

            $section++;
        }

        $format_data->config_menu = $config_menu;
        $format_data->mods = $mods;
        $format_data->modinfo = $modinfo;
        $this->_course = $course;
        $this->_format_data = $format_data;
        //END OF Load Menu configuration

        if ($PAGE->user_is_editing()) {

            echo html_writer::start_tag('form', array('method' => 'post'));
            echo html_writer::empty_tag('input', array('type' => 'submit', 'value'=>get_string('editmenu', 'format_menutopic')));
            echo html_writer::empty_tag('input', array('type' => 'hidden', 'value'=>'true', 'name'=>'editmenumode'));
            echo html_writer::empty_tag('input', array('type' => 'hidden', 'value'=>$course->id, 'name'=>'id'));
            echo html_writer::end_tag('form');
        }

        // Copy activity clipboard..
        echo $this->course_activity_clipboard($course, $displaysection);

        // General section if non-empty and course_display is multiple.
        if ($course->realcoursedisplay == COURSE_DISPLAY_MULTIPAGE && $displaysection !== 0) {
            $thissection = $sections[0];
            if ((($thissection->visible && $thissection->available) || $canviewhidden) && ($thissection->summary || $thissection->sequence || $PAGE->user_is_editing())) {
                echo $this->start_section_list();
                echo $this->section_header($thissection, $course, true);
                echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
                echo $this->courserenderer->course_section_add_cm_control($course, 0, $displaysection);
                echo $this->section_footer();
                echo $this->end_section_list();
            }
        }

        $this->print_menu($format_data, $course, $sections, $mods, $modnames, $modnamesused, $displaysection);

        // Start single-section div
        echo html_writer::start_tag('div', array('class' => 'single-section menutopic'));

        // Title with section navigation links.
        if (empty($format_data->config_menu->nodesnavigation)) {
            $sectionnavlinks = $this->get_nav_links($course, $sections, $displaysection);
        }
        else {
            $sectionnavlinks = $this->get_custom_nav_links($course, $sections, $displaysection, $format_data->config_menu->nodesnavigation);
        }

        $sectiontitle = '';
        $sectiontitle .= html_writer::start_tag('div', array('class' => 'section-navigation header headingblock'));

        if ($format_data->config_menu->displaynavigation == 'top' || $format_data->config_menu->displaynavigation == 'both') {
            $sectiontitle .= html_writer::tag('span', $sectionnavlinks['previous'], array('class' => 'mdl-left'));
            $sectiontitle .= html_writer::tag('span', $sectionnavlinks['next'], array('class' => 'mdl-right'));
        }
        // Title attributes
        $titleattr = 'mdl-align title';
        if (!$sections[$displaysection]->visible) {
            $titleattr .= ' dimmed_text';
        }
        $sectiontitle .= html_writer::tag('div', get_section_name($course, $sections[$displaysection]), array('class' => $titleattr));
        $sectiontitle .= html_writer::end_tag('div');
        echo $sectiontitle;

        // Now the list of sections..
        echo $this->start_section_list();

        // The requested section page.
        $thissection = $sections[$displaysection];
        echo $this->section_header($thissection, $course, true);
        // Show completion help icon.
        $completioninfo = new completion_info($course);
        echo $completioninfo->display_help_icon();

        if (!$config_menu->templatetopic || $config_menu->displaynousedmod || $PAGE->user_is_editing()) {
            echo $this->custom_course_section_cm_list($course, $thissection, $displaysection);
        }

        echo $this->courserenderer->course_section_add_cm_control($course, $displaysection, $displaysection);
        echo $this->section_footer();
        echo $this->end_section_list();

        if ($format_data->config_menu->displaynavigation == 'bottom' || $format_data->config_menu->displaynavigation == 'both') {
            // Display section bottom navigation.
            $sectionbottomnav = '';
            $sectionbottomnav .= html_writer::start_tag('div', array('class' => 'section-navigation mdl-bottom'));
            $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['previous'], array('class' => 'mdl-left'));
            $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['next'], array('class' => 'mdl-right'));
            $sectionbottomnav .= html_writer::end_tag('div');
            echo $sectionbottomnav;
        }

        // close single-section div.
        echo html_writer::end_tag('div');
        
        // Include course format js module
        $PAGE->requires->js('/course/format/menutopic/format.js');
    }

    /**
     * Output the html for a edit mode page.
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections The course_sections entries from the DB
     * @param array $mods used for print_section()
     * @param array $modnames used for print_section()
     * @param array $modnamesused used for print_section()
     * @param int $displaysection The section number in the course which is being displayed
     */
    public function print_edition_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {
        global $PAGE, $CFG, $DB, $OUTPUT;

        if (!$PAGE->user_is_editing()) {
            $this->print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection);
            return;
        }

        echo html_writer::start_tag('form', array('method' => 'GET'));
        echo html_writer::empty_tag('input', array('type' => 'submit', 'value'=>get_string('end_editmenu', 'format_menutopic')));
        echo html_writer::empty_tag('input', array('type' => 'hidden', 'value'=>$displaysection, 'name'=>'section'));
        echo html_writer::empty_tag('input', array('type' => 'hidden', 'value'=>$course->id, 'name'=>'id'));
        echo html_writer::end_tag('form');

        $menuaction = optional_param('menuaction', 'config', PARAM_ALPHA);

        $options = array('config', 'tree', 'jstemplate', 'csstemplate');

        if (!in_array($menuaction, $options)) {
            $menuaction = 'config';
        }

        $course_link         = new moodle_url($CFG->wwwroot.'/course/view.php', array('id'=>$course->id, 'editmenumode'=>'true', 'section'=>$displaysection));
        $course_cancel_link = new moodle_url($CFG->wwwroot.'/course/view.php', array('id'=>$course->id, 'section'=>$displaysection));

        $tabs = array();

        $tabs[] = new tabobject("tab_configmenu_config", $course_link . '&menuaction=config',
        '<div style="white-space:nowrap">' . get_string('config_editmenu', 'format_menutopic') . "</div>", get_string('config_editmenu', 'format_menutopic'));
        $tabs[] = new tabobject("tab_configmenu_tree", $course_link . '&menuaction=tree',
        '<div style="white-space:nowrap">' . get_string('tree_editmenu', 'format_menutopic') . "</div>", get_string('tree_editmenu', 'format_menutopic'));
        $tabs[] = new tabobject("tab_configmenu_jstemplate", $course_link . '&menuaction=jstemplate',
        '<div style="white-space:nowrap">' . get_string('jstemplate_editmenu', 'format_menutopic') . "</div>", get_string('jstemplate_editmenu', 'format_menutopic'));
        $tabs[] = new tabobject("tab_configmenu_csstemplate", $course_link . '&menuaction=csstemplate',
        '<div style="white-space:nowrap">' . get_string('csstemplate_editmenu', 'format_menutopic') . "</div>", get_string('csstemplate_editmenu', 'format_menutopic'));

        print_tabs(array($tabs), "tab_configmenu_" . $menuaction);

        // Start box container
        echo html_writer::start_tag('div', array('class' => 'box generalbox'));

        if(!($format_data = $DB->get_record('format_menutopic', array('course'=>$course->id)))){
            $format_data = new stdClass();
            $format_data->course    = $course->id;

            if (!($format_data->id = $DB->insert_record('format_menutopic', $format_data))) {
                debugging('Not is possible save the course format data in menutopic format', DEBUG_DEVELOPER);
                redirect($course_cancel_link);
            }
        }

        include $CFG->dirroot . '/course/format/menutopic/form_' . $menuaction . '.php';

        // close box container
        echo html_writer::end_tag('div');
    }

    /**
     * Print the custom menu
     *
     * @param stdClass $format_data Data used to create menu and other functionality in the format
     * @param stdClass $course The course entry from DB
     * @param array $sections The course_sections entries from the DB
     * @param array $mods used for print_section()
     * @param array $modnames used for print_section()
     * @param array $modnamesused used for print_section()
     * @param int $displaysection The section number in the course which is being displayed
     */
    public function print_menu($format_data, $course, $sections, $mods, $modnames, $modnamesused, $displaysection) {
        global $CFG, $PAGE;

        if (!empty($format_data->tree) && $format_data->config_menu->menuposition != 'hide') {

            require_once $CFG->dirroot . '/course/format/menutopic/menu.php';

            $menu = new format_menutopic_menu();
            $menu->tree = $format_data->tree;
            $menu->current = $displaysection;

            $print_for_menu = '';

            if (!empty($format_data->js)) {
                $PAGE->requires->js_init_code($format_data->js, true);
            }

            if (!empty($format_data->css)) {
                $print_for_menu .= html_writer::start_tag('style');
                $print_for_menu .= $format_data->css;
                $print_for_menu .= html_writer::end_tag('style');
            }

            echo $print_for_menu;

            //HTML code for load the menu
            echo $menu->script_menu($format_data->config_menu, $displaysection, $format_data->config_menu->cssdefault);

        }
    }

    /**
     * Generate html for a section summary text
     *
     * @param stdClass $section The course_section entry from DB
     * @return string HTML to output.
     */
    protected function format_summary_text($section) {

        if ($this->_format_data->config_menu->templatetopic) {
            $section->summary = $this->replace_resources($section);
        }

        return parent::format_summary_text($section);
    }

    private function replace_resources ($section) {

        global $CFG, $USER;

        static $initialised;

        static $groupbuttons;
        static $groupbuttonslink;
        static $strunreadpostsone;
        static $usetracking;
        static $groupings;

        $course = $this->_course;
        $completioninfo = new completion_info($course);

        if (!isset($initialised)) {
            $groupbuttons     = ($course->groupmode || (!$course->groupmodeforce));
            $groupbuttonslink = (!$course->groupmodeforce);
            include_once($CFG->dirroot.'/mod/forum/lib.php');
            if ($usetracking = forum_tp_can_track_forums()) {
                $strunreadpostsone = get_string('unreadpostsone', 'forum');
            }
            $initialised = true;
        }

        $labelformatoptions = new object();
        $labelformatoptions->noclean = true;

        /// Casting $course->modinfo to string prevents one notice when the field is null
        $modinfo = $this->_format_data->modinfo;

        $summary = $section->summary;

        $html_resource = '';
        $html_more     = '';

        if (!empty($section->sequence)) {
            $sectionmods = explode(",", $section->sequence);

            $obj_replace = new format_menutopic_replace_regularexpression();

            foreach ($sectionmods as $modnumber) {
                if (empty($this->_format_data->mods[$modnumber])) {
                    continue;
                }

                $mod = $this->_format_data->mods[$modnumber];

                if ($mod->modname == "label") {
                    continue;
                }

                /*if (isset($modinfo->cms[$modnumber])) {
                    if (!$modinfo->cms[$modnumber]->uservisible) {
                        $instancename = format_string($modinfo->cms[$modnumber]->name, true,  $course->id);
                        //Replace the link in pattern: [[resource name]]
                        $summary = preg_replace("/(\[\[)(([<][^>]*>)*)((" . preg_quote($instancename, '/') . ")(:?))([^\]]*)\]\]/i", '<span class="conditionalhidden">' . $instancename . '</span>', $summary);
                        continue;
                    }
                }
                else {
                    if (!file_exists("$CFG->dirroot/mod/$mod->modname/lib.php")) {
                        // module not installed
                        continue;
                    }
                    if (!coursemodule_visible_for_user($mod)) {
                        // full visibility check
                        continue;
                    }
                }*/

                $instancename = format_string($modinfo->cms[$modnumber]->name, true, $course->id);

                /*$html_resource =  '<span class="context_activity activity '.$mod->modname.'">';

                $extra = '';
                if (!empty($modinfo->cms[$modnumber]->extra)) {
                    $extra = $modinfo->cms[$modnumber]->extra;
                }


                if (!empty($modinfo->cms[$modnumber]->icon)) {
                    $icon = $this->output->pix_url($modinfo->cms[$modnumber]->icon);
                } else {
                    $icon = $this->output->pix_url('icon', 'mod_' . $mod->modname);
                }

                //Accessibility: for files get description via icon.
                $altname = '';
                if ('resource' == $mod->modname) {
                    if (!empty($modinfo->cms[$modnumber]->icon)) {
                        $possaltname = $modinfo->cms[$modnumber]->icon;

                        $instancename = $mod->get_formatted_name();
                        $altname = $mod->modfullname;
                    }
                    else {
                        $altname = $mod->modfullname;
                    }
                }
                else {
                    $altname = $mod->modfullname;
                }
                // Avoid unnecessary duplication.
                if (false!==stripos($instancename, $altname)) {
                    $altname = '';
                }
                // File type after name, for alphabetic lists (screen reader).
                if ($altname) {
                    $altname = get_accesshide(' '.$altname);
                }

                $linkcss = $mod->visible ? "" : " class=\"dimmed\" ";*/

                // Display the link to the module (or do nothing if module has no url)
                $cmname = $this->courserenderer->course_section_cm_name($mod);

                if (!empty($cmname)) {
                    $cmname = str_replace('<div ', '<span ', $cmname);
                    $cmname = str_replace('</div>', '</span>', $cmname);
                    $html_resource = $cmname . $mod->afterlink;
                }
                else {
                    $html_resource = '';
                }

                // If there is content but NO link (eg label), then display the
                // content here (BEFORE any icons). In this case cons must be
                // displayed after the content so that it makes more sense visually
                // and for accessibility reasons, e.g. if you have a one-line label
                // it should work similarly (at least in terms of ordering) to an
                // activity.
                $contentpart = $this->courserenderer->course_section_cm_text($mod);

                $url = $mod->url;
                if (!empty($url)) {
                    // If there is content AND a link, then display the content here
                    // (AFTER any icons). Otherwise it was displayed before
                    $html_resource .= $contentpart;
                }

                $availability_text = trim($this->courserenderer->course_section_cm_availability($mod));

                if (!empty($availability_text)) {
                    $unique_id = 'format_menutopic_winfo_' . time() . '-' . rand(0, 1000);
                    $html_resource .= '<img src="' . $this->output->pix_url('a/help') . '" class="iconhelp" alt="" onclick="M.course.format.show_info(\'' . $unique_id . '\')" />';

                    $html_more .= '<div id="' . $unique_id . '" class="availability_info_box" style="display: none;">' . $availability_text . '</div>';
                }

                //Replace the link in pattern: [[resource name]]
                $obj_replace->_string_replace = $html_resource;
                $obj_replace->_string_search = $instancename;

                $new_summary = preg_replace_callback("/(\[\[)(([<][^>]*>)*)((" . preg_quote($obj_replace->_string_search, '/') . ")(:?))([^\]]*)\]\]/i", array($obj_replace, "replace_tag_in_expresion"), $summary); 

                if ($new_summary != $summary) {
                    unset($this->_format_data->mods[$modnumber]);
                }

                $summary = $new_summary;
            }

        }

        if (!$this->_format_data->config_menu->icons_templatetopic) {
            $summary = '<span class="menutopic_hideicons">' . $summary . '</span>';
        }

        return $summary . $html_more;

    }

    /**
     * Customization of core_course_renderer->course_section_cm_list
     *
     *
     * @param stdClass $course course object
     * @param int|stdClass|section_info $section relative section number or section object
     * @param int $sectionreturn section number to return to
     * @param int $displayoptions
     * @return void
     */
    public function custom_course_section_cm_list($course, $section, $sectionreturn = null, $displayoptions = array()) {
        global $USER;

        $output = '';
        $modinfo = $this->_format_data->modinfo;
        if (is_object($section)) {
            $section = $modinfo->get_section_info($section->section);
        } else {
            $section = $modinfo->get_section_info($section);
        }
        $completioninfo = new completion_info($course);

        // check if we are currently in the process of moving a module with JavaScript disabled
        $ismoving = $this->courserenderer->page->user_is_editing() && ismoving($course->id);
        if ($ismoving) {
            $movingpix = new pix_icon('movehere', get_string('movehere'), 'moodle', array('class' => 'movetarget'));
            $strmovefull = strip_tags(get_string("movefull", "", "'$USER->activitycopyname'"));
        }

        // Get the list of modules visible to user (excluding the module being moved if there is one)
        $moduleshtml = array();
        if (!empty($modinfo->sections[$section->section])) {
            foreach ($modinfo->sections[$section->section] as $modnumber) {

                //Custom modification in order to hide resources if they are shown in summary
                if (!$this->courserenderer->page->user_is_editing() && !isset($this->_format_data->mods[$modnumber])) {
                    continue;
                }
                //End of custom modification

                $mod = $modinfo->cms[$modnumber];

                if ($ismoving and $mod->id == $USER->activitycopy) {
                    // do not display moving mod
                    continue;
                }

                if ($modulehtml = $this->courserenderer->course_section_cm_list_item($course,
                        $completioninfo, $mod, $sectionreturn, $displayoptions)) {
                    $moduleshtml[$modnumber] = $modulehtml;
                }
            }
        }

        $sectionoutput = '';
        if (!empty($moduleshtml) || $ismoving) {
            foreach ($moduleshtml as $modnumber => $modulehtml) {
                if ($ismoving) {
                    $movingurl = new moodle_url('/course/mod.php', array('moveto' => $modnumber, 'sesskey' => sesskey()));
                    $sectionoutput .= html_writer::tag('li',
                            html_writer::link($movingurl, $this->courserenderer->output->render($movingpix), array('title' => $strmovefull)),
                            array('class' => 'movehere'));
                }

                $sectionoutput .= $modulehtml;
            }

            if ($ismoving) {
                $movingurl = new moodle_url('/course/mod.php', array('movetosection' => $section->id, 'sesskey' => sesskey()));
                $sectionoutput .= html_writer::tag('li',
                        html_writer::link($movingurl, $this->courserenderer->output->render($movingpix), array('title' => $strmovefull)),
                        array('class' => 'movehere'));
            }
        }

        // Always output the section module list.
        $output .= html_writer::tag('ul', $sectionoutput, array('class' => 'section img-text'));

        return $output;
    }

    private function _remove_topic_in_tree ($topics, $section, $only_hide = false, $remove_topic = true) {

        foreach ($topics as $key=>$topic) {

            if ($topic->topicnumber == $section) {
                if ($only_hide) {
                    $topic->hidden = true;
                    if (isset($topic->subtopics) && is_array($topic->subtopics) && count($topic->subtopics) > 0) {
                        $this->_remove_topic_in_tree($topic->subtopics, $section, $only_hide, $remove_topic);
                    }
                }
                else {
                    $topic->visible = false;
                    $topic->subtopics = array();
                }

                if ($remove_topic) {
                    $topic->topicnumber = null;
                    $topic->subtopics = array();
                }
            }
            else if (isset($topic->subtopics) && is_array($topic->subtopics) && count($topic->subtopics) > 0) {
                $this->_remove_topic_in_tree($topic->subtopics, $section, $only_hide, $remove_topic);
            }
        }

    }

    /**
     * Generate the edit control items of a section
     *
     * @param stdClass $course The course entry from DB
     * @param stdClass $section The course_section entry from DB
     * @param bool $onsectionpage true if being printed on a section page
     * @return array of edit control items
     */
    protected function section_edit_control_items($course, $section, $onsectionpage = false) {
        global $PAGE;

        if (!$PAGE->user_is_editing()) {
            return array();
        }

        $coursecontext = context_course::instance($course->id);

        if ($onsectionpage) {
            $url = course_get_url($course, $section->section);
        } else {
            $url = course_get_url($course);
        }
        $url->param('sesskey', sesskey());

        $isstealth = $section->section > $course->numsections;
        $controls = array();
        if (!$isstealth && $section->section && has_capability('moodle/course:setcurrentsection', $coursecontext)) {
            if ($course->marker == $section->section) {  // Show the "light globe" on/off.
                $url->param('marker', 0);
                $markedthistopic = get_string('markedthistopic');
                $highlightoff = get_string('highlightoff');
                $controls['highlight'] = array('url' => $url, "icon" => 'i/marked',
                                               'name' => $highlightoff,
                                               'pixattr' => array('class' => '', 'alt' => $markedthistopic),
                                               'attr' => array('class' => 'editing_highlight', 'title' => $markedthistopic));
            } else {
                $url->param('marker', $section->section);
                $markthistopic = get_string('markthistopic');
                $highlight = get_string('highlight');
                $controls['highlight'] = array('url' => $url, "icon" => 'i/marker',
                                               'name' => $highlight,
                                               'pixattr' => array('class' => '', 'alt' => $markthistopic),
                                               'attr' => array('class' => 'editing_highlight', 'title' => $markthistopic));
            }
        }

        $parentcontrols = parent::section_edit_control_items($course, $section, $onsectionpage);

        // If the edit key exists, we are going to insert our controls after it.
        if (array_key_exists("edit", $parentcontrols)) {
            $merged = array();
            // We can't use splice because we are using associative arrays.
            // Step through the array and merge the arrays.
            foreach ($parentcontrols as $key => $action) {
                $merged[$key] = $action;
                if ($key == "edit") {
                    // If we have come to the edit key, merge these controls here.
                    $merged = array_merge($merged, $controls);
                }
            }

            return $merged;
        } else {
            return array_merge($controls, $parentcontrols);
        }
    }
}
