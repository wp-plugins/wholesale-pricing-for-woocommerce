<?php
 /*
   Rule CPT rows are stored.  At rule store/update
   time, a master rule option array is (re)created, to allow speedier access to rule information at
   product/cart processing time.
 */
   
class VTWPR_Rules_UI{ 
	
	public function __construct(){       
    global $post, $vtwpr_info;
    
    //ACTION TO ALLOW THEME TO OFFER ALL PRODUCTS AT A DISCOUNT.....
    
        
    add_action( 'add_meta_boxes_vtwpr-rule', array(&$this, 'vtwpr_remove_meta_boxes') );   
    add_action( 'add_meta_boxes_vtwpr-rule', array(&$this, 'vtwpr_add_metaboxes') );
    add_action( "admin_enqueue_scripts",     array(&$this, 'vtwpr_enqueue_admin_scripts') );

    add_action( 'add_meta_boxes_vtwpr-rule', array($this, 'vtwpr_remove_all_in_one_seo_aiosp') ); 
    
    //AJAX actions
    //   uses the action name from the js....
    add_action( 'wp_ajax_vtwpr_ajax_load_variations_in',         array(&$this, 'vtwpr_ajax_load_variations_in') ); 
    add_action( 'wp_ajax_vtwpr_ajax_load_variations_out',        array(&$this, 'vtwpr_ajax_load_variations_out') );
    add_action( 'wp_ajax_noprov_vtwpr_ajax_load_variations_in',  array(&$this, 'vtwpr_ajax_load_variations_in') );      
    add_action( 'wp_ajax_noprov_vtwpr_ajax_load_variations_out', array(&$this, 'vtwpr_ajax_load_variations_out') );     
      
    //add a metabox to the **parent product custom post type page**
    //v1.0.3 begin  ==>> all in one seo conflicts with this box, don't show when that plugin is active
    if ( ! defined( 'AIOSEOP_VERSION' ) ) {
      add_action( 'add_meta_boxes_' .$vtwpr_info['parent_plugin_cpt'] , array(&$this, 'vtwpr_parent_product_meta_box_cntl') );
    } 
    // v1.0.3 end
	}
                               
    
  public function vtwpr_enqueue_admin_scripts() {
    global $post_type, $vtwpr_info;
    if( $post_type == 'vtwpr-rule' ){     //Put all JS into the FOOTER
        
        //QTip Resources
        wp_register_style ('vtwpr-qtip-style', VTWPR_URL.'/admin/css/vtwpr.qtip.min.css' );  
        wp_enqueue_style  ('vtwpr-qtip-style'); 
       
       //qtip resources named jquery-qtip, to agree with same name used in wordpress-seo from yoast!
        wp_register_script('jquery-qtip', VTWPR_URL.'/admin/js/vtwpr.qtip.min.js' );  
        wp_enqueue_script ('jquery-qtip', array('jquery'), false, true);

        wp_register_style ('vtwpr-admin-style', VTWPR_URL.'/admin/css/vtwpr-admin-style.css' );  
        wp_enqueue_style  ('vtwpr-admin-style');
        
        wp_register_script('vtwpr-admin-script', VTWPR_URL.'/admin/js/vtwpr-admin-script.js' );  
        //create ajax resource
        wp_localize_script('vtwpr-admin-script', 'variationsInAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )  ));        
        //create ajax resource
        wp_localize_script('vtwpr-admin-script', 'variationsOutAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )  ));                
        wp_enqueue_script ('vtwpr-admin-script', array('jquery', 'vtwpr-qtip-js'), false, true);

      
        //Datepicker resources, some part of WP
        wp_register_style ('vtwpr-jquery-datepicker-style', VTWPR_URL.'/admin/css/smoothness/jquery-ui-1.10.2.custom.css' );  
        wp_enqueue_style  ('vtwpr-jquery-datepicker-style');
        wp_enqueue_script ('jquery-ui-core', array('jquery'), false, true );
        wp_enqueue_script ('jquery-ui-datepicker', array('jquery'), false, true );

        if(defined('VTWPR_PRO_DIRNAME')) {
            wp_register_style ('vtwpr-admin-style2', VTWPR_PRO_URL.'/admin/css/vtwpr-admin-style2.css' );  
            wp_enqueue_style  ('vtwpr-admin-style2');
        }
       
      }
    //These are for the include/exclude meta box on the parent plugin PRODUCT page
    if( $post_type == $vtwpr_info['parent_plugin_cpt']){
      wp_register_style('vtwpr-admin-product-metabox-style', VTWPR_URL.'/admin/css/vtwpr-admin-product-metabox-style.css' );  
      wp_enqueue_style( 'vtwpr-admin-product-metabox-style');
      if (defined('VTWPR_PRO_DIRNAME'))  {
        $register_metabox_script = VTWPR_PRO_URL.'/admin/js/vtwpr-admin-product-metabox-script.js';
      } else {
        $register_metabox_script = VTWPR_URL.'/admin/js/vtwpr-admin-product-metabox-script.js';
      }     
      wp_register_script('vtwpr-admin-product-metabox-script', $register_metabox_script );
      wp_enqueue_script('vtwpr-admin-product-metabox-script', array('jquery'), false, true);    
    }
  }    
  
  public function vtwpr_remove_meta_boxes() {
     if(!current_user_can('administrator')) {  
      	remove_meta_box( 'revisionsdiv', 'post', 'normal' ); // Revisions meta box
        remove_meta_box( 'commentsdiv', 'vtwpr-rule', 'normal' ); // Comments meta box
      	remove_meta_box( 'authordiv', 'vtwpr-rule', 'normal' ); // Author meta box
      	remove_meta_box( 'slugdiv', 'vtwpr-rule', 'normal' );	// Slug meta box        	
      	remove_meta_box( 'postexcerpt', 'vtwpr-rule', 'normal' ); // Excerpt meta box
      	remove_meta_box( 'formatdiv', 'vtwpr-rule', 'normal' ); // Post format meta box
      	remove_meta_box( 'trackbacksdiv', 'vtwpr-rule', 'normal' ); // Trackbacks meta box
      	remove_meta_box( 'postcustom', 'vtwpr-rule', 'normal' ); // Custom fields meta box
      	remove_meta_box( 'commentstatusdiv', 'vtwpr-rule', 'normal' ); // Comment status meta box
      	remove_meta_box( 'postimagediv', 'vtwpr-rule', 'side' ); // Featured image meta box
      	remove_meta_box( 'pageparentdiv', 'vtwpr-rule', 'side' ); // Page attributes meta box
        remove_meta_box( 'categorydiv', 'vtwpr-rule', 'side' ); // Category meta box
        remove_meta_box( 'tagsdiv-post_tag', 'vtwpr-rule', 'side' ); // Post tags meta box
        remove_meta_box( 'tagsdiv-vtwpr_rule_category', 'vtwpr-rule', 'side' ); // vtwpr_rule_category tags  
        remove_meta_box( 'relateddiv', 'vtwpr-rule', 'side');                  
      } 
 
  }
 
        
  public  function vtwpr_add_metaboxes() {
      global $post, $vtwpr_info, $vtwpr_rule, $vtwpr_rules_set;        

      $found_rule = false;                            
      if ($post->ID > ' ' ) {
        $post_id =  $post->ID;
        $vtwpr_rules_set   = get_option( 'vtwpr_rules_set' ) ;

        $sizeof_rules_set = sizeof($vtwpr_rules_set);
        for($i=0; $i < $sizeof_rules_set; $i++) {  
           if ($vtwpr_rules_set[$i]->post_id == $post_id) {
              $vtwpr_rule = $vtwpr_rules_set[$i];  //load vtwpr-rule               
              $found_rule = true;
              $found_rule_index = $i; 
              $i = $sizeof_rules_set;              
           }
        }
      } 

      if (!$found_rule) {
        $this->vtwpr_build_new_rule();        
      } 
         
      add_meta_box('vtwpr-deal-selection',  __('Wholesale Pricing', 'vtwpr') , array(&$this, 'vtwpr_deal'), 'vtwpr-rule', 'normal', 'high');

      //side boxes
//      add_meta_box('vtwpr-rule-id', __('Rule In Words', 'vtwpr'), array(&$this, 'vtwpr_rule_id'), 'vtwpr-rule', 'side', 'low'); //low = below Publish box
//      add_meta_box('vtwpr-rule-resources', __('Resources', 'vtwpr'), array(&$this, 'vtwpr_rule_resources'), 'vtwpr-rule', 'side', 'low'); //low = below Publish box 

      //create help tab...                                                                                                                                                                                                          
      $content;
      $content .= '<br><a id="wholesale_pricing-title-more2" class="more-anchor" href="javascript:void(0);"><img class="wholesale_pricing-title-helpPng" alt="help"  width="14" height="14" src="' . VTWPR_URL .  '/admin/images/help.png" />' .    __(' Help! ', 'vtwpr')  .'&nbsp;'.   __('Tell me about Wholesale Pricing ', 'vtwpr') . '<img class="plus-button" alt="help" height="10px" width="10px" src="' . VTWPR_URL . '/admin/images/plus-toggle2.png" /></a>';            
      $content .= '    <a id="wholesale_pricing-title-less2" class="more-anchor less-anchor" href="javascript:void(0);"><img class="wholesale_pricing-title-helpPng" alt="help" width="14" height="14" src="' . VTWPR_URL . '/admin/images/help.png" />' . __('   Less Wholesale Pricing Help ... ', 'vtwpr') . '<img class="minus-button" alt="help" height="12px" width="12px" src="' . VTWPR_URL . '/admin/images/minus-toggle2.png" /></a>';   
      
      $screen = get_current_screen();
      $screen->add_help_tab( array( 
         'id' => 'vtwpr-help',            //unique id for the tab
         'title' => 'Wholesale Pricing Help',      //unique visible title for the tab
         'content' => $content  //actual help text
        ) );  
  }                   
   
                                                    
  public function vtwpr_error_messages() {     
      global $post, $vtwpr_rule;

      $error_msg_count = sizeof($vtwpr_rule->rule_error_message);
       ?>        
          <script type="text/javascript">
          jQuery(document).ready(function($) {           
          $('<div class="vtwpr-error" id="vtwpr-error-announcement"><?php _e("Please Repair Errors below", "vtwpr"); ?></div>').insertBefore('#vtwpr-deal-selection');  
      <?php 
      //loop through all of the error messages 
      //          $vtmax_info['line_cnt'] is used when table formattted msgs come through.  Otherwise produces an inactive css id. 
     for($i=0; $i < $error_msg_count; $i++) { 
       ?>
             $('<div class="vtwpr-error"><?php echo $vtwpr_rule->rule_error_message[$i]['error_msg'];?></div>').insertBefore('<?php echo $vtwpr_rule->rule_error_message[$i]['insert_error_before_selector']; ?>');
      <?php 
  
      }  //end 'for' loop      
      ?>   
            });   
          </script>
     <?php 
     
     //Change the label color to red for fields in error
     if ( sizeof($vtwpr_rule->rule_error_red_fields) > 0 )  {
      
       echo '<style>' ;   // echo '<style type="text/css">' ;
       
       for($i=0; $i < sizeof($vtwpr_rule->rule_error_red_fields); $i++) { 
          if ($i > 0) { // if 2nd to n field name, put comma before the name...
            echo ', ';
          }
          echo $vtwpr_rule->rule_error_red_fields[$i];
       }
       echo '{color:red !important; display:block;}' ;         // display:block added for hidden date err msg fields
       
       for($i=0; $i < sizeof($vtwpr_rule->rule_error_box_fields); $i++) { 
          if ($i > 0) { // if 2nd to n field name, put comma before the name...
            echo ', ';
          }
          echo $vtwpr_rule->rule_error_box_fields[$i];
       }
       echo '{border-color:red !important; display:block;}' ;         // display:block added for hidden date err msg fields
              
       echo '</style>' ;
     }

      
      if( $post->post_status == 'publish') { //if post status not = pending, make it so  
          $post_id = $post->ID;
          global $wpdb;
          $wpdb->update( $wpdb->posts, array( 'post_status' => 'pending' ), array( 'ID' => $post_id ) );
      } 

  }   

/* **************************************************************
    Deal Selection Metabox
                                                                                     
    Includes: 
    - Rule type info
    - Rule deal info
    - applies-to max info
    - rule catalog/cart display msgs
    - cumulative logic rule switches
************************************************************** */                                                   
  public function vtwpr_deal() {     
      global $vtwpr_rule_template_framework, $vtwpr_deal_structure_framework, $vtwpr_deal_screen_framework, $vtwpr_rule_display_framework, $vtwpr_rule, $vtwpr_info, $vtwpr_setup_options;
      $selected = 'selected="selected"';
      $checked = 'checked="checked"';
      $disabled = 'disabled="disabled"' ; 
      $vtwprNonce = wp_create_nonce("vtwpr-rule-nonce"); //nonce verified in vt-wholesale-pricing.php
                
      if ( sizeof($vtwpr_rule->rule_error_message ) > 0 ) {    //these error messages are from the last upd action attempt, coming from vtwpr-rules-update.php
           $this->vtwpr_error_messages();
      } 
    
      $currency_symbol = vtwpr_get_currency_symbol();
    
      //**********************************************************************
      //IE CSS OVERRIDES, done here to ensure they're last in line...
      //**********************************************************************
      echo '<!--[if IE]>';
	    echo '<link rel="stylesheet" type="text/css"  media="all" href="' .VTWPR_URL.'/admin/css/vtwpr-admin-style-ie.css" />';
      echo '<![endif]-->';
      // end override
       
      //This Div only shows if there is a JS error in the customer implementation of the plugin, as the JS hides this div, if the JS is active
      //vtwpr_show_help_if_js_is_broken();  
      
      ?>
        
        <script type="text/javascript">
        jQuery(document).ready(function($) {
        
        //Spinner gif wasn't working... 
         $('.spinner').append('<img src="<?php echo VTWPR_URL;?>/admin/images/indicator.gif" />');
       
          });   
        </script>
     
       <?php /*
       <div class="hide-by-jquery">
        <span class="">< ?php _e('If you can see this, there is a JavaScript Error on the Page. Hover over this &rarr;', 'vtwpr'); ? > </span>
            < ?php vtwpr_show_help_tooltip($context = 'onlyShowsIfJSerror', $location = 'title'); ? >
       </div>
       */
       ?>

    <?php //BANNER AND BUTTON AREA ?>
                         

    
    <img id="wholesale-pricing-img-preload" alt="" src="<?php echo VTWPR_URL;?>/admin/images/upgrade-bkgrnd-banner.jpg" />
 		<div id="upgrade-title-area">
      <a  href=" <?php echo VTWPR_PURCHASE_PRO_VERSION_BY_PARENT ; ?> "  title="Purchase Pro">
      <img id="wholesale-pricing-img" alt="help" height="40px" width="40px" src="<?php echo VTWPR_URL;?>/admin/images/sale-circle.png" />
      </a>      
      <h2>
        <?php _e('Wholesale Pricing', 'vtwpr'); ?>
        <?php if(defined('VTWPR_PRO_DIRNAME')) {  
                _e(' Pro', 'vtwpr');
              }
        ?>    
        
        </h2>  
      
      <?php if(!defined('VTWPR_PRO_DIRNAME')) {  ?> 
          <span class="group-power-msg">
            <strong><em><?php _e('Create rules for Any Group you can think of, and More!', 'vtwpr'); ?></em></strong>
            <?php /* 
              - Product Category
              - Wholesale Pricing Custom Category
              - Logged-in Status
              - Product
              - Variations!
                */ ?> 
          </span> 
          <span class="buy-button-area">
            <a href="<?php echo VTWPR_PURCHASE_PRO_VERSION_BY_PARENT; ?>" class="help tooltip tooltipWide buy-button">
                <span class="buy-button-label"><?php _e('Get Wholesale Pricing Pro', 'vtwpr'); ?></span>
                <b> <?php vtwpr_show_help_tooltip_text('upgradeToPro'); ?> </b>
            </a>
          </span> 
      <?php }  ?>
          
    </div>  

            
      <?php //RULE EXECUTION TYPE ?> 
      <div class="display-virtual_box  top-box">                           
        
        <?php //************************ ?>
        <?php //HIDDEN FIELDS BEGIN ?>
        <?php //************************ ?>
        <?php //RULE EXECUTION blue-dropdownS - only one actually displays at a time, depending on ?>
        <input type="hidden" id="vtwpr_nonce" name="vtwpr_nonce" value="<?php echo $vtwprNonce; ?>" />
        <?php //Hidden switch to communicate with the JS that the data is 1st time screenful ?>
        <input type="hidden" id="firstTimeBackFromServer" name="firstTimeBackFromServer" value="yes" />        
        <input type="hidden" id="upperSelectsFirstTime" name="upperSelectsFirstTime" value="yes" />
        <input type="hidden" id="upperSelectsDoneSw" name="upperSelectsDoneSw" value="" />
        <input type="hidden" id="catalogCheckoutMsg" name="catalogCheckoutMsg" value="<?php echo __('Message unused for Catalog Discount', 'vtwpr');?>" />        
        <input type="hidden" id="vtwpr-moreInfo" name="vtwpr-docTitle" value="<?php _e('More Info', 'vtwpr');?>" /> <?php //v1.0.5 added 2nd button ?>
        <input type="hidden" id="vtwpr-docTitle" name="vtwpr-docTitle" value="<?php _e('- Help! -', 'vtwpr');?>" />         
        <input type="hidden" id="fullMsg" name="fullMsg" value="<?php echo $vtwpr_info['default_full_msg'];?>" /> 
        
        <?php //************************ ?>
        <?php //DUMMY HIDDEN FIELDS BEGIN, assigning default values ?>                          
        <?php //************************ ?> 
        <input type="hidden" id="cart-or-catalog-select" name="cart-or-catalog-select" value="catalog" />
        <input type="hidden" id="minimum-purchase-select" name="minimum-purchase-select" value="none" />
        <input type="hidden" id="discount_rule_max_amt_msg" name="discount_rule_max_amt_msg" value="none" />
        <input type="hidden" id="discount_lifetime_max_amt_msg" name="discount_lifetime_max_amt_msg" value="none" /> 
        <input type="hidden" id="discount_rule_cum_max_amt_msg" name="discount_rule_cum_max_amt_msg" value="none" /> 
        <input type="hidden" id="discount_product_short_msg" name="discount_product_short_msg" value="none" /> 
        <input type="hidden" id="shortMsg" name="shortMsg" value="none" />  
        <input type="hidden" id="cumulativeRulePricing" name="cumulativeRulePricing" value="yes" /> 
        <input type="hidden" id="cumulativeSalePricing" name="cumulativeSalePricing" value="addToSalePrice" /> 
        <input type="hidden" id="cumulativeCouponPricing" name="cumulativeCouponPricing" value="yes" /> 
        <input type="hidden" id="popChoiceOut" name="popChoiceOut" value="sameAsInPop" /> 
        <input type="hidden" id="action_amt_type_0" name="action_amt_type_0" value="none" />      
        <input type="hidden" id="action_amt_mod_0" name="action_amt_mod_0" value="none" />
        <input type="hidden" id="action_repeat_condition_0" name="action_repeat_condition_0" value="none" /> 
        <input type="hidden" id="discount_applies_to_0" name="discount_applies_to_0" value="each" /> 
        <input type="hidden" id="discount_lifetime_max_amt_type_0" name="discount_lifetime_max_amt_type_0" value="none" /> 
        <input type="hidden" id="discount_rule_max_amt_type_0" name="discount_rule_max_amt_type_0" value="none" /> 
        <input type="hidden" id="discount_rule_cum_max_amt_type_0" name="discount_rule_cum_max_amt_type_0" value="none" />
        <input type="hidden" id="ruleApplicationPriority_num" name="ruleApplicationPriority_num" value="10" />
        <input type="hidden" id="buy_amt_type_0" name="buy_amt_type_0" value="none" />
        <input type="hidden" id="buy_amt_mod_0" name="buy_amt_mod_0" value="none" />
        <input type="hidden" id="buy_repeat_condition_0" name="buy_repeat_condition_0" value="none" />                            
        <input type="hidden" id="buy_amt_applies_to_0" name="buy_amt_applies_to_0" value="each" />
        <input type="hidden" id="action_amt_applies_to_0" name="action_amt_applies_to_0" value="all" /> 
           
        <input type="hidden" id="buy_repeat_count_0" name="buy_repeat_count_0" value="0" />
        <input type="hidden" id="buy_amt_count_0" name="buy_amt_count_0" value="0" />
        <input type="hidden" id="buy_amt_mod_count_0" name="buy_amt_mod_count_0" value="0" />
        <input type="hidden" id="action_repeat_count_0" name="action_repeat_count_0" value="0" />
        <input type="hidden" id="action_amt_count_0" name="action_amt_count_0" value="0" />
        <input type="hidden" id="action_amt_mod_count_0" name="action_amt_mod_count_0" value="0" />
        <input type="hidden" id="discount_auto_add_free_product_0" name="discount_auto_add_free_product_0" value="" />
        <input type="hidden" id="discount_rule_max_amt_count_0" name="discount_rule_max_amt_count_0" value="0" />
        <input type="hidden" id="discount_lifetime_max_amt_count_0" name="discount_lifetime_max_amt_count_0" value="0" />
        <input type="hidden" id="discount_rule_cum_max_amt_count_0" name="discount_rule_cum_max_amt_count_0" value="0" />
        
        

          
           
        <?php 
           /*
            Assign a numeric value to the switch
              showing HOW MANY selects have data
                on 1st return from server...
           */           
           $data_sw = '0';
           
           //test the Various group filter selects and set a value...
           switch( true) {
              case ( ($vtwpr_rule->get_group_filter_select > ' ') &&
                     ($vtwpr_rule->get_group_filter_select != 'choose') ):
                  $data_sw = '5';
                break;
              case ( ($vtwpr_rule->buy_group_filter_select > ' ') &&
                     ($vtwpr_rule->buy_group_filter_select != 'choose') ):
                  $data_sw = '4';
                break;  
              case ( ($vtwpr_rule->minimum_purchase_select > ' ') &&
                     ($vtwpr_rule->minimum_purchase_select != 'choose') ):              
                  $data_sw = '3';
                break;   
              case ( ($vtwpr_rule->pricing_type_select > ' ') &&
                     ($vtwpr_rule->pricing_type_select != 'choose') ):
                  $data_sw = '2';
                break;   
              case ( ($vtwpr_rule->cart_or_catalog_select > ' ') &&
                     ($vtwpr_rule->cart_or_catalog_select != 'choose') ):              
                  $data_sw = '1';
                break;                    
             } 
             
             /*  upperSelectsHaveDataFirstTime has values from 0 => 5
             value = 0  no previous data saved 
             value = 1  last run got to:  cart_or_catalog_select
             value = 2  last run got to:  pricing_type_select
             value = 3  last run got to:  minimum_purchase_select
             value = 4  last run got to:  buy_group_filter_select
             value = 5  last run got to:  get_group_filter_select
             */
        ?>
        <input type="hidden" id="upperSelectsHaveDataFirstTime" name="upperSelectsHaveDataFirstTime" value="<?php echo $data_sw; ?>" />
        
        <input type="hidden" id="templateChanged" name="templateChanged" value="no" /> 
        
        <?php //Statuses used for switching of the upper dropdowns ?>
        <input type="hidden" id="select_status_sw"  name="select_status_sw"  value="no" />
        
        <?php //pass these two messages up to JS, translated here if necessary ?>  
        <input type="hidden" id="shortMsg" name="shortMsg" value="<?php echo $vtwpr_info['default_short_msg'];?>" /> 
  
        <input id="pluginVersion" type="hidden" value="<?php if(defined('VTWPR_PRO_DIRNAME')) { echo "proVersion"; } else { echo "freeVersion"; } ?>" name="pluginVersion" />  
        <input id="rule_template_framework" type="hidden" value="<?php echo $vtwpr_rule->rule_template;  ?>" name="rule_template_framework" />
              
           
        <?php //************************ ?>
        <?php //HIDDEN FIELDS END ?>
        <?php //************************ ?>

        <div class="template-area clear-left">  

          <div class="clear-left" id="first-blue-line">                                                           
               <span class="left-column  left-column-less-padding-top3">                              
                 <?php //mwn20140414 added id ?>
                 <label id="pricing-type-select-label"  class="hasWizardHelpRight"   for="<?php echo $vtwpr_rule_display_framework['pricing_type_select']['label']['for'];?>"><?php echo $vtwpr_rule_display_framework['pricing_type_select']['label']['title'];?></label>
                 <?php vtwpr_show_object_hover_help ('pricing_type_select', 'wizard') ?> 
               </span>
               <span class="blue-dropdown  right-column" id="pricing-type-select-area">   
                 <select id="<?php echo $vtwpr_rule_display_framework['pricing_type_select']['select']['id'];?>" class="<?php echo$vtwpr_rule_display_framework['pricing_type_select']['select']['class']; ?>  " name="<?php echo $vtwpr_rule_display_framework['pricing_type_select']['select']['name'];?>" tabindex="<?php //echo $vtwpr_rule_display_framework['pricing_type_select']['select']['tabindex']; ?>" >          
                   <?php
                   for($i=0; $i < sizeof($vtwpr_rule_display_framework['pricing_type_select']['option']); $i++) { 
                   ?>                             
                      <option id="<?php echo $vtwpr_rule_display_framework['pricing_type_select']['option'][$i]['id']; ?>"  class="<?php echo $vtwpr_rule_display_framework['pricing_type_select']['option'][$i]['class']; ?>"  value="<?php echo $vtwpr_rule_display_framework['pricing_type_select']['option'][$i]['value']; ?>"   <?php if ($vtwpr_rule_display_framework['pricing_type_select']['option'][$i]['value'] == $vtwpr_rule->pricing_type_select )  { echo $selected; } ?> >  <?php echo $vtwpr_rule_display_framework['pricing_type_select']['option'][$i]['title']; ?> </option>
                   <?php } ?> 
                 </select>  
                  <span class="shortIntro  shortIntro2"  id="buy_group_filter_comment">
                      <span class="">
                          <em><?php _e("What kind of Deal is it?", 'vtwpr');?></em>
                          &nbsp;
                          <img  class="hasHoverHelp2" width="11px" alt=""  src="<?php echo VTWPR_URL;?>/admin/images/help.png" />  
                          <?php vtwpr_show_object_hover_help ('pricing_type_select', 'small') ?>
                      </span>                   
                      <br>
                      <a class="commentURL" target="_blank" href="http://www.varktech.com/documentation/wholesale-pricing/examples"><?php _e('Deal Examples', 'vtwpr');?></a>                
                  </span>                                                          
               </span> 
          </div> <?php //end blue-line ?>
  
          <div class="blue-line  blue-line-less-top  clear-left">
              <span class="left-column">                                                      
                <label class="scheduling-label hasWizardHelpRight" id="scheduling-label-item"><?php _e('Deal Schedule', 'vtwpr');?></label>   
                <?php vtwpr_show_object_hover_help ('scheduling', 'wizard') ?>
              </span>
              <span class="blue-dropdown  scheduling-group  right-column" id="scheduling-area">   
                <span class="date-line" id='date-line-0'>                               
                <?php //   <label class="scheduling-label">Scheduling</label> ?>                                              
                    <span class="date-line-area">  
                      <?php  $this->vtwpr_rule_scheduling(); ?> 
                    </span> 
                    <span class="on-off-switch">                              
                    <?php //     <label for="rule-state-select">On/Off Switch</label>  ?> 
                       <select id="<?php echo $vtwpr_rule_display_framework['rule_on_off_sw_select']['select']['id'];?>" class="<?php echo$vtwpr_rule_display_framework['rule_on_off_sw_select']['select']['class']; ?>" name="<?php echo $vtwpr_rule_display_framework['rule_on_off_sw_select']['select']['name'];?>" tabindex="<?php //echo $vtwpr_rule_display_framework['rule_on_off_sw_select']['select']['tabindex']; ?>" >          
                         <?php
                         for($i=0; $i < sizeof($vtwpr_rule_display_framework['rule_on_off_sw_select']['option']); $i++) { 
                         ?>                             
                            <option id="<?php echo $vtwpr_rule_display_framework['rule_on_off_sw_select']['option'][$i]['id']; ?>"  class="<?php echo $vtwpr_rule_display_framework['rule_on_off_sw_select']['option'][$i]['class']; ?>"  value="<?php echo $vtwpr_rule_display_framework['rule_on_off_sw_select']['option'][$i]['value']; ?>"   <?php if ($vtwpr_rule_display_framework['rule_on_off_sw_select']['option'][$i]['value'] == $vtwpr_rule->rule_on_off_sw_select )  { echo $selected; } ?> >  <?php echo $vtwpr_rule_display_framework['rule_on_off_sw_select']['option'][$i]['title']; ?> </option>
                         <?php } ?> 
                       </select>                        
                    </span>                                
                </span> 
                   

                  <span class="shortIntro"  id="buy_group_filter_comment">
                    <em>
                    <?php _e('Active When?', 'vtwpr');?>
                    <br>
                    <?php _e('On or Off?', 'vtwpr');?>
                    </em>
                    &nbsp;
                    <img  class="hasHoverHelp2" width="11px" alt=""  src="<?php echo VTWPR_URL;?>/admin/images/help.png" /> 
                    <?php vtwpr_show_object_hover_help ('scheduling', 'small') ?>
                  </span>                                                      
              </span>      
          </div> <?php //end blue-line ?>
                                               
      </div> <?php //end template-area ?>                       

     </div> <?php //end top-box ?>                
     
  <div class="display-virtual_box hideMe" id="lower-screen-wrapper" >

  
      <?php //****************  
            //DEAL INFO GROUP  
            //**************** ?> 
 
     <div class="display-virtual_box  clear-left" id="rule_deal_info_group">  
                       
      <?php  for($k=0; $k < sizeof($vtwpr_rule->rule_deal_info); $k++) {  ?>         
      <div class="display-virtual_box rule_deal_info" id="rule_deal_info_line<?php echo '_' .$k; ?>">   
        <div class="display-virtual_box" id="buy_info<?php echo '_' .$k; ?>">  
         
           <input id="hiddenDealInfoLine<?php echo '_' .$k; ?>" type="hidden" value="lineActive" name="dealInfoLine<?php echo '_' .$k; ?>" />

           <?php 
              //*****************************************************
              //set the switch used on the screen for JS data check 
              //*****************************************************  ?>
           <?php //end switch ************************************** ?> 

         <div class="screen-box buy_group_box" id="buy_group_box<?php echo '_' .$k; ?>" >
            <span class="left-column">
                <span class="title  hasWizardHelpRight" id="buy_group_title">
                  <a id="buy_group_title_anchor" class="title-anchors second-level-title" href="javascript:void(0);"><span class="showBuyAsBuy"><?php _e('Product Filter', 'vtwpr');?></span><span class="showBuyAsDiscount"><?php _e('Product Filter', 'vtwpr');?></span> </a>                                       
                </span>
                <?php vtwpr_show_object_hover_help ('inPop', 'wizard') ?> 
                 
            </span>
            
            <span class="dropdown  buy_group  right-column" id="buy_group_dropdown">              
               <select id="<?php echo $vtwpr_rule_display_framework['inPop']['select']['id'];?>" class="<?php echo$vtwpr_rule_display_framework['inPop']['select']['class']; ?> " name="<?php echo $vtwpr_rule_display_framework['inPop']['select']['name'];?>" tabindex="<?php //echo $vtwpr_rule_display_framework['inPop']['select']['tabindex']; ?>" >          
                 <?php
                 for($i=0; $i < sizeof($vtwpr_rule_display_framework['inPop']['option']); $i++) { 
                      
                      //pick up the free/pro version of the title => in this case, title and title3
                      $title = $vtwpr_rule_display_framework['inPop']['option'][$i]['title'];
                      if ( ( defined('VTWPR_PRO_DIRNAME') ) &&
                           ( $vtwpr_rule_display_framework['inPop']['option'][$i]['title3'] > ' ' ) ) {
                        $title = $vtwpr_rule_display_framework['inPop']['option'][$i]['title3'];                        
                      }                 
                 ?>                             
                    <option id="<?php echo $vtwpr_rule_display_framework['inPop']['option'][$i]['id']; ?>"  class="<?php echo $vtwpr_rule_display_framework['inPop']['option'][$i]['class']; ?>"  value="<?php echo $vtwpr_rule_display_framework['inPop']['option'][$i]['value']; ?>"   <?php if ($vtwpr_rule_display_framework['inPop']['option'][$i]['value'] == $vtwpr_rule->inPop )  { echo $selected; } ?> >  <?php echo $title; ?> </option>
                 <?php } ?> 
               </select> 
               
                           
               <span class="buy_group_line_remainder_class" id="buy_group_line_remainder">   
                  <?php $this->vtwpr_buy_group_cntl(); ?> 
               </span>                
               
               <span class="shortIntro  shortIntro2" >
                  <em>
                  <?php _e('Product must be in the Filter Group', 'vtwpr');?>
                  </em><br>
                  <em>
                  <?php _e('to be valid for the Deal', 'vtwpr');?>
                  </em> 
                &nbsp;
                  <img  class="hasHoverHelp2" width="11px" alt=""  src="<?php echo VTWPR_URL;?>/admin/images/help.png" /> 
                  <?php vtwpr_show_object_hover_help ('inPop', 'small') ?>
               </span>                               
                                        
            </span>                                                                          

         </div><!-- //buy_group_box -->


        </div><!-- //buy_info -->

        <div class="display-virtual_box" id="discount_info">
                 
          <div class="screen-box discount_amt_box  discount_amt_box_class<?php echo '_' .$k; ?>" id="discount_amt_box<?php echo '_' .$k; ?>" >  

            <span class="clear-both left-column">
                <span class="title  discount_action_type  hasWizardHelpRight" id="discount_action_type_title<?php echo '_' .$k; ?>" >            
                  <a id="discount_action_title_anchor<?php echo '_' .$k; ?>" class="title-anchors second-level-title" href="javascript:void(0);"><?php _e('Discount Amount', 'vtwpr');?></a>
                </span>
                <?php vtwpr_show_object_hover_help ('discount_amt_type', 'wizard') ?>
            </span>

            <span class="dropdown discount_amt_type right-column" id="discount_amt_type_dropdown<?php echo '_' .$k; ?>">              
              
               <select id="<?php echo $vtwpr_deal_screen_framework['discount_amt_type']['select']['id'] . '_' .$k ; ?>" class="<?php echo$vtwpr_deal_screen_framework['discount_amt_type']['select']['class']; ?>" name="<?php echo $vtwpr_deal_screen_framework['discount_amt_type']['select']['name'] . '_' .$k ; ?>" tabindex="<?php //echo $vtwpr_deal_screen_framework['discount_amt_type']['select']['tabindex']; ?>" >          
                 <?php
                 for($i=0; $i < sizeof($vtwpr_deal_screen_framework['discount_amt_type']['option']); $i++) { 
                          $this->vtwpr_change_title_currency_symbol('discount_amt_type', $i, $currency_symbol);                 
                  ?>                                                
                    <option id="<?php echo $vtwpr_deal_screen_framework['discount_amt_type']['option'][$i]['id'] . '_'  .$k  ?>"  class="<?php echo $vtwpr_deal_screen_framework['discount_amt_type']['option'][$i]['class']; ?>"  value="<?php echo $vtwpr_deal_screen_framework['discount_amt_type']['option'][$i]['value']; ?>"   <?php if ($vtwpr_deal_screen_framework['discount_amt_type']['option'][$i]['value'] == $vtwpr_rule->rule_deal_info[$k]['discount_amt_type'] )  { echo $selected; } ?> >  <?php echo $vtwpr_deal_screen_framework['discount_amt_type']['option'][$i]['title']; ?> </option>
                 <?php } ?> 
               </select>
               
                
               <span class="discount_amt_count_area  discount_amt_count_area_class<?php echo '_' .$k; ?>  amt-field" id="discount_amt_count_area<?php echo '_' .$k; ?>">    
                 <span class="discount_amt_count_label" id="discount_amt_count_label<?php echo '_' .$k; ?>"> 
                    <span class="forThePriceOf-amt-literal-inserted  discount_amt_count_literal  discount_amt_count_literal<?php echo '_' .$k;?> " id="discount_amt_count_literal_forThePriceOf_buyAmt<?php echo '_' .$k; ?>"><?php $this->vtwpr_load_forThePriceOf_literal($k); ?> </span>
                    <span class="discount_amt_count_literal  discount_amt_count_literal_forThePriceOf  discount_amt_count_literal<?php echo '_' .$k;?> " id="discount_amt_count_literal_forThePriceOf<?php echo '_' .$k; ?>"><?php _e('units ', 'vtwpr'); echo  '&nbsp;';  _e(' For the Price of ', 'vtwpr');?> </span>
                    <span class="discount_amt_count_literal  discount_amt_count_literal_forThePriceOf_Currency  discount_amt_count_literal<?php echo '_' .$k;?> " id="discount_amt_count_literal_forThePriceOf_Currency<?php echo '_' .$k; ?>"><?php echo $currency_symbol; ?></span>
                 </span>                 
                 <input id="<?php echo $vtwpr_deal_screen_framework['discount_amt_count']['id'] . '_'  .$k; ?>" class="<?php echo $vtwpr_deal_screen_framework['discount_amt_count']['class']; ?>" type="<?php echo $vtwpr_deal_screen_framework['discount_amt_count']['type']; ?>" name="<?php echo $vtwpr_deal_screen_framework['discount_amt_count']['name'] . '_' .$k ; ?>" value="<?php echo $vtwpr_rule->rule_deal_info[$k]['discount_amt_count']; ?>" />                 
                 <span class="discount_amt_count_literal_units_area  discount_amt_count_literal<?php echo '_' .$k;?>  discount_amt_count_literal_units_area_class<?php echo '_' .$k; ?>" id="discount_amt_count_literal_units_area<?php echo '_' .$k; ?>">
                   <span class="discount_amt_count_literal" id="discount_amt_count_literal_units<?php echo '_' .$k; ?>"><?php _e(' units', 'vtwpr');?> </span>
                   <?php vtwpr_show_help_tooltip($context = 'discount_amt_count_forThePriceOf'); ?>
                 </span>                
               </span>
                <label id="<?php echo $vtwpr_deal_screen_framework['discount_auto_add_free_product']['label']['id'] . '_'  .$k; ?>"   class="<?php echo $vtwpr_deal_screen_framework['discount_auto_add_free_product']['label']['class'] ?>"> 
                    
                    <input id="<?php echo $vtwpr_deal_screen_framework['discount_auto_add_free_product']['checkbox']['id'] . '_'  .$k; ?>" 
                          class="<?php echo $vtwpr_deal_screen_framework['discount_auto_add_free_product']['checkbox']['class']; ?>  hasWizardHelpBelow"
                          type="checkbox" 
                          value="<?php echo $vtwpr_deal_screen_framework['discount_auto_add_free_product']['checkbox']['value']; ?>" 
                           <?php if ($vtwpr_deal_screen_framework['discount_auto_add_free_product']['checkbox']['value'] == $vtwpr_rule->rule_deal_info[$k]['discount_auto_add_free_product'] )  { echo $checked; } ?>
                          name="<?php echo $vtwpr_deal_screen_framework['discount_auto_add_free_product']['checkbox']['name'] . '_'  .$k; ?>" />
                    <?php vtwpr_show_object_hover_help ('discount_free', 'wizard') ?> 
                          
                    <?php echo $vtwpr_deal_screen_framework['discount_auto_add_free_product']['label']['title']; ?>  
                    <?php vtwpr_show_help_tooltip($context = 'discount_auto_add_free_product', $location = 'title'); ?> 
                </label>
                        
               <span class="shortIntro  shortIntro2" >
                  <em>
                  <?php _e('What kind of Discount is offered,', 'vtwpr');?>
                  </em><br>
                  <em>
                  <?php _e('and in what amount?', 'vtwpr');?>
                  </em>                   
                &nbsp;
                  <img  class="hasHoverHelp2" width="11px" alt=""  src="<?php echo VTWPR_URL;?>/admin/images/help.png" /> 
                  <?php vtwpr_show_object_hover_help ('discount_amt_type', 'small') ?>
               </span>                                     
            </span>
     
          </div> <!-- //discount_amt_box -->
                        
        </div> <!-- //discount_info -->

      <div id="messages-outer-box">           
         <div class="screen-box  messages-box_class" id="messages-box">
           <span class="dropdown messages-box-area clear-left"  id="discount_msgs_dropdown">      
             <span class="discount_product_full_msg_area clear-both">

                 <span class="left-column">
                     <span class="title  hasWizardHelpRight">                
                         <span class="title-anchors" id="discount_product_full_msg_label"> <?php _e('Advertising Message', 'vtwpr');?> </span> 
                     </span>
                     <?php vtwpr_show_object_hover_help ('discount_product_full_msg', 'wizard') ?>
                 </span>
                                    
                 <span class="right-column">                
                     <span class="column-width50">
                         <textarea rows="2" cols="35" id="<?php echo $vtwpr_rule_display_framework['discount_product_full_msg']['id']; ?>" class="<?php echo $vtwpr_rule_display_framework['discount_product_full_msg']['class']; ?>  right-column" type="<?php echo $vtwpr_rule_display_framework['discount_product_full_msg']['type']; ?>" name="<?php echo $vtwpr_rule_display_framework['discount_product_full_msg']['name']; ?>" ><?php echo $vtwpr_rule->discount_product_full_msg; ?></textarea>                                                                                              
                         
                     </span>                               
                     <span class="shortIntro" >
                        <em>
                        <?php _e('Optional, Can be shown in your', 'vtwpr');?>
                        </em><br>
                        <em>
                        <?php _e('Website using ', 'vtwpr');?>
                        </em>
                     &nbsp;
                    <a class="commentURL" target="_blank" href="http://www.varktech.com/documentation/pricing-deals/shortcodes"><?php _e('Shortcodes', 'vtwpr');?></a>                                                     
                      &nbsp;
                        <img  class="hasHoverHelp2" width="11px" alt=""  src="<?php echo VTWPR_URL;?>/admin/images/help.png" /> 
                       <?php vtwpr_show_object_hover_help ('discount_product_full_msg', 'small') ?>
                     </span>                               
                  </span> 
            
             </span>
           </span>
         </div>    
      </div>
         
        
        </div> <!-- //end DEAL INFO line in "for" loop --><?php //end DEAL INFO line in "for" loop ?>   
      <?php } //end $k'for' LOOP ?>
      </div> <!-- //rule_deal_info_group --> <?php //end rule_deal_info_group ?>  

      </div> <?php //lower-screen-wrapper ?>
      
      <?php 
          
    //lots of selects change their values between standard and 'discounted' titles.
    //This is where we supply the HIDEME alternative titles
    $this->vtwpr_print_alternative_title_selects();  
    
//echo '$vtwpr_rule= <pre>'.print_r($vtwpr_rule, true).'</pre>' ; 
         
  }  //end vtwpr_deal
      
   public    function vtwpr_buy_action_groups() {      
       $this->vtwpr_buy_group_cntl();
       $this->vtwpr_action_group_cntl();                
}
      
  
    public    function vtwpr_buy_group_cntl() {   
       global $post, $vtwpr_info, $vtwpr_rule, $vtwpr_rule_display_framework, $vtwpr_rules_set;
       $selected = 'selected="selected"';
       $checked = 'checked="checked"';  
     ?>
                          
        <span class="amt-field" id="singleChoiceIn-span">                                  
          <span class="amt-field-label" id="singleProdID-in-label"><span class="showBuyAsBuy"><?php _e('Buy Product ID Number', 'vtwpr');?></span><span class="showBuyAsDiscount"><?php _e('Discount Product ID Number', 'vtwpr');?></span></span>
          <input id="<?php echo $vtwpr_rule_display_framework['inPop_singleProdID']['id']; ?>" class="<?php echo $vtwpr_rule_display_framework['inPop_singleProdID']['class']; ?>" type="<?php echo $vtwpr_rule_display_framework['inPop_singleProdID']['type']; ?>" name="<?php echo $vtwpr_rule_display_framework['inPop_singleProdID']['name']; ?>" value="<?php echo $vtwpr_rule->inPop_singleProdID; ?>" />
          <?php vtwpr_show_help_tooltip($context = 'pop-prod-id', $location = 'title'); ?>
          
          <?php if ($vtwpr_rule->inPop_singleProdID['value'] > ' ' ) { ?>           
              <span class="" id="singleProdID-in-name-area">
                <span class="amt-field-label" id="singleProdID-in-name-label"><?php _e('Product Name', 'vtwpr');?></span>
                <span id="singleProdID-in-name" ><?php echo $vtwpr_rule->inPop_singleProdID_name; ?></span>
              </span>
          <?php } ?>                                                     
        </span> 
               
         
        <div id="inPop-varProdID-cntl">            

          <div id="inPopVarBox">
              <h3 id="inPopVarBox_label"><?php _e('Enter Product ID', 'vtwpr');?>
                  <?php vtwpr_show_help_tooltip($context = 'pop-prod-id', $location = 'title'); ?>
              </h3>
              <div id="inPopVarProduct">                                    
                  <input id="<?php echo $vtwpr_rule_display_framework['inPop_varProdID']['id']; ?>" class="<?php echo $vtwpr_rule_display_framework['inPop_varProdID']['class']; ?>" type="<?php echo $vtwpr_rule_display_framework['inPop_varProdID']['type']; ?>" name="<?php echo $vtwpr_rule_display_framework['inPop_varProdID']['name']; ?>" value="<?php echo $vtwpr_rule->inPop_varProdID; ?>" />
                 <br />                            
              </div>
              <div id="inPopVarButton">
                 <?php
                    if ($vtwpr_rule->inPop_varProdID) {
                      $product_ID = $vtwpr_rule->inPop_varProdID; 
                      $product_variation_IDs = vtwpr_get_variations_list($product_ID);
                    } else {
                      $product_variation_IDs = array();  //v1.0.3
                    }
                    /* ************************************************
                    **   Get Variations Button for Rule screen
                    *     ==>>> get the product id from $_REQUEST['varProdID'];  in the receiving ajax routine. 
                    ************************************************ */                     
                 ?>
                                                        
                 <div class="inPopVar-loading-animation">
										<img title="Loading" alt="Loading" src="<?php echo VTWPR_URL;?>/admin/images/indicator.gif" />
										<?php _e('Getting Variations ...', 'vtwpr'); ?>
								 </div>
                 
                 
                 <a id="ajaxVariationIn" href="javascript:void(0);">
                    <?php if ($product_ID > ' ') {   ?>
                      <?php _e('Refresh Variations', 'vtwpr');?>                      
                    <?php } else {   ?>
                      <?php _e('Get Variations', 'vtwpr');?> 
                    <?php } ?>
                  </a>
                 
              </div>
          </div>
          <div id="variations-in">
          <?php              
/*
           echo '$product_variation_IDs= '.$product_variation_IDs.'<br>' ;
           echo '$product_variation_IDs= '.$product_variation_IDs.'<br>' ;
           echo '$vtwpr_rule <pre>'.print_r($vtwpr_rule, true).'</pre>' ; 
*/           
           if ($product_variation_IDs) { //if product still has variations, expose them here
           ?>
              <h3><?php _e('Product Variations', 'vtwpr');?></h3>                  
            <?php
              //********************************
              $this->vtwpr_post_category_meta_box($post, array( 'args' => array( 'taxonomy' => 'variations', 'tax_class' => 'var-in', 'checked_list' => $vtwpr_rule->var_in_checked, 'pop_in_out_sw' => 'in', 'product_ID' => $product_ID, 'product_variation_IDs' => $product_variation_IDs )));
              // ********************************                            
            }                               
          ?>
            <?php //output hidden count of all variation checkboxes.  Used on update to store info used in 'yousave' messaging?>
            <input type="hidden" id="checkbox_count-var-in" name="checkbox_count-var-in" value="<?php echo $vtwpr_info['inpop_variation_checkbox_total']; ?>" />
           </div>  <?php //end variations-in ?>
        </div>  <?php //end inPopVarProdID ?> 

        <div class="" id="vtwpr-pop-in-groups-cntl">             
        <div class="<?php //echo $groupPop_vis ?> " id="vtwpr-pop-in-cntl">                                                           
        <div  class="clear-left" id="prodcat-in">
          <h3 id="prodcat-in-label"><?php _e('Product Categories', 'vtwpr');?></h3>
          
          <?php
          // ********************************
          $this->vtwpr_post_category_meta_box($post, array( 'args' => array( 'taxonomy' => $vtwpr_info['parent_plugin_taxonomy'], 'tax_class' => 'prodcat-in', 'checked_list' => $vtwpr_rule->prodcat_in_checked, 'pop_in_out_sw' => 'in')));
          // ********************************
          ?>
        
        </div>  <?php //end prodcat-in ?>
        <h4 class="and-or" id="and-or-in-label"><?php _e('Or', 'vtwpr') //('And / Or', 'vtwpr');?></h4>
        <div id="rulecat-in">
          <h3 id="rulecat-in-label"><?php _e('Wholesale Pricing Categories', 'vtwpr');?></h3>
          
          <?php
          // ********************************
          $this->vtwpr_post_category_meta_box($post, array( 'args' => array( 'taxonomy' => $vtwpr_info['rulecat_taxonomy'], 'tax_class' => 'rulecat-in', 'checked_list' => $vtwpr_rule->rulecat_in_checked , 'pop_in_out_sw' => 'in' )));
          // ********************************
          ?> 
                         
        </div>  <?php //end rulecat-in ?>
        
        
        <div id="and-or-role-div">
          <?php
           for($i=0; $i < sizeof($vtwpr_rule_display_framework['role_and_or_in']); $i++) { 
           ?>                               
              <input id="<?php echo $vtwpr_rule_display_framework['role_and_or_in'][$i]['id']; ?>" class="<?php echo $vtwpr_rule_display_framework['role_and_or_in'][$i]['class']; ?>" type="<?php echo $vtwpr_rule_display_framework['role_and_or_in'][$i]['type']; ?>" name="<?php echo $vtwpr_rule_display_framework['role_and_or_in'][$i]['name']; ?>" value="<?php echo $vtwpr_rule_display_framework['role_and_or_in'][$i]['value']; ?>" <?php if ( $vtwpr_rule_display_framework['role_and_or_in'][$i]['value'] == $vtwpr_rule->role_and_or_in) { echo $checked; } ?>    /><span id="<?php echo $vtwpr_rule_display_framework['role_and_or_in'][$i]['id'] . '-label'; ?>"> <?php echo $vtwpr_rule_display_framework['role_and_or_in'][$i]['label']; ?></span><br /> 
           <?php } 
           //if neither 'and' nor 'or' selected, select 'or'
         /*  if ( (!$vtwpr_rule_display_framework['role_and_or_in'][0]['user_input'] == 's') && (!$vtwpr_rule_display_framework['role_and_or_in'][1]['user_input'] == 's') )   {
               $vtwpr_rule_display_framework['role_and_or_in'][1]['user_input'] = 's';
           }   */
                      
           ?>                 
          </div>
        
        
        <div id="role-in">
          <h3 id="role-in-label"><?php _e('Logged-in Role', 'vtwpr');?></h3>
          
          <?php
          // ********************************
          $this->vtwpr_post_category_meta_box($post, array( 'args' => array( 'taxonomy' => 'roles', 'tax_class' => 'role-in', 'checked_list' => $vtwpr_rule->role_in_checked  )));
          // ********************************
          ?>
        </div>
        
       </div> <?php //end vtwpr-pop-in-groups-cntl ?>  
       </div> <?php //end vtwpr-pop-in-cntl ?>  
      
    <?php
 
}
      

                                                                            
    public    function vtwpr_action_group_cntl() { 
       global $post, $vtwpr_info, $vtwpr_rule, $vtwpr_rule_display_framework, $vtwpr_rules_set;
       $selected = 'selected="selected"';
       $checked = 'checked="checked"';       
    ?>                                             

        <span class="amt-field" id="singleChoiceOut-span">                                  
          <span class="amt-field-label" id="singleProdID-out-label"><?php _e('Discount Product ID Number', 'vtwpr');?></span>                    
            <input id="<?php echo $vtwpr_rule_display_framework['actionPop_singleProdID']['id']; ?>" class="<?php echo $vtwpr_rule_display_framework['actionPop_singleProdID']['class']; ?>" type="<?php echo $vtwpr_rule_display_framework['actionPop_singleProdID']['type']; ?>" name="<?php echo $vtwpr_rule_display_framework['actionPop_singleProdID']['name']; ?>" value="<?php echo $vtwpr_rule->actionPop_singleProdID; ?>" />
            <?php vtwpr_show_help_tooltip($context = 'pop-prod-id', $location = 'title'); ?>
          
          <?php if ($vtwpr_rule->actionPop_singleProdID['value'] > ' ' ) { ?>           
              <span class="" id="singleProdID-out-name-area">
                <span class="amt-field-label"  id="singleProdID-out-name-label"><?php _e('Product Name', 'vtwpr'); ?></span>
                <span id="singleProdID-out-name" ><?php echo $vtwpr_rule->actionPop_singleProdID_name; ?></span>
              </span>
          <?php } ?>                                                
        </span>
          
        <!-- </div> -->       

         
        <div id="actionPop-varProdID-cntl">            

          <div id="actionPopVarBox">
              <h3 id="actionPopVarBox_label"><?php _e('Enter Product ID', 'vtwpr');?>
                  <?php vtwpr_show_help_tooltip($context = 'pop-prod-id', $location = 'title'); ?>
              </h3>
              <div id="actionPopVarProduct">                  
                  <input id="<?php echo $vtwpr_rule_display_framework['actionPop_varProdID']['id']; ?>" class="<?php echo $vtwpr_rule_display_framework['actionPop_varProdID']['class']; ?>" type="<?php echo $vtwpr_rule_display_framework['actionPop_varProdID']['type']; ?>" name="<?php echo $vtwpr_rule_display_framework['actionPop_varProdID']['name']; ?>" value="<?php echo $vtwpr_rule->actionPop_varProdID; ?>" />
                 <br />                            
              </div>
              <div id="actionPopVarButton">
                 <?php
                    if ($vtwpr_rule->actionPop_varProdID) {
                      $product_ID = $vtwpr_rule->actionPop_varProdID; 
                      $product_variation_IDs = vtwpr_get_variations_list($product_ID);
                    }   else {                           
                      $product_variation_IDs = array();  //v1.0.3
                    }  
                    /* ************************************************
                    **   Get Variations Button for Rule screen
                    *     ==>>> get the product id from $_REQUEST['varProdID'];  in the receiving ajax routine. 
                    ************************************************ */                     
                 ?>
                                                        
                 <div class="actionPopVar-loading-animation">
										<img title="Loading" alt="Loading" src="<?php echo VTWPR_URL;?>/admin/images/indicator.gif" />
										<?php _e('Getting Variations ...', 'vtwpr'); ?>
								 </div>
                 
                 
                 <a id="ajaxVariationOut" href="javascript:void(0);">
                    <?php if ($product_ID > ' ') {   ?>
                      <?php _e('Refresh Variations', 'vtwpr');?>                      
                    <?php } else {   ?>
                      <?php _e('Get Variations', 'vtwpr');?> 
                    <?php } ?>
                  </a>
                 
              </div>
          </div>
          <div id="variations-out">
          <?php              
           if ($product_variation_IDs) { //if product still has variations, expose them here
           ?>
              <h3><?php _e('Product Variations', 'vtwpr');?></h3>                  
            <?php
              //********************************
              $this->vtwpr_post_category_meta_box($post, array( 'args' => array( 'taxonomy' => 'variations', 'tax_class' => 'var-out', 'checked_list' => $vtwpr_rule->var_out_checked, 'pop_in_out_sw' => 'out', 'product_ID' => $product_ID, 'product_variation_IDs' => $product_variation_IDs )));
              // ********************************
            }                               
          ?>
           </div>  <?php //end variations-out ?>
        </div>  <?php //end actionPopVarProdID ?> 
        
 
        <div class="" id="vtwpr-pop-out-cntl">                                                  
    
        <div class="clear-left" id="prodcat-out">
          <h3 id="prodcat-out-label"><?php _e('Product Categories', 'vtwpr');?></h3>
          
          <?php
          // ********************************
          $this->vtwpr_post_category_meta_box($post, array( 'args' => array( 'taxonomy' => $vtwpr_info['parent_plugin_taxonomy'], 'tax_class' => 'prodcat-out', 'checked_list' => $vtwpr_rule->prodcat_out_checked, 'pop_in_out_sw' => 'out')));
          // ********************************
          ?>
        
        </div>  <?php //end prodcat-out ?>
        <h4 class="and-or"><?php _e('Or', 'vtwpr') //('And / Or', 'vtwpr');?></h4>
        <div id="rulecat-out">
          <h3 id="rulecat-out-label"><?php _e('Wholesale Pricing Categories', 'vtwpr');?></h3>
          
          <?php
          // ********************************
          $this->vtwpr_post_category_meta_box($post, array( 'args' => array( 'taxonomy' => $vtwpr_info['rulecat_taxonomy'], 'tax_class' => 'rulecat-out', 'checked_list' => $vtwpr_rule->rulecat_out_checked , 'pop_in_out_sw' => 'out')));
          // ********************************
          ?> 
          
          <?php
            /*
            REMOVED and/or  ROLES for action area =>SUPERFLOUS
            */
          ?>
          
                         
        </div>  <?php //end rulecat-out ?>
        

      </div> <?php //end vtwpr-pop-out-cntl ?> 
  
  <?php
    }  
      
  
    public    function vtwpr_pop_in_specifics( ) {                     
       global $post, $vtwpr_info, $vtwpr_rule; $vtwpr_rules_set;
       $checked = 'checked="checked"';  
  ?>
        
       <div class="column1" id="specDescrip">
          <h4><?php _e('How is the Rule applied to the search results?', 'vtwpr');?></h4>
          <p><?php _e("Once we've figured out the population we're working on (cart only or specified groups),
          how do we apply the rule?  Do we look at each product individually and apply the rule to
          each product we find?  Or do we look at the population as a group, and apply the rule to the
          group as a tabulated whole?  Or do we apply the rule to any we find, and limit the application 
          of the rule to a certain number of products?", 'vtwpr');?>           
          </p>
       </div>
       <div class="column2" id="specChoiceIn">
          <h3><?php _e('Select Rule Application Method', 'vtwpr');?></h3>
          <div id="specRadio">
            <span id="Choice-input-span">
                <?php
               for($i=0; $i < sizeof($vtwpr_rule->specChoice_in); $i++) { 
               ?>                 

                  <input id="<?php echo $vtwpr_rule->specChoice_in[$i]['id']; ?>" class="<?php echo $vtwpr_rule->specChoice_in[$i]['class']; ?>" type="<?php echo $vtwpr_rule->specChoice_in[$i]['type']; ?>" name="<?php echo $vtwpr_rule->specChoice_in[$i]['name']; ?>" value="<?php echo $vtwpr_rule->specChoice_in[$i]['value']; ?>" <?php if ( $vtwpr_rule->specChoice_in[$i]['user_input'] > ' ' ) { echo $checked; } ?> /><?php echo $vtwpr_rule->specChoice_in[$i]['label']; ?><br />

               <?php
                }
               ?>  
            </span>
            <span class="" id="anyChoiceIn-span">
                <span><?php _e('*Any* applies to a *required*', 'vtwpr');?></span><br />
                 <?php _e('Maximum of:', 'vtwpr');?>                      
                 <input id="<?php echo $vtwpr_rule->anyChoiceIn_max['id']; ?>" class="<?php echo $vtwpr_rule->anyChoiceIn_max['class']; ?>" type="<?php echo $vtwpr_rule->anyChoiceIn_max['type']; ?>" name="<?php echo $vtwpr_rule->anyChoiceIn_max['name']; ?>" value="<?php echo $vtwpr_rule->anyChoiceIn_max['value']; ?>" />
                 <?php _e('Products', 'vtwpr');?>
            </span>           
          </div>                
       </div>                                                
       <div class="column3 specExplanation" id="allChoiceIn-chosen">
          <h4><?php _e('Treat the Selected Group as a Single Entity', 'vtwpr');?><span> - <?php _e('explained', 'vtwpr');?></span></h4>
          <p><?php _e("Using *All* as your method, you choose to look at all the products from your cart search results.  That means we add
          all the quantities and/or price across all relevant products in the cart, to test against the rule's requirements.", 'vtwpr');?>           
          </p>
       </div>
       <div class="column3 specExplanation" id="eachChoiceIn-chosen">
          <h4><?php _e('Each in the Selected Group', 'vtwpr');?><span> - <?php _e('explained', 'vtwpr');?></span></h4>
          <p><?php _e("Using *Each* as your method, we apply the rule to each product from your cart search results.
          So if any of these products fail to meet the rule's requirements, the cart as a whole receives an error message.", 'vtwpr');?>           
          </p>
       </div>
       <div class="column3 specExplanation" id="anyChoiceIn-chosen">
          <h4><?php _e('Apply the rule to any Individual Product in the Cart', 'vtwpr');?><span> - <?php _e('explained', 'vtwpr');?></span></h4>
          <p><?php _e("Using *Any*, we can apply the rule to any product in the cart from your cart search results, similar to *Each*.  However, there is a
          maximum number of products to which the rule is applied. The product group is checked to see if any of the group fail to reach the maximum amount
          threshhold.  If so, the error will be applied to products in the cart based on cart order, up to the maximum limit supplied.", 'vtwpr');?>
          <br /> <br /> 
          <?php _e('For example, the rule might be something like:', 'vtwpr');?>
          <br /> <br /> &nbsp;&nbsp;
          <?php _e('"You may buy a maximum of $10 for each of any of 2 products from this group."', 'vtwpr');?>              
          </p>               
       </div> 
      
    <?php
  }  
                                                                           
    public    function vtwpr_rule_id() {          
        global $post, $vtwpr_rule;           
       
        if ($vtwpr_rule->ruleInWords > ' ') { ?>
            <span class="ruleInWords" >              
               <span class="clear-left">  <?php echo $vtwpr_rule->ruleInWords; ?></span><!-- /clear-left -->                              
            </span><!-- /ruleInWords -->              
        <?php } //end ruleInWords 
  } 
  
    public    function vtwpr_rule_resources() {          
        echo '<a id="vtwpr-rr-doc"  href="' . VTWPR_DOCUMENTATION_PATH_PRO_BY_PARENT . '"  title="Access Plugin Documentation">' . __('Plugin', 'vtwpr'). '<br>' . __('Documentation', 'vtwpr'). '</a>';
        //Back to the Top box, fixed at lower right corner!!!!!!!!!!
        echo '<a href="#" id="back-to-top-tab" class="show-tab">' . __('Back to Top', 'vtwpr'). ' <strong>&uarr;</strong></a>';
  }   

      
    public    function vtwpr_rule_scheduling() {             //periodicByDateRange
        global $vtwpr_rule;
        
        //**********************************************************************************
        //script goes here, rather than in enqueued resources, due to timing issues 
        //**********************************************************************************
       ?>     
          <script type="text/javascript">
          jQuery.noConflict();
          jQuery(document).ready(function($) {
             //DatePicker                       
             // from  http://jquerybyexample.blogspot.com/2012/01/end-date-should-not-be-greater-than.html
                $("#date-begin-0").datepicker({
                  dateFormat : 'yy-mm-dd', 
                  minDate: 0,
                 // maxDate: "+60D",
                  numberOfMonths: 2,
                  onSelect: function(selected) {
                    $("#date-end-0").datepicker("option","minDate", selected)
                  }
              });
              $("#date-end-0").datepicker({ 
                  dateFormat : 'yy-mm-dd', 
                  minDate: 0,
                 // maxDate:"+60D",
                  numberOfMonths: 2,
                  onSelect: function(selected) {
                     $("#date-begin-0").datepicker("option","maxDate", selected)
                  }                             
              });

            });   
          </script>                            
     <?php       
     //load up default if no date range
     if ( sizeof($vtwpr_rule->periodicByDateRange) == 0 ) {     
        $vtwpr_rule->periodicByDateRange[0]['rangeBeginDate'] = date('Y-m-d');
        $vtwpr_rule->periodicByDateRange[0]['rangeEndDate']   = (date('Y')+1) . date('-m-d') ;
     } 
     ?> 
        <span class="basic-begin-date-area blue-dropdown"> 
            <label class="begin-date first-in-line-label"><?php _e('Begin Date', 'vtwpr');?></label> 
            <input type='text' id='date-begin-0' class='pickdate  clear-left' size='7' value="<?php echo $vtwpr_rule->periodicByDateRange[0]['rangeBeginDate']; ?>" name='date-begin-0' readonly="readonly" />				
        </span>        
        <span class="basic-end-date-area blue-dropdown">          
          <label class="end-date first-in-line-label"><?php _e('End Date', 'vtwpr');?></label>                      
          <input type='text' id='date-end-0'   class='pickdate   clear-left' size='7' value="<?php echo $vtwpr_rule->periodicByDateRange[0]['rangeEndDate']; ?>"   name='date-end-0' readonly="readonly"  />          
        </span>        
        
    <?php      
       global $vtwpr_setup_options;
       /* scares the punters
       if ( $vtwpr_setup_options['use_this_timeZone'] == 'none') {
          echo __('<span id="options-setup-error" style="color:red !important;">Scheduling requires setup: <a  href="/wp-admin/edit.php?post_type=vtwpr-rule&page=vtwpr_setup_options_page"  title="select">Please - Click Here - to Select the Store GMT Time Zone</a></span>', 'vtwpr'); 
        }          
      */
  }   

  public  function vtwpr_change_title_currency_symbol( $variable_name, $i, $currency_symbol ) {
     global $vtwpr_deal_screen_framework;
      //replace $$ with setup currency!!                        
      $vtwpr_deal_screen_framework[$variable_name]['option'][$i]['title'] = 
                str_replace('$$', $currency_symbol, $vtwpr_deal_screen_framework[$variable_name]['option'][$i]['title'] );
  }    
       
  public  function vtwpr_post_category_meta_box( $post, $box ) {
      $defaults = array('taxonomy' => 'category');
      if ( !isset($box['args']) || !is_array($box['args']) )
          $args = array();
      else
          $args = $box['args'];
      extract( wp_parse_args($args, $defaults), EXTR_SKIP );
      $tax = get_taxonomy($taxonomy);

      ?>
      <div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">
   
          <div id="<?php echo $taxonomy; ?>-pop" class="tabs-panel" style="display: none;">
              <ul id="<?php echo $taxonomy; ?>checklist-pop" class="categorychecklist form-no-clear" >
                  <?php $popular_ids = wp_popular_terms_checklist($taxonomy); ?>
              </ul>
          </div>
   
          <div id="<?php echo $taxonomy; ?>-all" class="tabs-panel">
              <?php
              $name = ( $taxonomy == 'category' ) ? 'post_category' : 'tax_input[' .  $tax_class . ']';     //vark replaced $taxonomy with $tax_class
              echo "<input type='hidden' name='{$name}[]' value='0' />"; // Allows for an empty term set to be sent. 0 is an invalid Term ID and will be ignored by empty() checks.
              ?>
              <ul id="<?php echo $taxonomy; ?>checklist" class="list:<?php echo $taxonomy?> categorychecklist form-no-clear">
      <?php    

            switch( $taxonomy ) {
              case 'roles': 
                  $vtwpr_checkbox_classes = new VTWPR_Checkbox_classes; 
                  $vtwpr_checkbox_classes->vtwpr_fill_roles_checklist($tax_class, $checked_list);
                break;
              case 'variations':                  
                  vtwpr_fill_variations_checklist($tax_class, $checked_list, $pop_in_out_sw, $product_ID, $product_variation_IDs);                            
                break;
              default:  //product category or vtwpr category...
                  $this->vtwpr_build_checkbox_contents ($taxonomy, $tax_class, $checked_list, $pop_in_out_sw);                             
                break;
            }
            
      ?>  
              </ul>
          </div>
      <?php if ( current_user_can($tax->cap->edit_terms) && !($taxonomy == 'roles') && !($taxonomy == 'variations') ): ?>
              <div id="<?php echo $taxonomy; ?>-adder" class="wp-hidden-children">
                  <h4>
                      <a id="<?php echo $taxonomy; ?>-add-toggle" href="#<?php echo $taxonomy; ?>-add" class="hide-if-no-js" tabindex="3">
                          <?php
                              /* translators: %s: add new taxonomy label */
                              printf( __( '+ %s' ), $tax->labels->add_new_item );
                          ?>
                      </a>
                  </h4>
                  <p id="<?php echo $taxonomy; ?>-add" class="category-add wp-hidden-child">
                      <label class="screen-reader-text" for="new<?php echo $taxonomy; ?>"><?php echo $tax->labels->add_new_item; ?></label>
                      <input type="text" name="new<?php echo $taxonomy; ?>" id="new<?php echo $taxonomy; ?>" class="form-required form-input-tip" value="<?php echo esc_attr( $tax->labels->new_item_name ); ?>" tabindex="3" aria-required="true"/>
                      <label class="screen-reader-text" for="new<?php echo $taxonomy; ?>_parent">
                          <?php echo $tax->labels->parent_item_colon; ?>
                      </label>
                      <?php wp_dropdown_categories( array( 'taxonomy' => $taxonomy, 'hide_empty' => 0, 'name' => 'new'.$taxonomy.'_parent', 'orderby' => 'name', 'hierarchical' => 1, 'show_option_none' => '&mdash; ' . $tax->labels->parent_item . ' &mdash;', 'tab_index' => 3 ) ); ?>
                      <input type="button" id="<?php echo $taxonomy; ?>-add-submit" class="add:<?php echo $taxonomy ?>checklist:<?php echo $taxonomy ?>-add button category-add-sumbit" value="<?php echo esc_attr( $tax->labels->add_new_item ); ?>" tabindex="3" />
                      <?php wp_nonce_field( 'add-'.$taxonomy, '_ajax_nonce-add-'.$taxonomy, false ); ?>
                      <span id="<?php echo $taxonomy; ?>-ajax-response"></span>
                  </p>
              </div>
          <?php endif; ?>
      </div>
      <?php
}


    function vtwpr_load_forThePriceOf_literal($k) {
      global $vtwpr_rule;
     if (($vtwpr_rule->rule_deal_info[$k]['discount_amt_type'] =='forThePriceOf_Units') ||
         ($vtwpr_rule->rule_deal_info[$k]['discount_amt_type'] =='forThePriceOf_Currency')) {
        switch ($vtwpr_rule->rule_template) {
          case 'C-forThePriceOf-inCart':    //buy-x-action-forThePriceOf-same-group-discount              
              echo ' Buy ';
              echo $vtwpr_rule->rule_deal_info[$k]['buy_amt_count'];
            break;
          case 'C-forThePriceOf-Next':  //buy-x-action-forThePriceOf-other-group-discount
              echo ' Get ';
              echo $vtwpr_rule->rule_deal_info[$k]['action_amt_count'];
            break;
        }
      }
    }


    //remove conflict with all-in-one seo pack!!  
    //  from http://wordpress.stackexchange.com/questions/55088/disable-all-in-one-seo-pack-for-some-custom-post-types
    function vtwpr_remove_all_in_one_seo_aiosp() {
        $cpts = array( 'vtwpr-rule' );
        foreach( $cpts as $cpt ) {
            remove_meta_box( 'aiosp', $cpt, 'advanced' );
        }
    }


    
  /*
    *  taxonomy (r) - registered name of taxonomy
    *  tax_class (r) - name options => 'prodcat-in' 'prodcat-out' 'rulecat-in' 'rulecat-out'
    *             refers to product taxonomy on the candidate or action categories,
    *                       rulecat taxonomy on the candidate or action categories
    *                         :: as there are only these 4, they are unique   
    *  checked_list (o) - selection list from previous iteration of rule selection                              
    *                          
   */

  public function vtwpr_build_checkbox_contents ($taxonomy, $tax_class, $checked_list = NULL, $pop_in_out_sw ) {
        global $wpdb, $vtwpr_info;         
        $sql = "SELECT terms.`term_id`, terms.`name`  FROM `" . $wpdb->prefix . "terms` as terms, `" . $wpdb->prefix . "term_taxonomy` as term_taxonomy WHERE terms.`term_id` = term_taxonomy.`term_id` AND term_taxonomy.`taxonomy` = '" . $taxonomy . "' ORDER BY terms.`term_id` ASC";                         
		    $categories = $wpdb->get_results($sql,ARRAY_A) ;
        
        foreach ($categories as $category) {
            
            $term_id = $category['term_id'];
            
            $output  = '<li id='.$taxonomy.'-'.$term_id.'>' ;
            $output  .= '<label class="selectit  '.$taxonomy.'-list-checkbox">' ;
            $output  .= '<input id="'.$tax_class.'.'.$taxonomy.'-'.$term_id.' " ';
            $output  .= 'type="checkbox" name="tax-input-' .  $tax_class . '[]" ';
            $output  .= 'value="'.$term_id.'" ';
            $check_found = 'no';
            if ($checked_list) {
                if (in_array($term_id, $checked_list)) {   //if cat_id is in previously checked_list      if (in_array("Irix", $os)) {
                   $output  .= 'checked="checked"';
                   $check_found = 'yes';
                }               
            }
           /*
            if ( ($taxonomy == $vtwpr_info['parent_plugin_taxonomy']) || ($taxonomy == $vtwpr_info['rulecat_taxonomy']) )           {       
                  $output  .= ' disabled="disabled"';
            }
            */
            $output  .= ' />'; //end input statement
            $output  .= '&nbsp;' . $category['name'];
            $output  .= '</label>';            
            $output  .= '</li>';
              echo $output ;    
         }
         return;
    }



    /*
     *  ==========================
     *     AJAX Functions
     *  ==========================                                
     */

    public function vtwpr_ajax_load_variations_in() {
    global $wpdb, $post, $vtwpr_rule;
       /*  *********************************************
         USE exit rather than return
         as the return statement engerders a 0 return code in the ajax
         which displays as an errant '0' with the ajax display. 
        ********************************************* */
    $vtwpr_rule->inPop_varProdID  = $_POST['inVarProdID'];  //from var *passed in from ajax js     
    $product_ID = $vtwpr_rule->inPop_varProdID;
    $product_variation_IDs = $this->vtwpr_ajax_edit_product($product_ID, 'in');
    
    if ($vtwpr_rule->rule_error_message[0] > ' ') {
       echo '<div id="inVariationsError">';
       echo $vtwpr_rule->rule_error_message[0];
       echo '</div>';
    } else {
       $this->vtwpr_ajax_show_variations_in ($product_variation_IDs); 
    }
          
    exit;
  }   //end ajax_load_variations_in(

    public function vtwpr_ajax_load_variations_out() {
    global $wpdb, $post, $vtwpr_rule;
       /*  *********************************************
         USE exit rather than return
         as the return statement engerders a 0 return code in the ajax
         which displays as an errant '0' with the ajax display. 
        ********************************************* */
    $vtwpr_rule->actionPop_varProdID  = $_POST['outVarProdID'];  //from var *passed in from ajax js     
    $product_ID = $vtwpr_rule->actionPop_varProdID;
    $product_variation_IDs = $this->vtwpr_ajax_edit_product($product_ID, 'out');
    
    if ($vtwpr_rule->rule_error_message[0] > ' ') {
       echo '<div id="outVariationsError">';
       echo $vtwpr_rule->rule_error_message[0];
       echo '</div>';
    } else {
       $this->vtwpr_ajax_show_variations_out ($product_variation_IDs);
    }
          
    exit;
  }   //end ajax_load_variations_out(
     
  public function vtwpr_ajax_edit_product($product_ID, $inOrOut) {
    global $wpdb, $post, $vtwpr_rule, $vtwpr_setup_options;
    
    //edits copied from vtwpr-rules-update.php
    if ($product_ID == ' '){
      $vtwpr_rule->rule_error_message[] = __('No Product ID was supplied.', 'vtwpr');
      return;
    } 
     
    if ( is_numeric($product_ID) === false ) {
       $vtwpr_rule->rule_error_message[0] =  __('<br><br>Product ID in error = <span id="varProdID-error-ID">', 'vtwpr')   .$product_ID .    __('</span>', 'vtwpr') ;
               
       if ( $vtwpr_setup_options['debugging_mode_on'] == 'yes' ){
          $vtwpr_rule->rule_error_message[0] =  __('<br><br>Product ID in error = <span id="varProdID-error-ID">', 'vtwpr')   .$product_ID .    __('</span>', 'vtwpr') ;
       }              
       return;
    } 
    
    $test_post = get_post($product_ID);
    if (!$test_post ) {
       $vtwpr_rule->rule_error_message[] =  __('Product ID was not found. >', 'vtwpr') ;                    
      if ( $vtwpr_setup_options['debugging_mode_on'] == 'yes' ){
          $vtwpr_rule->rule_error_message[0] =  __('<br><br>Product ID in error = <span id="varProdID-error-ID">', 'vtwpr')   .$product_ID .    __('</span>', 'vtwpr') ; 
       }  
      return;
    }
    
    if ($inOrOut == 'in') {
      $vtwpr_rule->inPop_varProdID_name      = $test_post->post_title;
    } else {
      $vtwpr_rule->actionPop_varProdID_name  = $test_post->post_title;
    }
    
    
    $product_has_variations = vtwpr_test_for_variations($product_ID);

    if ($product_has_variations == 'no') {
      $vtwpr_rule->rule_error_message[] =  __('Product has no Variations. Product Name = ', 'vtwpr') .$test_post->post_title.   __('<br><br> Please use "Single Product Only" option, above.', 'vtwpr') ;
      if ( $vtwpr_setup_options['debugging_mode_on'] == 'yes' ){
          $vtwpr_rule->rule_error_message[0] =  __('<br><br>Product ID in error = <span id="varProdID-error-ID">', 'vtwpr')   .$product_ID .    __('</span>', 'vtwpr') ;
       }  
      return;
    }
    
    $product_variation_IDs = vtwpr_get_variations_list($product_ID);
    if (sizeof($product_variation_IDs) == 0) {     //v1.0.3
      $vtwpr_rule->rule_error_message[] = __('Product has no Variations. Product Name = ', 'vtwpr') .$test_post->post_title.   __('<br><br> Please use "Single Product Only" option, above.', 'vtwpr') ;
      if ( $vtwpr_setup_options['debugging_mode_on'] == 'yes' ){
          $vtwpr_rule->rule_error_message[0] =  __('<br><br>Product ID in error = <span id="varProdID-error-ID">', 'vtwpr')   .$product_ID .    __('</span>', 'vtwpr') ;
       }  
      return;
    }
    
    return ($product_variation_IDs);
  } 
  
     
  public function vtwpr_ajax_show_variations_in ($product_variation_IDs) {
     global $post, $vtwpr_info, $vtwpr_rule; $vtwpr_rules_set;
     ?>             
          <h3><?php _e('Product Variations', 'vtwpr');?></h3>                  
     <?php
            //********************************
            $this->vtwpr_post_category_meta_box($post, array( 'args' => array( 'taxonomy' => 'variations', 'tax_class' => 'var-in', 'checked_list' => $vtwpr_rule->var_in_checked, 'product_ID' => $vtwpr_rule->inPop_varProdID, 'product_variation_IDs' => $product_variation_IDs )));
            // ******************************** 
            //output hidden count of all variation checkboxes.  Used on update to store info used in 'yousave' messaging
            ?>
            <input type="hidden" id="checkbox_count-var-in" name="checkbox_count-var-in" value="<?php echo $vtwpr_info['inpop_variation_checkbox_total']; ?>" />
            <?php                 
  } 
  
  public function vtwpr_ajax_show_variations_out ($product_variation_IDs) {
     global $post, $vtwpr_info, $vtwpr_rule; $vtwpr_rules_set;
     ?>             
          <h3><?php _e('Product Variations', 'vtwpr');?></h3>                  
     <?php
            //********************************
            $this->vtwpr_post_category_meta_box($post, array( 'args' => array( 'taxonomy' => 'variations', 'tax_class' => 'var-out', 'checked_list' => $vtwpr_rule->var_out_checked, 'product_ID' => $vtwpr_rule->actionPop_varProdID, 'product_variation_IDs' => $product_variation_IDs )));
            // ********************************                 
  } 

  //     END AJAX Functions


  // *********************************************************
  //   META BOX for PARENT PLUGIN PRODUCT SCREEN
  // *********************************************************          
  public  function vtwpr_parent_product_meta_box_cntl() {
      global $post, $vtwpr_info, $vtwpr_rule, $vtwpr_rules_set;        
      
      if(defined('VTWPR_PRO_DIRNAME')) {
        $metabox_title =  __('Wholesale Pricing: Product Include or Exclude', 'vtwpr');
      } else {
        $metabox_title =  __('Wholesale Pricing: Product Include or Exclude', 'vtwpr') . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . __('(Available with the Pro plugin)', 'vtwpr') ;
      }
      
      add_meta_box('vtwpr-wholesale_pricing-info', $metabox_title , array(&$this, 'vtwpr_add_parent_product_meta_box'), $vtwpr_info ['parent_plugin_cpt'], 'normal', 'low');                           
  }                   
  /*
  // *********************************************************
     add a meta box to the PARENT PLUGIN'S PRODUCT SCREEN
       * Rule include/exclude info at the product level
       * anchor redirect in the category matabox, to this box, 
       *        inserts directly onto the page 
         from vtwpr-admin-product-metabox-script.js
  // ********************************************************* 
  */      
  public  function vtwpr_add_parent_product_meta_box() {
      global $post, $vtwpr_info, $vtwpr_rule, $vtwpr_rules_set, $vtwpr_rule_display_framework ;        
      $selected = 'selected="selected"';
      
      if ( get_post_meta($post->ID, $vtwpr_info['product_meta_key_includeOrExclude'], true) ) {
        $vtwpr_includeOrExclude = get_post_meta($post->ID, $vtwpr_info['product_meta_key_includeOrExclude'], true);
      } else {
        $vtwpr_includeOrExclude = array (
            'includeOrExclude_option'    => 'includeAll', //initialize the value...
            'includeOrExclude_checked_list'    => array() 
          );
      }
      
      ?>    
        <?php //pass literals up to JS, translated here if necessary ?>
        <input type="hidden" id="vtwpr-sectionTitle" name="vtwpr-sectionTitle" value="<?php _e('Wholesale Pricing Include or Exclude', 'vtwpr');?>" />
        <input type="hidden" id="vtwpr-urlTitle" name="vtwpr-urlTitle" value="<?php _e('Product Include or Exclude', 'vtwpr');?>" />      
        <input id="vtwpr-pluginVersion" type="hidden" value="<?php if(defined('VTWPR_PRO_DIRNAME')) { echo "proVersion"; } else { echo "freeVersion"; } ?>" name="vtwpr-pluginVersion" />       
        
        <h4 id="includeOrExclude-area-title"><?php _e('*Include or Exclude Product*', 'vtwpr'); echo '&nbsp;'; _e(' in Wholesale Pricing Rule processing, based on the Options and Rule List below', 'vtwpr');?></h4>                    
        <div class="dropdown includeOrExclude_area clear-left" id="includeOrExclude_areaID">              
           <span class="dropdown-label" id="includeOrExclude_label"><?php _e('Product Options:', 'vtwpr');?></span>               
           <select id="<?php echo $vtwpr_rule_display_framework['includeOrExclude']['select']['id'];?>" class="<?php echo$vtwpr_rule_display_framework['includeOrExclude']['select']['class']; ?>" name="<?php echo $vtwpr_rule_display_framework['includeOrExclude']['select']['name'];?>" tabindex="<?php echo $vtwpr_rule_display_framework['includeOrExclude']['select']['tabindex']; ?>" >          
             <?php
             for($i=0; $i < sizeof($vtwpr_rule_display_framework['includeOrExclude']['option']); $i++) {            
             ?>                             
                <option id="<?php echo $vtwpr_rule_display_framework['includeOrExclude']['option'][$i]['id']; ?>"  class="<?php echo $vtwpr_rule_display_framework['includeOrExclude']['option'][$i]['class']; ?>"  value="<?php echo $vtwpr_rule_display_framework['includeOrExclude']['option'][$i]['value']; ?>" <?php if ($vtwpr_rule_display_framework['includeOrExclude']['option'][$i]['value'] == $vtwpr_includeOrExclude['includeOrExclude_option'] )  { echo $selected; } ?> >  <?php echo $vtwpr_rule_display_framework['includeOrExclude']['option'][$i]['title']; ?> </option>
             <?php } ?> 
           </select> 
           <?php vtwpr_show_help_tooltip($context = 'includeOrExclude', $location = 'title'); ?>
        </div>
 
          <div id="includeOrExclude-all" class="tabs-panel">
            <h3 id="includeOrExclude-title">Wholesale Pricing Rule List</h3>
            <p id="includeOrExclude-area-title2"><?php _e("These selections do not ", 'vtwpr'); echo '<em>'; _e("force", 'vtwpr'); echo '</em>'; 
                _e(" the product into a rule.  ", 'vtwpr'); echo '<em>'; _e("Inclusion only applies if the product naturally falls
                into the specified rule populations already.", 'vtwpr'); echo '</em>'; ?></p>
            <ul id="includeOrExclude-checklist" class="categorychecklist form-no-clear">   
                  <?php  vtwpr_fill_include_exclude_lists ($vtwpr_includeOrExclude['includeOrExclude_checked_list'])?>  
            </ul>
          </div>      
      
      
      <?php 
       
      return;
  }    
 
         
  public  function vtwpr_build_new_rule() {
      global $post, $vtwpr_info, $vtwpr_rule, $vtwpr_rules_set, $vtwpr_deal_structure_framework; 
                    
        //initialize rule
        $vtwpr_rule = new VTWPR_Rule;
 
         //fill in standard default values not already supplied
         
        //load the 1st iteration of deal info by default    => internal defaults set in vtwpr_deal_structure_framework
        
        $vtwpr_rule->rule_deal_info[] = $vtwpr_deal_structure_framework;  

        $vtwpr_rule->rule_deal_info[0]['buy_repeat_condition'] = 'unlimited'; 
        $vtwpr_rule->rule_deal_info[0]['buy_amt_type'] = 'none';
        $vtwpr_rule->rule_deal_info[0]['buy_amt_mod'] = 'none';
        $vtwpr_rule->rule_deal_info[0]['buy_amt_applies_to'] = 'all';
        $vtwpr_rule->rule_deal_info[0]['action_repeat_condition'] = 'none'; 
        $vtwpr_rule->rule_deal_info[0]['action_amt_type'] = 'none';  
        $vtwpr_rule->rule_deal_info[0]['action_amt_mod'] = 'none';
        $vtwpr_rule->rule_deal_info[0]['action_amt_applies_to'] = 'all';
        $vtwpr_rule->rule_deal_info[0]['discount_amt_type'] = '0';
        $vtwpr_rule->rule_deal_info[0]['discount_applies_to'] = 'each';
        $vtwpr_rule->rule_deal_info[0]['discount_rule_max_amt_type'] = 'none';
        $vtwpr_rule->rule_deal_info[0]['discount_lifetime_max_amt_type'] = 'none';
        $vtwpr_rule->rule_deal_info[0]['discount_rule_cum_max_amt_type'] = 'none'; 
        $vtwpr_rule->cumulativeRulePricing = 'yes';  
        $vtwpr_rule->cumulativeSalePricing = 'addToSalePrice';   //v1.0.3 
        $vtwpr_rule->cumulativeCouponPricing = 'yes';
               //discount occurs 5 times
        $vtwpr_rule->ruleApplicationPriority_num = '10';         
        $vtwpr_rule->rule_type_selected_framework_key =  'Title01'; //default 1st title for BOTH dropdowns
        
        $vtwpr_rule->inPop = 'wholeStore';  //apply to all products
        $vtwpr_rule->role_and_or_in = 'or';
        $vtwpr_rule->actionPop = 'sameAsInPop' ; 
        $vtwpr_rule->role_and_or_out = 'or';
        
        //new upper selects 
        $vtwpr_rule->cart_or_catalog_select = 'catalog';
        $vtwpr_rule->pricing_type_select = 'choose';
        $vtwpr_rule->minimum_purchase_select = 'none';
        $vtwpr_rule->buy_group_filter_select = 'choose';
        $vtwpr_rule->get_group_filter_select = 'choose';
        $vtwpr_rule->rule_on_off_sw_select = 'on';
        $vtwpr_rule->wizard_on_off_sw_select = 'off';
        $vtwpr_rule->rule_type_select = 'basic';          
         
    return;
  }        
     //lots of selects change their values between standard and 'discounted' titles.
    //This is where we supply the HIDEME alternative titles
  public  function vtwpr_print_alternative_title_selects() {
      global $vtwpr_rule_display_framework, $vtwpr_deal_screen_framework;
      ?>          
             
           <?php 
           /* +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-
             Hidden Selects containing various versions of the Select Option texts.
             
                #1  = the default version of the titles
                #2  = the altenate (Discount) version of the titles
              
              Both are supplied, so the JS can toggle between these two sets,
              as needed by the Upper select choices
              +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-
           */ ?>  
             <?php //Upper  pricint_type_select?>  
              <select id="<?php echo $vtwpr_rule_display_framework['pricing_type_select']['select']['id'] .'1';?>" class="<?php echo$vtwpr_rule_display_framework['pricing_type_select']['select']['class'] .'1'; ?> hideMe" name="<?php echo $vtwpr_rule_display_framework['pricing_type_select']['select']['name'] .'1';?>" tabindex="<?php //echo $vtwpr_rule_display_framework['pricing_type_select']['select']['tabindex']; ?>" >          
                 <?php
                 for($i=0; $i < sizeof($vtwpr_rule_display_framework['pricing_type_select']['option']); $i++) { 
                                             
                      //pick up the free/pro version of the title => in this case, title and title3
                      $title = $vtwpr_rule_display_framework['pricing_type_select']['option'][$i]['title'];
                 ?>                             
                    <option id="<?php echo $vtwpr_rule_display_framework['pricing_type_select']['option'][$i]['id'] .'1'; ?>"  class="<?php echo $vtwpr_rule_display_framework['pricing_type_select']['option'][$i]['class'] .'1'; ?>"  value="<?php echo $vtwpr_rule_display_framework['pricing_type_select']['option'][$i]['value']; ?>"    ><?php echo $title; ?></option>
                 <?php } ?> 
               </select>                                        
              <select id="<?php echo $vtwpr_rule_display_framework['pricing_type_select']['select']['id'] .'-catalog';?>" class="<?php echo$vtwpr_rule_display_framework['pricing_type_select']['select']['class'] .'-catalog'; ?> hideMe" name="<?php echo $vtwpr_rule_display_framework['pricing_type_select']['select']['name'] .'-catalog';?>" tabindex="<?php //echo $vtwpr_rule_display_framework['pricing_type_select']['select']['tabindex']; ?>" >          
                 <?php
                 for($i=0; $i < sizeof($vtwpr_rule_display_framework['pricing_type_select']['option']); $i++) { 
                                             
                      //pick up the free/pro version of the title 
                      $title = $vtwpr_rule_display_framework['pricing_type_select']['option'][$i]['title-catalog'];
                
                 ?>                             
                    <option id="<?php echo $vtwpr_rule_display_framework['pricing_type_select']['option'][$i]['id'] .'-catalog'; ?>"  class="<?php echo $vtwpr_rule_display_framework['pricing_type_select']['option'][$i]['class'] .'-catalog'; ?>"  value="<?php echo $vtwpr_rule_display_framework['pricing_type_select']['option'][$i]['value']; ?>"    ><?php echo $title; ?></option>
                 <?php } ?> 
               </select>   
                          
             
             <?php //Upper  minimum_purchase_select?>  
              <select id="<?php echo $vtwpr_rule_display_framework['minimum_purchase_select']['select']['id'] .'1';?>" class="<?php echo$vtwpr_rule_display_framework['minimum_purchase_select']['select']['class'] .'1'; ?> hideMe" name="<?php echo $vtwpr_rule_display_framework['minimum_purchase_select']['select']['name'] .'1';?>" tabindex="<?php //echo $vtwpr_rule_display_framework['minimum_purchase_select']['select']['tabindex']; ?>" >          
                 <?php
                 for($i=0; $i < sizeof($vtwpr_rule_display_framework['minimum_purchase_select']['option']); $i++) { 
                                             
                      //pick up the free/pro version of the title => in this case, title and title3
                      $title = $vtwpr_rule_display_framework['minimum_purchase_select']['option'][$i]['title'];
                 ?>                             
                    <option id="<?php echo $vtwpr_rule_display_framework['minimum_purchase_select']['option'][$i]['id'] .'1'; ?>"  class="<?php echo $vtwpr_rule_display_framework['minimum_purchase_select']['option'][$i]['class'] .'1'; ?>"  value="<?php echo $vtwpr_rule_display_framework['minimum_purchase_select']['option'][$i]['value']; ?>"    ><?php echo $title; ?></option>
                 <?php } ?> 
               </select>                                        
              <select id="<?php echo $vtwpr_rule_display_framework['minimum_purchase_select']['select']['id'] .'-catalog';?>" class="<?php echo$vtwpr_rule_display_framework['minimum_purchase_select']['select']['class'] .'-catalog'; ?> hideMe" name="<?php echo $vtwpr_rule_display_framework['minimum_purchase_select']['select']['name'] .'-catalog';?>" tabindex="<?php //echo $vtwpr_rule_display_framework['minimum_purchase_select']['select']['tabindex']; ?>" >          
                 <?php
                 for($i=0; $i < sizeof($vtwpr_rule_display_framework['minimum_purchase_select']['option']); $i++) { 
                                             
                      //pick up the free/pro version of the title 
                      $title = $vtwpr_rule_display_framework['minimum_purchase_select']['option'][$i]['title-catalog'];
                
                 ?>                             
                    <option id="<?php echo $vtwpr_rule_display_framework['minimum_purchase_select']['option'][$i]['id'] .'-catalog'; ?>"  class="<?php echo $vtwpr_rule_display_framework['minimum_purchase_select']['option'][$i]['class'] .'-catalog'; ?>"  value="<?php echo $vtwpr_rule_display_framework['minimum_purchase_select']['option'][$i]['value']; ?>"    ><?php echo $title; ?></option>
                 <?php } ?> 
               </select>   
             
             <?php //Upper  buy_group_filter_select?>  
              <select id="<?php echo $vtwpr_rule_display_framework['buy_group_filter_select']['select']['id'] .'1';?>" class="<?php echo$vtwpr_rule_display_framework['buy_group_filter_select']['select']['class'] .'1'; ?> hideMe" name="<?php echo $vtwpr_rule_display_framework['buy_group_filter_select']['select']['name'] .'1';?>" tabindex="<?php //echo $vtwpr_rule_display_framework['buy_group_filter_select']['select']['tabindex']; ?>" >          
                 <?php
                 for($i=0; $i < sizeof($vtwpr_rule_display_framework['buy_group_filter_select']['option']); $i++) { 
                                             
                      //pick up the free/pro version of the title => in this case, title and title3
                      $title = $vtwpr_rule_display_framework['buy_group_filter_select']['option'][$i]['title'];
                      if ( ( defined('VTWPR_PRO_DIRNAME') ) &&
                           ( $vtwpr_rule_display_framework['buy_group_filter_select']['option'][$i]['title3'] > ' ' ) ) {
                        $title = $vtwpr_rule_display_framework['buy_group_filter_select']['option'][$i]['title3'];                        
                      }
                 ?>                             
                    <option id="<?php echo $vtwpr_rule_display_framework['buy_group_filter_select']['option'][$i]['id'] .'1'; ?>"  class="<?php echo $vtwpr_rule_display_framework['buy_group_filter_select']['option'][$i]['class'] .'1'; ?>"  value="<?php echo $vtwpr_rule_display_framework['buy_group_filter_select']['option'][$i]['value']; ?>"    ><?php echo $title; ?></option>
                 <?php } ?> 
               </select>                                        
              <select id="<?php echo $vtwpr_rule_display_framework['buy_group_filter_select']['select']['id'] .'2';?>" class="<?php echo$vtwpr_rule_display_framework['buy_group_filter_select']['select']['class'] .'2'; ?> hideMe" name="<?php echo $vtwpr_rule_display_framework['buy_group_filter_select']['select']['name'] .'2';?>" tabindex="<?php //echo $vtwpr_rule_display_framework['buy_group_filter_select']['select']['tabindex']; ?>" >          
                 <?php
                 for($i=0; $i < sizeof($vtwpr_rule_display_framework['buy_group_filter_select']['option']); $i++) { 
                                             
                      //pick up the free/pro version of the title 
                      $title = $vtwpr_rule_display_framework['buy_group_filter_select']['option'][$i]['title2'];
                      if ( ( defined('VTWPR_PRO_DIRNAME') ) &&
                           ( $vtwpr_rule_display_framework['buy_group_filter_select']['option'][$i]['title4'] > ' ' ) ) {
                        $title = $vtwpr_rule_display_framework['buy_group_filter_select']['option'][$i]['title4'];                        
                      }                
                 ?>                             
                    <option id="<?php echo $vtwpr_rule_display_framework['buy_group_filter_select']['option'][$i]['id'] .'2'; ?>"  class="<?php echo $vtwpr_rule_display_framework['buy_group_filter_select']['option'][$i]['class'] .'2'; ?>"  value="<?php echo $vtwpr_rule_display_framework['buy_group_filter_select']['option'][$i]['value']; ?>"    ><?php echo $title; ?></option>
                 <?php } ?> 
               </select>
              <select id="<?php echo $vtwpr_rule_display_framework['buy_group_filter_select']['select']['id'] .'-catalog';?>" class="<?php echo$vtwpr_rule_display_framework['buy_group_filter_select']['select']['class'] .'-catalog'; ?> hideMe" name="<?php echo $vtwpr_rule_display_framework['buy_group_filter_select']['select']['name'] .'-catalog';?>" tabindex="<?php //echo $vtwpr_rule_display_framework['buy_group_filter_select']['select']['tabindex']; ?>" >          
                 <?php
                 for($i=0; $i < sizeof($vtwpr_rule_display_framework['buy_group_filter_select']['option']); $i++) { 
                                             
                      //pick up the free/pro version of the title 
                      $title = $vtwpr_rule_display_framework['buy_group_filter_select']['option'][$i]['title-catalog'];
                
                 ?>                             
                    <option id="<?php echo $vtwpr_rule_display_framework['buy_group_filter_select']['option'][$i]['id'] .'-catalog'; ?>"  class="<?php echo $vtwpr_rule_display_framework['buy_group_filter_select']['option'][$i]['class'] .'-catalog'; ?>"  value="<?php echo $vtwpr_rule_display_framework['buy_group_filter_select']['option'][$i]['value']; ?>"    ><?php echo $title; ?></option>
                 <?php } ?> 
               </select>                  
      
             <?php //buy_amt_type ?>  
              <select id="<?php echo $vtwpr_deal_screen_framework['buy_amt_type']['select']['id'] .'1';?>" class="<?php echo$vtwpr_deal_screen_framework['buy_amt_type']['select']['class'] .'1'; ?> hideMe" name="<?php echo $vtwpr_deal_screen_framework['buy_amt_type']['select']['name'] .'1';?>" tabindex="<?php //echo $vtwpr_deal_screen_framework['buy_amt_type']['select']['tabindex']; ?>" >          
                 <?php
                 for($i=0; $i < sizeof($vtwpr_deal_screen_framework['buy_amt_type']['option']); $i++) { 
                 ?>                             
                    <option id="<?php echo $vtwpr_deal_screen_framework['buy_amt_type']['option'][$i]['id'] .'1'; ?>"  class="<?php echo $vtwpr_deal_screen_framework['buy_amt_type']['option'][$i]['class'] .'1'; ?>"  value="<?php echo $vtwpr_deal_screen_framework['buy_amt_type']['option'][$i]['value']; ?>"    ><?php echo $vtwpr_deal_screen_framework['buy_amt_type']['option'][$i]['title']; ?></option>
                 <?php } ?> 
               </select>                                        
              <select id="<?php echo $vtwpr_deal_screen_framework['buy_amt_type']['select']['id'] .'2';?>" class="<?php echo$vtwpr_deal_screen_framework['buy_amt_type']['select']['class'] .'2'; ?> hideMe" name="<?php echo $vtwpr_deal_screen_framework['buy_amt_type']['select']['name'] .'2';?>" tabindex="<?php //echo $vtwpr_deal_screen_framework['buy_amt_type']['select']['tabindex']; ?>" >          
                 <?php
                 for($i=0; $i < sizeof($vtwpr_deal_screen_framework['buy_amt_type']['option']); $i++) { 
                 ?>                             
                    <option id="<?php echo $vtwpr_deal_screen_framework['buy_amt_type']['option'][$i]['id'] .'2'; ?>"  class="<?php echo $vtwpr_deal_screen_framework['buy_amt_type']['option'][$i]['class'] .'2'; ?>"  value="<?php echo $vtwpr_deal_screen_framework['buy_amt_type']['option'][$i]['value']; ?>"    ><?php echo $vtwpr_deal_screen_framework['buy_amt_type']['option'][$i]['title2']; ?></option>
                 <?php } ?> 
               </select>
              <select id="<?php echo $vtwpr_deal_screen_framework['buy_amt_type']['select']['id'] .'-catalog';?>" class="<?php echo$vtwpr_deal_screen_framework['buy_amt_type']['select']['class'] .'-catalog'; ?> hideMe" name="<?php echo $vtwpr_deal_screen_framework['buy_amt_type']['select']['name'] .'-catalog';?>" tabindex="<?php //echo $vtwpr_deal_screen_framework['buy_amt_type']['select']['tabindex']; ?>" >          
                 <?php
                 for($i=0; $i < sizeof($vtwpr_deal_screen_framework['buy_amt_type']['option']); $i++) { 
                                             
                      //pick up the free/pro version of the title 
                      $title = $vtwpr_deal_screen_framework['buy_amt_type']['option'][$i]['title-catalog'];
                
                 ?>                             
                    <option id="<?php echo $vtwpr_deal_screen_framework['buy_amt_type']['option'][$i]['id'] .'-catalog'; ?>"  class="<?php echo $vtwpr_deal_screen_framework['buy_amt_type']['option'][$i]['class'] .'-catalog'; ?>"  value="<?php echo $vtwpr_deal_screen_framework['buy_amt_type']['option'][$i]['value']; ?>"    ><?php echo $title; ?></option>
                 <?php } ?> 
               </select>                   
               
             <?php //buy_amt_applies_to ?>  
              <select id="<?php echo $vtwpr_deal_screen_framework['buy_amt_applies_to']['select']['id'] .'1';?>" class="<?php echo$vtwpr_deal_screen_framework['buy_amt_applies_to']['select']['class'] .'1'; ?> hideMe" name="<?php echo $vtwpr_deal_screen_framework['buy_amt_applies_to']['select']['name'] .'1';?>" tabindex="<?php //echo $vtwpr_deal_screen_framework['buy_amt_applies_to']['select']['tabindex']; ?>" >          
                 <?php
                 for($i=0; $i < sizeof($vtwpr_deal_screen_framework['buy_amt_applies_to']['option']); $i++) { 
                 ?>                             
                    <option id="<?php echo $vtwpr_deal_screen_framework['buy_amt_applies_to']['option'][$i]['id'] .'1'; ?>"  class="<?php echo $vtwpr_deal_screen_framework['buy_amt_applies_to']['option'][$i]['class'] .'1'; ?>"  value="<?php echo $vtwpr_deal_screen_framework['buy_amt_applies_to']['option'][$i]['value']; ?>"    ><?php echo $vtwpr_deal_screen_framework['buy_amt_applies_to']['option'][$i]['title']; ?></option>
                 <?php } ?> 
               </select>                                        
              <select id="<?php echo $vtwpr_deal_screen_framework['buy_amt_applies_to']['select']['id'] .'2';?>" class="<?php echo$vtwpr_deal_screen_framework['buy_amt_applies_to']['select']['class'] .'2'; ?> hideMe" name="<?php echo $vtwpr_deal_screen_framework['buy_amt_applies_to']['select']['name'] .'2';?>" tabindex="<?php //echo $vtwpr_deal_screen_framework['buy_amt_applies_to']['select']['tabindex']; ?>" >          
                 <?php
                 for($i=0; $i < sizeof($vtwpr_deal_screen_framework['buy_amt_applies_to']['option']); $i++) { 
                 ?>                             
                    <option id="<?php echo $vtwpr_deal_screen_framework['buy_amt_applies_to']['option'][$i]['id'] .'2'; ?>"  class="<?php echo $vtwpr_deal_screen_framework['buy_amt_applies_to']['option'][$i]['class'] .'2'; ?>"  value="<?php echo $vtwpr_deal_screen_framework['buy_amt_applies_to']['option'][$i]['value']; ?>"    ><?php echo $vtwpr_deal_screen_framework['buy_amt_applies_to']['option'][$i]['title2']; ?></option>
                 <?php } ?> 
               </select>  
               
             <?php //buy_amt_mod ?>  
              <select id="<?php echo $vtwpr_deal_screen_framework['buy_amt_mod']['select']['id'] .'1';?>" class="<?php echo$vtwpr_deal_screen_framework['buy_amt_mod']['select']['class'] .'1'; ?> hideMe" name="<?php echo $vtwpr_deal_screen_framework['buy_amt_mod']['select']['name'] .'1';?>" tabindex="<?php //echo $vtwpr_deal_screen_framework['buy_amt_mod']['select']['tabindex']; ?>" >          
                 <?php
                 for($i=0; $i < sizeof($vtwpr_deal_screen_framework['buy_amt_mod']['option']); $i++) { 
                 ?>                             
                    <option id="<?php echo $vtwpr_deal_screen_framework['buy_amt_mod']['option'][$i]['id'] .'1'; ?>"  class="<?php echo $vtwpr_deal_screen_framework['buy_amt_mod']['option'][$i]['class'] .'1'; ?>"  value="<?php echo $vtwpr_deal_screen_framework['buy_amt_mod']['option'][$i]['value']; ?>"    ><?php echo $vtwpr_deal_screen_framework['buy_amt_mod']['option'][$i]['title']; ?></option>
                 <?php } ?> 
               </select>                                        
              <select id="<?php echo $vtwpr_deal_screen_framework['buy_amt_mod']['select']['id'] .'2';?>" class="<?php echo$vtwpr_deal_screen_framework['buy_amt_mod']['select']['class'] .'2'; ?> hideMe" name="<?php echo $vtwpr_deal_screen_framework['buy_amt_mod']['select']['name'] .'2';?>" tabindex="<?php //echo $vtwpr_deal_screen_framework['buy_amt_mod']['select']['tabindex']; ?>" >          
                 <?php
                 for($i=0; $i < sizeof($vtwpr_deal_screen_framework['buy_amt_mod']['option']); $i++) { 
                 ?>                             
                    <option id="<?php echo $vtwpr_deal_screen_framework['buy_amt_mod']['option'][$i]['id'] .'2'; ?>"  class="<?php echo $vtwpr_deal_screen_framework['buy_amt_mod']['option'][$i]['class'] .'2'; ?>"  value="<?php echo $vtwpr_deal_screen_framework['buy_amt_mod']['option'][$i]['value']; ?>"    ><?php echo $vtwpr_deal_screen_framework['buy_amt_mod']['option'][$i]['title2']; ?></option>
                 <?php } ?> 
               </select>  
             
            <?php //buy_repeat_condition ?>  
              <select id="<?php echo $vtwpr_deal_screen_framework['buy_repeat_condition']['select']['id'] .'1';?>" class="<?php echo$vtwpr_deal_screen_framework['buy_repeat_condition']['select']['class'] .'1'; ?> hideMe" name="<?php echo $vtwpr_deal_screen_framework['buy_repeat_condition']['select']['name'] .'1';?>" tabindex="<?php //echo $vtwpr_deal_screen_framework['buy_repeat_condition']['select']['tabindex']; ?>" >          
                 <?php
                 for($i=0; $i < sizeof($vtwpr_deal_screen_framework['buy_repeat_condition']['option']); $i++) { 
                 ?>                             
                    <option id="<?php echo $vtwpr_deal_screen_framework['buy_repeat_condition']['option'][$i]['id'] .'1'; ?>"  class="<?php echo $vtwpr_deal_screen_framework['buy_repeat_condition']['option'][$i]['class'] .'1'; ?>"  value="<?php echo $vtwpr_deal_screen_framework['buy_repeat_condition']['option'][$i]['value']; ?>"    ><?php echo $vtwpr_deal_screen_framework['buy_repeat_condition']['option'][$i]['title']; ?></option>
                 <?php } ?> 
               </select>                                        
              <select id="<?php echo $vtwpr_deal_screen_framework['buy_repeat_condition']['select']['id'] .'2';?>" class="<?php echo$vtwpr_deal_screen_framework['buy_repeat_condition']['select']['class'] .'2'; ?> hideMe" name="<?php echo $vtwpr_deal_screen_framework['buy_repeat_condition']['select']['name'] .'2';?>" tabindex="<?php //echo $vtwpr_deal_screen_framework['buy_repeat_condition']['select']['tabindex']; ?>" >          
                 <?php
                 for($i=0; $i < sizeof($vtwpr_deal_screen_framework['buy_repeat_condition']['option']); $i++) { 
                 ?>                             
                    <option id="<?php echo $vtwpr_deal_screen_framework['buy_repeat_condition']['option'][$i]['id'] .'2'; ?>"  class="<?php echo $vtwpr_deal_screen_framework['buy_repeat_condition']['option'][$i]['class'] .'2'; ?>"  value="<?php echo $vtwpr_deal_screen_framework['buy_repeat_condition']['option'][$i]['value']; ?>"    ><?php echo $vtwpr_deal_screen_framework['buy_repeat_condition']['option'][$i]['title2']; ?></option>
                 <?php } ?> 
               </select>  
              <select id="<?php echo $vtwpr_deal_screen_framework['buy_repeat_condition']['select']['id'] .'-catalog';?>" class="<?php echo$vtwpr_deal_screen_framework['buy_repeat_condition']['select']['class'] .'-catalog'; ?> hideMe" name="<?php echo $vtwpr_deal_screen_framework['buy_repeat_condition']['select']['name'] .'-catalog';?>" tabindex="<?php //echo $vtwpr_deal_screen_framework['buy_repeat_condition']['select']['tabindex']; ?>" >          
                 <?php
                 for($i=0; $i < sizeof($vtwpr_deal_screen_framework['buy_repeat_condition']['option']); $i++) { 
                                             
                      //pick up the free/pro version of the title 
                      $title = $vtwpr_deal_screen_framework['buy_repeat_condition']['option'][$i]['title-catalog'];
                
                 ?>                             
                    <option id="<?php echo $vtwpr_deal_screen_framework['buy_repeat_condition']['option'][$i]['id'] .'-catalog'; ?>"  class="<?php echo $vtwpr_deal_screen_framework['buy_repeat_condition']['option'][$i]['class'] .'-catalog'; ?>"  value="<?php echo $vtwpr_deal_screen_framework['buy_repeat_condition']['option'][$i]['value']; ?>"    ><?php echo $title; ?></option>
                 <?php } ?> 
               </select>
      
             <?php //action_amt_type ?>  
              <select id="<?php echo $vtwpr_deal_screen_framework['action_amt_type']['select']['id'] .'1';?>" class="<?php echo$vtwpr_deal_screen_framework['action_amt_type']['select']['class'] .'1'; ?> hideMe" name="<?php echo $vtwpr_deal_screen_framework['action_amt_type']['select']['name'] .'1';?>" tabindex="<?php //echo $vtwpr_deal_screen_framework['action_amt_type']['select']['tabindex']; ?>" >          
                 <?php
                 for($i=0; $i < sizeof($vtwpr_deal_screen_framework['action_amt_type']['option']); $i++) { 
                 ?>                             
                    <option id="<?php echo $vtwpr_deal_screen_framework['action_amt_type']['option'][$i]['id'] .'1'; ?>"  class="<?php echo $vtwpr_deal_screen_framework['action_amt_type']['option'][$i]['class'] .'1'; ?>"  value="<?php echo $vtwpr_deal_screen_framework['action_amt_type']['option'][$i]['value']; ?>"    ><?php echo $vtwpr_deal_screen_framework['action_amt_type']['option'][$i]['title']; ?></option>
                 <?php } ?> 
               </select>                                        
              <select id="<?php echo $vtwpr_deal_screen_framework['action_amt_type']['select']['id'] .'2';?>" class="<?php echo$vtwpr_deal_screen_framework['action_amt_type']['select']['class'] .'2'; ?> hideMe" name="<?php echo $vtwpr_deal_screen_framework['action_amt_type']['select']['name'] .'2';?>" tabindex="<?php //echo $vtwpr_deal_screen_framework['action_amt_type']['select']['tabindex']; ?>" >          
                 <?php
                 for($i=0; $i < sizeof($vtwpr_deal_screen_framework['action_amt_type']['option']); $i++) { 
                 ?>                             
                    <option id="<?php echo $vtwpr_deal_screen_framework['action_amt_type']['option'][$i]['id'] .'2'; ?>"  class="<?php echo $vtwpr_deal_screen_framework['action_amt_type']['option'][$i]['class'] .'2'; ?>"  value="<?php echo $vtwpr_deal_screen_framework['action_amt_type']['option'][$i]['value']; ?>"    ><?php echo $vtwpr_deal_screen_framework['action_amt_type']['option'][$i]['title2']; ?></option>
                 <?php } ?> 
               </select> 
               
            <?php //inPop ?>  
              <select id="<?php echo $vtwpr_rule_display_framework['inPop']['select']['id'] .'1';?>" class="<?php echo$vtwpr_rule_display_framework['inPop']['select']['class'] .'1'; ?> hideMe" name="<?php echo $vtwpr_rule_display_framework['inPop']['select']['name'] .'1';?>" tabindex="<?php //echo $vtwpr_rule_display_framework['inPop']['select']['tabindex']; ?>" >          
                 <?php
                 for($i=0; $i < sizeof($vtwpr_rule_display_framework['inPop']['option']); $i++) { 
                                             
                      //pick up the free/pro version of the title 
                      $title = $vtwpr_rule_display_framework['inPop']['option'][$i]['title'];
                      if ( ( defined('VTWPR_PRO_DIRNAME') ) &&
                           ( $vtwpr_rule_display_framework['inPop']['option'][$i]['title3'] > ' ' ) ) {
                        $title = $vtwpr_rule_display_framework['inPop']['option'][$i]['title3'];                        
                      }                  
                 ?>                             
                    <option id="<?php echo $vtwpr_rule_display_framework['inPop']['option'][$i]['id'] .'1'; ?>"  class="<?php echo $vtwpr_rule_display_framework['inPop']['option'][$i]['class'] .'1'; ?>"  value="<?php echo $vtwpr_rule_display_framework['inPop']['option'][$i]['value']; ?>"    ><?php echo $title; ?></option>
                 <?php } ?> 
               </select>                                        
              <select id="<?php echo $vtwpr_rule_display_framework['inPop']['select']['id'] .'2';?>" class="<?php echo$vtwpr_rule_display_framework['inPop']['select']['class'] .'2'; ?> hideMe" name="<?php echo $vtwpr_rule_display_framework['inPop']['select']['name'] .'2';?>" tabindex="<?php //echo $vtwpr_rule_display_framework['inPop']['select']['tabindex']; ?>" >          
                 <?php
                 for($i=0; $i < sizeof($vtwpr_rule_display_framework['inPop']['option']); $i++) { 
                                             
                      //pick up the free/pro version of the title 
                      $title = $vtwpr_rule_display_framework['inPop']['option'][$i]['title2'];
                      if ( ( defined('VTWPR_PRO_DIRNAME') ) &&
                           ( $vtwpr_rule_display_framework['inPop']['option'][$i]['title4'] > ' ' ) ) {
                        $title = $vtwpr_rule_display_framework['inPop']['option'][$i]['title4'];                        
                      }                    
                 ?>                             
                    <option id="<?php echo $vtwpr_rule_display_framework['inPop']['option'][$i]['id'] .'2'; ?>"  class="<?php echo $vtwpr_rule_display_framework['inPop']['option'][$i]['class'] .'2'; ?>"  value="<?php echo $vtwpr_rule_display_framework['inPop']['option'][$i]['value']; ?>"    ><?php echo $title; ?></option>
                 <?php } ?> 
               </select>  
                 
             <?php //specChoice_in ?>  
              <select id="<?php echo $vtwpr_rule_display_framework['specChoice_in']['select']['id'] .'1';?>" class="<?php echo$vtwpr_rule_display_framework['specChoice_in']['select']['class'] .'1'; ?> hideMe" name="<?php echo $vtwpr_rule_display_framework['specChoice_in']['select']['name'] .'1';?>" tabindex="<?php //echo $vtwpr_rule_display_framework['specChoice_in']['select']['tabindex']; ?>" >          
                 <?php
                 for($i=0; $i < sizeof($vtwpr_rule_display_framework['specChoice_in']['option']); $i++) { 
                 ?>                             
                    <option id="<?php echo $vtwpr_rule_display_framework['specChoice_in']['option'][$i]['id'] .'1'; ?>"  class="<?php echo $vtwpr_rule_display_framework['specChoice_in']['option'][$i]['class'] .'1'; ?>"  value="<?php echo $vtwpr_rule_display_framework['specChoice_in']['option'][$i]['value']; ?>"    ><?php echo $vtwpr_rule_display_framework['specChoice_in']['option'][$i]['title']; ?></option>
                 <?php } ?> 
               </select>                                        
              <select id="<?php echo $vtwpr_rule_display_framework['specChoice_in']['select']['id'] .'2';?>" class="<?php echo$vtwpr_rule_display_framework['specChoice_in']['select']['class'] .'2'; ?> hideMe" name="<?php echo $vtwpr_rule_display_framework['specChoice_in']['select']['name'] .'2';?>" tabindex="<?php //echo $vtwpr_rule_display_framework['specChoice_in']['select']['tabindex']; ?>" >          
                 <?php                                               
                 for($i=0; $i < sizeof($vtwpr_rule_display_framework['specChoice_in']['option']); $i++) { 
                 ?>                             
                    <option id="<?php echo $vtwpr_rule_display_framework['specChoice_in']['option'][$i]['id'] .'2'; ?>"  class="<?php echo $vtwpr_rule_display_framework['specChoice_in']['option'][$i]['class'] .'2'; ?>"  value="<?php echo $vtwpr_rule_display_framework['specChoice_in']['option'][$i]['value']; ?>"    ><?php echo $vtwpr_rule_display_framework['specChoice_in']['option'][$i]['title2']; ?></option>
                 <?php } ?> 
               </select>  
                          
   <?php         
  }          
      
} //end class
