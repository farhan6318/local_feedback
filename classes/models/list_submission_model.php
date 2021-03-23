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

namespace local_feedback\models;

defined('MOODLE_INTERNAL') || die;

class list_submission_model extends base_model {

    /**
     * @var string
     * @wsdesc Name of assignment.
     * @wsrequired true
     */
    public $assignmentname;

    /**
     * @var batch_output_model
     * @wsdesc Batch information.
     * @wsrequired true
     */
    public $batch;

    /**
     * @var grade_model
     * @wsdesc Grade model
     * @wsrequired true
     */
    public $grademodel;

    /**
     * @var submission_model[]
     * @wsdesc Submission model
     * @wsrequired true
     */
    public $submissions;

    public function __construct($batch, $grademodel, $submissions, $assignmentname) {
        $this->set_props_construct_args(func_get_args());
    }
    /**
     * This is here for IDE completion.
     * @param array|object $data
     * @return array
     * @throws \coding_exception
     */
    public static function from_data($data): list_submission_model {
        return parent::do_make_from_data($data);
    }
}