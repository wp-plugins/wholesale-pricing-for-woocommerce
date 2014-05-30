<?php
   
class VTWPR_Rule_update {
	

	public function __construct(){  
    
    $this->vtwpr_edit_rule();
     
    //apply rule scehduling
    $this->vtwpr_validate_rule_scheduling();
     
    //clear out irrelevant/conflicting data (if no errors)
    $this->vtwpr_maybe_clear_extraneous_data();
       
    //translate rule into text...
    $this->vtwpr_build_ruleInWords();
/*
    global $vtwpr_rule;
      echo '$vtwpr_rule <pre>'.print_r($vtwpr_rule, true).'</pre>' ;  
 wp_die( __('<strong>Looks like you\'re running an older version of WordPress, you need to be running at least WordPress 3.3 to use the Varktech Minimum Purchase plugin.</strong>', 'vtmin'), __('VT Minimum Purchase not compatible - WP', 'vtmin'), array('back_link' => true));
 */     
    //update rule...
    $this->vtwpr_update_rules_info();
      
  }
  
  /**************************************************************************************** 
  ERROR MESSAGES SHOULD GO ABOVE THE FIELDS IN ERROR, WHERE POSSIBLE, WITH A GENERAL ERROR MSG AT TOP.
  ****************************************************************************************/ 
            
  public  function vtwpr_edit_rule() {
      global $post, $wpdb, $vtwpr_rule, $vtwpr_info, $vtwpr_rules_set, $vtwpr_rule_template_framework, $vtwpr_deal_edits_framework, $vtwpr_deal_structure_framework; 
                                                                                                                                                         
      $vtwpr_rule_new = new VTWPR_Rule();   //  always  start with fresh copy
      $selected = 's';

      $vtwpr_rule = $vtwpr_rule_new;  //otherwise vtwpr_rule is not addressable!
      
      // NOT NEEDED now that the edits are going through successfully
      //for new rule, put in 1st iteration of deal info
      //$vtwpr_rule->rule_deal_info[] = $vtwpr_deal_structure_framework;   mwnt
       
     //*****************************************
     //  FILL / upd VTWPR_RULE...
     //*****************************************
     //   Candidate Population
     
     $vtwpr_rule->post_id = $post->ID;

     if ( ($_REQUEST['post_title'] > ' ' ) ) {
       //do nothing
     } else {     
       $vtwpr_rule->rule_error_message[] = array( 
              'insert_error_before_selector' => '#vtwpr-deal-selection',
              'error_msg'  => __('The Rule needs to have a Title, but Title is empty.', 'vtwpr')  );   
     }

/*

//specialty edits list:

**FOR THE PRICE OF**
=>for the price of within the group:
buy condition must be an amt
buy amt count must be > 1
buy amt must be = to discount amount count

action group condition must be 'applies to entire'
action group must be same as buy pool group only
discount applies to must be = 'all'

=> for the price of next
buy condition can be anything
action amt condition must be an amt
action amt count must be > 1
action amt must be = to discount amount count 

**CHEAPEST/MOST EXPENSIVE**
*=> in buy group
buy condition must be an amt
buy amt count must be > 1

*=> in action group
buy condition can be anything
action amt condition can be an amt or $$

*/
      //Upper Selects

      $vtwpr_rule->cart_or_catalog_select   = $_REQUEST['cart-or-catalog-select'];  
      $vtwpr_rule->pricing_type_select      = $_REQUEST['pricing-type-select'];  
      $vtwpr_rule->minimum_purchase_select  = $_REQUEST['minimum-purchase-select'];  
      $vtwpr_rule->buy_group_filter_select  = $_REQUEST['buy-group-filter-select'];  
      $vtwpr_rule->get_group_filter_select  = $_REQUEST['get-group-filter-select'];  
      $vtwpr_rule->rule_on_off_sw_select    = $_REQUEST['rule-on-off-sw-select'];
      $vtwpr_rule->rule_type_select         = $_REQUEST['rule-type-select'];
      $vtwpr_rule->wizard_on_off_sw_select  = $_REQUEST['wizard-on-off-sw-select']; 
        
      $upperSelectsDoneSw                   = $_REQUEST['upperSelectsDoneSw']; 
      
      if ($upperSelectsDoneSw != 'yes') {       
          $vtwpr_rule->rule_error_message[] = array( 
                'insert_error_before_selector' => '.top-box',  
                'error_msg'  => __('Blueprint choices not yet completed', 'vtwpr') );   //mwn20140414       
          $vtwpr_rule->rule_error_red_fields[] = '#blue-area-title' ;    //mwn20140414   
      } 
      //mwn20140414    begin   ==> added these IDs to rules_ui.php ...
      if (($vtwpr_rule->pricing_type_select == 'choose') || ($vtwpr_rule->pricing_type_select <= ' ')) {
          $vtwpr_rule->rule_error_message[] = array( 
                'insert_error_before_selector' => '.top-box',  
                'error_msg'  => __('Deal Type choice not yet made', 'vtwpr') );   //mwn20140414       
          $vtwpr_rule->rule_error_red_fields[] = '#pricing-type-select-label' ; 
      }       
      //mwn20140414    end 
      
      //#RULEtEMPLATE IS NOW A HIDDEN FIELD which carries the rule template SET WITHIN THE JS
      //   in response to the inital dropdowns being selected. 
     $vtwpr_rule->rule_template = $_REQUEST['rule_template_framework']; 
     if ($vtwpr_rule->rule_template <= '0') {   //mwn20140414 
          /*  mwn20140414 
          $vtwpr_rule->rule_error_message[] = array( 
                'insert_error_before_selector' => '.template-area',  
                'error_msg'  => __('Wholesale Pricing Template choice is required.', 'vtwpr') );
          $vtwpr_rule->rule_error_red_fields[] = '#deal-type-title' ;
          */ 
          $this->vtwpr_dump_deal_lines_to_rule();
         // $this->vtwpr_update_rules_info();  //mwn20140414             
          return; //fatal exit....           
      } else {    
        for($i=0; $i < sizeof($vtwpr_rule_template_framework['option']); $i++) {
          //get template title to make that name available on the Rule
          if ( $vtwpr_rule_template_framework['option'][$i]['value'] == $vtwpr_rule->rule_template )  {
            $vtwpr_rule->rule_template_name = $vtwpr_rule_template_framework['option'][$i]['title'];
            $i = sizeof($vtwpr_rule_template_framework['option']);
          } 
        }
      }

     //DISCOUNT TEMPLATE
     $display_or_cart = substr($vtwpr_rule->rule_template ,0 , 1);
     if ($display_or_cart == 'D') {
       $vtwpr_rule->rule_execution_type = 'display';
     } else {
       $vtwpr_rule->rule_execution_type = 'cart';
     }

     //using the selected Template, build the $vtwpr_deal_edits_framework, used for all DEAL edits following
     $this->vtwpr_build_deal_edits_framework();
  
     //********************************************************************************
     //EDIT DEAL LINES
     //***LOOP*** through all of the deal line iterations, edit lines 
     //********************************************************************************        
     $deal_iterations_done = 'no'; //initialize variable
     $active_line_count = 0; //initialize variable
     $active_field_count = 0;     

     for($k=0; $deal_iterations_done == 'no'; $k++) {      
       if ( (isset( $_REQUEST['buy_repeat_condition_' . $k] )) && (!empty( $_REQUEST['buy_repeat_condition_' . $k] )) ) {    //is a deal line there? always 1 at least...
         foreach( $vtwpr_deal_structure_framework as $key => $value ) {   //spin through all of the screen fields=>  $key = field name, so has multiple uses...  
            //load up the deal structure with incoming fields
            $vtwpr_deal_structure_framework[$key] = $_REQUEST[$key . '_' .$k];
         }   
          
            //Edit deal line
         $this->vtwpr_edit_deal_info_line($active_field_count, $active_line_count, $k);
            //add deal line to rule
         $vtwpr_rule->rule_deal_info[] = $vtwpr_deal_structure_framework;   //add each line to rule, regardless if empty              
       } else {     
         $deal_iterations_done = 'yes';
       }
     }

    //if max_amt_type is active, may have a max_amt_msg
    $vtwpr_rule->discount_rule_max_amt_msg = $_REQUEST['discount_rule_max_amt_msg'];
  
    //EDITED * + * +  * + * +  * + * +  * + * + * + * +  * + * +  * + * +  * + * +
    // + ADDED => pro-only option chosen                                                                             
    if ($vtwpr_rule->rule_deal_info[0]['discount_rule_max_amt_type'] != 'none') { 
         $vtwpr_rule->rule_error_message[] = array(                                                                              
                'insert_error_before_selector' => '.top-box',  
                'error_msg'  => __('The "Maximum Rule Discount for the Cart" option chosen is only available in the Pro Version. ', 'vtwpr') .'<em><strong>'. __(' * Option restored to default value, * Please Update to Confirm!', 'vtwpr') .'</strong></em>');
          $vtwpr_rule->rule_error_red_fields[] = '#discount_rule_max_amt_type_label_0' ; 
          $vtwpr_rule->rule_deal_info[0]['discount_rule_max_amt_type'] = 'none'; //overwrite ERROR choice with DEFAULT  
    } 
    //EDITED end   * + * +  * + * +  * + * +  * + * + * + * +  * + * +  * + * +  * + * +
    
    //if max_lifetime_amt_type is active, may have a max_amt_msg
    $vtwpr_rule->discount_lifetime_max_amt_msg = $_REQUEST['discount_lifetime_max_amt_msg'];

    //if max_cum_amt_type is active, may have a max_amt_msg
    $vtwpr_rule->discount_rule_cum_max_amt_msg = $_REQUEST['discount_rule_cum_max_amt_msg'];
    
               
    $vtwpr_rule->discount_product_short_msg = $_REQUEST['discount_product_short_msg'];
    if ( ($vtwpr_rule->discount_product_short_msg <= ' ') || 
         ($vtwpr_rule->discount_product_short_msg == $_REQUEST['shortMsg']) ) {
        $vtwpr_rule->rule_error_message[] = array( 
              'insert_error_before_selector' => '#messages-box',            
              'error_msg'  => __('Checkout Short Message is required.', 'vtwpr') );
        $vtwpr_rule->rule_error_red_fields[] = '#discount_product_short_msg_label' ;
        $vtwpr_rule->rule_error_box_fields[] = '#discount_product_short_msg';       
    }    

    $vtwpr_rule->discount_product_full_msg = $_REQUEST['discount_product_full_msg']; 
    //if default msg, get rid of it!!!!!!!!!!!!!!
    if ( $vtwpr_rule->discount_product_full_msg == $vtwpr_info['default_full_msg'] ) {
       $vtwpr_rule->discount_product_full_msg == ' ';
    }          
    /* full msg now OPTIONAL
    if ( ($vtwpr_rule->discount_product_full_msg <= ' ') || 
         ($vtwpr_rule->discount_product_full_msg == $_REQUEST['fullMsg'] )){
        $vtwpr_rule->rule_error_message[] = array( 
              'insert_error_before_selector' => '#messages-box',  
              'error_msg'  => __('Theme Full Message is required.', 'vtwpr') );
        $vtwpr_rule->rule_error_red_fields[] = '#discount_product_full_msg_label' ;       
    }    
*/
              
    $vtwpr_rule->cumulativeRulePricing = $_REQUEST['cumulativeRulePricing']; 
    if ($vtwpr_rule->cumulativeRulePricing == 'yes') {
       if ($vtwpr_rule->cumulativeRulePricingAllowed == 'yes') {
         $vtwpr_rule->ruleApplicationPriority_num = $_REQUEST['ruleApplicationPriority_num'];
         $vtwpr_rule->ruleApplicationPriority_num = preg_replace('/[^0-9.]+/', '', $vtwpr_rule->ruleApplicationPriority_num); //remove leading/trailing spaces, percent sign, dollar sign
         if ( is_numeric($vtwpr_rule->ruleApplicationPriority_num) === false ) { 
            $vtwpr_rule->ruleApplicationPriority_num = '10'; //init variable 
            /*
            $vtwpr_rule->rule_error_message[] = array( 
                  'insert_error_before_selector' => '#cumulativePricing_box',  
                  'error_msg'  => __('"Apply this Rule Discount in Addition to Other Rule Discounts" = Yes.  Rule Priority Sort Number is required, and must be numeric. "10" inserted if blank.', 'vtwpr') );
            $vtwpr_rule->rule_error_red_fields[] = '#ruleApplicationPriority_num_label' ;        
            */
         }
       } else {
            $vtwpr_rule->rule_error_message[] = array( 
                  'insert_error_before_selector' => '#cumulativePricing_box',  
                  'error_msg'  => __('With this Rule Template chosen, "Apply this Rule Discount in Addition to Other Rule Discounts" must = "No".', 'vtwpr') );
            $vtwpr_rule->rule_error_red_fields[] = '#ruleApplicationPriority_num_label' ;
            $vtwpr_rule->rule_error_box_fields[] = '#ruleApplicationPriority_num'; 
            $vtwpr_rule->ruleApplicationPriority_num = '10'; //init variable     
       }
    } else {
      $vtwpr_rule->ruleApplicationPriority_num = '10'; //init variable  
    }
                 
    $vtwpr_rule->cumulativeSalePricing   = $_REQUEST['cumulativeSalePricing'];
    if ( ($vtwpr_rule->cumulativeSalePricing != 'no') && ($vtwpr_rule->cumulativeSalePricingAllowed == 'no') ) {
      $vtwpr_rule->rule_error_message[] = array( 
            'insert_error_before_selector' => '#cumulativePricing_box',  
            'error_msg'  => __('With this Rule Template chosen, "Rule Discount in addition to Product Sale Pricing" must = "Does not apply when Product Sale Priced".', 'vtwpr') );
      $vtwpr_rule->rule_error_red_fields[] = '#cumulativePricing_box';
      $vtwpr_rule->rule_error_box_fields[] = '#cumulativePricing';  
    }
               
    $vtwpr_rule->cumulativeCouponPricing = $_REQUEST['cumulativeCouponPricing'];            
    if ( ($vtwpr_rule->cumulativeCouponPricing == 'yes') && ($vtwpr_rule->cumulativeCouponPricingAllowed == 'no') ) {
      $vtwpr_rule->rule_error_message[] = array( 
            'insert_error_before_selector' => '#cumulativePricing_box',  
            'error_msg'  => __('With this Rule Template chosen, " Apply Rule Discount in addition to Coupon Discount?" must = "No".', 'vtwpr') );
      $vtwpr_rule->rule_error_red_fields[] = '#cumulativePricing_box' ;
      $vtwpr_rule->rule_error_box_fields[] = '#cumulativePricing'; 
    } 
 
 
         
     //inPop        
     $vtwpr_rule->role_and_or_in = 'or'; //initialize so it's always there.  overwritten by logic as needed.
     $vtwpr_rule->inPop = $_REQUEST['popChoiceIn'];
     switch( $vtwpr_rule->inPop ) {
        case 'wholeStore':
          break;
        
        //EDITED * + * +  * + * +  * + * +  * + * + * + * +  * + * +  * + * +  * + * +
        default: // +ADDED => pro-only option chosen                                                                             
            $vtwpr_rule->rule_error_message[] = array( 
                  'insert_error_before_selector' => '.top-box',  
                  'error_msg'  => __('The " Product Filter Selection" option chosen is only available in the Pro Version. ', 'vtwpr') .'<em><strong>'. __(' * Option restored to default value, * Please Update to Confirm!', 'vtwpr') .'</strong></em>');
            $vtwpr_rule->rule_error_red_fields[] = '#buy_group_label' ; 
             $vtwpr_rule->inPop = 'wholeStore'; //overwrite ERROR choice with DEFAULT 
             $vtwpr_rule->buy_group_filter_select = 'wholeStore'; //overwrite ERROR choice with DEFAULT     
          break; 
        //Edited end  * + * +  * + * +  * + * +  * + * + * + * +  * + * +  * + * +  * + * +
        
      }

      //********************************************************************************************************************
      //The WPSC Realtime Catalog repricing action does not pass variation-level info, so these options are disallowed
      //********************************************************************************************************************
      if (($vtwpr_rule->rule_execution_type == 'display') && 
          (VTWPR_PARENT_PLUGIN_NAME == 'WP E-Commerce') ) {
          
            if ($vtwpr_rule->inPop == 'vargroup')  { 
               $vtwpr_rule->rule_error_message[] = array( 
                              'insert_error_before_selector' => '#inPop-varProdID-cntl', 
                              'error_msg'  => __('"Buy" Group Selection - Single with Variations may not be chosen when "Apply Price Reduction to Products in the Catalog" Wholesale Pricing Type is chosen.', 'vtwpr') );
               $vtwpr_rule->rule_error_red_fields[] = '#inPopChoiceIn_label';
            }
    
            if ($vtwpr_rule->cumulativeSalePricingAllowed == 'no' )  { 
               $vtwpr_rule->rule_error_message[] = array( 
                              'insert_error_before_selector' => '#cumulativePricing_box',  
                              'error_msg'  => __('"Apply this Rule Discount in addition to Product Sale Pricing" must not be "No" when "Apply Price Reduction to Products in the Catalog" Wholesale Pricing Type is chosen.', 'vtwpr') );
               $vtwpr_rule->rule_error_red_fields[] = '#cumulativeSalePricing_label';
               $vtwpr_rule->rule_error_box_fields[] = '#cumulativeSalePricing';
            }        
      }                                                                                     

      //********************************************************************************************************************  
      //  ruleApplicationPriority_num must ALWAYS be numeric, to allow for sorting
      //********************************************************************************************************************
 /*     $vtwpr_rule->ruleApplicationPriority_num = preg_replace('/[^0-9.]+/', '', $vtwpr_rule->ruleApplicationPriority_num); //remove leading/trailing spaces, percent sign, dollar sign
      if ( is_numeric($vtwpr_rule->ruleApplicationPriority_num) === false ) { 
        $vtwpr_rule->ruleApplicationPriority_num = '10';
      }  */
      /* not necessary any more!
      if ($vtwpr_rule->rule_execution_type == 'display') {
        //display rules must ALWAYS sort first, so we reset it here
        $vtwpr_rule->ruleApplicationPriority_num = '0';  
      }
      */      
     //actionPop        
     $vtwpr_rule->role_and_or_out = 'or'; //initialize so it's always there.  overwritten by logic as needed.
     $vtwpr_rule->actionPop = $_REQUEST['popChoiceOut'];
     switch( $vtwpr_rule->actionPop ) {
        case 'sameAsInPop':
		    case 'wholeStore':
            //  $vtwpr_rule->actionPop[0]['user_input'] = $selected;
            //  $this->vtwpr_set_default_or_values_out();
          break;
        
        //EDITED * + * +  * + * +  * + * +  * + * + * + * +  * + * +  * + * +  * + * +
        default: //  +ADDED => pro-only option chosen                                                                             
            $vtwpr_rule->rule_error_message[] = array( 
                  'insert_error_before_selector' => '.top-box',  
                  'error_msg'  => __('The "Get Group Selection" option chosen is only available in the Pro Version. ', 'vtwpr') .'<em><strong>'. __(' * Option restored to default value, * Please Update to Confirm!', 'vtwpr') .'</strong></em>');
            $vtwpr_rule->rule_error_red_fields[] = '#action_group_label' ;
            $vtwpr_rule->actionPop = 'sameAsInPop'; //overwrite ERROR choice with DEFAULT 
            $vtwpr_rule->get_group_filter_select = 'sameAsInPop'; //overwrite ERROR choice with DEFAULT    
          break; 
        //EDIT end   * + * +  * + * +  * + * +  * + * + * + * +  * + * +  * + * +  * + * +
      
      }


      //********************************************************************************************************************
      //Specialty Complex edits... 
      //********************************************************************************************************************
                                                  
       //FOR THE PRICE OF requirements...
       if ($vtwpr_rule->rule_deal_info[0]['discount_amt_type'] =='forThePriceOf_Units') {
          switch ($vtwpr_rule->rule_template) {
           case 'C-forThePriceOf-inCart':  //buy-x-action-forThePriceOf-same-group-discount
                 if ($vtwpr_rule->rule_deal_info[0]['buy_amt_type'] != 'quantity') {
                    $vtwpr_rule->rule_error_message[] = array( 
                          'insert_error_before_selector' => '#buy_amt_box_0',  
                          'error_msg'  => __('"Buy Unit Quantity" required for Discount Type "For the Price of (Units) Discount"', 'vtwpr') );
                    $vtwpr_rule->rule_error_red_fields[] = '#buy_amt_type_label_0';
                    $vtwpr_rule->rule_error_red_fields[] = '#discount_amt_type_label_0';                
                 } 
                 elseif ( (is_numeric( $vtwpr_rule->rule_deal_info[0]['buy_amt_count'])) && ($vtwpr_rule->rule_deal_info[0]['buy_amt_count'] < '2' )) {
                    $vtwpr_rule->rule_error_message[] = array( 
                          'insert_error_before_selector' => '#buy_amt_box_0',  
                          'error_msg'  => __('"Buy Unit Quantity" must be > 1 for Discount Type "For the Price of (Units) Discount".', 'vtwpr') );
                    $vtwpr_rule->rule_error_red_fields[] = '#discount_amt_type_label_0';
                    $vtwpr_rule->rule_error_box_fields[] = '#buy_amt_count_0';                    
                 }
                 elseif ( (is_numeric( $vtwpr_rule->rule_deal_info[0]['buy_amt_count'])) && 
                          ($vtwpr_rule->rule_deal_info[0]['buy_amt_count'] <= $vtwpr_rule->rule_deal_info[0]['discount_amt_count'])  ) {
                    $vtwpr_rule->rule_error_message[] = array( 
                          'insert_error_before_selector' => '#buy_amt_box_0',  
                          'error_msg'  => __('"Buy Unit Quantity" must be greater than Discount Type "Discount For the Price of Units", when "For the Price of (Units) Discount" chosen.', 'vtwpr') );
                    $vtwpr_rule->rule_error_red_fields[] = '#discount_amt_count_literal_forThePriceOf_0';
                    $vtwpr_rule->rule_error_box_fields[] = '#buy_amt_count_0';                    
                 }      
              break;
           case 'C-forThePriceOf-Next':  //buy-x-action-forThePriceOf-other-group-discount
                 if ($vtwpr_rule->rule_deal_info[0]['action_amt_type'] != 'quantity') {
                    $vtwpr_rule->rule_error_message[] = array( 
                          'insert_error_before_selector' => '#action_amt_box_0',  
                          'error_msg'  => __('"Get Unit Quantity" required for Discount Type "For the Price of (Units) Discount"', 'vtwpr') );
                    $vtwpr_rule->rule_error_red_fields[] = '#discount_amt_type_label_0';
                    $vtwpr_rule->rule_error_box_fields[] = '#action_amt_count_0';                
                 } 
                 elseif ( (is_numeric($vtwpr_rule->rule_deal_info[0]['action_amt_count'])) && ($vtwpr_rule->rule_deal_info[0]['action_amt_count'] < '2') ) {
                    $vtwpr_rule->rule_error_message[] = array( 
                          'insert_error_before_selector' => '#action_amt_box_0',  
                          'error_msg'  => __('"Get Unit Quantity" must be > 1 for Discount Type "For the Price of (Units) Discount".', 'vtwpr') );
                    $vtwpr_rule->rule_error_red_fields[] = '#discount_amt_type_label_0';
                    $vtwpr_rule->rule_error_box_fields[] = '#action_amt_count_0';                    
                 }
                 elseif ( (is_numeric($vtwpr_rule->rule_deal_info[0]['action_amt_count'])) &&
                        ($vtwpr_rule->rule_deal_info[0]['action_amt_count'] <= $vtwpr_rule->rule_deal_info[0]['discount_amt_count']) ) {
                    $vtwpr_rule->rule_error_message[] = array( 
                          'insert_error_before_selector' => '#action_amt_box_0',  
                          'error_msg'  => __('"Get Unit Quantity" must be greater than Discount Type "Discount For the Price of Units", when "For the Price of (Units) Discount" chosen.', 'vtwpr') );
                    $vtwpr_rule->rule_error_red_fields[] = '#discount_amt_count_literal_forThePriceOf_0';
                    $vtwpr_rule->rule_error_box_fields[] = '#action_amt_count_0';                    
                 }     
              break;
           default:
                $vtwpr_rule->rule_error_message[] = array( 
                      'insert_error_before_selector' => '#discount_amt_box_0',  
                      'error_msg'  => __('To use Discount Type "For the Price of (Units) Discount", choose a "For the Price Of" template type.', 'vtwpr') );
                $vtwpr_rule->rule_error_red_fields[] = '#discount_amt_type_label_0'; 
                $vtwpr_rule->rule_error_red_fields[] = '#deal-type-title';                   
              break;
         } //end switch   
       } //end if forThePriceOf_Units

       if ($vtwpr_rule->rule_deal_info[0]['discount_amt_type'] =='forThePriceOf_Currency') {
          switch ($vtwpr_rule->rule_template) {
           case 'C-forThePriceOf-inCart':  //buy-x-action-forThePriceOf-same-group-discount
                 if ($vtwpr_rule->rule_deal_info[0]['buy_amt_type'] != 'quantity') {
                    $vtwpr_rule->rule_error_message[] = array( 
                          'insert_error_before_selector' => '#buy_amt_box_0',  
                          'error_msg'  => __('"Buy Unit Quantity" required for Discount Type "For the Price of (Currency) Discount"', 'vtwpr') );
                    $vtwpr_rule->rule_error_red_fields[] = '#buy_amt_type_label_0';
                    $vtwpr_rule->rule_error_red_fields[] = '#discount_amt_type_label_0';                
                 } 
                 elseif ($vtwpr_rule->rule_deal_info[0]['buy_amt_count'] < '2' ) {
                    $vtwpr_rule->rule_error_message[] = array( 
                          'insert_error_before_selector' => '#buy_amt_box_0',  
                          'error_msg'  => __('"Buy Unit Quantity" must be > 1 for Discount Type "For the Price of (Currency) Discount".', 'vtwpr') );
                    $vtwpr_rule->rule_error_red_fields[] = '#discount_amt_type_label_0';
                    $vtwpr_rule->rule_error_box_fields[] = '#buy_amt_count_0';                    
                 }     
              break;
           case 'C-forThePriceOf-Next':  //buy-x-action-forThePriceOf-other-group-discount
                 if ($vtwpr_rule->rule_deal_info[0]['action_amt_type'] != 'quantity') {
                    $vtwpr_rule->rule_error_message[] = array( 
                          'insert_error_before_selector' => '#action_amt_box_0',  
                          'error_msg'  => __('"Get Unit Quantity" required for Discount Type "For the Price of (Currency) Discount"', 'vtwpr') );
                    $vtwpr_rule->rule_error_red_fields[] = '#action_amt_type_label_0';
                    $vtwpr_rule->rule_error_red_fields[] = '#discount_amt_type_label_0';                
                 } 
                 elseif ($vtwpr_rule->rule_deal_info[0]['action_amt_count'] < '2' ) {
                    $vtwpr_rule->rule_error_message[] = array( 
                          'insert_error_before_selector' => '#action_amt_box_0',  
                          'error_msg'  => __('"Get Unit Quantity" must be > 1 for Discount Type "For the Price of (Currency) Discount".', 'vtwpr') );
                    $vtwpr_rule->rule_error_red_fields[] = '#discount_amt_type_label_0';
                    $vtwpr_rule->rule_error_box_fields[] = '#action_amt_count_0';                    
                 }     
              break;
           default:
                $vtwpr_rule->rule_error_message[] = array( 
                      'insert_error_before_selector' => '#discount_amt_box_0',  
                      'error_msg'  => __('To use Discount Type "For the Price of (Currency) Discount", choose a "For the Price Of" template type.', 'vtwpr') );
                $vtwpr_rule->rule_error_red_fields[] = '#discount_amt_type_label_0'; 
                $vtwpr_rule->rule_error_red_fields[] = '#deal-type-title';                   
              break;
         } //end switch   
       } //end if forThePriceOf_Currency
                                                    
       //DISCOUNT APPLIES TO requirements...
       if ( ($vtwpr_rule->rule_deal_info[0]['discount_applies_to'] == 'cheapest') || 
            ($vtwpr_rule->rule_deal_info[0]['discount_applies_to'] == 'most_expensive') ){
          switch ($vtwpr_rule->rule_template) {
           case 'C-cheapest-inCart':  //buy-x-action-most-expensive-same-group-discount
                 if ( ($vtwpr_rule->rule_deal_info[0]['buy_amt_type'] != 'quantity') && 
                      ($vtwpr_rule->rule_deal_info[0]['buy_amt_type'] != 'currency') ) {
                    $vtwpr_rule->rule_error_message[] = array( 
                          'insert_error_before_selector' => '#buy_amt_box_0',  
                          'error_msg'  => __('Buy Amount type must be Quantity or Currency, when Discount "Applies To Cheapest/Most Expensive" chosen.', 'vtwpr') );
                    $vtwpr_rule->rule_error_red_fields[] = '#buy_amt_type_label_0';
                    $vtwpr_rule->rule_error_red_fields[] = '#discount_applies_to_label_0';                 
                 }                     
                 elseif ( (is_numeric($vtwpr_rule->rule_deal_info[0]['buy_amt_count'])) && ($vtwpr_rule->rule_deal_info[0]['buy_amt_count'] < '2') ) {
                    $vtwpr_rule->rule_error_message[] = array( 
                          'insert_error_before_selector' => '#buy_amt_box_0',  
                          'error_msg'  => __('Buy Amount Count must be greater than 1, when Discount "Applies To Cheapest/Most Expensive" chosen.', 'vtwpr') );
                    $vtwpr_rule->rule_error_red_fields[] = '#buy_amt_type_label_0';
                    $vtwpr_rule->rule_error_red_fields[] = '#discount_applies_to_label_0';                 
                 }                                                
              break;           
           case 'C-cheapest-Next':  //buy-x-action-most-expensive-other-group-discount
                 if ( ($vtwpr_rule->rule_deal_info[0]['action_amt_type'] != 'quantity') && 
                      ($vtwpr_rule->rule_deal_info[0]['action_amt_type'] != 'currency') ) {
                    $vtwpr_rule->rule_error_message[] = array( 
                          'insert_error_before_selector' => '#action_amt_box_0',  
                          'error_msg'  => __('Get Amount type must be Quantity or Currency, when Discount "Applies To Cheapest/Most Expensive" chosen.', 'vtwpr') );
                    $vtwpr_rule->rule_error_red_fields[] = '#action_amt_type_label_0';
                    $vtwpr_rule->rule_error_red_fields[] = '#discount_applies_to_label_0';                 
                 }                     
                 elseif ( (is_numeric($vtwpr_rule->rule_deal_info[0]['action_amt_count'])) && ($vtwpr_rule->rule_deal_info[0]['action_amt_count'] < '2') ) {
                    $vtwpr_rule->rule_error_message[] = array( 
                          'insert_error_before_selector' => '#action_amt_box_0',  
                          'error_msg'  => __('Get Amount Count must be greater than 1, when Discount "Applies To Cheapest/Most Expensive" chosen.', 'vtwpr') );
                    $vtwpr_rule->rule_error_red_fields[] = '#action_amt_type_label_0';
                    $vtwpr_rule->rule_error_red_fields[] = '#discount_applies_to_label_0';                 
                 }
              break;
           default:
                $vtwpr_rule->rule_error_message[] = array( 
                      'insert_error_before_selector' => '#discount_applies_to_box_0',  
                      'error_msg'  => __('Please choose a "Cheapest/Most Expensive" template type, when Discount "Applies To Cheapest/Most Expensive" chosen.', 'vtwpr') );
                $vtwpr_rule->rule_error_red_fields[] = '#discount_applies_to_label_0';
                $vtwpr_rule->rule_error_red_fields[] = '#deal-type-title';
              break;           
         } //end switch        
       } //end if discountAppliesTo


    
      //********************************************************************************************************************
      //The WPSC Realtime Catalog repricing action does not pass variation-level info, so these options are disallowed
      //********************************************************************************************************************
      if (($vtwpr_rule->rule_execution_type == 'display') && 
          (VTWPR_PARENT_PLUGIN_NAME == 'WP E-Commerce') ) {
          
            if ($vtwpr_rule->actionPop == 'vargroup')  { 
               $vtwpr_rule->rule_error_message[] = array( 
                              'insert_error_before_selector' => '#actionPop-varProdID-cntl',  
                              'error_msg'  => __('"Action" Group Selection - Single with Variations may not be chosen when "Apply Price Reduction to Products in the Catalog" Wholesale Pricing Type is chosen, due to a WPEC limitation.', 'vtwpr') );
               $vtwpr_rule->rule_error_red_fields[] = '#actionPopChoiceOut_label';
            }
    
            if ($vtwpr_rule->cumulativeSalePricingAllowed == 'no' )  { 
               $vtwpr_rule->rule_error_message[] = array( 
                              'insert_error_before_selector' => '#cumulativeSalePricing_areaID',  
                              'error_msg'  => __('"Apply this Rule Discount in addition to Product Sale Pricing" must not be "No" when "Apply Price Reduction to Products in the Catalog" Wholesale Pricing Type is chosen, due to a WPEC limitation.', 'vtwpr') );
               $vtwpr_rule->rule_error_red_fields[] = '#cumulativeSalePricing_label';
            }        
      }                                                                                     

      //********************************************************************************************************************      
      //********************************************************************************************************************
       
      //EDITED BEGIN * + * +  * + * +  * + * +  * + * + * + * +  * + * +  * + * +  * + * +
        // +ADDED =>    BEGIN
        // pro-only option chosen                                                                             
      if ($vtwpr_rule->rule_deal_info[0]['discount_lifetime_max_amt_type'] != 'none') { 
           $vtwpr_rule->rule_error_message[] = array( 
                  'insert_error_before_selector' => '.top-box',  
                  'error_msg'  => __('The "Maximum Discounts per Customer (for Lifetime of the rule)" option chosen is only available in the Pro Version ', 'vtwpr') .'<em><strong>'. __(' * Option restored to default value, * Please Update to Confirm!', 'vtwpr') .'</strong></em>');
            $vtwpr_rule->rule_error_red_fields[] = '#discount_lifetime_max_amt_type_label_0' ; 
            $vtwpr_rule->rule_deal_info[0]['discount_lifetime_max_amt_type'] = 'none'; //overwrite ERROR choice with DEFAULT   
      }    
      if ($vtwpr_rule->rule_deal_info[0]['discount_rule_cum_max_amt_type'] != 'none') { 
           $vtwpr_rule->rule_error_message[] = array( 
                  'insert_error_before_selector' => '.top-box',  
                  'error_msg'  => __('The "Cart Maximum for all Discounts Per Product" option chosen is only available in the Pro Version. ', 'vtwpr') .'<em><strong>'. __(' * Option restored to default value, * Please Update to Confirm!', 'vtwpr') .'</strong></em>');
            $vtwpr_rule->rule_error_red_fields[] = '#discount_rule_cum_max_amt_type_label_0' ; 
            $vtwpr_rule->rule_deal_info[0]['discount_rule_cum_max_amt_type'] = 'none'; //overwrite ERROR choice with DEFAULT  
      }      
      // +ADDED   End
      //EDITED END  * + * +  * + * +  * + * +  * + * + * + * +  * + * +  * + * +  * + * +   
 

      //*************************
      //AUTO ADD switching (+ sort field switching as well)
      //*************************
      $vtwpr_rule->rule_contains_auto_add_free_product = 'no';
      $vtwpr_rule->rule_contains_free_product = 'no'; //used for sort in apply-rules.php
      $vtwpr_rule->var_out_product_variations_parameter = array(); 
      $sizeof_rule_deal_info = sizeof($vtwpr_rule->rule_deal_info);
      for($d=0; $d < $sizeof_rule_deal_info; $d++) {                  
         if ($vtwpr_rule->rule_deal_info[$d]['discount_auto_add_free_product'] == 'yes') {
                                 
             //EDITED * + * +  * + * +  * + * +  * + * + * + * +  * + * +  * + * +  * + * +
            // + ADDED => pro-only option chosen                                                                             

                 $vtwpr_rule->rule_error_message[] = array(                                                                              
                        'insert_error_before_selector' => '#discount_amt_box_' .$d ,  
                        'error_msg'  => __('The " Automatically Add Free Product to Cart" option chosen is only available in the Pro Version. ', 'vtwpr') .'<em><strong>'. __(' * Option restored to default value, * Please Update to Confirm!', 'vtwpr') .'</strong></em>'
                        );
                  $vtwpr_rule->rule_error_red_fields[] = '#discount_auto_add_free_product_label_' .$d ;
                  
                  $vtwpr_rule->rule_deal_info[$d]['discount_auto_add_free_product'] = ''; 

            //EDITED end  * + * +  * + * +  * + * +  * + * + * + * +  * + * +  * + * +  * + * +
           
         }
         if ($vtwpr_rule->rule_deal_info[$d]['discount_amt_type'] == 'free') {
            $vtwpr_rule->rule_contains_free_product = 'yes'; 
         }                   
      }

      //*************************
      //Pop Filter Agreement Check (switch used in apply...)
      //*************************
      $this->vtwpr_maybe_pop_filter_agreement();

            
      //*************************
      //check against all other rules acting on the free product
      //*************************
      if ($vtwpr_rule->rule_contains_auto_add_free_product == 'yes') {

        $vtwpr_rules_set = get_option( 'vtwpr_rules_set' );

        $sizeof_rules_set = sizeof($vtwpr_rules_set);
       
        for($i=0; $i < $sizeof_rules_set; $i++) { 
                     
          if ( ($vtwpr_rules_set[$i]->rule_status != 'publish') ||
               ($vtwpr_rules_set[$i]->rule_on_off_sw_select == 'off') ) {             
             continue;
          }

            
          if ($vtwpr_rules_set[$i]->post_id == $vtwpr_rule->post_id) {                   
             continue;
          } 
                        
          //if another rule has the exact same FREE product, that's an ERROR
          if ($vtwpr_rules_set[$i]->rule_contains_auto_add_free_product == 'yes') {  
              
               //EDITED * + * +  * + * +  * + * +  * + * + * + * +  * + * +  * + * +  * + * +
               //   Leave the following here...
               //EDITED * + * +  * + * +  * + * +  * + * + * + * +  * + * +  * + * +  * + * +
              switch( true ) {    
                
                // compare to vtwpr_rule  actionpop vargroup
                case  ($vtwpr_rule->actionPop == 'vargroup' ) :
                     
                      //current rule vs other rule actionPop vs actionPop
                      if (($vtwpr_rules_set[$i]->actionPop           == 'vargroup') &&
                         ($vtwpr_rules_set[$i]->actionPop_varProdID  == $vtwpr_rule->actionPop_varProdID) &&
                         ($vtwpr_rules_set[$i]->var_out_checked[0]   == $vtwpr_rule->var_out_checked[0] )) {
                        $conflictPost = get_post($vtwpr_rules_set[$i]->post_id);
                        $vtwpr_rule->rule_error_message[] = array( 
                            'insert_error_before_selector' => '#discount_amt_box_0',  
                            'error_msg'  => __('When "Automatically Add Free Product to Cart" is Selected, no other Auto Add Rule may have the same product as the Discount Group.  CONFLICTING RULE NAME is: ', 'vtwpr') .$conflictPost->post_title 
                            );
                        $vtwpr_rule->rule_error_red_fields[] = '#discount_auto_add_free_product_label_0'; 
                        break 2; 
                      }   
                      
                      //current rule actionPop vs other rule inPop
                      if ($vtwpr_rules_set[$i]->actionPop  == 'sameAsInPop' ) { 
                          if (($vtwpr_rules_set[$i]->inPop              == 'vargroup') &&
                              ($vtwpr_rules_set[$i]->inPop_varProdID    == $vtwpr_rule->actionPop_varProdID) &&
                              ($vtwpr_rules_set[$i]->var_in_checked[0]  == $vtwpr_rule->var_out_checked[0] )) {
                            $conflictPost = get_post($vtwpr_rules_set[$i]->post_id);
                            $vtwpr_rule->rule_error_message[] = array( 
                                'insert_error_before_selector' => '#discount_amt_box_0',  
                                'error_msg'  => __('When "Automatically Add Free Product to Cart" is Selected, no other Auto Add Rule may have the same product as the Discount Group.  CONFLICTING RULE NAME is: ', 'vtwpr') .$conflictPost->post_title 
                                );
                            $vtwpr_rule->rule_error_red_fields[] = '#discount_auto_add_free_product_label_0';
                            break 2; 
                          }                      
                      }             

                   break;

                // compare to vtwpr_rule  actionpop single
                case  ($vtwpr_rule->actionPop == 'single' ) : 
                            
                      if (($vtwpr_rules_set[$i]->actionPop              == 'single') &&
                          ($vtwpr_rules_set[$i]->actionPop_singleProdID == $vtwpr_rule->actionPop_singleProdID) ) { 
                        $conflictPost = get_post($vtwpr_rules_set[$i]->post_id);
                        $vtwpr_rule->rule_error_message[] = array( 
                            'insert_error_before_selector' => '#discount_amt_box_0',  
                            'error_msg'  => __('When "Automatically Add Free Product to Cart" is Selected, no other Auto Add Rule may have the same product as the Discount Group.  CONFLICTING RULE NAME is: ', 'vtwpr') . $conflictPost->post_title 
                            );
                        $vtwpr_rule->rule_error_red_fields[] = '#discount_auto_add_free_product_label_0';
                        break 2;              
                      }                   

                      
                      //current rule actionPop vs other rule inPop
                      if ($vtwpr_rules_set[$i]->actionPop  == 'sameAsInPop' ) { 
                          if (($vtwpr_rules_set[$i]->inPop                == 'single') &&
                              ($vtwpr_rules_set[$i]->inPop_singleProdID   == $vtwpr_rule->actionPop_singleProdID) ) { 
                            $conflictPost = get_post($vtwpr_rules_set[$i]->post_id);
                            $vtwpr_rule->rule_error_message[] = array( 
                                'insert_error_before_selector' => '#discount_amt_box_0',  
                                'error_msg'  => __('When "Automatically Add Free Product to Cart" is Selected, no other Auto Add Rule may have the same product as the Discount Group.  CONFLICTING RULE NAME is: ', 'vtwpr') . $conflictPost->post_title 
                                );
                            $vtwpr_rule->rule_error_red_fields[] = '#discount_auto_add_free_product_label_0';
                            break 2;              
                          }                 
                      }

                   break;

                // compare to vtwpr_rule  inpop vargroup
                case  ( ($vtwpr_rule->actionPop == 'sameAsInPop' ) &&
                        ($vtwpr_rule->inPop     == 'vargroup' ) ) : 
                      
                      //current rule vs other rule actionPop vs actionPop
                      if (($vtwpr_rules_set[$i]->actionPop            == 'vargroup') &&
                          ($vtwpr_rules_set[$i]->actionPop_varProdID  == $vtwpr_rule->inPop_varProdID) &&
                          ($vtwpr_rules_set[$i]->var_out_checked[0]   == $vtwpr_rule->var_in_checked[0] ) ) {
                        $conflictPost = get_post($vtwpr_rules_set[$i]->post_id);
                        $vtwpr_rule->rule_error_message[] = array( 
                            'insert_error_before_selector' => '#discount_amt_box_0',  
                            'error_msg'  => __('When "Automatically Add Free Product to Cart" is Selected, no other Auto Add Rule may have the same product as the Discount Group.  CONFLICTING RULE NAME is: ', 'vtwpr') . $conflictPost->post_title 
                            );
                        $vtwpr_rule->rule_error_red_fields[] = '#discount_auto_add_free_product_label_0';
                        break 2; 
                      }   
                      
                      //current rule actionPop vs other rule inPop
                      if ($vtwpr_rules_set[$i]->actionPop  == 'sameAsInPop' ) { 
                          if (($vtwpr_rules_set[$i]->inPop             == 'vargroup') &&
                              ($vtwpr_rules_set[$i]->inPop_varProdID   == $vtwpr_rule->inPop_varProdID) &&
                              ($vtwpr_rules_set[$i]->var_in_checked[0] == $vtwpr_rule->var_in_checked[0] )) {
                            $conflictPost = get_post($vtwpr_rules_set[$i]->post_id);
                            $vtwpr_rule->rule_error_message[] = array( 
                                'insert_error_before_selector' => '#discount_amt_box_0',  
                                'error_msg'  => __('When "Automatically Add Free Product to Cart" is Selected, no other Auto Add Rule may have the same product as the Discount Group.  CONFLICTING RULE NAME is: ', 'vtwpr') . $conflictPost->post_title 
                                );
                            $vtwpr_rule->rule_error_red_fields[] = '#discount_auto_add_free_product_label_0';
                            break 2; 
                          }                      
                      }             

                   break;

                // compare to vtwpr_rule  inpop single
                case  ( ($vtwpr_rule->actionPop == 'sameAsInPop' ) &&
                        ($vtwpr_rule->inPop     == 'single' ) ) : 
                                                              
                      if ( ($vtwpr_rules_set[$i]->actionPop               == 'single') && 
                           ($vtwpr_rules_set[$i]->actionPop_singleProdID  == $vtwpr_rule->inPop_singleProdID) ) { 
                        $conflictPost = get_post($vtwpr_rules_set[$i]->post_id);
                        $vtwpr_rule->rule_error_message[] = array( 
                            'insert_error_before_selector' => '#discount_amt_box_0',  
                            'error_msg'  => __('When "Automatically Add Free Product to Cart" is Selected, no other Auto Add Rule may have the same product as the Discount Group.  CONFLICTING RULE NAME is: ', 'vtwpr') . $conflictPost->post_title 
                            );
                        $vtwpr_rule->rule_error_red_fields[] = '#discount_auto_add_free_product_label_0';
                        break 2;              
                      }                   

                      
                      //current rule actionPop vs other rule inPop
                      if ($vtwpr_rules_set[$i]->actionPop  == 'sameAsInPop' ) { 
                          if ( ($vtwpr_rules_set[$i]->inPop               == 'single') && 
                               ($vtwpr_rules_set[$i]->inPop_singleProdID  == $vtwpr_rule->inPop_singleProdID) ) { 
                            $conflictPost = get_post($vtwpr_rules_set[$i]->post_id);
                            $vtwpr_rule->rule_error_message[] = array( 
                                'insert_error_before_selector' => '#discount_amt_box_0',  
                                'error_msg'  => __('When "Automatically Add Free Product to Cart" is Selected, no other Auto Add Rule may have the same product as the Discount Group.  CONFLICTING RULE NAME is: ', 'vtwpr') . $conflictPost->post_title 
                                );
                            $vtwpr_rule->rule_error_red_fields[] = '#discount_auto_add_free_product_label_0';
                            break 2;              
                          }                 
                      }

                   break;                   
                   
            }  //end switch
          } //end if
          
        } //end 'for' loop
      } //end if auto product 
      //*************************



  } //end vtwpr_edit_rule
  
  
  public function vtwpr_update_rules_info() {
    global $post, $vtwpr_rule, $vtwpr_rules_set, $vtwpr_setup_options; 

    //v1.0.4
    if ( $vtwpr_setup_options['debugging_mode_on'] == 'yes' ){   
      error_log( print_r(  '$vtwpr_rule', true ) );
      error_log( var_export($vtwpr_rule, true ) );   
    }
/*      
    //set the switch used on the screen for JS data check
    switch( true ) {
      case (!$vtwpr_rule->rule_deal_info[0]['discount_rule_max_amt_type'] == 'none'):
      case ( $vtwpr_rule->rule_deal_info[0]['discount_rule_max_amt_count'] > 0) :
      case (!$vtwpr_rule->rule_deal_info[0]['discount_lifetime_max_amt_type'] == 'none'):
      case ( $vtwpr_rule->rule_deal_info[0]['discount_lifetime_max_amt_count'] > 0) :
      case (!$vtwpr_rule->rule_deal_info[0]['discount_rule_cum_max_amt_type'] == 'none'):
      case ( $vtwpr_rule->rule_deal_info[0]['discount_rule_cum_max_amt_count'] > 0) :
          $vtwpr_rule->advancedSettingsDiscountLimits = 'yes';
        break;
    }
  */
    //*****************************************
    //  If errors were found, the error message array will be displayed by the UI on next screen send.
    //*****************************************
    if  ( sizeof($vtwpr_rule->rule_error_message) > 0 ) {
      $vtwpr_rule->rule_status = 'pending';
    } else {
      $vtwpr_rule->rule_status = 'publish';
    }

    $rules_set_found = false;
    $vtwpr_rules_set = get_option( 'vtwpr_rules_set' ); 
    if ($vtwpr_rules_set) {
      $rules_set_found = true;
    }
          
    if ($rules_set_found) {
      $rule_found = false;
      $sizeof_rules_set = sizeof($vtwpr_rules_set);
      for($i=0; $i < $sizeof_rules_set; $i++) {       
         if ($vtwpr_rules_set[$i]->post_id == $post->ID) {
            $vtwpr_rules_set[$i] = $vtwpr_rule;
            $i =  $sizeof_rules_set;
            $rule_found = true;
         }
      }
      if (!$rule_found) {
         $vtwpr_rules_set[] = $vtwpr_rule;        
      } 
    } else {
      $vtwpr_rules_set = array();
      $vtwpr_rules_set[] = $vtwpr_rule;
    }

    if ($rules_set_found) {
      update_option( 'vtwpr_rules_set',$vtwpr_rules_set );
    } else {
      add_option( 'vtwpr_rules_set',$vtwpr_rules_set );
    }
                                                 
    //**************
    //keep a running track of $vtwpr_display_type_in_rules_set   ==> used in apply-rules processing
    //*************
    if ($vtwpr_rule->rule_execution_type  == 'display') {
      $ruleset_has_a_display_rule = 'yes';
    } else { 
      $ruleset_has_a_display_rule = 'no';
      $sizeof_rules_set = sizeof($vtwpr_rules_set);
      for($i=0; $i < $sizeof_rules_set; $i++) { 
         if ($vtwpr_rules_set[$i]->rule_execution_type == 'display') {
            $i =  $sizeof_rules_set;
            $ruleset_has_a_display_rule = 'yes'; 
         }
      }
    } 

   
    if (get_option('vtwpr_ruleset_has_a_display_rule') == true) {
      update_option( 'vtwpr_ruleset_has_a_display_rule',$ruleset_has_a_display_rule );
    } else {
      add_option( 'vtwpr_ruleset_has_a_display_rule',$ruleset_has_a_display_rule );
    }
    //**************        
    
    //nuke the browser session variables in this case - allows clean retest ...
/*  mwn20140414 =>code shifted to top of file...
    if(!isset($_SESSION['session_started'])){
      session_start();    
      header("Cache-Control: no-cache");
      header("Pragma: no-cache");      
    }          
*/
    // mwn20140414 begin => 
        
    if (session_id() == "") {
      session_start();    
    } 
    $_SESSION = array();
    $_SESSION['session_started'] = 'Yes!';  // need to initialize the session prior to destroy 
    session_destroy();   
    session_write_close();
    // mwn20140414 end
    
    return;
  } 
  
  public function vtwpr_validate_rule_scheduling() {
    global $vtwpr_rule, $vtwpr_setup_options;  
    
    $date_valid = true;     
    $loop_ended = 'no';
    $today = date("Y-m-d");

    if ( $vtwpr_setup_options['use_this_timeZone'] == 'none') {
        $vtwpr_rule->rule_error_message[] = array( 
          'insert_error_before_selector' => '#date-line-0',  
          'error_msg'  => __('Scheduling requires setup', 'vtwpr') );
        $date_valid = false; 
    }

    for($t=0; $loop_ended  == 'no'; $t++) {

      if ( (isset($_REQUEST['date-begin-' .$t])) ||
           (isset($_REQUEST['date-end-' .$t])) ) {  
       
        $date = $_REQUEST['date-begin-' .$t];
        
        $vtwpr_rule->periodicByDateRange[$t]['rangeBeginDate'] = $date;

        if (!vtwpr_checkDateTime($date)) {
           $vtwpr_rule->rule_error_red_fields[] = '#date-begin-' .$t. '-error';
           $date_valid = false;
        }

        $date = $_REQUEST['date-end-' .$t];
        $vtwpr_rule->periodicByDateRange[$t]['rangeEndDate'] = $date;
        if (!vtwpr_checkDateTime($date)) {
           $vtwpr_rule->rule_error_red_fields[] = '#date-end-' .$t. '-error';
           $date_valid = false;
        }
      
      } else {
        $loop_ended = true;
        break;        
      }

      if ($vtwpr_rule->periodicByDateRange[$t]['rangeBeginDate'] >  $vtwpr_rule->periodicByDateRange[$t]['rangeEndDate']) {
          $vtwpr_rule->rule_error_message[] = array( 
            'insert_error_before_selector' => '#date-line-0',  
            'error_msg'  => __('End Date must be Greater than or equal to Begin Date.', 'vtwpr') );
          $vtwpr_rule->rule_error_red_fields[] = '#end-date-label-' .$t;
          $date_valid = false;
      }    
      //emergency exit
      if ($t > 9) {
        break; //exit the for loop
      }
    } 
    
    if (!$date_valid) {
      $vtwpr_rule->rule_error_message[] = array( 
            'insert_error_before_selector' => '#vtwpr-rule-scheduling',  
            'error_msg'  => __('Please repair date error.', 'vtwpr') );                   
    }
    
  } 

  public function vtwpr_build_ruleInWords() {
    global $vtwpr_rule;
    
    //Don't process if errors present
  /*  if  ( sizeof($vtwpr_rule->rule_error_message) > 0 ) {
      $vtwpr_rule->ruleInWords = '';
      return;
    }    */
    
    $vtwpr_rule->ruleInWords = ''; 
    
    switch( $vtwpr_rule->rule_template   ) {
      //display templates
      case 'D-storeWideSale':  //Store-Wide Sale with a Percentage or $$ Value Off, at Catalog Display Time - Realtime
      case 'C-storeWideSale':  //Store-Wide Sale with a Percentage or $$ Value Off all Products in the Cart          vtwpr_buy_info(
          $vtwpr_rule->ruleInWords .= $this->vtwpr_show_buy_info();
          $vtwpr_rule->ruleInWords .= $this->vtwpr_show_action_info();
          $vtwpr_rule->ruleInWords .= $this->vtwpr_show_discount_amt();
        break;
      case 'D-simpleDiscount':  //Membership Discount in the Buy Pool Group, at Catalog Display Time - Realtime
      case 'C-simpleDiscount':  //Sale Price by any Buy Pool Group Criteria [Product / Category / Custom Taxonomy Category / Membership / Wholesale] - Cart
          $vtwpr_rule->ruleInWords .= $this->vtwpr_show_buy_info();
          $vtwpr_rule->ruleInWords .= $this->vtwpr_show_action_info();
          $vtwpr_rule->ruleInWords .= $this->vtwpr_show_discount_amt();          
        //  $vtwpr_rule->ruleInWords .= $this->vtwpr_show_pop();
        break;
      default:    
          $vtwpr_rule->ruleInWords .= $this->vtwpr_show_buy_info();
          $vtwpr_rule->ruleInWords .= $this->vtwpr_show_action_info();
          $vtwpr_rule->ruleInWords .= $this->vtwpr_show_discount_amt();          
          $vtwpr_rule->ruleInWords .= $this->vtwpr_show_repeats();
        //  $vtwpr_rule->ruleInWords .= $this->vtwpr_show_pop();
          $vtwpr_rule->ruleInWords .= $this->vtwpr_show_limits();   
        break;
    }
    
    //replace $ with the currency symbol set up on the Parent Plugin!!
    $currency_symbol = vtwpr_get_currency_symbol();
    $vtwpr_rule->ruleInWords = str_replace('$', $currency_symbol, $vtwpr_rule->ruleInWords);
    
  } 
  
  public function vtwpr_show_buy_info() {
    global $vtwpr_rule;  
    $output;    
    switch( $vtwpr_rule->rule_template   ) {
      case 'D-storeWideSale':
          $output .= '<span class="words-line"><span class="words-line-buy">' . __('* For</span><!-- 001 --> any item,', 'vtwpr') . '</span><!-- 001a --><!-- /words-line-->';
          return $output;
        break;      
      case 'D-simpleDiscount': 
          $output .= '<span class="words-line"><span class="words-line-buy">' . __('* For</span><!-- 002 --> any item within the defined Buy group,', 'vtwpr') . '</span><!-- 002a --><!-- /words-line-->';
          return $output;
        break;
      case 'C-storeWideSale':
          $output .= '<span class="words-line"><span class="words-line-buy">' . __('* Buy</span><!-- 003 --> any item,', 'vtwpr') . '</span><!-- 003a --><!-- /words-line-->';
          return $output;
        break;      
      case 'C-simpleDiscount': 
          $output .= '<span class="words-line"><span class="words-line-buy">' . __('* Buy</span><!-- 005 --> any item within the Buy defined group,', 'vtwpr') . '</span><!-- 005a --><!-- /words-line-->';
          return $output;
        break;
      default:
          $output .= '<span class="words-line"><span class="words-line-buy">' . __('* Buy</span><!-- 007 --> ', 'vtwpr') ;
        break;
    }
     
    switch( $vtwpr_rule->rule_deal_info[0]['buy_amt_type']  ) {    
      case 'none':
          $output .= __('any item within the defined Buy group,', 'vtwpr') . '</span><!-- 008 -->';
          return $output;
        break;
      case 'one':
          $output .= __('one item within the defined Buy group,', 'vtwpr') . '</span><!-- 009 -->';
          return $output;
        break; 
      case 'quantity':
          $output .= $vtwpr_rule->rule_deal_info[0]['buy_amt_count'];
          $output .= __(' units', 'vtwpr'); 
        break; 
      case 'currency':
          $output .= '$' . $vtwpr_rule->rule_deal_info[0]['buy_amt_count'];                    
        break;
      case 'nthQuantity':
          $output .= __('every', 'vtwpr'); 
          $output .= '$' . $vtwpr_rule->rule_deal_info[0]['buy_amt_count'];
          $output .= __('th unit ', 'vtwpr');                    
        break;
    }    
 
 
    switch( $vtwpr_rule->rule_deal_info[0]['buy_amt_mod']  ) {
      case 'none':
        break;
      case 'minCurrency':
          $output .= '<br> &nbsp;&nbsp;&nbsp; -'; 
          $output .= __(' for a mininimum of ', 'vtwpr');
          $output .= '$' . $vtwpr_rule->rule_deal_info[0]['buy_amt_mod_count'];
        break; 
      case 'maxCurrency':
          $output .= '<br> &nbsp;&nbsp;&nbsp; -'; 
          $output .= __(' for a maxinimum of ', 'vtwpr');
          $output .= '$' . $vtwpr_rule->rule_deal_info[0]['buy_amt_mod_count'];
        break;               
    }   
    
    switch( $vtwpr_rule->rule_deal_info[0]['buy_amt_applies_to']  ) {
      case 'all':
          $output .= '<br> &nbsp;&nbsp;&nbsp; -'; 
          $output .= __(' within the Buy group', 'vtwpr');
        break;
      case 'each':
          $output .= '<br> &nbsp;&nbsp;&nbsp; -'; 
          $output .= __(' of each product quantity of the defined Buy group', 'vtwpr');
        break;        
    }
    $output .=  '</span><!-- 010 -->';
   
    return $output;   
  } 


    
  public function vtwpr_show_action_info() {
    global $vtwpr_rule;  
    $output;    
    switch( $vtwpr_rule->rule_template   ) {                      
      case 'D-storeWideSale':    
      case 'D-simpleDiscount': 
          $output .= '<span class="words-line"><span class="words-line-get">' .  __('* Get ', 'vtwpr') . '</span><!-- 012 -->';
        break;
      case 'C-storeWideSale':    
      case 'C-simpleDiscount':
      case 'C-discount-inCart':
      case 'C-cheapest-inCart': 
          $output .= '<span class="words-line"><span class="words-line-get">' .  __('* Get ', 'vtwpr') . '</span><!-- 014 -->';
        break;
      case 'C-forThePriceOf-inCart':    //Buy 5, get them for the price of 4/$400
          $output .= '<span class="words-line"><span class="words-line-get">' .  __('* Get ', 'vtwpr') . '</span><!-- 014 -->' .  __('the  Product Filter ', 'vtwpr') . '</span>';
          return $output;
        break;  
      case 'C-discount-Next':
      case 'C-forThePriceOf-Next':     // Buy 5/$500, get next 3 for the price of 2/$200 - Cart
      case 'C-cheapest-Next':
      case 'C-nth-Next':
          $output .= '<span class="words-line"><span class="words-line-get">' .  __('* Get ', 'vtwpr') . '</span><!-- 014 -->' .  __('the Next - ', 'vtwpr');
        break;               
      default:
          $output .= '<span class="words-line"><span class="words-line-get">' .  __('* Get ', 'vtwpr') . '</span><!-- 015 -->';
        break;
    }
     
    switch( $vtwpr_rule->rule_deal_info[0]['action_amt_type']  ) {    
      case 'none':
          $output .= __('any item', 'vtwpr');
          $output .= '<br> &nbsp;&nbsp;&nbsp; -';  
          $output .= __('within the defined Get group,', 'vtwpr'); 
          return $output;
        break;
      case 'one':
          $output .= __('one item', 'vtwpr');
          $output .= '<br> &nbsp;&nbsp;&nbsp; -';  
          $output .= __('within the defined Get group,', 'vtwpr');
          return $output;
        break; 
      case 'quantity':
          $output .= $vtwpr_rule->rule_deal_info[0]['action_amt_count'];
          $output .= __(' units', 'vtwpr'); 
        break; 
      case 'currency':
          $output .= '$' . $vtwpr_rule->rule_deal_info[0]['action_amt_count'];                    
        break;
      case 'nthQuantity':
          $output .= __('every', 'vtwpr'); 
          $output .= '$' . $vtwpr_rule->rule_deal_info[0]['action_amt_count'];
          $output .= __('th unit ', 'vtwpr');                    
        break;
    }    
 
    switch( $vtwpr_rule->rule_deal_info[0]['action_amt_mod']  ) {
      case 'none':
        break;
      case 'minCurrency':
          $output .= '<br> &nbsp;&nbsp;&nbsp; -'; 
          $output .= __(' for a mininimum of ', 'vtwpr');
          $output .= '$' . $vtwpr_rule->rule_deal_info[0]['action_amt_mod_count'];
        break; 
      case 'maxCurrency':
          $output .= '<br> &nbsp;&nbsp;&nbsp; -'; 
          $output .= __(' for a maxinimum of ', 'vtwpr');
          $output .= '$' . $vtwpr_rule->rule_deal_info[0]['action_amt_mod_count'];
        break;               
    }   
    
    switch( $vtwpr_rule->rule_deal_info[0]['action_amt_applies_to']  ) {
      case 'all':
          $output .= '<br> &nbsp;&nbsp;&nbsp; -'; 
          $output .= __(' within the Get group', 'vtwpr');
        break;
      case 'each':
          $output .= '<br> &nbsp;&nbsp;&nbsp; -'; 
          $output .= __(' of each product quantity of the defined Get group', 'vtwpr');
        break;        
    }
    $output .=  '</span><!-- 018 --><!-- /words-line-->';
    
    return $output;   
  }   
 
     
  public function vtwpr_show_discount_amt() {
    global $vtwpr_rule;  
    $output;    
    
    switch( $vtwpr_rule->rule_deal_info[0]['discount_applies_to'] ) {
      case 'each':
          $output .= '<span class="words-line"> &nbsp;&nbsp;&nbsp; -';
          $output .=  __('each product ', 'vtwpr');
        break;
      case 'all': 
          if ( $vtwpr_rule->rule_template != 'C-forThePriceOf-inCart' ) { //Don't show for  "Buy 5, get them for the price of 4/$400"
            $output .= '<span class="words-line"> &nbsp;&nbsp;&nbsp; -';
            $output .=  __('all products', 'vtwpr');
          }
        break;      
      case 'cheapest':
          $output .= '<span class="words-line"> &nbsp;&nbsp;&nbsp; -';
          $output .=  __('cheapest product in the group ', 'vtwpr');
        break;       
      case 'most_expensive':
          $output .= '<span class="words-line"> &nbsp;&nbsp;&nbsp; -';
          $output .=  __('most expensive product in the group ', 'vtwpr');
        break;
      default:
        break; 
    /*  default:
          $output .=  __(' discount_applies_to= ', 'vtwpr');
          $output .=  $vtwpr_rule->rule_deal_info[0]['discount_applies_to'];
          $output .=  __('end ', 'vtwpr');
        break;   */
    }
    
    $output .= '</span><!-- 018b --><span class="words-line"><span class="words-line-get">';
    $output .= __('* For ', 'vtwpr') . '</span><!-- 018c -->';  
    
    switch( $vtwpr_rule->rule_deal_info[0]['discount_amt_type'] ) {
      case 'percent':
          $output .=  $vtwpr_rule->rule_deal_info[0]['discount_amt_count'] . __('% off', 'vtwpr');
        break;
      case 'currency': 
          $amt = vtwpr_format_money_element( $vtwpr_rule->rule_deal_info[0]['discount_amt_count'] );
          $output .= $amt . __(' off', 'vtwpr');
        break;      
      case 'fixedPrice':
          $amt = vtwpr_format_money_element( $vtwpr_rule->rule_deal_info[0]['discount_amt_count'] );
          $output .= $amt;
        break;       
      case 'free':
          $output .=  __('Free', 'vtwpr');
        break;
      case 'forThePriceOf_Units': 
      case 'forThePriceOf_Currency':
         $output .=  __('the Group Price of $', 'vtwpr');
         $output .=  $vtwpr_rule->rule_deal_info[0]['discount_amt_count']; 
        break;      
    }  
        
    switch( $vtwpr_rule->rule_template   ) {
      case 'D-storeWideSale':
      case 'D-simpleDiscount': 
          $output .= '<br> &nbsp;&nbsp;&nbsp; -'; 
          $output .=  __(' when catalog displays.', 'vtwpr')  . '</span><!-- 019 -->'; //'</span><!-- 019 --> </span><!-- 019a -->';
        break;
      default:
          $output .= '<br> &nbsp;&nbsp;&nbsp; -'; 
          $output .=  __(' when added to cart.', 'vtwpr') . '</span><!-- 020 -->'; //'</span><!-- 020 --> </span><!-- 020a -->';
        break;
    }       
    
    return $output;
  }
 
  
  public function vtwpr_show_pop() {  
    global $vtwpr_rule;  
   
    $output = '<span class="words-line extra-top-margin">';  
    
    $output .= '&nbsp;&nbsp;&nbsp; -';
    switch( $vtwpr_rule->inPop ) {
      case 'wholeStore':                                                                                      
          if ( ($vtwpr_rule->actionPop == 'sameAsInPop') ||              //in these cases, inpop/actionpop treated as 'sameAsInPop'
               ($vtwpr_rule->actionPop == 'wholeStore') ||
               ($vtwpr_rule->actionPop == 'cart') ) {
            $output .=  __(' Acts on the Whole Store ', 'vtwpr'); 
          }  else {
            $output .=  __(' The  Product Filter is the Whole Store ', 'vtwpr');
          }         
        break;
      case 'cart':                                                                                      
          if ( ($vtwpr_rule->actionPop == 'sameAsInPop') ||              //in these cases, inpop/actionpop treated as 'sameAsInPop'
               ($vtwpr_rule->actionPop == 'wholeStore') ||
               ($vtwpr_rule->actionPop == 'cart') ) {
            $output .=  __(' Acts on the any Product in the Cart ', 'vtwpr'); 
          }  else {
            $output .=  __('  Product Filter is any Product in the Cart ', 'vtwpr');
          }                              
        break;
  
      //EDITED * + * +  * + * +  * + * +  * + * + * + * +  * + * +  * + * +  * + * + 
             
    }

   switch( $vtwpr_rule->actionPop ) { 
      case 'sameAsInPop':
      case 'wholeStore':;           
      case 'cart':
        //all done, all processing completed while handling inpop above                                                                                     
        break;
  
    //EDITED * + * +  * + * +  * + * +  * + * + * + * +  * + * +  * + * +  * + * +
    
    }      
/*   
     //**********************************************************
     If inpop = ('wholeStore' or 'cart') and actionpop = ('sameAsInPop' or 'wholeStore' or 'cart')
        inpop and actionpop are treated as a single group ('sameAsInPop'), and the 'ball' bounces between them.
     //**********************************************************
        //logic from apply-rules.php:
        switch( $vtwpr_rules_set[$i]->inpop ) {
          case 'wholeStore':
          case 'cart':        //in these cases, inpop/actionpop treated as 'sameAsInPop'                                                                               
              if ( ($vtwpr_rules_set[$i]->actionPop == 'sameAsInPop') ||              
                   ($vtwpr_rules_set[$i]->actionPop == 'wholeStore') ||
                   ($vtwpr_rules_set[$i]->actionPop == 'cart') ) {
                $vtwpr_rules_set[$i]->actionPop = 'sameAsInPop';
                $vtwpr_rules_set[$i]->discountAppliesWhere =  'nextInInPop' ;
              }   
            break; 
        }  

*/   

     $output .= '</span><!-- 021 -->';
     
    return $output; 
  } 
  
 
  public function vtwpr_show_repeats() {
    global $vtwpr_rule;  
    $output;
     
    
    switch( $vtwpr_rule->rule_deal_info[0]['action_repeat_condition'] ) {
      case 'none':
        break;
      case 'unlimited': 
          $output .= '<span class="words-line extra-top-margin"><em>'; //here due to 'none'
          $output .=  __('Once the Buy group threshhold has been reached, the action group repeats an unlimited number of times. ', 'vtwpr');
          $output .=  '</em></span><!-- 023 -->';
        break;      
      case 'count':
          $output .= '<span class="words-line extra-top-margin"><em>';
          $output .=  __('Once the Buy group threshhold has been reached, the action group repeats ', 'vtwpr'); 
          $output .=  $vtwpr_rule->rule_deal_info[0]['action_repeat_count'];
          $output .=  __(' times. ', 'vtwpr');
          $output .=  '</em></span><!-- 024 -->';
        break;       
    }
    
        
    switch( $vtwpr_rule->rule_deal_info[0]['buy_repeat_condition'] ) {
      case 'none':
        break;
      case 'unlimited': 
          $output .= '<span class="words-line extra-top-margin"><em>';
          $output .=  __('The entire rule repeats an unlimited number of times. ', 'vtwpr');
          $output .=  '</em></span><!-- 024 -->';
        break;      
      case 'count':
          $output .= '<span class="words-line extra-top-margin"><em>';
          $output .=  __('The entire rule repeats ', 'vtwpr');  
          $output .=  $vtwpr_rule->rule_deal_info[0]['buy_repeat_count'];
          $output .=  __(' times. ', 'vtwpr');
          $output .=  '</em></span><!-- 024 -->';
        break;       
    }
    
    return $output;
  }  
  
 
  public function vtwpr_show_limits() {
    global $vtwpr_rule;  
    $output;
        
    switch( $vtwpr_rule->rule_deal_info[0]['discount_rule_max_amt_type']) {
      case 'none':
        break;
      case 'percent':
          $output .=  __(' Discount Cart Maximum set at ', 'vtwpr');
          $output .= $vtwpr_rule->rule_deal_info[0]['discount_rule_max_amt_count'];
          $output .=  __('% ', 'vtwpr');
        break;
      case 'quantity':
          $output .=  __(' Discount Cart Maximum set at ', 'vtwpr');
          $output .= $vtwpr_rule->rule_deal_info[0]['discount_rule_max_amt_count'];
          $output .=  __(' times it can be applied. ', 'vtwpr');
        break;
      case 'currency':
          $output .=  __(' Discount Cart Maximum set at $$', 'vtwpr');
          $output .= $vtwpr_rule->rule_deal_info[0]['discount_rule_max_amt_count'];      
        break; 
    }
        
    switch( $vtwpr_rule->rule_deal_info[0]['discount_lifetime_max_amt_type']) {
      case 'none':
        break;
      case 'percent':
          $output .=  __(' Discount Lifetime Maximum set at ', 'vtwpr');
          $output .= $vtwpr_rule->rule_deal_info[0]['discount_lifetime_max_amt_count'];
          $output .=  __('% ', 'vtwpr');
        break;
      case 'quantity':
          $output .=  __(' Discount Lifetime Maximum set at ', 'vtwpr');
          $output .= $vtwpr_rule->rule_deal_info[0]['discount_lifetime_max_amt_count'];
          $output .=  __(' times it can be applied. ', 'vtwpr');
        break;
      case 'currency':
          $output .=  __(' Discount Lifetime Maximum set at $$', 'vtwpr');
          $output .= $vtwpr_rule->rule_deal_info[0]['discount_lifetime_max_amt_count'];      
        break; 
    }    
        
    switch( $vtwpr_rule->rule_deal_info[0]['discount_rule_cum_max_amt_type']) {
      case 'none':
        break;
      case 'percent':
          $output .=  __(' Discount Cumulative Maximum set at ', 'vtwpr');
          $output .= $vtwpr_rule->rule_deal_info[0]['discount_rule_cum_max_amt_count'];
          $output .=  __('% ', 'vtwpr');
        break;
      case 'quantity':
          $output .=  __(' Discount Cumulative_cum Maximum set at ', 'vtwpr');
          $output .= $vtwpr_rule->rule_deal_info[0]['discount_rule_cum_max_amt_count'];
          $output .=  __(' times it can be applied. ', 'vtwpr');
        break;
      case 'currency':
          $output .=  __(' Discount Cumulative Maximum set at $$', 'vtwpr');
          $output .= $vtwpr_rule->rule_deal_info[0]['discount_rule_cum_max_amt_count'];      
        break; 
    }   
      
             
    return $output;
  }    
  
/*  
 //default to 'OR', as the default value goes away and may be needed if the user switches back to 'groups'...
  public function vtwpr_set_default_or_values_in() {
    global $vtwpr_rule;  
   // $vtwpr_rule->role_and_or_in[1]['user_input'] = 's'; //'s' = 'selected'
    $vtwpr_rule->role_and_or_in = 'or';
  } */
 /*
 //default to 'OR', as the default value goes away and may be needed if the user switches back to 'groups'...
  public function vtwpr_set_default_or_values_out() {
    global $vtwpr_rule;  
   // $vtwpr_rule->role_and_or_out[1]['user_input'] = 's'; //'s' = 'selected'
    $vtwpr_rule->role_and_or_out = 'or';
  }   */

  public function vtwpr_initialize_deal_structure_framework() {
    global $vtwpr_deal_structure_framework;
    foreach( $vtwpr_deal_structure_framework as $key => $value ) { 
    //for($i=0; $i < sizeof($vtwpr_deal_field_name_array); $i++) {
       $vtwpr_deal_structure_framework[$value] = '';
       //FIX THIS -> BUG where the foreach goes beyond the end of the $vtwpr_deal_structure_framework - emergency eXIT
       if ($key == 'discount_rule_cum_max_amt_count') {
         break; //emergency end of the foreach...
       }            
    }     
  }
  
  //**********************
  // DEAL Line Edits
  //**********************
  public function vtwpr_edit_deal_info_line($active_field_count, $active_line_count, $k ) {
    global $vtwpr_rule, $vtwpr_deal_structure_framework, $vtwpr_deal_edits_framework;
   
    $skip_amt_edit_dropdown_values  =  array('once', 'none' , 'zero', 'one' , 'unlimited', 'each', 'all', 'cheapest', 'most_expensive');
   
   //FIX THIS LATER!!!!!!!!!!!!!!!
   /* if ($active_field_count == 0) { 
      if ( !isset( $_REQUEST['dealInfoLine_' . ($k + 1) ] ) ) {  //if we're on the last line onscreen
         if ($k == 0) { //if the 1st line is the only line 
            $vtwpr_rule->rule_error_message[] = array( 'insert_error_before_selector' => '#rule_deal_info_line.0',  //errmsg goes before the 1st line onscreen
                                                        'error_msg'  => __('Deal Info Line must be filled in, for the rule to be valid.', 'vtwpr')  );
          }  else {
            $vtwpr_rule->rule_error_message[] = array( 'insert_error_before_selector' => '#rule_deal_info_line.' .$k,  //errmsg goes before current onscreen line
                                                        'error_msg'  => __('At least one Deal Info Line must be filled in, for the rule to be valid.', 'vtwpr')  );        
          }
        
      } else {    //this empty line is not the last...
            $vtwpr_rule->rule_error_message[] = array( 'insert_error_before_selector' => '#rule_deal_info_line.' .$k,  //errmsg goes before current onscreen line
                                                       'error_msg'  => __('Deal Info Line is not filled in.  Please delete the line.', 'vtwpr')  );      
      }
      return;
    }    */

  
    //Go through all of the possible deal structure fields            
    foreach( $vtwpr_deal_edits_framework as $fieldName => $fieldAttributes ) {      
       /* ***********************
       special handling for  discount_rule_max_amt_type, discount_lifetime_max_amt_type.  Even though they appear iteratively in deal info,
       they are only active on the '0' occurrence line.  further, they are displayed only AFTER all of the deal lines are displayed
       onscreen... This is actually a kluge, done to utilize the complete editing already available here for a  dropdown and an associated amt field.
       The ui-php points to the '0' iteration of the deal data, when displaying these fields.
       *********************** */
       if ( ($fieldName == 'discount_rule_max_amt_type' )     || ($fieldName == 'discount_rule_max_amt_count' ) ||
            ($fieldName == 'discount_rule_cum_max_amt_type' ) || ($fieldName == 'discount_rule_cum_max_amt_count' ) ||
            ($fieldName == 'discount_lifetime_max_amt_type' ) || ($fieldName == 'discount_lifetime_max_amt_count' ) ) {
          //only process these combos on the 1st iteration only!!
          if ($k > 0) {
             break;
          }
       }

      $field_has_an_error = 'no'; 
      //if the DEAL STRUCTURE KEY field name is in the RULE EDITS array
      if ( $fieldAttributes['edit_is_active'] ) {   //if field active for this template selection
        $dropdown_status; //init variable
        $dropdown_value;  //init variable  
        switch( $fieldAttributes['field_type'] ) {
          case 'dropdown':                   
                if ( ( $vtwpr_deal_structure_framework[$fieldName] == '0' ) || ($vtwpr_deal_structure_framework[$fieldName] == ' ' ) || ($vtwpr_deal_structure_framework[$fieldName] == ''  ) ) {   //dropdown value not selected
                    if ( $fieldAttributes['required_or_optional'] == 'required' ) {                          
                      $vtwpr_rule->rule_error_message[] = array( 
                        'insert_error_before_selector' => $fieldAttributes['insert_error_before_selector']. '_' . $k,  //errmsg goes before current onscreen line
                        'error_msg'  => $fieldAttributes['field_label'] . __(' is required. Please select an option.', 'vtwpr') );
                      $vtwpr_rule->rule_error_red_fields[] = '#' . $fieldName . '_label_' .$k ; 
                      $vtwpr_rule->rule_error_box_fields[] = '#' . $fieldName . '_' .$k ;        
                      $dropdown_status = 'error';
                      $field_has_an_error = 'yes';
                    }  else {
                       $dropdown_status = 'notSelected'; //optional, still at title, Nothing selected
                    }
                } else {  //something selected
                  //standard 'selected' path              
                  $dropdown_status = 'selected';
                  $dropdown_value  =  $vtwpr_deal_structure_framework[$fieldName];
                } 
             break;

          case 'amt':   //amt is ALWAYS preceeded by a dropdown of some sort...
              //clear the amt field if the matching dropdown is not selected
              if ($dropdown_status == 'notSelected') {
                 $vtwpr_deal_structure_framework[$fieldName] = ''; //initialize the amt field
                 break;
              }
              //clear the amt field if the matching dropdown is selected, but has a value of  'none', etc.. [values not requiring matching amt]
              $dropdown_values_with_no_amt = array('none', 'unlimited', 'zero', 'one', 'no', 'free', 'each', 'all', 'cheapest', 'most_expensive');
              if ( ($dropdown_status == 'selected') && (in_array($dropdown_value, $dropdown_values_with_no_amt)) ) {
                 $vtwpr_deal_structure_framework[$fieldName] = ''; //initialize the amt field
                 break;              
              }                           
             
              // if 'once', 'none' , 'unlimited' on dropdown , then amt field not relevant.
              if ( ($dropdown_status == 'selected') &&  ( in_array($dropdown_value, $skip_amt_edit_dropdown_values) )  ) {                                      
                break;
              }                         
              
              $vtwpr_deal_structure_framework[$fieldName] =  preg_replace('/[^0-9.]+/', '', $vtwpr_deal_structure_framework[$fieldName]); //remove leading/trailing spaces, percent sign, dollar sign
              if ( !is_numeric($vtwpr_deal_structure_framework[$fieldName]) ) {  // not numeric covers it all....
                 if ($dropdown_status == 'selected') { //only produce err msg if previous dropdown status=selected [otherwise amt field cannot be entered]              
                    if  ($vtwpr_deal_structure_framework[$fieldName] <= ' ') {  //if blank, use 'required' msg
                        if ( $fieldAttributes['required_or_optional'] == 'required' ) {
                           $error_msg = $fieldAttributes['field_label'] . 
                                        __(' is required. Please enter a value.', 'vtwpr'); 
                        } else {
                           $error_msg = $fieldAttributes['field_label'] . 
                                        __(' must have a value when a count option chosen in ', 'vtwpr') .
                                        $fieldAttributes['matching_dropdown_label'];
                                                       
                        }
                     } else { //something entered but not numeric...
                        if ( $fieldAttributes['required_or_optional'] == 'required' ) {
                           $error_msg = $fieldAttributes['field_label'] . 
                                        __(' is required and not numeric. Please enter a numeric value <em>only</em>.', 'vtwpr');
                        } else {
                           $error_msg = $fieldAttributes['field_label'] . 
                                        __(' is not numeric, and must have a value value when a count option chosen in ', 'vtwpr') .
                                        $fieldAttributes['matching_dropdown_label'];                             
                        }                         
                     }
                     
                     $vtwpr_rule->rule_error_message[] = array( 
                        'insert_error_before_selector' => $fieldAttributes['insert_error_before_selector'] . '_' . $k,  //errmsg goes before current onscreen line
                        'error_msg'  => $error_msg ); 
                     //$vtwpr_rule->rule_error_red_fields[] = '#' . $fieldName . '_label_' .$k ;
                     $vtwpr_rule->rule_error_red_fields[] = $fieldAttributes['matching_dropdown_label_id'] . '_' .$k ;
                     $vtwpr_rule->rule_error_box_fields[] = '#' . $fieldName . '_' .$k ;   
                     $field_has_an_error = 'yes';                            
                 } //end  if 'selected' 
                 //THIS path exits here  
              } else {  
                //SPECIAL NUMERIC EDITS, PRN                  
                 switch( $dropdown_value ) {
                    case 'quantity':
                    case 'forThePriceOf_Units':                                           //only allow whole numbers
                        if ($vtwpr_deal_structure_framework[$fieldName] <= 0) {
                           $vtwpr_rule->rule_error_message[] = array( 
                              'insert_error_before_selector' => $fieldAttributes['insert_error_before_selector']. '_' . $k,  //errmsg goes before current onscreen line
                              'error_msg'  => $fieldAttributes['field_label'] .  __(' - when Units are selected, the number must be greater than zero. ', 'vtwpr') );
                           $vtwpr_rule->rule_error_red_fields[] = '#' . $fieldName . '_label_' .$k ;
                           $vtwpr_rule->rule_error_box_fields[] = '#' . $fieldName . '_' .$k ;                         
                           $field_has_an_error = 'yes';
                        } else {
                          $number_of_decimal_places = vtwpr_numberOfDecimals( $vtwpr_deal_structure_framework[$fieldName] ) ;
                          if ( $number_of_decimal_places > 0 ) {           
                             $vtwpr_rule->rule_error_message[] = array( 
                                'insert_error_before_selector' => $fieldAttributes['insert_error_before_selector']. '_' . $k,  //errmsg goes before current onscreen line
                                'error_msg'  => $fieldAttributes['field_label'] .  __(' - when Units are selected, no decimals are allowed. ', 'vtwpr') );
                             $vtwpr_rule->rule_error_red_fields[] = '#' . $fieldName . '_label_' .$k ;
                             $vtwpr_rule->rule_error_box_fields[] = '#' . $fieldName . '_' .$k ; 
                             $field_has_an_error = 'yes';
                          }                        
                        }                            
                      break;
                    case 'forThePriceOf_Currency':  // (only on discount_amt_type)
                        if ( $vtwpr_deal_structure_framework[$fieldName] <= 0 ) {           
                           $vtwpr_rule->rule_error_message[] = array( 
                              'insert_error_before_selector' => $fieldAttributes['insert_error_before_selector'] . '_' . $k,  //errmsg goes before current onscreen line
                              'error_msg'  => $fieldAttributes['field_label'] .  __(' - when For the Price of (Currency) is selected, the amount must be greater than zero. ', 'vtwpr') );
                           $vtwpr_rule->rule_error_red_fields[] = '#' . $fieldName . '_label_' .$k ;
                           $vtwpr_rule->rule_error_box_fields[] = '#' . $fieldName . '_' .$k ; 
                        } else {
                          $number_of_decimal_places = vtwpr_numberOfDecimals( $vtwpr_deal_structure_framework[$fieldName] ) ;
                          if ( $number_of_decimal_places > 2 ) {           
                             $vtwpr_rule->rule_error_message[] = array( 
                                'insert_error_before_selector' => $fieldAttributes['insert_error_before_selector']. '_' . $k,  //errmsg goes before current onscreen line
                                'error_msg'  => $fieldAttributes['field_label'] .  __(' - when For the Price of (Currency) is selected, up to 2 decimal places <em>only</em>  are allowed. ', 'vtwpr') );
                             $vtwpr_rule->rule_error_red_fields[] = '#' . $fieldName . '_label_' .$k ;
                             $vtwpr_rule->rule_error_box_fields[] = '#' . $fieldName . '_' .$k ; 
                          }
                        }                           
                      break;  
                    case 'currency':
                        if ( $vtwpr_deal_structure_framework[$fieldName] <= 0 ) {           
                           $vtwpr_rule->rule_error_message[] = array( 
                              'insert_error_before_selector' => $fieldAttributes['insert_error_before_selector'] . '_' . $k,  //errmsg goes before current onscreen line
                              'error_msg'  => $fieldAttributes['field_label'] .  __(' - when Currency is selected, the amount must be greater than zero. ', 'vtwpr') );
                           $vtwpr_rule->rule_error_red_fields[] = '#' . $fieldName . '_label_' .$k ;
                           $vtwpr_rule->rule_error_box_fields[] = '#' . $fieldName . '_' .$k ; 
                           $field_has_an_error = 'yes';
                        } else {
                          $number_of_decimal_places = vtwpr_numberOfDecimals( $vtwpr_deal_structure_framework[$fieldName] ) ;
                          if ( $number_of_decimal_places > 2 ) {           
                             $vtwpr_rule->rule_error_message[] = array( 
                                'insert_error_before_selector' => $fieldAttributes['insert_error_before_selector']. '_' . $k,  //errmsg goes before current onscreen line
                                'error_msg'  => $fieldAttributes['field_label'] .  __(' - when Currency is selected, up to 2 decimal places <em>only</em>  are allowed. ', 'vtwpr') );
                             $vtwpr_rule->rule_error_red_fields[] = '#' . $fieldName . '_label_' .$k ;
                             $vtwpr_rule->rule_error_box_fields[] = '#' . $fieldName . '_' .$k ; 
                             $field_has_an_error = 'yes';
                          }
                        }                             
                      break;
                    case 'fixedPrice':   // (only on discount_amt_type)
                        if ( $vtwpr_deal_structure_framework[$fieldName] <= 0 ) {           
                           $vtwpr_rule->rule_error_message[] = array( 
                              'insert_error_before_selector' => $fieldAttributes['insert_error_before_selector'] . '_' . $k,  //errmsg goes before current onscreen line
                              'error_msg'  => $fieldAttributes['field_label'] .  __(' - when Fixed Price is selected, the amount must be greater than zero. ', 'vtwpr') );
                           $vtwpr_rule->rule_error_red_fields[] = '#' . $fieldName . '_label_' .$k ;
                           $vtwpr_rule->rule_error_box_fields[] = '#' . $fieldName . '_' .$k ; 
                           $field_has_an_error = 'yes';
                        } else {
                          $number_of_decimal_places = vtwpr_numberOfDecimals( $vtwpr_deal_structure_framework[$fieldName] ) ;
                          if ( $number_of_decimal_places > 2 ) {           
                             $vtwpr_rule->rule_error_message[] = array( 
                                'insert_error_before_selector' => $fieldAttributes['insert_error_before_selector']. '_' . $k,  //errmsg goes before current onscreen line
                                'error_msg'  => $fieldAttributes['field_label'] .  __(' - when Fixed Price is selected, up to 2 decimal places <em>only</em>  are allowed. ', 'vtwpr') );
                             $vtwpr_rule->rule_error_red_fields[] = '#' . $fieldName . '_label_' .$k ;
                             $vtwpr_rule->rule_error_box_fields[] = '#' . $fieldName . '_' .$k ; 
                             $field_has_an_error = 'yes';
                          }                        
                        }                             
                      break;
                    case 'percent':
                        if ( $vtwpr_deal_structure_framework[$fieldName] <= 0 ) {           
                           $vtwpr_rule->rule_error_message[] = array( 
                              'insert_error_before_selector' => $fieldAttributes['insert_error_before_selector'] . '_' . $k,  //errmsg goes before current onscreen line
                              'error_msg'  => $fieldAttributes['field_label'] .  __(' - when Percent is selected, the amount must be greater than zero. ', 'vtwpr') );
                           $vtwpr_rule->rule_error_red_fields[] = '#' . $fieldName . '_label_' .$k ;
                           $vtwpr_rule->rule_error_box_fields[] = '#' . $fieldName . '_' .$k ; 
                           $field_has_an_error = 'yes';
                        } else {
                          if ( $vtwpr_deal_structure_framework[$fieldName] < 1 ) {           
                             $vtwpr_rule->rule_error_message[] = array( 
                                'insert_error_before_selector' => $fieldAttributes['insert_error_before_selector'] . '_' . $k,  //errmsg goes before current onscreen line
                                'error_msg'  => $fieldAttributes['field_label'] .  __(' - the Percent value must be greater than 1.  For example 10% would be "10", not ".10" . ', 'vtwpr') );
                             $vtwpr_rule->rule_error_red_fields[] = '#' . $fieldName . '_label_' .$k ;
                             $vtwpr_rule->rule_error_box_fields[] = '#' . $fieldName . '_' .$k ; 
                             $field_has_an_error = 'yes';
                          } 
                         }                                  
                      break;
                    case '':
                      break;
                } //end switch
              } //end amount numeric testing       
            break;
         
         case 'text':
              if ( ($vtwpr_deal_structure_framework[$fieldName] <= ' ') && ( $fieldAttributes['required_or_optional'] == 'required' ) ) {  //error possible only if blank                        
                        $vtwpr_rule->rule_error_message[] = array( 
                          'insert_error_before_selector' => $fieldAttributes['insert_error_before_selector'] . '_' . $k,  //errmsg goes before current onscreen line
                          'error_msg'  => $fieldAttributes['field_label'] . __(' is required. Please enter a description.', 'vtwpr') );
                        $vtwpr_rule->rule_error_red_fields[] = '#' . $fieldName . '_label_' .$k ;
                        $vtwpr_rule->rule_error_box_fields[] = '#' . $fieldName . '_' .$k ;  
                        $field_has_an_error = 'yes';
              }
            break;
        } //end switch
      }  else {
        //if this field doesn't have an active edit and hence is not allowed, clear it out in the DEAL STRUCTURE.
        $vtwpr_deal_structure_framework[$fieldName] = '';
      }

      //*******************************
      //Template-Level and Cross-field edits
      //*******************************      
      //This picks up the template_profile_error_msg if appropriate, 
      //  and if no other error messages already created
      if ($field_has_an_error == 'no') {
        switch( $fieldAttributes['allowed_values'] ) {
            case 'all':    //all values are allowed
              break;
            case '':       //no values are allowed
                if ( ($vtwpr_deal_structure_framework[$fieldName] > ' ') && ($fieldAttributes['template_profile_error_msg'] > ' ' ) ) {
                  $field_has_an_error = 'yes';
                  $display_this_msg = $fieldAttributes['template_profile_error_msg'];
                  $insertBefore = $fieldAttributes['insert_error_before_selector'];
                  $this->vtwpr_add_cross_field_error_message($insertBefore, $k, $display_this_msg, $fieldName);
                }                      
              break;              
            default:  //$fieldAttributes['allowed_values'] is an array!
                //check for valid values
                if ( !in_array($vtwpr_deal_structure_framework[$fieldName], $fieldAttributes['allowed_values']) ) {  
                  $field_has_an_error = 'yes';
                  $display_this_msg = $fieldAttributes['template_profile_error_msg'];
                  $insertBefore = $fieldAttributes['insert_error_before_selector'];
                  $this->vtwpr_add_cross_field_error_message($insertBefore, $k, $display_this_msg, $fieldName);
                }
              break;
        }

        //Cross-field edits
        $sizeof_cross_field_edits = sizeof($fieldAttributes['cross_field_edits']);
        if ( ($field_has_an_error == 'no') && ($sizeof_cross_field_edits > 0) ) {
          for ( $c=0; $c < $sizeof_cross_field_edits; $c++) {
              //if current field values fall within value array that the cross-edit applies to
              if ( in_array($vtwpr_deal_structure_framework[$fieldName], $fieldAttributes['cross_field_edits'][$c]['applies_to_this_field_values']) ) {               
                 $cross_field_name = $fieldAttributes['cross_field_edits'][$c]['cross_field_name'];
                 if ( !in_array($vtwpr_deal_structure_framework[$cross_field_name], $fieldAttributes['cross_field_edits'][$c]['cross_allowed_values']) ) {  
                    //special handling for these 2, as they're not in the standard edit framwork, and we don't have the values yet
                    if ( ($fieldName = 'discount_auto_add_free_product') &&
                        (($cross_field_name == 'popChoiceOut') ||
                         ($cross_field_name == 'cumulativeCouponPricing')) ) {
                        
                        if ($cross_field_name == 'popChoiceOut') {
                          $field_value_temp = $_REQUEST['popChoiceOut'];
                        } else {
                          $field_value_temp = $_REQUEST['cumulativeCouponPricing'];
                        }
                        
                        if ( !in_array($field_value_temp, $fieldAttributes['cross_field_edits'][$c]['cross_allowed_values']) ) { 
                          $field_has_an_error = 'yes';
                          $display_this_msg = $fieldAttributes['cross_field_edits'][$c]['cross_error_msg'];
                          $insertBefore = $fieldAttributes['cross_field_edits'][$c]['cross_field_insertBefore'];
                          $vtwpr_rule->rule_error_red_fields[] = '#' . $cross_field_name . '_label_' .$k ;
                          //custom error name
                          //this cross-edit name wasn't being picked up correctly...                    
                          $this->vtwpr_add_cross_field_error_message($insertBefore, $k, $display_this_msg, $fieldName);	 
                        } else {
                          
                          if ($cross_field_name == 'popChoiceOut') {
                          
                                
                                //EDITED * + * +  * + * +  * + * +  * + * + * + * +  * + * +  * + * +  * + * +             
                                                                                
                          
                          }
                          
                        }
                        
                    } else {
                      //Normal error processing
                      $field_has_an_error = 'yes';
                      $display_this_msg = $fieldAttributes['cross_field_edits'][$c]['cross_error_msg'];
                      $insertBefore = $fieldAttributes['cross_field_edits'][$c]['cross_field_insertBefore'];
                      $vtwpr_rule->rule_error_red_fields[] = '#' . $cross_field_name . '_label_' .$k ;
                      //custom error name
                      //this cross-edit name wasn't being picked up correctly...                    
                      $this->vtwpr_add_cross_field_error_message($insertBefore, $k, $display_this_msg, $fieldName);	 
                      
                    }

              }
            }
          } //end for cross-edit loop       
        } //END Template-Level and Cross-field edits

      } //end if no-error 
      
       
    }  //end foreach

    return;
  }

  public function vtwpr_add_cross_field_error_message($insertBefore, $k, $display_this_msg, $fieldName) { 
    global $vtwpr_rule, $vtwpr_deal_structure_framework;
    $vtwpr_rule->rule_error_message[] = array( 
      'insert_error_before_selector' =>  $insertBefore . '_' . $k,  //errmsg goes before current onscreen line
      'error_msg'  => $display_this_msg 
      );
    //  'error_msg'  => $fieldAttributes['field_label'] . ' ' .$display_this_msg );
    $vtwpr_rule->rule_error_red_fields[] = '#' . $fieldName . '_label_' .$k ;  
  }
  
    
  public function vtwpr_dump_deal_lines_to_rule() {  
     global $vtwpr_rule, $vtwpr_deal_structure_framework;
     $deal_iterations_done = 'no'; //initialize variable

     for($k=0; $deal_iterations_done == 'no'; $k++) {      
       if ( (isset( $_REQUEST['buy_repeat_condition_' . $k] )) && (!empty( $_REQUEST['buy_repeat_condition_' . $k] )) ) {    //is a deal line there? always 1 at least...
         //INITIALIZE was introducing an iteration error!!!!!!!!          
         //$this->vtwpr_initialize_deal_structure_framework();       
         foreach( $vtwpr_deal_structure_framework as $key => $value ) {   //spin through all of the screen fields  
            $vtwpr_deal_structure_framework[$key] = $_REQUEST[$key . '_' .$k];        
         }                 
         $vtwpr_rule->rule_deal_info[] = $vtwpr_deal_structure_framework;   //add each line to rule, regardless if empty              
       } else {     
         $deal_iterations_done = 'yes';
       }
     }		  
  }
  
  public function vtwpr_build_deal_edits_framework() {
    global $vtwpr_rule, $vtwpr_template_structures_framework, $vtwpr_deal_edits_framework;
    
    //mwn20140414
    if ($vtwpr_rule->rule_template <= '0') {
        return; 
    }
        
    // previously determined template key
    $templateKey = $vtwpr_rule->rule_template; 
    $additional_template_rule_switches = array ( 'discountAppliesWhere' ,  'inPopAllowed' , 'actionPopAllowed'  , 'cumulativeRulePricingAllowed', 'cumulativeSalePricingAllowed', 'replaceSalePricingAllowed', 'cumulativeCouponPricingAllowed') ;
    $nextInActionPop_templates = array ( 'C-discount-Next', 'C-forThePriceOf-Next', 'C-cheapest-Next', 'C-nth-Next' );
 
    foreach( $vtwpr_template_structures_framework[$templateKey] as $key => $value ) {            
      //check for addtional template switches first ==> they are stored in this framework for convenience only.
      if ( in_array($key, $additional_template_rule_switches) ) {
        switch( $key ) {
            case 'discountAppliesWhere':               // 'allActionPop' / 'inCurrentInPopOnly'  / 'nextInInPop' / 'nextInActionPop' / 'inActionPop' /
              // if template set to nextInActionPop, check if it should be overwritten...
              //this is a duplicate field load, done here in advance PRN 
              //OVERWRITE discountAppliesWhere TO GUIDE THE APPLY LOGIC AS TO WHICH GROUP WILL BE ACTED UPON 
              $vtwpr_rule->actionPop = $_REQUEST['popChoiceOut'];
              if ( (in_array($templateKey, $nextInActionPop_templates))  &&
                   ($vtwpr_rule->actionPop == 'sameAsInPop') ) {
                $vtwpr_rule->discountAppliesWhere =  'nextInInPop';
              } else {
                $vtwpr_rule->discountAppliesWhere = $value;
              }             
            break;
          case 'inPopAllowed':
              $vtwpr_rule->inPopAllowed = $value;
            break; 
          case 'actionPopAllowed':
              $vtwpr_rule->actionPopAllowed = $value;
            break;            
          case 'cumulativeRulePricingAllowed':
              $vtwpr_rule->cumulativeRulePricingAllowed = $value; 
            break;
          case 'cumulativeSalePricingAllowed':
              $vtwpr_rule->cumulativeSalePricingAllowed = $value; 
            break;
          case 'replaceSalePricingAllowed':
              $vtwpr_rule->replaceSalePricingAllowed = $value; 
            break;            
          case 'cumulativeCouponPricingAllowed':
              $vtwpr_rule->cumulativeCouponPricingAllowed = $value; 
            break;
        }
      } else {      
        if ( ($value['required_or_optional'] == 'required') || ($value['required_or_optional'] == 'optional') ) {
          //update required/optional, $key = field name, same relative value across both frameworks...
          $vtwpr_deal_edits_framework[$key]['edit_is_active']       = 'yes';
          $vtwpr_deal_edits_framework[$key]['required_or_optional'] = $value['required_or_optional'];         
        } else {
          $vtwpr_deal_edits_framework[$key]['edit_is_active']       = '';
        }
        
        $vtwpr_deal_edits_framework[$key]['allowed_values']  =  $value['allowed_values'];
        $vtwpr_deal_edits_framework[$key]['template_profile_error_msg']  =  $value['template_profile_error_msg'];
        
        //cross_field_edits is an array which ***will only exist where required ****
        if ($value['cross_field_edits']) {
           $vtwpr_deal_edits_framework[$key]['cross_field_edits']  =  $value['cross_field_edits'];
        }
      }            
    } 
   
    return;
  }  

  /* **********************************
   If no edit errors are present,
      clear out irrelevant/conflicting data 
      left over from setting up the rule
        where conditions were changed
      *************************************** */
  public function vtwpr_maybe_clear_extraneous_data() { 
    global $post, $vtwpr_rule, $vtwpr_rule_template_framework, $vtwpr_deal_edits_framework, $vtwpr_deal_structure_framework;     
    
    //IF there are edit errors, leave everything as is, exit stage left...
    if ( sizeof($vtwpr_rule->rule_error_message ) > 0 ) {  
      return;
    }

    //*************
    //Clear BUY area
    //*************
    if (($vtwpr_rule->rule_deal_info[0]['buy_amt_type'] == 'none') ||
        ($vtwpr_rule->rule_deal_info[0]['buy_amt_type'] == 'one')) {
       $vtwpr_rule->rule_deal_info[0]['buy_amt_count'] = null; 
    }
    
    if ($vtwpr_rule->rule_deal_info[0]['buy_amt_mod'] == 'none') {
       $vtwpr_rule->rule_deal_info[0]['buy_amt_mod_count'] = null; 
    }  
  
    switch( $vtwpr_rule->inPop ) {
      case 'wholeStore':
          //clear vargroup
          $vtwpr_rule->inPop_varProdID = null;
          $vtwpr_rule->inPop_varProdID_name = null; 
          $vtwpr_rule->var_in_checked = array(); 
          $vtwpr_rule->inPop_varProdID_parentLit = null; 
          //clear single
          $vtwpr_rule->inPop_singleProdID = null; 
          $vtwpr_rule->inPop_singleProdID_name = null;
          //clear groups
          $vtwpr_rule->prodcat_in_checked = array();
          $vtwpr_rule->rulecat_in_checked = array();
          $vtwpr_rule->role_in_checked = array();
          $vtwpr_rule->role_and_or_in = null;          
        break;
      
       //EDITED * + * +  * + * +  * + * +  * + * + * + * +  * + * +  * + * +  * + * +
      
    }  
    
    if ($vtwpr_rule->rule_deal_info[0]['buy_repeat_condition'] == 'none') {
       $vtwpr_rule->rule_deal_info[0]['buy_repeat_count'] = null; 
    }      
    //End BUY area clear

    //*************
    //Clear GET area
    //*************
    if (($vtwpr_rule->rule_deal_info[0]['action_amt_type'] == 'none') ||
        ($vtwpr_rule->rule_deal_info[0]['action_amt_type'] == 'zero') ||
        ($vtwpr_rule->rule_deal_info[0]['action_amt_type'] == 'one')) {
       $vtwpr_rule->rule_deal_info[0]['action_amt_count'] = null; 
    }
    
    if ($vtwpr_rule->rule_deal_info[0]['action_amt_mod'] == 'none') {
       $vtwpr_rule->rule_deal_info[0]['action_amt_mod_count'] = null; 
    }  
  
    switch( $vtwpr_rule->actionPop ) {
      case 'sameAsInPop':
      case 'wholeStore':
          //clear vargroup
          $vtwpr_rule->actionPop_varProdID = null;
          $vtwpr_rule->actionPop_varProdID_name = null; 
          $vtwpr_rule->var_out_checked = array(); 
          $vtwpr_rule->actionPop_varProdID_parentLit = null;
         // $vtwpr_rule->var_out_product_variations_parameter = array(); 
          //clear single
          $vtwpr_rule->actionPop_singleProdID = null; 
          $vtwpr_rule->actionPop_singleProdID_name = null;
          //clear groups
          $vtwpr_rule->prodcat_out_checked = array();
          $vtwpr_rule->rulecat_out_checked = array();
          $vtwpr_rule->role_out_checked = array();
          $vtwpr_rule->role_and_or_out = null;          
        break;
      
       //EDITED * + * +  * + * +  * + * +  * + * + * + * +  * + * +  * + * +  * + * +
       
    }  
    
    if ($vtwpr_rule->rule_deal_info[0]['action_repeat_condition'] == 'none') {
       $vtwpr_rule->rule_deal_info[0]['action_repeat_count'] = null; 
    }      
    //End GET area clear


    //*************
    //Clear DISCOUNT area        
    //*************
    switch( $vtwpr_rule->rule_deal_info[0]['discount_amt_type'] ) {
      case 'percent':
          $vtwpr_rule->rule_deal_info[0]['discount_auto_add_free_product'] = null;  
        break;
      case 'currency':
          $vtwpr_rule->rule_deal_info[0]['discount_auto_add_free_product'] = null; 
        break;
      case 'fixedPrice':
          $vtwpr_rule->rule_deal_info[0]['discount_auto_add_free_product'] = null; 
        break;
      case 'free':
          $vtwpr_rule->rule_deal_info[0]['discount_amt_count'] = null;
        break;
      case 'forThePriceOf_Units':
          $vtwpr_rule->rule_deal_info[0]['discount_auto_add_free_product'] = null; 
        break;
      case 'forThePriceOf_Currency':
          $vtwpr_rule->rule_deal_info[0]['discount_auto_add_free_product'] = null; 
        break;
    }
    //End Discount clear


    //*************
    //Clear MAXIMUM LIMITS area        
    //*************    
    
    if ($vtwpr_rule->rule_deal_info[0]['discount_rule_max_amt_type'] == 'none') {
       $vtwpr_rule->rule_deal_info[0]['discount_rule_max_amt_count'] = null; 
    } 
    if ($vtwpr_rule->rule_deal_info[0]['discount_lifetime_max_amt_type'] == 'none') {
       $vtwpr_rule->rule_deal_info[0]['discount_lifetime_max_amt_count'] = null; 
    }     
    if ($vtwpr_rule->rule_deal_info[0]['discount_rule_cum_max_amt_type'] == 'none') {
       $vtwpr_rule->rule_deal_info[0]['discount_rule_cum_max_amt_count'] = null; 
    } 
    //End Maximum Limits clear        

    return;  
  }


  //*************************
  //Pop Filter Agreement Check (switch used in apply...)
  //*************************
  public function vtwpr_maybe_pop_filter_agreement() { 
    global $vtwpr_rule;     
  
    if ($vtwpr_rule->actionPop  ==  'sameAsInPop' ) {
      $vtwpr_rule->set_actionPop_same_as_inPop = 'yes';
      return;
    }
    
    
    if (($vtwpr_rule->inPop      ==  'wholeStore') &&
        ($vtwpr_rule->actionPop  ==  'wholeStore') ) {
      $vtwpr_rule->set_actionPop_same_as_inPop = 'yes';
      return;
    }


    //EDITED * + * +  * + * +  * + * +  * + * + * + * +  * + * +  * + * +  * + * +
     
      
    $vtwpr_rule->set_actionPop_same_as_inPop = 'no';
    return;
  }
  


  /* ************************************************
  **   Get single variation data to support discount_auto_add_free_product, Pro Only
  *************************************************** */
  public function vtwpr_get_variations_parameter($which_vargroup) {

    global $wpdb, $post, $vtwpr_rule, $woocommerce;

    if ($which_vargroup == 'inPop') {
       $product_id    =  $vtwpr_rule->inPop_varProdID;
       $variation_id  =  $vtwpr_rule->var_in_checked[0];    
    } else {
       $product_id    =  $vtwpr_rule->actionPop_varProdID;
       $variation_id  =  $vtwpr_rule->var_out_checked[0];     
    }
 
    //************************
    //FROM woocommerce/woocommerce-functions.php  function woocommerce_add_to_cart_action
    //************************
    
	  $adding_to_cart      = get_product( $product_id );

  	$all_variations_set = true;
  	$variations         = array();

		$attributes = $adding_to_cart->get_attributes();
		$variation  = get_product( $variation_id );

		// Verify all attributes
		foreach ( $attributes as $attribute ) {
      if ( ! $attribute['is_variation'] )
      	continue;

      $taxonomy = 'attribute_' . sanitize_title( $attribute['name'] );


          // Get value from post data
          // Don't use woocommerce_clean as it destroys sanitized characters
         // $value = sanitize_title( trim( stripslashes( $_REQUEST[ $taxonomy ] ) ) );
          $value = $variation->variation_data[ $taxonomy ];

          // Get valid value from variation
          $valid_value = $variation->variation_data[ $taxonomy ];
          // Allow if valid
          if ( $valid_value == '' || $valid_value == $value ) {
            if ( $attribute['is_taxonomy'] )
            	$variations[ esc_html( $attribute['name'] ) ] = $value;
            else {
              // For custom attributes, get the name from the slug
              $options = array_map( 'trim', explode( '|', $attribute['value'] ) );
              foreach ( $options as $option ) {
              	if ( sanitize_title( $option ) == $value ) {
              		$value = $option;
              		break;
              	}
              }
               $variations[ esc_html( $attribute['name'] ) ] = $value;
            }
            continue;
        }

    }


    $product_variations_array = array(
       'parent_product_id'    => $product_id,
       'variation_product_id' => $variation_id,
       'variations_array'     => $variations
      );   
    

    return ($product_variations_array);
  } 
  
       
  
} //end class