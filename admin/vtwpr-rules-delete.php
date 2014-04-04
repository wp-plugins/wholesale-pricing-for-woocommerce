<?php
class VTWPR_Rule_delete {
	
	public function __construct(){
     
    }
    
  public  function vtwpr_delete_rule () {
    global $post, $vtwpr_info, $vtwpr_rules_set, $vtwpr_rule;
    $post_id = $post->ID;    
    $vtwpr_temp_rules_set = array();
    $vtwpr_rules_set = get_option( 'vtwpr_rules_set' ) ;
    for($i=0; $i < sizeof($vtwpr_rules_set); $i++) { 
       //load up temp_rule_set with every rule *except* the one to be deleted
       if ($vtwpr_rules_set[$i]->post_id != $post_id) {
          $vtwpr_temp_rules_set[] = $vtwpr_rules_set[$i];
       }
    }
    $vtwpr_rules_set = $vtwpr_temp_rules_set;
   
    if (count($vtwpr_rules_set) == 0) {
      delete_option( 'vtwpr_rules_set' );
    } else {
      update_option( 'vtwpr_rules_set', $vtwpr_rules_set );
    }
 }  
 
  /* Change rule status to 'pending'
        if status is 'pending', the rule will not be executed during cart processing 
  */ 
  public  function vtwpr_trash_rule () {
    global $post, $vtwpr_info, $vtwpr_rules_set, $vtwpr_rule;
    $post_id = $post->ID;    
    $vtwpr_rules_set = get_option( 'vtwpr_rules_set' ) ;
    for($i=0; $i < sizeof($vtwpr_rules_set); $i++) { 
       if ($vtwpr_rules_set[$i]->post_id == $post_id) {
          if ( $vtwpr_rules_set[$i]->rule_status =  'publish' ) {    //only update if necessary, may already be pending
            $vtwpr_rules_set[$i]->rule_status =  'pending';
            update_option( 'vtwpr_rules_set', $vtwpr_rules_set ); 
          }
          $i =  sizeof($vtwpr_rules_set); //set to done
       }
    }
 
    if (count($vtwpr_rules_set) == 0) {
      delete_option( 'vtwpr_rules_set' );
    } else {
      update_option( 'vtwpr_rules_set', $vtwpr_rules_set );
    }    
    
 }  

  /*  Change rule status to 'publish' 
        if status is 'pending', the rule will not be executed during cart processing  
  */
  public  function vtwpr_untrash_rule () {
    global $post, $vtwpr_info, $vtwpr_rules_set, $vtwpr_rule;
    $post_id = $post->ID;     
    $vtwpr_rules_set = get_option( 'vtwpr_rules_set' ) ;
    for($i=0; $i < sizeof($vtwpr_rules_set); $i++) { 
       if ($vtwpr_rules_set[$i]->post_id == $post_id) {
          if  ( sizeof($vtwpr_rules_set[$i]->rule_error_message) > 0 ) {   //if there are error message, the status remains at pending
            //$vtwpr_rules_set[$i]->rule_status =  'pending';   status already pending
            global $wpdb;
            $wpdb->update( $wpdb->posts, array( 'post_status' => 'pending' ), array( 'ID' => $post_id ) );    //match the post status to pending, as errors exist.
          }  else {
            $vtwpr_rules_set[$i]->rule_status =  'publish';
            update_option( 'vtwpr_rules_set', $vtwpr_rules_set );  
          }
          $i =  sizeof($vtwpr_rules_set);   //set to done
       }
    }
 }  
 
     
  public  function vtwpr_nuke_all_rules() {
    global $post, $vtwpr_info;
    
   //DELETE all posts from CPT
   $myPosts = get_posts( array( 'post_type' => 'vtwpr-rule', 'number' => 500, 'post_status' => array ('draft', 'publish', 'pending', 'future', 'private', 'trash' ) ) );
   //$mycustomposts = get_pages( array( 'post_type' => 'vtwpr-rule', 'number' => 500) );
   foreach( $myPosts as $mypost ) {
     // Delete's each post.
     wp_delete_post( $mypost->ID, true);
    // Set to False if you want to send them to Trash.
   }
    
   //DELETE matching option array
   delete_option( 'vtwpr_rules_set' );
 }  
     
  public  function vtwpr_nuke_all_rule_cats() {
    global $vtwpr_info;
    
   //DELETE all rule category entries
   $terms = get_terms($vtwpr_info['rulecat_taxonomy'], 'hide_empty=0&parent=0' );
   $count = count($terms);
   if ( $count > 0 ){  
       foreach ( $terms as $term ) {
          wp_delete_term( $term->term_id, $vtwpr_info['rulecat_taxonomy'] );
       }
   } 
 }  
      
  public  function vtwpr_repair_all_rules() {
    global $wpdb, $post, $vtwpr_info, $vtwpr_rules_set, $vtwpr_rule;    
    $vtwpr_temp_rules_set = array();
    $vtwpr_rules_set = get_option( 'vtwpr_rules_set' ) ;
    for($i=0; $i < sizeof($vtwpr_rules_set); $i++) { 
       //$test_post = get_post($vtwpr_rules_set[$i]->post_id );
       //load up temp_rule_set with every rule *except* the one to be deleted
       if ( get_post($vtwpr_rules_set[$i]->post_id ) ) {
          $vtwpr_temp_rules_set[] = $vtwpr_rules_set[$i];
       }
    }
    $vtwpr_rules_set = $vtwpr_temp_rules_set;
   

    
    if (count($vtwpr_rules_set) == 0) {
      delete_option( 'vtwpr_rules_set' );
    } else {
      update_option( 'vtwpr_rules_set', $vtwpr_rules_set );
    }
 }
     
  public  function vtwpr_nuke_lifetime_purchase_history() {
    global $wpdb;      
    $wpdb->query("DROP TABLE IF EXISTS `".VTWPR_LIFETIME_LIMITS_PURCHASER."` ");  
    $wpdb->query("DROP TABLE IF EXISTS `".VTWPR_LIFETIME_LIMITS_PURCHASER_RULE."` " );
    $wpdb->query("DROP TABLE IF EXISTS `".VTWPR_LIFETIME_LIMITS_PURCHASER_LOGID_RULE."` " );
  }
       
  public  function vtwpr_nuke_audit_trail_logs() {
    global $wpdb;    
    $wpdb->query("DROP TABLE IF EXISTS `".VTWPR_PURCHASE_LOG."` ");
    $wpdb->query("DROP TABLE IF EXISTS `".VTWPR_PURCHASE_LOG_PRODUCT."` ");
    $wpdb->query("DROP TABLE IF EXISTS `".VTWPR_PURCHASE_LOG_PRODUCT_RULE."` " );
  }
} //end class
