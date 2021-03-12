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
 * List submissions service class
 * @package local_feedback
 * @copyright 2021, Farhan Karmali <farhan6318@gmail.com>, Guy Thomas <brudinie@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_feedback\service;

use core\session\manager as session_manager;
use local_feedback\models\list_submission_model;
use local_feedback\models\submission_model;
use mod_assign\plugininfo\assignsubmission;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot . '/mod/assign/gradingtable.php');
/**
 * List submissions service class
 * @package local_feedback
 * @copyright 2021, Farhan Karmali <farhan6318@gmail.com>, Guy Thomas <brudinie@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class update_grade extends base_service {

    /**
     * @var $submissionid - this is the submission id we are going to process.
     */
    private $submissionid;

    /**
     * @var $assignid - this is the assign id we are going to process.
     */
    private $assignid;

    /**
     * @var $grade - this is the grade.
     */
    private $grade;

    /**
     * @var $feedback - this is the feedback.
     */
    private $feedback;

    /**
     * @var $userid - this is the userid.
     */
    private $userid;

    public function update_grade_and_feedback(int $submissionid, float $grade = 0, string $feedback = '', int $graderuserid = null) {
        global $DB, $USER;
        $this->submissionid = $submissionid;
        $this->grade = $grade;
        $this->feedback = $feedback;
        if (!$this->submissionid) {
            throw new \coding_exception('You must call set_submissionid before you can use this method');
        }
        $submissionrecord = $DB->get_record('assign_submission', ['id' => $this->submissionid]);
        if (!$submissionrecord) {
            throw new \moodle_exception('Invalid submission id');
        }
        $this->assignid = $submissionrecord->assignment;
        $this->userid = $submissionrecord->userid;
        $cm = get_coursemodule_from_instance('assign', $this->assignid);
        $context = \context_module::instance($cm->id);
        $course = get_course($cm->course);
        $assign = new \assign($context, $cm, $course);
        if ($graderuserid && has_capability('mod/assign:grade', $context, $graderuserid)) {
            $graderuser = $DB->get_record('user', ['id' => $graderuserid]);
            session_manager::set_user($graderuser);
        }
        $feedbackplugin = $assign->get_feedback_plugin_by_type('comments');

        $data = (object) [
                'assignfeedbackcomments_editor' => [
                        'text' => $this->feedback,
                        'format' => 1,
                ]
        ];

        $usergrade = $assign->get_user_grade($this->userid, true);
        $usergrade->grade = $this->grade;
        $assign->update_grade($usergrade);

        $feedbackplugin->save($usergrade, $data);
        session_manager::set_user($USER);
        return true;
    }

    /**
     * Get instance of this service.
     * @return update_grade
     */
    public static function instance(): update_grade {
        return static::get_instance();
    }

}