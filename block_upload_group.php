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

class block_upload_group extends block_base {

    /**
     * initialize the plugin
     */
    public function init() {
        $this->title = get_string('blocktitle', 'block_upload_group');
    }


    /**
     * @see block_base::applicable_formats()
     */
    public function applicable_formats() {
        return array('course-view' => true);
    }


    /**
     * no need to have multiple blocks to perform the same functionality
     */
    public function instance_allow_multiple() {
        return false;
    }

    /**
     * @see block_base::get_content()
     */
    public function get_content() {
        global $CFG, $PAGE, $USER, $COURSE, $OUTPUT;

        if ($this->content !== null) {
            return $this->content;
        }

        // Display admin or user page depending capability.
        $context = context_block::instance($this->instance->id);

        $this->content = new stdClass();

        if (has_capability('moodle/course:managegroups', $context)) {
            $this->content->text = '<a href="'.$CFG->wwwroot.
                '/blocks/upload_group/index.php?id='.$COURSE->id.'">Upload groups</a>';
        } else {
            $this->content->text = '';
        }

        $this->content->footer = '';
        return $this->content;
    }

    public function has_config() {
        return true;
    }
}
