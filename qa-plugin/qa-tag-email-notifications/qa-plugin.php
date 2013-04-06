<?php

/*
	Plugin Name: Tag Email Notifications
	Plugin URI: 
	Plugin Description: Sends email for new questions, to users who favorited the tag where it was posted. Based on https://github.com/dunse/qa-tag-email-notifications.
	Plugin Version: 0.9
	Plugin Date: 2013-03-30
	Plugin Author: Spencer Thiel
	Plugin Author URI: 
	Plugin License: MIT License
	Plugin Minimum Question2Answer Version: 1.5
	Plugin Update Check URI: 
*/

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}

	qa_register_plugin_module('event', 'qa-tag-email-notifications-event.php', 'qa_tag_email_notifications_event', 'Tag Email Notifications');


/*
        Omit PHP closing tag to help avoid accidental output
*/

