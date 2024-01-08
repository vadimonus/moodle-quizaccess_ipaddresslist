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

require_once('../../../../config.php');

require_login();
require_capability('moodle/site:config', context_system::instance());
require_sesskey();

$action = required_param('action', PARAM_ALPHANUMEXT);
$id = required_param('id', PARAM_INT);

switch ($action) {
    case 'delete':
        $DB->delete_records('quizaccess_ipaddresslist', ['subnetid' => $id]);
        $DB->delete_records('quizaccess_ipaddresslist_net', ['id' => $id]);
        quizaccess_ipaddresslist_reorder_subnets();
        break;

    case 'up':
        $subnets = $DB->get_records('quizaccess_ipaddresslist_net', [], 'sortorder ASC, name ASC');
        $previous = null;
        foreach ($subnets as $subnet) {
            if ($subnet->id == $id) {
                if ($previous) {
                    $temp = $subnet->sortorder;
                    $subnet->sortorder = $previous->sortorder;
                    $previous->sortorder = $temp;
                    $DB->update_record('quizaccess_ipaddresslist_net', $subnet);
                    $DB->update_record('quizaccess_ipaddresslist_net', $previous);
                    quizaccess_ipaddresslist_reorder_subnets();
                    break;
                }
            }
            $previous = $subnet;
        }
        break;

    case 'down':
        $subnets = $DB->get_records('quizaccess_ipaddresslist_net', [], 'sortorder DESC, name DESC');
        $previous = null;
        foreach ($subnets as $subnet) {
            if ($subnet->id == $id) {
                if ($previous) {
                    $temp = $subnet->sortorder;
                    $subnet->sortorder = $previous->sortorder;
                    $previous->sortorder = $temp;
                    $DB->update_record('quizaccess_ipaddresslist_net', $subnet);
                    $DB->update_record('quizaccess_ipaddresslist_net', $previous);
                    quizaccess_ipaddresslist_reorder_subnets();
                    break;
                }
            }
            $previous = $subnet;
        }
        break;
}

redirect(new moodle_url('/admin/settings.php', ['section' => 'modsettingsquizcatipaddresslist']));

/**
 * Fixes sortorder.
 */
function quizaccess_ipaddresslist_reorder_subnets() {
    global $DB;
    $subnets = $DB->get_records('quizaccess_ipaddresslist_net', [], 'sortorder ASC, name ASC');
    $current = 1;
    foreach ($subnets as $subnet) {
        if ($subnet->sortorder != $current) {
            $subnet->sortorder = $current;
            $DB->update_record('quizaccess_ipaddresslist_net', $subnet);
        }
        $current++;
    }
}
