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
 * Course menu control file.
 *
 * @since 2.3
 * @package format_menutopic
 * @copyright 2012 David Herney Bernal - cirano
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class to build the menu
 *
 * @package format_menutopic
 * @copyright 2012 David Herney Bernal - cirano
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_menutopic_menu {

    /**
     * Menu tree info.
     *
     * @var object|null
     */
    public $tree;

    /**
     * Format configuration.
     *
     * @var object
     */
    private $_config;

    /**
     * Current selected menu node.
     *
     * @var int
     */
    public $current = 0;

    /**
     * Section selected to display.
     *
     * @var int
     */
    public $displaysection;

    /**
     * Object construct.
     *
     * @param object $config Format configuration.
     */
    public function __construct($config = null) {
        if (!empty($config) && is_object($config)) {
            $this->_config = $config;
        } else {
            $this->_config = new stdClass();
            $this->_config->cssdefault = true;
            $this->_config->usehtml = false;
            $this->_config->menuposition = 'middle';
            $this->_config->linkinparent = false;
        }
    }

    /**
     * Build HTML code to horizontal menu.
     *
     * @param bool $withstyles True if include the basic styles.
     * @return string Menu HTML.
     */
    public function list_code_horizontal_menu($withstyles) {

        global $PAGE;

        if (empty($this->tree)) {
            return '';
        }

        $content = '';

        if (isset($this->tree->topics) && is_array($this->tree->topics)) {
            $properties = array('id' => 'format_menutopic_menu');

            $properties['class'] = $this->_config->menuposition;

            if ($withstyles) {
                $properties['class'] .= ' format-menutopic-menu';
            }

            $content = html_writer::start_tag('div', $properties);
            $content .= html_writer::start_tag('ul', array('class' => 'menu-body-content menu-level-0'));
            // Render each child.
            foreach ($this->tree->topics as $item) {
                $content .= $this->list_item_menu($item, 0);
            }
            // Close the open tags.
            $content .= html_writer::end_tag('ul');
            $content .= html_writer::tag('div', '', array('class' => 'clearfix'));
            $content .= html_writer::end_tag('div');

            if ($this->_config->menuposition == 'left') {
                $PAGE->requires->js('/course/format/menutopic/format.js');
                $PAGE->requires->js_init_call('M.course.format.moveMenuLeft', null, true);
            } else if ($this->_config->menuposition == 'right') {
                $PAGE->requires->js('/course/format/menutopic/format.js');
                $PAGE->requires->js_init_call('M.course.format.moveMenuRight', null, true);
            }

        }

        // Return the menu.
        return $content;
    }

    /**
     * Renders a menu node as part of a submenu.
     *
     * @param object $menunode Each menu node.
     * @param int $level Node level into the menu.
     * @return string Item HTML.
     */
    private function list_item_menu($menunode, $level) {

        if (isset($menunode->visible) && !$menunode->visible) {
            return '';
        }

        $topicnumber = -1;
        if (!empty($menunode->topicnumber) || $menunode->topicnumber === '0' || $menunode->topicnumber === 0) {
            $topicnumber = (int)$menunode->topicnumber;
        }

        if (empty($menunode->url)) {
            if (!empty($menunode->topicnumber) || $menunode->topicnumber === "0" || $menunode->topicnumber === 0) {
                global $COURSE, $CFG;

                if (isset($menunode->hidden) && $menunode->hidden
                        && !has_capability('moodle/course:viewhiddensections', context_course::instance($COURSE->id))) {
                    $url = 'javascript:;';
                } else {
                    $url = new moodle_url($CFG->wwwroot.'/course/view.php',
                            array('id' => $COURSE->id, 'section' => $menunode->topicnumber));
                }
            } else {
                $url = 'javascript:;';
            }
        } else {
            $url = $menunode->url;
        }

        if (isset($menunode->subtopics) && is_array($menunode->subtopics) && count($menunode->subtopics) > 0) {

            if (!$this->_config->linkinparent) {
                $url = 'javascript:;';
            }

            $liproperties = array('class' => "menuitem menu-withsubitems");

            if ($this->displaysection == $topicnumber) {
                $liproperties['class'] .= ' current';
            }

            if (isset($menunode->hidden) && $menunode->hidden) {
                $liproperties['class'] .= ' disabled';
            }

            $content = html_writer::start_tag('li', $liproperties);

            $linkproperties = array('class' => 'menu-label');

            if (!empty($menunode->target)) {
                $linkproperties['target'] = $menunode->target;
            }

            $content .= html_writer::link($url, $menunode->name, $linkproperties);
            $content .= html_writer::start_tag('ul', array('class' => 'submenu-body-content menu-level-' . ($level + 1)));
            foreach ($menunode->subtopics as $node) {
                $content .= $this->list_item_menu($node, $level + 1);
            }
            $content .= html_writer::end_tag('ul');
            $content .= html_writer::end_tag('li');
        } else {

            $linkproperties = array('class' => 'menuitem-content');
            $liproperties = array('class' => 'menuitem menu-level-' . $level);

            if (!empty($menunode->target)) {
                $linkproperties['target'] = $menunode->target;
            }

            if ($this->displaysection == $topicnumber) {
                $liproperties['class'] .= ' current';
            }

            if (isset($menunode->hidden) && $menunode->hidden) {
                $liproperties['class'] .= ' disabled';
            }

            // The node doesn't have children so produce a final menuitem.
            $content = html_writer::start_tag('li', $liproperties);
            $content .= html_writer::link($url, $menunode->name, $linkproperties);
            $content .= html_writer::end_tag('li');
        }

        // Return the sub menu.
        return $content;
    }


    /**
     * Renderer to build the menu HTML.
     *
     * @param object $config Format configuration.
     * @param int $displaysection Section selected to display.
     * @param bool $withstyles True if include the basic styles.
     * @return string Menu HTML.
     */
    public function script_menu($config, $displaysection, $withstyles = true) {
        $this->_config = $config;
        $this->displaysection = $displaysection;

        return $this->list_code_horizontal_menu ($withstyles);
    }
}
