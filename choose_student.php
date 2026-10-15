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

 require(__DIR__.'/../../config.php');
 require_once(__DIR__.'/lib.php');
 require_once(__DIR__.'/choose_student_form.php');

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

$PAGE->set_url('/mod/istudy/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);

$school_term = mod_istudyschedule\utils::get_term($USER->schooltermsch);
$advisor = mod_istudyschedule\teacher_info::get_advisor($USER->advisorsch);
$school_scheduleonly = $USER->scheduleonlysch;
$lastnamesearch = mod_istudyschedule\utils::get_lastnamesearch($USER->lastnamesearchsch);

// Display form.
$formparams = array('schoolterm' => $school_term, 'advisor' => $advisor, 'lastnamesearch' => $lastnamesearch, 'school_scheduleonly' => $school_scheduleonly);
$mform = new mod_istudyschedule_choose_student_form(null, $formparams);
$mform->set_data($istudyscheduleid);
if ($mform->is_cancelled()) {
	// Cancelled forms redirect to the course main page.
	$url = new moodle_url('/course/view.php', array('id' => $course->id));
	redirect($url);
	return;

} else if ($data = $mform->get_data()) {

	// Make sure at least 1 student was chosen.
	$student_was_chosen = mod_istudyschedule\student_info::get_student_chosen($data);
	if ($student_was_chosen) {
		// Get the course data.
		mod_istudyschedule\student_info::get_the_schedules($data);
		// Changes have been saved - reload the page and output the message 'Changes Saved'.
		$USER->just_savedsch = 'Yes';
		$mform->reset_checkboxes($data);
	}
	else{
		$USER->just_savedsch = 'Error';
		$choose_studenturl = new moodle_url('/mod/istudyschedule/choose_student.php', array('id' => $cm->id));
		redirect($choose_studenturl);
	}
} else {
	// Form has not been submitted or there was an error.
	// Just display the form.
	$mform->set_data(array('id' => $id));
}
echo $OUTPUT->header();
// Finish the page.
if ($USER->just_savedsch == 'Yes') {
	echo $OUTPUT->notification('STUDENT SCHEDULES HAVE BEEN PRINTED - If schedules did not print, please turn off your popup blocker for this site', 'notifysuccess');
	$USER->just_savedsch = 'No';
}
elseif ($USER->just_savedsch == "Error") {
	echo $OUTPUT->notification('ERROR: Please choose at least 1 student.');
	$USER->just_savedsch = 'No';
}
$mform->display();
echo $OUTPUT->footer();