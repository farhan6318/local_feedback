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
 * Feedback lti launch code.
 * @package     local_feedback
 * @author      Guy Thomas
 * @copyright   2021 Citricity Ltd
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from "jquery";
import Config from 'core/config';
import {get_string as getString} from "core/str";

export default class main {
    static init(course, cmid) {
        if (cmid) {
            getString("launchfeedbacklti", "local_feedback").then((buttonStr) => {
                // Append to grading summary
                const newButton = `<button class="btn btn btn-secondary ml-1" id="laucnfeedbacklti">${buttonStr}</button>`;
                $(".gradingsummary .submissionlinks").append(newButton);
            });

            $(".gradingsummary .submissionlinks").on("click", "#laucnfeedbacklti", (e) => {
                e.preventDefault();
                window.location.href = `${Config.wwwroot}/local/feedback/launch.php?course=${course}&cmid=${cmid}`;
            });
        }
    }
}