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

defined('MOODLE_INTERNAL') || die;
global $CFG;
require_once($CFG->dirroot . '/mod/assign/tests/generator.php');

use local_feedback\models\batch_model;
use local_feedback\service\list_submissions_service;

/**
 * Tests for list submissions service.
 *
 * @package     local_feedback
 * @copyright   2021, Farhan Karmali <farhan6318@gmail.com>, Guy Thomas <brudinie@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class list_submissions_service_test extends advanced_testcase {

    // Use the generator helper.
    use mod_assign_test_generator;

    public function test_list_submissions() {
        global $DB;
        $this->resetAfterTest();
        $dg = $this->getDataGenerator();
        $course = $dg->create_course();
        $assigngenerator = $dg->get_plugin_generator('mod_assign');
        $instance = $assigngenerator->create_instance(['course' => $course->id]);
        $cm = get_coursemodule_from_instance('assign', $instance->id);
        $context = context_module::instance($cm->id);
        $assign = new assign($context, $cm, $course);

        $roles = $DB->get_records('role', null, '', 'shortname, id');
        $teacher = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($teacher->id,
                $course->id,
                $roles['teacher']->id);

        $this->setUser($teacher);

        // Enrol two students.
        $students = [];
        for ($i = 0; $i < 2; $i++) {
            $student = $this->getDataGenerator()->create_user();
            $this->getDataGenerator()->enrol_user($student->id,
                    $course->id,
                    $roles['student']->id);
            $students[$student->id] = $student;
        }
        $participants = $assign->list_participants(null, false);
        $this->add_submission($students[reset($participants)->id], $assign);

        $page = 1;
        $perpage = null;
        $model = $perpage ? new batch_model($page, $perpage) : new batch_model($page);

        $result = list_submissions_service::instance()->set_batch($model)->set_cmid($cm->id)->get_data();

        $this->assertNotEmpty($result);
        $this->assertObjectHasAttribute('submissions', $result);
        $this->assertIsArray($result->submissions);
        $latestsubmission = array_pop($result->submissions);
        $this->assertSame($latestsubmission->email, $student->email);
    }

    public function test_get_submission() {
        global $DB;
        $this->resetAfterTest();
        $dg = $this->getDataGenerator();
        $course = $dg->create_course();
        $assigngenerator = $dg->get_plugin_generator('mod_assign');
        $instance = $assigngenerator->create_instance(['course' => $course->id]);
        $cm = get_coursemodule_from_instance('assign', $instance->id);
        $context = context_module::instance($cm->id);
        $assign = new assign($context, $cm, $course);

        $roles = $DB->get_records('role', null, '', 'shortname, id');
        $teacher = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($teacher->id,
                $course->id,
                $roles['teacher']->id);

        $this->setUser($teacher);

        // Enrol two students.
        $students = [];
        for ($i = 0; $i < 2; $i++) {
            $student = $this->getDataGenerator()->create_user();
            $this->getDataGenerator()->enrol_user($student->id,
                    $course->id,
                    $roles['student']->id);
            $students[$student->id] = $student;
        }
        $participants = $assign->list_participants(null, false);
        $this->add_submission($students[reset($participants)->id], $assign);
        $page = 1;
        $perpage = null;
        $model = $perpage ? new batch_model($page, $perpage) : new batch_model($page);

        $result1 = list_submissions_service::instance()->set_batch($model)->set_cmid($cm->id)->get_data();
        $submission = reset($result1->submissions);

        $result = \local_feedback\service\get_submission_service::instance()->set_submissionid($submission->submissionid)->get_data();
        $this->assertIsObject($result);
        $this->assertSame($result->submissionid, $submission->submissionid);
    }

    public function test_update_grade_and_feedback() {
        global $DB;
        $this->resetAfterTest();
        $dg = $this->getDataGenerator();
        $course = $dg->create_course();
        $assigngenerator = $dg->get_plugin_generator('mod_assign');
        $instance = $assigngenerator->create_instance(['course' => $course->id]);
        $cm = get_coursemodule_from_instance('assign', $instance->id);
        $context = context_module::instance($cm->id);
        $assign = new assign($context, $cm, $course);

        $roles = $DB->get_records('role', null, '', 'shortname, id');
        $teacher = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($teacher->id,
                $course->id,
                $roles['teacher']->id);

        $this->setUser($teacher);

        // Enrol two students.
        $students = [];
        for ($i = 0; $i < 2; $i++) {
            $student = $this->getDataGenerator()->create_user();
            $this->getDataGenerator()->enrol_user($student->id,
                    $course->id,
                    $roles['student']->id);
            $students[$student->id] = $student;
        }
        $participants = $assign->list_participants(null, false);
        $this->add_submission($students[reset($participants)->id], $assign);
        $page = 1;
        $perpage = null;
        $model = $perpage ? new batch_model($page, $perpage) : new batch_model($page);

        $result1 = list_submissions_service::instance()->set_batch($model)->set_cmid($cm->id)->get_data();
        $submission = reset($result1->submissions);

        $result = \local_feedback\service\update_grade::instance()->update_grade_and_feedback($submission->submissionid, 10.75, 'test feedback');
        $this->assertTrue($result);
        $result1 = list_submissions_service::instance()->set_batch($model)->set_cmid($cm->id)->get_data();
        $submission = reset($result1->submissions);

        $this->assertSame($submission->grade, 10.75);
        $this->assertSame($submission->feedbackcomments, 'test feedback');
    }
}