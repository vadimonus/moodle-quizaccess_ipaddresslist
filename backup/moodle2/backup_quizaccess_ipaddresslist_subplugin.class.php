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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/backup/moodle2/backup_mod_quiz_access_subplugin.class.php');

/**
 * Provides the information to backup the ipaddresslist quiz access plugin.
 *
 * If this plugin is required, a <quizaccess_ipaddresslist> tag
 * will be added to the XML in the appropriate place. Otherwise nothing will be
 * added.
 *
 * @package    quizaccess_ipaddresslist
 * @copyright  2016 Vadim Dvorovenko <Vadimon@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_quizaccess_ipaddresslist_subplugin extends backup_mod_quiz_access_subplugin {

    /**
     * Use this method to describe the XML structure required to store your
     * sub-plugin's settings for a particular quiz, and how that data is stored
     * in the database.
     */
    protected function define_quiz_subplugin_structure() {

        $subplugin = $this->get_subplugin_element();

        $subpluginwrapper = new backup_nested_element($this->get_recommended_name());
        $subplugin->add_child($subpluginwrapper);

        $subplugintablesettings = new backup_nested_element('quizaccess_ipaddresslist', null, ['subnetid']);
        $subpluginwrapper->add_child($subplugintablesettings);

        $subplugintablesettings->set_source_table('quizaccess_ipaddresslist',
                ['quizid' => backup::VAR_ACTIVITYID]);

        return $subplugin;
    }
}
