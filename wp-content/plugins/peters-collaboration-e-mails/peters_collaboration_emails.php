<?php
/*
Plugin Name: Peter's Collaboration E-mails
Plugin URI: http://www.theblog.ca/wordpress-collaboration-emails
Description: Enhance the "Submit for Review" feature for Contributor users. This plugin enables automatic e-mails to the relevant users when posts are pending, when they are approved, and when their statuses are changed from "pending" back to "draft".
Author: Peter Keung
Version: 1.8.1
Change Log:
2012-10-08  1.8.1: Rewrite function that gets post type moderators to support collaborators with more restricted permissions. (Thanks Harold!)
2012-10-05  1.8.0: Added approver_user_id custom field upon pending-to-publish and pending-to-future transitions. (Thanks Chris Andrews!)
2012-10-03  1.7.1: Minor fixes for translations. (Thanks Hijili Kosugi!)
2012-09-22  1.7.0: Added e-mails at the "private-to-published" transition (enabled by default), and if a post is edited AND commented using the Peter's Post Notes plugin by another user (thanks Erik!) (disabled by default).
2011-09-22  1.6.2: Minor code edit for WordPress 3.3 compatibility.
2011-08-13  1.6.1: Minor code cleanup to remove unnecessary error notices.
2011-06-19  1.6.0: Added settings to disable any of the e-mails.
2010-11-27  1.5.0: Support for custom post types and taxonomies
2010-09-02  1.4.0: Added ability to specify contributor and moderator roles for sites with custom roles and capabilities
2010-04-25  1.3.5: E-mails are now all encoded in UTF-8.
2010-01-11  1.3.4: Plugin now removes its database tables when it is uninstalled, instead of when it is deactivated. This prevents the collaboration rules from being deleted when upgrading WordPress automatically.
2009-09-22  1.3.3: Maintenance release to remove unnecessary code calls and increase security.
2009-06-27  1.3.2: Minor fixes for translations.
2009-06-19  1.3.1: Updated for WordPress 2.8 so that the approver doesn't get an e-mail if they simply save an already pending post.
2009-02-16  1.3.0: Added e-mails at the "pending-to-future" and "future-to-publish" transitions.
2009-02-06  1.2.2: Backwards translation support for WordPress 2.5
2009-01-03  1.2.1: Added .po and .mo files for translators.
2008-12-10  1.2.0: Added another e-mail trigger: when a pending post's status is changed back to a draft. Also added interoperability with Peter's Post Notes (for WordPress 2.7 and up; http://www.theblog.ca/wordpress-post-notes) so that users can leave descriptive notes at each step in the workflow.
2008-09-18  1.1.0: You can specify moderators per category. This update also includes several bug fixes to the management page functionality.
2008-08-07  1.0.1: Database table names no longer use a fixed prefix. They now use whatever your WordPress installation uses ("wp_" by default).
2008-07-22  1.0.0: You can specify moderators per user. This is managed in the Settings section of the WordPress admin interface.
2007-11-11  0.2.0: You can specify a name and e-mail address for the sender of all collaboration e-mails or have the sender information default to the user performing the action. You can also toggle whether the post author should be told which user approved their post.
2007-10-31  First version. You can e-mail multiple moderators when a post is submitted for review. Also, the author is e-mailed when one of their posts is approved.
Author URI: http://www.theblog.ca/
*/

// ----------------------------------------------------------------------
// Follow the instructions in this section to customize the notifications
// ----------------------------------------------------------------------

// The URL to your site. Replace this with the base WordPress directory (containing the wp-admin folder) if the pending e-mail notification does not have the correct URL
$pce_siteurl = get_option('siteurl');

// The name of your blog, to appear in the title of e-mails. Replace this if e-mail subjects aren't correct
$pce_blogname = get_option('blogname');

// Enter the e-mail address for the person sending all e-mails. When this is set to false, the sender is the user performing the action. For example, the pending e-mail would be sent from the post author.
$pce_fromaddress = false;

// Enter the name for the person sending all e-mails. When this is set to false, the name is of the user performing the action.
$pce_fromname = false;

// Set this value to true if you want the contributor to know who approved his / her post.
// When this value is true, the above two settings are usually set to false
$pce_whoapproved = true;

// Which roles on your site can only "submit for review"
// Typically you do not have to edit this unless you have custom roles and capabilities
$pce_contributor_roles = array();
$pce_contributor_roles[] = 'contributor';

// Which roles on your site can approve posts
// Typically you do not have to edit this unless you have custom roles and capabilities
$pce_moderator_roles = array();
$pce_moderator_roles[] = 'administrator';
$pce_moderator_roles[] = 'editor';

// Which e-mails to send. Set any of these to false to disable them
$pce_emails_to_send = array();
// Set to pending for the first time
$pce_emails_to_send['pending'] = true;
// Goes from pending to immediately published
$pce_emails_to_send['approved'] = true;
// Goes from pending to approved at a set time in the future
$pce_emails_to_send['future'] = true;
// Goes back to draft status
$pce_emails_to_send['backtodraft'] = true;
// Goes from scheduled to be published to actually published
$pce_emails_to_send['wentlive'] = true;
// Goes from private to published
$pce_emails_to_send['private_to_published'] = true;
// Edited and commented on (using the Peter's Post Notes plugin) by someone who is not the author, without having changed the status
// This applies no matter what the status is, so it works for example for pending and published posts
$pce_emails_to_send['edited'] = false;

// To edit the collaboration e-mail settings in the WordPress admin panel, users need this capability
// Typically editors and up have "manage_links" capabilities
// See http://codex.wordpress.org/Roles_and_Capabilities for more information about out of the box capabilities
$pce_required_capability = 'manage_links';

global $wpdb;
global $pce_db_group;
// Name of the database table that will hold group information and moderator rules
$pce_db_group = $wpdb->prefix . 'collaboration';

global $pce_db_collab;
// Name of the database table that will hold group - collaborator associations
$pce_db_collab = $wpdb->prefix . 'collabwriters';

global $pce_db_cats;
// Name of the database table that will hold category-specific moderators
// This table is no longer used, but defined here for upgrading and uninstalling purposes
$pce_db_cats = $wpdb->prefix . 'collabcats';

global $pce_db_collabrules;
// Name of the database table that will hold post-type-specific moderators
$pce_db_collabrules = $wpdb->prefix . 'collabrules';

// -------------------------------------------
// You should not have to edit below this line
// -------------------------------------------

global $pce_version;
$pce_version = '1.8.1';

// Enable translations
add_action('init', 'pce_textdomain');
function pce_textdomain() {
	load_plugin_textdomain('peters_collaboration_emails', PLUGINDIR . '/' . dirname(plugin_basename(__FILE__)), dirname(plugin_basename(__FILE__)));
}

// Call jQuery
function pce_js_admin_header()
{
    wp_enqueue_script( 'jquery' );
}
add_action( 'admin_print_scripts', 'pce_js_admin_header' );

function pce_pending($pce_newstatus, $pce_oldstatus, $pce_object) {
    global $wpdb, $pce_db_group, $pce_db_collab, $pce_siteurl, $pce_blogname, $pce_fromname, $pce_fromaddress, $pce_whoapproved, $pce_emails_to_send, $user_identity, $user_email, $user_ID, $_POST;

    // The person who wrote the post
    $pce_thisuser = get_userdata($pce_object->post_author);

    // Get information about the currently logged in user, as the person submitting the post for review or approving it
    // Their name is mapped to $user_identity and their e-mail address is mapped to $user_email
    get_currentuserinfo();

    // If specified in the settings, assign the current user values as the e-mail sender information
    if (!$pce_fromname) $pce_fromname = $user_identity;
    if (!$pce_fromaddress) $pce_fromaddress = $user_email;

    // Line break, which we will use many times in constructing e-mails
    $pce_eol = "\r\n";
    
    // If a note was submitted, we will use it in the e-mails
    if (isset($_POST['ppn_post_note']) && $_POST['ppn_post_note'] != '') {
        $pce_post_note = stripslashes( $_POST['ppn_post_note'] );
    }
    
    // Make sure the mail client knows it's a UTF-8 e-mail
    $pce_headers = 'Content-Type: text/plain; charset=utf-8' . $pce_eol;

    // E-mail moderator(s) for pending posts
    if( $pce_emails_to_send['pending'] && 'pending' == $pce_newstatus && 'pending' != $pce_oldstatus )
    {

        $pce_moderators_unserialized = array();
        
        // Get the moderator information based on the collaboration rules
        $pce_collabgroups = $wpdb->get_results('SELECT groupid FROM ' . $pce_db_collab . ' WHERE writerid = ' . $pce_object->post_author, ARRAY_N);
        
        // If they are part of groups, get the moderator info for each group
        if ($pce_collabgroups) {
            foreach ($pce_collabgroups as $pce_collabgroup) {
                $pce_moderators = $wpdb->get_var('SELECT moderators FROM ' . $pce_db_group . ' WHERE collabgroup = ' . $pce_collabgroup[0]);
                $pce_moderators_unserialized = array_merge(unserialize($pce_moderators), $pce_moderators_unserialized);
            }
        }

        // Get post type rules
        $pce_moderators = pce_get_post_type_moderators( $pce_object );

        if( count( $pce_moderators ) )
        {
            foreach( $pce_moderators as $pce_moderator )
            {
                $pce_moderators_unserialized = array_merge( unserialize( $pce_moderator ), $pce_moderators_unserialized );
            }
        }
        
        
        // Remove duplicate entries for groups and categories
        $pce_moderators_unserialized = array_unique($pce_moderators_unserialized);

        // Get the default moderator information
        if (count($pce_moderators_unserialized) == 0) {
            $pce_moderators = $wpdb->get_var('SELECT moderators FROM ' . $pce_db_group . ' WHERE collabgroup = 1');
            $pce_moderators_unserialized = unserialize($pce_moderators);
        }
        $pce_moderators_emails = array();
        
        foreach ($pce_moderators_unserialized as $pce_moderator_unserialized) {
            if (is_numeric($pce_moderator_unserialized)) {
                $pce_moderator_data = get_userdata($pce_moderator_unserialized);
                $pce_moderators_emails[] = $pce_moderator_data->user_email;
            }
            elseif($pce_moderator_unserialized == 'admin') {
                $pce_moderators_emails[] = get_option('admin_email');
            }
            
            // must be an e-mail address
            else {
                $pce_moderators_emails[] = $pce_moderator_unserialized;
            }
        }
        
        // Remove duplicate entries after converting to e-mail addresses
        $pce_moderators_emails = array_unique($pce_moderators_emails);
        
        $pce_moderator = implode (', ', $pce_moderators_emails);

        // Header stuff for a pending post
        // Header stuff from http://ca.php.net/mail
        $pce_headers .= 'From:' . $pce_fromname . ' <' . $pce_fromaddress . '>'. $pce_eol;
        $pce_headers .= 'Reply-To:' . $pce_fromname . ' <'. $pce_fromaddress . '>' . $pce_eol;
        $pce_headers .= 'Return-Path:' . $pce_fromname . ' <'. $pce_fromaddress . '>' . $pce_eol;

        // Body of the e-mail for a pending post
        $pce_body = sprintf(__('There is a new post to review, written by %s.', 'peters_collaboration_emails'), $pce_fromname) . $pce_eol . $pce_eol;
        // Insert note if applicable
        if(isset($pce_post_note)) {
            $pce_body .= sprintf(__('Accompanying note from %s:', 'peters_collaboration_emails'), $pce_fromname) . $pce_eol;
            $pce_body .= $pce_post_note . $pce_eol . $pce_eol;
        }
        $pce_body .= __('Review and publish it here: ', 'peters_collaboration_emails') . $pce_siteurl . '/wp-admin/post.php?action=edit&post=' . $pce_object->ID;

        // E-mail subject for a pending post
        $pce_subject = '[' . $pce_blogname . '] "' . $pce_object->post_title . '" ' . __('pending', 'peters_collaboration_emails');

        // Send the notification e-mail for a pending post
        wp_mail($pce_moderator, $pce_subject, $pce_body, $pce_headers);
    }


    // E-mail the post author when a post is approved
    elseif( $pce_emails_to_send['approved'] && 'pending' == $pce_oldstatus && 'publish' == $pce_newstatus )
    {
    
        // Store the ID of the user who approved the post in the database
        update_post_meta( $pce_object->ID, 'approver_user_id', $user_ID );

        // Header stuff for an approved post
        // Header stuff from http://ca.php.net/mail
        $pce_headers .= 'From: ' . $pce_fromname . ' <' . $pce_fromaddress . '>' . $pce_eol;
        $pce_headers .= 'Reply-To: ' . $pce_fromname . ' <' . $pce_fromaddress . '>' . $pce_eol;
        $pce_headers .= 'Return-Path: ' . $pce_fromname. ' <' . $pce_fromaddress .'>' . $pce_eol;

        // E-mail body for an approved post
        $pce_body = sprintf( __( 'Hi %s!', 'peters_collaboration_emails' ), $pce_thisuser->display_name ) . $pce_eol . $pce_eol;
        if( $pce_whoapproved )
        {
            $pce_body .= sprintf( __( 'Your post has been approved by %s and is now published', 'peters_collaboration_emails' ), $pce_fromname );
        }
        else
        {
            $pce_body .= __( 'Your post has been approved', 'peters_collaboration_emails' );
        }
        
        $pce_body .= $pce_eol . $pce_eol;
        
        // Insert note if applicable
        if(isset($pce_post_note)) {
            $pce_body .= __('Accompanying note:', 'peters_collaboration_emails') . $pce_eol;
            $pce_body .= $pce_post_note . $pce_eol . $pce_eol;
        }
        $pce_body .= __('See it here:', 'peters_collaboration_emails') . ' ' . get_permalink($pce_object->ID);

        // E-mail subject for an approved post
        $pce_subject = '[' . $pce_blogname . '] "' . $pce_object->post_title . '" ' . __('published', 'peters_collaboration_emails');

        // Send the notification e-mail for an approved post
        wp_mail($pce_thisuser->user_email, $pce_subject, $pce_body, $pce_headers);
    }

    
    // E-mail the post author when a post is scheduled to be published
    elseif( $pce_emails_to_send['future'] && 'pending' == $pce_oldstatus && 'future' == $pce_newstatus )
    {

        // Store the ID of the user who approved the post in the database
        update_post_meta( $pce_object->ID, 'approver_user_id', $user_ID );

        // Header stuff for an approved post
        // Header stuff from http://ca.php.net/mail
        $pce_headers .= 'From: ' . $pce_fromname . ' <' . $pce_fromaddress . '>' . $pce_eol;
        $pce_headers .= 'Reply-To: ' . $pce_fromname . ' <' . $pce_fromaddress . '>' . $pce_eol;
        $pce_headers .= 'Return-Path: ' . $pce_fromname. ' <' . $pce_fromaddress .'>' . $pce_eol;

        // E-mail body for a scheduled post
        $pce_body = sprintf(__('Hi %s!', 'peters_collaboration_emails'), $pce_thisuser->display_name) . $pce_eol . $pce_eol;
        if( $pce_whoapproved )
        {
            $pce_body .= sprintf( __( 'Your post has been approved by %s and is scheduled to be published on %s UTC %s', 'peters_collaboration_emails' ), $pce_fromname, $pce_object->post_date, get_option( 'gmt_offset' ) );
        }
        else
        {
            $pce_body .= __( 'Your post has been approved', 'peters_collaboration_emails' );
        }
        $pce_body .= $pce_eol . $pce_eol;

        // Insert note if applicable
        if(isset($pce_post_note)) {
            $pce_body .= __('Accompanying note:', 'peters_collaboration_emails') . $pce_eol;
            $pce_body .= $pce_post_note . $pce_eol . $pce_eol;
        }

        // E-mail subject for an approved post
        $pce_subject = '[' . $pce_blogname . '] "' . $pce_object->post_title . '" ' . __('approved and scheduled', 'peters_collaboration_emails');

        // Send the notification e-mail for an approved post
        wp_mail($pce_thisuser->user_email, $pce_subject, $pce_body, $pce_headers);
    }

    // E-mail the post author if their post is back to draft status
    elseif( $pce_emails_to_send['backtodraft'] && 'pending' == $pce_oldstatus && 'draft' == $pce_newstatus )
    {
        // E-mail the post author to let them know that their post has been published

        // Header stuff for a "back to draft" post
        // Header stuff from http://ca.php.net/mail
        $pce_headers .= 'From: ' . $pce_fromname . ' <' . $pce_fromaddress . '>' . $pce_eol;
        $pce_headers .= 'Reply-To: ' . $pce_fromname . ' <' . $pce_fromaddress . '>' . $pce_eol;
        $pce_headers .= 'Return-Path: ' . $pce_fromname. ' <' . $pce_fromaddress .'>' . $pce_eol;

        // E-mail body for a "back to draft" post
        $pce_body = sprintf(__('Hi %s!', 'peters_collaboration_emails'), $pce_thisuser->display_name) . $pce_eol . $pce_eol;
        if( $pce_whoapproved )
        {
            $pce_body .= sprintf( __( 'Your post has been reverted back to draft status by %s.', 'peters_collaboration_emails' ), $pce_fromname );
        }
        else
        {
            $pce_body .= __( 'Your post has been reverted back to draft status.', 'peters_collaboration_emails' );
        }
        
        $pce_body .= $pce_eol . $pce_eol;

        if(isset($pce_post_note)) {
            $pce_body .= __('Accompanying note:', 'peters_collaboration_emails') . $pce_eol;
            $pce_body .= $pce_post_note . $pce_eol . $pce_eol;
        }
        
        $pce_body .= __('Edit it again here:', 'peters_collaboration_emails') . ' ' . $pce_siteurl . '/wp-admin/post.php?action=edit&post=' . $pce_object->ID;

        // E-mail subject for a "back to draft" post
        $pce_subject = '[' . $pce_blogname . '] "' . $pce_object->post_title . '" ' . __('back to draft', 'peters_collaboration_emails');

        // Send the notification e-mail for a "back to draft" post
        wp_mail($pce_thisuser->user_email, $pce_subject, $pce_body, $pce_headers);
    }

    // E-mail author when his/her scheduled post is published
    elseif( ( $pce_emails_to_send['wentlive'] && 'future' == $pce_oldstatus && 'publish' == $pce_newstatus ) ||
            ( $pce_emails_to_send['private_to_published'] && 'private' == $pce_oldstatus && 'publish' == $pce_newstatus ) )
    {

        $pce_fromaddress = get_option('admin_email');

        // Header stuff for a pending post
        // Header stuff from http://ca.php.net/mail
        $pce_headers .= 'From: ' . $pce_blogname . ' <' . $pce_fromaddress . '>'. $pce_eol;
        $pce_headers .= 'Reply-To: ' . $pce_blogname . ' <'. $pce_fromaddress . '>' . $pce_eol;
        $pce_headers .= 'Return-Path: ' . $pce_blogname . ' <'. $pce_fromaddress . '>' . $pce_eol;

        // Body of the e-mail for a previously-scheduled, now published post
        $pce_body = sprintf(__('Hi %s!', 'peters_collaboration_emails'), $pce_thisuser->display_name) . $pce_eol . $pce_eol;
        $pce_body .= __('Your post is now live.', 'peters_collaboration_emails') . $pce_eol . $pce_eol;
        $pce_body .= __('See it here:', 'peters_collaboration_emails') . ' ' . get_permalink($pce_object->ID);

        // E-mail subject for a previously-scheduled, now published post
        $pce_subject = '[' . $pce_blogname . '] "' . $pce_object->post_title . '" ' . __('is now live', 'peters_collaboration_emails');

        // Send the notification e-mail for a previously-scheduled or private, now published post
        wp_mail($pce_thisuser->user_email, $pce_subject, $pce_body, $pce_headers);
    }

    // E-mail author if the post status isn't changed, but someone else has added a note (using the Peter's Post Notes plugin)
    elseif( $pce_emails_to_send['edited'] && $pce_oldstatus == $pce_newstatus && isset( $_POST['ppn_post_note'] ) && '' != $_POST['ppn_post_note'] && $pce_thisuser->user_email != $user_email )
    {
        $pce_headers .= 'From: ' . $pce_blogname . ' <' . $pce_fromaddress . '>'. $pce_eol;
        $pce_headers .= 'Reply-To: ' . $pce_blogname . ' <'. $pce_fromaddress . '>' . $pce_eol;
        $pce_headers .= 'Return-Path: ' . $pce_blogname . ' <'. $pce_fromaddress . '>' . $pce_eol;

        // Body of the e-mail
        $pce_body = sprintf(__('Hi %s!', 'peters_collaboration_emails'), $pce_thisuser->display_name) . $pce_eol . $pce_eol;
        $pce_body .= sprintf( __( 'An editorial note has been added to your post by %s:', 'peters_collaboration_emails' ), $pce_fromname ) . $pce_eol . $pce_eol;
        // We know that $pce_post_note has been set, because we've already checked for the same conditions that defined the variable
        $pce_body .= $pce_post_note . $pce_eol . $pce_eol;
        $pce_body .= __('Edit it again here:', 'peters_collaboration_emails') . ' ' . $pce_siteurl . '/wp-admin/post.php?action=edit&post=' . $pce_object->ID;

        // E-mail subject
        $pce_subject = '[' . $pce_blogname . '] "' . $pce_object->post_title . '" ' . __('has been modified with an editorial note', 'peters_collaboration_emails');

        // Send the notification e-mail for a post that has been commented on by someone who is not the author and whose status hasn't changed
        wp_mail( $pce_thisuser->user_email, $pce_subject, $pce_body, $pce_headers );
    }
}

function pce_get_post_type_moderators( $post_object )
{
    global $wpdb, $pce_db_collabrules;

    $post_type_moderators = array();

    // Get this post's taxonomies
    // First, find out its post type
    // If post type is a revision, fetch the post's parent
    if( 'revision' == $post_object->post_type )
    {
        $post_object = get_post( $post_object->post_parent );
    }

    // Get moderators for $post_object->post_type
    $post_type_rules = $wpdb->get_results( 'SELECT taxonomy, term, moderators FROM ' . $pce_db_collabrules . ' WHERE post_type = \'' . $post_object->post_type . '\'', OBJECT );

    if( $post_type_rules )
    {
        foreach( $post_type_rules as $post_type_rule )
        {
            // Note that terms are case sensitive!
            if( 'all' == $post_type_rule->taxonomy || has_term( $post_type_rule->term, $post_type_rule->taxonomy ) )
            {
                $post_type_moderators[] = $post_type_rule->moderators;
            }
        }
    }

    return $post_type_moderators;
}

add_filter('transition_post_status', 'pce_pending','',3);

if( is_admin() ) { // This line makes sure that all of this code below only runs if someone is in the WordPress back-end

// This generates an option of checkbox output for contributors or editors and administrators in the system, as well as an "admin" and "other" choice
function pce_usersoptions($pce_existingmoderators = array(), $pce_contributors_or_moderators, $pce_optionsoutput = true, $pce_numbered = 0) {
    global $wpdb, $pce_contributor_roles, $pce_moderator_roles, $pce_moderatorcache;

    $pce_usersoptions = '';
    
    // Build SQL query portion to filter contributors or approvers
    $pce_contrib_approve_code = '';
    switch ($pce_contributors_or_moderators) {
        case 'contributors':
            $pce_filter_roles = $pce_contributor_roles;
            break;
        case 'moderators':
        default:
            $pce_filter_roles = $pce_moderator_roles;
            break;
    }
    $delimiter = '';
    foreach( $pce_filter_roles as $pce_filter_role )
    {
        $pce_contrib_approve_code .= $delimiter;
        $pce_contrib_approve_code .= "'%" . $pce_filter_role . "%'";
        $delimiter = ' OR ' . $wpdb->usermeta . '.meta_value LIKE ';
    }
    
    if (isset($pce_userresultscache) && $pce_contributors_or_moderators != 'contributors') {
        $pce_userresults = $pce_moderatorcache;
    }
    else {
        $pce_userresults = $wpdb->get_results("SELECT ID, $wpdb->users.display_name, $wpdb->users.user_email FROM $wpdb->users, $wpdb->usermeta WHERE $wpdb->users.ID = $wpdb->usermeta.user_id AND $wpdb->usermeta.meta_key = '{$wpdb->prefix}capabilities' AND ($wpdb->usermeta.meta_value LIKE " . $pce_contrib_approve_code . ") ORDER BY $wpdb->users.display_name", ARRAY_N);
    }
    if ($pce_userresults) {
        $i = $pce_numbered;
        foreach ($pce_userresults as $pce_userresult) {
            if (isset($pce_existingmoderators[$pce_userresult[0]])) {
                continue;
            }
            if ($pce_optionsoutput) {
                $pce_usersoptions .= "\n" . '                    <option value="' . $pce_userresult[0] . '">' . $pce_userresult[1] . ' (' . $pce_userresult[2] . ')</option>';
            }
            else {
                $pce_usersoptions .= "\n" . '                    <p><input type="checkbox" name="pce_contributors[' . $i . ']" value="' . $pce_userresult[0] . '" /> ' . $pce_userresult[1] . '</p>';
            }
            ++$i;
        }
    }
    if ($pce_contributors_or_moderators == 'moderators' && $pce_optionsoutput) {
        $pce_moderatorcache = $pce_userresults;
    
        if (!isset($pce_existingmoderators['admin'])) {
            $pce_usersoptions .= "\n" . '                    <option value="admin">' . __('Admin address', 'peters_collaboration_emails') . ' (' . get_option('admin_email') . ')</option>';
        }
        $pce_usersoptions .= "\n" . '                    <option value="other">' . __('Other', 'peters_collaboration_emails') . '</option>';
    }
    return $pce_usersoptions;
}

// All sorts of validation on moderators, returning either an error or an array of moderators
function pce_mod_array($pce_mods, $pce_add, $pce_other_field) {
    $pce_return_mods = array();

    $i = 0;
    
    foreach ($pce_mods as $pce_mod) {
        
        // Check that it is a valid user
        if (is_numeric($pce_mod)) {
            $pce_validuser = get_userdata($pce_mod);
            if (!$pce_validuser) {
                return __('**** ERROR: Invalid moderator user ID ****', 'peters_collaboration_emails');
            }
            $pce_return_mods[$i] = intval($pce_mod);
        }
        
        // If it's a checkbox, we need the value of the dropdown list
        elseif ($pce_mod == 'on') {

            // If the dropdown equals "other" then look for content in the "other" field, which had better be an e-mail address
            if ($pce_add == 'other' && is_email($pce_other_field)) {
                $pce_return_mods[$i] = $pce_other_field;
            }
            
            elseif (is_numeric($pce_add)) {
                $pce_validuser = get_userdata($pce_add);
                if (!$pce_validuser) {
                    return __('**** ERROR: Invalid moderator user ID ****', 'peters_collaboration_emails');
                }
                $pce_return_mods[$i] = intval($pce_add);
            }
            
            elseif ($pce_add == 'admin') {
                $pce_return_mods[$i] = $pce_add;
            }
            
            else {
               return __('**** ERROR: Invalid moderator e-mail address submitted ****', 'peters_collaboration_emails');
            }
        }
        
        // Must be an e-mail address or admin
        elseif (is_email($pce_mod) || $pce_mod == 'admin') {
            $pce_return_mods[$i] = $pce_mod;
        }
        
        else {
            return __('**** ERROR: Invalid e-mail address submitted ****', 'peters_collaboration_emails');
        }
        ++$i;
    }
    return $pce_return_mods;
}

// Processes changes to the moderator rules (who approves whose posts)
function pce_modsubmit() {
    global $wpdb, $pce_db_group;
        
    // ----------------------------------
    // Process the default mod changes
    // ----------------------------------
    
    $pce_defaultmods = $_POST['pce_defaultmod']; // An array of default moderators (contains User IDs, "admin" or strictly e-mail addresses)
    $pce_defaultmods_update = array();
    if ($pce_defaultmods) {
        $pce_defaultmods_update = pce_mod_array($pce_defaultmods, $_POST['adddefaultmod'], $_POST['pce_defaultmodadd']);

        // Nicely scrubbed array of mods to serialize
        if (is_array($pce_defaultmods_update)) {
            $pce_defaultmod_serialized = serialize($pce_defaultmods_update);
        }

        // It return an error
        else {
            return array( 'error', $pce_process_close );
        }
        
        $pce_defaultmodsuccess = $wpdb->query('UPDATE ' . $pce_db_group . ' SET moderators = \'' . $pce_defaultmod_serialized . '\' WHERE collabgroup = 1');
        
        if ($pce_defaultmodsuccess) {
            return array( 'success', __( 'Default moderators updated.', 'peters_collaboration_emails' ) );
        }
    }
    else {
        return array( 'error', __('You must have at least one default mod.', 'peters_collaboration_emails') );
    }

    // We've made it this far, so nothing to report
    return false;
}

function pce_rulesubmit() {
    global $wpdb, $pce_db_group;
    
    // ----------------------------------
    // Process the rule changes
    // ----------------------------------

    $updated = false;
    
    $pce_usermods = $_POST['pce_usermod']; // An array of moderators for each group (contains User IDs, "admin" or strictly e-mail addresses)
    $pce_groupids = $_POST['pce_groupid']; // An array of group IDs whose moderators need to be updated
    $pce_num_submits = array_keys( (array) $pce_groupids );
    
    if ($pce_num_submits) {
        foreach($pce_num_submits as $pce_num_submit) {
            $pce_usermods_update = array();
            $pce_usermod = $pce_usermods[$pce_num_submit];
            $pce_groupid = intval($pce_groupids[$pce_num_submit]);
            
            // Does this group exist?
            $pce_groupname = $wpdb->get_var('SELECT groupname FROM ' . $pce_db_group . ' WHERE collabgroup = ' . $pce_groupid);
            
            if (!$pce_groupname) {
                return array( 'error', sprintf(__('**** ERROR: Group with ID of %d does not exist ****', 'peters_collaboration_emails'), $pce_groupid ) );
            }
            
            if ($pce_usermod) {
                $pce_usermod_update = pce_mod_array($pce_usermod, $_POST['addusermod'][$pce_num_submit], $_POST['pce_usermodadd'][$pce_num_submit]); 
            
                // Nicely scrubbed array of mods to serialize
                if (is_array($pce_usermod_update)) {
                    $pce_usermod_serialized = serialize($pce_usermod_update);
                }
                
                // It returns an error
                else {
                    return array( 'error', $pce_process_close );
                }
                
                $pce_usermodsuccess = $wpdb->query('UPDATE ' . $pce_db_group . ' SET moderators = \'' . $pce_usermod_serialized . '\' WHERE collabgroup = ' . $pce_groupid);
                if( $pce_usermodsuccess )
                {
                    $updated = true;
                }
            }
            else {
                return array( 'error', sprintf( __( 'You must have at least one default mod for the group "%s".', 'peters_collaboration_emails' ), $pce_groupname ) );
            }
        }
    }

    if( $updated )
    {
        // We've made it this far, so success!
        return array( 'success', sprintf( __( 'Group moderators updated.', 'peters_collaboration_emails' ) ) );
    }
    else
    {
        return false;
    }
}

function pce_groupsubmit() {
    global $wpdb, $pce_db_group, $pce_db_collab;

    // ----------------------------------
    // Process a new group addition
    // ----------------------------------

    if (!empty($_POST['newgroupname']) && $_POST['addrule'] != -1 && $_POST['addgroupmod'] != -1) {
        $newgroupname = $_POST['newgroupname'];
        $addrule = intval($_POST['addrule']);
        $addgroupmod = $_POST['addgroupmod'];
        
        // Check a contributor (basically that this contributor exists)
        $check_contributor = get_userdata($addrule);
        if (!$check_contributor) {
            return array( 'error', __( '**** ERROR: Invalid new group contributor user ID ****', 'peters_collaboration_emails' ) );
        }
        
        // Check the added group moderator (admin, user ID, or e-mail address)

        // Check that it is a valid user
        if (is_numeric($addgroupmod)) {
            $pce_validuser = get_userdata($addgroupmod);
            if (!$pce_validuser) {
                return array( 'error', __( '**** ERROR: Invalid new group moderator user ID ****', 'peters_collaboration_emails' ) );
            }
            $addgroupmod = intval($addgroupmod);
        }
            
        // If the dropdown equals "other" then look for content in pce_groupmodadd, which had better be an e-mail address
        elseif ($addgroupmod == 'other' && is_email($_POST['pce_groupmodadd'])) {
            $addgroupmod = $_POST['pce_groupmodadd'];
        }
        elseif ($addgroupmod != 'admin') {
            return array( 'error', __( '**** ERROR: Invalid new group moderator submitted ****', 'peters_collaboration_emails' ) );
        }
        
        $addgroupmod_serialized = serialize(array($addgroupmod));
        $pce_addgroupsuccess = $wpdb->query('INSERT INTO ' . $pce_db_group . ' (moderators, groupname) VALUES(\'' . $addgroupmod_serialized . '\', \'' . $newgroupname . '\')');
        if ($pce_addgroupsuccess) {
            $pce_addwritersuccess = $wpdb->query('INSERT INTO ' . $pce_db_collab . ' (groupid, writerid) VALUES (LAST_INSERT_ID(), ' . $addrule . ')');
            if ($pce_addwritersuccess) {
                return array( 'success', __( 'New group created.', 'peters_collaboration_emails' ) );
            }
            else {
                return array( 'error', __( '**** ERROR: Unknown query error when adding a collaborator to the new group ****', 'peters_collaboration_emails' ) );
            }
        }
        else {
            return array( 'error', __( '**** ERROR: Unknown query error when creating new group ****', 'peters_collaboration_emails' ) );
        }
    }
    
    else {
        return array( 'error', __( '**** ERROR: Not all necessary group information was submitted to add a group ****', 'peters_collaboration_emails' ) );
    }
    
    // We've made it this far, so nothing to do
    return false;
}

// Processes changes to a group name and its members
function pce_edit_group_submit()
{
    global $wpdb, $pce_db_group, $pce_db_collab;
    $pce_groupid = intval($_GET['group']);
    
    $pce_groupname = $wpdb->get_var('SELECT groupname FROM ' . $pce_db_group . ' WHERE collabgroup = ' . $pce_groupid);
    
    if (!$pce_groupname) {
        die(__('That group does not exist.', 'peters_collaboration_emails'));
    }
    
    // Open the informational div
    $pce_process_submit = '<div id="message" class="updated fade">' . "\n";
    
    // Code to close the informational div
    $pce_process_close = '</div>' . "\n";
    
    if (!empty($_POST['pce_groupname']) && !empty($_POST['pce_contributors'])) {
        $pce_groupname = $_POST['pce_groupname'];
        $pce_contributors = $_POST['pce_contributors'];
    }
    else {
        $pce_process_submit .= '<p><strong>' . __('**** ERROR: Insufficient group name or contributor information ****', 'peters_collaboration_emails') . '</strong></p>' . "\n";
        $pce_process_submit .= '<p><strong>' . __('**** Make sure that there is at least one contributor. ****', 'peters_collaboration_emails') . '</strong></p>' . "\n";
        $pce_process_submit .= $pce_process_close;
        return $pce_process_submit;    
    }
    
    // ----------------------------------
    // Process the group changes
    // ----------------------------------
    
    // First find out which contributors already exist
    $pce_existing_contributors = $wpdb->get_results('SELECT writerid FROM ' . $pce_db_collab . ' WHERE groupid = ' . $pce_groupid, ARRAY_N);
    $pce_existing_contributor_array = array();
    
    if ($pce_existing_contributors) {
        foreach($pce_existing_contributors as $pce_existing_contributor) {
            $pce_existing_contributor_array[$pce_existing_contributor[0]] = $pce_existing_contributor[0];
        }
    }
    
    $pce_insert_writer = false;
    
    $pce_contributors_update = array();
        
    foreach ($pce_contributors as $pce_contributor) {
            
        // Check that it is a valid user
        if (is_numeric($pce_contributor)) {
            $pce_validcontributor = get_userdata($pce_contributor);
            if (!$pce_validcontributor) {
                $pce_process_submit .= '<p><strong>' . __('**** ERROR: Invalid contributor user ID ****', 'peters_collaboration_emails') . '</strong></p>' . "\n";
                $pce_process_submit .= $pce_process_close;
                return $pce_process_submit;
            }
            if (isset($pce_existing_contributor_array[$pce_contributor])) {
                unset($pce_existing_contributor_array[$pce_contributor]);
            }
            else {
                $pce_insert_success = $wpdb->query('INSERT INTO ' . $pce_db_collab . ' (groupid, writerid) VALUES (' . $pce_groupid . ', ' . $pce_contributor. ')');
                if ($pce_insert_success && !$pce_insert_writer) {
                    $pce_insert_writer = true;
                }
            }                        
        }
    }
    if (!empty($pce_existing_contributor_array)) {
        $pce_delete_contributors = $wpdb->query('DELETE FROM ' . $pce_db_collab . ' WHERE groupid = ' . $pce_groupid . ' AND writerid IN (' . implode(',', $pce_existing_contributor_array) . ')'); 
        if ($pce_delete_contributors && !$pce_insert_writer) {
            $pce_insert_writer = true;
        }
    }
    
    if ($pce_insert_writer) {
        $pce_process_submit .= '<p><strong>' . __('Collaborators updated.', 'peters_collaboration_emails') . '</strong></p>' . "\n";
    }
    $pce_groupname_success = $wpdb->query('UPDATE ' . $pce_db_group . ' SET groupname = \'' . $pce_groupname . '\' WHERE collabgroup = ' . $pce_groupid);
        
    if ($pce_groupname_success) {
        $pce_process_submit .= '<p><strong>' . __('Group name updated.', 'peters_collaboration_emails') . '</strong></p>' . "\n";
    }

    // Close the informational div
    $pce_process_submit .= $pce_process_close;
    
    // We've made it this far, so success!
    return $pce_process_submit;
}

// Deletes a group
function pce_delete_group_submit() {
    global $wpdb, $pce_db_group, $pce_db_collab;
    
    $pce_groupid = intval($_POST['pce_groupid']);
    
    $pce_groupname = $wpdb->get_var('SELECT groupname FROM ' . $pce_db_group . ' WHERE collabgroup = ' . $pce_groupid);
    
    if (!$pce_groupname) {
        return array( 'error', __( '**** ERROR: That group does not exist ****', 'peters_collaboration_emails' ) );
    }

    // ----------------------------------
    // Process the group deletion
    // ----------------------------------
    
    // Remove all contributors
    $pce_remove_contributors = $wpdb->query('DELETE FROM ' . $pce_db_collab . ' WHERE groupid = ' . $pce_groupid);
    
    // Remove the group
    $pce_remove_group = $wpdb->query('DELETE FROM ' . $pce_db_group . ' WHERE collabgroup = ' . $pce_groupid . ' LIMIT 1');
    
    if ($pce_remove_contributors && $pce_remove_group) {
        return array( 'success', sprintf( __( 'Group %s successfully deleted.', 'peters_collaboration_emails' ), $pce_groupname ) );
    }
    else {
        return array( 'error', __( '**** ERROR: Database problem in removing the group. ****', 'peters_collaboration_emails' ) );
    }

    // Nothing to say here
    return false;
}

// This is the options page in the WordPress admin panel that enables you to set moderators on a per-user basis
function pce_optionsmenu() {
    if( isset( $_GET['group'] ) )
    {
        pce_groupoptionsmenu( $_GET['group'] );
    }
    elseif( isset( $_GET['delete_post_type_rule'] ) )
    {
        pceFunctionCollection::pce_delete_post_type_rule( $_GET['delete_post_type_rule'] );
    }
    else
    {
        pce_mainoptionsmenu();
    }
}

function pce_groupoptionsmenu( $pce_groupid )
{
    global $wpdb, $pce_db_group, $pce_db_collab;
    $pce_groupid = intval( $pce_groupid );
    
    $pce_process_submit = '';
    
    // Update the group name and contributors
    if( isset( $_POST['pce_edit_group_submit'] ) )
    {
        $pce_process_submit = pce_edit_group_submit();
    }
    
    $pce_groupname = $wpdb->get_var('SELECT groupname FROM ' . $pce_db_group . ' WHERE collabgroup = ' . $pce_groupid);
    
    if( !$pce_groupname )
    {
        die( __( 'That group does not exist.', 'peters_collaboration_emails' ) );
    }
    
    $pce_groupname = htmlspecialchars($pce_groupname, ENT_QUOTES);
    
    $pce_contributors = $wpdb->get_results('SELECT writerid FROM ' . $pce_db_collab . ' WHERE groupid = ' . $pce_groupid, ARRAY_N);
    $pce_contributors_current = '';
    $pce_contributors_whitespace = '                    ';
    
    if ($pce_contributors) {
        $pce_contributors_array = array();
        $i = 0;
        foreach ($pce_contributors as $pce_contributor) {
            $pce_contributor_data = get_userdata($pce_contributor[0]);
            
            if ($pce_contributor_data) {
                $pce_contributors_current .= "\n" . $pce_contributors_whitespace . '<p><input type="checkbox" name="pce_contributors[' . $i . ']" value="' . $pce_contributor[0] . '" checked="checked"/> ' . $pce_contributor_data->display_name . '</p>';
                $pce_contributors_array[$pce_contributor[0]] = '';
            }
            ++$i;
        }
    }
    
    
    
    // Contributors that aren't part of this group
    $pce_contributors_remaining = pce_usersoptions($pce_contributors_array, 'contributors', false, $i);
?>
    <div class="wrap">
        <h2><?php _e('Manage group:', 'peters_collaboration_emails'); ?> <?php print $pce_groupname; ?></h2>
        <?php print $pce_process_submit; ?>
        <p><a href="<?php print '?page=' . basename(__FILE__); ?>"><?php _e('Back to the main collaboration config menu', 'peters_collaboration_emails'); ?></a></p>
        <form name="pce_edit_group" method="post" action="<?php print '?page=' . basename(__FILE__) . '&group=' . $pce_groupid; ?>">
            <p><?php _e('Group name:', 'peters_collaboration_emails'); ?> <input type="text" width="30" maxlength="90" name="pce_groupname" value="<?php print $pce_groupname; ?>" /></p>
            <p><strong><?php _e('Contributors in this group:', 'peters_collaboration_emails'); ?></strong></p>
            <?php print $pce_contributors_current; ?>            
            <?php print $pce_contributors_remaining; ?>
            
            <p class="submit"><input type="submit" name="pce_edit_group_submit" value="<?php _e('Update', 'peters_collaboration_emails'); ?>" /></p>
        </form>
        
        <form name="pce_delete_group" method="post" action="<?php print '?page=' . basename(__FILE__); ?>">
            <p class="submit"><input type="hidden" name="pce_groupid" value="<?php print $pce_groupid;?>"><input type="submit" name="pce_delete_group_submit" value="<?php _e('Delete this group', 'peters_collaboration_emails'); ?>" /></p>
        </form>
    </div>
    
<?php
}

function pce_mainoptionsmenu()
{
    global $wpdb, $pce_db_group, $pce_db_collab, $pce_db_collabrules;

    // Upgrade check here because it's the only place we know they will visit
    pce_upgrade();
    
    $pce_process_submit = '';

    if( isset( $_POST['pce_modsubmit'] ) )
    {
        $pce_process_submit = pce_modsubmit();
    }
    elseif( isset( $_POST['pce_rulesubmit'] ) )
    {
        $pce_process_submit = pce_rulesubmit();
    }
    elseif( isset($_POST['pce_groupsubmit'] ) )
    {
        $pce_process_submit = pce_groupsubmit();
    }
    elseif( isset( $_POST['pce_delete_group_submit'] ) )
    {
        $pce_process_submit = pce_delete_group_submit();
    }
    elseif( isset( $_POST['pce_post_type_submit'] ) )
    {
        $pce_process_submit = pceFunctionCollection::pce_post_type_submit();
    }
    elseif( isset( $_POST['pce_add_post_type_submit'] ) )
    {
        $pce_process_submit = pceFunctionCollection::pce_add_post_type_submit();
    }
    
    // -----------------------------------
    // Get the list of default moderators
    // -----------------------------------
    
    $pce_defaultmods_serialized = $wpdb->get_var('SELECT moderators FROM ' . $pce_db_group . ' WHERE collabgroup = 1');
    
    // Put this list into an array since it is stored in the database as serialized
    $pce_defaultmods = unserialize($pce_defaultmods_serialized);

    // Build the list of options based on this array

    // Set up the default options variable
    $pce_defaultoptions = '';

    // Whitespace!
    $pce_defaultoptionswhitespace = '                ';

    // Establish a counter for the checkboxes
    $i = 0;

    $pce_existingmods = array();
    
    foreach ($pce_defaultmods as $pce_defaultmod) {
        // If they've chosen a user ID, get the e-mail address associated with that user ID
        if (is_numeric($pce_defaultmod)) {
            $pce_userinfo = get_userdata($pce_defaultmod);
            $pce_defaultoptions .= "\n" . $pce_defaultoptionswhitespace . '<p><input type="checkbox" name="pce_defaultmod[' . $i . ']" value="' . $pce_defaultmod . '" checked="checked" /> ' . $pce_userinfo->display_name . ' (' . $pce_userinfo->user_email . ')</p>';
            $pce_existingmods[$pce_defaultmod] = '';
        }

        // If they've chosen it to be the site admin, get the site admin e-mail address
        elseif ($pce_defaultmod == 'admin') {
            $pce_defaultoptions .= "\n" . $pce_defaultoptionswhitespace  . '<p><input type="checkbox" name="pce_defaultmod[' . $i . ']" value="' . $pce_defaultmod . '" checked="checked" /> ' . sprintf( __( 'General admin (%s)', 'peters_collaboration_emails' ), get_option( 'admin_email' ) ) . '</p>';
            $pce_existingmods['admin'] = '';
        }
        
        // Whatever is left should be a custom e-mail address
        else {
            $pce_defaultoptions .= "\n" . $pce_defaultoptionswhitespace . '<p><input type="checkbox" name="pce_defaultmod[' . $i . ']" value="' . $pce_defaultmod . '" checked="checked" /> ' . $pce_defaultmod . '</p>';
        }

        ++$i;
    }
    
    $pce_defaultoptions .= "\n" . $pce_defaultoptionswhitespace . '<p><input type="checkbox" name="pce_defaultmod[' . $i .']" /> ' . __( 'Add:', 'peters_collaboration_emails' ) . ' <select name="adddefaultmod" id="adddefaultmod">';
    $pce_defaultoptions .= pce_usersoptions($pce_existingmods, 'moderators');
    $pce_defaultoptions .= "\n" . $pce_defaultoptionswhitespace . '</select></p><p id="pce_adddefaultmod">E-mail: <input type="text" name="pce_defaultmodadd" width="30" maxlength="90" /></p>';
    
    // -----------------------------------
    // Get the group-specific moderator rules
    // -----------------------------------

    // Set up the default options variable
    $pce_useroptions = '';

    $pce_usermods_results = $wpdb->get_results('SELECT collabgroup, moderators, groupname FROM ' . $pce_db_group . ' WHERE collabgroup != 1 ORDER BY groupname', ARRAY_N);
    
    if( $pce_usermods_results )
    {

        $i_m = 0;
        
        foreach ($pce_usermods_results as $pce_usermod_result) {
        
            // Define the group name
            $pce_groupname = htmlspecialchars($pce_usermod_result[2], ENT_QUOTES);
            
            $pce_useroptions .= '<tr>' . "\n";
            $pce_useroptions .= '<td><p><strong>' . $pce_groupname . '</strong> [<a href="?page=' . basename(__FILE__) . '&group=' . $pce_usermod_result[0]. '">' . __( 'Edit', 'peters_collaboration_emails' ) . '</a>]</p>';

            
            // Define the group ID
            $pce_groupid = $pce_usermod_result[0];
            
            // Get the writers in this group
            $pce_writers = $wpdb->get_results('SELECT writerid FROM ' . $pce_db_collab . ' WHERE groupid = ' . $pce_groupid, ARRAY_N);
            
            if ($pce_writers) {
            
                $pce_useroptions .= "\n" . '<p>';
                
                foreach ($pce_writers as $pce_writer) {
                    $pce_thiswriter = get_userdata($pce_writer[0]);
                    $pce_useroptions .= "\n" . $pce_thiswriter->display_name . '<br />';
                }
                $pce_useroptions .= "\n" . '</p>';
            }
            
            $pce_useroptions .= "\n" . '</td>';
                        
            // Put this list of e-mail addresses an array since it is stored in the database as serialized
            $pce_usermods = unserialize($pce_usermod_result[1]);

            // Build the list of options based on this array

            // Establish a counter for the checkboxes
            $i = 0;
            
            $pce_useroptions .= "\n" . '<td>';
            
            $pce_existingmods = array();
            
            foreach ($pce_usermods as $pce_usermod) {

                // If they've chosen a user ID, get the e-mail address associated with that user ID
                if (is_int($pce_usermod)) {
                    $pce_userinfo = get_userdata($pce_usermod);
                    $pce_useroptions .= "\n" . '<p><input type="checkbox" name="pce_usermod[' . $i_m . '][' . $i .']" value="' . $pce_usermod . '" checked="checked" /> ' . $pce_userinfo->display_name . ' (' . $pce_userinfo->user_email . ')</p>';
                    $pce_existingmods[$pce_usermod] = '';
                }

                // If they've chosen it to be the site admin, get the site admin e-mail address
                elseif ($pce_usermod == 'admin') {
                    $pce_useroptions .= "\n" . '<p><input type="checkbox" name="pce_usermod[' . $i_m . '][' . $i .']" value="' . $pce_usermod . '" checked="checked" /> ' . sprintf( __( 'General admin (%s)', 'peters_collaboration_emails' ), get_option( 'admin_email' ) ) . '</p>';
                    $pce_existingmods['admin'] = '';
                }
                
                // Whatever is left should be a custom e-mail address
                else {
                    $pce_useroptions .= "\n" . '<p><input type="checkbox" name="pce_usermod[' . $i_m . '][' . $i .']" value="' . $pce_usermod . '" checked="checked" /> ' . $pce_usermod . '</p>';
                }
                
                ++$i;
            }
            
            $pce_useroptions .= "\n" . '<p><input type="checkbox" name="pce_usermod[' . $i_m . '][' . $i .']" /> ' . __( 'Add:', 'peters_collaboration_emails' ) . ' <select name="addusermod[' . $i_m . ']" id="usermodadd[' . $i_m . ']">';
            $pce_useroptions .= pce_usersoptions($pce_existingmods, 'moderators');
            $pce_useroptions .= "\n" . '</select></p><p id="pce_usermodadd[' . $i_m . ']">E-mail: <input type="text" name="pce_usermodadd[' . $i_m . ']" width="30" maxlength="90" /></p>';
            $pce_useroptions .= "\n" . '<input type="hidden" name="pce_groupid[' . $i_m . ']" value="' . $pce_groupid . '" /></td>';
            $pce_useroptions .= "\n" . '</tr>';
            ++$i_m;
        }
    }
    
    // --------------------------------------------------------------------
    // Form to add a group, needing at least one user and at least one moderator 
    // --------------------------------------------------------------------
    
    $pce_groupoptions = '';
    
    $pce_groupoptions .= '<p>' . __( 'Group name:', 'peters_collaboration_emails' ) . ' <input type="text" name="newgroupname" width="30" maxlength="90" /></p>'; 
    $pce_groupoptions .= "\n" . '<p>' . __('Add contributor:', 'peters_collaboration_emails') . ' <select name="addrule">';
    $pce_groupoptions .= "\n" . '<option value="-1"></option>';
    
    // This list should only include users
    $pce_groupoptions .= pce_usersoptions(array(), 'contributors');
    $pce_groupoptions .= "\n" . '</select>';
    
    $pce_groupoptions .= "\n" . '<p>' . __('Add moderator:', 'peters_collaboration_emails') . ' <select name="addgroupmod" id="groupmodadd">';
    $pce_groupoptions .= "\n" . '<option value="-1"></option>';
    $pce_groupoptions .= pce_usersoptions(array(), 'moderators');
    $pce_groupoptions .= "\n" . '</select></p><p id="pce_groupmodadd">E-mail: <input type="text" name="pce_groupmodadd" width="30" maxlength="90" /></p>';

    // -----------------------------------
    // Get the post-type-specific moderator rules
    // -----------------------------------

    // Set up the default options variable
    $pce_post_type_rules = '';

    $pce_post_type_mods_results = $wpdb->get_results( 'SELECT rule_id, post_type, taxonomy, term, moderators FROM ' . $pce_db_collabrules . ' ORDER BY post_type ASC, taxonomy ASC, term ASC', OBJECT );
    if( $pce_post_type_mods_results )
    {
        $i_p = 0;
        
        foreach( $pce_post_type_mods_results as $pce_post_type_mods_result )
        {
            $pce_post_type_rules .= '<tr>' . "\n";
            
            $pce_rule_id = $pce_post_type_mods_result ->rule_id;
            
            
            // Output the post type name
            $pce_post_type_name = htmlspecialchars($pce_post_type_mods_result->post_type, ENT_QUOTES );
            $pce_post_type_rules .= '<td><p><strong>' . $pce_post_type_name . '</strong> [<a href="?page=' . basename(__FILE__) . '&delete_post_type_rule=' . $pce_rule_id. '">' . __( 'Delete', 'peters_collaboration_emails' ) . '</a>]</p></td>';

            // Output the post type taxonomy
            $pce_post_type_taxonomy = htmlspecialchars( $pce_post_type_mods_result->taxonomy, ENT_QUOTES );
            if( 'all' == $pce_post_type_taxonomy )
            {
                $pce_post_type_taxonomy_label = __( 'All', 'peters_collaboration_emails' );
            }
            else
            {
                $pce_post_type_taxonomy_label = get_taxonomy( $pce_post_type_taxonomy );
                if( '' == $pce_post_type_taxonomy_label )
                {
                    $pce_post_type_taxonomy_label = __( '*** undefined ***', 'peters_collaboration_emails' );
                }
                else
                {
                    $pce_post_type_taxonomy_label = $pce_post_type_taxonomy_label->label;
                }
            }
            $pce_post_type_rules .= '<td><p>' . $pce_post_type_taxonomy_label . '</p></td>';
            
            $pce_post_type_term = htmlspecialchars( $pce_post_type_mods_result->term, ENT_QUOTES );
            // Output the post type term
            if( 'all' == $pce_post_type_taxonomy )
            {
                $pce_post_type_term = __( '*** n/a ***', 'peters_collaboration_emails' );
            }
            elseif( is_numeric( $pce_post_type_term ) )
            {
                $pce_post_type_term_object = get_term( $pce_post_type_term, $pce_post_type_taxonomy );
                if( $pce_post_type_term_object && property_exists( $pce_post_type_term_object, 'name' ) )
                {
                    $pce_post_type_term = $pce_post_type_term_object->name;
                }
            }
            $pce_post_type_rules .= '<td><p>' . $pce_post_type_term . '</p></td>';

            // Put this list of e-mail addresses an array since it is stored in the database as serialized
            $pce_post_type_mods = unserialize( $pce_post_type_mods_result->moderators );

            // Build the list of options based on this array
            // Establish a counter for the checkboxes
            $i = 0;
            
            $pce_post_type_rules .= "\n" . '<td>';
            
            $pce_existingmods = array();
            
            foreach( $pce_post_type_mods as $pce_post_type_mod )
            {
                // If they've chosen a user ID, get the e-mail address associated with that user ID
                if( is_int( $pce_post_type_mod ) )
                {
                    $pce_userinfo = get_userdata( $pce_post_type_mod );
                    $pce_post_type_rules .= "\n" . '<p><input type="checkbox" name="pce_post_type_mod[' . $i_p . '][' . $i .']" value="' . $pce_post_type_mod . '" checked="checked" /> ' . $pce_userinfo->display_name . ' (' . $pce_userinfo->user_email . ')</p>';
                    $pce_existingmods[$pce_post_type_mod] = '';
                }

                // If they've chosen it to be the site admin, get the site admin e-mail address
                elseif( 'admin' == $pce_post_type_mod )
                {
                    $pce_post_type_rules .= "\n" . '<p><input type="checkbox" name="pce_post_type_mod[' . $i_p . '][' . $i .']" value="' . $pce_post_type_mod . '" checked="checked" /> ' . __( 'General admin', 'peters_collaboration_emails' ) . '(' . get_option('admin_email') . ')</p>';
                    $pce_existingmods['admin'] = '';
                }
                
                // Whatever is left should be a custom e-mail address
                else
                {
                    $pce_post_type_rules .= "\n" . '<p><input type="checkbox" name="pce_post_type_mod[' . $i_p . '][' . $i .']" value="' . $pce_post_type_mod . '" checked="checked" /> ' . $pce_post_type_mod . '</p>';
                }
                
                ++$i;
            }
            
            $pce_post_type_rules .= "\n" . '<p><input type="checkbox" name="pce_post_type_mod[' . $i_p . '][' . $i .']" /> ' . __( 'Add:', 'peters_collaboration_emails' ) . ' <select name="add_post_type_mod[' . $i_p . ']" id="post_type_mod_add[' . $i_p . ']">';
            $pce_post_type_rules .= pce_usersoptions( $pce_existingmods, 'moderators' );
            $pce_post_type_rules .= "\n" . '</select></p><p id="pce_post_type_mod_add[' . $i_p. ']">E-mail: <input type="text" name="pce_post_type_mod_add[' . $i_p . ']" width="30" maxlength="90" /></p>';
            $pce_post_type_rules .= "\n" . '<input type="hidden" name="pce_rule_id[' . $i_p . ']" value="' . $pce_rule_id . '" /></td>';
            $pce_post_type_rules .= "\n" . '</tr>';
            ++$i_p;
        }
    }
    
?>
    <div class="wrap">
        <h2><?php _e('Manage collaboration e-mails', 'peters_collaboration_emails'); ?></h2>
        <p><?php _e('Set the moderators who should be e-mailed whenever Contributor users submit pending posts.', 'peters_collaboration_emails'); ?></p>
        <?php // 
        if( is_array( $pce_process_submit ) && count( $pce_process_submit ) )
        {
            print '<div id="message" class="updated fade">' . "\n";
            print $pce_process_submit[1];
            print '</div>' . "\n";
        }
        ?>

        <h3><?php _e('Default moderators', 'peters_collaboration_emails'); ?></h3>
        <form name="pce_modform" action="<?php print '?page=' . basename(__FILE__); ?>" method="post">
        <p><?php _e('These users will be e-mailed if none of the rules below match. Note that they must be either editors or administrators.', 'peters_collaboration_emails'); ?></p>
            <?php print $pce_defaultoptions; ?>
            
        <p class="submit"><input type="submit" name="pce_modsubmit" value="<?php _e('Update', 'peters_collaboration_emails'); ?>" /></p>
        </form>
            
        <h3><?php _e('Moderators by group', 'peters_collaboration_emails'); ?></h3>
        <form name="pce_ruleform" action="<?php print '?page=' . basename(__FILE__); ?>" method="post">
        <h4><?php _e('Existing rules', 'peters_collaboration_emails'); ?></h4>
        <table class="widefat">
            <tr>
                <th><?php _e('Group', 'peters_collaboration_emails'); ?></th>
                <th><?php _e('Moderators', 'peters_collaboration_emails'); ?></th>
            </tr>
            <?php print $pce_useroptions; ?>
            
        </table>
        <p class="submit"><input type="submit" name="pce_rulesubmit" value="<?php _e('Update', 'peters_collaboration_emails'); ?>" /></p>
        </form>
        <form name="pce_groupform" action="<?php print '?page=' . basename(__FILE__); ?>" method="post">
        <h4><?php _e('Add a group', 'peters_collaboration_emails'); ?></h4>
            <?php print $pce_groupoptions; ?>
        
        <p class="submit"><input type="submit" name="pce_groupsubmit" value="<?php _e('Update', 'peters_collaboration_emails'); ?>" /></p>
        </form>
        
        <h3><?php _e('Moderators by post type and taxonomy', 'peters_collaboration_emails'); ?></h3>
        <form name="pce_post_type_form" action="<?php print '?page=' . basename(__FILE__); ?>" method="post">
        <h4><?php _e('Existing rules', 'peters_collaboration_emails'); ?></h4>
        <table class="widefat">
            <tr>
                <th><?php _e('Post Type', 'peters_collaboration_emails'); ?></th>
                <th><?php _e('Taxonomy', 'peters_collaboration_emails'); ?></th>
                <th><?php _e('Term', 'peters_collaboration_emails'); ?></th>
                <th><?php _e('Moderators', 'peters_collaboration_emails'); ?></th>
            </tr>
            <?php print $pce_post_type_rules; ?>
            
        </table>
        <p class="submit"><input type="submit" name="pce_post_type_submit" value="<?php _e('Update', 'peters_collaboration_emails'); ?>" /></p>
        </form>
        <form name="pce_add_post_type_form" action="<?php print '?page=' . basename(__FILE__); ?>" method="post">
        <h4><?php _e('Add post-type-specific moderators', 'peters_collaboration_emails'); ?></h4>
            <?php
            $post_types = get_post_types( '', 'objects');
            print __( 'Post type:', 'peters_collaboration_emails' ) . ' <select name="post_types" id="post_types">';
            print '<option value="0">----------</option>';
            foreach( $post_types as $post_type )
            {
                // Don't show revisions, attachments, or navigation menu items, as they're not traditional posts
                if( ! in_array( $post_type->name, array( 'revision', 'attachment', 'nav_menu_item' ) ) )
                {
                    print '<option value="' . $post_type->name . '">' . $post_type->label . '</option>';
                }
            }
            print '</select><br /><br />';
            print __( 'Taxonomy:', 'peters_collaboration_emails' ) . ' <select name="taxonomy_types" id="taxonomy_types">';
            print '</select><br /><br />';
            
            print __( 'Term:', 'peters_collaboration_emails' ) . ' <select name="taxonomy_terms" id="taxonomy_terms">';
            print '</select>';
            print '<input name="taxonomy_term" id="taxonomy_term" style="display: none;" type="text" width="30" maxlength="50" />';
            print '<p>' . __('Add moderator:', 'peters_collaboration_emails') . ' <select name="add_post_type_rule_mod" id="post_type_rule_mod_add">';
            print '<option value="-1"></option>';
            print pce_usersoptions( array(), 'moderators' );
            print '</select></p><p id="pce_post_type_rule_mod_add">E-mail: <input type="text" name="pce_post_type_rule_mod_add" width="30" maxlength="90" /></p>';
            ?>
        <p class="submit"><input type="submit" name="pce_add_post_type_submit" value="<?php _e('Update', 'peters_collaboration_emails'); ?>" /></p>
        </form>        
    </div>

    <script type="text/javascript">
        jQuery( document ).ready( function()
        {
            // Functionality to hide the "Other" field as necessary
            if( jQuery( '#adddefaultmod' ).val() != 'other' )
            {
                jQuery( '#pce_adddefaultmod' ).hide();
            }
            if( jQuery( '#groupmodadd' ).val() != 'other' )
            {
                jQuery( '#pce_groupmodadd' ).hide();
            }
            if( jQuery( '#post_type_rule_mod_add' ).val() != 'other' )
            {
                jQuery( '#pce_post_type_rule_mod_add' ).hide();
            }
            jQuery( '[id^=post_type_mod_add]' ).each( function()
            {
                var textareaID = '#pce_' + jQuery( this ).attr( 'id' );
                textareaID = textareaID.replace("[", "\\[");
                textareaID = textareaID.replace("]", "\\]");
                if( jQuery( this ).val() != 'other' )
                {
                    jQuery( textareaID ).hide();
                }
            });
            jQuery( '[id^=usermodadd]' ).each( function()
            {
                var textareaID = '#pce_' + jQuery( this ).attr( 'id' );
                textareaID = textareaID.replace("[", "\\[");
                textareaID = textareaID.replace("]", "\\]");
                if( jQuery( this ).val() != 'other' )
                {
                    jQuery( textareaID ).hide();
                }
            });
            jQuery( '#adddefaultmod' ).change( function()
            {
                addMod( jQuery( this ) );
            });
            jQuery( '#groupmodadd' ).change( function()
            {
                addMod( jQuery( this ) );
            });
            jQuery( '#post_type_rule_mod_add' ).change( function()
            {
                addMod( jQuery( this ) );
            });
            jQuery( '[id^=usermodadd]' ).change( function()
            {
                addMod( jQuery( this ) );
            });
            jQuery( '[id^=post_type_mod_add]' ).change( function()
            {
                addMod( jQuery( this ) );
            });
            
            // AJAX for post types
            jQuery( '#post_types' ).change( function()
            {
                var data = {
                        post_type: jQuery( this ).val(),
                        action: 'get_post_type_taxonomies'
                };
                
                jQuery( '#taxonomy_types' ).empty();
                jQuery( '#taxonomy_terms' ).empty();
                jQuery( '#taxonomy_term' ).val( '' );
                jQuery( '#taxonomy_terms' ).show();
                jQuery( '#taxonomy_term' ).hide();
                jQuery.post( ajaxurl, data, function( response )
                {
                    var taxonomies = jQuery.parseJSON( response );
                    if( '0' != taxonomies )
                    {
                        jQuery( '#taxonomy_types' ).append( '<option selected="selected" value="all"><?php _e( 'All', 'peters_collaboration_emails' ); ?></option>' );
                        jQuery.each( taxonomies, function( index, item )
                        {
                            jQuery( '#taxonomy_types' ).append( '<option value="' + item.name + '">' + item.label + '</option>' );
                        });
                    }
                });
            });
            jQuery( '#taxonomy_types' ).change( function()
            {
                var data = {
                        taxonomy: jQuery( this ).val(),
                        action: 'get_taxonomy_terms'
                };
                
                jQuery( '#taxonomy_terms' ).empty();
                jQuery.post( ajaxurl, data, function( response )
                {
                    var taxonomy_terms = jQuery.parseJSON( response );
                    if( '1' == taxonomy_terms )
                    {
                        jQuery( '#taxonomy_terms' ).hide();
                        jQuery( '#taxonomy_term' ).show();
                    }
                    else if( '0' != taxonomy_terms )
                    {
                        jQuery( '#taxonomy_terms' ).show();
                        jQuery( '#taxonomy_term' ).hide();
                        jQuery.each( taxonomy_terms, function( index, item )
                        {
                            jQuery( '#taxonomy_terms' ).append( '<option value="' + item.id + '">' + item.name + '</option>' );
                        });
                        jQuery( '#taxonomy_terms' ).append( '<option value="-1">--- <?php _e( 'Manual', 'peters_collaboration_emails' ); ?> ---</option>' )
                    }
                });
            });
            jQuery( '#taxonomy_terms' ).change( function()
            {
                if( -1 == jQuery( this ).val() )
                {
                    jQuery( '#taxonomy_term' ).show();
                }
                else
                {
                    jQuery( '#taxonomy_term' ).val( '' );
                    jQuery( '#taxonomy_term' ).hide();
                }
            });
        });
        function addMod( htmlElement )
        {
            // Escape selectors for jQuery
            var textareaID = '#pce_' + jQuery( htmlElement ).attr( 'id' );
            textareaID = textareaID.replace("[", "\\[");
            textareaID = textareaID.replace("]", "\\]");
            if( 'other' == jQuery( htmlElement ).val() )
            {
                jQuery( textareaID ).show();
            }
            else
            {
                jQuery( textareaID ).hide();
            }
        }
        
    </script>
<?php
}

// This class should eventually hold all helper functions
class pceFunctionCollection
{

    // Ajax function to return JSON-encoded taxonomies for a given post type
    function pce_get_post_type_taxonomies()
    {
        $post_type = $_POST['post_type'];
        if( post_type_exists( $post_type ) )
        {
            $taxonomies = get_taxonomies( array( 'object_type' => array( $post_type ) ), 'objects' );
            if( $taxonomies )
            {
                $taxonomy_array = array();
                foreach( $taxonomies as $taxonomy )
                {
                    // Skip a few built-in taxonomy types for now, as we're not going to support them
                    if( ! in_array( $taxonomy->name, array( 'nav_menu', 'link_category', 'post_format' ) ) )
                    {
                        if( '' == $taxonomy->label )
                        {
                            $taxonomy_label = $taxonomy->name;
                        }
                        else
                        {
                            $taxonomy_label = $taxonomy->label;
                        }
                        
                        $taxonomy_array[] = array( 'name' => $taxonomy->name,
                                                   'label' => $taxonomy_label );
                    }
                }
                print json_encode( $taxonomy_array );
                die();
            }
        }
        print '0';
        die();
    }

    // Ajax function to return JSON-encoded terms for a given taxonomy
    function pce_get_taxonomy_terms()
    {
        $taxonomy = $_POST['taxonomy'];
        if( taxonomy_exists( $taxonomy ) )
        {
            $terms = get_terms( $taxonomy, array( 'hide_empty' => false ) );
            if( $terms )
            {
                $terms_array = array();
                foreach( $terms as $term )
                {
                    $terms_array[] = array( 'id' => $term->term_id, 'name' => $term->name );
                }
                print json_encode( $terms_array );
                die();
            }
            else
            {
                print '1';
                die();
            }
        }
        print '0';
        die();
    }
    function pce_add_post_type_submit()
    {
        global $wpdb, $pce_db_collabrules;

        // ----------------------------------
        // Process a new post type rule submission
        // ----------------------------------

        if( -1 != $_POST['add_post_type_rule_mod'] )
        {
            $post_type = $_POST['post_types'];
            $taxonomy_type = $_POST['taxonomy_types'];
            if( '' != trim( $_POST['taxonomy_term'] ) )
            {
                $taxonomy_term = trim( $_POST['taxonomy_term'] );
            }
            elseif( 'all' == $taxonomy_type )
            {
                $taxonomy_term = '';
            }
            else
            {
                $taxonomy_term = $_POST['taxonomy_terms'];
            }
            
            $post_type_mod = $_POST['add_post_type_rule_mod'];

            // Check: Is this taxonomy available for this post type?
            $post_type_taxonomies = get_taxonomies( array( 'object_type' => array( $post_type ) ), 'names' );
            if( ! in_array( $taxonomy_type, $post_type_taxonomies ) && 'all' != $taxonomy_type )
            {
                return array( 'error', __( '**** ERROR: That taxonomy type does not exist for that post type ****', 'peters_collaboration_emails' ) );
            }
            
            // No check if taxonomy term is available for this taxonomy because you can often have freeform terms
            $taxonomy_term = substr( $taxonomy_term, 0, 255 );
            
            // Check to make sure that this rule doesn't already exist with the same post type, taxonomy type, and taxonomy term
            // It might still exist if you had first submitted the term freeform and now you're submitting after the term was officially added and vice versa
            // Possible TODO: Check for both the ID and the text version of the term
            $rule_exists = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $pce_db_collabrules
                                                            WHERE post_type = %s
                                                            AND taxonomy = %s
                                                            AND term = %s;"
                                                            , $post_type, $taxonomy_type, $taxonomy_term
                                                         ) );
            if( $rule_exists )
            {
                return array( 'error', __( '**** ERROR: That post type rule already exists. Please add or remove moderators to the existing rule. ****', 'peters_collaboration_emails' ) );
            }
            
            // Check the added moderator (admin, user ID, or e-mail address)
            // Check that it is a valid user
            if( is_numeric( $post_type_mod ) )
            {
                $pce_validuser = get_userdata( $post_type_mod );
                if( !$pce_validuser )
                {
                    return array( 'error', __( '**** ERROR: Invalid new moderator user ID ****', 'peters_collaboration_emails' ) );
                }
                $post_type_mod = intval( $post_type_mod );
            }
                
            // If the dropdown equals "other" then look for content in pce_post_type_rule_mod_add, which had better be an e-mail address
            elseif( 'other' == $post_type_mod && is_email( $_POST['pce_post_type_rule_mod_add'] ) )
            {
                $post_type_mod = $_POST['pce_post_type_rule_mod_add'];
            }
            elseif( 'admin' != $post_type_mod )
            {
                return array( 'error', __( '**** ERROR: Invalid new moderator submitted ****', 'peters_collaboration_emails' ) );
            }

            $post_type_mod_serialized = serialize( array( $post_type_mod ) );
            $pce_add_post_type_mod_success = $wpdb->insert(
                $pce_db_collabrules,
                array(   'post_type'  => $post_type
                       , 'taxonomy'   => $taxonomy_type
                       , 'term'       => $taxonomy_term
                       , 'moderators' => $post_type_mod_serialized
                      )
                );

            if( $pce_add_post_type_mod_success )
            {
                return array( 'success', sprintf( __( 'New moderator added for the post type rule.', 'peters_collaboration_emails' ) ) );
            }
            else
            {
                return array( 'error', __( '**** ERROR: Unknown query error when adding a new moderator for the post type rule ****', 'peters_collaboration_emails' ) );
            }
        }
        
        else
        {
            return array( 'error', __( '**** ERROR: No moderator was submitted for the post type rule ****', 'peters_collaboration_emails' ) );
        }

        // We've made it this far, so nothing to do!
        return false;
    }
    
    // Edit post type moderators
    function pce_post_type_submit()
    {
        global $wpdb, $pce_db_collabrules;

        // ----------------------------------
        // Process the post-type-specific moderator changes
        // ----------------------------------
        
        $updated = false;
        $pce_post_type_mods = $_POST['pce_post_type_mod']; // An array of moderators for each post type rule (contains User IDs, "admin" or strictly e-mail addresses)
        $pce_rule_ids = $_POST['pce_rule_id']; // An array of post type rules to be updated
        $pce_num_submits = array_keys( (array) $pce_rule_ids );

        if( $pce_num_submits )
        {
            foreach( $pce_num_submits as $pce_num_submit )
            {
                $pce_post_type_mods_update = array();
                $pce_post_type_mod = $pce_post_type_mods[$pce_num_submit];
                $pce_rule_id = intval( $pce_rule_ids[$pce_num_submit] );
                
                // Does this post type rule exist?
                $post_type_rule_exists = pceFunctionCollection::post_type_rule_exists( $pce_rule_id );
                
                if( ! $post_type_rule_exists )
                {
                    return array( 'error', sprintf(__('**** ERROR: Post type rule with ID of %d does not exist ****', 'peters_collaboration_emails'), $pce_rule_id ) );
                }
                if( $pce_post_type_mod )
                {
                    $pce_post_type_mod_update = pce_mod_array( $pce_post_type_mod, $_POST['add_post_type_mod'][$pce_num_submit], $_POST['pce_post_type_mod_add'][$pce_num_submit] );

                    // Nicely scrubbed array of mods to serialize
                    if( is_array( $pce_post_type_mod_update ) )
                    {
                        $pce_post_type_mod_serialized = serialize( $pce_post_type_mod_update );
                    }
                    // It returns an error
                    else
                    {
                        return array( 'error', $pce_post_type_mod_update );
                    }
                    
                    $pce_post_type_mod_success = $wpdb->update(
                                                                $pce_db_collabrules
                                                                , array( 'moderators' => $pce_post_type_mod_serialized )
                                                                , array( 'rule_id' => $pce_rule_id )
                                                               );
                    if( $pce_post_type_mod_success )
                    {
                        $updated = true;
                    }
                }
                else
                {
                    return array( 'error', __( 'You must have at least one default moderator for each rule. Otherwise, delete the rule.', 'peters_collaboration_emails' ) );
                }
            }
        }
        
        // We've made it this far, so success!
        if( $updated )
        {
            return array( 'success', __( 'Moderators for the post type rules updated.', 'peters_collaboration_emails' ) );
        }
        else
        {
            return false;
        }
    }
    function pce_delete_post_type_rule( $rule_id )
    {
        global $wpdb, $pce_db_collabrules;
        $rule_id = intval( $rule_id );

        print '<div class="wrap">';
        print '<h2>' . __( 'Delete post type rule', 'peters_collaboration_emails' ) . '</h2>';

        if( pceFunctionCollection::post_type_rule_exists( $rule_id ) )
        {                
            // If they actually wanted to delete the moderators for this category, let them know the result
            if( isset( $_POST['pce_delete_post_type_rule_yes'] ) )
            {
                $wpdb->query( 'DELETE FROM ' . $pce_db_collabrules . ' WHERE rule_id = ' . $rule_id . ' LIMIT 1' );
                print "\n" . '<p>' . sprintf( __( 'Post type rule successfully deleted.', 'peters_collaboration_emails' ) );
                print "\n" . '<p><a href="?page=' . basename(__FILE__) . '">Back</a></p>' . "\n";
            }
            else
            {
                print "\n" . '<p>' . __( 'Are you sure you want to remove this post type rule?', 'peters_collaboration_emails' ) . '</p>';
                print "\n" . '<form method="post" action="?page=' . basename(__FILE__) . '&delete_post_type_rule=' . $rule_id . '">';
                print "\n" . '<p class="submit"><input type="submit" name="pce_delete_post_type_rule_yes" value="' . __( 'Yes', 'peters_collaboration_emails') . '" /></p>';
                print "\n" . '</form>';
                print "\n" . '<form method="post" action="?page=' . basename(__FILE__) . '">';
                print "\n" . '<p class="submit"><input type="submit" value="' . __( 'No, go back', 'peters_collaboration_emails' ) . '" /></p>';
                print "\n" . '</form>';
            }
        }
        else
        {
            print '<p>' . __( 'That post type rule does not exist.', 'peters_collaboration_emails' ) . '</p>';
            print '<p><a href="?page=' . basename(__FILE__) . '">Back</a></p>' . "\n";
        }
        print '</div>';
    }
    function post_type_rule_exists( $rule_id )
    {
        global $wpdb, $pce_db_collabrules;
        
        $exists = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $pce_db_collabrules
                                                            WHERE rule_id = %d"
                                                            , intval( $rule_id )
                                                         ) );
        return $exists;
    }
    function create_post_type_table()
    {
        global $wpdb, $pce_db_collabrules;
        if( $wpdb->get_var( 'SHOW TABLES LIKE \'' . $pce_db_collabrules . '\'' ) != $pce_db_collabrules )
        {
            $sql = "CREATE TABLE $pce_db_collabrules (
                  rule_id int(11) NOT NULL auto_increment,
                  post_type varchar(50) NOT NULL,
                  taxonomy varchar(50) NOT NULL,
                  term varchar(255) NOT NULL,
                  moderators longtext NOT NULL,
                  UNIQUE KEY rule_id (rule_id)
                );";
            $wpdb->query( $sql );
        }
    }
}

add_action( 'wp_ajax_get_post_type_taxonomies', array( 'pceFunctionCollection', 'pce_get_post_type_taxonomies' ) );
add_action( 'wp_ajax_get_taxonomy_terms', array( 'pceFunctionCollection', 'pce_get_taxonomy_terms' ) );


function pce_addoptionsmenu()
{
    global $pce_required_capability;
    add_options_page( __( 'Collaboration e-mails', 'peters_collaboration_emails' ), __( 'Collaboration e-mails', 'peters_collaboration_emails' ), $pce_required_capability, basename( __FILE__ ), 'pce_optionsmenu' );
}

add_action('admin_menu','pce_addoptionsmenu',1);

// Perform upgrade functions
// Some newer operations are duplicated from pce_install() as there's no guarantee that the user will follow a specific upgrade procedure
function pce_upgrade()
{
    global $wpdb, $pce_db_cats, $pce_db_collabrules, $pce_version;

    // Turn version into an integer for comparisons
    $current_version = intval( str_replace( '.', '', get_option( 'pce_version' ) ) );

    if( $current_version < 150 )
    {
        pceFunctionCollection::create_post_type_table();

        if( $wpdb->get_var( 'SHOW TABLES LIKE \'' . $pce_db_cats . '\'' ) == $pce_db_cats )
        {
            // Transfer all category-specific rules
            $category_rules = $wpdb->get_results( 'SELECT catid, moderators FROM ' . $pce_db_cats, OBJECT );
            if( $category_rules )
            {
                foreach( $category_rules as $category_rule )
                {
                    // Add these rules for both posts and pages and let the user delete the post/post rules if not applicable
                    $wpdb->insert( $pce_db_collabrules,
                                   array(   'post_type'  => 'post'
                                          , 'taxonomy'   => 'category'
                                          , 'term'       => $category_rule->catid
                                          , 'moderators' => $category_rule->moderators
                                        ) );
                    $wpdb->insert( $pce_db_collabrules,
                                   array(   'post_type'  => 'page'
                                          , 'taxonomy'   => 'category'
                                          , 'term'       => $category_rule->catid
                                          , 'moderators' => $category_rule->moderators
                                        ) );
                }
            }
            
            // Delete old category table
            $wpdb->query( 'DROP TABLE ' . $pce_db_cats );
        }
        
        // Future versions
        // update_option( 'pce_version', '1.5.0', '', 'no' );
    }
    
    if( $current_version != intval( str_replace( '.', '', $pce_version ) ) )
    {
        // Add the version number to the database
        delete_option( 'pce_version' );
        add_option( 'pce_version', $pce_version, '', 'no' );
    }
}

// Add and remove database tables when installing and uninstalling

function pce_install()
{
    global $wpdb, $pce_db_group, $pce_db_collab, $pce_db_collabrules, $pce_version;

    // Add the table to hold group information and moderator rules
    if($wpdb->get_var('SHOW TABLES LIKE \'' . $pce_db_group . '\'') != $pce_db_group) {
        $sql = 'CREATE TABLE ' . $pce_db_group . ' (
        collabgroup bigint(20) NOT NULL auto_increment,
        moderators longtext NOT NULL,
        groupname varchar(255) NOT NULL,
        KEY collabgroup (collabgroup)
        ) AUTO_INCREMENT=2;';

        $wpdb->query($sql);
    }
    
    // Insert the default moderator rule
    $sql = 'INSERT INTO ' . $pce_db_group . ' (collabgroup, moderators, groupname) VALUES 
    (1, \'a:1:{i:0;s:5:"admin";}\', \'Default\')';
    $wpdb->query($sql);

    // Add the table to hold group - collaborator associations
    if($wpdb->get_var('SHOW TABLES LIKE \'' . $pce_db_collab . '\'') != $pce_db_collab) {
        $sql = 'CREATE TABLE ' . $pce_db_collab . ' (
        groupid bigint(20) NOT NULL,
        writerid bigint(20) NOT NULL,
        UNIQUE KEY groupwriter (groupid, writerid)
        )';
          $wpdb->query($sql);
    }
    
    pceFunctionCollection::create_post_type_table();
    
    pce_upgrade();
}

function pce_uninstall() {
    global $wpdb, $pce_db_group, $pce_db_collab, $pce_db_cats, $pce_db_collabrules;

    if($wpdb->get_var('SHOW TABLES LIKE \'' . $pce_db_group . '\'') == $pce_db_group) {
        $sql = 'DROP TABLE ' . $pce_db_group;
        $wpdb->query($sql);
    }
    if($wpdb->get_var('SHOW TABLES LIKE \'' . $pce_db_collab . '\'') == $pce_db_collab) {
        $sql = 'DROP TABLE ' . $pce_db_collab;
        $wpdb->query($sql);
    }
    if($wpdb->get_var('SHOW TABLES LIKE \'' . $pce_db_cats . '\'') == $pce_db_cats) {
        $sql = 'DROP TABLE ' . $pce_db_cats;
        $wpdb->query($sql);
    }
    if($wpdb->get_var('SHOW TABLES LIKE \'' . $pce_db_collabrules . '\'') == $pce_db_collabrules) {
        $sql = 'DROP TABLE ' . $pce_db_collabrules;
        $wpdb->query($sql);
    }
    delete_option( 'pce_version' );
}

register_activation_hook( __FILE__, 'pce_install' );
register_uninstall_hook( __FILE__, 'pce_uninstall' );

} // This closes that initial check to make sure someone is actually logged in
?>