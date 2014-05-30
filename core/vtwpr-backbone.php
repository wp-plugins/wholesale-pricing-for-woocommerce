<?php

class VTWPR_Backbone{   
	
	public function __construct(){
		  $this->vtwpr_register_post_types();
      $this->vtwpr_add_dummy_rule_category();
   //   add_filter( 'post_row_actions', array(&$this, 'vtwpr_remove_row_actions'), 10, 2 );
	}
  
  public function vtwpr_register_post_types() {
   global $vtwpr_info;
  
  $tax_labels = array(
		'name' => _x( 'Wholesale Pricing Categories', 'taxonomy general name', 'vtwpr' ),
		'singular_name' => _x( 'Wholesale Pricing Category', 'taxonomy singular name', 'vtwpr' ),
		'search_items' => __( 'Search Wholesale Pricing Category', 'vtwpr' ),
		'all_items' => __( 'All Wholesale Pricing Categories', 'vtwpr' ),
		'parent_item' => __( 'Wholesale Pricing Category', 'vtwpr' ),
		'parent_item_colon' => __( 'Wholesale Pricing Category:', 'vtwpr' ),
		'edit_item' => __( 'Edit Wholesale Pricing Category', 'vtwpr' ),
		'update_item' => __( 'Update Wholesale Pricing Category', 'vtwpr' ),
		'add_new_item' => __( 'Add New Wholesale Pricing Category', 'vtwpr' ),
		'new_item_name' => __( 'New Wholesale Pricing Category', 'vtwpr' )
  ); 	

  
  $tax_args = array(
    'hierarchical' => true,
		'labels' => $tax_labels,
		'show_ui' => true,
		'query_var' => false,
		'rewrite' => array( 'slug' => 'vtwpr_rule_category' )
  ) ;            

  $taxonomy_name =  'vtwpr_rule_category';
 
  
   //REGISTER TAXONOMY 
  	register_taxonomy($taxonomy_name, $vtwpr_info['applies_to_post_types'], $tax_args); 

  //this only works after the setup has been updated, and after a refresh...
  global $vtwpr_setup_options;
  $vtwpr_setup_options = get_option( 'vtwpr_setup_options' );
  if ( (isset( $vtwpr_setup_options['register_under_tools_menu'] ))  && 
       ($vtwpr_setup_options['register_under_tools_menu'] == 'yes') ) {       
      $this->vtwpr_register_under_tools_menu();
  } else {
      $this->vtwpr_register_in_main_menu();
  }  
 
//	$role = get_role( 'administrator' );    //v1.0.3 removed conflict
//	$role->add_cap( 'read_vtwpr-rule' );    //v1.0.3 removed conflict
}

  public function vtwpr_add_dummy_rule_category() {
      $category_list = get_terms( 'vtwpr_rule_category', 'hide_empty=0&parent=0' );
    	if ( count( $category_list ) == 0 ) {
    		wp_insert_term( __( 'Wholesale Pricing Category', 'vtwpr' ), 'vtwpr_rule_category', "parent=0" );
      }
  }


  public function vtwpr_register_in_main_menu() {
      $post_labels = array(
				'name' => _x( 'Wholesale Pricing Rules', 'post type name', 'vtwpr' ),
        'singular_name' => _x( 'Wholesale Pricing Rule', 'post type singular name', 'vtwpr' ),
        'add_new' => _x( 'Add New', 'admin menu: add new Wholesale Pricing Rule', 'vtwpr' ),
        'add_new_item' => __('Add New Wholesale Pricing Rule', 'vtwpr' ),
        'edit_item' => __('Edit Wholesale Pricing Rule', 'vtwpr' ),
        'new_item' => __('New Wholesale Pricing Rule', 'vtwpr' ),
        'view_item' => __('View Wholesale Pricing Rule', 'vtwpr' ),
        'search_items' => __('Search Wholesale Pricing Rules', 'vtwpr' ),
        'not_found' =>  __('No Wholesale Pricing Rules found', 'vtwpr' ),
        'not_found_in_trash' => __( 'No Wholesale Pricing Rules found in Trash', 'vtwpr' ),
        'parent_item_colon' => '',
        'menu_name' => __( 'Wholesale Pricing Rules', 'vtwpr' )
			);
    	register_post_type( 'vtwpr-rule', array(
    		  'capability_type' => 'post',
          'hierarchical' => true,
    		  'exclude_from_search' => true,
          'labels' => $post_labels,
    			'public' => true,
    			'show_ui' => true,
         // 'show_in_menu' => true,
          'query_var' => true,
          'rewrite' => false,     
          'supports' => array('title' )	 //remove 'revisions','editor' = no content/revisions boxes 
    		)
    	);
  }

  public function vtwpr_register_under_tools_menu() {
      $post_labels = array(
				'name' => _x( 'Wholesale Pricing Rules', 'post type name', 'vtwpr' ),
        'singular_name' => _x( 'Wholesale Pricing Rule', 'post type singular name', 'vtwpr' ),
        'add_new' => _x( 'Add New', 'vtwpr' ),
        'add_new_item' => __('Add New Wholesale Pricing Rule', 'vtwpr' ),
        'edit' => __('Edit', 'vtwpr' ),
        'edit_item' => __('Edit Wholesale Pricing Rule', 'vtwpr' ),
        'new_item' => __('New Wholesale Pricing Rule', 'vtwpr' ),
        'view_item' => __('View Wholesale Pricing Rule', 'vtwpr' ),
        'search_items' => __('Search Wholesale Pricing Rules', 'vtwpr' ),
        'not_found' =>  __('No Wholesale Pricing Rules found', 'vtwpr' ),
        'not_found_in_trash' => __( 'No Wholesale Pricing Rules found in Trash', 'vtwpr' ),
        'parent_item_colon' => '',
        'menu_name' => __( 'Wholesale Pricing Rules', 'vtwpr' )
			);
    	register_post_type( 'vtwpr-rule', array(
    		  'capability_type' => 'post',
          'hierarchical' => true,
    		  'exclude_from_search' => true,
          'labels' => $post_labels,
    			'public' => true,
    			'show_ui' => true,
	        "show_in_menu" => 'tools.php',
          'query_var' => true,
          'rewrite' => false,     
          'supports' => array('title' )	 //remove 'revisions','editor' = no content/revisions boxes 
    		)
    	);
  }  


function vtwpr_register_settings() {
    register_setting( 'vtwpr_options', 'vtwpr_rules' );
} 



} //end class
$vtwpr_backbone = new VTWPR_Backbone;
  
  
  
  class VTWPR_Functions {   
	
	public function __construct(){

	}
    
  function vtwpr_getSystemMemInfo() 
  {       
      $data = explode("\n", file_get_contents("/proc/meminfo"));
      $meminfo = array();
      foreach ($data as $line) {
          list($key, $val) = explode(":", $line);
          $meminfo[$key] = trim($val);
      }
      return $meminfo;
  }
  
  } //end class