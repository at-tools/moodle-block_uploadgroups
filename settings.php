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

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    // It must be included from a Moodle page.
}

$ADMIN->add('accounts', new admin_externalpage(
    'upload_group',
    'Upload group',
    $CFG->wwwroot.'/blocks/upload_group/block_upload_group.php',
    array('block/upload_group:addinstance')
));

if ($hassiteconfig) {
    // Add a setting page.
    $settings = new admin_settingpage('block_upload_group', get_string('pluginname', 'block_upload_group'));

    $settings->add(new admin_setting_pickroles(
                    'block_upload_group/allowed_uploadgroup_roles',
                    new lang_string('availableuploadgrouproles', 'block_upload_group'),
                    new lang_string('configalloweduploadgrouproles', 'block_upload_group'),
                    array('student')));
}

