<?php

    $s_form_security_token ='plugin_OtrsMailer_mail_issue';
    
    # params for form
    $f_bug_id = null;
    $f_action = null;
    $a_mail = null;
    
    # TODO: validate content of POST/GET params
    if (!empty($_POST))
    {
        form_security_validate( $s_form_security_token );

        $f_bug_id = trim(gpc_get_string('bug_id'));
        $f_action = trim(gpc_get_string('action'));
        
        $a_mail = event_signal( 'EVENT_OTRSMAILER_MAIL_GEN', $f_bug_id);
        $a_mail['mailto'] = trim(gpc_get_string('mailTo'));
    }
    else
    {
        $f_bug_id = trim(gpc_get_string('bug_id'));
        $f_action = trim(gpc_get_string('action'));
        $a_mail = event_signal( 'EVENT_OTRSMAILER_MAIL_GEN', $f_bug_id);
        
        # optional get param mailto 
        $a_mail['mailto'] = isset($_GET['mailto']) ? trim(gpc_get_string('mailto')) : $a_mail['mailto'];
    }

?>
<?php

# print header mantis
layout_page_header(plugin_get_current());
layout_page_begin();

# check required access level for page
$t_res = event_signal('EVENT_OTRSMAILER_PLUGIN_IS_ENABLED_FOR_CURRENTUSER');
if(!$t_res[plugin_get_current()]['on_plugin_is_enabled_for_currentuser']) {
    access_denied();
}
 
# print header
event_signal('EVENT_OTRSMAILER_LAYOUT_WIDGET_HEADER_BEGIN', plugin_get_current() .' | ' . plugin_lang_get( 'link_send_mail' ));
event_signal( 'EVENT_OTRSMAILER_LAYOUT_WIDGET_TOOLBOX_BEGIN' );
    print_small_button( string_get_bug_view_url( $f_bug_id ), lang_get( 'back_to_bug_link' ) );
    echo '&nbsp;';
    print_small_button(plugin_config_get('otrs_url_main'), plugin_lang_get( 'link_open_otrs'));
event_signal( 'EVENT_OTRSMAILER_LAYOUT_WIDGET_TOOLBOX_END' );
event_signal( 'EVENT_OTRSMAILER_LAYOUT_WIDGET_BODY_BEGIN' );

switch ($f_action) {
    case 'preview':
        # print submit form 
        echo '<form class="form-inline noprint" action="' . plugin_page('mail_issue') . '" method="POST">' . 
        form_security_field( $s_form_security_token ) .
        '<input type="hidden" name="bug_id" value="'.$f_bug_id.'" />
        <input type="hidden" name="action" value="send" />
        <label class="inline" for="mailTo"> ' . plugin_lang_get('email_to') . ':</label>&nbsp;&nbsp;</label>
        <input class="input-sm" size="80" id="mailTo" name="mailTo" value= "' .$a_mail['mailto'].'" />&nbsp;&nbsp;
        <input class="btn btn-primary btn-sm btn-white btn-round" type="submit" value="' . plugin_lang_get( 'link_send_mail' ) . '">&nbsp;&nbsp;';
        echo '</form>';
        event_signal( 'EVENT_OTRSMAILER_LAYOUT_WIDGET_BODY_END' );
        echo event_signal('EVENT_OTRSMAILER_LAYOUT_WIDGET_HEADER_END');

        # print preview mail widget
        echo event_signal('EVENT_OTRSMAILER_LAYOUT_WIDGET_HEADER_BEGIN', plugin_lang_get( 'preview' ));
        event_signal( 'EVENT_OTRSMAILER_LAYOUT_WIDGET_BODY_BEGIN' );
        echo    '<h5>' . plugin_lang_get('email_to') . '</h5><pre>'. $a_mail['mailto'] . '</pre>
                 <h5>' . plugin_lang_get('subject') . '</h5><pre>' . $a_mail['subject'] . '</pre>
                 <h5>' . plugin_lang_get('body') . '</h5><pre>' .  $a_mail['body'] . '</pre>';
        break;
    case 'send':
	
        # send email
        $t_result = event_signal( 'EVENT_OTRSMAILER_MAIL_SEND', array($a_mail));

        # show result
        if( !$t_result[plugin_get_current()]['on_mail_issue_send'] ) {
            
            $htmlMessage = sprintf(
                '<strong>%s</strong> - %s: %s. %s',
                plugin_lang_get( 'mail' ),
                plugin_lang_get('msg_err_mail_sent_failed'),
                $a_mail['mailto'],
                plugin_lang_get('msg_err_check_mail_settings'));
             
             
            echo event_signal( 'EVENT_OTRSMAILER_LAYOUT_BOX_FAIL', $htmlMessage);
        
        } else {

            $htmlMessage =  sprintf(
                '<strong>%s</strong> -  %s.<br/>' ,
                plugin_lang_get( 'mail' ),
                plugin_lang_get('msg_succ_mail_sent')
                ); 
                
            event_signal( 'EVENT_OTRSMAILER_LAYOUT_BOX_SUCCESS', $htmlMessage);
            
            if( plugin_config_get( 'add_note_after_mail_sent' ) == true ){
                $t_bugnote_id = bugnote_add( $f_bug_id, plugin_lang_get('msg_succ_note_mail_sent'), '0:00' , false, 0, '', null, false );
            }
        }

        form_security_purge( $s_form_security_token );
        break;

    default:
        event_signal( 'EVENT_OTRSMAILER_LAYOUT_BOX_FAIL', '<strong>Unknown action:</strong> - action "' . $f_action . '" is not supported.');

}

# close last widget
event_signal( 'EVENT_OTRSMAILER_LAYOUT_WIDGET_BODY_END' );
event_signal('EVENT_OTRSMAILER_LAYOUT_WIDGET_HEADER_END');  

# print footer mantis
layout_page_end();

?>

 