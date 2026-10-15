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
 * Class utils
 *
 * @package    mod_istudyschedule
 * @copyright  2024 Jesus Ramirez <jram1993@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class utils {

    /**
     * Make sure that the date is not zero.
     *
     * @param [object] $submission
     * @return string
     */
    public static function check_date($submission) {
        if ($submission > 0) {
            return date("m-d-Y", $submission);
        }
        else {
            return '';
        }
    }

    // This function will trim a string, but will return '' if the $value is not a string.
    public static function custom_trim(?string $value) {
        return trim($value ?? '');
    }

    // Return this school year for the fall(year_start) and the spring (year_end).
    public static function get_schoolyear($schoolterm) {
        // Find the beginning and the end of the current school year.
        $month_today = strval(date("m"));
        if ($month_today >= 7) {
            $year_start = strval(date("Y"));
        }
        else {
            $year_start = strval(date("Y")) - 1;
        }
        $year_end = $year_start + 1;
        if (($schoolterm == 'Fall') OR ($schoolterm == 'fall')) {
            return $year_start;
        }
        else {
            return $year_end;
        }
    }

    // Replace the dash (-) with a comma and space.
    public static function put_comma_in_name($myteacher) {
        $postion = strrpos($myteacher,"-");
        $name_with_dash = substr($myteacher, $postion);
        $name_with_comma = ', ' . substr($name_with_dash,1);
        $new_name = str_replace($name_with_dash,$name_with_comma,$myteacher);
        return $new_name;
    }

    // Return the course info - subject, teacher, section, and room number.
    public static function get_mycourse($mycourse) {
        $course_info = new \stdClass();
        $course_info->subject = strstr($mycourse, '/', true);
        $mycourse = strstr($mycourse, "/");
        $mycourse = substr($mycourse, 1);
        $course_info->teacher = strstr($mycourse, '/', true);
        $mycourse = strstr($mycourse, "/");
        $mycourse = substr($mycourse, 1);
        $course_info->coursenumber = utils::custom_trim($mycourse);
        $course_info->room = teacher_info::get_advisor_room($course_info->teacher);
        return $course_info;
    }

    // Return the course info - subject, teacher, section, and room number.
    public static function get_section_number($teacherid, $this_weekday, $this_session) {
        global $DB;
        $course_info = new stdClass();
        if ($DB->record_exists('istudycoursenum_sections', array ('teacher_id' => $teacherid, 'day_of_week' => $this_weekday, 'session' => $this_session))) {
            $sql = "SELECT section_number FROM {istudycoursenum_sections} WHERE teacher_id = " . $teacherid . " AND day_of_week = " . $this_weekday . " AND session = " .$this_session;
            $params = null;
            $strictness = IGNORE_MULTIPLE;
            $course_info = $DB->get_record_sql($sql, $params, $strictness);
            return $course_info->section_number;
        }
        else {
            return '';
        }
    }
    // Return the term chosen - 1=Fall, 2=Spring, 3=Summer.
    public static function get_term($term_choice) {
        $schoolterms[1] = 'Fall';
        $schoolterms[2] = 'Spring';
        $schoolterms[3] = 'Summer';
        return $schoolterms[$term_choice];
    }

    // Get the lastname search criteria.
    public static function get_lastnamesearch($lastnameinitial){
        if ($lastnameinitial < 27) {
            $lastname_criteria = chr($lastnameinitial + 96) . '%';
        }
        else {
            $lastname_criteria = '%';
        }
        return $lastname_criteria;
    }

    /**
     * Calculate the grade level from the graduation year.
     *
     * @param [integer] $grad_year
     * @return [integer] $my_grade_level
     */
    public static function get_gradelevel($grad_year) {
        $currentmonth = date('m');
        $currentyear = date('Y');
        if ($grad_year <= $currentyear) {
            $years_left = 0;
        }
        else if ($currentmonth >= 7) {
            $years_left = $grad_year - ($currentyear + 1);
        }
        else {
            $years_left = $grad_year - $currentyear;
        }
        $my_grade_level = 12 - $years_left;
        return $my_grade_level;
    }

    /**
     * Get the user_info_field id to use in searching user_info_data.
     *
     * @param [string] $item_name
     * @return [integer]
     */
    public static function get_fieldid($item_name) {
        global $DB;
        $item = $DB->get_record('user_info_field', ['shortname' => $item_name], 'id');
        return $item->id;
    }

    /**
     * Search user_info_data for student data using the student id and user_info_field id.
     *
     * @param [integer] $student_id
     * @param [integer] $itemid
     * @return [string]
     */
    public static function get_field_data($student_id, $itemid) {
        global $DB;
        if ($DB->record_exists('user_info_data', ['userid'=>$student_id, 'fieldid'=>$itemid])) {
            $search_results = $DB->get_record('user_info_data', ['userid'=>$student_id, 'fieldid'=>$itemid], 'data');
            return $search_results->data;
        }
        else {
            return '';
        }
    }

    // Contract Only - Remove extra characters from the string & replace.
    public static function contract_extra_symbols($snip) {
        $snip = utils::custom_trim($snip);
        $snip = str_replace("'", "’", $snip); // remove apostrophe
        $snip = str_replace("\t", "     ", $snip); // remove tabs
        $snip = str_replace("\n", "</div><div>", $snip); // remove new lines
        $snip = str_replace("\r", "</div><div>", $snip); // remove carriage returns
        return $snip;
    }

    /**
     * Remove extra characters from the string.
     *
     * @param [string] $snip
     * @return string
     */
    public static function remove_extra_symbols($snip) {
        $snip = utils::custom_trim($snip);
        $snip = str_replace("\t", '', $snip); // remove tabs
        $snip = str_replace("\r", '', $snip); // remove carriage returns
        return $snip;
    }
}