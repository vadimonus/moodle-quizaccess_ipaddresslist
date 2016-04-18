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
        //if (empty($quizobj->get_quiz()->subnet)) {
        //    return null;
        //}
return null;
        //return new self($quizobj, $timenow);
    }

    /**
     * Whether or not a user should be allowed to start a new attempt at this quiz now.
     * @param int $numattempts the number of previous attempts this user has made.
     * @param object $lastattempt information about the user's last completed attempt.
     * @return string false if access should be allowed, a message explaining the
     *      reason if access should be prevented.
     */
    public function prevent_access() {
        if (address_in_subnet(getremoteaddr(), $this->quiz->subnet)) {
            return false;
        } else {
            return get_string('subnetwrong', 'quizaccess_ipaddress');
        }
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
        // By default do nothing.
    }

    /**
     * Validate the data from any form fields added using {@link add_settings_form_fields()}.
     * @param array $errors the errors found so far.
     * @param array $data the submitted form data.
     * @param array $files information about any uploaded files.
     * @param mod_quiz_mod_form $quizform the quiz form object.
     * @return array $errors the updated $errors array.
     */
    public static function validate_settings_form_fields(array $errors,
            array $data, $files, mod_quiz_mod_form $quizform) {

        return $errors;
    }

    /**
     * Save any submitted settings when the quiz settings form is submitted. This
     * is called from {@link quiz_after_add_or_update()} in lib.php.
     * @param object $quiz the data from the quiz form, including $quiz->id
     *      which is the id of the quiz being saved.
     */
    public static function save_settings($quiz) {
        // By default do nothing.
    }

    /**
     * Delete any rule-specific settings when the quiz is deleted. This is called
     * from {@link quiz_delete_instance()} in lib.php.
     * @param object $quiz the data from the database, including $quiz->id
     *      which is the id of the quiz being deleted.
     * @since Moodle 2.7.1, 2.6.4, 2.5.7
     */
    public static function delete_settings($quiz) {
        // By default do nothing.
    }

    /**
     * Return the bits of SQL needed to load all the settings from all the access
     * plugins in one DB query. The easiest way to understand what you need to do
     * here is probalby to read the code of {@link quiz_access_manager::load_settings()}.
     *
     * If you have some settings that cannot be loaded in this way, then you can
     * use the {@link get_extra_settings()} method instead, but that has
     * performance implications.
     *
     * @param int $quizid the id of the quiz we are loading settings for. This
     *     can also be accessed as quiz.id in the SQL. (quiz is a table alisas for {quiz}.)
     * @return array with three elements:
     *     1. fields: any fields to add to the select list. These should be alised
     *        if neccessary so that the field name starts the name of the plugin.
     *     2. joins: any joins (should probably be LEFT JOINS) with other tables that
     *        are needed.
     *     3. params: array of placeholder values that are needed by the SQL. You must
     *        used named placeholders, and the placeholder names should start with the
     *        plugin name, to avoid collisions.
     */
    public static function get_settings_sql($quizid) {
        return array('', '', array());
    }

    /**
     * You can use this method to load any extra settings your plugin has that
     * cannot be loaded efficiently with get_settings_sql().
     * @param int $quizid the quiz id.
     * @return array setting value name => value. The value names should all
     *      start with the name of your plugin to avoid collisions.
     */
    public static function get_extra_settings($quizid) {
        return array();
    }
/*
    public static function add_settings_form_fields(
        mod_quiz_mod_form $quizform, MoodleQuickForm $mform) {
        global $DB, $COURSE, $PAGE, $CFG;

        $lessontypes = $DB->get_records('block_supervised_lessontype', array('courseid' => $COURSE->id));

        // Radiobuttons (modes).
        $radioarray = array();
        $radioarray[] =& $mform->createElement('radio', 'supervisedmode', '',
            get_string('checknotrequired', 'quizaccess_supervisedcheck'), 0);
        if (count($lessontypes) > 0) {  // Render 3rd mode only if we have some lesson types in course.
            $radioarray[] =& $mform->createElement('radio', 'supervisedmode', '',
                get_string('checkforall', 'quizaccess_supervisedcheck'), 1);
            $radioarray[] =& $mform->createElement('radio', 'supervisedmode', '',
                get_string('customcheck', 'quizaccess_supervisedcheck'), 2);
        } else { // No lesson types, so just it's just yes/no.
            $radioarray[] =& $mform->createElement('radio', 'supervisedmode', '',
                get_string('checkrequired', 'quizaccess_supervisedcheck'), 1);
        }
        $mform->addGroup($radioarray, 'radioar',
            get_string('allowcontrol', 'quizaccess_supervisedcheck'), '<br/>', false);

        // Checkboxes with lessontypes for 3rd mode.
        if (count($lessontypes) > 0) {
            $cbarray = array();
            foreach ($lessontypes as $id => $lessontype) {
                $cbarray[] =& $mform->createElement('advcheckbox', 'supervisedlessontype_'.$id, '', $lessontype->name);
            }
            $mform->addGroup($cbarray, 'lessontypesgroup', '', '<br/>', false);
        }

        $PAGE->requires->jquery();
        $PAGE->requires->js( new moodle_url($CFG->wwwroot . '/mod/quiz/accessrule/supervisedcheck/lib.js') );
        $PAGE->requires->css( new moodle_url($CFG->wwwroot . '/mod/quiz/accessrule/supervisedcheck/style.css') );
    }

    public static function save_settings($quiz) {
        global $DB, $COURSE;
        $oldrules = $DB->get_records('quizaccess_supervisedcheck', array('quizid' => $quiz->id));

        if ($quiz->supervisedmode == 2) {
            // Find checked lessontypes.
            $lessontypesincourse = $DB->get_records('block_supervised_lessontype', array('courseid' => $COURSE->id));
            $lessontypesinquiz = array();

            // Checks for all lesson types.
            foreach ($lessontypesincourse as $id => $lessontype) {
                if ($quiz->{'supervisedlessontype_'.$id}) {
                    $lessontypesinquiz[] = $id;
                }
            }

            // Update rules.
            if (empty($lessontypesinquiz)) {
                // If user didn't check any lessontype - add special lessontype with id = -1.
                $lessontypesinquiz[] = -1;
            }

            for ($i = 0; $i < count($lessontypesinquiz); $i++) {
                // Update an existing rule if possible.
                $rule = array_shift($oldrules);
                if (!$rule) {
                    $rule                   = new stdClass();
                    $rule->quizid           = $quiz->id;
                    $rule->lessontypeid     = -1;
                    $rule->supervisedmode   = $quiz->supervisedmode; // ...must be 2.
                    $rule->id               = $DB->insert_record('quizaccess_supervisedcheck', $rule);
                }
                $rule->lessontypeid         = $lessontypesinquiz[$i];
                $rule->supervisedmode       = $quiz->supervisedmode; // ...must be 2.
                $DB->update_record('quizaccess_supervisedcheck', $rule);
            }
            $oldrulesids = array();
            // Delete any remaining old rules.
            if(!empty($oldrules)) {
                foreach ($oldrules as $oldrule) {
                    $oldrulesids[] = $oldrule->id;
                }
                list($insql, $inparams) = $DB->get_in_or_equal($oldrulesids);
                $sqlstring = " id ";
                $sqlstring .= $insql;
                $DB->delete_records_select('quizaccess_supervisedcheck', $sqlstring, $inparams);
            }
        } else {
            // Update an existing rule if possible.
            $rule = array_shift($oldrules);
            if (!$rule) {
                $rule                   = new stdClass();
                $rule->quizid           = $quiz->id;
                $rule->lessontypeid     = -1;
                $rule->supervisedmode   = $quiz->supervisedmode;   // ...0 or 1.
                $rule->id               = $DB->insert_record('quizaccess_supervisedcheck', $rule);
            }
            $rule->lessontypeid         = -1;
            $rule->supervisedmode       = $quiz->supervisedmode;   // ...0 or 1.
            $DB->update_record('quizaccess_supervisedcheck', $rule);
            $oldrulesids = array();
            // Delete any remaining old rules.
            if(!empty($oldrules)) {
                foreach ($oldrules as $oldrule) {
                    $oldrulesids[] = $oldrule->id;
                }
                list($insql, $inparams) = $DB->get_in_or_equal($oldrulesids);
                $sqlstring = " id ";
                $sqlstring .= $insql;
                $DB->delete_records_select('quizaccess_supervisedcheck', $sqlstring, $inparams);
            }
        }
    }
*/

}
