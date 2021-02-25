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
 * Get version information.
 *
 * Based on tool_ally
 *
 * @package   local_feedback
 * @copyright Copyright (c) 2017 Blackboard Inc. (http://www.blackboard.com) / 2019 Titus Learning
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_feedback\webservice;

use local_feedback\models\list_submission_model;
use local_feedback\service\list_submissions_service;
use external_api;
use external_value;
use local_feedback\models\batch_model;
use external_single_structure;
use local_feedback\definition_helper;

defined('MOODLE_INTERNAL') || die();

/**
 * Get list of submissions
 *
 * @package   local_feedback
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class list_submissions extends external_api {
    /**
     * @return \external_function_parameters
     */
    public static function service_parameters() {
        return new \external_function_parameters([
            'request' => new external_single_structure([
                'batch' => new external_single_structure(
                        definition_helper::define_for_webservice(batch_model::class), 'Batch controls', VALUE_OPTIONAL
                ),
                'cmid' => new external_value(PARAM_INT, 'Course Module id', VALUE_REQUIRED)
            ])
        ]);
    }

    /**
     * @return \external_single_structure
     */
    public static function service_returns() {
        return new \external_single_structure([
            'response' => new \external_single_structure(
                definition_helper::define_for_webservice(list_submission_model::class)
            )
        ]);
    }

    /**
     * @return array
     */
    public static function service(array $request) {
        $args = (object) self::validate_parameters(self::service_parameters(), ['request' => $request]);
        $page = $args->request['batch']['page'] ?? 1;
        $perpage = $args->request['batch']['perpage'] ?? null;
        $model = $perpage ? new batch_model($page, $perpage) : new batch_model($page);
        $result =  ['response' => list_submissions_service::instance()->set_batch($model)->set_cmid($args->request['cmid'])->get_data()];
        return $result;
    }
}
