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
 * Web service token viewer. Visit this page to see the token for these web services.
 */

require_once(__DIR__.'/../../config.php');

use local_feedback\service\auto_config;

$ac = auto_config::instance()->configure();

$context = context_system::instance();

require_login();
require_capability('moodle/site:configview', $context);

$PAGE->set_context($context);

$PAGE->set_url('/local/feedback/wstoken.php');

$PAGE->set_title(get_string('wstokendetails', 'local_feedback'));
$PAGE->set_heading(get_string('wstokendetails', 'local_feedback'));

echo $OUTPUT->header();

echo $OUTPUT->notification(auto_config::get_ws_token()->token);

echo $OUTPUT->footer();