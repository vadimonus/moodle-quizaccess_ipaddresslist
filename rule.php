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

require_once($CFG->dirroot . '/mod/quiz/accessrule/accessrulebase.php');

/**
 * Rule class.
 *
 * @package    quizaccess_ipaddresslist
 * @copyright  2016 Vadim Dvorovenko <Vadimon@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quizaccess_ipaddresslist extends quiz_access_rule_base {

    /**
     * Return an appropriately configured instance of this rule, if it is applicable
     * to the given quiz, otherwise return null.
     * @param quiz $quizobj information about the quiz in question.
     * @param int $timenow the time that should be considered as 'now'.
     * @param bool $canignoretimelimits whether the current user is exempt from
     *      time limits by the mod/quiz:ignoretimelimits capability.
     * @return quiz_access_rule_base|null the rule, if applicable, else null.
     */
    public static function make(quiz $quizobj, $timenow, $canignoretimelimits) {
        if (!empty($quizobj->get_quiz()->ipaddresslistsubnetsarray)) {
            return new self($quizobj, $timenow);
        } else {
            return null;
        }
    }

    /**
     * Whether the user should be blocked from starting a new attempt or continuing
     * an attempt now.
     * @return string false if access should be allowed, a message explaining the
     *      reason if access should be prevented.
     */
    public function prevent_access() {
        global $DB;

        list($inorequal, $params) = $DB->get_in_or_equal($this->quiz->ipaddresslistsubnetsarray);
        $select = 'id ' . $inorequal;
        $subnets = $DB->get_records_select_menu('quizaccess_ipaddresslist_net', $select, $params, 'sortorder ASC, name ASC',
                'id, subnet');
        foreach ($subnets as $subnet) {
            if (address_in_subnet(getremoteaddr(), $subnet)) {
                return false;
            }
        }
        return get_string('subnetwrong', 'quizaccess_ipaddress');
    }

    /**
     * Add any fields that this rule requires to the quiz settings form. This
     * method is called from {@link mod_quiz_mod_form::definition()}, while the
     * security seciton is being built.
     * @param mod_quiz_mod_form $quizform the quiz settings form that is being built.
     * @param MoodleQuickForm $mform the wrapped MoodleQuickForm.
     */
    public static function add_settings_form_fields(
            mod_quiz_mod_form $quizform, MoodleQuickForm $mform) {
        global $DB;

        $pluginconfig = get_config('quizaccess_ipaddresslist');
        if (!isset($pluginconfig->defaultallowedsubnets_adv)) {
            $pluginconfig->defaultallowedsubnets_adv = true;
        }

        $subnets = $DB->get_records_menu('quizaccess_ipaddresslist_net', array(), 'sortorder ASC, name ASC', 'id, name');

        if (!empty($pluginconfig->defaultallowedsubnets)) {
            $defaultsubnets = explode(',', $pluginconfig->defaultallowedsubnets);
        } else {
            $defaultsubnets = array();
        }
        foreach($defaultsubnets as $subnetid) {
            $mform->setDefault("ipaddresslistsubnets[$subnetid]", 1);
        }
        $group = array();
        foreach($subnets as $subnetid => $subnetname) {
            $group[] = $mform->createElement('checkbox', "ipaddresslistsubnets[$subnetid]", '', $subnetname);
        }
        $mform->addGroup($group, 'ipaddresslistsubnets', get_string('allowedsubnets', 'quizaccess_ipaddresslist'), '<br />', false);
        $mform->setAdvanced("ipaddresslistsubnets", $pluginconfig->defaultallowedsubnets_adv);
        $mform->addHelpButton('ipaddresslistsubnets', 'allowedsubnets', 'quizaccess_ipaddresslist');
    }

    /**
     * Save any submitted settings when the quiz settings form is submitted. This
     * is called from {@link quiz_after_add_or_update()} in lib.php.
     * @param object $quiz the data from the quiz form, including $quiz->id
     *      which is the id of the quiz being saved.
     */
    public static function save_settings($quiz) {
        global $DB;

        $DB->delete_records('quizaccess_ipaddresslist', array('quizid' => $quiz->id));
        if (!empty($quiz->ipaddresslistsubnets)) {
            foreach ($quiz->ipaddresslistsubnets as $subnetid => $unused) {
                $ipaddresslistrecord = new stdClass();
                $ipaddresslistrecord->quizid = $quiz->id;
                $ipaddresslistrecord->subnetid = $subnetid;
                $DB->insert_record('quizaccess_ipaddresslist', $ipaddresslistrecord);
            }
        }
    }

    /**
     * Delete any rule-specific settings when the quiz is deleted. This is called
     * from {@link quiz_delete_instance()} in lib.php.
     * @param object $quiz the data from the database, including $quiz->id
     *      which is the id of the quiz being deleted.
     * @since Moodle 2.7.1, 2.6.4, 2.5.7
     */
    public static function delete_settings($quiz) {
        global $DB;

        $DB->delete_records('quizaccess_ipaddresslist', array('quizid' => $quiz->id));
    }

    /**
     * You can use this method to load any extra settings your plugin has that
     * cannot be loaded efficiently with get_settings_sql().
     * @param int $quizid the quiz id.
     * @return array setting value name => value. The value names should all
     *      start with the name of your plugin to avoid collisions.
     */
    public static function get_extra_settings($quizid) {
        global $DB;

        $subnets = array();
        $allsubnets = $DB->get_records('quizaccess_ipaddresslist_net');
        $usedsubnets = $DB->get_records_menu('quizaccess_ipaddresslist', array('quizid' => $quizid), '', 'id, subnetid');
        foreach ($allsubnets as $subnetid => $subnet) {
            if (in_array($subnetid, $usedsubnets)) {
                $subnets["ipaddresslistsubnets[$subnetid]"] = 1;
            } else {
                $subnets["ipaddresslistsubnets[$subnetid]"] = 0;
            }
        }
        $subnets['ipaddresslistsubnetsarray'] = $usedsubnets;
        return $subnets;
    }

}
