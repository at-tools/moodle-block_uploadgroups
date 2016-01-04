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
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v or later
 */

require(realpath(dirname(dirname(dirname($_SERVER["SCRIPT_FILENAME"])))).'/config.php');
require_once($CFG->dirroot.'/blocks/upload_group/forms.php');
require_once($CFG->dirroot.'/blocks/upload_group/lib.php');
require_once($CFG->libdir.'/csvlib.class.php');


$courseid  = required_param('id', PARAM_INT);
$action     = optional_param('action', null, PARAM_TEXT);

// Get the course record.
if (! ($course = $DB->get_record('course', array('id' => $courseid)))) {
    print_error('invalidcourseid', 'error');
}

require_login($course);

$context = context_course::instance($course->id);
require_capability('moodle/course:managegroups', $context);

$returnurl = new moodle_url('/blocks/upload_group/index.php', array('id' => $course->id));

$PAGE->set_url($returnurl);
$PAGE->set_title("Upload group data");
$PAGE->set_pagelayout('course');
$PAGE->set_heading($course->fullname);

if ($action != null) {
    // Process form submission.
    switch ($action) {
        case 'upload_group_data':
            $form = new block_upload_group_upload_form();

            if ($form->is_cancelled()) {
                redirect($CFG->wwwroot . '/course/view.php?id=' . $course->id);
            }

            $formdata = $form->get_data();

            $iid    = csv_import_reader::get_new_iid('upload_group');
            $reader = new csv_import_reader($iid, 'upload_group');

            $readcount = $reader->load_csv_content($form->get_file_content('group_data'),
                                                    $formdata->encoding,
                                                    $formdata->delimiter);

            if ($readcount === false) {
                print_error('csvloaderror', '', $returnurl);
            } else if ($readcount == 0) {
                print_error('csvemptyfile', 'error', $returnurl);
            }

            $selflib = new block_upload_group_lib();

            // Test if columns ok.
            try {
                $selflib->validate_headers($reader);
            } catch(Exception $e) {
                print_error('invalid_header', 'block_upload_group', $returnurl, array('msg' => $e->getMessage()));
            }

            // Print out sample lines and the confirm button.
            $reader->init();

            echo $OUTPUT->header();
            echo '<h>Upload groups preview</h>';

            $table = new html_table();
            $table->head = $reader->get_columns();

            $table->data = array();
            while ($line = $reader->next()) {
                $table->data[] = $line;
            }

            echo get_string('confirm_upload_help', 'block_upload_group');
            echo html_writer::table($table);

            // The confirm form.
            $data = array('id'    => $course->id,
                          'iid'   => $iid);
            $confirmform = new block_upload_group_confirm_form(null, $data);

            $confirmform->display();

            echo $OUTPUT->footer();
            break;

        case 'process_group_data':
            $iid  = required_param('iid', PARAM_INT);
            $reader = new csv_import_reader($iid, 'upload_group');

            $form = new block_upload_group_confirm_form();

            if ($form->is_cancelled()) {
                $reader->cleanup();
                redirect($CFG->wwwroot . '/course/view.php?id=' . $course->id);
            }

            $formdata = $form->get_data();

            $selflib = new block_upload_group_lib();

            echo $OUTPUT->header();

            try {
                $result = $selflib->process_uploaded_groups($course, $reader, $formdata->role);
            } catch(Exception $e) {
                print_error('e_process_group', 'block_upload_group', $returnurl,
                    array('msg' => $e->getMessage()));
            }

            // Output the result.
            echo $selflib->format_result($result);

            echo $OUTPUT->footer();
            break;


        default:
            // Display error.
            print_error('unknown_action', 'block_upload_group', $returnurl);
            break;
    }
} else {
    // Display the upload form.
    echo $OUTPUT->header();
    echo "<h2 id=\"upload_group_title\">Upload Groups</h2>";

    $form = new block_upload_group_upload_form(null, array('id' => $course->id));
    $form->display();

    echo $OUTPUT->footer();
}
