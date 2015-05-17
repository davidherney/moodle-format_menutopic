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
 * @since 2.3
 * @package contribution
 * @copyright 2012 David Herney Bernal - cirano
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class format_menutopic_menu {

    public $tree;
    private $_config;
    public $current = 0;
    public $displaysection;

    // Object construct
    public function __construct($config = null) {
        if (!empty($config) && is_object($config)) {
            $this->_config    = $config;
        }
        else {
            $this->_config = new object();
            $this->_config->cssdefault = true;
            $this->_config->usehtml = false;
            $this->_config->menuposition = 'middle';
            $this->_config->linkinparent = false;
        }
    }

    public function list_code_horizontal_menu ($with_styles) {

        if (empty($this->tree)){
            return '';
        }

        $content = '';

        if (isset($this->tree->topics) && is_array($this->tree->topics)){
            $properties = array('id'=>'format_menutopic_menu');

            $properties['class'] = $this->_config->menuposition;

            if ($with_styles) {
                $properties['class'] .= ' format-menutopic-menu';
            }

            $content = html_writer::start_tag('div', $properties);
            $content .= html_writer::start_tag('ul', array('class'=>'menu-body-content menu-level-0'));
            // Render each child
            foreach ($this->tree->topics as $item) {
                $content .= $this->list_item_menu($item, 0);
            }
            // Close the open tags
            $content .= html_writer::end_tag('ul');
            $content .= html_writer::tag('div', '', array('class' => 'clearfix'));
            $content .= html_writer::end_tag('div');

            if ($this->_config->menuposition == 'left') {
                $content .= "<script>Y.on('contentready', function () { Y.Node.one('#block-region-side-pre').prepend(Y.Node.one('#format_menutopic_menu')); }, '#block-region-side-pre'); Y.one('body.empty-region-side-pre').removeClass('empty-region-side-pre');</script>";
            }
            else if ($this->_config->menuposition == 'right') {
                $content .= "<script>Y.on('contentready', function () { Y.Node.one('#block-region-side-post').prepend(Y.Node.one('#format_menutopic_menu')); }, '#block-region-side-post'); Y.one('body.empty-region-side-post').removeClass('empty-region-side-post');</script>";
            }

        }

        // Return the menu
        return $content;
    }

    /**
     * Renders a menu node as part of a submenu
     *
     */
    private function list_item_menu ($menunode, $level) {

        if (isset($menunode->visible) && !$menunode->visible) {
            return '';
        }

        $topic_number = -1;
        if (!empty($menunode->topicnumber) || $menunode->topicnumber === '0' || $menunode->topicnumber === 0) {
            $topic_number = (int)$menunode->topicnumber;
        }

        if (empty($menunode->url)) {
            if (!empty($menunode->topicnumber) || $menunode->topicnumber === "0" || $menunode->topicnumber === 0) {
                global $course, $CFG;
                $url = new moodle_url($CFG->wwwroot.'/course/view.php', array('id'=>$course->id, 'section'=>$menunode->topicnumber));
            }
            else {
                $url = 'javascript:;';
            }
        }
        else {
            $url = $menunode->url;
        }

        if (isset($menunode->subtopics) && is_array($menunode->subtopics) && count($menunode->subtopics) > 0) {

            if (!$this->_config->linkinparent) {
                $url = 'javascript:;';
            }

            $li_properties = array('class'=>"menuitem menu-withsubitems");

            if ($this->displaysection == $topic_number) {
                $li_properties['class'] .= ' current';
            }

            if (isset($menunode->hidden) && $menunode->hidden) {
                $li_properties['class'] .= ' disabled';
            }

            $content = html_writer::start_tag('li', $li_properties);

            $link_properties = array('class'=>'menu-label');

            if (!empty($menunode->target)) {
                $link_properties['target'] = $menunode->target;
            }

            $content .= html_writer::link($url, $menunode->name, $link_properties);
            $content .= html_writer::start_tag('ul', array('class'=>'submenu-body-content menu-level-' . ($level + 1)));
            foreach ($menunode->subtopics as $node) {
                $content .= $this->list_item_menu($node, $level + 1);
            }
            $content .= html_writer::end_tag('ul');
            $content .= html_writer::end_tag('li');
        }
        else {

            $link_properties = array('class'=>'menuitem-content');
            $li_properties = array('class'=>'menuitem menu-level-' . $level);

            if (!empty($menunode->target)) {
                $link_properties['target'] = $menunode->target;
            }

            if ($this->displaysection == $topic_number) {
                $li_properties['class'] .= ' current';
            }

            if (isset($menunode->hidden) && $menunode->hidden) {
                $li_properties['class'] .= ' disabled';
            }

            // The node doesn't have children so produce a final menuitem
            $content = html_writer::start_tag('li', $li_properties);
            $content .= html_writer::link($url, $menunode->name, $link_properties);
            $content .= html_writer::end_tag('li');
        }
        // Return the sub menu
        return $content;

    }


    public function script_menu($config, $displaysection, $with_styles = true) {
        $this->_config = $config;
        $this->displaysection = $displaysection;

        $print_for_menu = $this->list_code_horizontal_menu ($with_styles);

        return $print_for_menu;
    }
}
