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
 * Toxicity check via Perspective API for Forum posts.
 *
 * @package    local_toxicitycheck
 * @author     Paul Vincent
 * @copyright  2021 Paul Vincent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/*
$observers = array(
    array(
        'eventname' => '\mod_forum\event\post_created',
        'callback' => 'local_toxicitycheck_observer::newPost',
    ),
);
*/
   if (!empty(get_config('local_toxicitycheck', 'toxcheckstatus'))) {
            
        $observers = array();
        $events = array(
            "\\mod_forum\\event\\discussion_created",
            "\\mod_forum\\event\\post_created",
        );
        foreach ($events AS $event) {
            $observers[] = array(
                    'eventname' => $event,
                    'callback' => '\local_toxicitycheck_observer::newPost',
                    'priority' => 9999,
                );
        }
   }
