<?php

/**
 *   based on code from the following:  (example is a tabbed settings page)
 *  http://wp.tutsplus.com/series/the-complete-guide-to-the-wordpress-settings-api/   
 *    (code at    https://github.com/tommcfarlin/WordPress-Settings-Sandbox) 
 *  http://www.chipbennett.net/2011/02/17/incorporating-the-settings-api-in-wordpress-themes/?all=1 
 *  http://www.presscoders.com/2010/05/wordpress-settings-api-explained/  
 */
class VTWPR_Setup_Plugin_Options { 
	
	public function __construct(){ 
  
    add_action( 'admin_init',            array(&$this, 'vtwpr_initialize_options' ) );
    add_action( 'admin_menu',            array(&$this, 'vtwpr_add_admin_menu_setup_items' ) );
    add_action( "admin_enqueue_scripts", array(&$this, 'vtwpr_enqueue_setup_scripts') );
  } 

function vtwpr_add_admin_menu_setup_items() {
 // add items to the Wholesale Pricing custom post type menu structure
  global $vtwpr_setup_options;
  $vtwpr_setup_options = get_option( 'vtwpr_setup_options' );
  if ( (isset( $vtwpr_setup_options['register_under_tools_menu'] ))  && 
       ($vtwpr_setup_options['register_under_tools_menu'] == 'yes') ) {      
      $settingsLocation = 'options-general.php';
  } else {
      $settingsLocation = 'edit.php?post_type=vtwpr-rule';
  } 
  
   
	add_submenu_page(
		$settingsLocation,	// The ID of the top-level menu page to which this submenu item belongs
		__( 'Wholesale Pricing Settings', 'vtwpr' ), // The value used to populate the browser's title bar when the menu page is active                           
		__( 'Wholesale Pricing Settings', 'vtwpr' ),					// The label of this submenu item displayed in the menu
		'administrator',					// What roles are able to access this submenu item
		'vtwpr_setup_options_page',	// The slug used to represent this submenu item
		array( &$this, 'vtwpr_setup_options_cntl' ) 				// The callback function used to render the options for this submenu item
	);
  
 if(!defined('VTWPR_PRO_DIRNAME')) {  //update to pro version...
   add_submenu_page(
		'edit.php?post_type=vtwpr-rule',	// The ID of the top-level menu page to which this submenu item belongs
		__( 'Upgrade to Wholesale Pricing Pro', 'vtwpr' ), // The value used to populate the browser's title bar when the menu page is active                           
		__( 'Upgrade to Pro', 'vtwpr' ),					// The label of this submenu item displayed in the menu
		'administrator',					// What roles are able to access this submenu item
		'vtwpr_pro_upgrade',	// The slug used to represent this submenu item
		array( &$this, 'vtwpr_pro_upgrade_cntl' ) 				// The callback function used to render the options for this submenu item
	); 
 }
    add_submenu_page(
		'edit.php?post_type=vtwpr-rule',	// The ID of the top-level menu page to which this submenu item belongs
		__( 'Wholesale Pricing Help', 'vtwpr' ), // The value used to populate the browser's title bar when the menu page is active                           
		__( 'Wholesale Pricing Help', 'vtwpr' ),					// The label of this submenu item displayed in the menu
		'administrator',					// What roles are able to access this submenu item
		'vtwpr_show_help_page',	// The slug used to represent this submenu item
		array( &$this, 'vtwpr_show_help_page_cntl' ) 				// The callback function used to render the options for this submenu item
	);  
/* 
    add_submenu_page(
		'edit.php?post_type=vtwpr-rule',	// The ID of the top-level menu page to which this submenu item belongs
		__( 'Wholesale Pricing FAQ', 'vtwpr' ), // The value used to populate the browser's title bar when the menu page is active                           
		__( 'Wholesale Pricing FAQ', 'vtwpr' ),					// The label of this submenu item displayed in the menu
		'administrator',					// What roles are able to access this submenu item
		'vtwpr_show_faq_page',	// The slug used to represent this submenu item
		array( &$this, 'vtwpr_show_faq_page_cntl' ) 				// The callback function used to render the options for this submenu item
	);  
 */
  //Add a DUPLICATE custom tax URL to be in the main Wholesale Pricing menu as well as in the PRODUCT menu
  //post_type=product => PARENT plugin post_type
    add_submenu_page(
		'edit.php?post_type=vtwpr-rule',	// The ID of the top-level menu page to which this submenu item belongs
		__( 'Wholesale Pricing Categories', 'vtwpr' ), // The value used to populate the browser's title bar when the menu page is active                           
		__( 'Wholesale Pricing Categories', 'vtwpr' ),					// The label of this submenu item displayed in the menu
		'administrator',					// What roles are able to access this submenu item
		'edit-tags.php?taxonomy=vtwpr_rule_category&post_type=product',	// The slug used to represent this submenu item
    //                                          PARENT PLUGIN POST TYPE      
		''  				// NO CALLBACK FUNCTION REQUIRED
	);

  
} 

function vtwpr_pro_upgrade_cntl() {

    //PRO UPGRADE PAGE
 ?>
  <style type="text/css">
      #upgrade-title-area {
          float:left;
          background-image:url("/wp-content/plugins/wholesale-pricing-for-wp-e-commerce/admin/images/upgrade-bkgrnd-banner.png");
          background-repeat: no-repeat;
          background-size:cover;
          width: 75%;
          padding: 10px 0 10px 20px;
          border-radius: 5px 5px 5px 5px;
      }
       #upgrade-title-area a {float:left;}
       #wholesale-pricing-img {
           float:left;
           padding-right: 10px;
       }
      .wrap h2, .subtitle {
          color:white;
          text-shadow:none;
      }

      #upgrade-div {
                clear:left;
                float: left;
               /* width: 2.5%;     */
                border: 1px solid #CCCCCC;
                border-radius: 5px 5px 5px 5px;
                padding: 0 15px 15px 0;
                font-size:18px;
                background: linear-gradient(to top, #ECECEC, #F9F9F9) repeat scroll 0 0 #F1F1F1;
                margin: 15px 0 0 7.5%;
                width: 68%;
                line-height: 25px;
            }
      #upgrade-div h3, #upgrade-div h4 {margin-left:20px;}
      #upgrade-div ul {list-style-type: none;margin-left:50px;}
      #upgrade-div ul ul {list-style-type: circle;font-size:16px !important;}
      /*#upgrade-div ul li {font-size:16px !important;}*/
      #upgrade-div a {font-size:16px; margin-left:23%;font-weight: bold;} 
      #upgrade-blurb {
        float:left;
        margin:15px 0 0 100px;
        font-weight:bold;
        color:blue;
      }
      #upgrade-div ul#vtwpr-main-attributes ul {list-style-type: none;margin-left: 20px;}
      #upgrade-div ul#vtwpr-main-attributes ul li {margin-left:15px;line-height:16px;color:blue;}
      #upgrade-blurb a, #upgrade-div a {color:blue;}
      #upgrade-blurb a:hover, #upgrade-div a:hover {color:#21759B;}
      .vtwpr-highlight {color:blue;font-weight:bold;}
      
      .buy-button,
      .buy-button-area,
      .buy-button-area a,
      .buy-button-area a img,
      .buy-button-label {
        float:left;
      }
    .buy-button-area {
      margin-left:10px;
      margin-top: 3px;
    }
 .buy-button {
	margin-top:20px;
  -moz-box-shadow:inset 0px 1px 0px 0px #caefab;
	-webkit-box-shadow:inset 0px 1px 0px 0px #caefab;
	box-shadow:inset 0px 1px 0px 0px #caefab;
	background-color:#77d42a;
	-moz-border-radius:6px;
	-webkit-border-radius:6px;
	border-radius:6px;
	border:1px solid #268a16;
	display:inline-block;
	color:#FFF;
	font-family:arial;
	/*font-size:15px;*/
	font-weight:bold;
	padding:6px 15px; /*changed*/
	text-decoration:none;
	text-shadow:1px 1px 0px #aade7c;
}
.buy-button:hover {
	background-color:#5a8939;
}
.buy-button:active {
	position:relative;
	top:1px;
}  
.buy-button:hover {
	background-color:#5a8939;
  color:white;
} 
    
  </style>
   
	<div class="wrap">
		<div id="icon-themes" class="icon32"></div>
    
		<div id="upgrade-title-area">
      <a  href=" <?php echo VTWPR_PURCHASE_PRO_VERSION_BY_PARENT ; ?> "  title="Purchase Pro">
      <img id="wholesale-pricing-img" alt="help" height="40px" width="40px" src="<?php echo VTWPR_URL;?>/admin/images/sale-circle.png" />
      </a>
      <h2><?php esc_attr_e('Upgrade to Wholesale Pricing Pro', 'vtwpr'); ?></h2>
    </div>  
      <h2 id="upgrade-blurb" ><?php _e('Wholesale Pricing Pro', 'vtwpr') ?> </h2>
      <span class="buy-button-area">
        <a href="<?php echo VTWPR_PURCHASE_PRO_VERSION_BY_PARENT; ?>" class="buy-button">
            <span class="buy-button-label">Get Wholesale Pricing Pro</span>
        </a>
      </span>


    <div id="upgrade-div">       
      
      <ul id="vtwpr-main-attributes">
        <li> <span class="vtwpr-highlight"><?php _e('Group Power &nbsp;-&nbsp; Apply rules to <em>any group you can think of!</em>', 'vtwpr') ?></span>
          <ul> <strong><em><?php _e('Filter By', 'vtwpr') ?></em></strong>
            <li><?php _e(' -  Wholesale / Membership / Role (Logged-in Status)', 'vtwpr') ?></li>
            <li><?php _e(' -  Product Category', 'vtwpr') ?></li>
            <li><?php _e(' -  Wholesale Pricing Custom Category', 'vtwpr') ?></li>
            <li><?php _e(' -  Product', 'vtwpr') ?></li>
            <li><?php _e(' -  Variations', 'vtwpr') ?></li>
          </ul>             
        </li>
        <li><span class="vtwpr-highlight"><?php _e('Product-level Deal Exclusion', 'vtwpr') ?></span></li>
        <li><span class="vtwpr-highlight"><?php _e('Maximum Deal Limits, including "One Per Customer" limit', 'vtwpr') ?></span></li>
      </ul>
      
      <ul>  
        <li><?php _e('<em>Deal Types Now have Tremendous Additional Power, with full filtering capability</em>, including:', 'vtwpr') ?>
          <ul>
            <li><?php _e('BOGO (Buy One, Get One) [for All]', 'vtwpr') ?></li>
            <li><?php _e('Sale Pricing [for All]', 'vtwpr') ?></li>
            <li><?php _e('Group Pricing [for All]', 'vtwpr') ?></li>
            <li><?php _e('Special Promotions [for All]', 'vtwpr') ?></li>
          </ul>         
        </li>

        <li><?php _e('Using Wholesale Pricing Custom Categories makes Group pricing and many other Discount Types *much more powerful*', 'vtwpr') ?>
          <ul>
            <li><?php _e('Group together any products you elect seamlessly', 'vtwpr') ?></li>
            <li><?php _e('Special Price for this Group', 'vtwpr') ?></li>
            <li><?php _e('Grouping affects no other part of your store', 'vtwpr') ?></li>
            <li><?php _e('Wholesale Pricing Custom Categories *do not affect* Product Category store organization and presentation *in any way*', 'vtwpr') ?></li>
          </ul>
        </li>
      </ul>
      <span class="buy-button-area">                                 
        <a href="<?php echo VTWPR_PURCHASE_PRO_VERSION_BY_PARENT; ?>" class="buy-button">
            <span class="buy-button-label">Get Wholesale Pricing Pro</span>
        </a>
      </span>                 
    </div>
        
      
  </div>
 
 <?php
}

function vtwpr_show_help_page_cntl() {
 ?>
  <style type="text/css">
      .wholesale_pricing-button {    
            float: left;
            font-size: 26px;
            margin: 50px;  }
  </style>
     
  
  <div class="wrap">
		<div id="icon-themes" class="icon32"></div>        
		  <a class="wholesale_pricing-button" target="_blank" href="http://www.varktech.com/documentation/wholesale-pricing/introrule/"><?php _e('Open Help Tab!', 'vtwpr');?></a> 
 
 <?php
}


function vtwpr_show_faq_page_cntl() {
 ?>
  <style type="text/css">
      #selection-panel-5 {display:block;}  /*default the panel to open*/
      #wholesale_pricing-examples-more{display:none;}
      #wholesale_pricing-examples-less{display:block;}
      #wholesale_pricing-examples-more, #wholesale_pricing-examples-less {margin-right: 10%;}
  </style>
	<div class="wrap">
		<div id="icon-themes" class="icon32"></div>        
		  <h2><?php esc_attr_e('Wholesale Pricing Examples FAQ', 'vtwpr'); ?></h2>                    
      <a id="wholesale_pricing-examples-more" class="more-anchor" href="javascript:void(0);"><img class="wholesale_pricing-examples-helpPng" alt="help"  width="14" height="14" src="<?php echo VTWPR_URL;?>/admin/images/help.png" /><?php _e(' Wholesale Pricing Examples FAQ ', 'vtwpr'); ?><img class="plus-button" alt="help" height="14px" width="14px" src="<?php echo VTWPR_URL;?>/admin/images/plus-toggle2.png" /></a>            
      <a id="wholesale_pricing-examples-less" class="more-anchor less-anchor" href="javascript:void(0);"><img class="wholesale_pricing-examples-helpPng" alt="help" width="14" height="14" src="<?php echo VTWPR_URL;?>/admin/images/help.png" /><?php _e('   Less Examples ...', 'vtwpr'); ?><img class="minus-button" alt="help" height="14px" width="14px" src="<?php echo VTWPR_URL;?>/admin/images/minus-toggle2.png" /></a>              
        <?php vtwpr_show_help_selection_panel_5();  ?>
  </div>
 
 <?php
}
/**
 * Renders a simple page to display for the menu item added above.
 */
function vtwpr_setup_options_cntl() {
  //add help tab to this screen...
  //$vtwpr_backbone->vtwpr_add_help_tab ();
    $content = '<br><a  href="' . VTWPR_DOCUMENTATION_PATH . '"  title="Access Plugin Documentation">Access Plugin Documentation</a>';
    $screen = get_current_screen();
    $screen->add_help_tab( array( 
       'id' => 'vtwpr-help-options',            //unique id for the tab
       'title' => 'Wholesale Pricing Settings Help',      //unique visible title for the tab
       'content' => $content  //actual help text
      ) );

  if(!defined('VTWPR_PRO_DIRNAME')) {  
        // **********************************************
      // also disable and grey out options on free version
      // **********************************************
        ?>
        <style type="text/css">
             #use_lifetime_max_limits,
             #vtwpr-lifetime-limit-by-ip,
             #vtwpr-lifetime-limit-by-email,
             #vtwpr-lifetime-limit-by-billto-name,
             #vtwpr-lifetime-limit-by-billto-addr,
             #vtwpr-lifetime-limit-by-shipto-name,             
             #vtwpr-lifetime-limit-by-shipto-addr,
             #max_purch_checkout_forms_set,
             #show_error_before_checkout_products_selector,
             #show_error_before_checkout_address_selector,
             #lifetime_purchase_button_error_msg
             {color:#aaa;}  /*grey out unavailable choices*/
        </style>
        <script type="text/javascript">
            jQuery.noConflict();
            jQuery(document).ready(function($) {                                                        
              // To disable 
              $('#use_lifetime_max_limits').attr('disabled', 'disabled');
              $('#vtwpr-lifetime-limit-by-ip').attr('disabled', 'disabled');
              $('#vtwpr-lifetime-limit-by-email').attr('disabled', 'disabled');
              $('#vtwpr-lifetime-limit-by-billto-name').attr('disabled', 'disabled');             
              $('#vtwpr-lifetime-limit-by-billto-addr').attr('disabled', 'disabled');
              $('#vtwpr-lifetime-limit-by-shipto-name').attr('disabled', 'disabled');
              $('#vtwpr-lifetime-limit-by-shipto-addr').attr('disabled', 'disabled');
             /* Can't use the disable - it clears out the default value on these fields!!              
              $('#max_purch_checkout_forms_set').attr('disabled', 'disabled');
              $('#show_error_before_checkout_products_selector').attr('disabled', 'disabled');
              $('#show_error_before_checkout_address_selector').attr('disabled', 'disabled');
              $('#lifetime_purchase_button_error_msg').attr('disabled', 'disabled');   */     
            }); //end ready function 
        </script>
  <?php } ?>        
 
  
	<div class="wrap">
		<div id="icon-themes" class="icon32"></div>
    
		<h2>
      <?php 
        if(defined('VTWPR_PRO_DIRNAME')) { 
          esc_attr_e('Wholesale Pricing Pro Options', 'vtwpr'); 
        } else {
          esc_attr_e('Wholesale Pricing Settings', 'vtwpr'); 
        }    
      ?>    
    </h2>
    
		<?php settings_errors(); ?>
    
    <?php 
    /*if ( isset( $_GET['settings-updated'] ) ) {
         echo "<div class='updated'><p>Theme settings updated successfully.</p></div>";
    } */
    ?>
		
		<form method="post" action="options.php">
			<?php
          //WP functions to execute the registered settings!
					settings_fields( 'vtwpr_setup_options_group' );     //activates the field settings setup below
					do_settings_sections( 'vtwpr_setup_options_page' );   //activates the section settings setup below 
			?>	
			
      <span id="floating-buttons" class="show-buttons">
        <?php	submit_button(); ?>       			     
        <input name="vtwpr_setup_options[options-reset]"      type="submit" class="button-secondary"  value="<?php esc_attr_e('Reset to Defaults', 'vtwpr'); ?>" />
        <a class="button-secondary" target="_blank" href="http://www.varktech.com/documentation/wholesale-pricing/settings"><?php _e('Help!', 'vtwpr');?></a> 
      </span>
      
      <span id="vtwpr-system-buttons-anchor"></span>  
       <p id="system-buttons">
          <h3><?php esc_attr_e('System Repair and Delete Buttons', 'vtwpr'); ?></h3> 
          <h4 class="system-buttons-h4"><?php esc_attr_e('Repair reknits the Rules Custom Post Type with the Wholesale Pricing rules option array, if out of sync.', 'vtwpr'); ?></h4>        
          <input id="repair-button"       name="vtwpr_setup_options[rules-repair]"    type="submit" class="nuke_buttons button-fourth"     value="<?php esc_attr_e('Repair Rules Structures', 'vtwpr'); ?>" /> 
          <h4 class="system-buttons-h4"><?php esc_attr_e('Nuke Rules deletes all Wholesale Pricing Rules.', 'vtwpr'); ?></h4>
          <input id="nuke-rules-button"   name="vtwpr_setup_options[rules-nuke]"      type="submit" class="nuke_buttons button-third"      value="<?php esc_attr_e('Nuke all Rules', 'vtwpr'); ?>" />
          <h4 class="system-buttons-h4"><?php esc_attr_e('Nuke Rule Cats deletes all Wholesale Pricing Rule Categories', 'vtwpr'); ?></h4>
          <input id="nuke-cats-button"    name="vtwpr_setup_options[cats-nuke]"       type="submit" class="nuke_buttons button-fifth"      value="<?php esc_attr_e('Nuke all Rule Cats', 'vtwpr'); ?>" />
          <h4 class="system-buttons-h4"><?php esc_attr_e('Nuke Customer Max Purchase History Tables', 'vtwpr'); ?></h4>
          <input id="nuke-hist-button"    name="vtwpr_setup_options[hist-nuke]"       type="submit" class="nuke_buttons button-fifth"      value="<?php esc_attr_e('Nuke Lifetime Max Purchase History Tables', 'vtwpr'); ?>" />
          <h4 class="system-buttons-h4"><?php esc_attr_e('Nuke Audit Trail Log Tables', 'vtwpr'); ?></h4>
          <input id="nuke-log-button"    name="vtwpr_setup_options[log-nuke]"         type="submit" class="nuke_buttons button-seventh"    value="<?php esc_attr_e('Nuke Audit Trail Log Tables', 'vtwpr'); ?>" />                    
          <h4 class="system-buttons-h4"><?php esc_attr_e('Nuke Session Variables', 'vtwpr'); ?></h4>
          <input id="nuke-session-button"    name="vtwpr_setup_options[session-nuke]" type="submit" class="nuke_buttons button-sixth"      value="<?php esc_attr_e('Nuke Session Variables', 'vtwpr'); ?>" />
          <h4 class="system-buttons-h4"><?php esc_attr_e('Nuke Cart Contents', 'vtwpr'); ?></h4>
          <input id="nuke-cart-button"    name="vtwpr_setup_options[cart-nuke]"       type="submit" class="nuke_buttons button-second"     value="<?php esc_attr_e('Nuke Cart Contents', 'vtwpr'); ?>" />                    
        </p>      
		</form>
    
    
    <?php 
    global $vtwpr_setup_options, $wp_version;
    $vtwpr_setup_options = get_option( 'vtwpr_setup_options' );	 
    if ( $vtwpr_setup_options['debugging_mode_on'] == 'yes' ) {  
      $vtwpr_functions = new VTWPR_Functions;
      $your_system_info = $vtwpr_functions->vtwpr_getSystemMemInfo();
    }
    ?>
    
    <span id="vtwpr-plugin-info-anchor"></span>
    <h3 id="system-info-title">Plugin Info</h3>
    
    <h4 class="system-info-subtitle">System Info</h4>
    <span class="system-info">
       <span class="system-info-line"><span class="system-info-label">FREE_VERSION: </span> <span class="system-info-data"><?php echo VTWPR_VERSION;  ?></span> </span>
       <span class="system-info-line"><span class="system-info-label">FREE_LAST_UPDATE_DATE: </span> <span class="system-info-data"><?php echo VTWPR_LAST_UPDATE_DATE;  ?></span></span>
       <span class="system-info-line"><span class="system-info-label">FREE_DIRNAME: </span> <span class="system-info-data"><?php echo VTWPR_DIRNAME;  ?></span></span>
       <span class="system-info-line"><span class="system-info-label">URL: </span> <span class="system-info-data"><?php echo VTWPR_URL;  ?></span></span>
       <span class="system-info-line"><span class="system-info-label">EARLIEST_ALLOWED_WP_VERSION: </span> <span class="system-info-data"><?php echo VTWPR_EARLIEST_ALLOWED_WP_VERSION;  ?></span></span>
       <span class="system-info-line"><span class="system-info-label">WP VERSION: </span> <span class="system-info-data"><?php echo $wp_version; ?></span> </span>
       <span class="system-info-line"><span class="system-info-label">EARLIEST_ALLOWED_PHP_VERSION: </span> <span class="system-info-data"><?php echo VTWPR_EARLIEST_ALLOWED_PHP_VERSION ;?></span> </span>
       <span class="system-info-line"><span class="system-info-label">FREE_PLUGIN_SLUG: </span> <span class="system-info-data"><?php echo VTWPR_PLUGIN_SLUG;  ?></span></span>
     </span> 
    
    <h4 class="system-info-subtitle">Parent Plugin Info</h4>
    <span class="system-info">
       <span class="system-info-line"><span class="system-info-label">PARENT_PLUGIN_NAME: </span> <span class="system-info-data"><?php echo VTWPR_PARENT_PLUGIN_NAME;  ?></span> </span>
       <span class="system-info-line"><span class="system-info-label">EARLIEST_ALLOWED_PARENT_VERSION: </span> <span class="system-info-data"><?php echo VTWPR_EARLIEST_ALLOWED_PARENT_VERSION;  ?></span></span>

       <?php if(defined('WOOCOMMERCE_VERSION') && (VTWPR_PARENT_PLUGIN_NAME == 'WooCommerce')) { ?>
       <span class="system-info-line"><span class="system-info-label">PARENT_VERSION (WOOCOMMERCE): </span> <span class="system-info-data"><?php echo WOOCOMMERCE_VERSION;  ?></span></span>
       <?php } ?>
       
       <?php if(defined('JIGOSHOP_VERSION') && (VTWPR_PARENT_PLUGIN_NAME == 'JigoShop')) {  ?>
       <span class="system-info-line"><span class="system-info-label">PARENT_VERSION (JIGOSHOP): </span> <span class="system-info-data"><?php echo JIGOSHOP_VERSION;  ?></span></span>
       <?php } ?>
       
       <span class="system-info-line"><span class="system-info-label">TESTED_UP_TO_PARENT_VERSION: </span> <span class="system-info-data"><?php echo VTWPR_TESTED_UP_TO_PARENT_VERSION;  ?></span></span>
  
     </span> 

     <?php   if (defined('VTWPR_PRO_DIRNAME')) {  ?> 
      <h4 class="system-info-subtitle">Pro Info</h4>
      <span class="system-info">      
       <span class="system-info-line"><span class="system-info-label">PRO_PLUGIN_NAME: </span> <span class="system-info-data"><?php echo VTWPR_PRO_PLUGIN_NAME; ?></span> </span>
       <span class="system-info-line"><span class="system-info-label">PRO_FREE_PLUGIN_NAME: </span> <span class="system-info-data"><?php echo VTWPR_PRO_FREE_PLUGIN_NAME; ?></span> </span>
       <span class="system-info-line"><span class="system-info-label">PRO_VERSION: </span> <span class="system-info-data"><?php echo VTWPR_PRO_VERSION; ?></span> </span>
       <span class="system-info-line"><span class="system-info-label">PRO_LAST_UPDATE_DATE: </span> <span class="system-info-data"><?php echo VTWPR_PRO_LAST_UPDATE_DATE;  ?></span></span>
       <span class="system-info-line"><span class="system-info-label">PRO_DIRNAME: </span> <span class="system-info-data"><?php echo VTWPR_PRO_DIRNAME;  ?></span></span>
       <span class="system-info-line"><span class="system-info-label">PRO_MINIMUM_REQUIRED_FREE_VERSION: </span> <span class="system-info-data"><?php echo VTWPR_PRO_MINIMUM_REQUIRED_FREE_VERSION;  ?></span></span>
       <span class="system-info-line"><span class="system-info-label">PRO_BASE_NAME: </span> <span class="system-info-data"><?php echo VTWPR_PRO_BASE_NAME; ?></span> </span>
       <span class="system-info-line"><span class="system-info-label">PRO_PLUGIN_SLUG: </span> <span class="system-info-data"><?php echo VTWPR_PLUGIN_SLUG; ?></span> </span>
       <span class="system-info-line"><span class="system-info-label">PRO_REMOTE_VERSION_FILE: </span> <span class="system-info-data"><?php echo VTWPR_PRO_REMOTE_VERSION_FILE; ?></span> </span>
      </span> 
     <?php   }  ?>   

        
     <?php   if ( $vtwpr_setup_options['debugging_mode_on'] == 'yes' ){  ?> 
     <h4 class="system-info-subtitle">Debug Info</h4>
      <span class="system-info">                  
       <span class="system-info-line"><span class="system-info-label">PHP VERSION: </span> <span class="system-info-data"><?php echo phpversion(); ?></span> </span>
       <span class="system-info-line"><span class="system-info-label">SYSTEM MEMORY: </span> <span class="system-info-data"><?php echo '<pre>'.print_r( $your_system_info , true).'</pre>' ;  ?></span> </span>
     </span> 
     <?php   }  ?>
  
	</div><!-- /.wrap -->

<?php
} // end vtwpr_display  


/* ------------------------------------------------------------------------ *
 * Setting Registration
 * ------------------------------------------------------------------------ */ 

/**
 * Initializes the theme's Discount Reporting Options page by registering the Sections,
 * Fields, and Settings.
 *
 * This function is registered with the 'admin_init' hook.
 */ 

function vtwpr_initialize_options() {
  
	// If the theme options don't exist, create them.
	if( false == get_option( 'vtwpr_setup_options' ) ) {
		add_option( 'vtwpr_setup_options', $this->vtwpr_set_default_options() );  //add the option into the table based on the default values in the function.
	} // end if

 
	// First, we register a section. This is necessary since all future options must belong to a 
	add_settings_section(
		'nav_section',			// ID used to identify this section and with which to register options
		'',	// Title to be displayed on the administration page
		array(&$this, 'vtwpr_nav_callback'),	// Callback used to render the description of the section
		'vtwpr_setup_options_page'		// Page on which to add this section of options
	);
  //****************************
  //  Checkout Discount Reporting OptionS Area
  //****************************  

//COMMENT BEGIN 
/*
	add_settings_section(
		'checkout_settings_section',			// ID used to identify this section and with which to register options
		__( 'Checkout Discount Display<span id="vtwpr-checkout-reporting-anchor"></span>', 'vtwpr' ),	// Title to be displayed on the administration page
		array(&$this, 'vtwpr_checkout_options_callback'),	// Callback used to render the description of the section
		'vtwpr_setup_options_page'		// Page on which to add this section of options
	);
  
          
    add_settings_field(	         //opt6
		'show_checkout_discount_detail_lines',						// ID used to identify the field throughout the theme
		__( 'Show Product Discount Detail Lines?', 'vtwpr' ), // The label to the left of the option interface element
		array(&$this, 'vtwpr_show_checkout_discount_detail_lines_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'checkout_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Show checkout discount details line', 'vtwpr' )
		)
	);
    
    add_settings_field(	         //opt21
		'show_checkout_discount_details_grouped_by_what',						// ID used to identify the field throughout the theme
		'&nbsp;&nbsp;&nbsp;&nbsp;'        .__( 'Product Discounts Grouped By?', 'vtwpr' ),	// The label to the left of the option interface element
		array(&$this, 'vtwpr_show_checkout_discount_details_grouped_by_what_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'checkout_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Show discount details grouped by', 'vtwpr' )
		)
	);
      
    add_settings_field(	         //opt23
		'show_checkout_discount_titles_above_details',						// ID used to identify the field throughout the theme
		'&nbsp;&nbsp;&nbsp;&nbsp;'        . __( 'Show Short Checkout Message for', 'vtwpr' )
    . '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ' . __( '"Grouped by Rule within Product"?', 'vtwpr' ),
		array(&$this, 'vtwpr_show_checkout_discount_titles_above_details_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'checkout_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Show Titles above Checkout Discount detail lines?', 'vtwpr' )
		)
	);  
   
    add_settings_field(	         //opt24
		'show_checkout_purchases_subtotal',						// ID used to identify the field throughout the theme
    __( 'Show Cart Purchases Subtotal Line?', 'vtwpr' ),    
		array(&$this, 'vtwpr_show_checkout_purchases_subtotal_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'checkout_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Show Subtotal of cart purchases at checkout', 'vtwpr' )
		)
	);
    
    add_settings_field(	         //opt30
		'checkout_credit_subtotal_title',						// ID used to identify the field throughout the theme
    '&nbsp;&nbsp;&nbsp;&nbsp;'        .__( 'Cart Purchases Subtotal Line', 'vtwpr' )
    . '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'  . __( ' - Label Title', 'vtwpr' ),
    array(&$this, 'vtwpr_checkout_credit_subtotal_title_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'checkout_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Show discount totals title', 'vtwpr' )
		)
	);
 
    add_settings_field(	         //opt5
		'show_checkout_discount_total_line',						// ID used to identify the field throughout the theme
		__( 'Show Discounts Grand Totals Line?', 'vtwpr' ),	// The label to the left of the option interface element
		array(&$this, 'vtwpr_show_checkout_discount_total_line_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'checkout_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Show Checkout separate discount totals line?', 'vtwpr' )
		)
	);
  
    add_settings_field(	         //opt31
		'checkout_credit_total_title',						// ID used to identify the field throughout the theme
    '&nbsp;&nbsp;&nbsp;&nbsp;'        .__( 'Discounts Grand Totals Line', 'vtwpr' )
    . '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'  . __( ' - Label Title', 'vtwpr' ),		
    array(&$this, 'vtwpr_checkout_credit_total_title_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'checkout_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Show discount totals title', 'vtwpr' )
		)
	);  


    add_settings_field(	         //opt10
		'checkout_credit_detail_label',						// ID used to identify the field throughout the theme
		__( 'Discount Detail Line - Credit Label', 'vtwpr' ) ,	// The label to the left of the option interface element
		array(&$this, 'vtwpr_checkout_credit_detail_label_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'checkout_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Show discount totals line', 'vtwpr' )
		)
	);
  
    add_settings_field(	         //opt11
		'checkout_credit_total_label',						// ID used to identify the field throughout the theme
		__( 'Discount Total Line - Credit Label', 'vtwpr' ),	// The label to the left of the option interface element
		array(&$this, 'vtwpr_checkout_credit_total_label_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'checkout_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Show discount totals line', 'vtwpr' )
		)
	);

    add_settings_field(	         //opt43
		'checkout_new_subtotal_line',						// ID used to identify the field throughout the theme
		 __( 'Show Products + Discounts', 'vtwpr' )
    . '<br>'  . __( ' Grand Total Line', 'vtwpr' ),	// The label to the left of the option interface element
		array(&$this, 'vtwpr_checkout_new_subtotal_line_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'checkout_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'New checkout subtotal switch', 'vtwpr' )
		)
	);  
    
    
    add_settings_field(	         //opt44
		'checkout_new_subtotal_label',						// ID used to identify the field throughout the theme
    '&nbsp;&nbsp;&nbsp;&nbsp;'        .__( 'Product + Discount', 'vtwpr' )
    . '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'  . __( 'Grand Total Line - Label Title', 'vtwpr' ),		
    array(&$this, 'vtwpr_checkout_new_subtotal_label_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'checkout_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'New checkout subtotal label', 'vtwpr' )
		)
	);



    
  //****************************     
  //  Cart Widget Discount Reporting OptionS Area
  //****************************      
	add_settings_section(
		'cartWidget_settings_section',			// ID used to identify this section and with which to register options
		__( 'Cart Widget Discount Display<span id="vtwpr-cartWidget-options-anchor"></span>', 'vtwpr' ),	// Title to be displayed on the administration page
		array(&$this, 'vtwpr_cartWidget_options_callback'),	// Callback used to render the description of the section
		'vtwpr_setup_options_page'		// Page on which to add this section of options
	); 
     
    add_settings_field(	         //opt27
		'show_cartWidget_discount_detail_lines',						// ID used to identify the field throughout the theme
		__( 'Show Product Discount Detail Lines?', 'vtwpr' ),	// The label to the left of the option interface element
		array(&$this, 'vtwpr_show_cartWidget_discount_detail_lines_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'cartWidget_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Show cartWidget discount details line', 'vtwpr' )
		)
	);	

    add_settings_field(	         //opt22
		'show_cartWidget_discount_details_grouped_by_what',						// ID used to identify the field throughout the theme
		'&nbsp;&nbsp;&nbsp;&nbsp;'        .__( 'Product Discounts Grouped By?', 'vtwpr' ),	// The label to the left of the option interface element
		array(&$this, 'vtwpr_show_cartWidget_discount_details_grouped_by_what_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'cartWidget_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Show discount details grouped by', 'vtwpr' )
		)
	);
      
    add_settings_field(	         //opt7
		'show_cartWidget_discount_titles_above_details',						// ID used to identify the field throughout the theme
		'&nbsp;&nbsp;&nbsp;&nbsp;'        . __( 'Show Short Checkout Message for', 'vtwpr' )
    . '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ' . __( '"Grouped by Rule within Product"?', 'vtwpr' ),   
    array(&$this, 'vtwpr_show_cartWidget_discount_titles_above_details_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'cartWidget_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Show Titles above Cart Widget Discount detail lines?', 'vtwpr' )
		)
	);
      
    add_settings_field(	         //opt25
		'show_cartWidget_purchases_subtotal',						// ID used to identify the field throughout the theme
    __( 'Show Cart Purchases Subtotal Line?', 'vtwpr' ),
    array(&$this, 'vtwpr_show_cartWidget_purchases_subtotal_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'cartWidget_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Show Subtotal of cart purchases in the Cart Widget', 'vtwpr' )
		)
	);
    
    add_settings_field(	         //opt32
		'cartWidget_credit_subtotal_title',						// ID used to identify the field throughout the theme
    '&nbsp;&nbsp;&nbsp;&nbsp;'        .__( 'Cart Purchases Subtotal Line', 'vtwpr' )
    . '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'  . __( ' - Label Title', 'vtwpr' ),
		array(&$this, 'vtwpr_cartWidget_credit_subtotal_title_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'cartWidget_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Show discount totals title', 'vtwpr' )
		)
	);

    add_settings_field(	         //opt26
		'show_cartWidget_discount_total_line',						// ID used to identify the field throughout the theme
    __( 'Show Discounts Grand Totals Line?', 'vtwpr' ),		
    array(&$this, 'vtwpr_show_cartWidget_discount_total_line_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'cartWidget_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Show cartWidget separate discount totals line?', 'vtwpr' )
		)
	);

    add_settings_field(	         //opt33
		'cartWidget_credit_total_title',						// ID used to identify the field throughout the theme
    '&nbsp;&nbsp;&nbsp;&nbsp;'        .__( 'Discounts Grand Totals Line', 'vtwpr' )
    . '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'  . __( ' - Label Title', 'vtwpr' ),	
		array(&$this, 'vtwpr_cartWidget_credit_total_title_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'cartWidget_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Show discount totals title', 'vtwpr' )
		)
	);
 
         
    add_settings_field(	         //opt28
		'cartWidget_credit_detail_label',						// ID used to identify the field throughout the theme
		__( 'Discount Detail Line - Credit Label', 'vtwpr' ),	// The label to the left of the option interface element
		array(&$this, 'vtwpr_cartWidget_credit_detail_label_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'cartWidget_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Show discount totals line', 'vtwpr' )
		)
	);
  
    add_settings_field(	         //opt29
		'cartWidget_credit_total_label',						// ID used to identify the field throughout the theme
		__( 'Discount Total Line - Credit Label', 'vtwpr' ),	// The label to the left of the option interface element
		array(&$this, 'vtwpr_cartWidget_credit_total_label_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'cartWidget_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Show discount totals line', 'vtwpr' )
		)
	);
  
  
    add_settings_field(	         //opt45
		'cartWidget_new_subtotal_line',						// ID used to identify the field throughout the theme
		 __( 'Show Products + Discounts', 'vtwpr' )
    . '<br>'  . __( ' Grand Total Line', 'vtwpr' ),	// The label to the left of the option interface element
		array(&$this, 'vtwpr_cartWidget_new_subtotal_line_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'cartWidget_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'New cartWidget subtotal switch', 'vtwpr' )
		)
	);  
    
    
    add_settings_field(	         //opt46
		'cartWidget_new_subtotal_label',						// ID used to identify the field throughout the theme
    '&nbsp;&nbsp;&nbsp;&nbsp;'        .__( 'Product + Discount', 'vtwpr' )
    . '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'  . __( 'Grand Total Line - Label Title', 'vtwpr' ),		
    array(&$this, 'vtwpr_cartWidget_new_subtotal_label_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'cartWidget_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'New cartWidget subtotal label', 'vtwpr' )
		)
	);  
*/
 
  //****************************
  //  Discount Catalog Display - Strikethrough
  //****************************

	// First, we register a section. This is necessary since all future options must belong to a 
	add_settings_section(
		'catalog_settings_section',			// ID used to identify this section and with which to register options
		__( 'Catalog Price Display<span id="vtwpr-catalog-options-anchor"></span>', 'vtwpr' ),	// Title to be displayed on the administration page
		array(&$this, 'vtwpr_catalog_options_callback'),	// Callback used to render the description of the section
		'vtwpr_setup_options_page'		// Page on which to add this section of options
	);

 
    add_settings_field(	         //opt47
		'show_catalog_price_crossout',						// ID used to identify the field throughout the theme
		__( 'Show Catalog Discount Price Crossout', 'vtwpr' ),	// The label to the left of the option interface element
		array(&$this, 'vtwpr_show_catalog_price_crossout_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'catalog_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Do we show the original price with a crossout, followed by the sale price?', 'vtwpr' )
		)
	);
 
  
 /*
  //****************************
  //  Discount Messaging for Theme Area
  //****************************

	// First, we register a section. This is necessary since all future options must belong to a 
	add_settings_section(
		'general_settings_section',			// ID used to identify this section and with which to register options
		__( 'Sell Your Deal Messages - Shown in Theme<span id="vtwpr-discount-messaging-anchor"></span>', 'vtwpr' ),	// Title to be displayed on the administration page
		array(&$this, 'vtwpr_general_options_callback'),	// Callback used to render the description of the section
		'vtwpr_setup_options_page'		// Page on which to add this section of options
	);

 
    add_settings_field(	         //opt34
		'show_yousave_one_some_msg',						// ID used to identify the field throughout the theme
		__( 'Show Catalog Discount Additional Message', 'vtwpr' ),	// The label to the left of the option interface element
		array(&$this, 'vtwpr_show_yousave_one_some_msg_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'general_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Show discount details grouped by', 'vtwpr' )
		)
	);

*/ 
//COMMENT END      
  //****************************
  //  PROCESSING OPTIONS Area
  //****************************
  
  	add_settings_section(
		'processing_settings_section',			// ID used to identify this section and with which to register options
		__( 'Processing Options<span id="vtwpr-processing-options-anchor"></span>', 'vtwpr' ),// Title to be displayed on the administration page
		array(&$this, 'vtwpr_processing_options_callback'), // Callback used to render the description of the section
		'vtwpr_setup_options_page'		// Page on which to add this section of options
	);

/* 	  		
	add_settings_field(	           //opt47
		'bogo_auto_add_the_same_product_type',						// ID used to identify the field throughout the theme
		__( 'BOGO Behavior for Auto Add of Same Product', 'vtwpr' ),		// The label to the left of the option interface element        
		array(&$this, 'vtwpr_bogo_auto_add_the_same_product_type_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'processing_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'BOGO Behavior for Auto Add of Same Product', 'vtwpr' )
		)
	);	  
 */ 
    add_settings_field(	         //opt3
		'discount_floor_pct_per_single_item',						// ID used to identify the field throughout the theme
		__( 'Product Discount Max % Override', 'vtwpr' ),							// The label to the left of the option interface element
		array(&$this, 'vtwpr_discount_floor_pct_per_single_item_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'processing_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Product Discount max percentage', 'vtwpr' )
		)
	);
/*
    add_settings_field(	         //opt4
		'discount_floor_pct_msg',						// ID used to identify the field throughout the theme
		__( 'Product Discount Max % Override Message', 'vtwpr' ),							// The label to the left of the option interface element
    array(&$this, 'vtwpr_discount_floor_pct_msg_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'processing_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Product Discount max percentage message', 'vtwpr' )
		)
	);               
   
    add_settings_field(	        //opt19
		'use_plugin_front_end_css',						// ID used to identify the field throughout the theme
		__( 'Use the Plugin CSS file for Discount Display?', 'vtwpr' ),			// The label to the left of the option interface element
		array(&$this, 'vtwpr_use_plugin_front_end_css_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'processing_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Do we use the plugin front end css at all?', 'vtwpr' )
		)
	);      
     
    add_settings_field(	        //opt9
		'custom_checkout_css',						// ID used to identify the field throughout the theme
		__( 'Custom CSS overrides or additions', 'vtwpr' )
    .'<br>'.
    __( 'to the end of the Plugin CSS File', 'vtwpr' ),			// The label to the left of the option interface element
		array(&$this, 'vtwpr_custom_checkout_css_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'processing_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Do we apply multiple rules to a given product?', 'vtwpr' )
		)
	);     
*/ 
/*    
  //****************************
  //  LIFETIME RULE OPTIONS Area
  //****************************    
  	 if(defined('VTWPR_PRO_DIRNAME')) { 
       $lifetime_rule_settings_section_msg;
     } else {
       $lifetime_rule_settings_section_msg = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . __( '(These options are available in the Pro Version)', 'vtwpr' );
     }
    add_settings_section(
		'lifetime_rule_settings_section',			// ID used to identify this section and with which to register options
		__( 'Customer Rule Limit - Options', 'vtwpr' ) . '<span id="vtwpr-lifetime-options-anchor"></span>' . $lifetime_rule_settings_section_msg,// Title to be displayed on the administration page
		array(&$this, 'vtwpr_lifetime_rule_options_callback'), // Callback used to render the description of the section
		'vtwpr_setup_options_page'		// Page on which to add this section of options
	);

	add_settings_field(	           //opt2
		'use_lifetime_max_limits',						// ID used to identify the field throughout the theme
		__( 'Use Customer Rule Limits', 'vtwpr' ) .'<br><em>&nbsp;&nbsp;&nbsp;&nbsp;'. __( '- Store-Wide Master Switch', 'vtwpr' ) .'</em>',							// The label to the left of the option interface element    
		array(&$this, 'vtwpr_use_lifetime_max_limits_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'lifetime_rule_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			__( 'Store-Wide switch for Customer Rule Limits.', 'vtwpr' )
		)
	);
  
      add_settings_field(	        //opt13
		'max_purch_rule_lifetime_limit_by_ip',						// ID used to identify the field throughout the theme
		__( 'Check Customer against Rule Purchase History,', 'vtwpr' ) .'<br>&nbsp;&nbsp;<i>'. __( 'by IP', 'vtwpr' ) .'</i>',			// The label to the left of the option interface element
		array(&$this, 'vtwpr_lifetime_limit_by_ip_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'lifetime_rule_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Do we Check Customer against Rule Purchase History, by IP?', 'vtwpr' )
		)
	);   
     
      add_settings_field(	        //opt14
		'max_purch_rule_lifetime_limit_by_email',						// ID used to identify the field throughout the theme
		__( 'Check Customer against Rule Purchase History,', 'vtwpr' ) .'<br>&nbsp;&nbsp;<i>'. __( 'by Email', 'vtwpr' ) .'</i>',			// The label to the left of the option interface element
		array(&$this, 'vtwpr_lifetime_limit_by_email_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'lifetime_rule_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Do we Check Customer against Rule Purchase History, by Email?', 'vtwpr' )
		)
	);

          add_settings_field(	        //opt15
		'max_purch_rule_lifetime_limit_by_billto_name',						// ID used to identify the field throughout the theme
		__( 'Check Customer against Rule Purchase History,', 'vtwpr' ) .'<br>&nbsp;&nbsp;<i>'. __( 'by BillTo Name', 'vtwpr' ) .'</i>',			// The label to the left of the option interface element
		array(&$this, 'vtwpr_lifetime_limit_by_billto_name_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'lifetime_rule_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Do we Check Customer against Rule Purchase History, by BillTo Name?', 'vtwpr' )
		)
	);

          add_settings_field(	        //opt16
		'max_purch_rule_lifetime_limit_by_billto_addr',						// ID u<br>&nbsp; sed to identify the field throughout the theme
		__( 'Check Customer against Rule Purchase History,', 'vtwpr' ) .'<br>&nbsp;&nbsp;<i>'. __( 'by BillTo Address', 'vtwpr' ) .'</i>',			// The label to the left of the option interface element
		array(&$this, 'vtwpr_lifetime_limit_by_billto_addr_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'lifetime_rule_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Check Customer against Rule Purchase History, by BillTo Address?', 'vtwpr' )
		)
	);

          add_settings_field(	        //opt17
		'max_purch_rule_lifetime_limit_by_shipto_name',						// ID used to identify the field throughout the theme
		__( 'Check Customer against Rule Purchase History,', 'vtwpr' ) .'<br>&nbsp;&nbsp;<i>'. __( 'by ShipTo Name', 'vtwpr' ) .'</i>',			// The label to the left of the option interface element
		array(&$this, 'vtwpr_lifetime_limit_by_shipto_name_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'lifetime_rule_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Do we Check Customer against Rule Purchase History, by ShipTo Name?', 'vtwpr' )
		)
	);

          add_settings_field(	        //opt18
		'max_purch_rule_lifetime_limit_by_shipto_addr',						// ID u<br>&nbsp; sed to identify the field throughout the theme
		__( 'Check Customer against Rule Purchase History,', 'vtwpr' ) .'<br>&nbsp;&nbsp;<i>'. __( 'by ShipTo Address', 'vtwpr' ) .'</i>',			// The label to the left of the option interface element
		array(&$this, 'vtwpr_lifetime_limit_by_shipto_addr_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'lifetime_rule_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Check Customer against Rule Purchase History, by ShipTo Address?', 'vtwpr' )
		)
	);

    add_settings_field(	         //opt39
		'show_error_before_checkout_products_selector',						// ID used to identify the field throughout the theme
		__( 'Show Error Messages Just Before Checkout', 'vtwpr' ) .'<br>'. __( 'Products List - HTML Selector ', 'vtwpr' ) .'<em>'. __( '(see => "more info")', 'vtwpr' ) .'<em>',							// The label to the left of the option interface element
		array(&$this, 'vtwpr_before_checkout_products_selector_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'lifetime_rule_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			__( 'For the Product area, Supplies the ID or Class HTML selector this message appears before', 'vtwpr' )
		)
	);

    add_settings_field(	         //opt40
		'show_error_before_checkout_address_selector',						// ID used to identify the field throughout the theme
		__( 'Show Error Messages Just Before Checkout', 'vtwpr' ) .'<br>'. __( 'Address List - HTML Selector ', 'vtwpr' )  .'<em>'. __( '(see => "more info")', 'vtwpr' ) .'<em>',		// The label to the left of the option interface element
		array(&$this, 'vtwpr_before_checkout_address_selector_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'lifetime_rule_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			__( 'For the Address area, Supplies the ID or Class HTML selector this message appears before', 'vtwpr' )
		)
	);

    add_settings_field(	         //opt41
		'lifetime_purchase_button_error_msg',						// ID used to identify the field throughout the theme
		__( 'Customer Rule Limit - Button Error Message', 'vtwpr' ),							// The label to the left of the option interface element
		array(&$this, 'vtwpr_lifetime_purchase_button_error_msg_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'lifetime_rule_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			__( 'Customer Rule Limit - Checkout Button Limit Reached Error Message', 'vtwpr' )
		)
	);
*/
         
  //****************************
  //  SYSTEM AND DEBUG OPTIONS Area
  //****************************
  
  	add_settings_section(
		'internals_settings_section',			// ID used to identify this section and with which to register options
		__( 'System and Debug Options<span id="vtwpr-system-options-anchor"></span>', 'vtwpr' ),		// Title to be displayed on the administration page
		array(&$this, 'vtwpr_internals_options_callback'), // Callback used to render the description of the section
		'vtwpr_setup_options_page'		// Page on which to add this section of options
	);
	  		
	add_settings_field(	           //opt20
		'use_this_timeZone',						// ID used to identify the field throughout the theme
		__( 'Select ', 'vtwpr' ) .'<em>'. __( 'Store ', 'vtwpr' ) .'</em>'.  __( 'Time Zone', 'vtwpr' ),		// The label to the left of the option interface element        
		array(&$this, 'vtwpr_use_this_timeZone_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'internals_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Select Store Time Zone', 'vtwpr' )
		)
	);
/*		
	add_settings_field(	           //opt1
		'register_under_tools_menu',						// ID used to identify the field throughout the theme
		__( 'Wholesale Pricing Backend Admin Menu Screens Location', 'vtwpr' ),		// The label to the left of the option interface element        
		array(&$this, 'vtwpr_register_under_tools_menu_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'internals_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Wholesale Pricing Admin Menu Location', 'vtwpr' )
		)
	);
*/	
    add_settings_field(	        //opt8
		'debugging_mode_on',						// ID used to identify the field throughout the theme
		__( 'Test Debugging Mode Turned On', 'vtwpr' ) .'<br>'. __( '(Use Only during testing)', 'vtwpr' ),							// The label to the left of the option interface element
		array(&$this, 'vtwpr_debugging_mode_callback'), // The name of the function responsible for rendering the option interface
		'vtwpr_setup_options_page',	// The page on which this option will be displayed
		'internals_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			__( 'Show any built-in debug info for Rule processing.', 'vtwpr' )
		)
	);                    
  /*	
  
 */
	
	// Finally, we register the fields with WordPress
	register_setting(
		'vtwpr_setup_options_group',
		'vtwpr_setup_options' ,
    array(&$this, 'vtwpr_validate_setup_input')
	);
	
} // end vtwpr_initialize_options

 
  
   
  //****************************
  //  DEFAULT OPTIONS INITIALIZATION
  //****************************
function vtwpr_set_default_options() {
      if(defined('VTWPR_PRO_DIRNAME')) { 
        $use_lifetime_max_limits_default = 'yes';
      } else {
        $use_lifetime_max_limits_default = 'no';
      }      
     $options = array(           
          'register_under_tools_menu'=> 'no',  //opt1         
          'use_lifetime_max_limits' => $use_lifetime_max_limits_default,    //opt2
          'discount_floor_pct_per_single_item' => '', //opt3  STORE-WIDE Discount max percent
          'discount_floor_pct_msg' => 'System Max xx% Discount reached.',  //opt4
          'show_checkout_discount_total_line' => 'yes', //opt5  yes/no => show total of discounts AFTER products displayed
          'show_checkout_discount_detail_lines' => 'yes', //opt6  yes/no => show detail of discounts AFTER products displayed
          'show_cartWidget_discount_titles_above_details'  => 'yes',  //opt7
          'debugging_mode_on' => 'no',                    //opt8
          'custom_checkout_css'  => '',  //opt9
          
          'checkout_credit_detail_label' => '-', //opt10  TEXT field, suggest '-', 'CR', 'cr' ==>> their choice!!!!!!!!!!!!!!!
          'checkout_credit_total_label' => '-', //opt11  TEXT field, suggest '-', 'CR', 'cr' ==>> their choice!!!!!!!!!!!!!!!
          'checkout_html_colspan_value' => '3', //opt42
          'cartWidget_html_colspan_value' => '5', //opt12
          'max_purch_rule_lifetime_limit_by_ip' => 'yes',  //opt13
          'max_purch_rule_lifetime_limit_by_email' => 'yes',  //opt14
          'max_purch_rule_lifetime_limit_by_billto_name' => 'yes',  //opt15
          'max_purch_rule_lifetime_limit_by_billto_addr' => 'yes',  //opt16
          'max_purch_rule_lifetime_limit_by_shipto_name' => 'yes',  //opt17
          'max_purch_rule_lifetime_limit_by_shipto_addr' => 'yes',   //opt18                    
          'use_plugin_front_end_css'  => 'yes',  //opt19  allows the user to shut off msg css and put their own into their own theme
          'use_this_timeZone'  => 'keep',  //opt20 set store timezone relative to gmt 
//          'nanosecond_delay_for_add_to_cart_processing' => '1000', //opt46 "1000" = 1 second
          'bogo_auto_add_the_same_product_type' => 'allAdds', //opt47  values: allAdds / fitInto
          'show_checkout_discount_details_grouped_by_what'  => 'rule',  //opt21
          'show_cartWidget_discount_details_grouped_by_what'  => 'rule',  //opt22 
          'show_checkout_discount_titles_above_details'  => 'yes',  //opt23 
          'show_checkout_purchases_subtotal'  => 'withDiscounts',  //opt24  
          'show_cartWidget_purchases_subtotal'  => 'none',  //opt25 
          'show_cartWidget_discount_total_line' => 'yes', //opt26  yes/no => show total of discounts AFTER products displayed
          'show_cartWidget_discount_detail_lines' => 'yes', //opt27  yes/no => show detail of discounts AFTER products displayed
          'cartWidget_credit_detail_label' => '-', //opt28  TEXT field, suggest '-', 'CR', 'cr' ==>> their choice!!!!!!!!!!!!!!!
          'cartWidget_credit_total_label' => '-', //opt29  TEXT field, suggest '-', 'CR', 'cr' ==>> their choice!!!!!!!!!!!!!!!      
          'checkout_credit_subtotal_title' => 'Subtotal - Cart Purchases:', //opt30 
          'checkout_credit_total_title' => 'Cart Discount Total:', //opt31
          'show_checkout_credit_total_when_coupon_active' => 'yes', //opt45
          'cartWidget_credit_subtotal_title' => 'Products:', //opt32 
          'cartWidget_credit_total_title' => 'Discounts:', //opt33 
          'show_yousave_one_some_msg' => 'yes', //opt34 
          'show_old_price' => 'docOnly', //opt35 not used as switching, just documentation!!
          'show_rule_msgs' => 'docOnly', //opt36 not used as switching, just documentation!!
          'discount_purchase_log' => 'yes', //opt37
          'max_purch_checkout_forms_set' => '0',  //opt38
          'show_error_before_checkout_products_selector' => VTWPR_CHECKOUT_PRODUCTS_SELECTOR_BY_PARENT,  //opt39
          'show_error_before_checkout_address_selector'  => VTWPR_CHECKOUT_ADDRESS_SELECTOR_BY_PARENT,  //opt40
          'lifetime_purchase_button_error_msg' => VTWPR_CHECKOUT_BUTTON_ERROR_MSG_DEFAULT,  //opt41
          'checkout_new_subtotal_line' => 'yes', //opt43  
          'checkout_new_subtotal_label' => 'Subtotal with Discount:', //opt44
          'cartWidget_new_subtotal_line' => 'yes', //opt45  
          'cartWidget_new_subtotal_label' => 'Subtotal with Discount:', //opt46  
          'show_catalog_price_crossout' => 'no' //opt47
     );
     return $options;
}

function vtwpr_processing_options_callback () {
    ?>
    <h4 id="vtwpr-processing-options">
    <?php esc_attr_e('These options apply to general discount processing.', 'vtwpr'); ?>
    <br><br>
    <?php esc_attr_e('Please NOTE: This Plugin uses the parent plugin currency settings. So if the parent plugin is set for &euro; currency symbol with a comma separator, that setting is used in this plugin as well.', 'vtwpr'); ?>
    </h4>
    <?php                                                                                                                                                                                      
}

   
function vtwpr_lifetime_rule_options_callback () {
    ?>
    <h4 id="vtwpr-lifetime-options"><?php esc_attr_e('Customer Rule Limit Options set Store-Wide switches regarding whether and at what information level Customer Rule Limits are applied.', 'vtwpr'); ?></h4>
    <br>
    <p id="subTitle"><?php _e('The "Use Customer Rule Limits" switch is a Store-Wide switch for the whole installation,
        and must be set to "Yes" in order for individual Rule-based Customer Rule Limit switches to be active.', 'vtwpr'); ?>
    </p>
    <p id="subTitle"><?php _e('Customer Rule Limits by IP can be applied immediately at add-to-cart time.
      All other name, email and address limits are applied at checkout time.', 'vtwpr');?>
        <br>&nbsp;&nbsp;&nbsp;&nbsp; 
      <?php _e('(Customer Rule Limit processing options are available with the Pro version)', 'vtwpr'); ?>
    </p> 
    <?php                                                                                                                                                                                      
}

function vtwpr_nav_callback () {                                      

    ?>

    <?php //BANNER AND BUTTON AREA ?>
    <img id="wholesale-pricing-img-preload" alt="" src="<?php echo VTWPR_URL;?>/admin/images/upgrade-bkgrnd-banner.jpg" />
 		<div id="upgrade-title-area">
      <a  href=" <?php echo VTWPR_PURCHASE_PRO_VERSION_BY_PARENT ; ?> "  title="Purchase Pro">
      <img id="wholesale-pricing-img" alt="help" height="40px" width="40px" src="<?php echo VTWPR_URL;?>/admin/images/sale-circle.png" />
      </a>
      <h2><?php _e('Wholesale Pricing', 'vtwpr'); ?>
          <?php
            if(defined('VTWPR_PRO_DIRNAME')) { 
               _e(' Pro', 'vtwpr');
            }           
          ?> 
      </h2>
      
      <?php if(!defined('VTWPR_PRO_DIRNAME')) {  ?> 
          <span class="group-power-msg"><strong><?php _e('Get Group Power', 'vtwpr'); ?></strong>
              &nbsp;-&nbsp; 
            <?php _e('Apply rules to ', 'vtwpr'); ?>
            <em>
            <?php _e('any group you can think of, and More!', 'vtwpr'); ?>
            </em>
          </span> 
          <span class="buy-button-area">
            <a href="<?php echo VTWPR_PURCHASE_PRO_VERSION_BY_PARENT; ?>" class="buy-button">
                <span class="buy-button-label"><?php _e('Get Wholesale Pricing Pro', 'vtwpr'); ?></span>
            </a>
          </span> 
      <?php }  ?>
          
    </div>  
           
                                       
     <div id="vtwpr-options-help-panel"> 
        <a id="wholesale_pricing-title-more" class="more-anchor" href="javascript:void(0);"><img class="wholesale_pricing-title-helpPng" alt="help"  width="14" height="14" src="<?php echo VTWPR_URL;?>/admin/images/help.png" /><?php _e(' Help!', 'vtwpr'); echo '&nbsp;'; _e('Tell me about Wholesale Pricing ... ', 'vtwpr'); ?><img class="plus-button" alt="help" height="14px" width="14px" src="<?php echo VTWPR_URL;?>/admin/images/plus-toggle2.png" /></a>            
        <a id="wholesale_pricing-title-less" class="more-anchor less-anchor" href="javascript:void(0);"><img class="wholesale_pricing-title-helpPng" alt="help" width="14" height="14" src="<?php echo VTWPR_URL;?>/admin/images/help.png" /><?php _e('   Less Wholesale Pricing Help ... ', 'vtwpr'); echo '&nbsp;'; ?><img class="minus-button" alt="help" height="14px" width="14px" src="<?php echo VTWPR_URL;?>/admin/images/minus-toggle2.png" /></a>                
          <?php  vtwpr_show_help_selection_panel_0();  ?>
     </div>

    
         <div id="vtwpr-options-menu">        
              <ul>                                                           
                <li>
                  <b>JUMP TO: </b>
                </li>
                <li>
                  <a href="#vtprd-catalog-options-anchor" title="Discount Catalog Display"><?php _e('Catalog Price Display', 'vtprd'); ?></a>
                </li>  
                <span>|
                </span>               
                <li>
                  <a href="#vtwpr-processing-options-anchor" title="Processing Options"><?php _e('Processing Options', 'vtwpr'); ?></a>
                </li>  
                <span>|
                </span>

                <li>
                  <a href="#vtwpr-system-options-anchor" title="System and Debug Options"><?php _e('System and Debug Options', 'vtwpr'); ?></a>
                </li>  
                <span>|
                </span>
                <li>
                  <a href="#vtwpr-system-buttons-anchor" title="System Buttons"><?php _e('System Buttons', 'vtwpr'); ?></a>
                </li>  
                <span>|
                </span>                
                <li>
                <a href="#vtwpr-plugin-info-anchor" title="Plugin Info"><?php _e('Plugin Info', 'vtwpr'); ?></a>
                </li>  
                <!-- last li does not have spacer at end... -->          
              </ul>   
            </div>   

    <?php
}

function vtwpr_catalog_options_callback () {
                                          
    ?>                                   
    <h4 id="vtwpr-discount-messaging"><?php esc_attr_e('These options control Catalog Discount Display in the Theme.', 'vtwpr'); ?> 

    </h4> 
    <?php    
    
}

function vtwpr_general_options_callback () {
                                          
    ?>                                   
    <h4 id="vtwpr-discount-messaging"><?php esc_attr_e('These options control Wholesale Pricing messaging shown in the Theme.', 'vtwpr'); ?> 

    </h4> 
    <?php    
    
}

function vtwpr_checkout_options_callback () {
    ?>                                   
    <h4 id="vtwpr-checkout-reporting"><?php esc_attr_e('These options control Wholesale Pricing checkout display.', 'vtwpr'); ?>
      <a id="help-all" class="help-anchor" href="javascript:void(0);" >
      <?php esc_attr_e('Show All:', 'vtwpr'); ?> 
      &nbsp; <span> <?php esc_attr_e('More Info', 'vtwpr'); ?> </span></a>     
    </h4> 
    <?php 
}

function vtwpr_cartWidget_options_callback () {
    if(defined('WPSC_VERSION') && (VTWPR_PARENT_PLUGIN_NAME == 'WP E-Commerce') ) {
    ?>
    <h4 id="vtwpr-cartWidget-options"><?php esc_attr_e('In order to display discounts in the Cart Widget, 2-3 lines of your theme cart-widget.php code need to be added/altered.   
            Instructions for these changes are described in the plugin readme file.', 'vtwpr'); ?></h4>
    <?php
    } 
}

function vtwpr_internals_options_callback () {
    ?>
    <h4 id="vtwpr-system-options" id="vtwpr-internal-options"><?php esc_attr_e('These options control internal functions within the plugin.', 'vtwpr'); ?></h4>
    <?php  
}

function vtwpr_show_old_price_callback () {   //opt35  documentation only, no switches set here!   
  $html .= '<span>'; 
  $html .=  __('When a Catalog Rule Discount rule is applied, the Old Price and You Save Messages can be displayed.', 'vtwpr')  .'<strong><em>'. __('See', 'vtwpr') .'</em> =></strong>'; 
  $html .= '<a id="help35" class="doc-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';  
  $html .= '</span>';
  $html .= '<p id="help35-text" class="help-text doc-text" >';
  $html .= '&nbsp;&nbsp;&nbsp; <strong>' . __('In order to show Old Price and You Save Messages when a Catalog Rule Discount rule is applied', 'vtwpr') .'</strong>'. 
           __(', change "wpsc_the_product_price_display()" to "vtwpr_the_product_price_display()" in the single product view, grid view,list view theme files, as documented in 
           the Wholesale Pricing plugin files to be found in WPSC-intgration/Sample wpsc-theme 3.8.9+.', 'vtwpr');
  $html .= '<br><br>&nbsp;' .  __('At that point, the Old Price and You Save messages will be automatically generated across all of the edited files.', 'vtwpr');
  $html .= '<br><br>&nbsp;' .  __('In order to control the messaging ', 'vtwpr') .'<em><strong>'. __('by file type', 'vtwpr') .'</strong></em>'. __(', add one or both of the array parameters as follows:', 'vtwpr'); 
  $html .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;' .  __(' vtwpr_the_product_price_display( array( "output_old_price" => false ) );  => Turns off the Old Price messages', 'vtwpr'); 
  $html .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;' .  __(' vtwpr_the_product_price_display( array( "output_you_save" => false ) );  => Turns off the You Save message', 'vtwpr');  
  $html .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;' .  __(' vtwpr_the_product_price_display( array( "output_old_price" => false, "output_you_save" => false ) );  => Turns off both messages', 'vtwpr');      
  $html .= '</p>';
 
	echo $html;
  
}


function vtwpr_show_rule_msgs_callback () {   //opt36   documentation only, no switches set here!   
  $html .= '<span>'; 
  $html .=  __('Wholesale Pricing Description Messages can be shown anywhere in Theme, both inside and outside the loop. ', 'vtwpr')  .'<strong><em>'. __('See', 'vtwpr') .'</em> =></strong>';  
  $html .= '<a id="help36" class="doc-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';  
  $html .= '</span>';
  $html .= '<p id="help36-text" class="help-text doc-text" >';
  $html .=  '&nbsp;&nbsp;&nbsp; <strong>' . __('Wholesale Pricing Description Messages can be shown anywhere in Theme, both inside and outside the loop.  They can be shown for
                the entire site (store-wide discounts), by product category, by wholesale pricing custom taxonomy, by product, by rule - 
                both inside the loop and outside, as documented in 
                the Wholesale Pricing plugin files to be found in WPSC-intgration/Sample wpsc-theme 3.8.9+.', 'vtwpr') .'</strong>';
  $html .= '<br><br>&nbsp;' .  __('At that point, the Old Price and You Save messages will be automatically generated across all of the edited files.', 'vtwpr');
  $html .= '<br><br>&nbsp;' .  __('In order to control the messaging', 'vtwpr') .'<em><strong>'. __('by file type', 'vtwpr') .'</strong></em>'. __(', add one or both of the array parameters as follows:', 'vtwpr'); 
  $html .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;' .  __(' vtwpr_the_product_price_display( array( "output_old_price" => false ) );  => Turns off the Old Price messages', 'vtwpr'); 
  $html .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;' .  __(' vtwpr_the_product_price_display( array( "output_you_save" => false ) );  => Turns off the You Save message', 'vtwpr');  
  $html .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;' .  __(' vtwpr_the_product_price_display( array( "output_old_price" => false, "output_you_save" => false ) );  => Turns off both messages', 'vtwpr');      
  $html .= '</p>';
 
	echo $html;
}


function vtwpr_show_catalog_price_crossout_callback () {   //opt47
	$options = get_option( 'vtwpr_setup_options' );	
	$html = '<select id="show_catalog_price_crossout" name="vtwpr_setup_options[show_catalog_price_crossout]">';	
  $html .= '<option value="yes"' . selected( $options['show_catalog_price_crossout'], 'yes', false) . '>'   . __('Yes', 'vtwpr') .  '&nbsp;</option>';
  $html .= '<option value="no"'  . selected( $options['show_catalog_price_crossout'], 'no', false)  . '>'   . __('No', 'vtwpr') . '</option>';
	$html .= '</select>';
  $html .= '<a id="help47" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
  $html .= '<p><em>&nbsp;&nbsp;';
  $html .= __('For a Catalog Template Rule, Do we show the original price with a crossout, followed by the sale price?', 'vtwpr');
  $html .=  '</em></p>';	
  $html .= '<p id="help47-text" class = "help-text" >'; 
  $html .= __('Useful if an item or group of items are on sale, independant of wholesale pricing...', 'vtwpr'); 
  $html .= '</p>';
  
	echo $html;
}

function vtwpr_show_yousave_one_some_msg_callback () {   //opt34
	$options = get_option( 'vtwpr_setup_options' );	
	$html = '<select id="show_yousave_one_some_msg" name="vtwpr_setup_options[show_yousave_one_some_msg]">';	
  $html .= '<option value="yes"' . selected( $options['show_yousave_one_some_msg'], 'yes', false) . '>'   . __('Yes', 'vtwpr') .  '&nbsp;</option>';
  $html .= '<option value="no"'  . selected( $options['show_yousave_one_some_msg'], 'no', false)  . '>'   . __('No', 'vtwpr') . '</option>';
	$html .= '</select>';
  $html .= '<a id="help34" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
  $html .= '<p><em>&nbsp;&nbsp;';
  $html .= __('For a Catalog Template Rule deal where a Product with variations has only some of the variations on sale, do we show an addtional "one of these are on sale" meessage
            saying "one/some of these are on sale and rule messages are displayed"?', 'vtwpr');
  $html .=  '</em></p>';	
  $html .= '<p id="help34-text" class = "help-text" >'; 
  $html .= __('"A Display (catlog) wholesale pricing is in force.  It acts on a product with multiple variations, but only some have a price reduction.', 'vtwpr') 
              .'<br><br>&nbsp;&nbsp;'. __('Instead of a "yousave" message, show either:', 'vtwpr') 
              .'<br>&nbsp;&nbsp&nbsp;&nbsp;'. __('"One of these are on sale"', 'vtwpr')  
              .'<br>&nbsp;&nbsp&nbsp;&nbsp;'. __('"Some of these are on sale".', 'vtwpr') 
              .'<br><br>&nbsp;&nbsp;'. 
              __('When messages are requested via the "vtwpr_show_product_realtime_discount_full_msgs_action", the "one of these are on sale" message will display also.', 'vtwpr'); 
  $html .= '</p>';
  
	echo $html;
}

function vtwpr_use_this_timeZone_callback() {    //opt20                                 
	$options = get_option( 'vtwpr_setup_options' );	
	/* scares the punters
  if ( $options['use_this_timeZone'] == 'none') {
      echo '<span id="gmtError">';
      echo __('Please Select the Store GMT Time Zone. Your Web Host Server can have a different date than your Store, which can throw off Wholesale Pricing Rules begin/end dates.', 'vtwpr');
      echo '</span><br>'; 
  }
  */
  $html = '<select id="use_this_timeZone" name="vtwpr_setup_options[use_this_timeZone]">';
	$html .= '<option value="keep"'                   .  selected( $options['use_this_timeZone'], 'keep', false)                    . '>' . __('Host Server already in the correct Time Zone', 'vtwpr') . '</option>';
  $html .= '<option value="Europe/London"'          .  selected( $options['use_this_timeZone'], 'Europe/London', false)           . '>GMT &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Europe/London</option>';
  $html .= '<option value="Europe/Paris"'           .  selected( $options['use_this_timeZone'], 'Europe/Paris', false)            . '>GMT+1 &nbsp;&nbsp;&nbsp; Europe/Paris</option>';
  $html .= '<option value="Europe/Athens"'          .  selected( $options['use_this_timeZone'], 'Europe/Athens', false)           . '>GMT+2 &nbsp;&nbsp;&nbsp; Europe/Athens</option>';
  $html .= '<option value="Africa/Nairobi"'         .  selected( $options['use_this_timeZone'], 'Africa/Nairobi', false)          . '>GMT+3 &nbsp;&nbsp;&nbsp; Africa/Nairobi</option>';
  $html .= '<option value="Europe/Moscow"'          .  selected( $options['use_this_timeZone'], 'Europe/Moscow', false)           . '>GMT+4 &nbsp;&nbsp;&nbsp; Europe/Moscow</option>';
  $html .= '<option value="Asia/Calcutta"'          .  selected( $options['use_this_timeZone'], 'Asia/Calcutta', false)           . '>GMT+5 &nbsp;&nbsp;&nbsp; Asia/Calcutta</option>';
  $html .= '<option value="Asia/Dhaka"'             .  selected( $options['use_this_timeZone'], 'Asia/Dhaka', false)              . '>GMT+6 &nbsp;&nbsp;&nbsp; Asia/Dhaka</option>';
  $html .= '<option value="Asia/Krasnoyarsk"'       .  selected( $options['use_this_timeZone'], 'Asia/Krasnoyarsk', false)        . '>GMT+7 &nbsp;&nbsp;&nbsp; Asia/Krasnoyarsk</option>';
  $html .= '<option value="Australia/Perth"'        .  selected( $options['use_this_timeZone'], 'Australia/Perth', false)         . '>GMT+8 &nbsp;&nbsp;&nbsp; Australia/Perth</option>';
  $html .= '<option value="Asia/Seoul"'             .  selected( $options['use_this_timeZone'], 'Asia/Seoul', false)              . '>GMT+9 &nbsp;&nbsp;&nbsp; Asia/Seoul</option>';
  $html .= '<option value="Australia/Darwin"'       .  selected( $options['use_this_timeZone'], 'Australia/Darwin', false)        . '>GMT+9.5&nbsp; Australia/Darwin</option>';
  $html .= '<option value="Australia/Sydney"'       .  selected( $options['use_this_timeZone'], 'Australia/Sydney', false)        . '>GMT+10 &nbsp; Australia/Sydney</option>';
  $html .= '<option value="Asia/Magadan"'           .  selected( $options['use_this_timeZone'], 'Asia/Magadan', false)            . '>GMT+11 &nbsp; Asia/Magadan</option>';
  $html .= '<option value="Pacific/Auckland"'       .  selected( $options['use_this_timeZone'], 'Pacific/Auckland', false)        . '>GMT+12 &nbsp; Pacific/Auckland</option>';
  $html .= '<option value="Atlantic/Azores"'        .  selected( $options['use_this_timeZone'], 'Atlantic/Azores', false)         . '>GMT-1 &nbsp;&nbsp;&nbsp;&nbsp; Atlantic/Azores</option>';
  $html .= '<option value="Atlantic/South_Georgia"' .  selected( $options['use_this_timeZone'], 'Atlantic/South_Georgia', false)  . '>GMT-2 &nbsp;&nbsp;&nbsp;&nbsp; Atlantic/South_Georgia</option>';
  $html .= '<option value="America/Sao_Paulo"'      .  selected( $options['use_this_timeZone'], 'America/Sao_Paulo', false)       . '>GMT-3 &nbsp;&nbsp;&nbsp;&nbsp; America/Sao_Paulo</option>';
  $html .= '<option value="America/St_Johns"'       .  selected( $options['use_this_timeZone'], 'America/St_Johns', false)        . '>GMT-3.5 &nbsp; America/St_Johns</option>';
  $html .= '<option value="America/Halifax"'        .  selected( $options['use_this_timeZone'], 'America/Halifax', false)         . '>GMT-4 &nbsp&nbsp;&nbsp;&nbsp; America/Halifax</option>';
  $html .= '<option value="America/Caracas"'        .  selected( $options['use_this_timeZone'], 'America/Caracas', false)         . '>GMT-4.5 &nbsp; America/Caracas</option>';
  $html .= '<option value="America/New_York"'       .  selected( $options['use_this_timeZone'], 'America/New_York', false)        . '>GMT-5 &nbsp&nbsp;&nbsp;&nbsp; America/New_York</option>';
  $html .= '<option value="America/Chicago"'        .  selected( $options['use_this_timeZone'], 'America/Chicago', false)         . '>GMT-6 &nbsp&nbsp;&nbsp;&nbsp; America/Chicago</option>';
  $html .= '<option value="America/Denver"'         .  selected( $options['use_this_timeZone'], 'America/Denver', false)          . '>GMT-7 &nbsp&nbsp;&nbsp;&nbsp; America/Denver</option>';
  $html .= '<option value="America/Los_Angeles"'    .  selected( $options['use_this_timeZone'], 'America/Los_Angeles', false)     . '>GMT-8 &nbsp&nbsp;&nbsp;&nbsp; America/Los_Angeles</option>';
  $html .= '<option value="America/Anchorage"'      .  selected( $options['use_this_timeZone'], 'America/Anchorage', false)       . '>GMT-9 &nbsp&nbsp;&nbsp;&nbsp; America/Anchorage</option>';
  $html .= '<option value="Pacific/Honolulu"'       .  selected( $options['use_this_timeZone'], 'Pacific/Honolulu', false)        . '>GMT-10 &nbsp;&nbsp; Pacific/Honolulu</option>';
  $html .= '<option value="Pacific/Midway"'         .  selected( $options['use_this_timeZone'], 'Pacific/Midway', false)          . '>GMT-11 &nbsp;&nbsp; Pacific/Midway</option>';
  $html .= '<option value="Kwajalein"'              .  selected( $options['use_this_timeZone'], 'Kwajalein', false)               . '>GMT-12 &nbsp;&nbsp; Kwajalein, Marshall Islands</option>';  
	$html .= '</select>';
  $html .= '&nbsp;&nbsp;&nbsp;<a  href="http://wwp.greenwichmeantime.com/time-zone/"  title="' . __('Find Your GMT Time Zone', 'vtwpr') . '">' . __('Find Your GMT Time Zone', 'vtwpr') . '</a>';
  $html .= '<a id="help20" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
  $html .= '<br><br><em>';
  $html .= __('(Your host server can have a Different Time Zone and *Date* than your store, which can throw off Rule begin/end dates.)', 'vtwpr');
  $html .=  '</em>';
	 
  $html .= '<p id="help20-text" class = "help-text" >'; 
  $html .= __('Please select the GMT value which matches the Store Location Time Zone.  This helps the date ranges in the Rule setup to be as accurate
              as possible.  They will now be anywhere from accurate to 1 hour off (because of Daylight Savings, different the world over).', 'vtwpr')  
              .'<br><br><em>'.
              __('Your host server can have a different date than your store, depending on time of day!', 'vtwpr')
              .'</em><br><br>'.
              __('You can find your store GMT timezone in', 'vtwpr') 
              .'<a  href="http://wwp.greenwichmeantime.com/time-zone/"  title="'. 
              __('Find Your GMT Time Zone">Find Your GMT Time Zone', 'vtwpr')
              .'</a><br><br>'.
              __('**If the time zone setting has no affect on the store, Check your php ini file whether timezone is set.**', 'vtwpr');
               
  $html .= '</p><br><br>';  
                                    
	echo $html;
}



function vtwpr_bogo_auto_add_the_same_product_type_callback() {    //opt47                                
	$options = get_option( 'vtwpr_setup_options' );	
  $html = '<select id="bogo_auto_add_the_same_product_type" name="vtwpr_setup_options[bogo_auto_add_the_same_product_type]">';
	$html .= '<option value="allAdds"' .  selected( $options['bogo_auto_add_the_same_product_type'], 'allAdds', false) . '>'   . __('Product Qty changes always considered to be purchases - auto adds added to the qty', 'vtwpr') .  '</option>';
  $html .= '<option value="fitInto"' .  selected( $options['bogo_auto_add_the_same_product_type'], 'fitInto', false) . '>'   . __('Auto adds applied to qty 1st time. Changed quantity = combined total of both purchases and auto adds', 'vtwpr') .  '</option>';
  $html .= '</select>';
  $html .= '<a id="help47" class="help-anchor" href="javascript:void(0);" >'   . __('SEE => More Info', 'vtwpr') .  '</a>';
  $html .= '<p id="help47-text" class = "help-text" >'; 
  $html .= __('(1) a rule is set up to be BOGO, and', 'vtwpr') 
          .'<br>'.
           __('(2) both the Buy and Action Filter groups apply to the same product, and', 'vtwpr') 
           .'<br>'.
           __('(3) the auto Add Free Product switch is on:', 'vtwpr')
           .'<br>'.
           __('This setting controls that Auto Add behavior.', 'vtwpr')  
          .'<br><br>'.
           __('Default behavior:  any change to the quantity field for the BOGO product is treated as a purchase, and the auto adds are then added to that quantity.', 'vtwpr')
           .'<br>&nbsp;&nbsp;&nbsp;&nbsp;'.
           __('For example: Initial purchase qty =2 units.  Result = 2 units purchased + 2 units free, for a total of 4 units.', 'vtwpr')
            .'<br>'.
           __('AFTER THE INITIAL ADD TO CART, If customer then CHANGES THE QTY to 5 units, the AUTO ADDS ARE APPLIED TO THAT QUANTTY.  Result = 5 units purchased + 5 units free, for a total of 10 units.', 'vtwpr') 
           .'<br><br>'.
           __('Optional behavior:  Only the first purchase the BOGO product is treated as a purchase, and the auto adds are then added to that quantity.', 'vtwpr')
           .'<br>&nbsp;&nbsp;&nbsp;&nbsp;'.
           __('For example: Initial purchase qty =2 units.  *** Result = 2 units purchased + 2 units free, for a total of 4 units *** .', 'vtwpr')
           .'<br>'.
           __('AFTER THE INITIAL ADD TO CART, If customer then CHANGES THE QTY that quantity becomes the TOTAL TARGET OF PURCHASES + AUTO ADDS.', 'vtwpr')
           .'<br>'.
           __('For example a CHANGED QTY of 7 REMAINS AT 7, CONTAINING: *** 4 purchased + 3 free *** .', 'vtwpr');  
                     
  $html .= '</p>';  
  
	echo $html;
}	  	

/*  
function vtwpr_register_under_tools_menu_callback() {   //opt1
	$options = get_option( 'vtwpr_setup_options' );	
	$html = '<select id="register_under_tools_menu" name="vtwpr_setup_options[register_under_tools_menu]">';
	$html .= '<option value="no"'  . selected( $options['register_under_tools_menu'], 'no', false) . '>'   . __('In the Main Admin Menu as its own Heading (def) ', 'vtwpr') . '&nbsp;</option>';
  $html .= '<option value="yes"' . selected( $options['register_under_tools_menu'], 'yes', false) . '>'   . __('"Hide" under the Tools Menu (and Settings go under the Settings Menu) ', 'vtwpr') .  '&nbsp;</option>';
	$html .= '</select>';
  $html .= '<a id="help1" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
  $html .= '<p><em>&nbsp;&nbsp;';
  $html .= __('(on update, the settings screen display will fail with "Cannot load", and that"s ok as the location will have shifted.)', 'vtwpr');
  $html .=  '</em></p>';	
  $html .= '<p id="help1-text" class = "help-text" >'; 
  $html .= __('"Wholesale Pricing Admin Menu Location" - The Admin menu area tends to get a little overcrowded.  If that is so in your installation, you can elect
             to move the Wholesale Pricing menu items under the TOOLS menu.', 'vtwpr'); 
  $html .= '</p>';
  
	echo $html;
}
*/

function vtwpr_use_lifetime_max_limits_callback() {   //opt2
	$options = get_option( 'vtwpr_setup_options' );	
	$html = '<select id="use_lifetime_max_limits" name="vtwpr_setup_options[use_lifetime_max_limits]">';
	$html .= '<option value="yes"' . selected( $options['use_lifetime_max_limits'], 'yes', false) . '>'   . __('Yes', 'vtwpr') .  '&nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['use_lifetime_max_limits'], 'no', false)  . '>'   . __('No', 'vtwpr') . '</option>';
	$html .= '</select>';
  
  $html .= '<a id="help2" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
  
  $html .= '<p id="help2-text" class = "help-text" >'; 
  $html .= __('The "Use Customer Rule Limits" switch is a Store-Wide switch for the whole installation,
        and must be set to "Yes" in order for individual Rule-based Customer Rule Limit switches to be active.', 'vtwpr'); 
  $html .= '</p>'; 
  $html .= '<p><em>&nbsp;&nbsp;';
  $html .= __('This switch controls both the active testing of Customer Rule Limits, and the
        storage of historical purchase data for future Customer Rule Limit checking.  Customer Rule Limit checking data will only be stored if this switch = "yes"', 'vtwpr');
  $html .=  '</em></p>';
    
  //Heading for the next section, with description      
  $html .= '<p id="lifetime-by-ip-intro" class="extra-intro">'; 
  $html .= '<strong>'. __('Checking by IP is immediately available at Shortcode time,, at Add to Cart time and at Checkout time.', 'vtwpr') .'</strong>'; 
  $html .= '</p>';  
            
	echo $html;
}

function vtwpr_discount_floor_pct_per_single_item_callback() {    //opt3
	$options = get_option( 'vtwpr_setup_options' );	
  $html = '<input type="text" class="smallText" id="discount_floor_pct_per_single_item"  rows="1" cols="20" name="vtwpr_setup_options[discount_floor_pct_per_single_item]" value="' . $options['discount_floor_pct_per_single_item'] . '">' . '%';

  $html .= '<a id="help3" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
  $html .= '<br>&nbsp;&nbsp;';
  $html .= '<i>'. __( 'Store-Wide "no more than" Product limit (Free products are exempt from this limit)', 'vtwpr' )  .'</i>';
    
  $html .= '<p id="help3-text" class = "help-text" >'; 
  $html .= __('Set an absolute Product Discount max percentage, below which no discount will go - Store-Wide Setting => all accumulated discounts applied to a product may not go below this percentage', 'vtwpr'); 
  $html .= '<br><br>'. __('Blank = do not use. ', 'vtwpr');
  $html .= '<br><br>'. __('Default = blank', 'vtwpr');
  $html .= '</p><br><br>';
	echo $html;
}


function vtwpr_discount_floor_pct_msg_callback() {    //opt4
	$options = get_option( 'vtwpr_setup_options' );	
  $html = '<textarea type="text" id="discount_floor_pct_msg"  rows="1" cols="60" name="vtwpr_setup_options[discount_floor_pct_msg]">' . $options['discount_floor_pct_msg'] . '</textarea>';

  $html .= '<a id="help4" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
  
  $html .= '<p id="help4-text" class = "help-text" >'; 
  $html .= __('Product Discount max Percentage Message.  Message must be both filled in here, and requested via theme customization
              using the "vtwpr_show_product_discount_limit_reached_short_msg_action" documented in the readme.', 'vtwpr')    
              .'<br><br>'.
              __('** The message is shown in cart and checkout only **', 'vtwpr') 
              .'<br><br>'.
              __('and will only appear when the Product Discount Max Percentage limit has been reached.', 'vtwpr')
              .'<br><br>'.
              __('Default value = "System Max xx% Discount reached.".', 'vtwpr');                
  $html .= '</p>';
  	
	echo $html;
}



function vtwpr_show_checkout_discount_details_grouped_by_what_callback() {    //opt21                                 
	$options = get_option( 'vtwpr_setup_options' );	
  $html = '<select id="show_checkout_discount_details_grouped_by_what" name="vtwpr_setup_options[show_checkout_discount_details_grouped_by_what]">';
	$html .= '<option value="rule"'             .  selected( $options['show_checkout_discount_details_grouped_by_what'], 'rule', false)    . '>'   . __('Grouped by Rule within Product', 'vtwpr') . '&nbsp;</option>';
  $html .= '<option value="product"'          .  selected( $options['show_checkout_discount_details_grouped_by_what'], 'product', false) . '>'   . __('Grouped by Product ', 'vtwpr') .  '</option>';
  $html .= '</select>';
  $html .= '<a id="help21" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
  $html .= '<p id="help21-text" class = "help-text" >'; 
  $html .= __('If Checkout Discount detail lines are to be displayed, how are they organized?  By Rule, there will be a separate line by Rule for each product which got a discount based on that rule.
              You can elect to show the relevant Rule short cart message in a line above each detail line.', 'vtwpr') 
          .'<br><br>'.
           __('By product totals up all discounts accrued to that product, and produces a single detail line.', 'vtwpr'); 
  $html .= '</p>';  
  
	echo $html;
}


function vtwpr_show_cartWidget_discount_details_grouped_by_what_callback() {    //opt22                                 
	$options = get_option( 'vtwpr_setup_options' );	
  $html = '<select id="show_cartWidget_discount_details_grouped_by_what" name="vtwpr_setup_options[show_cartWidget_discount_details_grouped_by_what]">';
	$html .= '<option value="rule"'             .  selected( $options['show_cartWidget_discount_details_grouped_by_what'], 'rule', false)    . '>'   . __('Grouped by Rule within Product ', 'vtwpr') .   '&nbsp;</option>';
  $html .= '<option value="product"'          .  selected( $options['show_cartWidget_discount_details_grouped_by_what'], 'product', false) . '>'   . __('Grouped by Product ', 'vtwpr') .  '</option>';
  $html .= '</select>';

  $html .= '<a id="help22" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
  $html .= '<p id="help22-text" class = "help-text" >'; 
  $html .= __('If Cart Widget Discount detail lines are to be displayed, how are they organized?  By Rule, there will be a separate line by Rule for each product which got a discount based on that rule.
              You can elect to show the relevant Rule short cart message in a line above each detail line.', 'vtwpr') 
              .'<br><br>'.
          __('By product totals up all discounts accrued to that product, and produces a single detail line.', 'vtwpr'); 
  $html .= '</p>';  
  
	echo $html;
}

function vtwpr_show_checkout_discount_titles_above_details_callback () {    //opt23
	$options = get_option( 'vtwpr_setup_options' );	
	$html = '<select id="show_checkout_discount_titles_above_details" name="vtwpr_setup_options[show_checkout_discount_titles_above_details]">';
	$html .= '<option value="yes"' . selected( $options['show_checkout_discount_titles_above_details'], 'yes', false) . '>'   . __('Yes', 'vtwpr') . '&nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['show_checkout_discount_titles_above_details'], 'no', false)  . '>'   . __('No', 'vtwpr') . '</option>';
	$html .= '</select>';
	
  $html .= '<a id="help23" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
  
  $html .= '<p id="help23-text" class = "help-text" >'; 
  $html .= __('When discount details display, do we show the Short Checkout Message above Rule Product Discount detail line?  Only applicable if Checkout "Grouped by Rule" chosen above.', 'vtwpr'); 
  $html .= '</p>'; 
  
	echo $html;
} 

function vtwpr_show_cartWidget_discount_titles_above_details_callback () {    //opt7
	$options = get_option( 'vtwpr_setup_options' );	
	$html = '<select id="show_cartWidget_discount_titles_above_details" name="vtwpr_setup_options[show_cartWidget_discount_titles_above_details]">';
	$html .= '<option value="yes"' . selected( $options['show_cartWidget_discount_titles_above_details'], 'yes', false) . '>'   . __('Yes', 'vtwpr')  .   '&nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['show_cartWidget_discount_titles_above_details'], 'no', false)  . '>'   . __('No', 'vtwpr') . '</option>';
	$html .= '</select>';
	
  $html .= '<a id="help7" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
  
  $html .= '<p id="help7-text" class = "help-text" >'; 
  $html .= __('When discount details display, do we show the Short Checkout Message above Rule Product Discount detail line? Only applicable if Cart Widget "Grouped by Rule" chosen above.', 'vtwpr'); 
  $html .= '</p>'; 
  
	echo $html;
}   

function vtwpr_show_checkout_purchases_subtotal_callback () {    //opt24
	$options = get_option( 'vtwpr_setup_options' );	
	$html = '<select id="show_checkout_purchases_subtotal" name="vtwpr_setup_options[show_checkout_purchases_subtotal]">';
  $html .= '<option value="withDiscounts"'  . selected( $options['show_checkout_purchases_subtotal'], 'withDiscounts', false)    . '>'   . __('Yes - Show ', 'vtwpr') .  '&nbsp;'   . __('After Discounts', 'vtwpr') .  '&nbsp;</option>';  
  $html .= '<option value="beforeDiscounts"' . selected( $options['show_checkout_purchases_subtotal'], 'beforeDiscounts', false) . '>'   . __('Yes - Show ', 'vtwpr') .  '&nbsp;'   . __('Before Discounts ', 'vtwpr') .  '&nbsp;</option>';
	$html .= '<option value="none"'  . selected( $options['show_checkout_purchases_subtotal'], 'none', false)  . '>'   . __('No - No New Subtotal Line ', 'vtwpr') .  '&nbsp;'   . __('for Cart Purchases ', 'vtwpr') . '</option>';
	$html .= '</select>';

  $html .= '<a id="help24" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
  
  $html .= '<p id="help24-text" class = "help-text" >'; 
  $html .= __('Do we show the purchases subtotal before discounts, with discounts or not at all?', 'vtwpr'); 
  $html .= '</p>'; 
  
	echo $html;
} 

function vtwpr_show_cartWidget_purchases_subtotal_callback () {    //opt25
	$options = get_option( 'vtwpr_setup_options' );	
	$html = '<select id="show_cartWidget_purchases_subtotal" name="vtwpr_setup_options[show_cartWidget_purchases_subtotal]">';
	$html .= '<option value="withDiscounts"'  . selected( $options['show_cartWidget_purchases_subtotal'], 'withDiscounts', false)    . '>'   . __('Yes - Show ', 'vtwpr') .  '&nbsp;'   . __('After Discounts ', 'vtwpr') .  '&nbsp;</option>';  
  $html .= '<option value="beforeDiscounts"' . selected( $options['show_cartWidget_purchases_subtotal'], 'beforeDiscounts', false) . '>'   . __('Yes - Show ', 'vtwpr') .  '&nbsp;'   . __('Before Discounts ', 'vtwpr') .  '&nbsp;</option>';
	$html .= '<option value="none"'  . selected( $options['show_cartWidget_purchases_subtotal'], 'none', false) . '>'   . __('No - No New Subtotal Line ', 'vtwpr') .  '&nbsp;'   . __('for Cart Purchases ', 'vtwpr') . '</option>';
	$html .= '</select>';

  $html .= '<a id="help25" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
  
  $html .= '<p id="help25-text" class = "help-text" >'; 
  $html .= __('Do we show the purchases subtotal before discounts, with discounts or not at all? (Default = no)', 'vtwpr'); 
  $html .= '</p>'; 
  
	echo $html;
} 

function vtwpr_show_checkout_discount_total_line_callback () {    //opt5
	$options = get_option( 'vtwpr_setup_options' );	
	$html = '<select id="show_checkout_discount_total_line" name="vtwpr_setup_options[show_checkout_discount_total_line]">';
	$html .= '<option value="yes"' . selected( $options['show_checkout_discount_total_line'], 'yes', false) . '>'   . __('Yes', 'vtwpr') .  '&nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['show_checkout_discount_total_line'], 'no', false) . '>'   . __('No', 'vtwpr') . '</option>';
	$html .= '</select>';

  $html .= '<a id="help5" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
  
  $html .= '<p id="help5-text" class = "help-text" >'; 
  $html .= __('When Checkout Discounts are taken, do we show a separate discount totals line?', 'vtwpr'); 
  $html .= '</p>'; 
  
	echo $html;
} 


function vtwpr_show_cartWidget_discount_total_line_callback () {    //opt26
	$options = get_option( 'vtwpr_setup_options' );	
	$html = '<select id="show_cartWidget_discount_total_line" name="vtwpr_setup_options[show_cartWidget_discount_total_line]">';
	$html .= '<option value="yes"' . selected( $options['show_cartWidget_discount_total_line'], 'yes', false) . '>'   . __('Yes', 'vtwpr') .  '&nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['show_cartWidget_discount_total_line'], 'no', false) . '>'   . __('No', 'vtwpr') . '</option>';
	$html .= '</select>';

  $html .= '<a id="help26" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
  
  $html .= '<p id="help26-text" class = "help-text" >'; 
  $html .= __('When Cart Widget Discounts are taken, do we show a separate discount totals line?', 'vtwpr'); 
  $html .= '</p>'; 
  
	echo $html;
} 


function vtwpr_show_checkout_discount_detail_lines_callback () {    //opt6
	$options = get_option( 'vtwpr_setup_options' );	
	$html = '<select id="show_checkout_discount_detail_lines" name="vtwpr_setup_options[show_checkout_discount_detail_lines]">';
	$html .= '<option value="yes"' . selected( $options['show_checkout_discount_detail_lines'], 'yes', false) . '>'   . __('Yes', 'vtwpr') .  '&nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['show_checkout_discount_detail_lines'], 'no', false)  . '>'   . __('No', 'vtwpr') . '</option>';
	$html .= '</select>';

  $html .= '<a id="help6" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
  
  $html .= '<p id="help6-text" class = "help-text" >'; 
  $html .= __('Do we show Checkout discount detail lines, or just show the discount grand total?', 'vtwpr'); 
  $html .= '</p>'; 
  
	echo $html;
} 


function vtwpr_show_cartWidget_discount_detail_lines_callback () {    //opt27
	$options = get_option( 'vtwpr_setup_options' );	
	$html = '<select id="show_cartWidget_discount_detail_lines" name="vtwpr_setup_options[show_cartWidget_discount_detail_lines]">';
	$html .= '<option value="yes"' . selected( $options['show_cartWidget_discount_detail_lines'], 'yes', false) . '>'   . __('Yes', 'vtwpr') .  '&nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['show_cartWidget_discount_detail_lines'], 'no', false) . '>'    . __('No', 'vtwpr') . '</option>';
	$html .= '</select>';

  $html .= '<a id="help27" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
  
  $html .= '<p id="help27-text" class = "help-text" >'; 
  $html .= __('Do we show cartWidget discount detail lines?', 'vtwpr'); 
  $html .= '</p>'; 
  
	echo $html;
}
 

function vtwpr_debugging_mode_callback () {    //opt8
	$options = get_option( 'vtwpr_setup_options' );	
	$html = '<select id="debugging_mode_on" name="vtwpr_setup_options[debugging_mode_on]">';
	$html .= '<option value="yes"' . selected( $options['debugging_mode_on'], 'yes', false) . '>'   . __('Yes', 'vtwpr') .  '&nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['debugging_mode_on'], 'no', false) . '>'    . __('No', 'vtwpr') . '</option>';
	$html .= '</select>';

  $html .= '<a id="help8" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
  
  $html .= '<p id="help8-text" class = "help-text" >'; 
  $html .= __('"Test Debugging Mode Turned On" => 
  Set this to "yes" if you want to see the full rule structures which produce any error messages. **ONLY** should be used during testing.', 'vtwpr'); 
  $html .= '</p>';  
  
	echo $html;
}

function vtwpr_custom_checkout_css_callback() {    //opt9
  $options = get_option( 'vtwpr_setup_options' );
  $html = '<textarea type="text" id="custom_checkout_css"  rows="200" cols="40" name="vtwpr_setup_options[custom_checkout_css]">' . $options['custom_checkout_css'] . '</textarea>';

  $html .= '<a id="help9" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
   
  $html .= '<p id="help9-text" class = "help-text" >'; 
  $html .= __('"Custom Error Message CSS at Checkout Time" => 
          The CSS used for maximum amount error messages is supplied.  If you want to override any of the css, supply just your overrides here. ', 'vtwpr')
          .'<br>'. 
          __('For Example => div.vtwpr-error .red-font-italic {color: green;}', 'vtwpr'); 
  $html .= '</p>';  
  
	echo $html;
}


function vtwpr_use_plugin_front_end_css_callback() {    //opt19                                               Use the Plugin CSS file for Discount Display
  $options = get_option( 'vtwpr_setup_options' );
	$html = '<select id="use_plugin_front_end_css" name="vtwpr_setup_options[use_plugin_front_end_css]">';
	$html .= '<option value="yes"' . selected( $options['use_plugin_front_end_css'], 'yes', false) . '>'   . __('Yes - Use the Plugin CSS file ', 'vtwpr') .  '&nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['use_plugin_front_end_css'], 'no', false) . '>'    . __('No - Don"t use the Plugin CSS file ', 'vtwpr') . '</option>';
	$html .= '</select>';

  $html .= '<a id="help19" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
  $html .= '<br><br><em>&nbsp;&nbsp;';
  $html .= __('(Shutting off the Plugin CSS file allows you to create your own custom CSS and place it in your theme CSS file directly.)', 'vtwpr');
  $html .=  '</em>';
     
  $html .= '<p id="help19-text" class = "help-text" >'; 
  $html .= __('An alternative to supplying custom override CSS in the options here, is to shut off the plugin front end
              CSS entirely.  This would allow you to supply all the CSS relevant to this plugin yourself,
              altered to suit, in your Theme.', 'vtwpr'); 
  $html .= '</p><br><br>';  
  
	echo $html;
}

function vtwpr_checkout_credit_detail_label_callback() {    //opt10
	$options = get_option( 'vtwpr_setup_options' );	
  $html = '<input type="text" class="smallText" id="checkout_credit_detail_label" name="vtwpr_setup_options[checkout_credit_detail_label]" value="' . $options['checkout_credit_detail_label'] . '">';
  $html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="help10" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
  $html .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
  $html .= '<i>'. __( '(suggested: "-" (minus sign) or "cr" (credit) )', 'vtwpr' ) .'</i>';
  
  $html .= '<p id="help10-text" class = "help-text" >'; 
  $html .= __('When showing a checkout credit detail line, this is a label which is just to the left of the currency sign, indicating that this is a credit.', 'vtwpr'); 
  $html .= '</p><br><br>';
  	
	echo $html;
}

function vtwpr_checkout_credit_total_label_callback() {    //opt11
	$options = get_option( 'vtwpr_setup_options' );	
  $html = '<input type="text" class="smallText" id="checkout_credit_total_label"  name="vtwpr_setup_options[checkout_credit_total_label]" value="' . $options['checkout_credit_total_label'] . '">';

  $html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="help11" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
  $html .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
  $html .= '<i>'. __( '(suggested: "-" (minus sign) or "cr" (credit) )', 'vtwpr' ) .'</i>';
    
  $html .= '<p id="help11-text" class = "help-text" >'; 
  $html .= __('When showing a checkout credit total line, this is a label which is just to the left of the currency sign, indicating that this is a credit.', 'vtwpr'); 
  $html .= '</p><br><br>';
  	
	echo $html;
}


function vtwpr_cartWidget_credit_detail_label_callback() {    //opt28
	$options = get_option( 'vtwpr_setup_options' );	
  $html = '<input type="text" class="smallText" id="cartWidget_credit_detail_label" name="vtwpr_setup_options[cartWidget_credit_detail_label]" value="' . $options['cartWidget_credit_detail_label'] . '">';
  $html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="help28" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
  $html .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
  $html .= '<i>'. __( '(suggested: "-" (minus sign) or "cr" (credit) )', 'vtwpr' ) .'</i>';
    
  $html .= '<p id="help28-text" class = "help-text" >'; 
  $html .= __('When showing a cartWidget credit detail line, this is a label which is just to the left of the currency sign, indicating that this is a credit.', 'vtwpr'); 
  $html .= '</p><br><br>';
  	
	echo $html;
}

function vtwpr_cartWidget_credit_total_label_callback() {    //opt29
	$options = get_option( 'vtwpr_setup_options' );	
  $html = '<input type="text" class="smallText" id="cartWidget_credit_total_label"  name="vtwpr_setup_options[cartWidget_credit_total_label]" value="' . $options['cartWidget_credit_total_label'] . '">';

  $html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="help29" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
  $html .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
  $html .= '<i>'. __( '(suggested: "-" (minus sign) or "cr" (credit) )', 'vtwpr' ) .'</i>';
  
  $html .= '<p id="help29-text" class = "help-text" >'; 
  $html .= __('When showing a cartWidget credit total line, this is a label which is just to the left of the currency sign, indicating that this is a credit.', 'vtwpr'); 
  $html .= '</p><br><br>';
  	
	echo $html;
}
function vtwpr_checkout_credit_subtotal_title_callback() {    //opt30
	$options = get_option( 'vtwpr_setup_options' );	
  $html = '<input type="text" class="largeText" id="checkout_credit_detail_label" name="vtwpr_setup_options[checkout_credit_subtotal_title]" value="' . $options['checkout_credit_subtotal_title'] . '">';
  $html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="help30" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
  
  $html .= '<p id="help30-text" class = "help-text" >'; 
  $html .= __('When showing a checkout credit detail line, this is title.', 'vtwpr')  
          .'<br><br>'.
          __('Default value = "Subtotal - Cart Purchases:".', 'vtwpr'); 
  $html .= '</p>';   
    	
	echo $html;
}

function vtwpr_checkout_credit_total_title_callback() {    //opt31
	$options = get_option( 'vtwpr_setup_options' );	
  $html = '<input type="text" class="largeText" id="checkout_credit_total_title"  name="vtwpr_setup_options[checkout_credit_total_title]" value="' . $options['checkout_credit_total_title'] . '">';

  $html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="help31" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
  
  $html .= '<p id="help31-text" class = "help-text" >'; 
  $html .= __('When showing a checkout credit total line, this is a title.', 'vtwpr') 
        .'<br><br>'.
        __('Default value = "Cart Discount Total:".', 'vtwpr'); 
  $html .= '</p>';  
     	
	echo $html;
}

/*
function vtwpr_show_checkout_credit_total_when_coupon_active_callback() {    //opt45
  $options = get_option( 'vtwpr_setup_options' );
	$html = '<select id="show_checkout_credit_total_when_coupon_active" name="vtwpr_setup_options[show_checkout_credit_total_when_coupon_active]">';
	$html .= '<option value="yes"' . selected( $options['show_checkout_credit_total_when_coupon_active'], 'yes', false) . '>'   . __('Yes', 'vtwpr') .  '&nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['show_checkout_credit_total_when_coupon_active'], 'no', false) . '>'    . __('No', 'vtwpr') . '</option>';
	$html .= '</select>';

  $html .= '<a id="help45" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';

  $html .= '<p id="help45-text" class = "help-text" >'; 
  $html .= __('At checkout, some themes already show the discount total when a coupon is present.  This switch allows you to turn off this plugin"s credit total line.', 'vtwpr'); 
  $html .= '</p>';  
  
	echo $html;
}
*/
function vtwpr_checkout_new_subtotal_line_callback() {    //opt43
  $options = get_option( 'vtwpr_setup_options' );
	$html = '<select id="checkout_new_subtotal_line" name="vtwpr_setup_options[checkout_new_subtotal_line]">';
	$html .= '<option value="yes"' . selected( $options['checkout_new_subtotal_line'], 'yes', false) . '>'   . __('Yes', 'vtwpr') .  '&nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['checkout_new_subtotal_line'], 'no', false) . '>'    . __('No', 'vtwpr') . '</option>';
	$html .= '</select>';

  $html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="help43" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
    
  $html .= '<p id="help43-text" class = "help-text" >'; 
  $html .= __('(If you want a new subtotal line to show after the Purchased Products and Discounts have been totaled, and your theme does not already do so...)', 'vtwpr'); 
  $html .= '</p>';
  
	echo $html;
}


function vtwpr_checkout_new_subtotal_label_callback() {    //opt44
	$options = get_option( 'vtwpr_setup_options' );	
  $html = '<input type="text" class="largeText" id="checkout_new_subtotal_label" name="vtwpr_setup_options[checkout_new_subtotal_label]" value="' . $options['checkout_new_subtotal_label'] . '">';
  $html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="help44" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
  
  $html .= '<p id="help44-text" class = "help-text" >'; 
  $html .= __('If you want a new subtotal line to show after the Purchased Products and Discounts have been totaled, and your theme does not already do so, this is the label to use.', 'vtwpr')  
           .'<br><br>'.
           __('Default value = "Subtotal with Discount"', 'vtwpr'); 
  $html .= '</p>';
  	
	echo $html;
}

function vtwpr_cartWidget_new_subtotal_line_callback() {    //opt45
  $options = get_option( 'vtwpr_setup_options' );
	$html = '<select id="cartWidget_new_subtotal_line" name="vtwpr_setup_options[cartWidget_new_subtotal_line]">';
	$html .= '<option value="yes"' . selected( $options['cartWidget_new_subtotal_line'], 'yes', false) . '>'   . __('Yes', 'vtwpr') .  '&nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['cartWidget_new_subtotal_line'], 'no', false) . '>'    . __('No', 'vtwpr') . '</option>';
	$html .= '</select>';

  $html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="help45" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
    
  $html .= '<p id="help45-text" class = "help-text" >'; 
  $html .= __('(If you want a new subtotal line to show after the Purchased Products and Discounts have been totaled, and your theme does not already do so...)', 'vtwpr'); 
  $html .= '</p>';
  
	echo $html;
}


function vtwpr_cartWidget_new_subtotal_label_callback() {    //opt46
	$options = get_option( 'vtwpr_setup_options' );	
  $html = '<input type="text" class="largeText" id="cartWidget_new_subtotal_label" name="vtwpr_setup_options[cartWidget_new_subtotal_label]" value="' . $options['cartWidget_new_subtotal_label'] . '">';
  $html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="help46" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
  
  $html .= '<p id="help46-text" class = "help-text" >'; 
  $html .= __('If you want a new subtotal line to show after the Purchased Products and Discounts have been totaled, and your theme does not already do so, this is the label to use.', 'vtwpr')  
           .'<br><br>'.
           __('Default value = "Subtotal with Discount"', 'vtwpr'); 
  $html .= '</p>';
  	
	echo $html;
}




function vtwpr_cartWidget_credit_subtotal_title_callback() {    //opt32
	$options = get_option( 'vtwpr_setup_options' );	
  $html = '<input type="text" class="mediumText" id="cartWidget_credit_detail_label" name="vtwpr_setup_options[cartWidget_credit_subtotal_title]" value="' . $options['cartWidget_credit_subtotal_title'] . '">';
  $html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="help32" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
  
  $html .= '<p id="help32-text" class = "help-text" >'; 
  $html .= __('When showing a cartWidget credit detail line, this is title.', 'vtwpr') 
          .'<br><br>'.
          __('Default value = "Products:".', 'vtwpr'); 
  $html .= '</p>';
  	
	echo $html;
}

function vtwpr_cartWidget_credit_total_title_callback() {    //opt33
	$options = get_option( 'vtwpr_setup_options' );	
  $html = '<input type="text" class="mediumText" id="cartWidget_credit_total_title"  name="vtwpr_setup_options[cartWidget_credit_total_title]" value="' . $options['cartWidget_credit_total_title'] . '">';

  $html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="help33" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
  
  $html .= '<p id="help33-text" class = "help-text" >'; 
  $html .= __('When showing a cartWidget credit total line, this is a title.', 'vtwpr') 
          .'<br><br>'.
          __('Default value = "Discounts:".', 'vtwpr'); 
  $html .= '</p>';
  	
	echo $html;
}
/*
function vtwpr_cartWidget_html_colspan_value_callback() {    //opt12
	$options = get_option( 'vtwpr_setup_options' );	
  $html = '<input type="text" class="smallText" id="cartWidget_html_colspan_value"  name="vtwpr_setup_options[cartWidget_html_colspan_value]" value="' . $options['cartWidget_html_colspan_value'] . '">';

  $html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="help12" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
  
  $html .= '<p id="help12-text" class = "help-text" >'; 
  $html .= __('Controls the overall width of the Cart Widget Discount display lines.   Test extensively before releasing any changes into the wild.  Pericoloso.', 'vtwpr')  
          .'<br><br>'.
          __('Default value = 5', 'vtwpr'); 
  $html .= '</p>';
  	
	echo $html;
}
*/
function vtwpr_lifetime_limit_by_ip_callback () {   //opt13
	$options = get_option( 'vtwpr_setup_options' );	
	$html = '<select id="vtwpr-lifetime-limit-by-ip" name="vtwpr_setup_options[max_purch_rule_lifetime_limit_by_ip]">';
	$html .= '<option value="yes"' . selected( $options['max_purch_rule_lifetime_limit_by_ip'], 'yes', false) . '>'   . __('Yes', 'vtwpr') .  '&nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['max_purch_rule_lifetime_limit_by_ip'], 'no', false)  . '>'   . __('No', 'vtwpr') . '</option>';
	$html .= '</select>';

  $html .= '<a id="help13" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';

  $html .= '<p id="help13-text" class = "help-text" >'; 
  $html .= __('"Check Customer against Rule Purchase History, by IP" => When using Customer Rule Limits, use IP to identify the customer.  Immediately avalable at Add to Cart time.', 'vtwpr'); 
  $html .= '</p>';  
  $html .= '<p><em>&nbsp;&nbsp;';
  $html .= __('This switch should always be set to "Yes" - check by ip is done at shortcode time, add to cart time and checkout time.', 'vtwpr');
  $html .=  '</em></p>';  
  //Heading for the next section, with description      
  $html .= '<p id="lifetime-by-other-switches-intro" class="extra-intro">'; 
  $html .= __('<span>The remainder of the Customer Rule Limit checks by user-supplied info, only happen when the User clicks the "Pay" button at Checkout Time .', 'vtwpr') 
        .'<br>&nbsp;&nbsp;&nbsp;'.
        __(' - The Customer Rule Limit checks are applied, and if the discount amounts have to be reduced, the User is returned to Checkout.', 'vtwpr')
        .'<br>&nbsp;&nbsp;&nbsp;'.
        __(' - An error message can be displayed, highlighting the discount and total changes.  When the User accepts the discount reduction
        and hits the "Pay" button again, the transaction is then processed.</span>', 'vtwpr'); 
  $html .= '</p>';
    
	echo $html;
}
  
function vtwpr_lifetime_limit_by_email_callback () {   //opt14
	$options = get_option( 'vtwpr_setup_options' );	
	$html = '<select id="vtwpr-lifetime-limit-by-email" name="vtwpr_setup_options[max_purch_rule_lifetime_limit_by_email]">';	
	$html .= '<option value="no"'  . selected( $options['max_purch_rule_lifetime_limit_by_email'], 'no', false)  . '>'   . __('No', 'vtwpr') . '</option>';
  $html .= '<option value="yes"' . selected( $options['max_purch_rule_lifetime_limit_by_email'], 'yes', false) . '>'   . __('Yes', 'vtwpr') .  '&nbsp;</option>';  
	$html .= '</select>';

  $html .= '<a id="help14" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';

  $html .= '<p id="help14-text" class = "help-text" >'; 
  $html .= __('"Check Customer against Rule Purchase History, by Email" => When using Customer Rule Limits, use email to identify the customer.', 'vtwpr'); 
  $html .= '</p>';  
	echo $html;
}
  
function vtwpr_lifetime_limit_by_billto_name_callback () {   //opt15
  $options = get_option( 'vtwpr_setup_options' );	
	$html = '<select id="vtwpr-lifetime-limit-by-billto-name" name="vtwpr_setup_options[max_purch_rule_lifetime_limit_by_billto_name]">';	
	$html .= '<option value="no"'  . selected( $options['max_purch_rule_lifetime_limit_by_billto_name'], 'no', false) . '>'   . __('No', 'vtwpr') . '</option>';
  $html .= '<option value="yes"' . selected( $options['max_purch_rule_lifetime_limit_by_billto_name'], 'yes', false) . '>'   . __('Yes', 'vtwpr') .  '&nbsp;</option>';  
	$html .= '</select>';

  $html .= '<a id="help15" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';

  $html .= '<p id="help15-text" class = "help-text" >'; 
  $html .= __('"Check Customer against Rule Purchase History, by Billto Name" => When using Customer Rule Limits, use billto name to identify the customer.', 'vtwpr'); 
  $html .= '</p>'; 
	echo $html;
}  

  
function vtwpr_lifetime_limit_by_billto_addr_callback () {   //opt16
	$options = get_option( 'vtwpr_setup_options' );	
	$html = '<select id="vtwpr-lifetime-limit-by-billto-addr" name="vtwpr_setup_options[max_purch_rule_lifetime_limit_by_billto_addr]">';
	$html .= '<option value="no"'  . selected( $options['max_purch_rule_lifetime_limit_by_billto_addr'], 'no', false)  . '>'   . __('No', 'vtwpr') . '</option>';
  $html .= '<option value="yes"' . selected( $options['max_purch_rule_lifetime_limit_by_billto_addr'], 'yes', false) . '>'   . __('Yes', 'vtwpr') .  '&nbsp;</option>';	
	$html .= '</select>';

  $html .= '<a id="help16" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';

  $html .= '<p id="help16-text" class = "help-text" >'; 
  $html .= __('"Check Customer against Rule Purchase History, by Billto addr" => When using Customer Rule Limits, use billto addr to identify the customer.', 'vtwpr'); 
  $html .= '</p>';  
	echo $html;
}
  
function vtwpr_lifetime_limit_by_shipto_name_callback () {   //opt17
	$options = get_option( 'vtwpr_setup_options' );	
	$html = '<select id="vtwpr-lifetime-limit-by-shipto-name" name="vtwpr_setup_options[max_purch_rule_lifetime_limit_by_shipto_name]">';
	$html .= '<option value="no"'  . selected( $options['max_purch_rule_lifetime_limit_by_shipto_name'], 'no', false)  . '>'   . __('No', 'vtwpr') . '</option>';
  $html .= '<option value="yes"' . selected( $options['max_purch_rule_lifetime_limit_by_shipto_name'], 'yes', false) . '>'   . __('Yes', 'vtwpr') .  '&nbsp;</option>';	
	$html .= '</select>';

  $html .= '<a id="help17" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';

  $html .= '<p id="help17-text" class = "help-text" >'; 
  $html .= __('"Check Customer against Rule Purchase History, by Shipto Name" => When using Customer Rule Limits, use shipto name to identify the customer.', 'vtwpr'); 
  $html .= '</p>';  
	echo $html;
}
  
function vtwpr_lifetime_limit_by_shipto_addr_callback () {   //opt18
	$options = get_option( 'vtwpr_setup_options' );	
	$html = '<select id="vtwpr-lifetime-limit-by-shipto-addr" name="vtwpr_setup_options[max_purch_rule_lifetime_limit_by_shipto_addr]">';
	$html .= '<option value="no"'  . selected( $options['max_purch_rule_lifetime_limit_by_shipto_addr'], 'no', false)  . '>'   . __('No', 'vtwpr') . '</option>';
  $html .= '<option value="yes"' . selected( $options['max_purch_rule_lifetime_limit_by_shipto_addr'], 'yes', false) . '>'   . __('Yes', 'vtwpr') .  '&nbsp;</option>';	
	$html .= '</select>';

  $html .= '<a id="help18" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';

  $html .= '<p id="help18-text" class = "help-text" >'; 
  $html .= __('"Check Customer against Rule Purchase History, by Shipto addr" => When using Customer Rule Limits, use shipto addr to identify the customer.', 'vtwpr'); 
  $html .= '</p>';  
	echo $html;
}
/*  
function vtwpr_checkout_forms_set_callback () {   //opt38 
  $options = get_option( 'vtwpr_setup_options' );
  $html = '<textarea type="text" id="max_purch_checkout_forms_set"  rows="1" cols="20" name="vtwpr_setup_options[max_purch_checkout_forms_set]">' . $options['max_purch_checkout_forms_set'] . '</textarea>';
  $html .= '<a id="help38" class="help-anchor" href="javascript:void(0);" >' .  __('More Info', 'vtwpr') . '</a>';

  $html .= '<p id="help38-text" class = "help-text" >'; 
  $html .= __('"Default checkout formset containing "billingemail" etc, is formset "0".  Should you wish to create a custom formset to administer the basic addressing of "billingemail" etc,
  it must duplicate all the internals of the default formset (name column can contain any value, though).', 'vtwpr'); 
  $html .= '</p>'; 
  
  //Heading for the next section, with description      
  $html .= '<p id="lifetime-error-msg-intro" class="extra-intro">'; 
  $html .= '<strong>'. __('Lifetime Rule Checkout Button Error Options (See => )', 'vtwpr') .'</strong>'; 
  $html .= '<a id="help41a" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';  
  $html .= '</p>'; 
   
  $html .= '<p id="help41a-text" class = "help-text" >'; 
  $html .= __('"Customer Rule Limit -  Checkout Button Error Options" => Customer Rule Limits can be based on IP, which is available at all times.  However, email address, shipto and soldto address
        are best verified at Payment Button click time.  The system rechecks any Customer Rule Limits, and if historical purchases are found, the discount amount is reduced in combination with with the purchase history
        which has now been found.  In order to alert the customer to the change in the discount amount, the screen returns to the Checkout screen, with this fields error message displayed.
        The default error message informs the user why the discount has been reduced, and invites the purchaser to accept the reduced discount, and click on the Payment button a second time, to carry on
        to the payment gateway.', 'vtwpr')
        .'<br><br>'.
        __('Default = "' .VTWPR_CHECKOUT_BUTTON_ERROR_MSG_DEFAULT  . '".', 'vtwpr'); 
  $html .= '</p>';    
 
	echo $html;
}
*/
function vtwpr_before_checkout_products_selector_callback() {    //opt39
  $options = get_option( 'vtwpr_setup_options' );
  $html = '<textarea type="text" id="show_error_before_checkout_products_selector"  rows="1" cols="20" name="vtwpr_setup_options[show_error_before_checkout_products_selector]">' . $options['show_error_before_checkout_products_selector'] . '</textarea>';

  $html .= '<a id="help39" class="help-anchor" href="javascript:void(0);" >' . __('More Info', 'vtwpr') . '</a>';
   
  $html .= '<p id="help39-text" class = "help-text" >'; 
  $html .= __('"Show Error Messages Just Before Checkout Products List - HTML Selector" => 
        This option controls the location of the message display.', 'vtwpr') 
        .'<br><br>'. __('Blank = do not use. ', 'vtwpr')
        .'<br><br>'. __('Default = "', 'vtwpr') .VTWPR_CHECKOUT_PRODUCTS_SELECTOR_BY_PARENT . '".'   
        .'<br><br>'. __('If you"ve changed this value and can"t get it to work, you can use the "reset to defaults" button (just below the "save changes" button) to get the value back (snapshot your other settings first to help you quickly set the other settings back the way to what you had before.)', 'vtwpr'); 
  $html .= '</p>';    
  $html .= '<br><em>&nbsp;&nbsp;';
  $html .= __('Blank = do not display error message before Checkout Products List', 'vtwpr');
  $html .=  '</em><br><br>';  
  
	echo $html;
}

function vtwpr_before_checkout_address_selector_callback() {    //opt40
  $options = get_option( 'vtwpr_setup_options' );
  $html = '<textarea type="text" id="show_error_before_checkout_address_selector"  rows="1" cols="20" name="vtwpr_setup_options[show_error_before_checkout_address_selector]">' . $options['show_error_before_checkout_address_selector'] . '</textarea>';

  $html .= '<a id="help40" class="help-anchor" href="javascript:void(0);" >' . __('More Info', 'vtwpr') . '</a>';
   
  $html .= '<p id="help40-text" class = "help-text" >'; 
  $html .= __('"Show Error Messages Just Before Checkout Address  List - HTML Selector" => 
        This option controls the location of the message display.', 'vtwpr') 
        .'<br><br>'. __('Blank = do not use. ', 'vtwpr')
        .'<br><br>'. __('Default = "', 'vtwpr') .VTWPR_CHECKOUT_ADDRESS_SELECTOR_BY_PARENT . '".'   
        .'<br><br>'. __('If you"ve changed this value and can"t get it to work, you can use the "reset to defaults" button (just below the "save changes" button) to get the value back (snapshot your other settings first to help you quickly set the other settings back the way to what you had before.)', 'vtwpr');   
  $html .= '</p>';   
  $html .= '<br><em>&nbsp;&nbsp;';
  $html .= __('Blank = do not display error message before Checkout Address List', 'vtwpr');
  $html .=  '</em><br><br>';
      
	echo $html;
}


function vtwpr_lifetime_purchase_button_error_msg_callback() {    //opt41
  $options = get_option( 'vtwpr_setup_options' );
  
  //REMOVE any line breaks, etc, which would cause a JS error !! 
  $tempMsg =    str_replace(array("\r\n", "\r", "\n", "\t"), ' ', $options['lifetime_purchase_button_error_msg']);
  $options['lifetime_purchase_button_error_msg'] = $tempMsg;
 
  $html = '<textarea type="text" id="lifetime_purchase_button_error_msg"  rows="200" cols="40" name="vtwpr_setup_options[lifetime_purchase_button_error_msg]">' . $options['lifetime_purchase_button_error_msg'] . '</textarea>';

  $html .= '<a id="help41" class="help-anchor" href="javascript:void(0);" >'   . __('More Info', 'vtwpr') .  '</a>';
   
  $html .= '<p id="help41-text" class = "help-text" >'; 
  $html .= __('"Customer Rule Limit - Payment Button Error Message" => Customer Rule Limits can be based on IP, which is available at all times.  However, email address, shipto and soldto address
        are best verified at Payment Button click time.  The system rechecks any Customer Rule Limit rules, and if historical purchases are found, the discount amount is reduced in combination with the purchase history
        which has now been found.  In order to alert the customer to the change in the discount amount, the screen returns to the Checkout screen, with this fields error message displayed.
        The default error message informs the user why the discount has been reduced, and invites the purchaser to accept the reduced discount, and click on the Payment button a second time, to carry on
        to the payment gateway.', 'vtwpr')
        .'<br><br>'.
        __('Default = "' .VTWPR_CHECKOUT_BUTTON_ERROR_MSG_DEFAULT  . '".', 'vtwpr'); 
  $html .= '</p>';  
  
	echo $html;
}

  public function vtwpr_enqueue_setup_scripts($hook_suffix) {
    switch( $hook_suffix) {        //weird but true
      case 'vtwpr-rule_page_vtwpr_setup_options_page':  
      case 'vtwpr-rule_page_vtwpr_show_help_page':  
      case 'vtwpr-rule_page_vtwpr_show_faq_page':              
        wp_register_style('vtwpr-admin-style', VTWPR_URL.'/admin/css/vtwpr-admin-style.css' );  
        wp_enqueue_style ('vtwpr-admin-style');
        wp_register_style('vtwpr-admin-settings-style', VTWPR_URL.'/admin/css/vtwpr-admin-settings-style.css' );  
        wp_enqueue_style ('vtwpr-admin-settings-style');
        wp_register_script('vtwpr-admin-settings-script', VTWPR_URL.'/admin/js/vtwpr-admin-settings-script.js' );  
        wp_enqueue_script ('vtwpr-admin-settings-script');
      break;
    }
  }    


function vtwpr_validate_setup_input( $input ) {

  //did this come from on of the secondary buttons?
  $reset        = ( ! empty($input['options-reset']) ? true : false );
  $repair       = ( ! empty($input['rules-repair']) ? true : false );
  $nuke_rules   = ( ! empty($input['rules-nuke']) ? true : false );
  $nuke_cats    = ( ! empty($input['cats-nuke']) ? true : false );
  $nuke_hist    = ( ! empty($input['hist-nuke']) ? true : false );
  $nuke_log     = ( ! empty($input['log-nuke']) ? true : false );  
  $nuke_session = ( ! empty($input['session-nuke']) ? true : false );
  $nuke_cart    = ( ! empty($input['cart-nuke']) ? true : false );
 
  
  switch( true ) { 
    case $reset        === true :    //reset options
        $output = $this->vtwpr_set_default_options();  //load up the defaults
        //as default options are set, no further action, just return
        return apply_filters( 'vtwpr_validate_setup_input', $output, $input );
      break;
    case $repair       === true :    //repair rules
        $vtwpr_nuke = new vtwpr_Rule_delete;            
        $vtwpr_nuke->vtwpr_repair_all_rules();
        $output = get_option( 'vtwpr_setup_options' ); 
      break;
    case $nuke_rules   === true :
        $vtwpr_nuke = new vtwpr_Rule_delete;            
        $vtwpr_nuke->vtwpr_nuke_all_rules();
        $output = get_option( 'vtwpr_setup_options' );  
      break;
    case $nuke_cats    === true :    
        $vtwpr_nuke = new vtwpr_Rule_delete;            
        $vtwpr_nuke->vtwpr_nuke_all_rule_cats();
        $output = get_option( 'vtwpr_setup_options' );  
      break;
    case $nuke_hist    === true :    
        $vtwpr_nuke = new vtwpr_Rule_delete;            
        $vtwpr_nuke->vtwpr_nuke_lifetime_purchase_history();
        $output = get_option( 'vtwpr_setup_options' );  
      break;
    case $nuke_log    === true :    
        $vtwpr_nuke = new vtwpr_Rule_delete;            
        $vtwpr_nuke->vtwpr_nuke_audit_trail_logs();
        $output = get_option( 'vtwpr_setup_options' );  
      break;      
    case $nuke_session === true :    
        if(!isset($_SESSION)){
          session_start();
          header("Cache-Control: no-cache");
          header("Pragma: no-cache");
        }    
        session_destroy();
        $output = get_option( 'vtwpr_setup_options' );  
      break; 
    case $nuke_cart === true :    
        if(defined('WPSC_VERSION') && (VTWPR_PARENT_PLUGIN_NAME == 'WP E-Commerce') ) {
        	 global $wpsc_cart;	
           $wpsc_cart->empty_cart( false );
        }
        $output = get_option( 'vtwpr_setup_options' );  
      break;
    default:   //standard update button hit...                 
        $output = get_option( 'vtprd_setup_options' ); //v1.0.3  changed from array initialize
      	foreach( $input as $key => $value ) {
      		if( isset( $input[$key] ) ) {
      			$output[$key] = strip_tags( stripslashes( $input[ $key ] ) );	
      		} // end if		
      	} // end foreach        
      break;
  }
   
/*

     //one of these switches must be on
     if ( ($input['use_lifetime_max_limits'] == 'no' ) &&
         (($input['max_purch_rule_lifetime_limit_by_ip'] == 'yes' ) ||
          ($input['max_purch_rule_lifetime_limit_by_email'] == 'yes' ) ||
          ($input['max_purch_rule_lifetime_limit_by_billto_name'] == 'yes' ) ||
          ($input['max_purch_rule_lifetime_limit_by_billto_addr'] == 'yes' ) ||
          ($input['max_purch_rule_lifetime_limit_by_shipto_name'] == 'yes' ) ||
          ($input['max_purch_rule_lifetime_limit_by_shipto_addr'] == 'yes' )) ) {
        $admin_errorMsg = __(' As one of the following switches has been set to "yes": ', 'vtwpr')
           .'<br>'.  __('"Check Customer against Rule Purchase History, by IP" ', 'vtwpr')
           .'<br>'.  __('"Check Customer against Rule Purchase History, by Email" ', 'vtwpr')
           .'<br>'.  __('"Check Customer against Rule Purchase History, by BillTo Name" ', 'vtwpr')
           .'<br>'.  __('"Check Customer against Rule Purchase History, by BillTo Address" ', 'vtwpr')
           .'<br>'.  __('"Check Customer against Rule Purchase History, by ShipTo Name" ', 'vtwpr')
           .'<br>'.  __('"Check Customer against Rule Purchase History, by ShipTo Address" ', 'vtwpr')
           .'<br>'.  __('The "Use Customer Rule Limits" must also be set to "yes" ', 'vtwpr');
        $admin_errorMsgTitle = __('Use Max Customer Rule Limit', 'vtwpr');
        add_settings_error( 'vtwpr Options', $admin_errorMsgTitle , $admin_errorMsg , 'error' );  
     }
   

     //one of these switches must be on
     if ( ($input['use_lifetime_max_limits'] == 'yes' ) &&
         (($input['max_purch_rule_lifetime_limit_by_ip'] == 'no' ) &&
          ($input['max_purch_rule_lifetime_limit_by_email'] == 'no' ) &&
          ($input['max_purch_rule_lifetime_limit_by_billto_name'] == 'no' ) &&
          ($input['max_purch_rule_lifetime_limit_by_billto_addr'] == 'no' ) &&
          ($input['max_purch_rule_lifetime_limit_by_shipto_name'] == 'no' ) &&
          ($input['max_purch_rule_lifetime_limit_by_shipto_addr'] == 'no' )) ) {
        $admin_errorMsg = __(' The "Use Customer Rule Limits" has been set to "yes", but all of the following switches has been set to "no": ', 'vtwpr')
           .'<br>'.  __('"Check Customer against Rule Purchase History, by IP" ', 'vtwpr')
           .'<br>'.  __('"Check Customer against Rule Purchase History, by Email" ', 'vtwpr')
           .'<br>'.  __('"Check Customer against Rule Purchase History, by BillTo Name" ', 'vtwpr')
           .'<br>'.  __('"Check Customer against Rule Purchase History, by BillTo Address" ', 'vtwpr')
           .'<br>'.  __('"Check Customer against Rule Purchase History, by ShipTo Name" ', 'vtwpr')
           .'<br>'.  __('"Check Customer against Rule Purchase History, by ShipTo Address" ', 'vtwpr');
        $admin_errorMsgTitle = __('Use Max Customer Rule Limit', 'vtwpr');
        add_settings_error( 'vtwpr Options', $admin_errorMsgTitle , $admin_errorMsg , 'error' );  
     }
*/  

  $input['discount_floor_pct_per_single_item'] = preg_replace('/[^0-9.]+/', '', $input['discount_floor_pct_per_single_item']); //remove leading/trailing spaces, percent sign, dollar sign
   
//  $tempMsg =    str_replace(array("\r\n", "\r", "\n", "\t"), ' ', $input['lifetime_purchase_button_error_msg']);
//  $input['lifetime_purchase_button_error_msg'] = $tempMsg;

/* 
    //In this situation, this 'id or class Selector' may not be blank, supply wpsc checkout default - must include '.' or '#'
  if ( $input['show_error_before_checkout_products_selector']  <= ' ' ) {
     $input['show_error_before_checkout_products_selector'] = VTWPR_CHECKOUT_PRODUCTS_SELECTOR_BY_PARENT;             
  }
    //In this situation, this 'id or class Selector' may not be blank, supply wpsc checkout default - must include '.' or '#'
  if ( $input['show_error_before_checkout_address_selector']  <= ' ' ) {
     $input['show_error_before_checkout_address_selector'] = VTWPR_CHECKOUT_ADDRESS_SELECTOR_BY_PARENT;             
  }
*/ 
  //NO Object-based code on the apply_filters statement needed or wanted!!!!!!!!!!!!!
  return apply_filters( 'vtwpr_validate_setup_input', $output, $input );                       
} 


} //end class
 $vtwpr_setup_plugin_options = new VTWPR_Setup_Plugin_Options;
  