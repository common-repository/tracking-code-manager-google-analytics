<?php
/**
 * Plugin Name:        Tracking Code Manager Google Analytics
 * Plugin URI:         http://www.wpbdcoders.me/
 * Description:        The Plugin should add Google Analytics Tracking Code to every Page, it should be
 *                     possible for specific pages (not posts) to disable the Google Analytics Tracking Code
 *                     right from the WordPress page edit screen.
 * Author:             Ruhul Amin
 * Version:            0.1.0
 * Author URI:         http://www.ruhulamin.me/
 *
 * License:             GPL v3
 *
 * Text Domain:        tracking-code-manager-google-analytics
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
**/

if ( ! defined( 'WPINC' ) ) {
    die;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/class-google-analytics-tracking-code-manager.php';

function google_analytics_tracking_code_manager() {

    $gatc = new Google_Analytics_Tracking_Code_Manager();
    $gatc->run();

}

google_analytics_tracking_code_manager();