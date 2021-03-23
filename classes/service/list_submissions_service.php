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

use local_feedback\models\grade_model;
use local_feedback\models\list_submission_model;
use local_feedback\models\submission_model;
use local_feedback\models\batch_model;
use mod_assign\plugininfo\assignsubmission;
use local_feedback\models\batch_output_model;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot . '/mod/assign/gradingtable.php');
/**
 * List submissions service class
 * @package local_feedback
 * @copyright 2021, Farhan Karmali <farhan6318@gmail.com>, Guy Thomas <brudinie@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class list_submissions_service extends base_service {

    /**
     * @var batch_model - this is the batch we are going to process.
     */
    private $batch;

    /**
     * @var $cmid - this is the cmid we are going to process.
     */
    private $cmid;

    public function set_cmid(int $cmid): list_submissions_service {
        $this->cmid = $cmid;
        return $this;
    }

    public function set_batch(batch_model $batch): list_submissions_service {
        $this->batch = $batch;
        return $this;
    }


    public function get_data() {
        global $DB;
        if (!$this->cmid) {
            throw new \coding_exception('You must call set_cmid before you can use this method');
        }
        if (!$this->batch) {
            throw new \coding_exception('You must call set_batch before you can use this method');
        }

        $cm = get_coursemodule_from_id('assign', $this->cmid);
        $context = \context_module::instance($cm->id);
        $course = get_course($cm->course);
        $assign = new \assign($context, $cm, $course);
        $participants = $assign->list_participants(null, false);

        $page = $this->batch->page;
        $perpage = $this->batch->perpage;

        $count = count($participants);
        $pages = ceil($count / $perpage);

        if ($page > $pages) {
            $msg = "Requested page ($page) is greater than the available number of pages ($pages)";
            throw new \moodle_exception($msg);
        }

        $batchoutput = new batch_output_model($page, $perpage, $pages, $count);

        $gradeitem = $assign->get_grade_item();
        $grademax = $gradeitem->grademax;
        $grademin = $gradeitem->grademin;
        if ($gradeitem->gradetype == GRADE_TYPE_VALUE) {
            $gradetype = 'Value';
            $gradedetails = grade_model::from_data(['gradetype' => $gradetype, 'grademin' => $grademin, 'grademax' => $grademax]);
        } else if ($gradeitem->gradetype == GRADE_TYPE_SCALE) {
            $gradetype = 'Scale';
            $scale = $DB->get_record('scale', ['id' => $gradeitem->scaleid]);
            $scalemenu = make_menu_from_list($scale->scale);
            $gradedetails = grade_model::from_data(['gradetype' => $gradetype, 'grademin' => $grademin, 'grademax' => $grademax, 'scalemenu' => $scalemenu]);
        }

        $table = new \assign_grading_table($assign, count($participants), '', 0, false);
        $table->setup();
        $table->query_db(count($participants));
        $rawdata = $table->rawdata;
        $response = [];
        $feedbackplugin = $assign->get_feedback_plugin_by_type('comments');
        $filesubmission = $assign->get_submission_plugin_by_type('file');
        foreach ($rawdata as $key => $value) {
            $text = $DB->get_field('assignsubmission_onlinetext', 'onlinetext', ['submission' => $value->submissionid]);
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
                    foreach ($file->dir['files'] as $filename => $file) {
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
            }
            if ($value->grade == ASSIGN_GRADE_NOT_SET) {
                $value->grade = null;
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
                'files' => $files,
                'submissiontext' => $text
            ]);
        }
        $result = list_submission_model::from_data(['submissions' => $response, 'grademodel' => $gradedetails, 'batch' => $batchoutput, 'assignmentname' => $cm->name]);
        return $result;
    }

    /**
     * Get instance of this service.
     * @return list_submissions_service
     */
    public static function instance(): list_submissions_service {
        return static::get_instance();
    }

}