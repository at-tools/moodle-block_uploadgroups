<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

$ADMIN->add('accounts', new admin_externalpage(
	'upload_group',
	'Upload group',
    $CFG->wwwroot.'/blocks/upload_group/block_upload_group.php',
    array('block/upload_group:addinstance')
));

if ($hassiteconfig) {
    // add a setting page
    $settings = new admin_settingpage('block_upload_group', get_string('pluginname', 'block_upload_group'));

    $settings->add(new admin_setting_pickroles(
                    'block_upload_group/allowed_uploadgroup_roles',
                    new lang_string('availableuploadgrouproles', 'block_upload_group'),
                    new lang_string('configalloweduploadgrouproles', 'block_upload_group'),
                    array('student')));
}

