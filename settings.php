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

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree && $hassiteconfig) {
    $settings->add(new quizaccess_ipaddresslist_subnet_list_editor());

    $settings->add(new admin_setting_heading('quizaccess_ipaddresslist/heading',
            get_string('generalsettings', 'admin'), get_string('configintro', 'quiz')));

    $choices = $DB->get_records_menu('quizaccess_ipaddresslist_net', [], 'sortorder ASC, name ASC', 'id, name');
    $defaultsetting = ['value' => [], 'adv' => true];
    $settings->add(new quizaccess_ipaddresslist_configmulticheckbox_with_advanced('quizaccess_ipaddresslist/defaultallowedsubnets',
            get_string('allowedsubnets', 'quizaccess_ipaddresslist'), '', $defaultsetting, $choices));
    unset($choices);
    unset($defaultsetting);
}
