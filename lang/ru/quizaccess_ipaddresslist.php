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

$string['addsubnet'] = 'Добавать расположение';
$string['allowedsubnets'] = 'Разрешенные расположения';
$string['allowedsubnets_help'] = 'Доступ к тесту может быть ограничен конкретными расположениями на основании IP-адреса. Список расположений и соответствующих IP-адресов и подсетей устанавливается администратором сайта. Не выбирайте ничего, чтобы отключить эту проверку расположения.';
$string['editsubnet'] = 'Редактирование расположения';
$string['managesubnets'] = 'Управление расположениями';
$string['pluginname'] = 'Правило доступа к тесту: список IP-адресов';
$string['subnet'] = 'IP подсеть';
$string['subnetwrong'] = 'Этот тест доступен только с определенных компьютеров; этот компьютер не находится в списке разрешенных.';
$string['subnet_help'] = '<p>Укажите список полных или частичных IP-адресов через запятую.</p><p>Примеры:</p><ul><li>192.168.10.1</li><li>192.168.</li><li>231.54.211.0/20</li><li>231.3.56.10-20</li><li>192.168.10.1,192.168.,231.54.211.0/20,231.3.56.10-20</li></ul>';

