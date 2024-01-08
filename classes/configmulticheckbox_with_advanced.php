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
 * Checking access to quiz by list of IP adresses defined by admin.
 *
 * @package    quizaccess_ipaddresslist
 * @copyright  2016 Vadim Dvorovenko <Vadimon@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Select multiple items from list with an advanced checkbox, that controls a additional $name.'_adv' setting.
 *
 * @package    quizaccess_ipaddresslist
 * @copyright  2016 Vadim Dvorovenko <Vadimon@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quizaccess_ipaddresslist_configmulticheckbox_with_advanced extends admin_setting_configmulticheckbox {

    /**
     * Constructor
     * @param string $name unique ascii name
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param array $defaultsetting array of selected items
     * @param array $choices array of $value=>$label for each list item
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $choices) {
        parent::__construct($name, $visiblename, $description, $defaultsetting['value'], $choices);
        $this->set_advanced_flag_options(admin_setting_flag::ENABLED, !empty($defaultsetting['adv']));
    }

    /**
     * Returns the select settings.
     * Does not return null as parent to skip displaying this setting on plugin install.
     *
     * @return array. Array of settings
     */
    public function get_setting() {
        $result = $this->config_read($this->name);

        if (is_null($result)) {
            return [];
        }
        if ($result === '') {
            return [];
        }
        $enabled = explode(',', $result);
        $setting = [];
        foreach ($enabled as $option) {
            $setting[$option] = 1;
        }
        return $setting;
    }

}
