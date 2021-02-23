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
 * Main lib.
 * Use this library ONLY for hooks.
 * DO NOT put your own functions in here - use a class instead.
 *
 * @package   local_feedback
 * @copyright Copyright (c) 2021 Citricity Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

function local_feedback_before_standard_html_head() {
    global $PAGE, $COURSE;

    // TODO - add a capability check here for teachers (will need new capability launchfeedbacklti.
    if ($PAGE->pagetype === 'mod-assign-view') {
        // Load up mod-assign JS.
        $PAGE->requires->js_call_amd('local_feedback/ltilaunch', 'init',
            [$COURSE->id, $PAGE->cm->id]);

    }
}