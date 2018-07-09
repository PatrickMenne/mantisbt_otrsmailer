<?php
/**
 * This include file defines the OtrsMailerPlugin.
 * @package MantisBT OtrsMailer
 * @link https://github.com/isamuxsama/mantisbt_otrsmailer
 *
 * @uses MantisHtmlBuilder.php
 * @uses plugin_api.php
 * @uses email_queue_api.php
 */
require_once( 'core/MantisHtmlBuilder.php' );

class OtrsMailerPlugin extends MantisPlugin {
    
    private $_conf_custom_config_filename = 'OtrsMailerCustomConfig_inc.php';

    function register() {
        $this->name = 'OtrsMailer';                                                 # Proper name of plugin
        $this->description = 'Simple way to pass issues to OTRS via email.';        # Short description of the plugin
        $this->page = 'config_view';                                                # Default plugin page

        $this->version = '1.0';                                                     # Plugin version string
        $this->requires = array(                                                    # Plugin dependencies
            'MantisCore' => '2.5.1'                                                 # Should always depend on an appropriate
                                                                                    # version of MantisBT
        );

        $this->author = 'isamux';                                                   # Author/team name
        $this->contact = 'isamuxsama@gmail.com';                                    # Author/team e-mail address
        $this->url = 'https://github.com/isamuxsama/mantisbt_otrsmailer';           # Support webpage
    }

    function config() {
        $t_config_file_path = __DIR__ . '/' . $this->_conf_custom_config_filename;
       
        if( file_exists( $t_config_file_path ) )
        {
            include_once( $t_config_file_path );
            return $a_OtrsMailerConfig;
        } 
        else 
        {
            return array(
                'otrs_url_main' => 'https://localhost/otrs/index.pl',                                           # url of otrs
                'otrs_mail_to' =>  'myotrs@localhost',                                                          # mail for otrs mailtickets
                'otrs_mail_subject_template' => "<BUG_SUMMARY> [M#<BUG_ID>]",                                   # template for mail subject
                'otrs_mail_body_template' => "" .                                                               # template for mail body
                    "===============================================================================\n" . 
                    "  MantisUrl: <BUG_URL>\n" . 
                    "===============================================================================\n" . 
                    "  Sent by: <MANTIS_CURR_USER_NAME>\n" .
                    "------------------------------------------------------\n" . 
                    "  General Information\n" . 
                    "------------------------------------------------------\n" . 
                    "  Project: <BUG_PROJECT_NAME>\n" . 
                    "  AssignedTo: <BUG_ASSIGNEDTO_USERNAME>\n" . 
                    "  Reporter: <BUG_REPORTER_USERNAME> (<BUG_REPORTER_EMAIL>)\n" . 
                    "  Priority: <BUG_PRIORITY>\n" . 
                    "------------------------------------------------------\n" . 
                    "  Description:\n" . 
                    "------------------------------------------------------\n" . 
                    "<BUG_DESCRIPTION>\n" . 
                    "\n------------------------------------------------------\n" . 
                    "StepsToReproduce:\n" . 
                    "------------------------------------------------------\n" . 
                    "<BUG_STEPS_TO_REPRODUCE>\n" . 
                    "\n------------------------------------------------------\n" . 
                    "Additional Info:\n" . 
                    "------------------------------------------------------\n" . 
                    "<BUG_ADDITIONAL_INFO>\n" .  
                    "\n===============================================================================",
                    
                    'view_page_mail_issue_threshold' => 90,             // user level threshold to allow access to the plugin. Default: administrator.
                    'limit_access_to_users_csv' => '',                  // leave empty for no filtering, otherwise comma separated list of usernames. Default: empty.
                    'add_note_after_mail_sent' => false,                // if true, adds a note to the issue, after a mail was sent. Default: false.
                    'issue_menu_show_add_otrs_open_link' => true,       // customize issue menu: display open otrs link. Default: true.
                    'issue_menu_show_mail_issue_direct_link' => false   // customize issue menu: display "direct" send mail link. Default: false.
                );
        }
    }
    
    function events() {
        return array(
            # function events
            'EVENT_OTRSMAILER_MAIL_GEN' => EVENT_TYPE_CHAIN,
            'EVENT_OTRSMAILER_MAIL_SEND' => EVENT_TYPE_DEFAULT,

            # return data events
            'EVENT_OTRSMAILER_PLUGIN_IS_ENABLED_FOR_CURRENTUSER' => EVENT_TYPE_DEFAULT,
            'EVENT_OTRSMAILER_INFO_PLACEHOLDERS_LIST' => EVENT_TYPE_CHAIN,

             # layouting events
            'EVENT_OTRSMAILER_LAYOUT_WIDGET_HEADER_BEGIN' => EVENT_TYPE_OUTPUT,
            'EVENT_OTRSMAILER_LAYOUT_WIDGET_HEADER_END' => EVENT_TYPE_OUTPUT,
            'EVENT_OTRSMAILER_LAYOUT_WIDGET_TOOLBOX_BEGIN' => EVENT_TYPE_OUTPUT,
            'EVENT_OTRSMAILER_LAYOUT_WIDGET_TOOLBOX_END' => EVENT_TYPE_OUTPUT,
            'EVENT_OTRSMAILER_LAYOUT_WIDGET_BODY_BEGIN' => EVENT_TYPE_OUTPUT,
            'EVENT_OTRSMAILER_LAYOUT_WIDGET_BODY_END' => EVENT_TYPE_OUTPUT,
            
            'EVENT_OTRSMAILER_LAYOUT_BOX_SUCCESS' => EVENT_TYPE_OUTPUT,
            'EVENT_OTRSMAILER_LAYOUT_BOX_FAIL' => EVENT_TYPE_OUTPUT
        );
    }
    
    function hooks() {
        return array(
            # extend issue menu
            'EVENT_MENU_ISSUE' => 'on_extend_menu_issue',

            # function events
            'EVENT_OTRSMAILER_MAIL_GEN' => 'on_mail_issue_generate',
            'EVENT_OTRSMAILER_MAIL_SEND' => 'on_mail_issue_send',

            # return data events
            'EVENT_OTRSMAILER_PLUGIN_IS_ENABLED_FOR_CURRENTUSER' => 'on_plugin_is_enabled_for_currentuser',
            'EVENT_OTRSMAILER_INFO_PLACEHOLDERS_LIST' => 'on_info_placeholder_list',
            
             # layouting events
            'EVENT_OTRSMAILER_LAYOUT_WIDGET_HEADER_BEGIN' => 'on_layout_widget_header_begin',
            'EVENT_OTRSMAILER_LAYOUT_WIDGET_HEADER_END' => 'on_layout_widget_header_end',

            'EVENT_OTRSMAILER_LAYOUT_WIDGET_TOOLBOX_BEGIN' => 'on_layout_widget_toolbox_begin',
            'EVENT_OTRSMAILER_LAYOUT_WIDGET_TOOLBOX_END' => 'on_layout_widget_toolbox_end',

            'EVENT_OTRSMAILER_LAYOUT_WIDGET_BODY_BEGIN' => 'on_layout_widget_body_begin',
            'EVENT_OTRSMAILER_LAYOUT_WIDGET_BODY_END' => 'on_layout_widget_body_end',
            
            'EVENT_OTRSMAILER_LAYOUT_BOX_SUCCESS' => 'on_layout_box_success',
            'EVENT_OTRSMAILER_LAYOUT_BOX_FAIL' => 'on_layout_box_fail'


        );
    }
 

    ###################################################
    # event handlers
    ###################################################
    
    public function on_extend_menu_issue( $p_event, $p_chained_param ) {
        
        if(!$this->is_plugin_enabled_for_current_user())
        {
            return;
        }

        $aLinks = array();

        #array_push($sLinks, MantisHtmlBuilder::Current()->page_generate_link_mail_issue_preview($p_chained_param));
        $aLinks[plugin_lang_get('link_mail_issue_preview')] = plugin_page('mail_issue').'&bug_id='.$p_chained_param.'&action=preview';

        if( plugin_config_get( 'issue_menu_show_mail_issue_direct_link' ) == true ){

            #array_push($aLinks, MantisHtmlBuilder::Current()->page_generate_link_mail_issue_send($p_chained_param) );
            $aLinks[plugin_lang_get('link_mail_issue')] = plugin_page('mail_issue').'&bug_id='.$p_chained_param.'&action=send';
            
        }

        if( plugin_config_get( 'issue_menu_show_add_otrs_open_link' ) == true ){
            
            #array_push($aLinks, MantisHtmlBuilder::Current()->page_generate_link_otrs_main($p_chained_param) );
            $aLinks[plugin_lang_get('link_open_otrs')] = plugin_config_get('otrs_url_main');
            
        }
        
        return $aLinks;
    }
    
    function on_mail_issue_generate( $p_event, $p_chained_param ) {
        return $this->build_mail($p_chained_param);
    }
    
     function on_mail_issue_send( $p_event, $p_chained_param ) {
        return $this->send_mail($p_chained_param);
    }

     function on_layout_box_success( $p_event, $p_chained_param) {
        return MantisHtmlBuilder::Current()->page_generate_box_success($p_chained_param);
     }
     
     function on_layout_box_fail( $p_event, $p_chained_param) {
        return MantisHtmlBuilder::Current()->page_generate_box_fail($p_chained_param);
     }
     
     function on_layout_widget_header_begin( $p_event, $p_chained_param) {
        return MantisHtmlBuilder::Current()->page_generate_widget_header_begin($p_chained_param);
     }
     
     function on_layout_widget_header_end( $p_event, $p_chained_param) {
        return MantisHtmlBuilder::Current()->page_generate_widget_header_end();
     }

     function on_layout_widget_toolbox_begin( $p_event, $p_chained_param) {
        return MantisHtmlBuilder::Current()->page_generate_toolbox_begin();
     }
     
     function on_layout_widget_toolbox_end( $p_event, $p_chained_param) {
        return MantisHtmlBuilder::Current()->page_generate_toolbox_end();
     }

     function on_layout_widget_body_begin( $p_event, $p_chained_param) {
        return MantisHtmlBuilder::Current()->page_generate_widget_body_begin();
     }
     
     function on_layout_widget_body_end( $p_event, $p_chained_param) {
        return MantisHtmlBuilder::Current()->page_generate_widget_body_end();
     }
     
     function on_info_placeholder_list( $p_event, $p_chained_param) {
        return $this->template_list_placeholders();
     }

     function on_plugin_is_enabled_for_currentuser( $p_event, $p_chained_param) {
        return $this->is_plugin_enabled_for_current_user();
     }

    /**
     * Returns true if current user has access to the plugin, otherwise false.
     * @return bool
     */
    function is_plugin_enabled_for_current_user()
    {
        $result = false;

        # check, if current user has sufficient access level
        $result = access_compare_level( access_get_global_level() , plugin_config_get( 'view_page_mail_issue_threshold' ) );

        # check, if plugin is limited to certain users
        # only done, if user has passed access level check.
        if($result == true) {

            $t_config_limit_to_users_csv = plugin_config_get( 'limit_access_to_users_csv' );

            if( trim( $t_config_limit_to_users_csv ) != '' ) {

                $t_username_list = explode(",", $t_config_limit_to_users_csv);

                if(sizeof( $t_username_list ) >= 1 ) {
                    $result = in_array( current_user_get_field('username'), $t_username_list );
                }
            }
        }

        return $result;
    }


    ###################################################
    # mail generate helpers
    ###################################################

    /**
     * Sends email. Build EmailData instance using the data in $p_mail.
     * @param array $p_mail  Array containing all data for the mail (mailto, subject, body).
     * @return array
     */
    private function send_mail($p_mail) {
        $t_email_data = new EmailData;
        $t_email_data->email = $p_mail['mailto'];
        $t_email_data->subject = $p_mail['subject'];
        $t_email_data->body = $p_mail['body'];
        $t_email_data->metadata['charset'] = 'utf-8';  

        return email_send( $t_email_data );
    }

    /**
     * Builds array with mail data (mailto, subject, body) for a given bugid.
     * @param object $p_bug_id  bug id.
     * @return array
     */
    function build_mail( $p_bug_id ) {
        
        $t_bug_id = $p_bug_id;
        $t_bug = bug_get( $t_bug_id, true );
        
        return array(
                'mailto' => $this->build_mail_to($t_bug),
                'subject' => $this->build_mail_subject($t_bug),
                'body' => $this->build_mail_body($t_bug)
                );
    }

    /**
     * Builds mail recipient for given bug.
     * @param object $p_bug  bug instance.
     * @return string
     */
    function build_mail_to( $p_bug ) {
        return plugin_config_get('otrs_mail_to');
    }

    /**
     * Builds mail subject for given bug.
     * @param object $p_bug  bug instance.
     * @return string
     */
    function build_mail_subject( $p_bug ){
        return sprintf( $this->template_replace_placeholdes(plugin_config_get('otrs_mail_subject_template'), $p_bug));
    }

    /**
     * Builds mail body for given bug.
     * @param object $p_bug  bug instance.
     * @return string
     */
    function build_mail_body( $p_bug ) {

        # get template and append "generated by plugin" 
        $t_template = plugin_config_get('otrs_mail_body_template');
        $t_template .= "\nGenerated by Plugin <PLUGIN_NAME> <PLUGIN_VERSION>.";
        
        return $this->template_replace_placeholdes($t_template, $p_bug);
    }

    /**
     * Takes mail template and replaces placeholders with actual values from the passed bug.
     * @param string $p_template  mailbody template.
     * @param object $p_bug  bug instance.
     * @return string
     */
    function template_replace_placeholdes( $p_template, $p_bug ) {
    
        $p_template = str_replace ( '<PLUGIN_NAME>', $this->name, $p_template );
        $p_template = str_replace ( '<PLUGIN_VERSION>', $this->version, $p_template );
        
        $p_template = str_replace ( '<MANTIS_CURR_USER_NAME>', user_get_field(auth_get_current_user_id(), 'username'), $p_template );

        $p_template = str_replace ( '<BUG_ID>', $p_bug->id, $p_template );
        $p_template = str_replace ( '<BUG_URL>', config_get( 'path' ).string_get_bug_view_url($p_bug->id), $p_template );
        $p_template = str_replace ( '<BUG_PROJECT_NAME>', project_get_name( $p_bug->project_id ), $p_template );
        $p_template = str_replace ( '<BUG_ASSIGNEDTO_USERNAME>', user_get_name( $p_bug->handler_id), $p_template );
        $p_template = str_replace ( '<BUG_REPORTER_USERNAME>', user_get_name( $p_bug->reporter_id ), $p_template );
        $p_template = str_replace ( '<BUG_REPORTER_EMAIL>', user_get_email ( $p_bug->reporter_id ), $p_template );
        $p_template = str_replace ( '<BUG_PRIORITY>', get_enum_element( 'priority', $p_bug->priority ), $p_template );
        $p_template = str_replace ( '<BUG_DESCRIPTION>', $p_bug->description, $p_template );
        $p_template = str_replace ( '<BUG_STEPS_TO_REPRODUCE>', $p_bug->steps_to_reproduce, $p_template );
        $p_template = str_replace ( '<BUG_ADDITIONAL_INFO>', $p_bug->additional_information, $p_template );
        $p_template = str_replace ( '<BUG_SUMMARY>', $p_bug->summary, $p_template );
        
        return $p_template;
    }

    /**
     * Returns list of available placeholders as array. 
     * Key contains name of placeholder, value is i18n descpription.
     * @return array
     */
    function template_list_placeholders() {
        $t_a =  array(
                'PLUGIN_NAME'=> plugin_lang_get('placeholder_PLUGIN_NAME'),
                'PLUGIN_VERSION'=> plugin_lang_get('placeholder_PLUGIN_VERSION'),
                'MANTIS_CURR_USER_NAME'=> plugin_lang_get('placeholder_PLUGIN_MANTIS_CURR_USER_NAME')
                ,
                'BUG_ID'=> plugin_lang_get('placeholder_PLUGIN_BUG_ID')
                ,
                'BUG_URL'=> plugin_lang_get('placeholder_PLUGIN_BUG_URL')
                ,
                'BUG_PROJECT_NAME'=> plugin_lang_get('placeholder_PLUGIN_BUG_PROJECT_NAME')
                ,
                'BUG_ASSIGNEDTO_USERNAME'=> plugin_lang_get('placeholder_PLUGIN_BUG_ASSIGNEDTO_USERNAME'),
                'BUG_REPORTER_USERNAME'=>  plugin_lang_get('placeholder_PLUGIN_BUG_REPORTER_USERNAME'),
                'BUG_REPORTER_EMAIL'=> plugin_lang_get('placeholder_PLUGIN_BUG_REPORTER_EMAIL'),
                'BUG_PRIORITY'=> plugin_lang_get('placeholder_PLUGIN_BUG_PRIORITY'),
                'BUG_DESCRIPTION'=> plugin_lang_get('placeholder_PLUGIN_BUG_DESCRIPTION'),
                'BUG_STEPS_TO_REPRODUCE'=> plugin_lang_get('placeholder_PLUGIN_BUG_STEPS_TO_REPRODUCE'),
                'BUG_ADDITIONAL_INFO'=> plugin_lang_get('placeholder_PLUGIN_BUG_ADDITIONAL_INFO'),
                'BUG_SUMMARY'=> plugin_lang_get('placeholder_PLUGIN_BUG_SUMMARY')
            );

        # sort by key
        ksort($t_a);

        return $t_a;
    }
      
}
?>