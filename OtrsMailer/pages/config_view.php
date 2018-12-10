<?php

# print default mantis page header
layout_page_header(plugin_get_current() . ' - ' . plugin_lang_get('configuration'));
layout_page_begin();

# only allow access if admin
if( !user_is_administrator(auth_get_current_user_id()) ) {
    access_denied();
    return;
}

# read config values to vars
$t_otrs_url_main = plugin_config_get( 'otrs_url_main' );
$t_otrs_mail_to = plugin_config_get( 'otrs_mail_to' );
$t_otrs_mail_subject_template = plugin_config_get( 'otrs_mail_subject_template' );
$t_otrs_mail_body_template = plugin_config_get( 'otrs_mail_body_template' );
$t_view_page_mail_issue_threshold = plugin_config_get( 'view_page_mail_issue_threshold' );
$t_add_note_after_mail_sent = plugin_config_get( 'add_note_after_mail_sent' );
$t_issue_menu_show_add_otrs_open_link = plugin_config_get( 'issue_menu_show_add_otrs_open_link' );
$t_issue_menu_show_mail_issue_direct_link = plugin_config_get( 'issue_menu_show_mail_issue_direct_link' );
$t_limit_access_to_users_csv = plugin_config_get( 'limit_access_to_users_csv' );
$t_issue_menu_show_otrs_search_link = plugin_config_get( 'issue_menu_show_otrs_search_link' );
$t_issue_id_tag_template = plugin_config_get( 'issue_id_tag_template' );
?>

<?php
event_signal( 'EVENT_OTRSMAILER_LAYOUT_WIDGET_HEADER_BEGIN' , plugin_get_current() .' | ' . plugin_lang_get('configuration') );
event_signal( 'EVENT_OTRSMAILER_LAYOUT_WIDGET_BODY_BEGIN' );
?>

<form action="<?php echo plugin_page( 'config_update' ) ?>" method="post">
    <?php echo form_security_field( 'plugin_OTRSMailer_config_update' ) ?>
    <table class="table table-bordered table-condensed">
        <tr <?php echo helper_alternate_class() ?>>
            <td class="category"><?php echo plugin_lang_get('issue_id_tag_template'); ?></td>
            <td>
                <input name="issue_id_tag_template" size="100" value="<?php echo string_attribute( $t_issue_id_tag_template); ?>"/>
            </td>
        </tr>
        <tr <?php echo helper_alternate_class() ?>>
            <td class="category"><?php echo plugin_lang_get('otrs_url'); ?></td>
            <td>
                <input name="otrs_url_main" size="100" value="<?php echo string_attribute( $t_otrs_url_main); ?>"/>
            </td>
        </tr>
        <tr <?php echo helper_alternate_class() ?>>
            <td class="category"><?php echo plugin_lang_get('otrs_mail_to'); ?></td>
            <td>
                <input name="otrs_mail_to" size="100" value="<?php echo string_attribute( $t_otrs_mail_to); ?>"/>
            </td>
        </tr>
        <tr <?php echo helper_alternate_class() ?>>
            <td class="category"><?php echo plugin_lang_get('otrs_mail_subject_template'); ?></td>
            <td>
                <input name="otrs_mail_subject_template" size="100" value="<?php echo string_attribute( $t_otrs_mail_subject_template); ?>"/>
            </td>
        </tr>
        <tr <?php echo helper_alternate_class() ?>>
            <td class="category"><?php echo plugin_lang_get('otrs_mail_body_template'); ?></td>
            <td>
                <br/>
                <textarea name="otrs_mail_body_template" cols="80" rows="30"><?php echo string_attribute( $t_otrs_mail_body_template);?></textarea>
            </td>
        </tr>
        <tr <?php echo helper_alternate_class() ?>>
        <td class="category"><?php echo plugin_lang_get('view_page_mail_issue_threshold'); ?></td>
            <td>
                <select name="view_page_mail_issue_threshold">
                <?php 
                    print_enum_string_option_list( 'access_levels', string_attribute( $t_view_page_mail_issue_threshold ) );
                ?>
                </select>
            </td>
        </tr>
        <tr <?php echo helper_alternate_class() ?>>
        <td class="category"><?php echo plugin_lang_get('limit_access_to_users_csv'); ?></td>
            <td>
                <input name="limit_access_to_users_csv" size="100" value="<?php echo string_attribute( $t_limit_access_to_users_csv); ?>"/><br/>
                <span><?php echo plugin_lang_get('limit_access_to_users_csv_explanation'); ?></span>
            </td>
        </tr>
        <tr <?php echo helper_alternate_class() ?>>
            <td class="category"><?php echo plugin_lang_get('add_note_to_issue'); ?></td>
            <td>
                <input type="checkbox" name="add_note_after_mail_sent" <?php if($t_add_note_after_mail_sent == 1) {echo ' checked ';} else {echo '';} ?> />&nbsp;<span><?php echo plugin_lang_get('add_note_to_issue_explanation'); ?></span>
            </td>
        </tr>
        <tr <?php echo helper_alternate_class() ?>>
            <td class="category"><?php echo plugin_lang_get('show_open_otrs_link'); ?></td>
            <td>
                <input type="checkbox" name="issue_menu_show_add_otrs_open_link" <?php if($t_issue_menu_show_add_otrs_open_link == 1) {echo ' checked ';} else {echo '';} ?> /></span>
            </td>
        </tr>
        <tr <?php echo helper_alternate_class() ?>>
            <td class="category"><?php echo plugin_lang_get('show_mail_direct_link'); ?></td>
            <td>
                <input type="checkbox" name="issue_menu_show_mail_issue_direct_link" <?php if($t_issue_menu_show_mail_issue_direct_link == 1) {echo ' checked ';} else {echo '';} ?> /></span>
            </td>
        </tr>
        <tr <?php echo helper_alternate_class() ?>>
            <td class="category"><?php echo plugin_lang_get('show_open_otrs_search_title'); ?></td>
            <td>
                <input type="checkbox" name="issue_menu_show_otrs_search_link" <?php if($t_issue_menu_show_otrs_search_link == 1) {echo ' checked ';} else {echo '';} ?> /></span>
            </td>
        </tr>
        <tr <?php echo helper_alternate_class() ?>>
            <td class="category"><?php echo plugin_lang_get('reset'); ?></td>
            <td>
                <input type="checkbox" name="reset"/> <?php echo plugin_lang_get('reset_explanation'); ?>
            </td>
        </tr>
        <tr>
            <td class="center" colspan="2" >
                <input class="btn btn-primary btn-sm btn-white btn-round" type="submit" value="<?php echo plugin_lang_get('submit'); ?>" />
            </td>
        </tr>
    </table>
</form>
<?php
event_signal( 'EVENT_OTRSMAILER_LAYOUT_WIDGET_BODY_END' );
event_signal( 'EVENT_OTRSMAILER_LAYOUT_WIDGET_HEADER_END' ); 
?>
<!-- show placeholder definition -->
<?php 
event_signal( 'EVENT_OTRSMAILER_LAYOUT_WIDGET_HEADER_BEGIN' , plugin_lang_get('header_available_placeholders') );
event_signal( 'EVENT_OTRSMAILER_LAYOUT_WIDGET_BODY_BEGIN' );
?>
<div>
    <p><?php echo plugin_lang_get('header_available_placeholders_description'); ?></p>
    <pre>
    <?php 
        # returns an array with placeholde info.
        $a_placeholders = event_signal('EVENT_OTRSMAILER_INFO_PLACEHOLDERS_LIST');
        
        # format as table
        echo '<table style="border: 1px solid #000;padding:0.5em;">';
        echo '<tr style="background-color:669fc7;"><th style="border: 1px solid #000;padding:1em;">Palceholder</th><th style="border: 1px solid #000;padding:1em;" >Description</th></tr>';
        foreach ( $a_placeholders as $key => $value ) {
           echo sprintf('<tr><td style="border: 1px solid #000; padding:1em;font-weight:bold;">&lt;'. $key .'&gt;</td><td style="border: 1px solid #000;padding:1em;">%s</td></tr>', $value);
        }
        echo '</table>';
    ?>
    </pre>
</div>

<?php
event_signal( 'EVENT_OTRSMAILER_LAYOUT_WIDGET_BODY_END' );
event_signal( 'EVENT_OTRSMAILER_LAYOUT_WIDGET_HEADER_END' ); 

# print default mantis page footer
layout_page_end();

?>