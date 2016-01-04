<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version  of the License, or
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
 *
 * @package   block_upload_group
 * @copyright 2015 onwards University of Minnesota
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || exit();

require_once($CFG->libdir.'/formslib.php');

/**
 * Upload a CSV file for importing
 *
 * @package   block_upload_group
 * @copyright 2015 onwards University of Minnesota
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_upload_group_upload_form extends moodleform {

    /**
     * The form's definition.
     */
    public function definition() {

        $mform = & $this->_form;
        $data = $this->_customdata;

        $mform->addElement('hidden', 'action', 'upload_group_data');
        $mform->setType('action', PARAM_ALPHA);
        $mform->addElement('hidden', 'id', $data['id']);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('html', get_string('upload_help', 'block_upload_group'));
        $mform->addElement('header', 'upload_group_data', get_string('upload_group_data', 'block_upload_group'));

        // Add a file manager.
        $mform->addElement('filepicker', 'group_data', '');

        // Add the encoding option.
        $choices = core_text::get_encodings();
        $mform->addElement('select', 'encoding', get_string('encoding', 'block_upload_group'), $choices);
        $mform->setDefault('encoding', 'UTF-8');

        // Add the delimiter option.
        $choices = csv_import_reader::get_delimiter_list();
        $mform->addElement('select', 'delimiter', get_string('delimiter', 'block_upload_group'), $choices);

        if (array_key_exists('cfg', $choices)) {
            $mform->setDefault('delimiter_name', 'cfg');
        } else if (get_string('listsep', 'langconfig') == ';') {
            $mform->setDefault('delimiter_name', 'semicolon');
        } else {
            $mform->setDefault('delimiter_name', 'comma');
        }

        $choices = array('10' => 10, '20' => 20, '100' => 100, '1000' => 1000, '100000' => 100000);
        $mform->addElement('select', 'preview_num', get_string('row_preview_num', 'block_upload_group'), $choices);
        $mform->setType('preview_num', PARAM_INT);

        $this->add_action_buttons(true, get_string('submit_group_data', 'block_upload_group'));
    }
}

/**
 * Display list of roles to choose from
 *
 * @package   block_upload_group
 * @copyright 2015 onwards University of Minnesota
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_upload_group_confirm_form extends moodleform {

    /**
     * The form's definition.
     */
    public function definition () {

        global $DB;

        $mform = & $this->_form;
        $data = $this->_customdata;

        $mform->addElement('hidden', 'action', 'process_group_data');
        $mform->setType('action', PARAM_ALPHA);
        $mform->addElement('hidden', 'id', $data['id']);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'iid', $data['iid']);
        $mform->setType('iid', PARAM_INT);

        // Get the available bulk enrolment roles.
        $roleids = get_config('block_upload_group', 'allowed_uploadgroup_roles');
        $roles = $DB->get_records_select('role', "id in ($roleids)");
        $rolemenu = role_fix_names($roles, null, ROLENAME_ALIAS, true);

        // Set student role as default.
        $defaultroleid = 0;
        foreach ($roles as $role) {
            if ($role->shortname == 'student') {
                $defaultroleid = $role->id;
            }
        }

        // Add the role option.
        $mform->addElement('select', 'role', get_string('role_desc', 'block_upload_group'), $rolemenu);
        $mform->setType('role', PARAM_INT);
        $mform->setDefault('role', $defaultroleid);

        $this->add_action_buttons(true, get_string('process_group_data', 'block_upload_group'));
    }
}
