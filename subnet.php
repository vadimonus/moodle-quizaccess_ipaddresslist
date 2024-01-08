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

require_once("../../../../config.php");

require_login();
require_capability('moodle/site:config', context_system::instance());

$id = optional_param('id', 0, PARAM_INT);

$PAGE->set_pagelayout('admin');
$pageurl = new moodle_url('/admin/settings.php', ['section' => 'modsettingsquizcatipaddresslist']);
$PAGE->set_url($pageurl);
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('editsubnet', 'quizaccess_ipaddresslist'));
$PAGE->set_heading($COURSE->fullname);

$formurl = new moodle_url('/mod/quiz/accessrule/ipaddresslist/subnet.php');
$mform = new quizaccess_ipaddresslist_edit_form($formurl);
if ($id) {
    $subnet = $DB->get_record('quizaccess_ipaddresslist_net', ['id' => $id]);
    $mform->set_data($subnet);
}

if ($mform->is_cancelled()) {
    redirect($pageurl);
} else if ($data = $mform->get_data()) {
    require_sesskey();
    if ($id) {
        $subnet->name = $data->name;
        $subnet->subnet = $data->subnet;
        $DB->update_record('quizaccess_ipaddresslist_net', $subnet);
    } else {
        $subnet = new stdClass();
        $subnet->name = $data->name;
        $subnet->subnet = $data->subnet;
        $subnet->sortorder = $DB->get_field_sql('SELECT MAX(sortorder) + 1 FROM {quizaccess_ipaddresslist_net}');
        if (is_null($subnet->sortorder)) {
            $subnet->sortorder = 1;
        }
        $DB->insert_record('quizaccess_ipaddresslist_net', $subnet);
    }
    redirect($pageurl);
}
echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
