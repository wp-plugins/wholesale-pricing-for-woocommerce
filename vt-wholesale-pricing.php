<?php
/*
Plugin Name: VarkTech Wholesale Pricing for WooCommerce
Plugin URI: http://varktech.com
Description: An e-commerce add-on for WooCommerce, supplying Wholesale Pricing functionality.
Version: 1.0.5
Author: Vark
Author URI: http://varktech.com
*/

/*  ******************* *******************
=====================
ASK YOUR HOST TO TURN OFF magic_quotes_gpc !!!!!
=====================
******************* ******************* */


/*
** define Globals 
*/
   $vtwpr_info;  //initialized in VTWPR_Parent_Definitions
   $vtwpr_rules_set;
   $vtwpr_rule;
   $vtwpr_cart;
   $vtwpr_cart_item;
   $vtwpr_setup_options;
   
   $vtwpr_rule_display_framework;
   $vtwpr_rule_type_framework; 
   $vtwpr_deal_structure_framework;
   $vtwpr_deal_screen_framework;
   $vtwpr_deal_edits_framework;
   $vtwpr_template_structures_framework;
   
   //initial setup only, overriden later in function vtwpr_debug_options
   error_reporting(E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR); //1.0.3
    
     
class VTWPR_Controller{
	
	public function __construct(){    
 
    if(!isset($_SESSION)){
      session_start();
      header("Cache-Control: no-cache");
      header("Pragma: no-cache");
    } 
    
		define('VTWPR_VERSION',                               '1.0.5');
    define('VTWPR_MINIMUM_PRO_VERSION',                   '1.0.3');
    define('VTWPR_LAST_UPDATE_DATE',                      '2014-12-06');
    define('VTWPR_DIRNAME',                               ( dirname( __FILE__ ) ));
    define('VTWPR_URL',                                   plugins_url( '', __FILE__ ) );
    define('VTWPR_EARLIEST_ALLOWED_WP_VERSION',           '3.3');   //To pick up wp_get_object_terms fix, which is required for vtwpr-parent-functions.php
    define('VTWPR_EARLIEST_ALLOWED_PHP_VERSION',          '5');
    define('VTWPR_PLUGIN_SLUG',                           plugin_basename(__FILE__));
    define('VTWPR_PRO_PLUGIN_NAME',                      'Varktech Pricing Deals Pro for WooCommerce');    //v1.0.3
       
    require_once ( VTWPR_DIRNAME . '/woo-integration/vtwpr-parent-definitions.php');
            
    // overhead stuff
    add_action('init', array( &$this, 'vtwpr_controller_init' ));
        
    /*  =============+++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
    //  these control the rules ui, add/save/trash/modify/delete
    /*  =============+++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
    
    /*  =============+++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
    //  One of these will pick up the NEW post, both the Rule custom post, and the PRODUCT
    //    picks up ONLY the 1st publish, save_post works thereafter...   
    //      (could possibly conflate all the publish/save actions (4) into the publish_post action...)
    /*  =============+++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */    
    if (is_admin()) {   //v1.03   only add during is_admin    
        add_action( 'draft_to_publish',       array( &$this, 'vtwpr_admin_update_rule_cntl' )); 
        add_action( 'auto-draft_to_publish',  array( &$this, 'vtwpr_admin_update_rule_cntl' ));
        add_action( 'new_to_publish',         array( &$this, 'vtwpr_admin_update_rule_cntl' )); 			
        add_action( 'pending_to_publish',     array( &$this, 'vtwpr_admin_update_rule_cntl' ));
        
        //standard mod/del/trash/untrash
        add_action('save_post',     array( &$this, 'vtwpr_admin_update_rule_cntl' ));
        add_action('delete_post',   array( &$this, 'vtwpr_admin_delete_rule' ));    
        add_action('trash_post',    array( &$this, 'vtwpr_admin_trash_rule' ));
        add_action('untrash_post',  array( &$this, 'vtwpr_admin_untrash_rule' ));
        /*  =============+++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
        
        //get rid of bulk actions on the edit list screen, which aren't compatible with this plugin's actions...
        add_action('bulk_actions-edit-vtwpr-rule', array($this, 'vtwpr_custom_bulk_actions') ); 
    } //v1.03  end
	}   //end constructor

  	                                                             
 /* ************************************************
 **   Overhead and Init
 *************************************************** */
	public function vtwpr_controller_init(){
    global $vtwpr_setup_options;

    //$product->get_rating_count() odd error at checkout... woocommerce/templates/single-product-reviews.php on line 20  
    //  (Fatal error: Call to a member function get_rating_count() on a non-object)
    global $product;

    //Split off for AJAX add-to-cart, etc for Class resources.  Loads for is_Admin and true INIT loads are kept here.
    //require_once ( VTWPR_DIRNAME . '/core/vtwpr-load-execution-resources.php' );
    
    load_plugin_textdomain( 'vtwpr', null, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 

    require_once  ( VTWPR_DIRNAME . '/core/vtwpr-backbone.php' );    
    require_once  ( VTWPR_DIRNAME . '/core/vtwpr-rules-classes.php');
    require_once  ( VTWPR_DIRNAME . '/admin/vtwpr-rules-ui-framework.php' );
    require_once  ( VTWPR_DIRNAME . '/woo-integration/vtwpr-parent-functions.php');
    require_once  ( VTWPR_DIRNAME . '/woo-integration/vtwpr-parent-theme-functions.php');
    require_once  ( VTWPR_DIRNAME . '/woo-integration/vtwpr-parent-cart-validation.php');   
    require_once  ( VTWPR_DIRNAME . '/woo-integration/vtwpr-parent-definitions.php');
    require_once  ( VTWPR_DIRNAME . '/core/vtwpr-cart-classes.php');
    
    //************
    //changed for AJAX add-to-cart, removed the is_admin around these resources => didn't work, for whatever reason...
    if(defined('VTWPR_PRO_DIRNAME')) {
      require_once  ( VTWPR_PRO_DIRNAME . '/core/vtwpr-apply-rules.php' );
  //    require_once  ( VTWPR_PRO_DIRNAME . '/woo-integration/vtwpr-lifetime-functions.php' );          
    } else {
      require_once  ( VTWPR_DIRNAME .     '/core/vtwpr-apply-rules.php' );
    }

    $vtwpr_setup_options = get_option( 'vtwpr_setup_options' );  //put the setup_options into the global namespace 
    
    vtwpr_debug_options();  //v1.0.5
      
            
    /*  **********************************
        Set GMT time zone for Store 
    Since Web Host can be on a different
    continent, with a different *Day* and Time,
    than the actual store.  Needed for Begin/end date processing
    **********************************  */
    vtwpr_set_selected_timezone();
        
    if (is_admin()){ 
        add_filter( 'plugin_action_links_' . VTWPR_PLUGIN_SLUG , array( $this, 'vtwpr_custom_action_links' ) );
        require_once ( VTWPR_DIRNAME . '/admin/vtwpr-setup-options.php');
        require_once ( VTWPR_DIRNAME . '/admin/vtwpr-rules-ui.php' );
           
        if(defined('VTWPR_PRO_DIRNAME')) {         
          require_once ( VTWPR_PRO_DIRNAME . '/admin/vtwpr-rules-update.php'); 
//          require_once ( VTWPR_PRO_DIRNAME . '/woo-integration/vtwpr-lifetime-functions.php' );           
        } else {
          require_once ( VTWPR_DIRNAME .     '/admin/vtwpr-rules-update.php');
        }
        
        require_once ( VTWPR_DIRNAME . '/admin/vtwpr-show-help-functions.php');
        require_once ( VTWPR_DIRNAME . '/admin/vtwpr-checkbox-classes.php');
        require_once ( VTWPR_DIRNAME . '/admin/vtwpr-rules-delete.php');
        
        $this->vtwpr_admin_init();
            
        //always check if the manually created coupon codes are there - if not create them.
       // vtwpr_woo_maybe_create_coupon_types();   
        
        //v1.0.3 begin
        if ( (defined('VTWPR_PRO_DIRNAME')) &&
             (version_compare(VTWPR_PRO_VERSION, VTWPR_MINIMUM_PRO_VERSION) < 0) ) {    //'<0' = 1st value is lower  
          add_action( 'admin_notices',array(&$this, 'vtwpr_admin_notice_version_mismatch') );            
        }
        //v1.0.3 begin 
          
    } else {

      //  add_action( "wp_enqueue_scripts", array(&$this, 'vtwpr_enqueue_frontend_scripts'), 1 );    //priority 1 to run 1st, so front-end-css can be overridden by another file with a dependancy

    }

    if (is_admin()){ 
      /*
      //LIFETIME logid cleanup...
      //  LogID logic from wpsc-admin/init.php
      if(defined('VTWPR_PRO_DIRNAME')) {
        switch( true ) {
          case ( isset( $_REQUEST['wpsc_admin_action2'] ) && ($_REQUEST['wpsc_admin_action2'] == 'purchlog_bulk_modify') )  :
                 vtwpr_maybe_lifetime_log_bulk_modify();
             break; 
          case ( isset( $_REQUEST['wpsc_admin_action'] ) && ($_REQUEST['wpsc_admin_action'] == 'delete_purchlog') ) :
                 vtwpr_maybe_lifetime_log_roll_out_cntl();
             break;                                             
        } 
          
        if (version_compare(VTWPR_PRO_VERSION, VTWPR_MINIMUM_PRO_VERSION) < 0) {    //'<0' = 1st value is lower  
          add_action( 'admin_notices',array(&$this, 'vtwpr_admin_notice_version_mismatch') );            
        }          
      }
      
      //****************************************
      //INSIST that coupons be enabled in woo, in order for this plugin to work!!
      //****************************************
      $coupons_enabled = get_option( 'woocommerce_enable_coupons' ) == 'no' ? false : true;
      if (!$coupons_enabled) {  
        add_action( 'admin_notices',array(&$this, 'vtwpr_admin_notice_coupon_enable_required') );            
      }  
      */   
         
    }

    return; 
  }
/*
  public function vtwpr_enqueue_frontend_scripts(){
    global $vtwpr_setup_options;
        
    wp_enqueue_script('jquery'); //needed universally
    
    if ( $vtwpr_setup_options['use_plugin_front_end_css'] == 'yes' ){
      wp_register_style( 'vtwpr-front-end-style', VTWPR_URL.'/core/css/vtwpr-front-end-min.css'  );   //every theme MUST have a style.css...  
      //wp_register_style( 'vtwpr-front-end-style', VTWPR_URL.'/core/css/vtwpr-front-end-min.css', array('style.css')  );   //every theme MUST have a style.css...      
      wp_enqueue_style('vtwpr-front-end-style');
    }
    
    return;
  
  }  
*/
         
  /* ************************************************
  **   Admin - Remove bulk actions on edit list screen, actions don't work the same way as onesies...
  ***************************************************/ 
  function vtwpr_custom_bulk_actions($actions){
    //v1.03  add  ".inline.hide-if-no-js, .view" to display:none; list
    ?> 
    <style type="text/css"> #delete_all, .inline.hide-if-no-js, .view  {display:none;} /*kill the 'empty trash' buttons, for the same reason*/ </style>
    <?php
    
    unset( $actions['edit'] );
    unset( $actions['trash'] );
    unset( $actions['untrash'] );
    unset( $actions['delete'] );
    return $actions;
  }
    
  /* ************************************************
  **   Admin - Show Rule UI Screen
  *************************************************** 
  *  This function is executed whenever the add/modify screen is presented
  *  WP also executes it ++right after the update function, prior to the screen being sent back to the user.   
  */  
	public function vtwpr_admin_init(){
     if ( !current_user_can( 'edit_posts', 'vtwpr-rule' ) )
          return;

     $vtwpr_rules_ui = new VTWPR_Rules_UI;      
  }

  /* ************************************************
  **   Admin - Publish/Update Rule or Parent Plugin CPT 
  *************************************************** */
	public function vtwpr_admin_update_rule_cntl(){
      global $post, $vtwpr_info;    
      switch( $post->post_type ) {
        case 'vtwpr-rule':
            $this->vtwpr_admin_update_rule();  
          break; 
        case $vtwpr_info['parent_plugin_cpt']: //this is the update from the PRODUCT screen, and updates the include/exclude lists
            $this->vtwpr_admin_update_product_meta_info();
          break;
      }  
      return;
  }
  
  
  /* ************************************************
  **   Admin - Publish/Update Rule 
  *************************************************** */
	public function vtwpr_admin_update_rule(){
    /* *****************************************************************
         The delete/trash/untrash actions *will sometimes fire save_post*
         and there is a case structure in the save_post function to handle this.
    
          the delete/trash actions are sometimes fired twice, 
               so this can be handled by checking 'did_action'
               
          'publish' action flows through to the bottom     
     ***************************************************************** */
      
      global $post, $vtwpr_rules_set;
      if ( !( 'vtwpr-rule' == $post->post_type )) {
        return;
      }  
      if (( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
            return; 
      }
     if (isset($_REQUEST['vtwpr_nonce']) ) {     //nonce created in vtwpr-rules-ui.php  
          $nonce = $_REQUEST['vtwpr_nonce'];
          if(!wp_verify_nonce($nonce, 'vtwpr-rule-nonce')) { 
            return;
          }
      } 
      if ( !current_user_can( 'edit_posts', 'vtwpr-rule' ) ) {
          return;
      }

      
      /* ******************************************
       The 'SAVE_POST' action is fired at odd times during updating.
       When it's fired early, there's no post data available.
       So checking for a blank post id is an effective solution.
      *************************************************** */      
      if ( !( $post->ID > ' ' ) ) { //a blank post id means no data to proces....
        return;
      } 
      //AND if we're here via an action other than a true save, do the action and exit stage left
      $action_type = $_REQUEST['action'];
      if ( in_array($action_type, array('trash', 'untrash', 'delete') ) ) {
        switch( $action_type ) {
            case 'trash':
                $this->vtwpr_admin_trash_rule();  
              break; 
            case 'untrash':
                $this->vtwpr_admin_untrash_rule();
              break;
            case 'delete':
                $this->vtwpr_admin_delete_rule();  
              break;
        }
        return;
      }
      // lets through  $action_type == editpost                
      $vtwpr_rule_update = new VTWPR_Rule_update;
  }
   
  
 /* ************************************************
 **   Admin - Delete Rule
 *************************************************** */
	public function vtwpr_admin_delete_rule(){
     global $post, $vtwpr_rules_set; 
     if ( !( 'vtwpr-rule' == $post->post_type ) ) {
      return;
     }        

     if ( !current_user_can( 'delete_posts', 'vtwpr-rule' ) )  {
          return;
     }
    
    $vtwpr_rule_delete = new VTWPR_Rule_delete;            
    $vtwpr_rule_delete->vtwpr_delete_rule();
        
    /* NO!! - the purchase history STAYS!
    if(defined('VTWPR_PRO_DIRNAME')) {
      vtwpr_delete_lifetime_rule_info();
    }   
     */
  }
  
  
  /* ************************************************
  **   Admin - Trash Rule
  *************************************************** */   
	public function vtwpr_admin_trash_rule(){
     global $post, $vtwpr_rules_set; 
     if ( !( 'vtwpr-rule' == $post->post_type ) ) {
      return;
     }        
  
     if ( !current_user_can( 'delete_posts', 'vtwpr-rule' ) )  {
          return;
     }  
     
     if(did_action('trash_post')) {    
         return;
    }
    
    $vtwpr_rule_delete = new VTWPR_Rule_delete;            
    $vtwpr_rule_delete->vtwpr_trash_rule();

  }
  
  
 /* ************************************************
 **   Admin - Untrash Rule
 *************************************************** */   
	public function vtwpr_admin_untrash_rule(){
     global $post, $vtwpr_rules_set; 
     if ( !( 'vtwpr-rule' == $post->post_type ) ) {
      return;
     }        

     if ( !current_user_can( 'delete_posts', 'vtwpr-rule' ) )  {
          return;
     }       
    $vtwpr_rule_delete = new VTWPR_Rule_delete;            
    $vtwpr_rule_delete->vtwpr_untrash_rule();
  }
  
  
  /* ************************************************
  **   Admin - Update PRODUCT Meta - include/exclude info
  *      from Meta box added to PRODUCT in rules-ui.php  
  *************************************************** */
	public function vtwpr_admin_update_product_meta_info(){
      global $post, $vtwpr_rules_set, $vtwpr_info;
      if ( !( $vtwpr_info['parent_plugin_cpt'] == $post->post_type )) {
        return;
      }  
      if (( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
            return; 
      }

      if ( !current_user_can( 'edit_posts', $vtwpr_info['parent_plugin_cpt'] ) ) {
          return;
      }
       //AND if we're here via an action other than a true save, exit stage left
      $action_type = $_REQUEST['action'];
      if ( in_array($action_type, array('trash', 'untrash', 'delete') ) ) {
        return;
      }
      
      /* ******************************************
       The 'SAVE_POST' action is fired at odd times during updating.
       When it's fired early, there's no post data available.
       So checking for a blank post id is an effective solution.
      *************************************************** */      
      if ( !( $post->ID > ' ' ) ) { //a blank post id means no data to proces....
        return;
      } 

      $includeOrExclude_option = $_REQUEST['includeOrExclude'];
      switch( $includeOrExclude_option ) {
        case 'includeAll':
        case 'excludeAll':   
            $includeOrExclude_checked_list = null; //initialize to null, as it's used later...
          break;
        case 'includeList':                  
        case 'excludeList':  
            $includeOrExclude_checked_list = $_REQUEST['includeOrExclude-checked_list']; //contains list of checked rule post-id"s                           
          break;
      }
      
      $vtwpr_includeOrExclude = array (
            'includeOrExclude_option'         => $includeOrExclude_option,
            'includeOrExclude_checked_list'   => $includeOrExclude_checked_list
             );
     
      //keep the add meta to retain the unique parameter...
      $vtwpr_includeOrExclude_meta  = get_post_meta($post->ID, $vtwpr_info['product_meta_key_includeOrExclude'], true);
      if ( $vtwpr_includeOrExclude_meta  ) {
        update_post_meta($post->ID, $vtwpr_info['product_meta_key_includeOrExclude'], $vtwpr_includeOrExclude);
      } else {
        add_post_meta($post->ID, $vtwpr_info['product_meta_key_includeOrExclude'], $vtwpr_includeOrExclude, true);
      }

  }
 

  /* ************************************************
  **   Admin - Activation Hook
  *************************************************** */  
	public function vtwpr_activation_hook() {
    global $wp_version, $vtwpr_setup_options;
    //the options are added at admin_init time by the setup_options.php as soon as plugin is activated!!!
        
    $this->vtwpr_create_discount_log_tables();
   

		$earliest_allowed_wp_version = 3.3;
    if( (version_compare(strval($earliest_allowed_wp_version), strval($wp_version), '>') == 1) ) {   //'==1' = 2nd value is lower  
        $message  =  '<strong>' . __('Looks like you\'re running an older version of WordPress, you need to be running at least WordPress 3.3 to use the Varktech Wholesale Pricing plugin.' , 'vtwpr') . '</strong>' ;
        $message .=  '<br>' . __('Current Wordpress Version = ' , 'vtwpr')  . $wp_version ;
        $admin_notices = '<div id="message" class="error fade" style="background-color: #FFEBE8 !important;"><p>' . $message . ' </p></div>';
        add_action( 'admin_notices', create_function( '', "echo '$admin_notices';" ) );
        return;
		}
   
            
   if (version_compare(PHP_VERSION, VTWPR_EARLIEST_ALLOWED_PHP_VERSION) < 0) {    //'<0' = 1st value is lower  
        $message  =  '<strong>' . __('Looks like you\'re running an older version of PHP.   - your PHP version = ' , 'vtwpr') .PHP_VERSION. '</strong>' ;
        $message .=  '<br>' . __('You need to be running **at least PHP version 5** to use this plugin. Please contact your host and request an upgrade to PHP 5+ .  Once that has been installed, you can activate this plugin.' , 'vtwpr');
        $admin_notices = '<div id="message" class="error fade" style="background-color: #FFEBE8 !important;"><p>' . $message . ' </p></div>';
        add_action( 'admin_notices', create_function( '', "echo '$admin_notices';" ) );
        return;      
      
		}

    
    if(defined('WOOCOMMERCE_VERSION') && (VTWPR_PARENT_PLUGIN_NAME == 'WooCommerce')) { 
      $new_version =      VTWPR_EARLIEST_ALLOWED_PARENT_VERSION;
      $current_version =  WOOCOMMERCE_VERSION;
      if( (version_compare(strval($new_version), strval($current_version), '>') == 1) ) {   //'==1' = 2nd value is lower 
        $message  =  '<strong>' . __('Looks like you\'re running an older version of WooCommerce. You need to be running at least ** WooCommerce 2.0 **, to use the Varktech Wholesale Pricing plugin' , 'vtwpr') . '</strong>' ;
        $message .=  '<br>' . __('Your current WooCommerce version = ' , 'vtwpr') .WOOCOMMERCE_VERSION;
        $admin_notices = '<div id="message" class="error fade" style="background-color: #FFEBE8 !important;"><p>' . $message . ' </p></div>';
        add_action( 'admin_notices', create_function( '', "echo '$admin_notices';" ) );
        return;         
  		}
    }   else 
    if (VTWPR_PARENT_PLUGIN_NAME == 'WooCommerce') {
        $message  =  '<strong>' . __('Varktech Wholesale Pricing for WooCommerce requires that WooCommerce be installed and activated. ' , 'vtwpr') . '</strong>' ;
        $message .=  '<br>' . __('It looks like WooCommerce is either not installed, or not activated. ' , 'vtwpr');
        $admin_notices = '<div id="message" class="error fade" style="background-color: #FFEBE8 !important;"><p>' . $message . ' </p></div>';
        add_action( 'admin_notices', create_function( '', "echo '$admin_notices';" ) );
        return;         
    }
     
  }


   public function vtwpr_admin_notice_coupon_enable_required() {
      $message  =  '<strong>' . __('In order for the Wholesale Pricing plugin to function successfully, the Woo Coupons Setting must be on, and it is currently off.' , 'vtwpr') . '</strong>' ;
      $message .=  '<br><br>' . __('Please go to the Woocommerce/Settings page.  Under the "General" tab, check the box next to "Enable the use of coupons" and click on the "Save Changes" button.'  , 'vtwpr');
      $admin_notices = '<div id="message" class="error fade" style="background-color: #FFEBE8 !important;"><p>' . $message . ' </p></div>';
      echo $admin_notices;
      return;    
  } 
            
   //v1.0.3 begin                          
   public function vtwpr_admin_notice_version_mismatch() {
      $message  =  '<strong>' . __('Please also update plugin: ' , 'vtwpr') . ' &nbsp;&nbsp;'  .VTWPR_PRO_PLUGIN_NAME . '</strong>' ;
      $message .=  '<br>&nbsp;&nbsp;&bull;&nbsp;&nbsp;' . __('Your Pro Version = ' , 'vtwpr') .VTWPR_PRO_VERSION. ' &nbsp;&nbsp;' . __(' The Minimum Required Pro Version = ' , 'vtwpr') .VTWPR_MINIMUM_PRO_VERSION ;      
      $message .=  '<br>&nbsp;&nbsp;&bull;&nbsp;&nbsp;' . __('Please delete the old Pro plugin from your installation via ftp.'  , 'vtwpr');
      $message .=  '<br>&nbsp;&nbsp;&bull;&nbsp;&nbsp;' . __('Go to ', 'vtwpr');
      $message .=  '<a target="_blank" href="http://www.varktech.com/download-pro-plugins/">Varktech Downloads</a>';
      $message .=   __(', download and install the newest <strong>'  , 'vtwpr') .VTWPR_PRO_PLUGIN_NAME. '</strong>' ;
      
      $admin_notices = '<div id="message" class="error fade" style="background-color: #FFEBE8 !important;"><p>' . $message . ' </p></div>';
      echo $admin_notices;
      return;    
  }   
   //v1.0.3 end 
    
  /* ************************************************
  **   Admin - **Uninstall** Hook and cleanup
  *************************************************** */ 
	public function vtwpr_uninstall_hook() {
      
      if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
      	return;
        //exit ();
      }
  
      delete_option('vtwpr_setup_options');
      $vtwpr_nuke = new VTWPR_Rule_delete;            
      $vtwpr_nuke->vtwpr_nuke_all_rules();
      $vtwpr_nuke->vtwpr_nuke_all_rule_cats();
      
  }
  
   
    //Add Custom Links to PLUGIN page action links                     ///wp-admin/edit.php?post_type=vtmam-rule&page=vtmam_setup_options_page
  public function vtwpr_custom_action_links( $links ) {                 
		$plugin_links = array(
			'<a href="' . admin_url( 'edit.php?post_type=vtwpr-rule&page=vtwpr_setup_options_page' ) . '">' . __( 'Settings', 'vtwpr' ) . '</a>',
			'<a href="http://www.varktech.com">' . __( 'Docs', 'vtwpr' ) . '</a>'
		);
		return array_merge( $plugin_links, $links );
	}



	public function vtwpr_create_discount_log_tables() {
    global $wpdb;
    //Cart Audit Trail Tables
  	
    $wpdb->hide_errors();    
  	$collate = '';
    if ( $wpdb->has_cap( 'collation' ) ) {  //mwn04142014
  		if( ! empty($wpdb->charset ) ) $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
  		if( ! empty($wpdb->collate ) ) $collate .= " COLLATE $wpdb->collate";
    }
     
      
  //  $is_this_purchLog = $wpdb->get_var("SHOW TABLES LIKE `".VTWPR_PURCHASE_LOG."` ");
    $table_name =  VTWPR_PURCHASE_LOG;
    $is_this_purchLog = $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" );
    if ( $is_this_purchLog  == VTWPR_PURCHASE_LOG) {
      return;
    }

     
    $sql = "
        CREATE TABLE  `".VTWPR_PURCHASE_LOG."` (
              id bigint NOT NULL AUTO_INCREMENT,
              cart_parent_purchase_log_id bigint,
              purchaser_name VARCHAR(50), 
              purchaser_ip_address VARCHAR(50),                
              purchase_date DATE NULL,
              cart_total_discount_currency DECIMAL(11,2),      
              ruleset_object TEXT,
              cart_object TEXT,
          KEY id (id, cart_parent_purchase_log_id)
        ) $collate ;      
        ";
 
     $this->vtwpr_create_table( $sql );
     
    $sql = "
        CREATE TABLE  `".VTWPR_PURCHASE_LOG_PRODUCT."` (
              id bigint NOT NULL AUTO_INCREMENT,
              purchase_log_row_id bigint,
              product_id bigint,
              product_title VARCHAR(100),
              cart_parent_purchase_log_id bigint,
              product_orig_unit_price   DECIMAL(11,2),     
              product_total_discount_units   DECIMAL(11,2),
              product_total_discount_currency DECIMAL(11,2),
              product_total_discount_percent DECIMAL(11,2),
          KEY id (id, purchase_log_row_id, product_id)
        ) $collate ;      
        ";
 
     $this->vtwpr_create_table( $sql );
     
    $sql = "
        CREATE TABLE  `".VTWPR_PURCHASE_LOG_PRODUCT_RULE."` (
              id bigint NOT NULL AUTO_INCREMENT,
              purchase_log_product_row_id bigint,
              product_id bigint,
			  rule_id bigint,
              cart_parent_purchase_log_id bigint,
              product_rule_discount_units   DECIMAL(11,2),
              product_rule_discount_dollars DECIMAL(11,2),
              product_rule_discount_percent DECIMAL(11,2),
          KEY id (id, purchase_log_product_row_id, rule_id)
        ) $collate ;      
        ";
 
     $this->vtwpr_create_table( $sql );



  }
  
	public function vtwpr_create_table( $sql ) {   
      global $wpdb;
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');	        
      dbDelta($sql);
      return; 
   } 
                            

  
} //end class
$vtwpr_controller = new VTWPR_Controller;
     
//has to be out here, accessing the plugin instance
if (is_admin()){
  register_activation_hook(__FILE__, array($vtwpr_controller, 'vtwpr_activation_hook'));
  register_uninstall_hook (__FILE__, array($vtwpr_controller, 'vtwpr_uninstall_hook'));
}

  
