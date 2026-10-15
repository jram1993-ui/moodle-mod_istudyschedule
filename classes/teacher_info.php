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

/**
 * Class teacher_info
 *
 * @package    mod_istudyschedule
 * @copyright  2024 Jesus Ramirez <jram1993@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class teacher_info {

    /**
     * Get a single advisor.
     *
     * @param [integer] $chosen_advisor
     * @return [string]
     */
    public static function get_advisor($chosen_advisor) {
        global $DB;
        $sql = "SELECT * FROM {istudyteachers} ORDER By lastname ASC, firstname ASC";
        $params = null;
        $all_advisors = $DB->get_records_sql($sql, $params);
        $advisor = [];
        $count = 0;
        $advisor[$count] = 'All';
        // Create the list of advisors.
       foreach ($all_advisors as $this_advisor) {
            $count = $count + 1;
            $teacher_lastname = $this_advisor->lastname;
            $teacher_firstname = $this_advisor->firstname;
            $advisor[$count] = $teacher_lastname . '-' . $teacher_firstname;
        }
        // Return the list of advisors.
        return $advisor[$chosen_advisor];
    }

    // Get a list of all advisors in numerical order (0, 1, 2, 3, etc.).
    public static function get_advisors_in_order() {
        global $DB;
        $sql = "SELECT * FROM {istudyteachers} ORDER By lastname ASC, firstname ASC";
        $params = null;
        $all_advisors = $DB->get_records_sql($sql, $params);
        $advisor = [];
        $count = 0;
        $advisor[$count] = 'All';
        // Create the list of advisors.
       foreach ($all_advisors as $this_advisor) {
            $count = $count + 1;
            $teacher_lastname = $this_advisor->lastname;
            $teacher_firstname = $this_advisor->firstname;
            $advisor[$count] = $teacher_lastname . '-' . $teacher_firstname;
        }
        // Return the list of advisors.
        return $advisor;
    }

    /**
     * Gets the moodle id for the teacher.
     *
     */
    public static function get_whichadvisorid($myteacher) {
        global $DB;
        // Separate the first name and last name that are joined with a dash (-).
        $teacher_lastname = strstr($myteacher, '-', true);
        $myteacher_temp = strstr($myteacher, '-');
        $teacher_firstname = substr($myteacher_temp, 1);
        $this_teacher = new stdClass();
        $sql = "SELECT * FROM {istudyteachers} WHERE lastname = '" .  $teacher_lastname . "' AND firstname = '" .  $teacher_firstname . "'";
        $params = null;
        $this_teacher = $DB->get_record_sql($sql, $params);
        return $this_teacher->teacherid;
    }

    /**
     * Gets the advisor ($myteacher) room number.
     *
     * @param [type] $myteacher
     * @return void
     */
    public static function get_advisor_room($myteacher) {
        global $DB;
        // Separate the first name and last name that are joined with a dash (-).
        $teacher_lastname = strstr($myteacher, '-', true);
        $myteacher_temp = strstr($myteacher, '-');
        $teacher_firstname = substr($myteacher_temp, 1);
        $this_teacher = new stdClass();
        $sql = "SELECT * FROM {istudyteachers} WHERE lastname = '" .  $teacher_lastname . "' AND firstname = '" .  $teacher_firstname . "'";
        $params = null;
        $this_teacher = $DB->get_record_sql($sql, $params);
        return $this_teacher->room;
    }

    /**
     * Gets the advisor ($myteacher) phone extension number.
     *
     * @param [type] $myteacher
     * @return void
     */
    public static function get_advisor_extension($myteacher) {
        global $DB;
        // Separate the first name and last name that are joined with a dash (-).
        $teacher_lastname = strstr($myteacher, '-', true);
        $myteacher_temp = strstr($myteacher, '-');
        $teacher_firstname = substr($myteacher_temp, 1);
        $this_teacher = new stdClass();
        $sql = "SELECT * FROM {istudyteachers} WHERE lastname = '" .  $teacher_lastname . "' AND firstname = '" .  $teacher_firstname . "'";
        $params = null;
        $this_teacher = $DB->get_record_sql($sql, $params);
        return $this_teacher->extension;
    }

    // Get a list of the student using the search criteria.
    public static function get_studentlist($schoolterm, $advisorlist, $lastnamesearch) {
        global $DB;
        $table = '{' . 'user' . '}';
        $select = " lastname LIKE " . "'" . $lastnamesearch . "'";
        $params = null;
        $sort =" ORDER BY lastname ASC, firstname ASC";
        $fields='id, firstname, lastname, email, deleted, suspended';
        $sql = "SELECT " . $fields . " FROM " . $table . " WHERE " . $select . $sort;
        $mystudent_lists = $DB->get_records_sql($sql, $params, 0, 0);
        $count = 0;
        $students_were_found = false;
        foreach ($mystudent_lists as $mystudent_list) {
            $record_found = true;
            $student_id = $mystudent_list->id;
            // Check for admin user, guest user, and anon user. If found, skip.
            if (($mystudent_list->firstname == 'Admin') OR (substr($mystudent_list->firstname, 0, 4) == 'anon')
                OR (substr($mystudent_list->firstname, 0, 5) == 'Guest') OR ($mystudent_list->deleted) OR ($mystudent_list->suspended)){
                $record_found = false;
            }
            // Get the year and term according to the search criteria.
            if ($record_found == true) {
                $whichuser_status = utils::get_field_data($student_id, utils::get_fieldid('whichteacher'));
                $myadvisor = utils::get_field_data($student_id, utils::get_fieldid('advisor'));
                // Check to see if user is an advisor or parent.  If so, exclude user.
                if ($whichuser_status <> 'Student') {
                        $record_found = false;
                    }
                // Check to see if the all adivsors or single advisor chosen match up.
                else if ($advisorlist <> 'All' AND $advisorlist <> $myadvisor) {
                    $record_found = false;
                }
            }
            if ($record_found == true) {
                $students_were_found = true;
                $count = $count +1;
                $students_found[$count] = new stdClass();
                // Student id, last name, & first name
                $students_found[$count]->id = $student_id;
                $students_found[$count]->schoolid = utils::get_field_data($student_id, utils::get_fieldid('schoolid'));
                $students_found[$count]->email = $mystudent_list->email;
                $students_found[$count]->lastname = $mystudent_list->lastname;
                $students_found[$count]->firstname = $mystudent_list->firstname;
                // Student grade level
                $my_gradelevel = utils::get_gradelevel(utils::get_field_data($student_id, utils::get_fieldid('graduationyear')));
                $students_found[$count]->gradelevel = $my_gradelevel;
                // Student's advisor
                $my_advisor = utils::get_field_data($student_id, utils::get_fieldid('advisor'));
                $students_found[$count]->advisor = $my_advisor;
                // Student's date of birth
                $students_found[$count]->birthdate = utils::get_field_data($student_id, utils::get_fieldid('dob'));
                // Student's grade point average
                $students_found[$count]->gpa = utils::get_field_data($student_id, utils::get_fieldid('gpa'));
                // Student's enrollment date
                $students_found[$count]->startdate = utils::get_field_data($student_id, utils::get_fieldid('startdate'));
            }
        }
        if ($students_were_found == false){
            return NULL; }
        else {
            return $students_found;
        }
    }
}