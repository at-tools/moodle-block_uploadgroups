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

$capabilities = array(
    'block/upload_group:addinstance'    => array(
        'riskbitmask'                   => RISK_DATALOSS,
        'captype'                       => 'write',
        'contextlevel'                  => CONTEXT_BLOCK,
        'archetypes'                    => array(
            'editingteacher'            => CAP_ALLOW,
            'manager'                   => CAP_ALLOW),
        'clonepermissionsfrom'          => 'moodle/site:manageblocks'
    )
);
