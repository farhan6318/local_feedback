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
 * Web service definitions.
 *
 * @package   local_feedback
 * @copyright Copyright (c) 2020 Titus Learning
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
        'local_feedback_version_info' => [
                'classname' => 'local_feedback\\webservice\\version_info',
                'methodname' => 'service',
                'description' => 'Return key version info for Feedback webservices',
                'type' => 'read',
                'capabilities' => 'moodle/site:configview',
        ],
];

$services = [
        'Feedback Generation API Services' => [
                'functions' => array_keys($functions),
                'enabled' => 0,
                'restrictedusers' => 0,
                'shortname' => 'local_feedback',
                'downloadfiles' => 1,
                'uploadfiles' => 1,
        ],
];