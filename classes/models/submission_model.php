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

class submission_model extends base_model {

    /**
     * @var int
     * @wsdesc Student id.
     * @wsrequired true
     */
    public $studentid;

    /**
     * @var string
     * @wsdesc Firstname.
     * @wsrequired true
     */
    public $firstname;

    /**
     * @var string
     * @wsdesc lastname.
     * @wsrequired true
     */
    public $lastname;

    /**
     * @var string
     * @wsdesc Email.
     * @wsrequired true
     */
    public $email;

    /**
     * @var string
     * @wsdesc status.
     * @wsrequired false
     */
    public $status;

    /**
     * @var int
     * @wsdesc submissionid.
     * @wsrequired false
     */
    public $submissionid;

    /**
     * @var float
     * @wsdesc grade.
     * @wsrequired false
     */
    public $grade;

    /**
     * @var int
     * @wsdesc timesubmitted.
     * @wsrequired false
     */
    public $timesubmitted;

    /**
     * @var int
     * @wsdesc timemarked.
     * @wsrequired false
     */
    public $timemarked;

    /**
     * @var string
     * @wsdesc feedback comment.
     * @wsrequired false
     */
    public $feedbackcomments;

    /**
     * @var string[]
     * @wsdesc submission file.
     * @wsrequired false
     */
    public $files;

    public function __construct(
        int $studentid,
        string $firstname,
        string $lastname,
        string $email,
        ?string $status = null,
        ?int $submissionid = null,
        ?float $grade = null,
        ?int $timesubmitted = null,
        ?int $timemarked = null,
        ?string $feedbackcomments = null,
        ?array $files = null) {
        $this->set_props_construct_args(func_get_args());
    }
    /**
     * This is here for IDE completion.
     * @param array|object $data
     * @return array
     * @throws \coding_exception
     */
    public static function from_data($data): submission_model {
        return parent::do_make_from_data($data);
    }
}