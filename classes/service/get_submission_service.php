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
class get_submission_service extends base_service {

    /**
     * @var $submissionid - this is the submission id we are going to process.
     */
    private $submissionid;

    /**
     * @var $assignid - this is the assign id we are going to process.
     */
    private $assignid;

    /**
     * @var $userid - this is the userid who submitted this assignment.
     */
    private $userid;

    public function set_submissionid(int $submissionid): get_submission_service {
        $this->submissionid = $submissionid;
        return $this;
    }

    public function get_data() {
        global $DB;
        if (!$this->submissionid) {
            throw new \coding_exception('You must call set_submissionid before you can use this method');
        }
        $response = [];
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

        $table = new \assign_grading_table($assign, 1, 'singleuser = '.$this->userid, 0, false);
        $table->setup();
        $table->query_db(1);
        $rawdata = $table->rawdata;
        $feedbackplugin = $assign->get_feedback_plugin_by_type('comments');
        $filesubmission = $assign->get_submission_plugin_by_type('file');
        foreach ($rawdata as $key => $value) {
            if ($feedbackplugin) {
                $grade = $assign->get_user_grade($key, false);
                if (isset($grade) && $grade) {
                    $feedback = $feedbackplugin->view($grade);
                } else {
                    $feedback = null;
                }
            }
            if ($filesubmission) {
                $file = new \assign_files($context, $value->submissionid, 'submission_files', 'assignsubmission_file');
                $files = [];
                if (isset($file->dir['files']) && !empty($file->dir['files'])) {
                    $file = reset($file->dir['files']);
                    $url = \moodle_url::make_pluginfile_url(
                            $file->get_contextid(),
                            'assignsubmission_file',
                            'submission_files',
                            $file->get_itemid(),
                            $file->get_filepath(),
                            $file->get_filename()
                    )->out();
                    $files[] = $url;
                }
            }
            $response[] = submission_model::from_data([
                    'studentid' => $key,
                    'firstname' => $value->firstname,
                    'lastname' => $value->lastname,
                    'email' => $value->email,
                    'status' => $value->status,
                    'submissionid' => $value->submissionid,
                    'grade' => $value->grade,
                    'timesubmitted' => $value->timesubmitted,
                    'timemarked' => $value->timemarked,
                    'feedbackcomments' => $feedback,
                    'files' => $files
            ]);
        }
        $result = reset($response);
        return $result;
    }

    /**
     * Get instance of this service.
     * @return get_submission_service
     */
    public static function instance(): get_submission_service {
        return static::get_instance();
    }

}