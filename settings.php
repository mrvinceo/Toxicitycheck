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
 * Version details
 *
 * @package    local_toxicitycheck
 * @author     Paul Vincent
 * @copyright  2021 Paul Vincent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Ensure the configurations for this site are set.
// Ensure the configurations for this site are set
if ( $hassiteconfig ){
 
	// Create the new settings page
	// - in a local plugin this is not defined as standard, so normal $settings->methods will throw an error as
	// $settings will be NULL
	$settings = new admin_settingpage( 'local_toxicitycheck', 'Toxicity Check Settings' );
 
	// Create 
	$ADMIN->add( 'localplugins', $settings );
  
/*    $settings->add(new admin_setting_configcheckbox(
        'local_toxicitycheck/toxcheckstatus',
        get_string('toxinfo_toxcheckstatus', 'local_toxicitycheck'),                                    
        get_string('toxinfo_help', 'local_toxicitycheck'),
        '0'
    ));*/
    
       $enabletoxicity = new admin_setting_configcheckbox(
           'local_toxicitycheck/toxcheckstatus',
            get_string('toxinfo_toxcheckstatus', 'local_toxicitycheck'),
            get_string('toxinfo_help', 'local_toxicitycheck'), 0, 1);
        $settings->add($enabletoxicity);
    
    	// Add a setting field to the settings for this page

	$settings->add( new admin_setting_configtext(
 
		// This is the reference you will use to your configuration
		'apikey',
 
		// This is the friendly title for the config, which will be displayed
		'Perspective API: Key',
 
		// This is helper text for this config field
		'This is the key used to access the External Perspective API',
 
		// This is the default value
		'No Key Defined',
 
		// This is the type of Parameter this config is
		PARAM_TEXT
 
	) );
 
}