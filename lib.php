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

require_once($CFG->dirroot.'/enrol/locallib.php');
require_once($CFG->dirroot.'/group/lib.php');

/**
 * Process uploads and format results output.
 *
 * @package   block_upload_group
 * @copyright 2015 onwards University of Minnesota
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_upload_group_lib {

    /**
     * validate the uploaded CSV has the correct headers
     * @param csv_import_reader $reader
     * @return bool
     * @throws Exception
     */
    public function validate_headers($reader) {
        $columns = array();

        foreach ($reader->get_columns() as $col) {
            $col = strtoupper(trim($col));
            $columns[$col] = true;
        }

        // Column "GROUP" is required.
        if (!isset($columns['GROUP'])) {
            throw new Exception('Column GROUP not found');
        }

        // Column "USERNAME" is required.
        if (!isset($columns['USERNAME'])) {
            throw new Exception('Column USERNAME not found');
        }

        return true;
    }


    /**
     * enrol and add user to groups in course
     * @param object $course
     * @param csv_import_reader $reader
     * @param int $roleid
     */
    public function process_uploaded_groups($course, $reader, $roleid) {
        global $DB, $PAGE;

        $usercol  = null;    // Index of username column.
        $groupcol = null;    // Index of group column.

        // Find the index of the needed columns.
        $i = 0;
        foreach ($reader->get_columns() as $col) {
            $col = strtoupper(trim($col));

            switch ($col) {
                case 'USERNAME':
                    $usercol = $i;
                    break;

                case 'GROUP':
                    $groupcol = $i;
                    break;
            }

            $i++;
        }

        // Get the manual enrolment plugin.
        $enrolinstances = enrol_get_instances($course->id, true);

        $manualinstance = null;
        foreach ($enrolinstances as $instance) {
            if ($instance->enrol == 'manual') {
                $manualinstance = $instance;
                break;
            }
        }
        $manualenroler = enrol_get_plugin('manual');

        // Get the list of enrolled users for the course.
        $manager = new course_enrolment_manager($PAGE, $course);
        $totalusers = $manager->get_total_users();

        /*
         * Since the number of fields being retrieved are limited (email, id, lastaccess, and lastseen),
         * I feel comfortable retrieving the entire enrolled userbase for this course.
         */
        $users  = $manager->get_users('firstname', 'ASC', 0, $totalusers);
        $groups = $manager->get_all_groups();

        $groupids = array();
        foreach ($groups as $group) {
            $groupids[$group->name] = $group->id;
        }

        // Prep the returned array.
        $output = array('group_created'     => array(),
                        'user_enrolled'     => array(),
                        'member_added'      => array(),
                        'error'             => array(
                               'user_not_found'    => array(),
                               'group_failed'      => array(),
                               'enrol_failed'      => array(),
                               'member_failed'     => array(),
                               'user_not_added'     => array()));

        // Loop through the records.
        $reader->init();

        while ($line = $reader->next()) {
            $username  = trim($line[$usercol]);
            $groupname = trim($line[$groupcol]);

            // Check if the user exists.
            $user = $DB->get_record('user', array('username' => $username));

            if ($user === false) {
                $output['error']['user_not_found'][] = $username;
                continue;
            }

            // Enroll the user as needed.
            if (!isset($users[$user->id])) {
                try {
                    $manualenroler->enrol_user($manualinstance, $user->id, $roleid);
                    $output['user_enrolled'][] = $username;
                } catch(Exception $e) {
                    $output['error']['enroll_failed'][] = $username;
                }
            }

            // Create the group as needed.
            if (!isset($groupids[$groupname])) {

                if ($groupname != '') {
                    $data = new stdClass();
                    $data->courseid = $course->id;
                    $data->name     = $groupname;

                    $newgroupid = groups_create_group($data);
                } else {
                    $newgroupid = false;
                }

                if ($newgroupid === false) {

                    if ($groupname != '') {
                        $output['error']['group_failed'][] = $groupname;
                    }
                } else {
                    $groupids[$groupname]     = $newgroupid;
                    $output['group_created'][] = $groupname;
                }
            }

            // Add the user to the group.
            if ($groupname != '') {

                if (groups_add_member($groupids[$groupname], $user->id)) {
                    if (!isset($output['member_added'][$groupname])) {
                        $output['member_added'][$groupname] = array();
                    }

                    $output['member_added'][$groupname][] = $username;
                } else {
                    if (!isset($output['error']['member_failed'][$groupname])) {
                        $output['error']['member_failed'][$groupname] = array();
                    }

                    $output['error']['member_failed'][$groupname][] = $username;
                }
            } else {
                // No group name was provided for this user.
                $output['error']['user_not_added'][] = $username;
            }
        }

        return $output;
    }

    /**
     * Format the result from process_uploaded_group into HTML
     *
     * @param array $result
     * @return string
     */
    public function format_result($result) {
        $str = '<h>'. count($result['group_created']) . ' ' .
            get_string('result_group_created', 'block_upload_group') . ':</h>';
        $str .= '<p>' . implode(', ', $result['group_created']) . '</p><br/>';

        $str .= '<h>' . count($result['user_enrolled']) . ' Users enrolled:</h>';
        $str .= '<p>' . implode(', ', $result['user_enrolled']) . '</p><br/>';

        $groupstr = '';
        $groupcount = 0;
        foreach ($result['member_added'] as $group => $members) {
            $groupcount += count($members);
            $groupstr .= '<br/><h>' . $group . ': </h>';
            $groupstr .= '<p>' . implode(', ', $members) . '</p>';
        }
        $str .= '<h>' . $groupcount . ' ' . get_string('result_member_added', 'block_upload_group') .
            ':</h>'.$groupstr;

        $error_count = count($result['error']['user_not_found']) +
                       count($result['error']['group_failed']) +
                       count($result['error']['enrol_failed']) +
                       count($result['error']['member_failed']) +
                       count($result['error']['user_not_added']);

        $str .= '<h style="color:red;"><br/>Errors:</h>';

        if (count($result['error']['user_not_found']) > 0) {
            $str .= '<br/><h>' . get_string('result_user_not_found', 'block_upload_group') . ': </h>';
            $str .= '<p>' . implode(', ', $result['error']['user_not_found']) . '</p>';
        }

        if (count($result['error']['group_failed']) > 0) {
            $str .= '<br/><h>' . get_string('result_group_failed', 'block_upload_group') . ': </h>';
            $str .= '<p>' . implode(', ', $result['error']['group_failed']) . '</p>';
        }

        if (count($result['error']['enrol_failed']) > 0) {
            $str .= '<br/><h>' . get_string('result_enroll_failed', 'block_upload_group') . ': </h>';
            $str .= '<p>' . implode(', ', $result['error']['enrol_failed']) . '</p>';
        }

        if (count($result['error']['member_failed']) > 0) {
            $str .= '<h>' . get_string('result_member_failed', 'block_upload_group') . ': </h>';

            foreach ($result['error']['member_failed'] as $group => $members) {
                $str .= '<br/><h>' . $group . ': </h>';
                $str .= '<p>' . implode(', ', $members) . '</p>';
            }
        }

        if (count($result['error']['user_not_added']) > 0) {
            $str .= '<br/><h>'. count($result['error']['user_not_added']) . ' ' .
                get_string('result_user_not_added', 'block_upload_group') . ': </h>';
            $str .= '<p>' . implode(', ', $result['error']['user_not_added']) . '</p>';
        }

        return $str;
    }
}
