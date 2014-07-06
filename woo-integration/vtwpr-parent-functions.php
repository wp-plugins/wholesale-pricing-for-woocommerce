<?php

	/*******************************************************  
 	     The session variable for this product will already have been
 	     stored during the catalog display of the product price 
          (similar pricing done in vtwpr-auto-add.php...)       
  ******************************************************** */
	function vtwpr_load_vtwpr_cart_for_processing(){
 
      global $post, $wpdb, $woocommerce, $vtwpr_cart, $vtwpr_cart_item, $vtwpr_setup_options, $vtwpr_info; 

     // from Woocommerce/templates/cart/mini-cart.php  and  Woocommerce/templates/checkout/review-order.php
      $woocommerce_cart_contents = $woocommerce->cart->get_cart();  
      if (sizeof($woocommerce_cart_contents) > 0) {
					$vtwpr_cart = new vtwpr_Cart;  
          foreach ( $woocommerce_cart_contents as $cart_item_key => $cart_item ) {
						$_product = $cart_item['data'];
						if ($_product->exists() && $cart_item['quantity']>0) {
							$vtwpr_cart_item                = new vtwpr_Cart_Item;
             
              //the product id does not change in woo if variation purchased.  
              //  Load expected variation id, if there, along with constructed product title.
              $varLabels = ' ';
              if ($cart_item['variation_id'] > ' ') {      
                 
                  // get parent title
                  $parent_post = get_post($cart_item['product_id']);
                  
                  // get variation names to string onto parent title
                  foreach($cart_item['variation'] as $key => $value) {          
                    $varLabels .= $value . '&nbsp;';           
                  }
                  
                  $vtwpr_cart_item->product_id           = $cart_item['variation_id'];
                  $vtwpr_cart_item->variation_array      = $cart_item['variation'];                  
                  $vtwpr_cart_item->product_name         = $parent_post->post_title . '&nbsp;' . $varLabels ;
                  $vtwpr_cart_item->parent_product_name  = $parent_post->post_title;

              } else { 
                  $vtwpr_cart_item->product_id           = $cart_item['product_id'];
                  $vtwpr_cart_item->product_name         = $_product->get_title().$woocommerce->cart->get_item_data( $cart_item );
              }
  
              
              $vtwpr_cart_item->quantity      = $cart_item['quantity'];                                                  
                  
              $product_id = $vtwpr_cart_item->product_id;
               
              //***always*** will be a session found
              //$session_found = vtwpr_maybe_get_product_session_info($product_id);
              vtwpr_maybe_get_product_session_info($product_id);

              //By this time, there may  be a 'display' session variable for this product, if a discount was displayed in the catalog
              //  so 2nd - nth iteration picks up the original unit price, not any discount shown currently
              //  The discount is applied in apply-rules for disambiguation, then rolled out before it gets processed (again)     product_discount_price
              if ($vtwpr_info['product_session_info']['product_discount_price'] > 0) {
                  $vtwpr_cart_item->unit_price             =  $vtwpr_info['product_session_info']['product_unit_price'];
                  $vtwpr_cart_item->db_unit_price          =  $vtwpr_info['product_session_info']['product_unit_price'];

                  $vtwpr_cart_item->db_unit_price_special  =  $vtwpr_info['product_session_info']['product_special_price']; 
                  $vtwpr_cart_item->db_unit_price_list     =  $vtwpr_info['product_session_info']['product_list_price'];
              } else {              
              //vat_exempt seems to be the same as tax exempt, using the same field???
              //  how does this work with taxes included in price?
              //  how about shipping includes taxes, then roll out taxes (vat_exempt) ???
                  //FROM my original plugins:  $vtwpr_cart_item->unit_price     =  get_option( 'woocommerce_display_cart_prices_excluding_tax' ) == 'yes' || $woocommerce->customer->is_vat_exempt() ? $_product->get_price_excluding_tax() : $_product->get_price();
	                //FROM woo cart		$product_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? $_product->get_price_excluding_tax() : $_product->get_price_including_tax();
                  //  combining the two:::
              
              //DON'T USE THIS ANYMORE, BECOMES SELF-REFERENTIAL, AS GET_PRICE() NOW HOOKS THE SINGLE PRODUCT DISCOUNT ...
              //    $vtwpr_cart_item->unit_price     =  get_option( 'woocommerce_tax_display_cart' ) == 'excl' || $woocommerce->customer->is_vat_exempt() ? $_product->get_price_excluding_tax() : $_product->get_price();


                  //v1.0.3 begin - redo for VAT processing...
                  
                  $regular_price = get_post_meta( $product_id, '_regular_price', true );
                  if ($regular_price > 0) {
                     $vtwpr_cart_item->db_unit_price_list  =  $regular_price;
                  } else {
                     $vtwpr_cart_item->db_unit_price_list  =  get_post_meta( $product_id, '_price', true );
                  }
    
                  $vtwpr_cart_item->db_unit_price_special  =  get_post_meta( $product_id, '_sale_price', true );                  

                  if (( get_option( 'woocommerce_prices_include_tax' ) == 'no' )  || 
                      ( $woocommerce->customer->is_vat_exempt()) ) {
                     //NO VAT included in price
                     $vtwpr_cart_item->unit_price =  $cart_item['line_subtotal'] / $cart_item['quantity'];                                                           
                  } else {
                     //VAT included in price, and $cart_item pricing has already subtracted out the VAT, so use DB prices (otherwise if other functions called, recursive processing will ensue...)
                     switch(true) {
                        case ($vtwpr_cart_item->db_unit_price_special > 0) :  
                            if ( ($vtwpr_cart_item->db_unit_price_list < 0) ||    //sometimes list price is blank, and user only sets a sale price!!!
                                 ($vtwpr_cart_item->db_unit_price_special < $vtwpr_cart_item->db_unit_price_list) )  {
                                //item is on sale, use sale price
                               $vtwpr_cart_item->unit_price = $vtwpr_cart_item->db_unit_price_special;
                            } else {
                               //use regular price
                               $vtwpr_cart_item->unit_price = $vtwpr_cart_item->db_unit_price_list;
                            }
                          break;
                        default:
                            $vtwpr_cart_item->unit_price = $vtwpr_cart_item->db_unit_price_list;                            
                          break;
                      }
                     /*
                     if ( ($vtwpr_cart_item->db_unit_price_special > 0) &&
                          ($vtwpr_cart_item->db_unit_price_special < $vtwpr_cart_item->db_unit_price_list) ) {
                       $vtwpr_cart_item->unit_price = $vtwpr_cart_item->db_unit_price_special;
                     } else {
                       $vtwpr_cart_item->unit_price = $vtwpr_cart_item->db_unit_price_list;
                     }
                     */
                  }
                  
                  $vtwpr_cart_item->db_unit_price  =  $vtwpr_cart_item->unit_price;

                  //v1.0.3 end                     
  
                                 
              }               


              // db_unit_price_special CAN be zero if item is FREE!!
              if ($vtwpr_cart_item->unit_price < $vtwpr_cart_item->db_unit_price_list )  {
                  $vtwpr_cart_item->product_is_on_special = 'yes';             
              }               

              $vtwpr_cart_item->total_price   = $vtwpr_cart_item->quantity * $vtwpr_cart_item->unit_price;
              
              /*  *********************************
              ***  JUST the cat *ids* please...
              ************************************ */
                       
                $vtwpr_cart_item->prod_cat_list = wp_get_object_terms( $cart_item['product_id'], $vtwpr_info['parent_plugin_taxonomy'], $args = array('fields' => 'ids') );
                $vtwpr_cart_item->rule_cat_list = wp_get_object_terms( $cart_item['product_id'], $vtwpr_info['rulecat_taxonomy'], $args = array('fields' => 'ids') );              
         


        
              //initialize the arrays
              $vtwpr_cart_item->prod_rule_include_only_list = array();  
              $vtwpr_cart_item->prod_rule_exclusion_list = array();
              
              /*  *********************************
              ***  fill in include/exclude arrays if selected on the PRODUCT Screen (parent plugin)
              ************************************ */
              $vtwpr_includeOrExclude_meta  = get_post_meta($product_id, $vtwpr_info['product_meta_key_includeOrExclude'], true);
              if ( $vtwpr_includeOrExclude_meta ) {
                switch( $vtwpr_includeOrExclude_meta['includeOrExclude_option'] ) {
                  case 'includeAll':  
                    break;
                  case 'includeList':                  
                      $vtwpr_cart_item->prod_rule_include_only_list = $vtwpr_includeOrExclude_meta['includeOrExclude_checked_list'];                                            
                    break;
                  case 'excludeList':  
                      $vtwpr_cart_item->prod_rule_exclusion_list = $vtwpr_includeOrExclude_meta['includeOrExclude_checked_list'];                                               
                    break;
                  case 'excludeAll':  
                      $vtwpr_cart_item->prod_rule_exclusion_list[0] = 'all';  //set the exclusion list to exclude all
                    break;
                }
              }
               
              //add cart_item to cart array
              $vtwpr_cart->cart_items[]       = $vtwpr_cart_item;
				    }
        } //	endforeach;
        
        
		} //end  if (sizeof($woocommerce->cart->get_cart())>0) 
     
     
    if ( (defined('VTWPR_PRO_DIRNAME')) && ($vtwpr_setup_options['use_lifetime_max_limits'] == 'yes') )  {
      vtwpr_get_purchaser_info_from_screen();   
    }
    $vtwpr_cart->purchaser_ip_address = $vtwpr_info['purchaser_ip_address']; 

  }


   //************************************* 

   //************************************* 
	function vtwpr_count_other_coupons(){
      global $woocommerce, $vtwpr_info, $vtwpr_rules_set; 

      $coupon_cnt = 0;
			if ( $woocommerce->applied_coupons ) {
				foreach ( $woocommerce->applied_coupons as $index => $code ) {
					if ( $code == $vtwpr_info['coupon_code_discount_deal_title'] ) {
            continue;  //if the coupon is a Wholesale Pricing discount, skip
          } else {
            $coupon_cnt++;
          }
				}
        $vtwpr_rules_set[0]->coupons_amount_without_rule_discounts = $coupon_cnt;
			} else {     
        $vtwpr_rules_set[0]->coupons_amount_without_rule_discounts = 0; 
      }     
     return;     
  } 
/* 
   //************************************* 
   // tabulate $vtwpr_info['cart_rows_at_checkout']
   //************************************* 
	function vtwpr_count_wpsc_cart_contents(){
      global $woocommerce, $vtwpr_info; 

      $vtwpr_info['cart_rows_at_checkout_count'] = 0;
      foreach($woocommerce->cart_items as $key => $cart_item) {
        $vtwpr_info['cart_rows_at_checkout_count']++;   //increment count by 1
      } 
      return;     
  } 
 */
 
	function vtwpr_load_vtwpr_cart_for_single_product_price($product_id, $price){
      global $post, $wpdb, $woocommerce, $vtwpr_cart, $vtwpr_cart_item, $vtwpr_info; 

      $vtwpr_cart = new VTWPR_Cart;  
      $vtwpr_cart_item                = new VTWPR_Cart_Item;      
      $post = get_post($product_id);
   
      //change??
      $vtwpr_cart_item->product_id            = $product_id;
      $vtwpr_cart_item->product_name          = $post->post_name;
      $vtwpr_cart_item->quantity              = 1;
      $vtwpr_cart_item->unit_price            = $price;
      $vtwpr_cart_item->db_unit_price         = $price;
      $vtwpr_cart_item->db_unit_price_list    = $price;
      $vtwpr_cart_item->db_unit_price_special = $price;    
      $vtwpr_cart_item->total_price           = $price;
            
      /*  *********************************
      ***  JUST the cat *ids* please...
      ************************************ */
      $vtwpr_cart_item->prod_cat_list = wp_get_object_terms( $product_id, $vtwpr_info['parent_plugin_taxonomy'], $args = array('fields' => 'ids') );
      $vtwpr_cart_item->rule_cat_list = wp_get_object_terms( $product_id, $vtwpr_info['rulecat_taxonomy'], $args = array('fields' => 'ids') );
        //*************************************                    

        
      //add cart_item to cart array
      $vtwpr_cart->cart_items[]       = $vtwpr_cart_item;  
      
                
  }

	
	function vtwpr_move_vtwpr_single_product_to_session($product_id){
      global $post, $wpdb, $woocommerce, $vtwpr_cart, $vtwpr_cart_item, $vtwpr_info, $vtwpr_setup_options, $vtwpr_rules_set;  

      $short_msg_array = array();
      $full_msg_array = array();
      $msg_already_done = 'no';
      $show_yousave_one_some_msg;
    
      //auditTrail keyed to rule_id, so foreach is necessary
      foreach ($vtwpr_cart->cart_items[0]->cartAuditTrail as $key => $row) {       
     /*   
        //parent product vargroup on sale, individual product variation may not be on sale.
        // send an additional sale msg for the varProd parent group...
        if ($vtwpr_setup_options['show_yousave_one_some_msg'] == 'yes' ) {
          $show_yousave_one_some_msg;
          if (!$show_yousave_one_some_msg) {
            $rulesetKey = $row['ruleset_occurrence'];
            switch( $vtwpr_rules_set[$rulesetKey]->inPop_varProdID_parentLit) {  
              case 'one':
                 $show_yousave_one_some_msg = __('One of these are on Sale', 'vtwpr');
                break;
              case 'some':
                 $show_yousave_one_some_msg = __('Some of these are on Sale', 'vtwpr');
                break;         
              case 'all':  //all are on sale, handled as normal.
                break; 
              default:  //handled as normal.
                break;       
            }
          }
        }
      */
         
        if ($row['rule_short_msg'] > ' ' ) {       
          $short_msg_array [] = $row['rule_short_msg'];
          $full_msg_array  [] = $row['rule_full_msg'];
        }

      }

      /*
       if  $vtwpr_cart->cart_level_status == 'rejected' no discounts found
       how to handle yousave display, etc.... If no yousave, return 'false'
      */
      if ( $vtwpr_cart->cart_level_status == 'rejected' ) {
        $vtwpr_cart->cart_items[0]->discount_price = 0;
        $vtwpr_cart->cart_items[0]->yousave_total_amt = 0;
        $vtwpr_cart->cart_items[0]->yousave_total_pct = 0;
      } 
      
      //needed for wp-e-commerce!!!!!!!!!!!
      //  if = 'yes', display of 'yousave' becomes 'save FROM' and doesn't change!!!!!!!
//      $product_variations_sw = vtwpr_test_for_variations($product_id);
      $product_variations_sw;
      
      
      if ($vtwpr_cart->cart_items[0]->yousave_total_amt > 0) {
         $list_price                    =   $vtwpr_cart->cart_items[0]->db_unit_price_list;
         $db_unit_price_list_html_woo   =   woocommerce_price($list_price);
         $discount_price                =   $vtwpr_cart->cart_items[0]->discount_price;
         $discount_price_html_woo       =   woocommerce_price($discount_price);

         //v1.0.3 begin
         //from woocommerce/includes/abstracts/abstract-wp-product.php
         // Check for Price Suffix
         $price_display_suffix  = get_option( 'woocommerce_price_display_suffix' );
      	 if ( ( $price_display_suffix ) &&                              //v1.0.3
              ( $vtwpr_cart->cart_items[0]->discount_price > 0 ) ) {   //v1.0.3  don't do suffix for zero amount...
            //first get rid of pricing functions which aren't relevant here
             $find = array(    //these are allowed in suffix, remove
      				'{price_including_tax}',
      				'{price_excluding_tax}'
      			);
      			$replace = '';
      			$price_display_suffix = str_replace( $find, $replace, $price_display_suffix );
            
            //then see if suffix is needed
            if (strpos($discount_price_html_woo, $price_display_suffix) !== false) { //if suffix already in price, do nothing
              $do_nothing;
            } else {
              $discount_price_html_woo = $discount_price_html_woo . ' <small class="woocommerce-price-suffix ">' . $price_display_suffix . '</small>';
            }
         }
         //v1.0.3 end
      } else {
         $db_unit_price_list_html_woo;
         $discount_price_html_woo;
         $vtwpr_cart->cart_items[0]->product_discount_price_html_woo = '';      
      }

      $vtwpr_info['product_session_info']  =     array (
            'product_list_price'           => $vtwpr_cart->cart_items[0]->db_unit_price_list,
            'product_list_price_html_woo'  => $db_unit_price_list_html_woo,
            'product_unit_price'           => $vtwpr_cart->cart_items[0]->db_unit_price,
            'product_special_price'        => $vtwpr_cart->cart_items[0]->db_unit_price_special,
            'product_discount_price'       => $vtwpr_cart->cart_items[0]->discount_price,
            'product_discount_price_html_woo'  => 
                                              $discount_price_html_woo,
            'product_is_on_special'        => $vtwpr_cart->cart_items[0]->product_is_on_special,
            'product_yousave_total_amt'    => $vtwpr_cart->cart_items[0]->yousave_total_amt,     
            'product_yousave_total_pct'    => $vtwpr_cart->cart_items[0]->yousave_total_pct,
 //           'product_discount_price_html_woo'   => $vtwpr_cart->cart_items[0]->product_discount_price_html_woo,     
            'product_rule_short_msg_array' => $short_msg_array,        
            'product_rule_full_msg_array'  => $full_msg_array,
            'product_has_variations'       => $product_variations_sw,
            'session_timestamp_in_seconds' => time(),
            'user_role'                    => vtwpr_get_current_user_role(),
            'product_in_rule_allowing_display'  => $vtwpr_cart->cart_items[0]->product_in_rule_allowing_display, //if not= 'yes', only msgs are returned 
            'show_yousave_one_some_msg'    => $show_yousave_one_some_msg, 
            //for later ajaxVariations pricing
            'this_is_a_parent_product_with_variations' => $vtwpr_cart->cart_items[0]->this_is_a_parent_product_with_variations,            
            'pricing_by_rule_array'        => $vtwpr_cart->cart_items[0]->pricing_by_rule_array                   
          ) ;      
      if(!isset($_SESSION)){
        session_start();
        header("Cache-Control: no-cache");
        header("Pragma: no-cache");
      } 
      //store session id 'vtwpr_product_session_info_[$product_id]'
      $_SESSION['vtwpr_product_session_info_'.$product_id] = $vtwpr_info['product_session_info'];
      
  }

    function vtwpr_fill_variations_checklist($tax_class, $checked_list = NULL, $pop_in_out_sw, $product_ID, $product_variation_IDs) { 
        global $post, $vtwpr_info;
        // *** ------------------------------------------------------------------------------------------------------- ***
        // additional code from:  woocommerce/admin/post-types/writepanels/writepanel-product-type-variable.php
        // *** ------------------------------------------------------------------------------------------------------- ***
        //    woo doesn't keep the variation title in post title of the variation ID post, additional logic constructs the title ...
        
        $parent_post = get_post($product_ID);
        
        $attributes = (array) maybe_unserialize( get_post_meta($product_ID, '_product_attributes', true) );
    
        $parent_post_terms = wp_get_post_terms( $post->ID, $attribute['name'] );
       
        // woo parent product title only carried on parent post
        echo '<h3>' .$parent_post->post_title.    ' - Variations</h3>'; 
        
        foreach ($product_variation_IDs as $product_variation_ID) {     //($product_variation_IDs as $product_variation_ID => $info)
            // $variation_post = get_post($product_variation_ID);
         
            $output  = '<li id='.$product_variation_ID.'>' ;
            $output  .= '<label class="selectit">' ;
            $output  .= '<input id="'.$product_variation_ID.'_'.$tax_class.' " ';
            $output  .= 'type="checkbox" name="tax-input-' .  $tax_class . '[]" ';
            $output  .= 'value="'.$product_variation_ID.'" ';
            if ($checked_list) {
                if (in_array($product_variation_ID, $checked_list)) {   //if variation is in previously checked_list   
                   $output  .= 'checked="checked"';
                }                
            }
            $output  .= '>'; //end input statement
 
            $variation_label = ''; //initialize label
            $variation_product_name_attributes;
            $variation_product_name_attributes_cnt = 0;
            
            //get the variation names
            foreach ($attributes as $attribute) :

									// Only deal with attributes that are variations
									if ( !$attribute['is_variation'] ) continue;

									// Get current value for variation (if set)
									$variation_selected_value = get_post_meta( $product_variation_ID, 'attribute_' . sanitize_title($attribute['name']), true );

									// Get terms for attribute taxonomy or value if its a custom attribute
									if ($attribute['is_taxonomy']) :
										$post_terms = wp_get_post_terms( $product_ID, $attribute['name'] );
										foreach ($post_terms as $term) :
											if ($variation_selected_value == $term->slug) {
                          $variation_label .= $term->name . '&nbsp;&nbsp;' ;
                          
                          //for auto-insert support
                          if ($variation_product_name_attributes_cnt > 0) {
                            $variation_product_name_attributes .= '{,}';  //custom list separator
                          }
                          $variation_product_name_attributes .= $term->name;
                          $variation_product_name_attributes_cnt++;
                          
                      }
										endforeach;
									else :
										$options = explode('|', $attribute['value']);
										foreach ($options as $option) :
											if ($variation_selected_value == $option) {
                        $variation_label .= ucfirst($option) . '&nbsp;&nbsp;' ;
                          
                          //for auto-insert support
                          if ($variation_product_name_attributes_cnt > 0) {
                            $variation_product_name_attributes .= '{,}';  //custom list separator
                          }
                          $variation_product_name_attributes .= ucfirst($option);
                          $variation_product_name_attributes_cnt++;
                                                 
                      }
										endforeach;
									endif;

						endforeach;
    
            $output  .= '&nbsp;&nbsp; #' .$product_variation_ID. '&nbsp;&nbsp; - &nbsp;&nbsp;' .$variation_label;
            $output  .= '</label>';
            
            
            //hide name attribute list, used in auto add function ONLY AUTOADD
            // custom list of attributes with   '{,}' as a separator...
            $woo_attributes_id =  'woo_attributes_' .$product_variation_ID. '_' .$tax_class ;
            $output  .= '<input type="hidden" id="'.$woo_attributes_id.'" name="'.$woo_attributes_id.'" value="'.$variation_product_name_attributes.'">';          


            $output  .= '</li>'; 
            echo $output ;           
         }   

         
               
        return;     
    }
    

  /* ************************************************
  **   Get all variations for product
  *************************************************** */
  function vtwpr_get_variations_list($product_ID) {
    global $wpdb;    
    //sql from woocommerce/classes/class-wc-product.php
   $variations = get_posts( array(
			'post_parent' 	=> $product_ID,
			'posts_per_page'=> -1,
			'post_type' 	  => 'product_variation',
			'fields' 		    => 'ids',
			'post_status'	  => 'publish',
      'order'         => 'ASC'
	  ));
    //v1.0.3 begin
   $product_variations_list = array(); //v1.0.3
   if ($variations)  {    
      foreach ( $variations as $variation) {
        $product_variations_list [] = $variation;             
    	}
   }
    //v1.0.3 end
     
    return ($product_variations_list);
  } 

  
  
  function vtwpr_test_for_variations($prod_ID) { 
      
     $vartest_response = 'no';
     
     /* Commented => DB access method uses more IO/CPU cycles than array processing below...
     //sql from woocommerce/classes/class-wc-product.php
     $variations = get_posts( array(
    			'post_parent' 	=> $prod_ID,
    			'posts_per_page'=> -1,
    			'post_type' 	=> 'product_variation',
    			'fields' 		=> 'ids',
    			'post_status'	=> 'publish'
    		));
     if ($variations)  {
        $vartest_response = 'yes';
     }  */
     
     // code from:  woocommerce/admin/post-types/writepanels/writepanel-product-type-variable.php
     $attributes = (array) maybe_unserialize( get_post_meta($prod_ID, '_product_attributes', true) );
     foreach ($attributes as $attribute) {
       if ($attribute['is_variation'])  {
          $vartest_response = 'yes';
          break;
       }
     }
     
     return ($vartest_response);     
  }  
  


    
   function vtwpr_format_money_element($price) { 
      //from woocommerce/woocommerce-core-function.php   function woocommerce_price
    	$return          = '';
    	$num_decimals    = (int) get_option( 'woocommerce_price_num_decimals' );
    	$currency_pos    = get_option( 'woocommerce_currency_pos' );
    	$currency_symbol = get_woocommerce_currency_symbol();
    	$decimal_sep     = wp_specialchars_decode( stripslashes( get_option( 'woocommerce_price_decimal_sep' ) ), ENT_QUOTES );
    	$thousands_sep   = wp_specialchars_decode( stripslashes( get_option( 'woocommerce_price_thousand_sep' ) ), ENT_QUOTES );
    
    	$price           = apply_filters( 'raw_woocommerce_price', (double) $price );
    	$price           = number_format( $price, $num_decimals, $decimal_sep, $thousands_sep );
    
    	if ( get_option( 'woocommerce_price_trim_zeros' ) == 'yes' && $num_decimals > 0 )
    		$price = woocommerce_trim_zeros( $price );
    
    	//$return = '<span class="amount">' . sprintf( get_woocommerce_price_format(), $currency_symbol, $price ) . '</span>'; 

     $formatted = sprintf( get_woocommerce_price_format(), $currency_symbol, $price );
     
     return $formatted;
   }
   
   //****************************
   // Gets Currency Symbol from PARENT plugin   - only used in backend UI during rules update
   //****************************   
  function vtwpr_get_currency_symbol() {    
    return get_woocommerce_currency_symbol();  
  } 

  function vtwpr_get_current_user_role() {
    global $current_user; 
    
    if ( !$current_user )  {
      $current_user = wp_get_current_user();
    }
    
    $user_roles = $current_user->roles;
    $user_role = array_shift($user_roles);
    if  ($user_role <= ' ') {
      $user_role = 'notLoggedIn';
    }      
    return $user_role;
  } 



  //*******************************************************************************

  //*******************************************************************************
  function vtwpr_print_widget_discount() {
    global $post, $wpdb, $woocommerce, $vtwpr_cart, $vtwpr_cart_item, $vtwpr_info, $vtwpr_rules_set, $vtwpr_setup_options;
      
      
    //  vtwpr_enqueue_front_end_css();      
      
    //*****************************
    //PRINT DISCOUNT ROWS + total line
    //*****************************

    $vtwpr_cart->cart_discount_subtotal = $vtwpr_cart->yousave_cart_total_amt; 
     
    if ($vtwpr_setup_options['show_cartWidget_discount_detail_lines'] == 'yes') {
      $output  = '<h3 class="widget-title">';
      $output .=  __('Discounts', 'vtwpr');
      $output .= '</h3>';
      echo $output;

      //do we repeat the purchases subtotal after the discount details?
      if ($vtwpr_setup_options['show_cartWidget_purchases_subtotal'] == 'beforeDiscounts') {
         vtwpr_print_widget_purchases_subtotal();
      } 

      if ($vtwpr_setup_options['show_cartWidget_discount_detail_lines'] == 'yes') {
         vtwpr_print_widget_discount_rows();
      } 
            
    } 

    //do we repeat the purchases subtotal after the discount details?
    if ($vtwpr_setup_options['show_cartWidget_purchases_subtotal'] == 'withDiscounts') {
       vtwpr_print_widget_purchases_subtotal();
       echo '<br>'; //additional break needed here
    } 

    if ($vtwpr_setup_options['show_cartWidget_discount_total_line'] == 'yes') {
       vtwpr_print_widget_discount_total();
    }     

    if ($vtwpr_setup_options['cartWidget_new_subtotal_line'] == 'yes') {
       vtwpr_print_widget_new_combined_total();
    }         
            

    return;
  }
 
  /* ************************************************
  **   print discount amount by product, and print total              
  *************************************************** */
	function vtwpr_print_widget_discount_rows() {
    global $vtwpr_cart, $vtwpr_cart_item, $vtwpr_info, $vtwpr_rules_set, $vtwpr_rule, $vtwpr_setup_options;

      echo '<ul class="cart_list product_list_widget vtwpr_product_list_widget">' ;

  
      $sizeof_cart_items = sizeof($vtwpr_cart->cart_items);
      for($k=0; $k < $sizeof_cart_items; $k++) {  
       	if ( $vtwpr_cart->cart_items[$k]->yousave_total_amt > 0) {            
            echo '<li>';
            $msg_cnt = 0;  
//echo 'yousave_by_rule_info= <pre>'.print_r($vtwpr_cart->cart_items[$k]->yousave_by_rule_info, true).'</pre>' ; 
            if ($vtwpr_setup_options['show_cartWidget_discount_details_grouped_by_what'] == 'rule') {
              //these rows are indexed by ruleID, so a foreach is needed...
              foreach($vtwpr_cart->cart_items[$k]->yousave_by_rule_info as $key => $yousave_by_rule) {
                $msg_cnt++;
                if ($msg_cnt > 1) {
                  echo '</li><li>';
                }
//echo '<br>$key= '.$key.'<br>' ;
                $i = $yousave_by_rule['ruleset_occurrence'];
                //display info is tabulated for cumulative rule processing, but the Price Reduction has already taken place!!
                $output  = '<span class="vtwpr-discount-msg-widget" >';                  
                $output .= stripslashes($yousave_by_rule['rule_short_msg']);
                $output .= '</span><br>';
                echo  $output;
//echo '$k= '.$k. ' $key= '.$key.'<br>' ;
//echo '$yousave_by_rule= <pre>'.print_r($yousave_by_rule, true).'</pre>' ;                 
                //if a max was reached and msg supplied, print here 
                if ($yousave_by_rule['rule_max_amt_msg'] > ' ') {    
                  $output  = '<span class="vtwpr-discount-max-msg-widget" >';                  
                  $output .= stripslashes($yousave_by_rule['rule_max_amt_msg']);
                  $output .= '</span><br>';
                  echo  $output;                  
                }
                
                $amt = $yousave_by_rule['yousave_amt']; 
                $units = $yousave_by_rule['discount_applies_to_qty'];                  
                vtwpr_print_discount_detail_line_widget($amt, $units, $k);
              }
            } else {   //show discounts by product
              $amt = $vtwpr_cart->cart_items[$k]->yousave_total_amt; 
              $units = $vtwpr_cart->cart_items[$k]->yousave_total_qty;                  
              vtwpr_print_discount_detail_line_widget($amt, $units, $k);
           }
           
           echo '</li>';
        }
      }

      echo '</ul>' ;

    return;
    
  }
     
	function vtwpr_print_discount_detail_line_widget($amt, $units, $k) {  
    global $vtwpr_cart, $vtwpr_cart_item, $vtwpr_info, $vtwpr_rules_set, $vtwpr_rule, $vtwpr_setup_options;
    $output;

    if (sizeof($vtwpr_cart->cart_items[$k]->variation_array) > 0   ) {
      $output .= '<span class="vtwpr-product-name-widget">' . $vtwpr_cart->cart_items[$k]->parent_product_name .'</span>';	
      $output .= '<dl class="variation">';
      foreach($vtwpr_cart->cart_items[$k]->variation_array as $key => $value) {          
        $name = sanitize_title( str_replace( 'attribute_pa_', '', $key  ) );  //post v 2.1
        $name = sanitize_title( str_replace( 'pa_', '', $name  ) );   //pre v 2.1
        $name = ucwords($name);  
        $output .= '<dt>'. $name . ': </dt>';
        $output .= '<dd>'. $value .'</dd>';           
      }
      $output .= '</dl>';
    
    } else {
      $output .= '<span class="vtwpr-product-name-widget">' . $vtwpr_cart->cart_items[$k]->product_name  .'</span>';
      $output .= '<br>';
    }    
        
    //*************************************
    //division creates a per-unit discount
    //*************************************
    $amt = $amt / $units;
    $amt = vtwpr_format_money_element($amt);		
    $output .= '<span class="quantity vtwpr-quantity-widget">' . $units  .' &times; ';	
    $output .= '<span class="amount vtwpr-amount-widget">' . $vtwpr_setup_options['cartWidget_credit_detail_label'] .$amt  .'</span>';
    $output .= '</span>';	    

    
        
    /*
    $output .= '<span class="vtwpr-prod-name-widget" >';
    $output .= $vtwpr_cart->cart_items[$k]->product_name;
    $output .= '</span><br>';        
    
    $output .= '<span class="quantity vtwpr-discount-quantity-widget" >';
    
    //*************************************
    //division creates a per-unit discount
    //*************************************
    $amt = $amt / $units;
    
    $amt = vtwpr_format_money_element($amt); 
    
    $output .= $units .' x &nbsp;-'. $amt;
    $output .= '</span>';
    */
    //$output .= 'HOLA!';//mwnecho
    
    echo  $output;
    return;  
 }

    
	function vtwpr_print_widget_discount_total() {
    global $vtwpr_cart, $woocommerce, $vtwpr_cart_item, $vtwpr_info, $vtwpr_rules_set, $vtwpr_rule, $vtwpr_setup_options;

    if ($vtwpr_setup_options['show_widget_discount_total_line'] == 'no') {
      return;
    }
                                                    
    $output;
    $output .= '<span class="total vtwpr-discount-total-label-widget" >';
    $output .= '<strong>'.$vtwpr_setup_options['cartWidget_credit_total_title']. '&nbsp;</strong>';     

    $amt = vtwpr_format_money_element($vtwpr_cart->cart_discount_subtotal); 
    
    $output .= '<span class="amount  vtwpr-discount-total-amount-widget">' . $vtwpr_setup_options['cartWidget_credit_total_label'] .$amt . '</span>';

    $output .= '</span>';
    echo  $output;
       
    return;
    
  }

    
	function vtwpr_print_widget_new_combined_total() {
    global $vtwpr_cart, $woocommerce, $vtwpr_cart_item, $vtwpr_info, $vtwpr_rules_set, $vtwpr_rule, $vtwpr_setup_options;

    if ($vtwpr_setup_options['cartWidget_new_subtotal_line'] == 'no') {
       return;
    }

    $output;
    $output .= '<p class="total vtwpr-combined-total-label-widget" >';
    $output .= '<strong>'.$vtwpr_setup_options['cartWidget_new_subtotal_label'] .'&nbsp;</strong>';     

    
    //can't use the regular routine, as it returns a formatted result
    //$subtotal = vtwpr_get_Woo_cartSubtotal(); 
   if ( $woocommerce->tax_display_cart == 'excl' ) {
			$subtotal = $woocommerce->cart->subtotal_ex_tax ;
		} else {
			$subtotal = $woocommerce->cart->subtotal;
    }

    
    $subtotal -= $vtwpr_cart->cart_discount_subtotal;

    $amt = vtwpr_format_money_element($subtotal); 

//    $amt = $woocommerce->cart->subtotal .' - ' . $vtwpr_cart->cart_discount_subtotal .' = '. $subtotal; //test test
    
    $output .= '<span class="amount  vtwpr-discount-total-amount-widget">' .$amt . '</span>';

    $output .= '</p>';
    echo  $output;
       
    return;
    
  }
  
  
  /* ************************************************
  **   print cart widget purchase subtotal             
  *************************************************** */
   
	function vtwpr_print_widget_purchases_subtotal() {
    global $vtwpr_cart, $woocommerce, $vtwpr_cart_item, $vtwpr_info, $vtwpr_rules_set, $vtwpr_rule, $vtwpr_setup_options;
                                               
    $output;
    $output .= '<span class="total vtwpr-product-total-label-widget" >';
    $output .= '<strong>'.$vtwpr_setup_options['cartWidget_credit_subtotal_title']. '&nbsp;</strong>';     

    $amt = vtwpr_get_Woo_cartSubtotal(); 
    
    $output .= '<span class="amount  vtwpr-discount-total-amount-widget">' .$amt . '</span>';

    $output .= '</span>';
    echo  $output;
       
    return;
    
  }
 
  /* ************************************************
  **   print discount amount by product, and print total AND MOVE DISCOUNT INTO TOTAL...             
  *************************************************** */

	function vtwpr_print_checkout_discount() {
    global $woocommerce, $vtwpr_cart, $vtwpr_cart_item, $vtwpr_info, $vtwpr_rules_set, $vtwpr_rule, $vtwpr_setup_options;
   //when executing from here, the table rows created by the print routines need a <table>
    //  when executed from the cart_widget, the TR lines appear in the midst of an existing <table>
    
    $execType = 'checkout';
    
    if ($vtwpr_setup_options['show_checkout_purchases_subtotal'] == 'beforeDiscounts') {
      vtwpr_print_cart_purchases_subtotal($execType);
    }
    
    $output;

    $output .=  '<table class="vtwpr-discount-table"> ';
    
    
    if ($vtwpr_setup_options['show_checkout_discount_titles_above_details'] == 'yes') {    
      $output .= '<tr id="vtwpr-discount-title-checkout" >';
            /* COLSPAN no longer used here, has no affect
      $output .= '<td colspan="' .$vtwpr_setup_options['checkout_html_colspan_value']. '" id="vtwpr-discount-title-above-checkout">';
      */
      $output .= '<td id="vtwpr-discount-title-above-checkout">';
      $output .= '<div class="vtwpr-discount-prodLine-checkout" >';
      
      $output .= '<span class="vtwpr-discount-prodCol-checkout">' .  __('Product', 'vtwpr') . '</span>';
      
      $output .= '<span class="vtwpr-discount-unitCol-checkout">' .  __('Discount Qty', 'vtwpr') . '</span>';
  
      $output .= '<span class="vtwpr-discount-amtCol-checkout">' .  __('Discount Amount', 'vtwpr') . '</span>';
      
      $output .= '</div'; //end prodline
      $output .= '</td>';
      $output .= '</tr>';
         
     }
     echo  $output;
    
    $vtwpr_cart->cart_discount_subtotal = $vtwpr_cart->yousave_cart_total_amt;
    
    /*
    if ($vtwpr_rules_set[0]->coupons_amount_without_rule_discounts > 0) {
       $vtwpr_cart->cart_discount_subtotal += $vtwpr_rules_set[0]->coupons_amount_without_rule_discounts;
       //print a separate discount line if price discounts taken, PRN
       vtwpr_print_coupon_discount_row($execType);
    }
    */ 
                                                 
    //print discount detail rows 
    vtwpr_print_cart_discount_rows($execType);
 
    if ($vtwpr_setup_options['show_checkout_purchases_subtotal'] == 'withDiscounts') {
      vtwpr_print_cart_purchases_subtotal($execType);
    } 
 
 /*
    if ($vtwpr_rules_set[0]->coupons_amount_without_rule_discounts > 0) {
       //print totals using the coupon amount  
       if ($vtwpr_setup_options['show_checkout_credit_total_when_coupon_active'] == 'yes')  {          
          vtwpr_print_cart_discount_total($execType); 
       }    
    } else {
      //if there's no coupon being presented, no coupon totals will be printed, so discount total line is needed     
      vtwpr_print_cart_discount_total($execType);   
    }
    */
    vtwpr_print_cart_discount_total($execType); 

    if ($vtwpr_setup_options['checkout_new_subtotal_line'] == 'yes') {
      vtwpr_print_new_cart_checkout_subtotal_line($execType);
    }    

    echo   '</table>  <!-- vtwpr discounts table close -->  '; 
        
 } 
 
  /* ************************************************
  **   print discount amount by product, and print total              
  *************************************************** */
	function vtwpr_print_cart_discount_rows($execType) {
    global $vtwpr_cart, $vtwpr_cart_item, $vtwpr_info, $vtwpr_rules_set, $vtwpr_rule, $vtwpr_setup_options;
       
      $printRowsCheck = 'show_' .$execType. '_discount_detail_lines';
      if ($vtwpr_setup_options[$printRowsCheck] == 'no') {
        return;
      }
  
      $sizeof_cart_items = sizeof($vtwpr_cart->cart_items);
      for($k=0; $k < $sizeof_cart_items; $k++) {  
       	if ( $vtwpr_cart->cart_items[$k]->yousave_total_amt > 0) {            
            if ((($execType == 'checkout')   && ($vtwpr_setup_options['show_checkout_discount_details_grouped_by_what']   == 'rule')) ||
                (($execType == 'cartWidget') && ($vtwpr_setup_options['show_cartWidget_discount_details_grouped_by_what'] == 'rule'))) {
              //these rows are indexed by ruleID, so a foreach is needed...
              foreach($vtwpr_cart->cart_items[$k]->yousave_by_rule_info as $key => $yousave_by_rule) {
                $i = $yousave_by_rule['ruleset_occurrence'];
                //display info is tabulated for cumulative rule processing, but the Price Reduction has already taken place!!
                if ($vtwpr_rules_set[$i]->rule_execution_type == 'cart') {
                  $output  = '<tr class="vtwpr-discount-title-row" >';                  
                  $output .= '<td  class="vtwpr-ruleNameCol-' .$execType. ' vtwpr-border-cntl vtwpr-deal-msg" >' . stripslashes($yousave_by_rule['rule_short_msg']) . '</td>';
                  $output .= '</tr>';
                  echo  $output;
                  
                  //if a max was reached and msg supplied, print here 
                  if ($yousave_by_rule['rule_max_amt_msg'] > ' ') {    
                    $output  = '<tr class="vtwpr-discount-title-row" >';                  
                    $output .= '<td  class="vtwpr-ruleNameCol-' .$execType. ' vtwpr-border-cntl vtwpr-deal-msg" >' . stripslashes($yousave_by_rule['rule_max_amt_msg']) . '</td>';
                    $output .= '</tr>';
                  echo  $output;                  
                  }
                  
                  $amt = $yousave_by_rule['yousave_amt']; 
                  $units = $yousave_by_rule['discount_applies_to_qty'];                  
                  vtwpr_print_discount_detail_line($amt, $units, $execType, $k);
                }
              }
            } else {   //show discounts by product
                  $amt = $vtwpr_cart->cart_items[$k]->yousave_total_amt; 
                  $units = $vtwpr_cart->cart_items[$k]->yousave_total_qty;                  
                  vtwpr_print_discount_detail_line($amt, $units, $execType, $k);
           }
        }
      }

    return;
    
  }

  function vtwpr_print_cart_widget_title() {     
    global $vtwpr_setup_options;
    if ($vtwpr_setup_options['show_cartWidget_discount_titles_above_details'] == 'yes') {    
      $output;  
      $output .= '<tr id="vtwpr-discount-title-cartWidget" >';
      $output .= '<td colspan="' .$vtwpr_setup_options['cartWidget_html_colspan_value']. '" id="vtwpr-discount-title-cartWidget-line">';
      $output .= '<div class="vtwpr-discount-prodLine-cartWidget" >';
      
      $output .= '<span class="vtwpr-discount-prodCol-cartWidget">&nbsp;</span>';
      
      $output .= '<span class="vtwpr-discount-unitCol-cartWidget">&nbsp;</span>';
  
      $output .= '<span class="vtwpr-discount-amtCol-cartWidget">' .  __('Discount', 'vtwpr') . '</span>';
      
      $output .= '</div>'; //end prodline
      $output .= '</td>';
      $output .= '</tr>';

      echo  $output;
    }
    return;   
  }
 
     
	function vtwpr_print_discount_detail_line($amt, $units, $execType, $k) {  
    global $vtwpr_cart, $vtwpr_cart_item, $vtwpr_info, $vtwpr_rules_set, $vtwpr_rule, $vtwpr_setup_options;
    $output;
    $output .= '<tr class="vtwpr-discount-total-for-product-rule-row-' .$execType. '  bottomLine-' .$execType. '" >';
    $output .= '<td colspan="' .$vtwpr_setup_options['' .$execType. '_html_colspan_value']. '">';
    $output .= '<div class="vtwpr-discount-prodLine-' .$execType. '" >';
    
    $output .= '<span class="vtwpr-discount-prodCol-' .$execType. '" id="vtwpr-discount-product-id-' . $vtwpr_cart->cart_items[$k]->product_id . '">';
    $output .= $vtwpr_cart->cart_items[$k]->product_name;
    $output .= '</span>';
    
    $output .= '<span class="vtwpr-discount-unitCol-' .$execType. '">' . $units . '</span>';
    
    $amt = vtwpr_format_money_element($amt); 
    $output .= '<span class="vtwpr-discount-amtCol-' .$execType. '">' . $vtwpr_setup_options['' .$execType. '_credit_detail_label'] . ' ' .$amt . '</span>';
    
    $output .= '</div>'; //end prodline
    $output .= '</td>';
    $output .= '</tr>';
    echo  $output;  
 }

/*  
  //coupon discount only shows at Checkout 
	function vtwpr_print_coupon_discount_row($execType) {
    global $woocommerce, $vtwpr_setup_options, $vtwpr_rules_set;

    $output;
    $output .= '<tr class="vtwpr-discount-total-for-product-rule-row-' .$execType. '  bottomLine-' .$execType. '  vtwpr-coupon_discount-' .$execType. '" >';
    $output .= '<td colspan="' .$vtwpr_setup_options['' .$execType. '_html_colspan_value']. '">';
    $output .= '<div class="vtwpr-discount-prodLine-' .$execType. '" >';
    
    $output .= '<span class="vtwpr-discount-prodCol-' .$execType. ' vtwpr-coupon_discount-literal-' .$execType. '">';
    $output .= __('Coupon Discount: ', 'vtwpr'); 
    $output .= '</span>';
    
    $output .= '<span class="vtwpr-discount-unitCol-' .$execType. '">&nbsp;</span>';
    
    $labelType = $execType . '_credit_detail_label';
    
    $amt = vtwpr_format_money_element($vtwpr_rules_set[0]->coupons_amount_without_rule_discounts);  //show original coupon amt as credit
    $output .= '<span class="vtwpr-discount-amtCol-' .$execType. '  vtwpr-coupon_discount-amt-' .$execType. '">' . $vtwpr_setup_options['' .$execType. '_credit_detail_label'] . ' ' .$amt . '</span>';
    
    $output .= '</div>'; //end prodline
    $output .= '</td>';
    $output .= '</tr>';
    echo  $output; 
       
    return;
    
  }
 */  
   
	//***************************************
  // Subtotal - Cart Purchases:
  //***************************************
  function vtwpr_print_cart_purchases_subtotal($execType) {
    global $vtwpr_cart, $woocommerce, $vtwpr_cart_item, $vtwpr_info, $vtwpr_rules_set, $vtwpr_rule, $vtwpr_setup_options;   
      $subTotalCheck = 'show_' .$execType. '_purchases_subtotal';
      if ($vtwpr_setup_options[$subTotalCheck] == 'none') {     
        return;
      }


      $output;
      if ($vtwpr_setup_options[$subTotalCheck] == 'beforeDiscounts') {
          $output .= '<tr class="vtwpr-discount-total-' .$execType. '" >';
          $output .= '<td colspan="' .$vtwpr_setup_options['' .$execType. '_html_colspan_value'].'" class="vtwpr-discount-total-' .$execType. '-line">';
          $output .= '<div class="vtwpr-discount-prodLine-' .$execType. '" >';
          
          $output .= '<span class="vtwpr-discount-totCol-' .$execType. '">';
          $output .= $vtwpr_setup_options['' .$execType. '_credit_subtotal_title'];
          $output .= '</span>';
      
 //       due to a WPEC problem,  $vtwpr_cart->cart_original_total_amt  may be inaccurate - use wpec's own subtotaling....
 //         $subTotal = $vtwpr_cart->cart_original_total_amt;    //show as a credit 
          $amt = vtwpr_get_Woo_cartSubtotal();
  
          $labelType = $execType . '_credit_detail_label';  
          $output .= '<span class="vtwpr-discount-totAmtCol-' .$execType. '"> &nbsp;&nbsp;' .$amt . '</span>';
          
          $output .= '</div>'; //end prodline
          $output .= '</td>';
          $output .= '</tr>'; 
      } else {
          $output .= '<tr class="vtwpr-discount-total-' .$execType. '" >';
          $output .= '<td colspan="' .$vtwpr_setup_options['' .$execType. '_html_colspan_value'].'" class="vtwpr-discount-total-' .$execType. '-line">';
          $output .= '<div class="vtwpr-discount-prodLine-' .$execType. '" >';
          
          $output .= '<span class="vtwpr-discount-totCol-' .$execType. '">';
          $output .= $vtwpr_setup_options['' .$execType. '_credit_subtotal_title'];
          $output .= '</span>';
      
      
 //         $subTotal = $vtwpr_cart->cart_original_total_amt;    //show as a credit
          $amt = vtwpr_get_Woo_cartSubtotal();
          
          $labelType = $execType . '_credit_detail_label';  
          $output .= '<span class="vtwpr-discount-totAmtCol-' .$execType. '"> &nbsp;&nbsp;' .$amt . '</span>';
          
          $output .= '</div>'; //end prodline
          $output .= '</td>';
          $output .= '</tr>'; 
      }
      echo  $output;
   
    return;
    
  }

  //***************************************
  // Subtotal with Discount:  (print)
  //***************************************
	function vtwpr_print_new_cart_checkout_subtotal_line($execType) {
    global $vtwpr_cart, $woocommerce, $vtwpr_cart_item, $vtwpr_info, $vtwpr_rules_set, $vtwpr_rule, $vtwpr_setup_options;   

      $output;
 
      $output .= '<tr class="vtwpr-discount-total-' .$execType. ' vtwpr-new-subtotal-line" >';
      $output .= '<td colspan="' .$vtwpr_setup_options['' .$execType. '_html_colspan_value'].'" class="vtwpr-discount-total-' .$execType. '-line">';
      $output .= '<div class="vtwpr-discount-prodLine-' .$execType. '" >';
      
      $output .= '<span class="vtwpr-discount-totCol-' .$execType. '">';
      $output .= $vtwpr_setup_options['' .$execType. '_new_subtotal_label'];
      $output .= '</span>';
  
      //$subTotal = $vtwpr_cart->cart_original_total_amt - $vtwpr_cart->yousave_cart_total_amt;    //show as a credit
      $subTotal  = $woocommerce->cart->subtotal;
  
   //no longer used...   $subTotal -= $vtwpr_cart->yousave_cart_total_amt;
      $subTotal  -= $vtwpr_cart->cart_discount_subtotal;
   
      $amt = vtwpr_format_money_element($subTotal);
      $labelType = $execType . '_credit_detail_label';  
      $output .= '<span class="vtwpr-discount-totAmtCol-' .$execType. ' vtwpr-new-subtotal-amt"> &nbsp;&nbsp;' .$amt . '</span>';
      
      $output .= '</div>'; //end prodline
      $output .= '</td>';
      $output .= '</tr>'; 

      echo  $output;
   
    return;  
  }
    
     
	function vtwpr_print_cart_discount_total($execType) {
    global $vtwpr_cart, $woocommerce, $vtwpr_cart_item, $vtwpr_info, $vtwpr_rules_set, $vtwpr_rule, $vtwpr_setup_options;
    
    $printRowsCheck = 'show_' .$execType. '_discount_total_line';
    
    if ($vtwpr_setup_options[$printRowsCheck] == 'no') {
      return;
    }
    $output;
    $output .= '<tr class="vtwpr-discount-total-' .$execType. ' vtwpr-discount-line" >';    
    $output .= '<td colspan="' .$vtwpr_setup_options['' .$execType. '_html_colspan_value']. '" class="vtwpr-discount-total-' .$execType. '-line ">';
    $output .= '<div class="vtwpr-discount-prodLine-' .$execType. '" >';
    
    $output .= '<span class="vtwpr-discount-totCol-' .$execType. '">';
    $output .= $vtwpr_setup_options['' .$execType. '_credit_total_title'];
    $output .= '</span>';


    $amt = vtwpr_format_money_element($vtwpr_cart->cart_discount_subtotal); 

    
    $output .= '<span class="vtwpr-discount-totAmtCol-' .$execType. ' vtwpr-discount-amt">' . $vtwpr_setup_options['' .$execType. '_credit_detail_label'] . ' ' .$amt . '</span>';
     
    $output .= '</div>'; //end prodline
    $output .= '</td>';
    $output .= '</tr>';
    echo  $output;
       
    return;
    
  }
   
    
     /*
    \n = CR (Carriage Return) // Used as a new line character in Unix
    \r = LF (Line Feed) // Used as a new line character in Mac OS
    \n\r = CR + LF // Used as a new line character in Windows
    (char)13 = \n = CR // Same as \n
    http://en.wikipedia.org/wiki/Newline
    */
  /* ************************************************
  **   Assemble all of the cart discount row info FOR email/transaction results messaging  
  *        $msgType = 'html' or 'plainText'            
  *************************************************** */
	function vtwpr_email_cart_reporting($msgType) {
    global $vtwpr_cart, $vtwpr_cart_item, $vtwpr_rules_set, $vtwpr_info, $vtwpr_setup_options;
    $output;
    
    if ($vtwpr_setup_options['show_checkout_discount_titles_above_details'] == 'yes') {
      if ($msgType == 'html') {
        //Skip a line between products and discounts      		
        $output .= '<tr>';
        $output .= '<td style="text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word"  colspan="3"> &nbsp;</td>';				
        $output .= '</tr>';  
        
        //New headers, but printed as TD instead, to keep the original structure going...                    
        $output .= '<tr>';
        $output .= '<td style="text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;font-weight:bold;">' . __('Discount Product', 'vtwpr') .'</td>';			
        $output .= '<td style="text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;font-weight:bold;">' . __('Quantity', 'vtwpr') .'</td>';			
        $output .= '<td style="text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;font-weight:bold;">' . __('Amount', 'vtwpr') .'</td>';		
        $output .= '</tr>';    
      
      } else {
        //first a couple of page ejects
        $output .= "\r\n \r\n";
        $output .= __( 'Discounts ', 'vtwpr' );
        $output .= "\r\n";
      }
    }
 
    //get the discount details    
    $output .= vtwpr_email_cart_discount_rows($msgType);
     
    if ($vtwpr_setup_options['show_checkout_discount_total_line'] == 'yes') {
      if ($msgType == 'html') {
        $amt = vtwpr_format_money_element($vtwpr_cart->yousave_cart_total_amt); 
        $output .= '<tr>';
        $output .= '<td style="text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;font-weight:bold"  colspan="2">'. $vtwpr_setup_options['checkout_credit_total_title'] .'</td>';						
        $output .= '<td style="text-align:left;vertical-align:middle;border:1px solid #eee">'  . $vtwpr_setup_options['checkout_credit_total_label'] .$amt .'</td>';		
        $output .= '</tr>';   
        $output .= '<tr>';
        $output .= '<td style="text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word"  colspan="3"> &nbsp;</td>';				
        $output .= '</tr>';
      } else {
        $output .= "\r\n";
        $output .= "\n" .$vtwpr_setup_options['checkout_credit_total_title'];
        $output .= "\n" .$vtwpr_setup_options['checkout_credit_total_label'] .$amt ;
      }
    }      
           
    return $output;
    
  }
  
  //coupon discount only shows at Checkout 
	function vtwpr_email_cart_coupon_discount_row($msgType) {
    global $vtwpr_cart, $vtwpr_rules_set, $vtwpr_setup_options;

    $output;
    $amt = vtwpr_format_money_element($vtwpr_cart->wpsc_orig_coupon_amount);  //show original coupon amt as credit
    
    if ($msgType == 'html')  {
      $output .= '<tr>';
        $output .= '<td colspan="2">' . __('Coupon Discount', 'vtwpr') .'</td>';
        $output .= '<td>' . $vtwpr_setup_options['checkout_credit_detail_label'] . ' ' .$amt .'</td>';
      $output .= '</tr>';    
    } else {
      $output .= __('Coupon Discount: ', 'vtwpr'); 
      
      $output .= $amt;
      $output .= "\r\n \r\n";
    }

    return $output; 
    
  }      
    
  /* ************************************************
  **   Assemble all of the cart discount row info              
  *************************************************** */
	function vtwpr_email_cart_discount_rows($msgType) {
    global $vtwpr_cart, $vtwpr_cart_item, $vtwpr_info, $vtwpr_rules_set, $vtwpr_rule, $vtwpr_setup_options;
       
      $output;

      $sizeof_cart_items = sizeof($vtwpr_cart->cart_items);
      for($k=0; $k < $sizeof_cart_items; $k++) {  
       	if ( $vtwpr_cart->cart_items[$k]->yousave_total_amt > 0) {            
            if ($vtwpr_setup_options['show_checkout_discount_details_grouped_by_what']   == 'rule') {
              //these rows are indexed by ruleID, so a foreach is needed...
              foreach($vtwpr_cart->cart_items[$k]->yousave_by_rule_info as $key => $yousave_by_rule) {
              
                //display info is tabulated for cumulative rule processing, but the Price Reduction has already taken place!!
                if ($yousave_by_rule['rule_execution_type'] == 'cart') {
                  //CREATE NEW SWITCH
                  //TEST TEST TEST
                 // if ($vtwpr_setup_options['show_checkout_discount_each_msg'] == 'yes') {
                    if ($msgType == 'html')  {
                        $output .= '<tr  class="vtwpr-rule-msg-checkout"  >';
                        $output .= '<td style="text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;" colspan="3">' . stripslashes($yousave_by_rule['rule_short_msg'])  .'</td>';				
                        $output .= '</tr>';                       
                    } else {
                      $output .= "\n" .  stripslashes($yousave_by_rule['rule_short_msg']) . "\r\n"; 
                    }                                 
                    $amt   = $yousave_by_rule['yousave_amt']; 
                    $units = $yousave_by_rule['discount_applies_to_qty'];                  
                    $output .= vtwpr_email_discount_detail_line($amt, $units, $msgType, $k); 
              
                 // } 
                }                
              }
            } else {   //show discounts by product
                  $amt = $vtwpr_cart->cart_items[$k]->yousave_total_amt; 
                  $units = $vtwpr_cart->cart_items[$k]->yousave_total_qty;                  
                  $output .= vtwpr_email_discount_detail_line($amt, $units, $msgType, $k);
           }
        }
      }

    return $output;
    
  }

    
	function vtwpr_email_discount_detail_line($amt, $units, $msgType, $k) {  
    global $vtwpr_cart, $vtwpr_cart_item, $vtwpr_info, $vtwpr_rules_set, $vtwpr_rule, $vtwpr_setup_options;
      $output;
    $amt = vtwpr_format_money_element($amt); //mwn
    if ($msgType == 'html')  {
      $output .= '<tr>';

      if (sizeof($vtwpr_cart->cart_items[$k]->variation_array) > 0   ) {
        $output .= '<td  class="vtwpr-product-name-email" style="text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word"><span class="vtwpr-product-name-span">' . $vtwpr_cart->cart_items[$k]->parent_product_name .'</span>';
        $output .= '<small>';
       // $output .= '<dl class="variation">';
        foreach($vtwpr_cart->cart_items[$k]->variation_array as $key => $value) {          
          $name = sanitize_title( str_replace( 'attribute_pa_', '', $key  ) );  //post v 2.1
          $name = sanitize_title( str_replace( 'pa_', '', $name  ) );   //pre v 2.1
          $name = ucwords($name);  
          $output .= '<br>'. $name . ': ' .$value ;           
        }
        //$output .= '</dl></small>';
        $output .= '</small>';      			
        $output .= '</td>';     
      } else {
        $output .= '<td  class="vtwpr-product-name-email" style="text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word">' . $vtwpr_cart->cart_items[$k]->product_name .'</td>';
      }
			
      $output .= '<td class="vtwpr-quantity-email" style="text-align:left;vertical-align:middle;border:1px solid #eee">' . $units .'</td>';			
      $output .= '<td class="vtwpr-amount-email"  style="text-align:left;vertical-align:middle;border:1px solid #eee">' . $vtwpr_setup_options['checkout_credit_detail_label'] . ' ' .$amt .'</td>';		
      $output .= '</tr>';        
    } else {
      $output .= "\n" . __( 'Product: ', 'vtwpr' ); 
      $output .= "\n" . $vtwpr_cart->cart_items[$k]->product_name;
      $output .= "\n" . __( ' Discount Units: ', 'vtwpr' );
      $output .= "\n" . $units ;
      $output .= "\n" . __( ' Discount Amount: ', 'vtwpr' ); 
      $output .= "\n" . $amt;
      $output .= "\r\n";
    }
    
    return  $output;  
 }
   
	function vtwpr_email_cart_purchases_subtotal($msgType) {
    global $vtwpr_cart, $woocommerce, $vtwpr_cart_item, $vtwpr_info, $vtwpr_rules_set, $vtwpr_rule, $vtwpr_setup_options;   

    $output;
    //$subTotal = $vtwpr_cart->cart_original_total_amt;    //show as a credit
    $amt = vtwpr_get_Woo_cartSubtotal(); 
    
    if ($msgType == 'html')  {
      $output .= '<tr>';
        $output .= '<td  class="vtwpr-subtotal-email" colspan="2">' . $vtwpr_setup_options['checkout_credit_subtotal_title'] .'</td>';
        $output .= '<td>' . $amt .'</td>';
      $output .= '</tr>';   
    } else {
      $output .= $vtwpr_setup_options['checkout_credit_subtotal_title'];
      $output .= '  ';
      $output .= $amt;
      $output .= "\r\n";        
    }
    return $output;  
  }
 
     
	function vtwpr_email_cart_discount_total($msgType) {
    global $vtwpr_cart, $vtwpr_rules_set, $vtwpr_setup_options;

      $output;
  
      
      $amt = vtwpr_format_money_element($vtwpr_cart->yousave_cart_total_amt);      
          
    if ($msgType == 'html')  {
      $output .= '<tr>';
        $output .= '<td colspan="2">' . $vtwpr_setup_options['checkout_credit_total_title'] .'</td>';
        $output .= '<td>' . $vtwpr_setup_options['checkout_credit_total_label'] . ' ' .$amt .'</td>';
      $output .= '</tr>';   
    } else {      
      $output .= $vtwpr_setup_options['checkout_credit_total_title'];          //Discount Total
      $output .= $amt ;
      $output .= "\r\n";        
    }
    
    return $output;  
    
  }
   
	
  //***************************************
  // Subtotal with Discount:  (email)
  //***************************************
  function vtwpr_email_new_cart_checkout_subtotal_line($msgType) {
    global $vtwpr_cart, $woocommerce, $vtwpr_cart_item, $vtwpr_info, $vtwpr_rules_set, $vtwpr_rule, $vtwpr_setup_options;   

      $output;
   
      // for wpec $vtwpr_cart->cart_original_total_amt is not accurate - use wpec's own routine
      //$subTotal = $vtwpr_cart->cart_original_total_amt - $vtwpr_cart->yousave_cart_total_amt;    //show as a credit
      
      $subTotal  = $woocommerce->cart->subtotal;
      
      //*****************************
      //No longer used - $subTotal -= $vtwpr_cart->yousave_cart_total_amt;
      //*****************************
      $subTotal -= $vtwpr_cart->cart_discount_subtotal;   //may or may not contain the coupon amount, depending on passed value calling function
      
      $amt = vtwpr_format_money_element($subTotal);

    if ($msgType == 'html')  {
      $output .= '<tr>';
        $output .= '<td colspan="2">' . $vtwpr_setup_options['checkout_new_subtotal_label'] .'</td>';
        $output .= '<td>' . $amt .'</td>';
      $output .= '</tr>';
    } else {
      $output .= $vtwpr_setup_options['checkout_new_subtotal_label'];
      $output .= '  '; 
      $output .= $amt;
      $output .= "\r\n";        
    }
    
    return $output; 
  }  

   

  /* ************************************************
  **   Assemble all of the cart discount row info FOR email/transaction results messaging  
  *        $msgType = 'html' or 'plainText'            
  *************************************************** */
	function vtwpr_thankyou_cart_reporting() {
    global $vtwpr_cart, $vtwpr_cart_item, $vtwpr_rules_set, $vtwpr_info, $vtwpr_setup_options, $woocommerce;
    $output;
   	
    $output .=  '<h2 id="vtwpr-thankyou-title">' . __('Cart Discount Details', 'vtwpr') .'<h2>';
     	
    $output .= '<table class="shop_table order_details vtwpr-thankyou-table">';
    $output .= '<thead>';
    $output .= '<tr>';
    $output .= '<th class="product-name">' . __('Discount Product', 'vtwpr') .'</th>';
    $output .= '<th class="product-name">' . __('Discount Amount', 'vtwpr') .'</th>';					
    $output .= '</tr>';    
    $output .= '</thead>';
    
    if (($vtwpr_setup_options['show_checkout_discount_total_line'] == 'yes') || 
        ($vtwpr_setup_options['checkout_new_subtotal_line']        == 'yes')) {
        $output .= '<tfoot>';
        if ($vtwpr_setup_options['show_checkout_discount_total_line'] == 'yes') {
            $amt = vtwpr_format_money_element($vtwpr_cart->yousave_cart_total_amt); 
            $output .= '<tr class="checkout_credit_total">';
            $output .= '<th scope="row">'. $vtwpr_setup_options['checkout_credit_total_title'] .'</th>';						
            $output .= '<td><span class="amount">'  . $vtwpr_setup_options['checkout_credit_total_label'] .$amt .'</span></td>';		
            $output .= '</tr>';
        }
        /*
        if ($vtwpr_setup_options['checkout_new_subtotal_line'] == 'yes') {
            //can't use the regular routine ($subtotal = vtwpr_get_Woo_cartSubtotal(); ), as it returns a formatted result
           if ( $woocommerce->tax_display_cart == 'excl' ) {
        			$subtotal = $woocommerce->cart->subtotal_ex_tax ;
        		} else {
        			$subtotal = $woocommerce->cart->subtotal;
            }   
            $subtotal -= $vtwpr_cart->yousave_cart_total_amt;
            $amt = vtwpr_format_money_element($subtotal);              
            $output .= '<tr class="checkout_new_subtotal">';
            $output .= '<th scope="row">'. $vtwpr_setup_options['checkout_new_subtotal_label'] .'</th>';						
            $output .= '<td><span class="amount">'  . $vtwpr_setup_options['checkout_credit_detail_label'] .$amt .'</span></td>';		
            $output .= '</tr>';
        }  
        */      
        $output .= '</tfoot>';   
    }   
 
    $output .= '<tbody>';
 
    //get the discount details    
    $output .= vtwpr_thankyou_cart_discount_rows($msgType);
  
    $output .= '</tbody>';
    $output .= '</table>';
           
    return $output;
    
  }
    
  /* ************************************************
  **   Assemble all of the cart discount row info              
  *************************************************** */
	function vtwpr_thankyou_cart_discount_rows($msgType) {
    global $vtwpr_cart, $vtwpr_cart_item, $vtwpr_info, $vtwpr_rules_set, $vtwpr_rule, $vtwpr_setup_options;
       
      $output;

      $sizeof_cart_items = sizeof($vtwpr_cart->cart_items);
      for($k=0; $k < $sizeof_cart_items; $k++) {  
       	if ( $vtwpr_cart->cart_items[$k]->yousave_total_amt > 0) {            
            if ($vtwpr_setup_options['show_checkout_discount_details_grouped_by_what']   == 'rule') {
              //these rows are indexed by ruleID, so a foreach is needed...
              foreach($vtwpr_cart->cart_items[$k]->yousave_by_rule_info as $key => $yousave_by_rule) {
              
                //display info is tabulated for cumulative rule processing, but the Price Reduction has already taken place!!
                if ($yousave_by_rule['rule_execution_type'] == 'cart') {
                  //CREATE NEW SWITCH
                  //TEST TEST TEST
                 // if ($vtwpr_setup_options['show_checkout_discount_each_msg'] == 'yes') {
                      $output .= '<tr class = "order_table_item">';
                      $output .= '<td  class="product-name">' . stripslashes($yousave_by_rule['rule_short_msg'])  .'</td>';
                      //td with blank needed to complete the border line in the finished product
                      $output .= '<td  class="product-name">&nbsp;</td>';				
                      $output .= '</tr>';                       
                 // }                                 
                    $amt   = $yousave_by_rule['yousave_amt']; 
                    $units = $yousave_by_rule['discount_applies_to_qty'];                  
                    $output .= vtwpr_thankyou_discount_detail_line($amt, $units, $msgType, $k); 
              
                }                
              }
            } else {   //show discounts by product
                  $amt = $vtwpr_cart->cart_items[$k]->yousave_total_amt; 
                  $units = $vtwpr_cart->cart_items[$k]->yousave_total_qty;                  
                  $output .= vtwpr_thankyou_discount_detail_line($amt, $units, $msgType, $k);
           }
        }
      }

    return $output;
    
  }
     
	function vtwpr_thankyou_discount_detail_line($amt, $units, $msgType, $k) {  
    global $vtwpr_cart, $vtwpr_cart_item, $vtwpr_info, $vtwpr_rules_set, $vtwpr_rule, $vtwpr_setup_options;
      $output;
    $amt = vtwpr_format_money_element($amt); //mwn

    $output .= '<tr class = "order_table_item">';
    /*
    $output .= '<td  class="product-name">' . $vtwpr_cart->cart_items[$k]->product_name ;
    $output .= '<strong class="product-quantity"> &times; ' . $units  .'</strong>';				
    $output .= '</td>';
    */

    if (sizeof($vtwpr_cart->cart_items[$k]->variation_array) > 0   ) {
      $output .= '<td  class="product-name vtwpr-product-name" ><span class="vtwpr-product-name-span">' . $vtwpr_cart->cart_items[$k]->parent_product_name .'</span>';
      $output .= '<strong class="product-quantity"> &times; ' . $units  .'</strong>';	
      $output .= '<dl class="variation">';
      foreach($vtwpr_cart->cart_items[$k]->variation_array as $key => $value) {          
        $name = sanitize_title( str_replace( 'attribute_pa_', '', $key  ) );  //post v 2.1
        $name = sanitize_title( str_replace( 'pa_', '', $name  ) );   //pre v 2.1
        $name = ucwords($name);  
        $output .= '<dt>'. $name . ': </dt>';
        $output .= '<dd>'. $value .'</dd>';           
      }
      $output .= '</dl>';
            			
      $output .= '</td>';     
    } else {
      $output .= '<td  class="product-name" >' . $vtwpr_cart->cart_items[$k]->product_name ;
			$output .= '<strong class="product-quantity"> &times; ' . $units  .'</strong>';	
      $output .= '</td>';
    }

    
    $output .= '<td  class="product-total">';
    $output .= '<span class="amount">' . $vtwpr_setup_options['checkout_credit_detail_label'] .$amt .'</span>';				
    $output .= '</td>';

    
                          
    $output .= '</tr>'; 
    
    return  $output;  
 }
   
	function vtwpr_thankyou_cart_purchases_subtotal($msgType) {
    global $vtwpr_cart, $woocommerce, $vtwpr_cart_item, $vtwpr_info, $vtwpr_rules_set, $vtwpr_rule, $vtwpr_setup_options;   

    $output;
    //$subTotal = $vtwpr_cart->cart_original_total_amt;    //show as a credit
    $amt = vtwpr_get_Woo_cartSubtotal(); 
    
    if ($msgType == 'html')  {
      $output .= '<tr>';
        $output .= '<td colspan="2">' . $vtwpr_setup_options['checkout_credit_subtotal_title'] .'</td>';
        $output .= '<td>' . $amt .'</td>';
      $output .= '</tr>';   
    } else {
      $output .= $vtwpr_setup_options['checkout_credit_subtotal_title'];
      $output .= '  ';
      $output .= $amt;
      $output .= "\r\n";        
    }
    return $output;  
  }


  

  /* ************************************************
  **   Assemble all of the cart discount row info FOR email/transaction results messaging  
  *        $msgType = 'html' or 'plainText'            
  *************************************************** */
	function vtwpr_checkout_cart_reporting() {
    global $vtwpr_cart, $vtwpr_cart_item, $vtwpr_rules_set, $vtwpr_info, $vtwpr_setup_options, $woocommerce;
    $output;        
   	if (($vtwpr_setup_options['show_checkout_discount_detail_lines'] == 'yes') ||  
        ($vtwpr_setup_options['show_checkout_discount_total_line']   == 'yes') ||  
        ($vtwpr_setup_options['checkout_new_subtotal_line']          == 'yes') ) {	
            $output .= '<table class="shop_table cart vtwpr_shop_table" cellspacing="0">';
            $output .= '<thead>';
            $output .= '<tr class="checkout_discount_headings">';
            $output .= '<th  class="product-name" >' . __('Discount Product', 'vtwpr') .'</th>';
            $output .= '<th  class="product-quantity">' . __('Quantity', 'vtwpr') .'</th>';
            $output .= '<th  class="product-subtotal" >' . __('Discount Amount', 'vtwpr') .'</th>';					
            $output .= '</tr>';    
            $output .= '</thead>';
    }
    
    if (($vtwpr_setup_options['show_checkout_discount_total_line'] == 'yes') || 
        ($vtwpr_setup_options['checkout_new_subtotal_line']        == 'yes')) { 
        $output .= '<tfoot>';
         if ($vtwpr_setup_options['show_checkout_discount_total_line'] == 'yes') {
            $amt = vtwpr_format_money_element($vtwpr_cart->yousave_cart_total_amt); 
            $output .= '<tr class="checkout_discount_total_line">';
            $output .= '<th scope="row" colspan="2">'. $vtwpr_setup_options['checkout_credit_total_title'] .'</th>';						
            $output .= '<td ><span class="amount">'  . $vtwpr_setup_options['checkout_credit_total_label'] .$amt .'</span></td>';		
            $output .= '</tr>';
        }
         
        if ($vtwpr_setup_options['checkout_new_subtotal_line'] == 'yes') {
            //can't use the regular routine ($subtotal = vtwpr_get_Woo_cartSubtotal(); ), as it returns a formatted result
           if ( $woocommerce->cart->tax_display_cart == 'excl' ) {
        			$subtotal = $woocommerce->cart->subtotal_ex_tax ;
        		} else {
        			$subtotal = $woocommerce->cart->subtotal;
            }   
            $subtotal -= $vtwpr_cart->yousave_cart_total_amt;
            $amt = vtwpr_format_money_element($subtotal);                     
            $output .= '<tr class="checkout_new_subtotal">';
            $output .= '<th scope="row" colspan="2">'. $vtwpr_setup_options['checkout_new_subtotal_label'] .'</th>';						
            $output .= '<td ><span class="amount">'  . $amt .'</span></td>';		
            $output .= '</tr>'; 
        }        
        $output .= '</tfoot>';   
    }   
 
    $output .= '<tbody>';

    //new    
    if ($vtwpr_setup_options['show_checkout_purchases_subtotal'] == 'beforeDiscounts') {
      $amt = vtwpr_get_Woo_cartSubtotal();
      $output .= '<tr class="checkout_purchases_subtotal">';
      $output .= '<th scope="row" colspan="2">'. $vtwpr_setup_options['checkout_credit_subtotal_title'] .'</th>';						
      $output .= '<td ><span class="amount">'   .$amt .'</span></td>';		
      $output .= '</tr>';
    }
 
    if ($vtwpr_setup_options['show_checkout_discount_detail_lines'] == 'yes') {
      //get the discount details    
      $output .= vtwpr_checkout_cart_discount_rows($msgType);
     }
     
    if ($vtwpr_setup_options['show_checkout_purchases_subtotal'] == 'withDiscounts') {
      $amt = vtwpr_get_Woo_cartSubtotal();
      $output .= '<tr class="checkout_purchases_subtotal">';
      $output .= '<th scope="row" colspan="2">'. $vtwpr_setup_options['checkout_credit_subtotal_title'] .'</th>';						
      $output .= '<td ><span class="amount">'   .$amt .'</span></td>';		
      $output .= '</tr>';
    }


    $output .= '</tbody>';
    $output .= '</table>';
    
    echo $output;
           
    return;
    
  }
    
  /* ************************************************
  **   Assemble all of the cart discount row info              
  *************************************************** */
	function vtwpr_checkout_cart_discount_rows($msgType) {
    global $vtwpr_cart, $vtwpr_cart_item, $vtwpr_info, $vtwpr_rules_set, $vtwpr_rule, $vtwpr_setup_options;
       
      $output;

      $sizeof_cart_items = sizeof($vtwpr_cart->cart_items);
      for($k=0; $k < $sizeof_cart_items; $k++) {  
       	if ( $vtwpr_cart->cart_items[$k]->yousave_total_amt > 0) {            
            if ($vtwpr_setup_options['show_checkout_discount_details_grouped_by_what']   == 'rule') {
              //these rows are indexed by ruleID, so a foreach is needed...
              foreach($vtwpr_cart->cart_items[$k]->yousave_by_rule_info as $key => $yousave_by_rule) {
              
                //display info is tabulated for cumulative rule processing, but the Price Reduction has already taken place!!
                if ($yousave_by_rule['rule_execution_type'] == 'cart') {
                  //CREATE NEW SWITCH
                  //TEST TEST TEST
                 // if ($vtwpr_setup_options['show_checkout_discount_each_msg'] == 'yes') {
                      $output .= '<tr class = "order_table_item">';
                      $output .= '<td  class="product-name vtwpr-rule_msg" colspan="3">' . stripslashes($yousave_by_rule['rule_short_msg'])  .'</td>';			
                      $output .= '</tr>';                       
                 // }                                 
                    $amt   = $yousave_by_rule['yousave_amt']; 
                    $units = $yousave_by_rule['discount_applies_to_qty'];                  
                    $output .= vtwpr_checkout_discount_detail_line($amt, $units, $msgType, $k); 
              
                }                
              }
            } else {   //show discounts by product
                  $amt = $vtwpr_cart->cart_items[$k]->yousave_total_amt; 
                  $units = $vtwpr_cart->cart_items[$k]->yousave_total_qty;                  
                  $output .= vtwpr_checkout_discount_detail_line($amt, $units, $msgType, $k);
           }
        }
      }

    return $output;
    
  }
     
	function vtwpr_checkout_discount_detail_line($amt, $units, $msgType, $k) {  
    global $vtwpr_cart, $vtwpr_cart_item, $vtwpr_info, $vtwpr_rules_set, $vtwpr_rule, $vtwpr_setup_options;
      $output;
    $amt = vtwpr_format_money_element($amt); //mwn

    $output .= '<tr class = "order_table_item">';

    if (sizeof($vtwpr_cart->cart_items[$k]->variation_array) > 0   ) {
      $output .= '<td  class="product-name vtwpr-product-name" ><span class="vtwpr-product-name-span">' . $vtwpr_cart->cart_items[$k]->parent_product_name .'</span>';
      
      $output .= '<dl class="variation">';

      foreach($vtwpr_cart->cart_items[$k]->variation_array as $key => $value) {          
        $name = sanitize_title( str_replace( 'attribute_pa_', '', $key  ) );  //post v 2.1
        $name = sanitize_title( str_replace( 'pa_', '', $name  ) );   //pre v 2.1
        $name = ucwords($name);  
        $output .= '<dt>'. $name . ': </dt>';
        $output .= '<dd>'. $value .'</dd>';           
      }
      $output .= '</dl>';
            			
      $output .= '</td>';
      //$output .= '<strong class="product-quantity"> &times; ' . $units  .'</strong>';	     
    } else {
      $output .= '<td  class="product-name" >' . $vtwpr_cart->cart_items[$k]->product_name ;
     // $output .= '<strong class="product-quantity"> &times; ' . $units  .'</strong>';				
      $output .= '</td>';
    }

    $output .= '<td  class="product-quantity" style="text-align:middle;">' . $units .'</td>';
    
    $output .= '<td  class="product-total">';
    $output .= '<span class="amount">' . $vtwpr_setup_options['checkout_credit_detail_label'] .$amt .'</span>';				
    $output .= '</td>';
                          
    $output .= '</tr>'; 
    
    return  $output;  
 }
   
	function vtwpr_checkout_cart_purchases_subtotal($msgType) {
    global $vtwpr_cart, $woocommerce, $vtwpr_cart_item, $vtwpr_info, $vtwpr_rules_set, $vtwpr_rule, $vtwpr_setup_options;   

    $output;
    //$subTotal = $vtwpr_cart->cart_original_total_amt;    //show as a credit
    $amt = vtwpr_get_Woo_cartSubtotal(); 
    
    if ($msgType == 'html')  {
      $output .= '<tr>';
        $output .= '<td colspan="2">' . $vtwpr_setup_options['checkout_credit_subtotal_title'] .'</td>';
        $output .= '<td>' . $amt .'</td>';
      $output .= '</tr>';   
    } else {
      $output .= $vtwpr_setup_options['checkout_credit_subtotal_title'];
      $output .= '  ';
      $output .= $amt;
      $output .= "\r\n";        
    }
    return $output;  
  }


  
  
  function vtwpr_numberOfDecimals($value) {
      if ((int)$value == $value) {
          return 0;
      }
      else if (! is_numeric($value)) {
          // throw new Exception('numberOfDecimals: ' . $value . ' is not a number!');
          return false;
      }
  
      return strlen($value) - strrpos($value, '.') - 1;  
  }

   function vtwpr_print_rule_full_msg($i) { 
    global $vtwpr_rules_set;
    $output  = '<span  class="vtwpr-full-messages" id="vtwpr-category-deal-msg' . $vtwpr_rules_set[$i]->post_id . '">';
    $output .= stripslashes($vtwpr_rules_set[$i]->discount_product_full_msg);
    $output .= '</span>'; 
    return $output;    
   }  


  // ****************  
  // Date Validity Rule Test
  // ****************             
   function vtwpr_rule_date_validity_test($i) {  
       global $vtwpr_rules_set;

       switch( $vtwpr_rules_set[$i]->rule_on_off_sw_select ) {
          case 'on':  //continue, use scheduling dates
            break;
          case 'off': //rule is always off!!!
              return false;
            break;
          case 'onForever': //rule is always on!!
              return true;
            break;
        }

       $today = date("Y-m-d");
       
       for($t=0; $t < sizeof($vtwpr_rules_set[$i]->periodicByDateRange); $t++) {
          if ( ($today >= $vtwpr_rules_set[$i]->periodicByDateRange[$t]['rangeBeginDate']) &&
               ($today <= $vtwpr_rules_set[$i]->periodicByDateRange[$t]['rangeEndDate']) ) {
             return true;  
          }
       } 
        
       return false; //marks test as valid
   }   


  /* ************************************************
  *    PRODUCT META INCLUDE/EXCLUDE RULE ID LISTS
  *       Meta box added to PRODUCT in rules-ui.php 
  *             updated in wholesale-pricing.php    
  * ************************************************               
  **   Products can be individually added to two lists:
  *       Include only list - **includes** the product in a rule population 
  *         *only" if:
  *           (1) The product already participates in the rule
  *           (2) The product is in the include only rule list 
*       Exclude list - excludes the product in a rule population 
  *         *only" if:
  *           (1) The product already participates in the rule
  *           (2) The product is in the exclude rule list        
  *************************************************** */  

  //depending on the switch setting, this will be either include or exclude - but from the function's
  //  point of view, it doesn't matter...
  function vtwpr_fill_include_exclude_lists($checked_list = NULL) { 
      global $wpdb, $post, $vtwpr_setup_options;

      $varsql = "SELECT posts.`id`
            			FROM `".$wpdb->posts."` AS posts			
            			WHERE posts.`post_status` = 'publish' AND posts.`post_type`= 'vtwpr-rule'";                    
    	$rule_id_list = $wpdb->get_col($varsql);

      //Include or Exclude list
      foreach ($rule_id_list as $rule_id) {     //($rule_ids as $rule_id => $info)
          $post = get_post($rule_id);
          $output  = '<li id="inOrEx-li-' .$rule_id. '">' ;
          $output  .= '<label class="selectit inOrEx-list-checkbox-label">' ;
          $output  .= '<input id="inOrEx-input-' .$rule_id. '" class="inOrEx-list-checkbox-class" ';
          $output  .= 'type="checkbox" name="includeOrExclude-checked_list[]" ';
          $output  .= 'value="'.$rule_id.'" ';
          $check_found = 'no';
          if ($checked_list) {
              if (in_array($rule_id, $checked_list)) {   //if variation is in previously checked_list   
                 $output  .= 'checked="checked"';
                 $check_found = 'yes';
              }                
          }
          $output  .= '>'; //end input statement
          $output  .= '&nbsp;' . $post->post_title;
          $output  .= '</label>';            
          $output  .= '</li>';
          echo  $output ;
       }
       
      return;   
  }
   
  function vtwpr_set_selected_timezone() {
   global $vtwpr_setup_options;
    //set server timezone to Store local for date processing
    switch( $vtwpr_setup_options['use_this_timeZone'] ) {
      case 'none':
      case 'keep':
        break;
      default:
          $useThisTimeZone = $vtwpr_setup_options['use_this_timeZone'];
          date_default_timezone_set($useThisTimeZone);
        break;
    }
  }

	//this routine only gets previouosly-stored session info
  function vtwpr_maybe_get_product_session_info($product_id) {
    global $vtwpr_info;    
    if(!isset($_SESSION)){
      session_start();
      header("Cache-Control: no-cache");
      header("Pragma: no-cache");
    }  
    // ********************************************************
    //this routine is also called during cart processing.             
    //  if so, get the session info if there, MOVE it to VTWPR_INFO and exit
    // ********************************************************
    if(isset($_SESSION['vtwpr_product_session_info_'.$product_id])) {      
      $vtwpr_info['product_session_info'] = $_SESSION['vtwpr_product_session_info_'.$product_id];
//echo 'at get session time, product id= ' .$product_id . '<br>';     
    } else {
      $vtwpr_info['product_session_info'] = array();
    }

           
  }  
          
   /* ************************************************
  **  get display session info and MOVE to $vtwpr_info['product_session_info']
  *  First time go to the DB.
  *  2nd thru nth go to session variable...
  *    If the ID is a Variation (only comes realtime from AJAX), the recompute is run to refigure price.
  *    
  * //$cart_processing_sw: 'yes' => only get the session info
  *                        'no'  => only get the session info
  *             
  *************************************************** */
	//PRICE only comes from  parent-cart-validation function vtwpr_show_product_catalog_price
  // $product_info comes from catalog calls...
  function vtwpr_get_product_session_info($product_id, $price=null){   
    global $post, $vtwpr_info;
//echo ' cv001a product_id= ' . $product_id. ' price= ' .$price. '<br>' ; //mwnt    
    //store product-specific session info
    if(!isset($_SESSION)){
      session_start();
      header("Cache-Control: no-cache");
      header("Pragma: no-cache");
    }  
    

    //if already in the session variable... => this routine can be called multiple times in displaying a single catalog price.  check first if already done.
    if(isset($_SESSION['vtwpr_product_session_info_'.$product_id])) {
       $vtwpr_info['product_session_info'] = $_SESSION['vtwpr_product_session_info_'.$product_id];   
      //will be a problem in Ajax...
      $current_time_in_seconds = time();          
      $user_role = vtwpr_get_current_user_role();      
      if ( ( ($current_time_in_seconds - $vtwpr_info['product_session_info']['session_timestamp_in_seconds']) > '3600' ) ||     //session data older than 60 minutes
           (  $user_role != $vtwpr_info['product_session_info']['user_role']) ) {    //current user role not the same as prev
        vtwpr_apply_rules_to_single_product($product_id, $price);
        //reset user role info, in case it changed
        $vtprd_info['product_session_info']['user_role'] = $user_role;
      }         
    } else { 
       //First time obtaining the info, also moves the data to $vtwpr_info       
      vtwpr_apply_rules_to_single_product($product_id, $price);
      // vtwpr_apply_rules_to_vargroup_or_single($product_id, $price);        
    } 

 /*   
    //If the correct discount already computed, then nothing further needed...
    if ($vtwpr_info['product_session_info']['product_unit_price'] == $price) {
      return;
    }

    // *****************
    //if this is the 2nd thru nth call, $price value passed in may be different (if product has a product sale price), reapply percent in all cases...
    // *****************
    if ($price > 0) {
      vtwpr_recompute_discount_price($product_id, $price);
    }        
//echo ' price after refigure= ' . $vtwpr_info['product_session_info']['product_discount_price']. '<br>';
 */
    return;
  }
  
   
  //if discount price already in session variable, get in during the get_price() woo function
  function vtwpr_maybe_get_discount_catalog_session_price($product_id){   
    global $post, $vtwpr_info;
//echo ' cv001a product_id= ' . $product_id. ' price= ' .$price. '<br>' ; //mwnt    
    //store product-specific session info
    if(!isset($_SESSION)){
      session_start();
      header("Cache-Control: no-cache");
      header("Pragma: no-cache");
    }  
    
//    echo '$product_id= ' .$product_id. '<br>';
    //if already in the session variable... => this routine can be called multiple times in displaying a single catalog price.  check first if already done.
    if(isset($_SESSION['vtwpr_product_session_info_'.$product_id])) {
//      echo 'isset  yes<br>';
       $vtwpr_info['product_session_info'] = $_SESSION['vtwpr_product_session_info_'.$product_id];   
      //will be a problem in Ajax...
      $current_time_in_seconds = time();          
      $user_role = vtwpr_get_current_user_role();      
      if ( ( ($current_time_in_seconds - $vtwpr_info['product_session_info']['session_timestamp_in_seconds']) > '3600' ) ||     //session data older than 60 minutes
           (  $user_role != $vtwpr_info['product_session_info']['user_role']) ) {    //current user role not the same as prev
        vtwpr_apply_rules_to_single_product($product_id, $price);
      }        
    }

      return;

    return;
  }  


  /* ************************************************
  **   Apply Rules to single product + store as session info
  *************************************************** */
	function vtwpr_apply_rules_to_single_product($product_id, $price=null){    
 
    global $post, $vtwpr_cart, $vtwpr_cart_item, $vtwpr_info, $vtwpr_rules_set, $vtwpr_rule;

    vtwpr_set_selected_timezone();
    vtwpr_load_vtwpr_cart_for_single_product_price($product_id, $price);

    $vtwpr_info['current_processing_request'] = 'display';
    $vtwpr_apply_rules = new VTWPR_Apply_Rules; 

    //also moves the data to $vtwpr_info
    vtwpr_move_vtwpr_single_product_to_session($product_id);
    //return formatted price; if discounted, store price, orig price and you_save in session id
    //  if no discount, formatted DB price returned, no session variable stored
      
    //price result stored in $vtwpr_info['product_session_info'] 
    return; 
      
  }
  
  //*********************************
  //NEW GET PRICE FUNCTION
  //FROM 'woocommerce_get_price' => Central behind the scenes pricing  
  //*********************************
  function vtwpr_maybe_get_price_single_product($product_id, $price=null){   
    global $post, $vtwpr_info;
//echo ' cv001a product_id= ' . $product_id. ' price= ' .$price. '<br>' ; //mwnt    
    //store product-specific session info
    if(!isset($_SESSION)){
      session_start();
      header("Cache-Control: no-cache");
      header("Pragma: no-cache");
    }  
/*      
      echo 'vtwpr_info <pre>'.print_r($vtwpr_info, true).'</pre>' ;
      session_start();    //mwntest
      echo 'SESSION data <pre>'.print_r($_SESSION, true).'</pre>' ;      
      echo '<pre>'.print_r($vtwpr_rules_set, true).'</pre>' ; 
      echo '<pre>'.print_r($vtwpr_cart, true).'</pre>' ;
      echo '<pre>'.print_r($vtwpr_setup_options, true).'</pre>' ;
      echo '<pre>'.print_r($vtwpr_info, true).'</pre>' ;  
 wp_die( __('<strong>Looks like you\'re running an older version of WordPress, you need to be running at least WordPress 3.3 to use the Varktech Minimum Purchase plugin.</strong>', 'vtmin'), __('VT Minimum Purchase not compatible - WP', 'vtmin'), array('back_link' => true));
*/
    //if already in the session variable... => this routine can be called multiple times in displaying a single catalog price.  check first if already done.
     
//      echo 'IN THE ROUTINE, $product_id= ' .$product_id.'<br>' ;
//            echo 'SESSION data <pre>'.print_r($_SESSION, true).'</pre>' ; 
      
    if(isset($_SESSION['vtwpr_product_session_info_'.$product_id])) {
       $vtwpr_info['product_session_info'] = $_SESSION['vtwpr_product_session_info_'.$product_id];   
      //will be a problem in Ajax...
      $current_time_in_seconds = time();          
      $user_role = vtwpr_get_current_user_role();      
      if ( ( ($current_time_in_seconds - $vtwpr_info['product_session_info']['session_timestamp_in_seconds']) > '3600' ) ||     //session data older than 60 minutes
           (  $user_role != $vtwpr_info['product_session_info']['user_role']) ) {    //current user role not the same as prev
        vtwpr_apply_rules_to_single_product($product_id, $price);
      }        
    } else { 
       //First time obtaining the info, also moves the data to $vtwpr_info       
      vtwpr_apply_rules_to_single_product($product_id, $price);
      // vtwpr_apply_rules_to_vargroup_or_single($product_id, $price);        
    } 

    //$vtwpr_info['product_session_info'] is loaded by this time...
    return;
  }
    
  
  
  
   
  
  /* ************************************************
  **   Post-purchase discount logging
  *************************************************** */	
	function vtwpr_save_discount_purchase_log($cart_parent_purchase_log_id) {   
      global $post, $wpdb, $woocommerce, $vtwpr_cart, $vtwpr_cart_item, $vtwpr_info, $vtwpr_rules_set;  

      if ($vtwpr_cart->yousave_cart_total_amt == 0) {
        return;
      }
      //Create PURCHASE LOG row - 1 per cart
      $purchaser_ip_address = $vtwpr_info['purchaser_ip_address']; 
      $next_id; //supply null value for use with autoincrement table key
 
      $ruleset_object = serialize($vtwpr_rules_set); 
      $cart_object    = serialize($vtwpr_cart);
      
      $wpdb->query("INSERT INTO `".VTWPR_PURCHASE_LOG."` (`id`,`cart_parent_purchase_log_id`,`purchaser_name`,`purchaser_ip_address`,`purchase_date`,`cart_total_discount_currency`,`ruleset_object`,`cart_object`) 
        VALUES ('{$next_id}','{$cart_parent_purchase_log_id}','{$vtwpr_cart->billto_name}','{$purchaser_ip_address}','{$date}','{$vtwpr_cart->yousave_cart_total_amt}','{$ruleset_object}','{$cart_object}' );");

      $purchase_log_row_id = $wpdb->get_var("SELECT LAST_INSERT_ID() AS `id` FROM `".VTWPR_PURCHASE_LOG."` LIMIT 1");

      foreach($vtwpr_cart->cart_items as $key => $cart_item) {  
        if ($cart_item->yousave_total_amt > 0 ) { 
          //Create PURCHASE LOG PRODUCT row - 1 per product
          $wpdb->query("INSERT INTO `".VTWPR_PURCHASE_LOG_PRODUCT."` (`id`,`purchase_log_row_id`,`product_id`,`product_title`,`cart_parent_purchase_log_id`,
                `product_orig_unit_price`,`product_total_discount_units`,`product_total_discount_currency`,`product_total_discount_percent`) 
            VALUES ('{$next_id}','{$purchase_log_row_id}','{$cart_item->product_id}','{$cart_item->product_name}','{$cart_parent_purchase_log_id}',
                '{$cart_item->db_unit_price}','{$cart_item->yousave_total_qty}','{$cart_item->yousave_total_amt}','{$cart_item->yousave_total_pct}' );");
      
          $purchase_log_product_row_id = $wpdb->get_var("SELECT LAST_INSERT_ID() AS `id` FROM `".VTWPR_PURCHASE_LOG_PRODUCT."` LIMIT 1"); 
          foreach($cart_item->yousave_by_rule_info as $key => $yousave_by_rule) {
            $ruleset_occurrence = $yousave_by_rule['ruleset_occurrence'] ;
            $rule_id = $vtwpr_rules_set[$ruleset_occurrence]->post_id;
            $discount_applies_to_qty = $yousave_by_rule['discount_applies_to_qty'];
            $yousave_amt = $yousave_by_rule['yousave_amt'];
            $yousave_pct = $yousave_by_rule['yousave_pct'];        
            //Create PURCHASE LOG PRODUCT RULE row  -  1 per product/rule combo
            $wpdb->query("INSERT INTO `".VTWPR_PURCHASE_LOG_PRODUCT_RULE."` (`id`,`purchase_log_product_row_id`,`product_id`,`rule_id`,`cart_parent_purchase_log_id`,
                  `product_rule_discount_units`,`product_rule_discount_dollars`,`product_rule_discount_percent`) 
              VALUES ('{$next_id}','{$purchase_log_product_row_id}','{$cart_item->product_id}','{$rule_id}','{$cart_parent_purchase_log_id}',
                  '{$discount_applies_to_qty}','{$yousave_amt}','{$yousave_pct}' );");              
          }    
        }
      }
      
           
  }
    
     
  /* ************************************************
  **   Recompute Discount for VARIATION Display rule AJAX  
  *************************************************** */
  function vtwpr_recompute_discount_price($variation_id, $price){
      global $vtwpr_info;  
      
      $yousave_amt = 0;
      $sizeof_pricing_array = sizeof($vtwpr_info['product_session_info']['pricing_by_rule_array']);
      for($y=0; $y < $sizeof_pricing_array; $y++) {
        
        $apply_this = 'yes';
        
        $pricing_rule_applies_to_variations_array = $vtwpr_info['product_session_info']['pricing_by_rule_array'][$y]['pricing_rule_applies_to_variations_array'];
        
        if (sizeof($pricing_rule_applies_to_variations_array) > 0) {
           if (in_array($variation_id, $pricing_rule_applies_to_variations_array )) {
             $apply_this = 'yes';
           } else {
             $apply_this = 'no';  //this rule is variation-specific, and the passed id is not!! in the group - skip
           }
        }
        
        if ($apply_this == 'yes') {
          if ($vtwpr_info['product_session_info']['pricing_by_rule_array'][$y]['pricing_rule_currency_discount'] > 0) {
            $yousave_amt +=  $vtwpr_info['product_session_info']['pricing_by_rule_array'][$y]['pricing_rule_currency_discount'];
          } else {
            $PercentValue =  $vtwpr_info['product_session_info']['pricing_by_rule_array'][$y]['pricing_rule_percent_discount'];
            $yousave_amt +=  vtwpr_compute_percent_discount($PercentValue, $price);
          }
        }
        
      }  //end for loop
      
      $vtwpr_info['product_session_info']['product_discount_price'] = $price - $yousave_amt;
      //                                  ************************
       
     return;
  }
  
   
  /* ************************************************
  **   Compute percent discount for VARIATION realtime
  *************************************************** */
  function vtwpr_compute_percent_discount($PercentValue, $price){
    //from apply-rules.php   function vtwpr_compute_each_discount
      $percent_off = $PercentValue / 100;          
      
      $discount_2decimals = bcmul($price , $percent_off , 2);
    
      //compute rounding
      $temp_discount = $price * $percent_off;
      $rounding = $temp_discount - $discount_2decimals;
      if ($rounding > 0.005) {
        $discount = $discount_2decimals + .01;
      }  else {
        $discount = $discount_2decimals;
      }
           
     return $discount;
  }

   
   //APPLY TEST Globally in wp-admin ...  supply woo with ersatz wholesale pricings discount type
   function vtwpr_woo_maybe_create_coupon_types() {
      global $wpdb, $vtwpr_info;    
      
      $deal_discount_title = $vtwpr_info['coupon_code_discount_deal_title'];

      $coupon_id 	= $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_title ='" . $deal_discount_title. "'  AND post_type = 'shop_coupon' AND post_status = 'publish'  LIMIT 1" );     	
      if (!$coupon_id) {
        //$coupon_code = 'UNIQUECODE'; // Code
        
        $amount = '0'; // Amount
        $discount_type = 'fixed_cart'; // Type: fixed_cart, percent, fixed_product, percent_product
        $coupon = array(
        'post_title' => $deal_discount_title, //$coupon_code,
        'post_content' => 'Wholesale Pricing Plugin Inserted Coupon, please do not delete',
        'post_excerpt' => 'Wholesale Pricing Plugin Inserted Coupon, please do not delete',        
        'post_status' => 'publish',
        'post_author' => 1,
        'post_type' => 'shop_coupon'
        );
        $new_coupon_id = wp_insert_post( $coupon );
        // Add meta
        update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
        update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
        update_post_meta( $new_coupon_id, 'individual_use', 'no' );
        update_post_meta( $new_coupon_id, 'product_ids', '' );
        update_post_meta( $new_coupon_id, 'exclude_product_ids', '' );
        update_post_meta( $new_coupon_id, 'usage_limit', '' );
        update_post_meta( $new_coupon_id, 'expiry_date', '' );
        update_post_meta( $new_coupon_id, 'apply_before_tax', 'yes' );
        update_post_meta( $new_coupon_id, 'free_shipping', 'no' );
      }
   
    /*  FUTURE code for free shipping with discount...
      $deal_free_shipping_title = __('Free Shipping Deal', 'vtwpr'); 

      $coupon_id 	= $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_title ='" .$deal_free_shipping_title. "'  AND post_type = 'shop_coupon' AND post_status = 'publish'  LIMIT 1" );
      if (!$coupon_id) {
        //$coupon_code = 'UNIQUECODE'; // Code
        
        $amount = '0'; // Amount
        $discount_type = 'fixed_cart'; // Type: fixed_cart, percent, fixed_product, percent_product
        $coupon = array(
        'post_title' => $deal_free_shipping_title, //$coupon_code,
        'post_content' => 'Wholesale Pricing Plugin Inserted Coupon, please do not delete',
        'post_excerpt' => 'Wholesale Pricing Plugin Inserted Coupon, please do not delete',
        'post_status' => 'publish',
        'post_author' => 1,
        'post_type' => 'shop_coupon'
        );
        $new_coupon_id = wp_insert_post( $coupon );
        // Add meta
        update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
        update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
        update_post_meta( $new_coupon_id, 'individual_use', 'no' );
        update_post_meta( $new_coupon_id, 'product_ids', '' );
        update_post_meta( $new_coupon_id, 'exclude_product_ids', '' );
        update_post_meta( $new_coupon_id, 'usage_limit', '' );
        update_post_meta( $new_coupon_id, 'expiry_date', '' );
        update_post_meta( $new_coupon_id, 'apply_before_tax', 'yes' );
        update_post_meta( $new_coupon_id, 'free_shipping', 'yes' ); //YES!!!
      }
      */      
      
     return;
   } 

  
  function vtwpr_woo_ensure_coupons_are_allowed() {     

    if ( ($_REQUEST['page'] == 'woocommerce_settings' ) ) {
      $coupons_enabled = get_option( 'woocommerce_enable_coupons' );
      if ($coupons_enabled == 'no') {
          $message  =  '<strong>' . __('Message from Wholesale Pricing Plugin => WooCommerce setting "Enable the use of coupons" checkbox must be checked, as Wholesale Pricing cart discounts are applied using the coupon system.' , 'vtwpr') . '</strong>' ;
          $message .=  '<br><br>';
          $message .=  '<strong>' . __('"Enable the use of coupons" reset to Checked.' , 'vtwpr') . '</strong>' ;
          $admin_notices = '<div id="message" class="error fade" style="background-color: #FFEBE8 !important;"><p>' . $message . ' </p></div>';
          add_action( 'admin_notices', create_function( '', "echo '$admin_notices';" ) );
          update_option( 'woocommerce_enable_coupons','yes');    
      }
    }
  }

  
  function vtwpr_checkDateTime($date) {
    if (date('Y-m-d', strtotime($date)) == $date) {
        return true;
    } else {
        return false;
    }
  }
      
  /* ************************************************
  **   Amount comes back Formatted!
  *************************************************** */  
  function vtwpr_get_Woo_cartSubtotal() {

      global $woocommerce;
      $amt = $woocommerce->cart->get_cart_subtotal();
      
      return $amt;
  }


  //***** v1.0.3 begin
  /* ************************************************
  **  if BCMATH not installed with PHP by host, this will replace it.
  *************************************************** */
  if (!function_exists('bcmul')) {
    function bcmul($_ro, $_lo, $_scale=0) {
      return round($_ro*$_lo, $_scale);
    }
  }
  if (!function_exists('bcdiv')) {
    function bcdiv($_ro, $_lo, $_scale=0) {
      return round($_ro/$_lo, $_scale);
    }
  }
  
  function vtwpr_debug_options(){ 
    global $vtwpr_setup_options;
    if ( ( isset( $vtwpr_setup_options['debugging_mode_on'] )) &&
         ( $vtwpr_setup_options['debugging_mode_on'] == 'yes' ) ) {  
      error_reporting(E_ALL);  
    }  else {
      error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED ^ E_STRICT ^ E_USER_DEPRECATED ^ E_USER_NOTICE ^ E_USER_WARNING ^ E_RECOVERABLE_ERROR );    //only allow FATAL error types 
    } 
  }
  //***** v1.0.3 end



  /* ************************************************
  **   Change the default title in the rule custom post type
  *************************************************** */
/*
  function vtwpr_change_default_title( $title ){
     $screen = get_current_screen();
     if  ( 'vtwpr-rule' == $screen->post_type ) {
          $title = 'Enter Rule Title';
     }
     return $title;
  }
  add_filter( 'enter_title_here', 'vtwpr_change_default_title' ); 
*/
  /* ************************************************
  **  Disable draggable metabox in the rule custom post type
  *************************************************** */
/*
  function vtwpr_disable_drag_metabox() {
     $screen = get_current_screen();
     if  ( 'vtwpr-rule' == $screen->post_type ) { 
       wp_deregister_script('postbox');
     }
  }
  add_action( 'admin_init', 'vtwpr_disable_drag_metabox' ); 
 */
  
  /* ************************************************
  **  Display DB queries, time spent and memory consumption  IF  debugging_mode_on
  *************************************************** */
/*
  function vtwpr_performance( $visible = false ) {
    if ( $vtwpr_setup_options['debugging_mode_on'] == 'yes' ){ 
      $stat = sprintf(  '%d queries in %.3f seconds, using %.2fMB memory',
          get_num_queries(),
          timer_stop( 0, 3 ),
          memory_get_peak_usage() / 1024 / 1024
          );
      echo  $visible ? $stat : "<!-- {$stat} -->" ;
    }
}
 add_action( 'wp_footer', 'vtwpr_performance', 20 );
*/ 
 
  /* ************************************************
  **  W# Total Cache Special Flush for Custom Post type
  *************************************************** */ 
/*
function vtwpr_w3_flush_page_custom( $post_id ) {

   if ( !is_plugin_active('W3 Total Cache') ) {
      return;
   }
   
   if ( 'vtwpr-rule' != get_post_type( $post_id ) ) {
      return;
   }
          
    $w3_plugin_totalcache->flush_pgcache();

}  
  add_action( 'edit_post',    'vtwpr_w3_flush_page_custom', 10, 1 );
  add_action( 'save_post',    'vtwpr_w3_flush_page_custom', 10, 1 );
  add_action( 'delete_post',  'vtwpr_w3_flush_page_custom', 10, 1 );
  add_action( 'trash_post',   'vtwpr_w3_flush_page_custom', 10, 1 );
  add_action( 'untrash_post', 'vtwpr_w3_flush_page_custom', 10, 1 );
 */