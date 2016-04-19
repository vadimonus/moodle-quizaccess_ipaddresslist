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

require_once("$CFG->libdir/adminlib.php");

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
        parent::__construct('quizaccess_ipaddresslist_subnet_list_editor', get_string('managesubnets', 'quizaccess_ipaddresslist'), '', '');
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

        $url = new moodle_url('/mod/quiz/accessrule/ipaddresslist/subnets.php', array('sesskey' => sesskey()));

        $actions = '';
        if ($current != 1 && $count > 1) {
            $upurl = new moodle_url($url, array('action' => 'up', 'id' =>$subnet->id));
            $upimg = html_writer::img($OUTPUT->pix_url('t/up'), get_string('up'), array('class' => 'iconsmall'));
            $upattr = array('title' => get_string('up'));
            $uplink = html_writer::link($upurl, $upimg, $upattr);
            $actions .= $uplink;
        } else {
            $upimg = html_writer::img($OUTPUT->pix_url('spacer'), '', array('class' => 'iconsmall'));
            $actions .= $upimg;
        }

        if ($current != $count && $count > 1) {
            $downurl = new moodle_url($url, array('action' => 'down', 'id' =>$subnet->id));
            $downimg = html_writer::img($OUTPUT->pix_url('t/down'), get_string('down'), array('class' => 'iconsmall'));
            $downattr = array('title' => get_string('down'));
            $downlink = html_writer::link($downurl, $downimg, $downattr);
            $actions .= $downlink;
        } else {
            $downimg = html_writer::img($OUTPUT->pix_url('spacer'), '', array('class' => 'iconsmall'));
            $actions .= $downimg;
        }

        $editurl = new moodle_url('/mod/quiz/accessrule/ipaddresslist/subnet.php', array('id' =>$subnet->id));
        $editimg = html_writer::img($OUTPUT->pix_url('t/edit'), get_string('edit'), array('class' => 'iconsmall'));
        $editattr = array('title' => get_string('edit'));
        $editlink = html_writer::link($editurl, $editimg, $editattr);
        $actions .= $editlink;

        $deleteurl = new moodle_url($url, array('action' => 'delete', 'id' =>$subnet->id));
        $deleteimg = html_writer::img($OUTPUT->pix_url('t/delete'), get_string('delete'), array('class' => 'iconsmall'));
        $deleteattr = array('title' => get_string('delete'));
        $deletelink = html_writer::link($deleteurl, $deleteimg, $deleteattr);
        $actions .= $deletelink;

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
        $table->head = array(get_string('name'), get_string('subnet', 'quizaccess_ipaddresslist'), '');
        $table->colclasses = array('leftalign', 'leftalign', 'centeralign');
        $table->id = 'quizaccess_ipaddresslist';
        $table->attributes['class'] = 'admintable generaltable';
        $table->data = array();
        $subnets = $DB->get_records('quizaccess_ipaddresslist_net', array(), 'sortorder ASC');
        $current = 1;
        $count = count($subnets);
        foreach ($subnets as $subnet) {
            $table->data[] = array($subnet->name, $subnet->subnet, $this->actions_list($subnet, $current, $count));
            $current++;
        }
        $addurl = new moodle_url('/mod/quiz/accessrule/ipaddresslist/subnet.php');
        $addimg = html_writer::img($OUTPUT->pix_url('t/add'), get_string('add'), array('class' => 'iconsmall'));
        $addattr = array('title' => get_string('add'));
        $addlink = html_writer::link($addurl, $addimg, $addattr);
        $table->data[] = array($addlink, '', '');

        $return = $OUTPUT->heading(get_string('managesubnets', 'quizaccess_ipaddresslist'), 3);
        $return .= $OUTPUT->box_start('generalbox');
        $return .= html_writer::table($table);
        $return .= html_writer::div(get_string('tablenosave', 'admin'));
        $return .= $OUTPUT->box_end();
        return highlight($query, $return);
    }
}
