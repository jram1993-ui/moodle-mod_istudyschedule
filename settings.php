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
 * istudyschedule module admin settings and defaults
 *
 * @package    mod
 * @subpackage istudyschedule
 * @copyright  2024 Jesus Ramirez <jram1993@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot.'/mod/istudyschedule/lib.php');

if ($ADMIN->fulltree) {

    // Page heading
	$settings->add(new admin_setting_heading('mod_istudyschedule/schoolinfoheading',
        get_string('schoolinfoheading', 'istudyschedule'), get_string('schoolinfoheading_details', 'istudyschedule')));

	// Student contract information
	$settings->add(new admin_setting_configtext_with_maxlength('mod_istudyschedule/objectives_title',
        get_string('objectives_title', 'istudyschedule'), get_string('objectives_title_details', 'istudyschedule'), get_string('objectives_title_default', 'istudyschedule'), PARAM_TEXT, 80, 80));
	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/objectives',
			get_string('objectives', 'istudyschedule'), get_string('objectives_details', 'istudyschedule'), get_string('objectives_default', 'istudyschedule'), PARAM_TEXT, 60, 4));
	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/additional_classes',
			get_string('additional_classes', 'istudyschedule'), get_string('additional_classes_details', 'istudyschedule'), get_string('additional_classes_default', 'istudyschedule'), PARAM_TEXT, 60, 4));
	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/missed_assignments',
			get_string('missed_assignments', 'istudyschedule'), get_string('missed_assignments_details', 'istudyschedule'), get_string('missed_assignments_default', 'istudyschedule'), PARAM_TEXT, 60, 4));
	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/agreement',
			get_string('agreement', 'istudyschedule'), get_string('agreement_details', 'istudyschedule'), get_string('agreement_default', 'istudyschedule'), PARAM_TEXT, 60, 4));

	// Contract terms - English
	$settings->add(new admin_setting_heading('mod_istudyschedule/terms_english',
        get_string('terms_english', 'istudyschedule'), get_string('terms_english_details', 'istudyschedule')));

	 // Number of Sessions - Paging options
     $options = array(
         5 => 5,
		 6 => 6,
		 7 => 7,
		 8 => 8,
		 9 => 9,
		 10 => 10,
		 11 => 11,
		 12 => 12,
		 13 => 13,
		 14 => 14,
		 15 => 15,
    );
	$settings->add(new admin_setting_configselect('mod_istudyschedule/number_of_terms',
        get_string('number_of_terms', 'istudyschedule'), get_string('number_of_terms_details', 'istudyschedule'), 12, $options));

	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/term01_english',
			get_string('term', 'istudyschedule') . '1', get_string('term_details', 'istudyschedule') . '1', get_string('term01_english_default', 'istudyschedule'), PARAM_TEXT, 60, 4));
	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/term02_english',
			get_string('term', 'istudyschedule') . '2', get_string('term_details', 'istudyschedule') . '2', get_string('term02_english_default', 'istudyschedule'), PARAM_TEXT, 60, 4));
	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/term03_english',
			get_string('term', 'istudyschedule') . '3', get_string('term_details', 'istudyschedule') . '3', get_string('term03_english_default', 'istudyschedule'), PARAM_TEXT, 60, 4));
	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/term04_english',
			get_string('term', 'istudyschedule') . '4', get_string('term_details', 'istudyschedule') . '4', get_string('term04_english_default', 'istudyschedule'), PARAM_TEXT, 60, 4));
	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/term05_english',
			get_string('term', 'istudyschedule') . '5', get_string('term_details', 'istudyschedule') . '5', get_string('term05_english_default', 'istudyschedule'), PARAM_TEXT, 60, 4));
	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/term06_english',
			get_string('term', 'istudyschedule') . '6', get_string('term_details', 'istudyschedule') . '6', get_string('term06_english_default', 'istudyschedule'), PARAM_TEXT, 60, 4));
	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/term07_english',
			get_string('term', 'istudyschedule') . '7', get_string('term_details', 'istudyschedule') . '7', get_string('term07_english_default', 'istudyschedule'), PARAM_TEXT, 60, 4));
	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/term08_english',
			get_string('term', 'istudyschedule') . '8', get_string('term_details', 'istudyschedule') . '8', get_string('term08_english_default', 'istudyschedule'), PARAM_TEXT, 60, 4));
	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/term09_english',
			get_string('term', 'istudyschedule') . '9', get_string('term_details', 'istudyschedule') . '9', get_string('term09_english_default', 'istudyschedule'), PARAM_TEXT, 60, 4));
	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/term10_english',
			get_string('term', 'istudyschedule') . '10', get_string('term_details', 'istudyschedule') . '10', get_string('term10_english_default', 'istudyschedule'), PARAM_TEXT, 60, 4));
	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/term11_english',
			get_string('term', 'istudyschedule') . '11', get_string('term_details', 'istudyschedule') . '11', get_string('term11_english_default', 'istudyschedule'), PARAM_TEXT, 60, 4));
	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/term12_english',
			get_string('term', 'istudyschedule') . '12', get_string('term_details', 'istudyschedule') . '12', get_string('term12_english_default', 'istudyschedule'), PARAM_TEXT, 60, 4));
	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/term13_english',
			get_string('term', 'istudyschedule') . '13', get_string('term_details', 'istudyschedule') . '13', get_string('term13_english_default', 'istudyschedule'), PARAM_TEXT, 60, 4));
	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/term14_english',
			get_string('term', 'istudyschedule') . '14', get_string('term_details', 'istudyschedule') . '14', get_string('term14_english_default', 'istudyschedule'), PARAM_TEXT, 60, 4));
	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/term15_english',
			get_string('term', 'istudyschedule') . '15', get_string('term_details', 'istudyschedule') . '15', get_string('term15_english_default', 'istudyschedule'), PARAM_TEXT, 60, 4));

	// Contract terms - Spanish
	$settings->add(new admin_setting_heading('mod_istudyschedule/terms_spanish',
        get_string('terms_spanish', 'istudyschedule'), get_string('terms_spanish_details', 'istudyschedule')));

	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/term01_spanish',
			get_string('term', 'istudyschedule') . '1', get_string('term_details', 'istudyschedule') . '1', get_string('term01_spanish_default', 'istudyschedule'), PARAM_TEXT, 60, 4));
	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/term02_spanish',
			get_string('term', 'istudyschedule') . '2', get_string('term_details', 'istudyschedule') . '2', get_string('term02_spanish_default', 'istudyschedule'), PARAM_TEXT, 60, 4));
	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/term03_spanish',
			get_string('term', 'istudyschedule') . '3', get_string('term_details', 'istudyschedule') . '3', get_string('term03_spanish_default', 'istudyschedule'), PARAM_TEXT, 60, 4));
	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/term04_spanish',
			get_string('term', 'istudyschedule') . '4', get_string('term_details', 'istudyschedule') . '4', get_string('term04_spanish_default', 'istudyschedule'), PARAM_TEXT, 60, 4));
	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/term05_spanish',
			get_string('term', 'istudyschedule') . '5', get_string('term_details', 'istudyschedule') . '5', get_string('term05_spanish_default', 'istudyschedule'), PARAM_TEXT, 60, 4));
	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/term06_spanish',
			get_string('term', 'istudyschedule') . '6', get_string('term_details', 'istudyschedule') . '6', get_string('term06_spanish_default', 'istudyschedule'), PARAM_TEXT, 60, 4));
	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/term07_spanish',
			get_string('term', 'istudyschedule') . '7', get_string('term_details', 'istudyschedule') . '7', get_string('term07_spanish_default', 'istudyschedule'), PARAM_TEXT, 60, 4));
	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/term08_spanish',
			get_string('term', 'istudyschedule') . '8', get_string('term_details', 'istudyschedule') . '8', get_string('term08_spanish_default', 'istudyschedule'), PARAM_TEXT, 60, 4));
	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/term09_spanish',
			get_string('term', 'istudyschedule') . '9', get_string('term_details', 'istudyschedule') . '9', get_string('term09_spanish_default', 'istudyschedule'), PARAM_TEXT, 60, 4));
	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/term10_spanish',
			get_string('term', 'istudyschedule') . '10', get_string('term_details', 'istudyschedule') . '10', get_string('term10_spanish_default', 'istudyschedule'), PARAM_TEXT, 60, 4));
	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/term11_spanish',
			get_string('term', 'istudyschedule') . '11', get_string('term_details', 'istudyschedule') . '11', get_string('term11_spanish_default', 'istudyschedule'), PARAM_TEXT, 60, 4));
	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/term12_spanish',
			get_string('term', 'istudyschedule') . '12', get_string('term_details', 'istudyschedule') . '12', get_string('term12_spanish_default', 'istudyschedule'), PARAM_TEXT, 60, 4));
	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/term13_spanish',
			get_string('term', 'istudyschedule') . '13', get_string('term_details', 'istudyschedule') . '13', get_string('term13_spanish_default', 'istudyschedule'), PARAM_TEXT, 60, 4));
	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/term14_spanish',
			get_string('term', 'istudyschedule') . '14', get_string('term_details', 'istudyschedule') . '14', get_string('term14_spanish_default', 'istudyschedule'), PARAM_TEXT, 60, 4));
	$settings->add(new admin_setting_configtextarea('mod_istudyschedule/term15_spanish',
			get_string('term', 'istudyschedule') . '15', get_string('term_details', 'istudyschedule') . '15', get_string('term15_spanish_default', 'istudyschedule'), PARAM_TEXT, 60, 4));
}