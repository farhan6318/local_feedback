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
 * Settings.
 *
 * @package   local_feedback
 * @copyright Copyright (c) 2021 Citricity Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// We have to include this so that it's available before an upgrade completes and registers the classes for autoloading.
require_once($CFG->dirroot.'/local/feedback/classes/adminsetting/feedback_trim.php');

use local_feedback\adminsetting\feedback_trim;

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_feedback', new lang_string('pluginname', 'local_feedback'));

    $settings->add(new feedback_trim('local_feedback/ltikey', new lang_string('key', 'local_feedback'),
        new lang_string('keydesc', 'local_feedback'), '', PARAM_ALPHANUMEXT));

    $settings->add(new admin_setting_configpasswordunmask('local_feedback/ltisecret',
        new lang_string('secret', 'local_feedback'), new lang_string('secretdesc', 'local_feedback'), ''));

    $settings->add(new admin_setting_configtext('local_feedback/ltilaunchurl', new lang_string('launchurl', 'local_feedback'),
        new lang_string('launchurldesc', 'local_feedback'), '', PARAM_URL, 60));

    $ADMIN->add('tools', $settings);
}
