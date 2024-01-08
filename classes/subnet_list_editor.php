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

require_once($CFG->libdir . '/adminlib.php');

/**
 * Class representing subnet list editor.
 *
 * @package    quizaccess_ipaddresslist
 * @copyright  2016 Vadim Dvorovenko <Vadimon@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quizaccess_ipaddresslist_subnet_list_editor extends admin_setting {

    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('quizaccess_ipaddresslist_subnet_list_editor',
                get_string('managesubnets', 'quizaccess_ipaddresslist'), '', '');
    }

    /**
     * Always returns true, does nothing.
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true, does nothing.
     *
     * @return true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Always returns '', does not write anything.
     *
     * @param mixed $data ignored
     * @return string Always returns ''
     */
    public function write_setting($data) {
        return '';
    }

    /**
     * Generates html with action links.
     *
     * @param stdClass $subnet
     * @param int $current
     * @param int $count
     * @return string
     */
    private function actions_list($subnet, $current, $count) {
        global $OUTPUT;

        $url = new moodle_url('/mod/quiz/accessrule/ipaddresslist/subnets.php', ['sesskey' => sesskey()]);

        $actions = '';
        if ($current != 1 && $count > 1) {
            $upurl = new moodle_url($url, ['action' => 'up', 'id' => $subnet->id]);
            $upicon = new pix_icon('t/up', get_string('up'));
            $actions .= $OUTPUT->action_link($upurl, '', null, [], $upicon);
        } else {
            $actions .= $OUTPUT->pix_icon('spacer', '');
        }

        if ($current != $count && $count > 1) {
            $downurl = new moodle_url($url, ['action' => 'down', 'id' => $subnet->id]);
            $downicon = new pix_icon('t/down', get_string('down'));
            $actions .= $OUTPUT->action_link($downurl, '', null, [], $downicon);
        } else {
            $actions .= $OUTPUT->pix_icon('spacer', '');
        }

        $editurl = new moodle_url('/mod/quiz/accessrule/ipaddresslist/subnet.php', ['id' => $subnet->id]);
        $editicon = new pix_icon('t/edit', get_string('edit'));
        $actions .= $OUTPUT->action_link($editurl, '', null, [], $editicon);

        $deleteurl = new moodle_url($url, ['action' => 'delete', 'id' => $subnet->id]);
        $deleteicon = new pix_icon('t/delete', get_string('delete'));
        $actions .= $OUTPUT->action_link($deleteurl, '', null, [], $deleteicon);

        return $actions;
    }

    /**
     * Builds the XHTML to display the control.
     *
     * @param string $data Unused
     * @param string $query
     * @return string
     */
    public function output_html($data, $query = '') {
        global $OUTPUT, $DB;

        $table = new html_table();
        $table->head = [get_string('name'), get_string('subnet', 'quizaccess_ipaddresslist'), ''];
        $table->colclasses = ['leftalign', 'leftalign', 'centeralign'];
        $table->id = 'quizaccess_ipaddresslist';
        $table->attributes['class'] = 'admintable generaltable';
        $table->data = [];
        $subnets = $DB->get_records('quizaccess_ipaddresslist_net', [], 'sortorder ASC');
        $current = 1;
        $count = count($subnets);
        foreach ($subnets as $subnet) {
            $table->data[] = [$subnet->name, $subnet->subnet, $this->actions_list($subnet, $current, $count)];
            $current++;
        }
        $addurl = new moodle_url('/mod/quiz/accessrule/ipaddresslist/subnet.php');
        $addicon = new pix_icon('t/add', get_string('add'));
        $addlink = $OUTPUT->action_link($addurl, '', null, [], $addicon);
        $table->data[] = [$addlink, '', ''];

        $return = $OUTPUT->heading(get_string('managesubnets', 'quizaccess_ipaddresslist'), 3);
        $return .= $OUTPUT->box_start('generalbox');
        $return .= html_writer::table($table);
        $return .= html_writer::div(get_string('tablenosave', 'admin'));
        $return .= $OUTPUT->box_end();
        return highlight($query, $return);
    }
}
