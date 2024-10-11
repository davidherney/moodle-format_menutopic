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
 * Class containing a basic menu structure.
 *
 * @package   format_menutopic
 * @copyright 2021 David Herney - https://bambuco.co
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace format_menutopic;

/**
 * Class containing the menu information.
 *
 * @copyright 2021 David Herney - https://bambuco.co
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class menu {

    /**
     * @var int Menu item level.
     */
    public $level = 0;

    /**
     * @var array Menu items.
     */
    private $menuitems;

    /**
     * To save the currect topic or URL.
     *
     * @var int $currentsection The current section number
     */
    public $currentsection;

    /**
     * Constructor.
     *
     * @param int|null $currentsection The current section number
     */
    public function __construct(?int $currentsection = null) {
        $this->menuitems = [];

        $this->currentsection = $currentsection;
    }

    /**
     * Check if exist menu items.
     *
     * @return bool True if has items, false in other case.
     */
    public function has_items() {
        return count($this->menuitems) > 0;
    }

    /**
     * Add a new item to the menu.
     *
     * @param \format_menutopic\menuitem $item The new instanced item.
     */
    public function add(menuitem $item) {
        $this->menuitems[] = $item;
        $item->get_submenu()->currentsection = $this->currentsection;
    }

    /**
     * Change visibility options for menu items printing.
     *
     * @param int $section The linked section
     * @param bool $onlyhide true if show item but disabled
     * @param bool $removetopic true if remove item from the menu
     */
    public function remove_topic($section, $onlyhide = false, $removetopic = true) {

        foreach ($this->menuitems as $key => $item) {

            if ($item->topicnumber == $section) {

                if ($onlyhide) {
                    $item->disabled = true;
                    $item->hidden = false;
                    $item->get_submenu()->remove_topic($section, $onlyhide, $removetopic);
                } else {

                    // Remove the current item from the menu.
                    unset($this->menuitems[$key]);
                    continue;
                }

                if ($removetopic) {
                    $item->topicnumber = null;
                    $item->clean_submenu();
                }

            } else {
                $item->get_submenu()->remove_topic($section, $onlyhide, $removetopic);
            }
        }

    }

    /**
     * Search menu items by section number.
     *
     * @param int $section The linked section
     * @return array of object
     */
    public function get_topics($section): ?array {

        $topics = [];
        foreach ($this->menuitems as $key => $item) {

            if ($item->topicnumber == $section) {
                $topics[] = $item;
            } else {
                $topics = array_merge($topics, $item->get_submenu()->get_topics($section));
            }
        }

        return $topics;
    }

    /**
     * To get the menu list.
     *
     * @param int $courseid The course id.
     * @return array of object.
     */
    public function get_list(int $courseid): array {

        $menutree = [];

        $anchortomenutree = get_config('format_menutopic', 'anchortomenutree');

        foreach ($this->menuitems as $menuitem) {

            if (!empty($menuitem->url)) {
                $url = $menuitem->url;
            } else {
                $url = (string)(new \moodle_url('/course/view.php', ['id' => $courseid, 'section' => $menuitem->topicnumber]));
                $url .= ($anchortomenutree ? '#menu-tree-start' : '');
            }

            $current = $menuitem->current;
            if (is_numeric($menuitem->topicnumber)
                    && is_numeric($this->currentsection)
                    && $menuitem->topicnumber == $this->currentsection) {
                $current = true;
            }

            $newitem = new \stdClass();
            $newitem->uniqueid = 'item-' . time() . '-' . rand(0, 1000);
            $newitem->specialclass = $menuitem->specialclass;
            $newitem->current = $current;
            $newitem->disabled = $menuitem->disabled;
            $newitem->url = $url;
            // Scape title in order to be used in html attribute.
            $newitem->title = str_replace('"', '&quot;', $menuitem->title);
            $newitem->text = $menuitem->name;
            $newitem->target = $menuitem->target;
            $newitem->availablemessage = $menuitem->availablemessage;
            $newitem->level = $this->level;
            $newitem->root = $this->level == 0;
            $newitem->haschilds = false;

            if ($menuitem->has_childs()) {
                $newitem->submenu = $menuitem->get_submenu()->get_list($courseid);
                $newitem->haschilds = true;

                foreach ($newitem->submenu as $submenuitem) {
                    if ($submenuitem->current) {
                        $newitem->current = true;
                        break;
                    }
                }
            }

            $menutree[] = $newitem;
        }

        return $menutree;
    }
}
