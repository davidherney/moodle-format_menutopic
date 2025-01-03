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
 * This file contains main class for the course format menutopic.
 *
 * @package   format_menutopic
 * @copyright 2016 David Herney - https://bambuco.co
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_courseformat\output\local\content as content_base;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot. '/course/format/lib.php');

/**
 * Main class for the menutopic course format.
 *
 * @package format_menutopic
 * @copyright 2012 David Herney Bernal - cirano
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_menutopic extends core_courseformat\base {

    /** @var int Only if theme not support "usescourseindex" */
    const SECTIONSNAVIGATION_SUPPORT = 'courseindex';

    /** @var int Not use */
    const SECTIONSNAVIGATION_NOT = 'nothing';

    /** @var int Only at the bottom */
    const SECTIONSNAVIGATION_BOTTOM = 'bottom';

    /** @var int Only at the top */
    const SECTIONSNAVIGATION_TOP = 'top';

    /** @var int Only at the bottom */
    const SECTIONSNAVIGATION_BOTH = 'both';

    /** @var int Like slides */
    const SECTIONSNAVIGATION_SLIDES = 'slides';

    /** @var string Use classic menu */
    const STYLE_BASIC = 'basic';

    /** @var string Use bootstrap menu structure */
    const STYLE_BOOTS = 'boots';

    /** @var string Use bootstrap dark menu structure */
    const STYLE_BOOTSDARK = 'bootsdark';

    /** @var \stdClass Local format data */
    public static $formatdata;

    /** @var array Messages to display */
    public static $formatmsgs = [];

    /** @var bool Edit menu mode */
    public static $editmenumode = false;

    /** @var array Modules used in template */
    public $tplcmsused = [];

    /** @var bool If print the menu in the current scope */
    public $printable = true;

    /** @var bool If the class was previously instanced, in one execution cycle */
    private static $loaded = false;

    /**
     * Creates a new instance of class
     *
     * Please use course_get_format($courseorid) to get an instance of the format class
     *
     * @param string $format
     * @param int $courseid
     * @return course_format
     */
    protected function __construct($format, $courseid) {

        parent::__construct($format, $courseid);

        // Hack for section number, when not is like a param in the url or section is not available.
        global $section, $sectionid, $PAGE, $USER, $urlparams, $DB;

        $inpopup = optional_param('inpopup', 0, PARAM_INT);
        if ($inpopup) {
            $this->printable = false;
        } else {
            $pagesavailable = ['course-view-menutopic', 'course-view', 'lib-ajax-service'];
            $patternavailable = '/^mod-.*-view$/';

            if (!in_array($PAGE->pagetype, $pagesavailable)) {
                $this->printable = preg_match($patternavailable, $PAGE->pagetype);
            }
        }

        $course = $this->get_course();

        if (!isset($section) && ($PAGE->pagetype == 'course-view-menutopic' || $PAGE->pagetype == 'course-view')) {

            if ($sectionid <= 0) {
                $section = optional_param('section', -1, PARAM_INT);
            }

            if ($section < 0) {
                if (isset($USER->display[$course->id])) {
                    $section = $USER->display[$course->id];
                } else if ($course->marker && $course->marker > 0) {
                    $section = (int)$course->marker;
                } else {
                    $section = 0;
                }
            }
        }

        if ($this->printable) {
            if (!self::$loaded && isset($section) && $courseid &&
                    ($PAGE->pagetype == 'course-view-menutopic' || $PAGE->pagetype == 'course-view')) {

                self::$loaded = true;

                $this->singlesection = $section;

                // The format is always multipage.
                $course->realcoursedisplay = property_exists($course, 'coursedisplay') ? $course->coursedisplay : false;
                $numsections = (int)$DB->get_field('course_sections', 'MAX(section)', ['course' => $courseid], MUST_EXIST);

                if ($section >= 0 && $numsections >= $section) {
                    $realsection = $section;
                } else {
                    $realsection = 0;
                }

                if ($course->realcoursedisplay == COURSE_DISPLAY_MULTIPAGE && $realsection === 0 && $numsections >= 1) {
                    $realsection = null;
                }

                $modinfo = get_fast_modinfo($course);
                $sections = $modinfo->get_section_info_all();

                // Check if the display section is available.
                if ($realsection === null || !$sections[$realsection]->uservisible) {

                    if ($realsection) {
                        self::$formatmsgs[] = get_string('hidden_message',
                                                            'format_menutopic',
                                                            $this->get_section_name($realsection));
                    }

                    $valid = false;
                    $k = $course->realcoursedisplay ? 1 : 0;

                    do {
                        $formatoptions = $this->get_format_options($k);
                        if ($formatoptions['level'] == 0 && $sections[$k]->uservisible) {
                            $valid = true;
                            break;
                        }

                        $k++;

                    } while (!$valid && $k <= $numsections);

                    $realsection = $valid ? $k : 0;
                }

                $realsection = $realsection ?? 0;
                // The $section var is a global var, we need to set it to the real section.
                $section = $realsection;
                $this->set_sectionnum($section);
                $USER->display[$course->id] = $realsection;
                $urlparams['section'] = $realsection;
                $PAGE->set_url('/course/view.php', $urlparams);
            }
        }

    }

    /**
     * Returns true if this course format uses sections.
     *
     * @return bool
     */
    public function uses_sections() {
        return true;
    }

    /**
     * Returns true if this course format uses course index
     *
     * @return bool
     */
    public function uses_course_index() {

        if ($this->show_editor()) {
            return true;
        }

        $course = $this->get_course();

        return isset($course->usescourseindex) ? $course->usescourseindex : true;
    }

    /**
     * Returns true if this course format uses activity indentation.
     *
     * @return bool if the course format uses indentation.
     */
    public function uses_indentation(): bool {
        return true;
    }

    /**
     * Returns the display name of the given section that the course prefers.
     *
     * Use section name is specified by user. Otherwise use default ("Topic #").
     *
     * @param int|stdClass $section Section object from database or just field section.section
     * @return string Display name that the course format prefers, e.g. "Topic 2"
     */
    public function get_section_name($section) {
        $section = $this->get_section($section);
        if ((string)$section->name !== '') {
            $coursecontext = $this->get_context();

            return format_string($section->name, true,
                ['context' => $coursecontext]);
        } else {
            return $this->get_default_section_name($section);
        }
    }

    /**
     * Get the current section number to display.
     * Some formats has the hability to swith from one section to multiple sections per page.
     *
     * @since Moodle 4.4
     * @return int|null the current section number or null when there is no single section.
     */
    public function get_sectionnum(): ?int {
        return $this->singlesection == null ? 0 : $this->singlesection;
    }

    /**
     * Returns the default section name for the topics course format.
     *
     * If the section number is 0, it will use the string with key = section0name from the course format's lang file.
     * If the section number is not 0, the base implementation of course_format::get_default_section_name which uses
     * the string with the key = 'sectionname' from the course format's lang file + the section number will be used.
     *
     * @param stdClass $section Section object from database or just field course_sections section
     * @return string The default value for the section name.
     */
    public function get_default_section_name($section) {
        if ($section->section == 0) {
            // Return the general section.
            return get_string('section0name', 'format_topics');
        } else {
            // Use course_format::get_default_section_name implementation which
            // will display the section name in "Topic n" format.
            return parent::get_default_section_name($section);
        }
    }

    /**
     * Get if the current format instance will show multiple sections or an individual one.
     *
     * Some formats has the hability to swith from one section to multiple sections per page,
     * output components will use this method to know if the current display is a single or
     * multiple sections.
     *
     * @return int|null null for all sections or the sectionid.
     */
    public function get_sectionid(): ?int {
        return null;
    }

    /**
     * Generate the title for this section page.
     *
     * @return string the page title
     */
    public function page_title(): string {
        return get_string('sectionoutline');
    }

    /**
     * The URL to use for the specified course (with section).
     *
     * @param int|stdClass $section Section object from database or just field course_sections.section
     *     if omitted the course view page is returned
     * @param array $options options for view URL. At the moment core uses:
     *     'navigation' (bool) if true and section has no separate page, the function returns null
     *     'sr' (int) used by multipage formats to specify to which section to return
     * @return null|moodle_url
     */
    public function get_view_url($section, $options = []) {
        global $CFG;
        $course = $this->get_course();
        $url = new moodle_url('/course/view.php', ['id' => $course->id]);

        $sr = null;
        if (array_key_exists('sr', $options)) {
            $sr = $options['sr'];
        }
        if (is_object($section)) {
            $sectionno = $section->section;
        } else {
            $sectionno = $section;
        }
        if ($sectionno !== null) {
            if ($sr !== null) {
                if ($sr) {
                    $usercoursedisplay = COURSE_DISPLAY_MULTIPAGE;
                    $sectionno = $sr;
                } else {
                    $usercoursedisplay = COURSE_DISPLAY_SINGLEPAGE;
                }
            } else {
                $usercoursedisplay = $course->coursedisplay;
            }
            $url->param('section', $sectionno);
        }
        return $url;
    }

    /**
     * Returns the information about the ajax support in the given source format.
     *
     * The returned object's property (bool)capable indicates that
     * the course format supports Moodle course ajax features.
     *
     * @return stdClass
     */
    public function supports_ajax() {
        $ajaxsupport = new stdClass();
        $ajaxsupport->capable = true;
        return $ajaxsupport;
    }

    /**
     * Returns true if this course format is compatible with content components.
     *
     * Using components means the content elements can watch the frontend course state and
     * react to the changes. Formats with component compatibility can have more interactions
     * without refreshing the page, like having drag and drop from the course index to reorder
     * sections and activities.
     *
     * @return bool if the format is compatible with components.
     */
    public function supports_components() {
        return true;
    }

    /**
     * Loads all of the course sections into the navigation.
     *
     * @param global_navigation $navigation
     * @param navigation_node $node The course node within the navigation
     * @return void
     */
    public function extend_course_navigation($navigation, navigation_node $node) {
        global $PAGE;
        // If section is specified in course/view.php, make sure it is expanded in navigation.
        if ($navigation->includesectionnum === false) {
            $selectedsection = $this->get_sectionnum();
            if ($selectedsection !== null && (!defined('AJAX_SCRIPT') || AJAX_SCRIPT == '0') &&
                    $PAGE->url->compare(new moodle_url('/course/view.php'), URL_MATCH_BASE)) {
                $navigation->includesectionnum = $selectedsection;
            }
        }

        // Check if there are callbacks to extend course navigation.
        parent::extend_course_navigation($navigation, $node);

        // We want to remove the general section if it is empty.
        $modinfo = get_fast_modinfo($this->get_course());
        $sections = $modinfo->get_sections();
        if (!isset($sections[0])) {
            // The general section is empty to find the navigation node for it we need to get its ID.
            $section = $modinfo->get_section_info(0);
            $generalsection = $node->get($section->id, navigation_node::TYPE_SECTION);
            if ($generalsection) {
                // We found the node - now remove it.
                $generalsection->remove();
            }
        }
    }

    /**
     * Custom action after section has been moved in AJAX mode.
     *
     * Used in course/rest.php
     *
     * @return array This will be passed in ajax respose
     */
    public function ajax_section_move() {
        global $PAGE;
        $titles = [];
        $course = $this->get_course();
        $modinfo = get_fast_modinfo($course);
        $renderer = $this->get_renderer($PAGE);
        if ($renderer && ($sections = $modinfo->get_section_info_all())) {
            foreach ($sections as $number => $section) {
                $titles[$number] = $renderer->section_title($section, $course);
            }
        }
        return ['sectiontitles' => $titles, 'action' => 'move'];
    }

    /**
     * Returns the list of blocks to be automatically added for the newly created course.
     *
     * @return array of default blocks, must contain two keys BLOCK_POS_LEFT and BLOCK_POS_RIGHT
     *     each of values is an array of block names (for left and right side columns)
     */
    public function get_default_blocks() {
        return [
            BLOCK_POS_LEFT => [],
            BLOCK_POS_RIGHT => [],
        ];
    }

    /**
     * Definitions of the additional options that this course format uses for course.
     *
     * Topics format uses the following options:
     * - coursedisplay
     * - numsections
     * - hiddensections
     *
     * @param bool $foreditform
     * @return array of options
     */
    public function course_format_options($foreditform = false) {
        static $courseformatoptions = false;
        if ($courseformatoptions === false) {
            $courseconfig = get_config('moodlecourse');
            $courseformatoptions = [
                'numsections' => [
                    'default' => $courseconfig->numsections,
                    'type' => PARAM_INT,
                ],
                'hiddensections' => [
                    'default' => $courseconfig->hiddensections,
                    'type' => PARAM_INT,
                ],
                'coursedisplay' => [
                    'default' => $courseconfig->coursedisplay,
                    'type' => PARAM_INT,
                ],
            ];
        }
        if ($foreditform && !isset($courseformatoptions['coursedisplay']['label'])) {
            $courseconfig = get_config('moodlecourse');
            $max = $courseconfig->maxsections;
            if (!isset($max) || !is_numeric($max)) {
                $max = 52;
            }
            $sectionmenu = [];
            for ($i = 0; $i <= $max; $i++) {
                $sectionmenu[$i] = "$i";
            }
            $courseformatoptionsedit = [
                'numsections' => [
                    'label' => new lang_string('numberweeks'),
                    'element_type' => 'select',
                    'element_attributes' => [$sectionmenu],
                ],
                'hiddensections' => [
                    'label' => new lang_string('hiddensections'),
                    'help' => 'hiddensections',
                    'help_component' => 'moodle',
                    'element_type' => 'select',
                    'element_attributes' => [
                        [
                            0 => new lang_string('hiddensectionscollapsed'),
                            1 => new lang_string('hiddensectionsinvisible'),
                        ],
                    ],
                ],
                'coursedisplay' => [
                    'label' => new lang_string('coursedisplay', 'format_menutopic'),
                    'element_type' => 'select',
                    'element_attributes' => [
                        [
                            COURSE_DISPLAY_SINGLEPAGE => new lang_string('coursedisplay_single', 'format_menutopic'),
                            COURSE_DISPLAY_MULTIPAGE => new lang_string('coursedisplay_multi', 'format_menutopic'),
                        ],
                    ],
                    'help' => 'coursedisplay',
                    'help_component' => 'format_menutopic',
                ],
            ];
            $courseformatoptions = array_merge_recursive($courseformatoptions, $courseformatoptionsedit);
        }
        return $courseformatoptions;
    }

    /**
     * Adds format options elements to the course/section edit form.
     *
     * This function is called from {@see course_edit_form::definition_after_data()}.
     *
     * @param MoodleQuickForm $mform form the elements are added to.
     * @param bool $forsection 'true' if this is a section edit form, 'false' if this is course edit form.
     * @return array array of references to the added form elements.
     */
    public function create_edit_form_elements(&$mform, $forsection = false) {
        $elements = parent::create_edit_form_elements($mform, $forsection);

        // Increase the number of sections combo box values if the user has increased the number of sections
        // using the icon on the course page beyond course 'maxsections' or course 'maxsections' has been
        // reduced below the number of sections already set for the course on the site administration course
        // defaults page.  This is so that the number of sections is not reduced leaving unintended orphaned
        // activities / resources.
        if (!$forsection) {
            $maxsections = get_config('moodlecourse', 'maxsections');
            $numsections = $mform->getElementValue('numsections');
            $numsections = $numsections[0];
            if ($numsections > $maxsections) {
                $element = $mform->getElement('numsections');
                for ($i = $maxsections + 1; $i <= $numsections; $i++) {
                    $element->addOption("$i", $i);
                }
            }
        }
        return $elements;
    }

    /**
     * Updates format options for a course.
     *
     * In case if course format was changed to 'menutopic', we try to copy special options from the previous format.
     * If previous course format did not have the options, we populate it with the
     * current number of sections and default options.
     *
     * @param stdClass|array $data return value from {@see moodleform::get_data()} or array with data
     * @param stdClass $oldcourse if this function is called from {@see update_course()}
     *     this object contains information about the course before update
     * @return bool whether there were any changes to the options values
     */
    public function update_course_format_options($data, $oldcourse = null) {
        global $DB;

        if ($oldcourse !== null) {
            $data = (array)$data;
            $oldcourse = (array)$oldcourse;
            $options = $this->course_format_options();
            foreach ($options as $key => $unused) {
                if (!array_key_exists($key, $data)) {
                    if (array_key_exists($key, $oldcourse)) {
                        $data[$key] = $oldcourse[$key];
                    } else if ($key === 'numsections') {
                        // If previous format does not have the field 'numsections' and $data['numsections'] is not set,
                        // we fill it with the maximum section number from the DB.
                        $maxsection = $DB->get_field_sql('SELECT max(section) from {course_sections} WHERE course = ?',
                                        [$this->courseid]);
                        if ($maxsection) {
                            // If there are no sections, or just default 0-section, 'numsections' will be set to default.
                            $data['numsections'] = $maxsection;
                        }
                    }
                }
            }
        }
        return $this->update_format_options($data);
    }

    /**
     * Whether this format allows to delete sections
     *
     * Do not call this function directly, instead use {@see course_can_delete_section()}
     *
     * @param int|stdClass|section_info $section
     * @return bool
     */
    public function can_delete_section($section) {
        return true;
    }

    /**
     * Course-specific information to be output immediately above content on any course page
     *
     * See {@see core_courseformat\base::course_header()} for usage
     *
     * @return null|renderable null for no output or object with data for plugin renderer
     */
    public function course_content_header() {

        self::$editmenumode = optional_param('editmenumode', false, PARAM_BOOL);

        if (self::$editmenumode) {
            return null;
        }

        return new \format_menutopic\header($this);
    }

    /**
     * Indicates whether the course format supports the creation of a news forum.
     *
     * @return bool
     */
    public function supports_news() {
        return true;
    }

    /**
     * Returns whether this course format allows the activity to
     * have "triple visibility state" - visible always, hidden on course page but available, hidden.
     *
     * @param stdClass|cm_info $cm course module (may be null if we are displaying a form for adding a module)
     * @param stdClass|section_info $section section where this module is located or will be added to
     * @return bool
     */
    public function allow_stealth_module_visibility($cm, $section) {
        // Allow the third visibility state inside visible sections or in section 0.
        return !$section->section || $section->visible;
    }

    /**
     * Callback used in WS core_course_edit_section when teacher performs an AJAX action on a section (show/hide).
     *
     * Access to the course is already validated in the WS but the callback has to make sure
     * that particular action is allowed by checking capabilities
     *
     * Course formats should register.
     *
     * @param section_info|stdClass $section
     * @param string $action
     * @param int $sr
     * @return null|array any data for the Javascript post-processor (must be json-encodeable)
     */
    public function section_action($section, $action, $sr) {
        global $PAGE;

        if ($section->section && ($action === 'setmarker' || $action === 'removemarker')) {
            // Format 'topics' allows to set and remove markers in addition to common section actions.
            $coursecontext = $this->get_context();
            require_capability('moodle/course:setcurrentsection', $coursecontext);
            course_set_marker($this->courseid, ($action === 'setmarker') ? $section->section : 0);
            return null;
        }

        // For show/hide actions call the parent method and return the new content for .section_availability element.
        $rv = parent::section_action($section, $action, $sr);
        $renderer = $PAGE->get_renderer('format_topics');

        if (!($section instanceof section_info)) {
            $modinfo = course_modinfo::instance($this->courseid);
            $section = $modinfo->get_section_info($section->section);
        }
        $elementclass = $this->get_output_classname('content\\section\\availability');
        $availability = new $elementclass($this, $section);

        $rv['section_availability'] = $renderer->render($availability);
        return $rv;
    }

    /**
     * Return the plugin configs for external functions.
     *
     * @return array the list of configuration settings
     * @since Moodle 3.5
     */
    public function get_config_for_external() {
        // Return everything (nothing to hide).
        return $this->get_format_options();
    }

    /**
     * Load configuration data.
     *
     * @return \stdClass Configuration data from current course format.
     */
    public function load_formatdata() {
        global $COURSE, $DB, $OUTPUT, $PAGE;

        // If the formatdata is in memory, return it.
        if (self::$formatdata) {
            return self::$formatdata;
        }

        if (!($formatdata = $DB->get_record('format_menutopic', ['course' => $COURSE->id]))) {
            $formatdata = new \stdClass();
            $formatdata->course = $COURSE->id;

            if (!($formatdata->id = $DB->insert_record('format_menutopic', $formatdata))) {
                debugging('Not is possible save the course format data in menutopic format', DEBUG_DEVELOPER);
            }
        }

        if (!is_object($formatdata)) {
            $formatdata = new \stdClass();
        }

        $formatdata->menu = new \format_menutopic\menu($this->get_sectionnum());
        $formatdata->menu->level = 0;

        $modinfo = get_fast_modinfo($COURSE);
        $course = course_get_format($COURSE)->get_course();
        $course->realcoursedisplay = $course->coursedisplay;
        $course->coursedisplay = COURSE_DISPLAY_MULTIPAGE;
        $formatdata->sections = $modinfo->get_section_info_all();
        $formatdata->modinfo = $modinfo;

        if (!empty($formatdata->tree)) {
            $tree = json_decode(stripslashes($formatdata->tree));

            if (is_object($tree) && property_exists($tree, 'topics') && is_array($tree->topics)) {
                foreach ($tree->topics as $topic) {
                    $item = new \format_menutopic\menuitem($topic->url, $topic->name);
                    $item->target = $topic->target;

                    if (empty($topic->url)) {
                        $item->topicnumber = $topic->topicnumber;
                    }

                    if (isset($topic->subtopics) && is_array($topic->subtopics)) {
                        $item->loadsubtopics($topic->subtopics, 1);
                    }

                    $formatdata->menu->add($item);
                }
            }

            $formatdata->autobuildtree = false;
        } else {
            $formatdata->autobuildtree = true;
        }

        // Make sure all sections are created.
        if (count($formatdata->sections) <= $course->numsections) {
            course_create_sections_if_missing($course, range(0, $course->numsections));
            $modinfo = get_fast_modinfo($COURSE);
            $course = course_get_format($COURSE)->get_course();
            $course->realcoursedisplay = $course->coursedisplay;
            $course->coursedisplay = COURSE_DISPLAY_MULTIPAGE;
            $formatdata->sections = $modinfo->get_section_info_all();
            $formatdata->modinfo = $modinfo;
        }

        // Load Menu configuration.
        $configmenu = new \stdClass();
        $configmenu->cssdefault = true;
        $configmenu->menuposition = 'middle';
        $configmenu->linkinparent = false;
        $configmenu->templatetopic = false;
        $configmenu->icons_templatetopic = false;
        $configmenu->displaynousedmod = false;
        $configmenu->displaynavigation = 'nothing';
        $configmenu->nodesnavigation = '';

        if (property_exists($formatdata, 'config') && !empty($formatdata->config)) {
            $configsaved = @unserialize($formatdata->config);

            if (!is_object($configsaved)) {
                $configsaved = new \stdClass();
            }

            if (isset($configsaved->cssdefault)) {
                $configmenu->cssdefault = $configsaved->cssdefault;
            }

            if (isset($configsaved->menuposition)) {
                $configmenu->menuposition = $configsaved->menuposition;
            }

            if (isset($configsaved->linkinparent)) {
                $configmenu->linkinparent = $configsaved->linkinparent;
            }

            if (isset($configsaved->templatetopic)) {
                $configmenu->templatetopic = $configsaved->templatetopic;
            }

            if (isset($configsaved->icons_templatetopic)) {
                $configmenu->icons_templatetopic = $configsaved->icons_templatetopic;
            }

            if (isset($configsaved->displaynousedmod)) {
                $configmenu->displaynousedmod = $configsaved->displaynousedmod;
            }

            if (isset($configsaved->displaynavigation)) {
                $configmenu->displaynavigation = $configsaved->displaynavigation;
            }

            if (isset($configsaved->nodesnavigation)) {
                $configmenu->nodesnavigation = $configsaved->nodesnavigation;
            }
        }

        $formatdata->configmenu = $configmenu;

        $section = 0;

        while ($section <= $course->numsections) {
            if ($course->realcoursedisplay == COURSE_DISPLAY_MULTIPAGE && $section == 0) {
                $section++;
                continue;
            }

            if (count($formatdata->sections) <= $section) {
                $section++;
                continue;
            }

            $thissection = $formatdata->sections[$section];

            $showsection = true;
            if (!$thissection->visible || !$thissection->available) {
                $showsection = false;
            } else if ($section == 0 && !($thissection->summary || $thissection->sequence || $PAGE->user_is_editing())) {
                $showsection = false;
            }

            $coursecontext = $this->get_context();
            $canviewhidden = has_capability('moodle/course:viewhiddensections', $coursecontext)
                                            || !$course->hiddensections;

            if ($showsection || $canviewhidden || !$course->hiddensections) {

                // Check if display available message is required.
                $sectiontpl = new content_base\section($this, $thissection);
                $availabilityclass = $this->get_output_classname('content\\section\\availability');
                $availability = new $availabilityclass($this, $thissection);
                $availabledata = $availability->export_for_template($OUTPUT);
                $item = null;

                if ($formatdata->autobuildtree) {
                    $item = new \format_menutopic\menuitem('', $this->get_section_name($thissection));
                    $item->disabled = !$showsection;

                    if ($showsection || $canviewhidden) {
                        $item->topicnumber = $section;
                    } else {
                        $item->topicnumber = null;
                    }

                    $formatdata->menu->add($item);
                } else if (!$showsection) {
                    $formatdata->menu->remove_topic($section, (!$course->hiddensections || $canviewhidden), !$canviewhidden);
                }

                if (!$item) {
                    $item = $formatdata->menu->get_topics($section);
                }

                if ($item && $availabledata->hasavailability) {
                    if (!is_array($item)) {
                        $item = [$item];
                    }

                    foreach ($item as $subitem) {
                        $subitem->availablemessage = $OUTPUT->render($availability);
                    }
                }

            } else {
                if (!$formatdata->autobuildtree) {
                    $formatdata->menu->remove_topic($section, false, true);
                }
            }

            $section++;
        }

        self::$formatdata = $formatdata;

        return $formatdata;
    }

}
