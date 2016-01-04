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
 * Strings for component 'access', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   block_upload_group 
 * @copyright 2015 onwards University of Minnesota
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Upload groups';
$string['blockname'] = 'Upload groups';
$string['blocktitle'] = 'Upload groups';
$string['usage'] = 'Assign participants into groups from an uploaded CSV file.';
$string['upload_group:addinstance'] = 'Add a new Upload Groups block';

// Interface.
$string['role_desc'] = 'Assign this role to users that will be enrolled:';
$string['upload_group_data'] = 'Upload group data';
$string['submit_group_data'] = 'Submit group data';
$string['encoding'] = 'Encoding';
$string['delimiter'] = 'Delimiter';
$string['row_preview_num'] = 'Row preview limit';
$string['process_group_data'] = 'Process group data';
$string['availableuploadgrouproles'] = 'Allowed group upload roles';
$string['configalloweduploadgrouproles'] = 'Select roles to be available for group upload.';

// Result.
$string['result_group_created'] = 'Groups created';
$string['result_member_added'] = 'Group members added';
$string['result_user_not_found'] = 'Users not found';
$string['result_group_failed'] = 'Group creation failed';
$string['result_enroll_failed'] = 'User enrolments failed';
$string['result_member_failed'] = 'Group members failed to be added';
$string['result_user_not_added'] = 'Users not added to a group';

$string['upload_help'] = '
Preparing the source file:
<ul>
<li>Create a CSV file with two columns: USERNAME and GROUP.</li>
<li>In the USERNAME column put the full username (e.g. jdoexxxx@umn.edu) of anyone you want to add to the group including users who are not enrolled in the course - they will be enrolled as part of the process.</li>
<li>In the GROUP column put the group name, which can be an existing group in the course or a new group which will be created and populated at the same time.</li>
<li>Save the file.</li>
</ul>
<br>
In the form below:
<ul>
<li>Click &#8220;Choose a file&#8221; and upload your CSV file.</li>
<li>Click &#8220;Submit group data&#8221;</li>
</ul>';

$string['confirm_upload_help'] = '
<p><ul>
<li>Verify the table below looks alright.</li>
<li>Select a role to be assigned to users that will be enrolled into the course.</li>
<li>Click &#8220;Process group data&#8221;</li>
</ul></p>';
