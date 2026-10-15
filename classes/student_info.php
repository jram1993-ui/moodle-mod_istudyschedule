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
 * @package    mod_istudyschedule
 * @copyright  2024 Jesus Ramirez <jram1993@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_istudyschedule;

use stdClass;
use Throwable;

/**
 * Class student_info
 *
 * @package    mod_istudy
 * @copyright  2024 Jesus Ramirez <jram1993@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class student_info {

    // Make sure that at least 1 student was chosen.
    public static function get_student_chosen($submission) {
        $student_count = $submission->studentcount;
        for ($i = 1; $i <= $student_count; $i++) {
            $this_student = 'student_chosen' . $i;
            // Check to make sure that the student exists and has not been deleted (rare occurance).
            if (!property_exists($submission, $this_student)) {
                return false;
            }
            $this_studentid = $submission->$this_student;
            if ($this_studentid <> 0) {
                return true;
            }
        }
        // No students were chosen.
        return false;
    }

    // Get single student record.
    public static function get_singlestudent($student_id, $school_term, $school_year) {
        global $DB;
        // Student Info
        $student_info = new stdClass();
        $student_info->id = $student_id;
        $student_temp = $DB->get_record('user', ['id' => $student_id] , '*');
        $student_info->lastname = $student_temp->lastname;
        $student_info->firstname = $student_temp->firstname;
        $student_info->middlename = utils::get_field_data($student_id, utils::get_fieldid('middlename'));
        $student_info->otherlastname = utils::get_field_data($student_id, utils::get_fieldid('otherlastname'));
        $student_info->email = $student_temp->email;
        $student_info->schoolid = utils::get_field_data($student_id, utils::get_fieldid('schoolid'));
        $student_info->address = utils::get_field_data($student_id, utils::get_fieldid('address'));
        // If a city was not chosen from the list, then it equals '' instead of 'None'.
        $temp_city = utils::get_field_data($student_id, utils::get_fieldid('city'));
        if ($temp_city <> 'None') {
            $student_info->city = $temp_city;
        }
        else {
            $student_info->city = '';
        }
        $student_info->studentcell = utils::get_field_data($student_id, utils::get_fieldid('studentcell'));
        $student_info->parentname = utils::get_field_data($student_id, utils::get_fieldid('parentname'));
        $student_info->homephone = utils::get_field_data($student_id, utils::get_fieldid('homephone'));
        $student_info->daytimephone = utils::get_field_data($student_id, utils::get_fieldid('daytimephone'));
        $student_info->term = $school_term;
        $student_info->year = $school_year;
        $student_info->gradelevel = utils::get_gradelevel(utils::get_field_data($student_id, utils::get_fieldid('graduationyear')));
        $my_advisor = utils::get_field_data($student_id, utils::get_fieldid('advisor'));
        if ($my_advisor <> 'None') {
            $student_info->advisor = $my_advisor;
            $student_info->roomextension = teacher_info::get_advisor_room($student_info->advisor) . '/' . teacher_info::get_advisor_extension($student_info->advisor);
        }
        else {
            $student_info->advisor = '';
            $student_info->roomextension = '';
        }
        $dob_temp = utils::get_field_data($student_id, utils::get_fieldid('dob'));
        $student_info->birthdate = utils::check_date($dob_temp);
        // If a birthdate was entered then calculate the age, otherwise return ''.
        if ($dob_temp > 0) {
            $student_info->age = floor((time() - $dob_temp) / 31556926);
        }
        else {
            $student_info->age = '';
        }
        $student_info->gpa = utils::get_field_data($student_id, utils::get_fieldid('gpa'));
        $student_info->creditsearned = utils::get_field_data($student_id, utils::get_fieldid('creditsearned'));
        $student_info->creditsneeded = utils::get_field_data($student_id, utils::get_fieldid('creditsneeded'));
        $student_info->parentname = utils::get_field_data($student_id, utils::get_fieldid('parentname'));
        $student_info->parentemail = utils::get_field_data($student_id, utils::get_fieldid('parentemail'));
        $student_info->previousschool = utils::get_field_data($student_id, utils::get_fieldid('previousschool'));
        $student_info->startdate = utils::check_date(utils::get_field_data($student_id, utils::get_fieldid('startdate')));
        $student_info->enddate = utils::check_date(utils::get_field_data($student_id, utils::get_fieldid('enddate')));
        $student_info->occupationaltraining = utils::get_field_data($student_id, utils::get_fieldid('occupationaltraining'));
        if (utils::get_field_data($student_id, utils::get_fieldid('goal1'))) {
            $student_info->goal1 = '&#9745'; }
        else {
            $student_info->goal1 = '&#9744'; }
        if (utils::get_field_data($student_id, utils::get_fieldid('goal2'))) {
            $student_info->goal2 = '&#9745'; }
        else {
            $student_info->goal2 = '&#9744'; }
        if (utils::get_field_data($student_id, utils::get_fieldid('goal3'))) {
            $student_info->goal3 = '&#9745'; }
        else {
            $student_info->goal3 = '&#9744'; }
        $goal_other = trim(utils::get_field_data($student_id, utils::get_fieldid('goal4')));
        if (strlen($goal_other) > 0) {
            $student_info->goal4 = '&#9745';
            $student_info->goal4_data = $goal_other; }
        else {
            $student_info->goal4 = '&#9744';
            $student_info->goal4_data = ''; }
        $student_info->anticipateddate = utils::check_date(utils::get_field_data($student_id, utils::get_fieldid('anticipateddate')));
        if (utils::get_field_data($student_id, utils::get_fieldid('eval1'))) {
            $student_info->eval1 = '&#9745'; }
        else {
            $student_info->eva1 = '&#9744'; }
        if (utils::get_field_data($student_id, utils::get_fieldid('eval1'))) {
            $student_info->eval2 = '&#9745'; }
        else {
            $student_info->eval2 = '&#9744'; }
        if (utils::get_field_data($student_id, utils::get_fieldid('eval3'))) {
            $student_info->eval3 = '&#9745'; }
        else {
            $student_info->eval3 = '&#9744'; }
        if (utils::get_field_data($student_id, utils::get_fieldid('eval4'))) {
            $student_info->eval4 = '&#9745'; }
        else {
            $student_info->eval4 = '&#9744'; }
        $evaluation_other = trim(utils::get_field_data($student_id, utils::get_fieldid('eval5')));
        if (strlen($evaluation_other) > 0) {
            $student_info->eval5 = '&#9745';
            $student_info->eval5_data = $evaluation_other; }
        else {
            $student_info->eval5 = '&#9744';
            $student_info->eval5_data = ''; }
        $student_info->mannerofmeeting = utils::get_field_data($student_id, utils::get_fieldid('mannerofmeeting'));
        $student_info->printdate = date("m-d-Y");
        // Student courses
        $summer = "";
        if ($school_term == 'Summer') {
            $summer = 'summer_'; }
        $week_day[1] = 'monday';
        $week_day[2] = 'tuesday';
        $week_day[3] = 'wednesday';
        $week_day[4] = 'thursday';
        $week_day[5] = 'friday';
        $number_of_sessions = get_config('mod_istudy', 'number_of_sessions');
        // Check for a daily schedule. If 'No', student schedules are different each day of the week.
        // If 'Yes', Mondays schedule is applied to each day of the week (Monday-Friday).
        if ($school_term == 'Fall' OR $school_term == 'Spring') {
            $dailyschedule = get_config('mod_istudy', 'dailyschedule');
        }
        else {
            $dailyschedule = get_config('mod_istudy', 'summer_dailyschedule');
        }
        if ($dailyschedule == 'No') {
            // Monday-Friday courses
            for ($i = 1; $i <= 5; $i++) {
                for ($j = 1; $j <= $number_of_sessions; $j++) {
                    $student_info->{$week_day[$i] . 'session' . $j . 'time'} = get_config('mod_istudy', $summer . $week_day[$i] . 'session' . $j);
                    $student_info->{$week_day[$i] . 'session' . $j . 'course'} =  utils::get_field_data($student_id, utils::get_fieldid($school_term . $week_day[$i] . 'session' . $j));
                    $student_info->{$week_day[$i] . 'session' . $j . 'credits'} = utils::get_field_data($student_id, utils::get_fieldid($school_term . $week_day[$i] . 'creditssession' . $j));
                }
            }
        }
        else {
            // Monday-Friday using a traditional daily schedule - courses are taken from Monday and used for each day of the week.
            for ($i = 1; $i <= 5; $i++) {
                // Use Monday's schedule for each day of the week.
                $this_day = 1;
                for ($j = 1; $j <= $number_of_sessions; $j++) {
                    $student_info->{$week_day[$i] . 'session' . $j . 'time'} = get_config('mod_istudy', $summer . $week_day[$this_day] . 'session' . $j);
                    $student_info->{$week_day[$i] . 'session' . $j . 'course'} =  utils::get_field_data($student_id, utils::get_fieldid($school_term . $week_day[$this_day] . 'session' . $j));
                    $student_info->{$week_day[$i] . 'session' . $j . 'credits'} = utils::get_field_data($student_id, utils::get_fieldid($school_term . $week_day[$this_day] . 'creditssession' . $j));
                }
            }
        }
        return $student_info;
    }

    // Create the student card for his or her weekly schedule.
    public static function get_studentcardlayout($student_info) {
        $week_day[1] = 'Monday';
        $week_day[2] = 'Tuesday';
        $week_day[3] = 'Wednesday';
        $week_day[4] = 'Thursday';
        $week_day[5] = 'Friday';
        $number_of_sessions = get_config('mod_istudy', 'number_of_sessions');
        $school_term = $student_info->term;
        $student_schedule = '<link rel="stylesheet" type="text/css" href="styles-student_card.css"><html><head><title>Student Schedule</title></head><body><p>' .
            get_config('mod_istudy', 'schoolname') . '<br>';
        $student_schedule = $student_schedule . get_config('mod_istudy', 'schooladdress') . '<br>';
        $student_schedule = $student_schedule . get_config('mod_istudy', 'schoolcity') . ', ' .
            get_config('mod_istudy', 'schoolstate') . ' ' .
            get_config('mod_istudy', 'schoolzip') . '</p>';
        $student_schedule = $student_schedule . '<h1>Student Schedule - '. $student_info->term . ' ' . $student_info->year . '</h1>';
        $student_schedule = $student_schedule .
            '<table class="table1"><tr><b><u><th>Student Name</th><th>ID #</th><th>Grade</th><th>Advisor</th></u></b></tr>' .
            '<tr><col width="30%"><col width="20%"><col width="2%"><col width="30%"><td>' . $student_info->lastname . ', ' . $student_info->firstname . '</td><td>' . $student_info->schoolid .
            '</td><td>' . $student_info->gradelevel . '</td><td>' . utils::put_comma_in_name($student_info->advisor) . '</td></tr></table><hr>';
        // Check for a daily schedule. If 'No', student schedules are different each day of the week.
        // If 'Yes', Mondays schedule is applied to each day of the week (Monday-Friday).
        if ($school_term == 'Fall' OR $school_term == 'Spring') {
            $dailyschedule = get_config('mod_istudy', 'dailyschedule');
        }
        else {
            $dailyschedule = get_config('mod_istudy', 'summer_dailyschedule');
        }
        if ($dailyschedule == 'No') {
            for ($i = 1; $i <= 5; $i++) {
                $student_schedule = $student_schedule . '<h4><center>' . $week_day[$i] . '</center></h4>';
                $classes_found = 0;
                for ($this_session = 1; $this_session <= $number_of_sessions; $this_session++) {
                    $mysessions[$this_session] = $student_info->{strtolower($week_day[$i]).'session' . $this_session . 'course'};
                    if ($mysessions[$this_session] == 'None') {
                        $classes_found = $classes_found + 1;
                    }
                }
                if ($classes_found == $number_of_sessions) {
                    $student_schedule = $student_schedule . '<h2>None</h2>';
                }
                else {
                    $student_schedule = $student_schedule .
                        '<table class="table2">' .
                        '<tr><col width="3%"><col width="22%"><col width="15%"><col width="25%"><col width="5%"><col width="20%"><col width="10%">' .
                        '<b><th><center>Pd</center></th><th><center>Time</center></th><th><center><b>Crse#</b>-Sec#</center></th><th><center>Course</center></th><th><center>Credits</center></th><th><center>Teacher</center></th><th><center>Room #</center></th></b></tr>';
                    for ($count = 1; $count <= $number_of_sessions; $count++) {
                        $mysession = $mysessions[$count];
                        if ($mysession <> 'None'){
                            $mycourse = new stdClass();
                            $mycourse = utils::get_mycourse($mysession);
                            $mycourse->time = $student_info -> {strtolower($week_day[$i]) . 'session' . $count . 'time'};
                            $mycourse->credits = $student_info -> {strtolower($week_day[$i]) . 'session' . $count . 'credits'};
                            $teacherid = teacher_info::get_whichadvisorid($mycourse->teacher);
                            $temp_section_number = utils::get_section_number($teacherid, $i, $count);
                            if ($temp_section_number == '') {
                                $section_number = '';
                            }
                            else {
                                $section_number = ' - ' .  $temp_section_number;
                            }

                            $student_schedule = $student_schedule .
                                '<tr><td><center>' . $count . '</center></td><td><center>' . $mycourse->time . '</center></td>' .
                                '<td><center><b>' . $mycourse->coursenumber . '</b>' . $section_number . '</center></td>' .
                                '<td><center>' . $mycourse->subject . '</center></td><td><center>' . $mycourse->credits . '</center></td>' .
                                '<td><center>' . utils::put_comma_in_name($mycourse->teacher) . '</center></td><td><center>' . $mycourse->room . '</center></td></tr>';
                        }
                    }
                    $student_schedule = $student_schedule . '</table></body></html>';
                }
            }
        }
        else {
            $i = 1;
            $student_schedule = $student_schedule . '<h4><center>Monday - Friday</center></h4>';
            $classes_found = 0;
            for ($this_session = 1; $this_session <= $number_of_sessions; $this_session++) {
                $mysessions[$this_session] = $student_info->{strtolower($week_day[$i]).'session' . $this_session . 'course'};
                if ($mysessions[$this_session] == 'None') {
                    $classes_found = $classes_found + 1;
                }
            }
            if ($classes_found == $number_of_sessions) {
                $student_schedule = $student_schedule . '<h2>None</h2>';
            }
            else {
                $student_schedule = $student_schedule .
                    '<table class="table2">' .
                    '<tr><col width="3%"><col width="22%"><col width="15%"><col width="25%"><col width="5%"><col width="20%"><col width="10%">' .
                    '<b><th><center>Pd</center></th><th><center>Time</center></th><th><center><b>Crse#</b>-Sec#</center></th><th><center>Course</center></th><th><center>Credits</center></th><th><center>Teacher</center></th><th><center>Room #</center></th></b></tr>';
                for ($count = 1; $count <= $number_of_sessions; $count++) {
                    $mysession = $mysessions[$count];
                    if ($mysession <> 'None'){
                        $mycourse = new stdClass();
                        $mycourse = utils::get_mycourse($mysession);
                        $mycourse->time = $student_info -> {strtolower($week_day[$i]) . 'session' . $count . 'time'};
                        $mycourse->credits = $student_info -> {strtolower($week_day[$i]) . 'session' . $count . 'credits'};
                        $teacherid = teacher_info::get_whichadvisorid($mycourse->teacher);
                        $temp_section_number = utils::get_section_number($teacherid, $i, $count);
                        if ($temp_section_number == '') {
                            $section_number = '';
                        }
                        else {
                            $section_number = ' - ' . $temp_section_number;
                        }

                        $student_schedule = $student_schedule .
                            '<tr><td><center>' . $count . '</center></td><td><center>' . $mycourse->time . '</center></td>' .
                            '<td><center><b>' . $mycourse->coursenumber . '</b>' . $section_number . '</center></td>' .
                            '<td><center>' . $mycourse->subject . '</center></td><td><center>' . $mycourse->credits . '</center></td>' .
                            '<td><center>' . utils::put_comma_in_name($mycourse->teacher) . '</center></td><td><center>' . $mycourse->room . '</center></td></tr>';
                    }
                }
                $student_schedule = $student_schedule . '</table></body>';
            }
        }
        $student_schedule = $student_schedule . '<hr><h3>Date Printed: ' . $student_info->printdate . '</h3></body></html>';
        return $student_schedule;
    }

    // Get the student contract ready for printing.
    public static function get_studentcontract($student_info) {
        // Variables for contract
        $contract_objectives_title = utils::contract_extra_symbols(get_config('mod_istudyschedule', 'objectives_title'));
        $contract_objectives = utils::contract_extra_symbols(get_config('mod_istudyschedule', 'objectives'));
        $contract_additional_classes = utils::contract_extra_symbols(get_config('mod_istudyschedule', 'additional_classes'));
        $contract_missed_assignments = utils::contract_extra_symbols(get_config('mod_istudyschedule', 'missed_assignments'));
        $contract_agreement = utils::contract_extra_symbols(get_config('mod_istudyschedule', 'agreement'));
        // Contract Title
        $student_contract = '<link rel="stylesheet" type="text/css" href="styles-student_contract.css"><html><head><title>Student Agreement</title></head><body><h1>' .
            get_config('mod_istudy', 'schoolname') . ' - Student Agreement</h1><h2>';
        $student_contract = $student_contract . get_config('mod_istudy', 'schooladdress') . '<br>';
        $student_contract = $student_contract . get_config('mod_istudy', 'schoolcity') . ', '
            . get_config('mod_istudy', 'schoolstate') . ' '
            . get_config('mod_istudy', 'schoolzip') . '<br>'
            . get_config('mod_istudy', 'schoolphone') . '•FAX'
            . get_config('mod_istudy', 'schoolfax') . '</h2><br>';
        // Student Demographics
        $student_contract = $student_contract . '<table class="table5">' .
            '<tr><td width="12%" class="mycell5">Last Name: </td><td width="23%" class="mycell6">' . $student_info->lastname . '</td>' .
            '<td width="12%" class="mycell5"> First Name: </td><td width="18%" class="mycell6">' . $student_info->firstname . '</td>' .
            '<td width="12%" class="mycell5"> ID #: </td><td width="13%" class="mycell6">' . $student_info->schoolid . '</td>' .
            '<td width="6%"class="mycell5"> Grade: </td><td width="4%" class="mycell6">' . $student_info->gradelevel . '</td></tr>' .

            '<tr><td class="mycell4">Other L.N.:</td><td class="mycell6">' . $student_info->otherlastname . '</td>' .
            '<td class="mycell4">Middle Name:</td><td class="mycell6">' . $student_info->middlename . '</td>' .
            '<td class="mycell4">Birth Date:</td><td class="mycell6">' . $student_info->birthdate . '</td>' .
            '<td class="mycell4">Age:</td><td class="mycell6">' . $student_info->age . '</td></tr>' .

            '<tr><td class="mycell5">Address:</td><td class="mycell6">' . $student_info->address . '</td>' .
            '<td class="mycell5">City:</td><td class="mycell6">' . $student_info->city . '</td>' .
            '<td class="mycell5">Home Phone:</td><td class="mycell6" colspan="3">' . $student_info->homephone . '</td></tr>' .

            '<tr><td class="mycell4">Prev School:</td><td class="mycell6">' . $student_info->previousschool . '</td>' .
            '<td class="mycell4">Day/Time:</td><td class="mycell6">' . $student_info->daytimephone . '</td>' .
            '<td class="mycell4">Student Cell:</td><td class="mycell6" colspan="3">' . $student_info->studentcell . '</td></tr>' .

            '<tr><td class="mycell5">Advisor:</td><td class="mycell6">' . utils::put_comma_in_name($student_info->advisor) . '</td>' .
            '<td class="mycell5">Start Date:</td><td class="mycell6">' . $student_info->startdate . '</td>' .
            '<td class="mycell5">End Date:</td><td class="mycell6" colspan="3">' . $student_info->enddate . '</td></tr>' .

            '<td class="mycell4">Room/Ext:</td><td class="mycell6">' . $student_info->roomextension . '</td>' .
            '<td class="mycell4">Email:</td><td class="mycell6" colspan="5">' . $student_info->email . '</td></tr></table>';
        // Student Courses
        $week_day[1] = 'monday';
        $week_day[2] = 'tuesday';
        $week_day[3] = 'wednesday';
        $week_day[4] = 'thursday';
        $week_day[5] = 'friday';
        $number_of_sessions = get_config('mod_istudy', 'number_of_sessions');
        $count = 0;
        $my_classes[1] = '';
        // Days of the week $i- Monday-Friday (1-5)
        for ($i = 1; $i <= 5; $i++) {
            for ($this_session = 1; $this_session <= $number_of_sessions; $this_session++) {
                $mysessions[$this_session] = $student_info->{$week_day[$i] . 'session' . $this_session . 'course'};
            }
            // Number of session $j
            for ($j = 1; $j <= $number_of_sessions; $j++) {
                $mysession = $mysessions[$j];
                $repeated_course = false;
                if ($count > 1) {
                    for ($r = 1; $r <= $count; $r++) {
                        if ($my_classes[$r] == $mysession) {
                            $repeated_course = true;
                        }
                    }
                }
                if ($mysession <> 'None' AND $repeated_course <> true){
                    $count = $count + 1;
                    $my_classes[$count] = $mysession;
                    $mycourse[$count] = new stdClass();
                    $mycourse[$count] = utils::get_mycourse($mysession);
                    $mycourse[$count]->credits = $student_info -> {$week_day[$i] . 'session' . $j . 'credits'};
                    $teacherid = teacher_info::get_whichadvisorid($mycourse[$count]->teacher);
                    $temp_section_number = utils::get_section_number($teacherid, $i, $j);
                    if ($temp_section_number == '') {
                         $mycourse[$count]->section_number = '';
                    }
                    else {
                         $mycourse[$count]->section_number = ' - ' . $temp_section_number;
                    }
                }
            }
        }
        // Add the courses to the student agreement.
        $student_contract = $student_contract . '<br><h3>Course List</h3><table class="table2">' .
            '<tr><col width="30%"><col width="15%"><col width="40%"><col width="15%"><th>Teacher</th><th><center><b>Course#</b> - Sec#</center></th><th>Courses*</th><th>Credits</th></tr>';
        if ($count > 0){
            // Sort the courses alphabetically
            $val = new stdClass();
            for ($i=1; $i<=$count; $i++){
                $val = $mycourse[$i];
                $j = $i-1;
                while($j>=1 && $mycourse[$j]->subject > $val->subject){
                    $mycourse[$j+1] = $mycourse[$j];
                    $j--;
                }
                $mycourse[$j+1] = $val;
            }
            // Add the sorted courses to the student agreement.
            for ($i = 1; $i<= $count; $i++) {
                $student_contract = $student_contract .
                    '<tr><td>' . utils::put_comma_in_name($mycourse[$i]->teacher) . '</td><td><center><b>' . $mycourse[$i]->coursenumber .
                    '</b>' . $mycourse[$i]->section_number . '</center></td><td>' .
                    $mycourse[$i]->subject . '</td><td>' . $mycourse[$i]->credits . '</td></tr>';
            }
        }
        // The end of the course list.
        $student_contract = $student_contract . '</table>';
        $student_contract = $student_contract . '<h4>*Maximum credits not to exceed 50 per semester.</h4><hr>';
        $temp_goal1 = utils::custom_trim(get_config('mod_istudy', 'agreementgoal1'));
        if (strlen($temp_goal1) == 0) {
            $temp_goal1 = "Furman High School Diploma (230 credits)";
        }
        $temp_goal2 = utils::custom_trim(get_config('mod_istudy', 'agreementgoal2'));
        if (strlen($temp_goal2) == 0) {
            $temp_goal2 = "Madera Adult School (180 credits)";
        }
        $temp_goal3 = utils::custom_trim(get_config('mod_istudy', 'agreementgoal3'));
        if (strlen($temp_goal3) == 0) {
            $temp_goal3 = "Transfer to MSH/MSHS/MTHS";
        }
        $student_contract = $student_contract . '<table class="table3">' .
            '<tr><td><b>Goal: </b></td><td>'. $student_info->goal1 .' ' . $temp_goal1 . '</td>' .
            '<td>'. $student_info->goal2 . ' ' . $temp_goal2 . '</td></tr>' .
            '<tr><td></td><td>' . $student_info->goal3 . ' ' . $temp_goal3 . '</td><td></td><td></td></tr></table>';
        $student_contract = $student_contract . '<table class="table3"><tr><col width="6%"><col width="8%"><col width="47%">' .
            '<col width="24%"><col width="15%"><td></td><td>' . $student_info->goal4 . ' Other:</td><td class="mycell2">' .
            '&nbsp&nbsp' . $student_info->goal4_data . '<td class="mycell1">Anticipated Date: </td><td class="mycell2">' .
            $student_info->anticipateddate . '</td></tr></table>';
        $temp_eval1 = utils::custom_trim(get_config('mod_istudy', 'agreementeval1'));
        if (strlen($temp_eval1) == 0) {
            $temp_eval1 = "Tests";
        }
        $temp_eval2 = utils::custom_trim(get_config('mod_istudy', 'agreementeval2'));
        if (strlen($temp_eval2) == 0) {
            $temp_eval2 = "Written Work";
        }
        $temp_eval3 = utils::custom_trim(get_config('mod_istudy', 'agreementeval3'));
        if (strlen($temp_eval3) == 0) {
            $temp_eval3 = "Demonstration";
        }
        $temp_eval4 = utils::custom_trim(get_config('mod_istudy', 'agreementeval4'));
        if (strlen($temp_eval4) == 0) {
            $temp_eval4 = "Oral Presentation";
        }
        $student_contract = $student_contract . '<table class="table3"><tr><col width="20%"><col width="8%"><col width="12%"><col width="20%"><col width="20%"><col width="20%">' .
            '<td><b>Means of Evaluation: </b></td>' .
            '<td>' . $student_info->eval1 . ' ' . $temp_eval1 . '</td><td></td>' .
            '<td>' . $student_info->eval2 . ' ' . $temp_eval2 . '</td>' .
            '<td>' . $student_info->eval3 . ' ' . $temp_eval3 . '</td>' .
            '<td>' . $student_info->eval4 . ' ' . $temp_eval4 . '</td><td></td></tr>' .
            '<tr><td></td><td>' . $student_info->eval5 . ' Other:</td>' .
            '<td class="mycell2" colspan="5">' . $student_info->eval5_data . '</tr></table>';
        $student_contract = $student_contract . '<table class="table3">' .
            '<tr><col width="23%"><col width="77%"><td class="mycell1">Occupational Training (G2):</td><td class="mycell2">' .
            '&nbsp&nbsp' . $student_info->occupationaltraining . '</td></tr></table><hr>';
        $student_contract = $student_contract . '<p><b><u>' . $contract_objectives_title . '</u></b><br>' .
            $contract_objectives . '</p>' .
            '<p><b>' . $contract_additional_classes . '</b></p>';
        $student_contract = $student_contract . '<table><tr><col width="17%"><col width="83%"><td class="mycell1">Manner of Meeting: </td>' .
            '<td class="mycell2">' . '&nbsp&nbsp' . $student_info->mannerofmeeting . '</td></tr></table>';
        $student_contract = $student_contract . '<p>' . $contract_missed_assignments . '</p>' .
            '<p>' . $contract_agreement . '</p>';
        $student_contract = $student_contract . '<table>' .
            '<tr><col width="18%"><col width="40%"><col width="2%"><col width="40%">' .
            '<td></td><td></td><td></td><td>Other Assisting Persons</td></tr>' .
            '<td></td><td></td><td></td><td></td></tr>' .
            '<tr><td class="mycell1">Student</td><td class="mycell2">:</td><td></td><td class="mycell2"></td></tr>' .
            '<tr><td></td><td class="mycell1">Date</td><td></td><td class="mycell1">Date</td></tr>' .
            '<tr><td class="mycell1">Parent/Guardian/</td><td class="mycell2"></td><td></td><td class="mycell2"></td></tr>' .
            '<tr><td class="mycell1">Caregiver</td><td class="mycell1">Date</td><td></td><td class="mycell1">Date</td></tr>' .
            '<tr><td class="mycell1">Supervising Teacher</td><td class="mycell2"></td><td></td><td class="mycell2"></td></tr>' .
            '<tr><td></td><td class="mycell1">Date</td><td></td><td class="mycell1">Date</td></tr>' .
            '<tr><td class="mycell1">Counselor</td><td class="mycell2"></td><td></td><td class="mycell2"></td></tr>' .
            '<tr><td></td><td class="mycell1">Date</td><td></td><td class="mycell1">Date</td></tr>' .
            '<tr><td class="mycell1">Other</td><td class="mycell2"></td><td></td><td class="mycell2"></td></tr>' .
            '<tr><td></td><td class="mycell1">Date</td><td></td><td class="mycell1">Date</td></tr></table></body></html>';
        return $student_contract;
    }

    // Print the student agreement terms in English and Spanish.
    public static function enrollment_terms() {
        $number_of_terms = utils::contract_extra_symbols(get_config('mod_istudyschedule', 'number_of_terms'));
        $contract_term_english[1] = '<div>' . utils::contract_extra_symbols(get_config('mod_istudyschedule', 'term01_english')) . '</div>';
        $contract_term_english[2] = '<div>' . utils::contract_extra_symbols(get_config('mod_istudyschedule', 'term02_english')) . '</div>';
        $contract_term_english[3] = '<div>' . utils::contract_extra_symbols(get_config('mod_istudyschedule', 'term03_english')) . '</div>';
        $contract_term_english[4] = '<div>' . utils::contract_extra_symbols(get_config('mod_istudyschedule', 'term04_english')) . '</div>';
        $contract_term_english[5] = '<div>' . utils::contract_extra_symbols(get_config('mod_istudyschedule', 'term05_english')) . '</div>';
        $contract_term_english[6] = '<div>' . utils::contract_extra_symbols(get_config('mod_istudyschedule', 'term06_english')) . '</div>';
        $contract_term_english[7] = '<div>' . utils::contract_extra_symbols(get_config('mod_istudyschedule', 'term07_english')) . '</div>';
        $contract_term_english[8] = '<div>' . utils::contract_extra_symbols(get_config('mod_istudyschedule', 'term08_english')) . '</div>';
        $contract_term_english[9] = '<div>' . utils::contract_extra_symbols(get_config('mod_istudyschedule', 'term09_english')) . '</div>';
        $contract_term_english[10] = '<div>' . utils::contract_extra_symbols(get_config('mod_istudyschedule', 'term10_english')) . '</div>';
        $contract_term_english[11] = '<div>' . utils::contract_extra_symbols(get_config('mod_istudyschedule', 'term11_english')) . '</div>';
        $contract_term_english[12] = '<div>' . utils::contract_extra_symbols(get_config('mod_istudyschedule', 'term12_english')) . '</div>';
        $contract_term_english[13] = '<div>' . utils::contract_extra_symbols(get_config('mod_istudyschedule', 'term13_english')) . '</div>';
        $contract_term_english[14] = '<div>' . utils::contract_extra_symbols(get_config('mod_istudyschedule', 'term14_english')) . '</div>';
        $contract_term_english[15] = '<div>' . utils::contract_extra_symbols(get_config('mod_istudyschedule', 'term15_english')) . '</div>';
        $contract_term_spanish[1] = '<div>' . utils::contract_extra_symbols(get_config('mod_istudyschedule', 'term01_spanish')) . '</div>';
        $contract_term_spanish[2] = '<div>' . utils::contract_extra_symbols(get_config('mod_istudyschedule', 'term02_spanish')) . '</div>';
        $contract_term_spanish[3] = '<div>' . utils::contract_extra_symbols(get_config('mod_istudyschedule', 'term03_spanish')) . '</div>';
        $contract_term_spanish[4] = '<div>' . utils::contract_extra_symbols(get_config('mod_istudyschedule', 'term04_spanish')) . '</div>';
        $contract_term_spanish[5] = '<div>' . utils::contract_extra_symbols(get_config('mod_istudyschedule', 'term05_spanish')) . '</div>';
        $contract_term_spanish[6] = '<div>' . utils::contract_extra_symbols(get_config('mod_istudyschedule', 'term06_spanish')) . '</div>';
        $contract_term_spanish[7] = '<div>' . utils::contract_extra_symbols(get_config('mod_istudyschedule', 'term07_spanish')) . '</div>';
        $contract_term_spanish[8] = '<div>' . utils::contract_extra_symbols(get_config('mod_istudyschedule', 'term08_spanish')) . '</div>';
        $contract_term_spanish[9] = '<div>' . utils::contract_extra_symbols(get_config('mod_istudyschedule', 'term09_spanish')) . '</div>';
        $contract_term_spanish[10] = '<div>' . utils::contract_extra_symbols(get_config('mod_istudyschedule', 'term10_spanish')) . '</div>';
        $contract_term_spanish[11] = '<div>' . utils::contract_extra_symbols(get_config('mod_istudyschedule', 'term11_spanish')) . '</div>';
        $contract_term_spanish[12] = '<div>' . utils::contract_extra_symbols(get_config('mod_istudyschedule', 'term12_spanish')) . '</div>';
        $contract_term_spanish[13] = '<div>' . utils::contract_extra_symbols(get_config('mod_istudyschedule', 'term13_spanish')) . '</div>';
        $contract_term_spanish[14] = '<div>' . utils::contract_extra_symbols(get_config('mod_istudyschedule', 'term14_spanish')) . '</div>';
        $contract_term_spanish[15] = '<div>' . utils::contract_extra_symbols(get_config('mod_istudyschedule', 'term15_spanish')) . '</div>';
        // Initialize page
        $student_contract = '<link rel="stylesheet" type="text/css" href="styles-student_contract.css"><html><head><title>Terms of Enrollment</title></head><body>';
        // Page 2 - Terms of Enrollment - English
        $student_contract = $student_contract . '<h1>' .
            get_config('mod_istudy', 'schoolname') . '</h1>' .
            '<h3>Terms of Enrollment</h3>';
        $student_contract = $student_contract . '<table class="table4">';
        for ($i = 1; $i <= $number_of_terms; $i++) {
            $student_contract = $student_contract . '<tr><td valign="top" class="mycell3">' . $i . '.</td><td  class="mycell3-2">' . $contract_term_english[$i] . '</td></tr>';
        }
        $student_contract = $student_contract . '</table>';
        // Signatures at the bottom of the page
        $student_contract = $student_contract . '<table class="table4">';
        $student_contract = $student_contract . '<tr><td><div>__________________________________</div></td><td><div>__________________________________</div></td><td><div>___________________</div></td></tr>';
        $student_contract = $student_contract . '<tr><td><div><center>Parent/Guardian Signature</center></div></td><td><div><center>Student Signature</center></div></td><td><div><center>Date</center></div></td></tr>';
        $student_contract = $student_contract . '</table>';
        // Add a page break at the end of the first page.
        $student_contract = $student_contract . '<P style="page-break-before: always">';
        // Page 3 - Terms of Enrollment - Spanish
        $student_contract = $student_contract . '<h1>' .
            get_config('mod_istudy', 'schoolname') . '</h1>' .
            '<h3>Términos de Matriculación</h3>';
        $student_contract = $student_contract . '<table class="table4">';
        for ($i = 1; $i <= $number_of_terms; $i++) {
            $student_contract = $student_contract . '<tr><td valign="top" class="mycell3">' . $i . '.</td><td  class="mycell3-2">' . $contract_term_spanish[$i] . '</td></tr>';
        }
        $student_contract = $student_contract . '</table>';
        // Signatures at the bottom of the page
        $student_contract = $student_contract . '<table class="table4">';
        $student_contract = $student_contract . '<tr><td><div>__________________________________</div></td><td><div>__________________________________</div></td><td><div>___________________</div></td></tr>';
        $student_contract = $student_contract . '<tr><td><div><center>Firma - padre/guardian</center></div></td><td><div><center>Firma - estudiante</center></div></td><td><div><center>Fecha</center></div></td></tr>';
        $student_contract = $student_contract . '</table></body></html>';
        return $student_contract;
    }

    // Create and print attendance cards.
    public static function get_the_schedules($submission) {
        $school_term = $submission->schoolterm;
        $school_year = utils::get_schoolyear($school_term);
        $student_count = $submission->studentcount;
        $schedule_only = $submission->school_scheduleonly;
        $students_per_tab = 100;
        $page_count = 0;
        // Initialize student cards to empty.
        $student_schedules = '';
        $student_agreements = '';
        $student_info = new stdClass();
        // Create student schedules.
        for ($i = 1; $i <= $student_count; $i++) {
            $this_studentid = $submission->{'student_chosen' . $i};
            if ($this_studentid > 0) {
                $page_count = $page_count + 1;
                // If $page_count is more than $students_per_tab, print the tab and then start a new tab for the student schedules.
                if ($page_count > $students_per_tab) {
                    // Print the student schedules.
                    $schedule_print_newtab = "<script>
                            var newWindow = window.open('iStudy');
                            newWindow.document.write('$student_schedules');
                        </script>";
                    echo $schedule_print_newtab;
                    $page_count = 1;
                    $student_schedules = '';
                    // Student Agreements
                    if ($schedule_only == 2) {
                        // Print the student agreements.
                        $agreement_print_newtab = "<script>
                                var newWindow = window.open('iStudy');
                                newWindow.document.write('$student_agreements');
                            </script>";
                        echo $agreement_print_newtab;
                        $student_agreements = '';
                    }
                }
                // Get the student information.
                $student_info = student_info::get_singlestudent($this_studentid, $school_term, $school_year);
                // Add a page break before the next page - do not add break if it's the first page.
                if ($student_schedules <> '') {
                    $student_schedules = $student_schedules . '<P style="page-break-before: always">';
                    // Student Agreements
                    if ($schedule_only == 2) {
                        $student_agreements = $student_agreements . '<P style="page-break-before: always">';
                    }
                }
                $student_schedules = $student_schedules . student_info::get_studentcardlayout($student_info);
                // Student Agreements
                if ($schedule_only == 2) {
                    // Add the student contract
                    $student_agreements = $student_agreements . student_info::get_studentcontract($student_info);
                }
            }
        }
        // Print the student schedules that have not been printed yet - the final tab.
        if ($student_schedules <> '') {
            $schedule_print_newtab = "<script>
                    var newWindow = window.open('iStudy');
                    newWindow.document.write('$student_schedules');
                </script>";
            echo $schedule_print_newtab;
            // Student Agreements
            if ($schedule_only == 2) {
                // Print the student agreements that have not been printed yet - the final tab.
                $agreement_print_newtab = "<script>
                        var newWindow = window.open('iStudy');
                        newWindow.document.write('$student_agreements');
                    </script>";
                echo $agreement_print_newtab;
            }
        }
        if ($schedule_only == 2) {
            // Create the student agreement terms pages - English & Spanish.
            $agreement_terms = student_info::enrollment_terms();
            // Print the student agreement terms pages - English & Spanish.
            $print_terms_newtab = "<script>
                    var newWindow = window.open('iStudy');
                    newWindow.document.write('$agreement_terms');
                </script>";
            echo $print_terms_newtab;
        }
    }
}