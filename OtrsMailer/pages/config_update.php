<?php

# validate form security token
$s_form_security_token = 'plugin_OTRSMailer_config_update';
form_security_validate( $s_form_security_token );

# get post values
$f_reset = gpc_get_bool( 'reset', false );

$f_otrs_url_main = trim(gpc_get_string( 'otrs_url_main' ));
$f_otrs_mail_to = trim(gpc_get_string( 'otrs_mail_to' ));
$f_otrs_mail_subject_template = trim(gpc_get_string( 'otrs_mail_subject_template' ));
$f_otrs_mail_body_template= (gpc_get_string( 'otrs_mail_body_template' ));
$f_view_page_mail_issue_threshold = trim(gpc_get_string( 'view_page_mail_issue_threshold' ));
$f_add_note_after_mail_sent = isset($_POST['add_note_after_mail_sent']);//gpc_get_bool('add_note_after_mail_sent') , false );
$f_issue_menu_show_add_otrs_open_link = isset($_POST['issue_menu_show_add_otrs_open_link']);
$f_issue_menu_show_mail_issue_direct_link = isset($_POST['issue_menu_show_mail_issue_direct_link']);
$f_limit_access_to_users_csv = trim(gpc_get_string( 'limit_access_to_users_csv' ));

# if reset was checked, clear custom config values
if( $f_reset == true ){
    config_reset();
} else {    
    # validate input
    if( is_blank( $f_otrs_mail_to ) ) {
        error_parameters( 'otrs_mail_to' );
        trigger_error( ERROR_EMPTY_FIELD, ERROR );
    }
    
    if( is_blank( $f_otrs_url_main ) ) {
        error_parameters( 'otrs_url_main' );
        trigger_error( ERROR_EMPTY_FIELD, ERROR );
    }
    
    if( is_blank( $f_otrs_mail_subject_template ) ) {
        error_parameters( 'otrs_mail_subject_template' );
        trigger_error( ERROR_EMPTY_FIELD, ERROR );
    }
    
    if( is_blank( $f_view_page_mail_issue_threshold ) ) {
        error_parameters( 'view_page_mail_issue_threshold' );
        trigger_error( ERROR_EMPTY_FIELD, ERROR );
    }

    # save config values. only store them if values differ from currently stored values
    config_set_if_changed( 'otrs_url_main', $f_otrs_url_main );
    config_set_if_changed( 'otrs_mail_to', $f_otrs_mail_to );
    config_set_if_changed( 'otrs_mail_subject_template', $f_otrs_mail_subject_template );
    config_set_if_changed( 'otrs_mail_body_template', $f_otrs_mail_body_template );
    config_set_if_changed( 'view_page_mail_issue_threshold', $f_view_page_mail_issue_threshold );
    config_set_if_changed( 'add_note_after_mail_sent', $f_add_note_after_mail_sent );
    config_set_if_changed( 'issue_menu_show_add_otrs_open_link', $f_issue_menu_show_add_otrs_open_link );  
    config_set_if_changed( 'issue_menu_show_mail_issue_direct_link', $f_issue_menu_show_mail_issue_direct_link );  
    config_set_if_changed( 'limit_access_to_users_csv', $f_limit_access_to_users_csv );  
    
}

# reset form security
form_security_purge( $s_form_security_token );

# back to config view
print_successful_redirect( plugin_page( 'config_view', true ) );

###################################################
# helper functions
###################################################

/**
 * Only stores config key, if value differs from current value.
 * @param string $p_config_key  key/name of the configvalue.
 * @param string $p_new_value   new value for config key.
 * @return void
 */
function config_set_if_changed( $p_config_key, $p_new_value ) {
    if ( $p_new_value != plugin_config_get( $p_config_key ) ) {
        plugin_config_set( $p_config_key, $p_new_value );
    }
}

/**
 * Reset all custom stored konfig values in database.
 * @return void
 */
function config_reset()
{
    plugin_config_delete( 'otrs_url_main' );
    plugin_config_delete( 'otrs_mail_to' );
    plugin_config_delete( 'otrs_mail_subject_template' );
    plugin_config_delete( 'otrs_mail_body_template' );
    plugin_config_delete( 'view_page_mail_issue_threshold' );
    plugin_config_delete( 'add_note_after_mail_sent' );
    plugin_config_delete( 'issue_menu_show_add_otrs_open_link' );  
    plugin_config_delete( 'issue_menu_show_mail_issue_direct_link' ); 
    plugin_config_delete( 'limit_access_to_users_csv' ); 
}

?>