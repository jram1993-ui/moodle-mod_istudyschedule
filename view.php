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
 * Prints a particular instance of istudyschedule
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_istudyschedule
 * @copyright  2024 Jesus Ramirez <jram1993@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Replace istudyschedule with the name of your module and remove this line.

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');
require_once(__DIR__.'/search_criteria_form.php');

global $DB, $USER;

/**
 * Course module id.
 */
$id = optional_param('id', 0, PARAM_INT);

/**
 * Activity instance id.
 */
$i = optional_param('i', 0, PARAM_INT);

if ($id) {
    $cm = get_coursemodule_from_id('istudyschedule', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $moduleinstance = $DB->get_record('istudyschedule', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
    $moduleinstance = $DB->get_record('istudyschedule', array('id' => $i), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('istudyschedule', $moduleinstance->id, $course->id, false, MUST_EXIST);
}
$istudyscheduleid = $cm->instance;

require_login($course, true, $cm);

$modulecontext = \context_module::instance($cm->id);

$PAGE->set_url('/mod/istudyschedule/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);

// Output starts here.
echo $OUTPUT->header();

$mform = new mod_istudyschedule_search_criteria_form();
$mform->set_data($istudyscheduleid);
if ($mform->is_cancelled()) {
	// Cancelled forms redirect to the course main page.
	$url = new moodle_url('/course/view.php', array('id' => $course->id));
	redirect($url, 'Returning to Course Page', 1);
} else if (($data = $mform->get_data())) {
	$school_term = $data->schoolterm;
	$school_advisor = $data->advisor;
	$school_scheduleonly = $data->scheduleonly;
	$school_lastnamesearch = $data->lastnamesearch;
	$USER->schooltermsch = $school_term;
	$USER->advisorsch = $school_advisor;
	$USER->scheduleonlysch = $school_scheduleonly;
	$USER->lastnamesearchsch = $school_lastnamesearch;
	// The student schedules have not been saved yet. Set to Yes after the first print.
	$USER->just_savedsch = 'No';
	$choose_studenturl = new moodle_url('/mod/istudyschedule/choose_student.php', array('id' => $cm->id));
	redirect($choose_studenturl);
} else {
	// Form has not been submitted or there was an error.
	// Just display the form.
	$mform->set_data(array('id' => $id));
}

$mform->display();
echo $OUTPUT->footer();