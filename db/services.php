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
 * @copyright 2021, Farhan Karmali <farhan6318@gmail.com>, Guy Thomas <brudinie@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_feedback_list_submissions' => [
        'classname' => 'local_feedback\\webservice\\list_submissions',
        'methodname' => 'service',
        'description' => 'Return list of submissions of the assignment',
        'type' => 'read',
        'capabilities' => 'mod/assign:grade',
    ],
    'local_feedback_get_submission' => [
        'classname' => 'local_feedback\\webservice\\get_submission',
        'methodname' => 'service',
        'description' => 'Return details of one submission',
        'type' => 'read',
        'capabilities' => 'mod/assign:grade',
    ],
    'local_feedback_update_grade' => [
        'classname' => 'local_feedback\\webservice\\update_grade',
        'methodname' => 'service',
        'description' => 'Update grade and feedback for one submission',
        'type' => 'write',
        'capabilities' => 'mod/assign:grade',
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