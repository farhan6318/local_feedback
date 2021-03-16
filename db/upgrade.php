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
 * local feedback webservices
 *
 * @package   local_feedback
 * @copyright 2021, Farhan Karmali <farhan6318@gmail.com>, Guy Thomas <brudinie@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;


/**
 * Titus core web services upgrades
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool always true
 */
function xmldb_local_feedback_upgrade($oldversion) {
    global $CFG, $DB;
    $dbman = $DB->get_manager();
    if ($oldversion < 2021031204) {
        $do = $DB->get_record('external_services', ['name' => 'Feedback Generation API Services']);
        if ($do) {
            $do->name = \local_feedback\service\auto_config::WEB_SERVICE_NAME;
            $DB->update_record('external_services', $do);
        }
        upgrade_plugin_savepoint(true, 2021031204, 'local', 'tlwebservices');
    }

    return true;
}