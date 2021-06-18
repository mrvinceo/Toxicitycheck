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


class local_toxicitycheck_observer {
    /**
     * Event processor - post created
     *
     * @param \mod_forum\event\post_created $event
     * @return bool
     */

    public static function newPost($event) {
        global $CFG, $DB, $OUTPUT;
        $entry = (object)$event->get_data();
        print_r($entry);

        if (substr($entry->eventname, 0, strlen("\\mod_forum\\event\\post_")) == "\\mod_forum\\event\\post_") {

            $forumid = $DB->get_record('forum_posts', array('id' => $entry->objectid));
            $message = $forumid->message;
            
        } else {
            $discussion = $DB->get_record("forum_discussions", array("id" => $entry->objectid));
            $post = $DB->get_record("forum_posts", array("discussion" => $discussion->id, "parent" => 0));
            $message = $post->message;
        }
        $message = strip_tags($message);
        $message = trim($message, $characters = " \n\r\t\v\0");

        // set our url with curl_setopt()
        if (!empty(get_config('local_toxicitycheck', 'apikey'))) {
            $api = get_config('local_toxicitycheck', 'apikey');
        } else {
            throw new moodle_exception('errorwebservice', 'local_toxicitycheck', '', get_string('toxerr_api_missing', 'toxicitycheck'));
        }
        $url = 'https://commentanalyzer.googleapis.com/v1alpha1/comments:analyze?key='.$api;

        $curl = curl_init();
        
        $phpdata = array(
            "comment" => array(
                "text" => $message,
                "languages" => array("en"),
                "requestedAttributes" => "TOXICITY:"
        ));
        
        $fields = '{
                    comment: {text: "'.$message.'"},
                    languages: ["en"],
                    requestedAttributes: {TOXICITY:{}, IDENTITY_ATTACK:{}, INSULT:{}, ATTACK_ON_COMMENTER:{}}
                    }';

        $json_string = json_encode($fields);
        print_r($json_string);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );

        $data = curl_exec($curl);
        //        print_r($data);
        $tox = json_decode($data);
                print_r($tox);
        $toxvalue = $tox->attributeScores->TOXICITY->summaryScore->value;
        $insvalue = $tox->attributeScores->INSULT->summaryScore->value;
        $idvalue = $tox->attributeScores->IDENTITY_ATTACK->summaryScore->value;
        $attackval = $tox->attributeScores->ATTACK_ON_COMMENTER->summaryScore->value;

        //$toxvalue = $tox->attributeScores->TOXICITY->summaryScore->value;        


      
        
       //do something with perspective api...
        \core\notification::add('This is My Message: ' . $message . ' Tox score: ' . $toxvalue . '. INSULT value: ' . $insvalue . '. ID attack:' . $idvalue . '. Attack Value: ' . $attackval . ' .', \core\output\notification::NOTIFY_INFO);
        
        curl_close($curl);
        sleep(3);
        }

       
}
