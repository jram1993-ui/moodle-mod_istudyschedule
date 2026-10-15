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
 * The main istudyschedule configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod_istudyschedule
 * @copyright  2024 Jesus Ramirez <jram1993@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * Module instance settings form
 *
 * @package    mod_istudyschedule
 * @copyright  2024 Jesus Ramirez <jram1993@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_istudyschedule_search_criteria_form extends moodleform
{
    public function definition()
	{
		$mform = $this->_form;
		$mform->addElement('html', '<table><tr><th colspan="14"><h3>Print Student Schedule & Student Agreement</h3>');
		$mform->addElement('html', '<h4>Refine Your Search</h4></th></tr>');
		$mform->addElement('html', '<link rel="stylesheet" type="text/css" href="styles-search_criteria_form.css">');

		// Choose the term that you are printing from.
		$defaultterm_set = get_config('mod_istudy','schoolterm');
		if ($defaultterm_set == 'Fall') { $defaultterm = 1; }
		else if ($defaultterm_set == 'Spring') { $defaultterm = 2; }
		else { $defaultterm = 3; };
		$options = array(
         1 => 'Fall',
         2 => 'Spring',
         3 => 'Summer',
		);

		$mform->addElement('html', '<tr><td class="mycell2">Choose the current school Term</td><td colspan="13">');
		$select = $mform->addElement('select', 'schoolterm', '', $options);
		// Set default to Fall.
		$select->setSelected($defaultterm);
		$mform->addElement('html', '</td></tr>');

		// Choose the Advisory Teacher.
		$advisors = mod_istudyschedule\teacher_info::get_advisors_in_order();
		$mform->addElement('html', '<tr><td class="mycell2">Choose All Advisors or a Specific Advisor</td><td colspan="13">');
		$select = $mform->addElement('select', 'advisor', '', $advisors);
		// Set default to Fall.
		$select->setSelected(0);
		$mform->addElement('html', '</td></tr>');

		// Choose to print student schedule only or all reports.
		$mform->addElement('html', '<tr><td class="mycell" valign="top">Print Student Schedule only or all reports</td><td class="mycell"  colspan="5">');
		$mform->addElement('radio', 'scheduleonly', '','Student Schedule', 1);
		$mform->addElement('html', '</td><td class="mycell"  colspan="8">');
		$mform->addElement('radio', 'scheduleonly', '','Student Schedule & Student Agreement', 2);
		$mform->addElement('html', '</td></tr>');

		$mform->setDefault('scheduleonly', 1);
		$mform->setType('scheduleonly', PARAM_INT);

		// Choose to print all students or print by last name.
		$mform->addElement('html', '<tr><td class="mycell" rowspan="3" valign="top">Choose All Students or by Last Name</td><td colspan="2">');

		$mform->addElement('radio', 'lastnamesearch', '','ALL', 27, ['class' => 'radiobuttons-ALL']);
		$mform->addElement('html', '</td><td colspan="11"></td></tr><tr><td>');
		$mform->addElement('radio', 'lastnamesearch', '','A', 1, ['class' => 'radiobuttons-lastname']);
		$mform->addElement('html', '</td><td>');
		$mform->addElement('radio', 'lastnamesearch', '','B', 2, ['class' => 'radiobuttons-lastname']);
		$mform->addElement('html', '</td><td>');
		$mform->addElement('radio', 'lastnamesearch', '','C', 3, ['class' => 'radiobuttons-lastname']);
		$mform->addElement('html', '</td><td>');
		$mform->addElement('radio', 'lastnamesearch', '','D', 4, ['class' => 'radiobuttons-lastname']);
		$mform->addElement('html', '</td><td>');
		$mform->addElement('radio', 'lastnamesearch', '','E', 5, ['class' => 'radiobuttons-lastname']);
		$mform->addElement('html', '</td><td>');
		$mform->addElement('radio', 'lastnamesearch', '','F', 6, ['class' => 'radiobuttons-lastname']);
		$mform->addElement('html', '</td><td>');
		$mform->addElement('radio', 'lastnamesearch', '','G', 7, ['class' => 'radiobuttons-lastname']);
		$mform->addElement('html', '</td><td>');
		$mform->addElement('radio', 'lastnamesearch', '','H', 8, ['class' => 'radiobuttons-lastname']);
		$mform->addElement('html', '</td><td>');
		$mform->addElement('radio', 'lastnamesearch', '','I', 9, ['class' => 'radiobuttons-lastname']);
		$mform->addElement('html', '</td><td>');
		$mform->addElement('radio', 'lastnamesearch', '','J', 10, ['class' => 'radiobuttons-lastname']);
		$mform->addElement('html', '</td><td>');
		$mform->addElement('radio', 'lastnamesearch', '','K', 11, ['class' => 'radiobuttons-lastname']);
		$mform->addElement('html', '</td><td>');
		$mform->addElement('radio', 'lastnamesearch', '','L', 12, ['class' => 'radiobuttons-lastname']);
		$mform->addElement('html', '</td><td>');
		$mform->addElement('radio', 'lastnamesearch', '','M', 13, ['class' => 'radiobuttons-lastname']);
		$mform->addElement('html', '</td></tr><tr><td>');
		$mform->addElement('radio', 'lastnamesearch', '','N', 14, ['class' => 'radiobuttons-lastname']);
		$mform->addElement('html', '</td><td>');
		$mform->addElement('radio', 'lastnamesearch', '','O', 15, ['class' => 'radiobuttons-lastname']);
		$mform->addElement('html', '</td><td>');
		$mform->addElement('radio', 'lastnamesearch', '','P', 16, ['class' => 'radiobuttons-lastname']);
		$mform->addElement('html', '</td><td>');
		$mform->addElement('radio', 'lastnamesearch', '','Q', 17, ['class' => 'radiobuttons-lastname']);
		$mform->addElement('html', '</td><td>');
		$mform->addElement('radio', 'lastnamesearch', '','R', 18, ['class' => 'radiobuttons-lastname']);
		$mform->addElement('html', '</td><td>');
		$mform->addElement('radio', 'lastnamesearch', '','S', 19, ['class' => 'radiobuttons-lastname']);
		$mform->addElement('html', '</td><td>');
		$mform->addElement('radio', 'lastnamesearch', '','T', 20, ['class' => 'radiobuttons-lastname']);
		$mform->addElement('html', '</td><td>');
		$mform->addElement('radio', 'lastnamesearch', '','U', 21, ['class' => 'radiobuttons-lastname']);
		$mform->addElement('html', '</td><td>');
		$mform->addElement('radio', 'lastnamesearch', '','V', 22, ['class' => 'radiobuttons-lastname']);
		$mform->addElement('html', '</td><td>');
		$mform->addElement('radio', 'lastnamesearch', '','W', 23, ['class' => 'radiobuttons-lastname']);
		$mform->addElement('html', '</td><td>');
		$mform->addElement('radio', 'lastnamesearch', '','X', 24, ['class' => 'radiobuttons-lastname']);
		$mform->addElement('html', '</td><td>');
		$mform->addElement('radio', 'lastnamesearch', '','Y', 25, ['class' => 'radiobuttons-lastname']);
		$mform->addElement('html', '</td><td>');
		$mform->addElement('radio', 'lastnamesearch', '','Z', 26, ['class' => 'radiobuttons-lastname']);
		$mform->addElement('html', '</td></tr>');

		$mform->setDefault('lastnamesearch', 27);
		$mform->setType('lastnamesearch', PARAM_INT);
		$mform->addElement('html', '</table>');

		$mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
		$mform->addElement('html', '<br><h3>  </h3>');

		// Add standard buttons, common to all modules.
		$mform->addElement('html', '<div class="buttonsetup">');
			$this->add_action_buttons(true, 'Submit');
		$mform->addElement('html', '</div>');
    }
}