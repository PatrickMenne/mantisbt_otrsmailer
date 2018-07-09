<?php
/**
 * This include file defines a helper class to generate MantisBT styled html elements.
 *
 * @package MantisBT OtrsMailer
 * @link https://github.com/isamuxsama/mantisbt_otrsmailer
 *
 * @uses plugin_api.php
 */
class MantisHtmlBuilder
{

    static $_instance = null;

    public static function Current()
    {
         $_instance = null;
        if ($_instance === null) {
            $_instance = new MantisHtmlBuilder();
        }
        return $_instance;
    }

    private function __construct()
    {

    }
    
    ###################################################
    #  mantis issue_menu_button
    ###################################################
    
    public function page_generate_link_btn_white($p_url,$p_label, $p_tooltip) {
        $format = '<a class="btn btn-primary btn-white btn-round btn-sm"  href="%s" title="%s">%s</a>';
        return sprintf($format, $p_url, $p_tooltip, $p_label);
    }
    
    public function page_generate_link_mail_issue_base( $p_bug_id, $p_action ){

        # build url
        $href = plugin_page('mail_issue').'&bug_id='.$p_bug_id.'&action='.$p_action;
        
        # build label and tooltip
        if( $p_action == 'preview'){
            $label = plugin_lang_get('link_mail_issue_preview');
            $tooltip = plugin_lang_get('link_mail_issue_preview_tooltip');
        } 
        else {
            $label = plugin_lang_get('link_mail_issue');
            $tooltip = plugin_lang_get('link_mail_issue_tooltip');
        }
        
        return $this->page_generate_link_btn_white($href, $label, $tooltip);
    }
    
    public function page_generate_link_mail_issue_preview( $p_bug_id ){
        return $this->page_generate_link_mail_issue_base( $p_bug_id, "preview");
    }
    
    public function page_generate_link_mail_issue_send( $p_bug_id ){
        return $this->page_generate_link_mail_issue_base( $p_bug_id, "send");
    }
    

    public function page_generate_link_otrs_main( $p_bug_id ){
        return $this->page_generate_link_btn_white(
            plugin_config_get('otrs_url_main'),
            plugin_lang_get('link_open_otrs'),
            plugin_lang_get('link_open_otrs_tooltip')
            );
 
    }
     
        
    ###################################################
    #  mantis widget box
    ###################################################
    
     public function page_generate_widget_white( $p_hmtl_content, $p_header_text )
     {
         return '
         <div class="widget-box widget-color-blue2 ">
            <div class="widget-header widget-header-small">
                <h4 class="widget-title lighter">
                <i class="ace-icon fa fa-bars"></i>
                '.$p_header_text.'    </h4>
                <div class="widget-toolbar">
                    <a data-action="collapse" href="-">
                        <i class="1 ace-icon fa fa-chevron-up bigger-125"></i>
                    </a>
                </div>
            </div>
            <div class="widget-body  padding-8">'.$p_hmtl_content.'</div>
        </div>';
     }
     
     public function page_generate_widget_header_begin($p_header_text)
     {
         return '<div class="widget-box widget-color-blue2 ">
                    <div class="widget-header widget-header-small">
                        <h4 class="widget-title lighter">
                        <i class="ace-icon fa fa-bars"></i>
                                ' . $p_header_text . '        </h4>
                        <div class="widget-toolbar">
                            <a data-action="collapse" href="-">
                            <i class="1 ace-icon fa fa-chevron-up bigger-125"></i>
                            </a>
                        </div>
                    </div>';
                    #<div class="widget-body padding-8">';
     }
     
      public function page_generate_widget_header_end()
     {
         #return '</div>
         return '<div class="widget-main no-padding"></div>
        </div><br/>';
        
     }


     public function page_generate_toolbox_begin()
     {
         return '<div class="widget-toolbox padding-8 clearfix">';
     }

     public function page_generate_toolbox_end()
     {
         return '</div>';
     }


     public function page_generate_widget_body_begin()
     {
         return '<div class="widget-body padding-8">';
     }

     public function page_generate_widget_body_end()
     {
         return '</div>';
     }

     
    ###################################################
    # mantis message box
    ###################################################

    public function page_generate_box_success( $p_html_content ){
        return $this->page_generate_box_base($p_html_content, true);
    }

    public function page_generate_box_fail( $p_html_content ){
        return $this->page_generate_box_base($p_html_content, false);
    }

    private function page_generate_box_base( $p_html_content, $p_success ){
        if( $p_success == true ) {  
            $t_htmlOutputTemplate = 
            '<div class="alert alert-sm alert-success">' . 
            '<i class="ace-icon fa fa-check fa-lg"></i>' .
            '%s' .
            '</div>';
        } 
        else {
            $t_htmlOutputTemplate = 
            '<div class="alert alert-sm alert-danger">' . 
            '<i class="ace-icon fa fa-times fa-lg"></i>' .
            '%s' .
            '</div>';
        }
        
        return sprintf($t_htmlOutputTemplate, $p_html_content);
    }
    
}

?>