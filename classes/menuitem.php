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
 * Class containing a single tab.
 *
 * @package   format_menutopic
 * @copyright 2021 David Herney - https://bambuco.co
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace format_menutopic;

/**
 * Class containing the menu item information.
 *
 * @copyright 2021 David Herney - https://bambuco.co
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class menuitem {

    /**
     * @var int Section index.
     */
    public $topicnumber = null;

    /**
     * @var string Menu item link.
     */
    public $url;

    /**
     * @var string Target attribute.
     */
    public $target = '';

    /**
     * @var string Menu item title.
     */
    public $title;

    /**
     * @var string Menu item label.
     */
    public $name;

    /**
     * @var string Available message, in html format, if exist.
     */
    public $availablemessage = '';

    /**
     * @var string Custom CSS styles.
     */
    public $customstyles = '';

    /**
     * @var string Custom extra CSS classes.
     */
    public $specialclass = '';

    /**
     * @var bool If menu item is selected.
     */
    public $current = false;

    /**
     * @var bool If menu item is disabled.
     */
    public $disabled = false;

    /**
     * @var bool If menu item is visible or hidden.
     */
    public $hidden = false;

    /**
     * @var \format_menutopic\menu Menu childs list.
     */
    private $submenu;

    /**
     * Constructor.
     *
     * @param string $url Menu item link.
     * @param string $title Menu item title.
     */
    public function __construct(string $url, string $title) {

        $this->url = $url;
        $this->title = $title;
        $this->name = $title;

        $this->submenu = new \format_menutopic\menu();
    }

    /**
     * Load submenu from configured subtopics in tree.
     *
     * @param array $subtopics Subtopics list.
     * @param int $level Submenu level.
     * @return void
     */
    public function loadsubtopics(array $subtopics, int $level = 0) : void {

        $this->submenu->level = $level;

        foreach ($subtopics as $topic) {
            $item = new \format_menutopic\menuitem($topic->url, $topic->name);
            $item->target = $topic->target;

            if (empty($topic->url)) {
                $item->topicnumber = $topic->topicnumber;
            }

            if (isset($topic->subtopics) && is_array($topic->subtopics)) {
                $item->loadsubtopics($topic->subtopics, $level + 1);
            }

            $this->submenu->add($item);
        }
    }

    /**
     * Check if current menu item has submenu items.
     *
     * @return bool true if has submenu items, false in other case.
     */
    public function has_childs() {
        return $this->submenu->has_items();
    }

    /**
     * To get the submenu items list.
     *
     * @return \format_menutopic\tabs The submenu list object.
     */
    public function get_submenu() {
        return $this->submenu;
    }

    /**
     * Remove all items from submenu.
     *
     * @return void
     */
    public function clean_submenu() : void {
        $this->submenu = new \format_menutopic\menu();
    }

}
