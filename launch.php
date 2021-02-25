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
 * LTI launch endpoint.
 *
 * @package    local_feedback
 * @author     Guy Thomas
 * @copyright  Copyright (c) 2020 Citricity Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/lti/locallib.php');

$courseid = required_param('course', PARAM_INT);
$cmid = optional_param('cmid', null, PARAM_INT);
require_login($courseid, false);

$PAGE->set_course(get_course($courseid));

$config = get_config('local_feedback');
$configured = !empty($config) && !empty($config->ltilaunchurl)
        && !empty($config->ltikey) && !empty($config->ltisecret);

$launchcontainer = LTI_LAUNCH_CONTAINER_DEFAULT;

$instance = (object) [
    'id' => 0,
    'course' => $courseid,
    'name' => 'Feedback generator',
    'typeid' => null,
    'instructorchoicesendname' => 1,
    'instructorchoicesendemailaddr' => 1,
    'instructorchoiceallowroster' => null,
    'instructorcustomparameters' => null,
    'instructorchoiceacceptgrades' => 0,
    'resourcekey' => $config->ltikey,
    'password' => $config->ltisecret,
    'launchcontainer' => $launchcontainer,
    'toolurl' => $config->ltilaunchurl,
    'securetoolurl' => '',
    'servicesalt' => uniqid('', true),
    'debuglaunch' => 0,
];

if ($cmid !== null) {
    $instance->resource_link_id = $cmid;
}

lti_launch_tool($instance);