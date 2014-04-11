<?php

/*
(-) "every Nth" 
	DEAL TYPE
	BUY GROUP AMOUNT
    deprecated - commented out in framework only
*/

class VTWPR_Rules_UI_Framework {
	
	public function __construct(){
    
    global  $vtwpr_rule_display_framework, 
            $vtwpr_rule_template_framework,    //$vtwpr_ruleTemplate_framework,
            $vtwpr_deal_edits_framework,
            $vtwpr_deal_structure_framework,
            $vtwpr_deal_screen_framework,
            $vtwpr_template_structures_framework;
            $vtwpr_rule_test_info_framework;    
  /*
    //                                *************                               *************
    $vtwpr_discount_display_framework GOES INTO => $vtwpr_discount_info_framework GOES INTO => $vtwpr_deal_structure_framework
    //                                *************                               *************
  */
    
    
    $vtwpr_rule_display_framework = array (
       //TOP LEVEL DROPDOWNS
          'cart_or_catalog_select' =>  array ( 
              //dropdown select info
              'label'    => array(
                    'for'    => 'cart-or-catalog-select',
                    'title'  => __('Cart or Catalog ', 'vtwpr') .'<br>'.  __('Discount ', 'vtwpr')
                    
                    //'title'  => __('Cart Purchase / Catalog Display', 'vtwpr')    .'&nbsp;&nbsp;'.  __('Discount Type', 'vtwpr')       
                ),
                'select'    => array(
                    'id'    => 'cart-or-catalog-select',
                    'class' => 'select-group clear-left',
                    'name'  => 'cart-or-catalog-select',
                    'tabindex'  => ''
                ),
              //dropdown options
              'option'  => array(
                  array (
                    'id'       => 'cart-or-catalog-Choose',
                    'class'    => '',
                    'value'    => 'choose',
                    'title'    => __(' - Cart or Catalog Discount?', 'vtwpr') //Cart or Catalog Discount?   
                   ),                   
                  array (
                    'id'       => 'cart-or-catalog-Cart',
                    'class'    => '',
                    'value'    => 'cart',
                    'title'    => __('CART Purchase Discount', 'vtwpr')   //Cart Discount (when Added to Cart) 
                   ),                                     
                  array (
                    'id'       => 'cart-or-catalog-Catalog',
                    'class'    => '',
                    'value'    => 'catalog',
                    'title'    => __('CATALOG Price Reduction', 'vtwpr')  //CATALOG Price Display Discount 
                   ) 
                 )
            ),
          'pricing_type_select' =>  array ( 
              //dropdown select info
              'label'    => array(
                    'for'    => 'pricing-type-select',
                    'title'  => __('Deal Type', 'vtwpr')
                ),
                'select'    => array(
                    'id'    => 'pricing-type-select',
                    'class' => 'select-group clear-left',
                    'name'  => 'pricing-type-select',
                    'tabindex'  => ''
                ),
              //dropdown options
              'option'  => array(
                  array (
                    'id'       => 'pricing-type-Choose',
                    'class'    => '',
                    'value'    => 'choose',
                    'title'    => __('- Deal Type ? - ', 'vtwpr')        
                   ),
                   
                  array (
                    'id'       => 'pricing-type-Simple',
                    'class'    => '',
                    'value'    => 'simple',
                    //Part of the store on sale
                    'title'    => __('Discount by Category, Logged-in Role, Product ... ', 'vtwpr') /*,
                    'title-catalog'
                               => __('Discount by Category, Logged-in Role, Product ... ', 'vtwpr')*/    
                   ),                                     

                  array (
                    'id'       => 'pricing-type-All',
                    'class'    => '',
                    'value'    => 'all',
                    //all of the store on sale
                    'title'    => __('Discount Whole Store', 'vtwpr'), //Discount the whole catalog    //vs Discount anything in the cart 
                    'title-catalog'   
                               => __('Discount Whole Store', 'vtwpr')
                   )                   
                 )
            ), 
          'minimum_purchase_select' =>  array ( 
              //dropdown select info
              'label'    => array(
                    'for'    => 'minimum-purchase-select',
                    'title'  => __('Deal Action', 'vtwpr') 
                    //'title'  => __('Discount', 'vtwpr') . '&nbsp;&nbsp' . __('Next Item / This Item', 'vtwpr')  //. '&nbsp;&nbsp;&nbsp;' .  __('Purchased', 'vtwpr')
                ),
                'select'    => array(
                    'id'    => 'minimum-purchase-select',
                    'class' => 'select-group clear-left',
                    'name'  => 'minimum-purchase-select',
                    'tabindex'  => ''
                ),
              //dropdown options
              'option'  => array(                   
                  /*array (
                    'id'       => 'minimum-purchase-Choose',
                    'class'    => '',
                    'value'    => 'choose',
                    'title'    => __(' - Select Minimum Purchase -', 'vtwpr')    
                   ),*/
                  array (
                    'id'       => 'minimum-purchase-Choose',
                    'class'    => '',
                    'value'    => 'choose',
                    'title'    => __(' - Discount Next Item / This Item ?  - ', 'vtwpr')    
                   ),    

                  array (
                    'id'       => 'minimum-purchase-Next',
                    'class'    => '',
                    'value'    => 'next',
                    'title'    => __('Buy something, discount the *next* item', 'vtwpr')
                    //'title'    => __('Buy these, discount *next* purchases', 'vtwpr')
                   ),
                  array (
                    'id'       => 'minimum-purchase-None',
                    'class'    => '',
                    'value'    => 'none',
                    'title'    => __('Buy something, discount the item', 'vtwpr'),
                    //'title'    => __('Buy these, discount *these* purchases', 'vtwpr'),
                    'title-catalog'   
                               => __('Apply Discount to Catalog Item', 'vtwpr')      //Buy these, these purchases discounted
                   )
                   
                   /*
                   ,                                                                                           
                  array (
                    'id'       => 'minimum-purchase-Minimum',
                    'class'    => '',
                    'value'    => 'next',
                    'title'    => __('Minimum Purchase required, then Apply cart discount ', 'vtwpr')   
                   )
                   */ 
                 )
            ),        
          'buy_group_filter_select' =>  array ( 
              //dropdown select info
              'label'    => array(
                    'id'     => 'buy-group-filter-select-label',
                    'for'    => 'buy-group-filter-select', 
                    'title'  => '<span class="select-label-text">'. 
                                __('Buy Filter', 'vtwpr') .     // Product Filter Filter
                                '</span>' .
                                '<span class="select-subTitle select-subTitle-all hideMe label-no-cap">"'. 
                                    __('Discount Group', 'vtwpr') . 
                                '"</span>' .
                                '<span class="select-subTitle select-subTitle-simple hideMe label-no-cap">"'. 
                                    __('Discount Group', 'vtwpr') . 
                                '"</span>' .
                                '<span class="select-subTitle select-subTitle-bogo hideMe label-no-cap">"'. 
                                    __('Buy a Laptop', 'vtwpr') . 
                                '"</span>' .
                                '<span class="select-subTitle select-subTitle-group hideMe label-no-cap">"'. 
                                    __('Buy 5 apples', 'vtwpr') . 
                                '"</span>' .
                                '<span class="select-subTitle select-subTitle-cheapest hideMe label-no-cap">"'. 
                                    __('Buy 5 items', 'vtwpr') . 
                                '"</span>' .
                                '<span class="select-subTitle select-subTitle-nth hideMe label-no-cap">"'. 
                                    __('Buy 5 items', 'vtwpr') . 
                                '"</span>' 
                ),
                'select'    => array(
                    'id'    => 'buy-group-filter-select',
                    'class' => 'select-group clear-left',
                    'name'  => 'buy-group-filter-select',
                    'tabindex'  => ''
                ),
              //dropdown options
              'option'  => array(                   
                  array (
                    'id'       => 'buy-group-filter-Choose',
                    'class'    => '',
                    'value'    => 'choose',
                    'title'    => __(' - Choose a  Product Filter Filter -', 'vtwpr'),
                    'title2'   => __(' - Choose a Discount Group Filter -', 'vtwpr'),
                    'title-catalog'   => __(' - Choose a Catalog Discount Group Filter -', 'vtwpr')  
                   ),                                       
                  array (
                    'id'       => 'buy-group-filter-All',
                    'class'    => '',
                    'value'    => 'wholeStore',
                    'title'    => __('Buy Any Product', 'vtwpr'),
                    'title2'   => __('Discount Each Item', 'vtwpr'),
                    'title-catalog'   => __('Any Catalog Product', 'vtwpr')    
                   ),                                                      
                  array (
                    'id'       => 'buy-group-filter-Group',
                    'class'    => '',
                    'value'    => 'groups',
                    'title'    => __('By Category / Logged-in Role ... (pro only) ...  ', 'vtwpr') . '&nbsp;&nbsp;',  //free version
                    'title2'   => __('By Category / Logged-in Role ... (pro only) ...  ', 'vtwpr') . '&nbsp;&nbsp;',   //free version
                    'title3'   => __('By Category / Logged-in Role / Plugin Category', 'vtwpr') . '&nbsp;&nbsp;',    //pro version
                    'title4'   => __('By Category / Logged-in Role / Plugin Category', 'vtwpr') . '&nbsp;&nbsp;'      //pro version
                   ),
                  array (
                    'id'       => 'buy-group-filter-Vargroup',
                    'class'    => '',
                    'value'    => 'vargroup',
                    'title'    => __('By Single Product with Variations  ... (pro only) ... ', 'vtwpr'),      //free version
                    'title2'   => __('By Single Product with Variations  ... (pro only) ... ', 'vtwpr'),   //free version
                    'title3'   => __('By Single Product with Variations', 'vtwpr'),
                    'title4'   => __('By Single Product with Variations', 'vtwpr')   
                   ),                   
                  array (
                    'id'       => 'buy-group-filter-Single',
                    'class'    => '',
                    'value'    => 'single',
                    'title'    => __('By Single Product ... (pro only) ... ', 'vtwpr'),          //free version
                    'title2'   => __('By Single Product ... (pro only) ... ', 'vtwpr'),      //free version
                    'title3'   => __('By Single Product', 'vtwpr'), 
                    'title4'   => __('By Single Product', 'vtwpr') 
                   )                    
                 )
            ),
          'get_group_filter_select' =>  array ( 
              //dropdown select info
              'label'    => array(
                    'id'     => 'get-group-filter-select-label',
                    'for'    => 'get-group-filter-select',
                    'title'  => '<span class="select-label-text">'. 
                                __('Get Filter', 'vtwpr') .  //Get Group Filter
                                '</span>' .                    
                                '<span class="select-subTitle select-subTitle-all hideMe label-no-cap">"'. 
                                    __('Inactive', 'vtwpr') . 
                                '"</span>' .
                                '<span class="select-subTitle select-subTitle-simple hideMe label-no-cap">"'. 
                                    __('Inactive', 'vtwpr') . 
                                '"</span>' .
                                '<span class="select-subTitle select-subTitle-bogo hideMe label-no-cap">"'. 
                                    __('Get a Mouse (Free)', 'vtwpr') . 
                                '"</span>' .
                                '<span class="select-subTitle select-subTitle-group hideMe label-no-cap">"'. 
                                    __('Get them for a Group Price', 'vtwpr') . 
                                '"</span>' .
                                '<span class="select-subTitle select-subTitle-cheapest hideMe label-no-cap">"'. 
                                    __('Get the Cheapest at a Discount', 'vtwpr') . 
                                '"</span>' .
                                '<span class="select-subTitle select-subTitle-nth hideMe label-no-cap">"'. 
                                    __('Get the 6th one at a Discount', 'vtwpr') . 
                                '"</span>'                                                                
                ),
                'select'    => array(
                    'id'    => 'get-group-filter-select',
                    'class' => 'select-group clear-left',
                    'name'  => 'get-group-filter-select',
                    'tabindex'  => ''
                ),
              //dropdown options
              'option'  => array(                   
                  array (
                    'id'       => 'get-group-filter-Choose',
                    'class'    => '',
                    'value'    => 'choose',
                    'title'    => __(' - Choose a Get Group Filter - ', 'vtwpr'),  //Choose a Get Group Filter
                    'title2'   => __(' - Choose a Discount Group Filter - ', 'vtwpr')
                   ),                                      

                  array (
                    'id'       => 'get-group-filter-SameAsBuy',
                    'class'    => '',
                    'value'    => 'sameAsInPop',
                    'title'    => __('Discount Group Same as  Product Filter', 'vtwpr') . '&nbsp;&nbsp;'    
                   ),                                                                           
                  array (
                    'id'       => 'get-group-filter-All',
                    'class'    => '',
                    'value'    => 'wholeStore',
                    'title'    => __('Discount Any Product', 'vtwpr'),  //free version                     
                   ),
                  array (
                    'id'       => 'get-group-filter-Group',
                    'class'    => '',
                    'value'    => 'groups',
                    'title'    => __('By Category / Plugin Category  ... (pro only) ...  ', 'vtwpr'),  //free version 
                    'title3'   => __('By Category / Plugin Category', 'vtwpr')    //pro version 
                   ),                   
                  array (
                    'id'       => 'get-group-filter-Vargroup',
                    'class'    => '',
                    'value'    => 'vargroup',
                    'title'    => __('By Single Product with Variations  ... (pro only) ...  ', 'vtwpr'),
                    'title3'   => __('By Single Product with Variations ', 'vtwpr')    //pro version   
                   ),                   
                  array (
                    'id'       => 'get-group-filter-Single',
                    'class'    => '',
                    'value'    => 'single',
                    'title'    => __('By Single Product  ... (pro only) ...  ', 'vtwpr'),
                    'title3'   => __('By Single Product ', 'vtwpr')    //pro version   
                   )                    
                 )
            ), 
           'rule-type-select' =>  array ( 
              array(  
                  'id'    => 'basicSelected',
                  'class'  => 'basic-advancedClass',
                  'type'   => 'radio',
                  'name'    => 'rule-type-select',  
                  'value'  => 'basic', 
                  'label'  =>  __('Basic Rule', 'vtwpr')
              ) , 
               array(  
                  'id'    => 'advancedSelected',
                  'class'  => 'basic-advancedClass',
                  'type'   => 'radio',
                  'name'    => 'rule-type-select',  
                  'value'  => 'advanced', //checked, selected, contents, etc 
                  'label'  =>  __('Advanced Rule', 'vtwpr')
              )  
            ),
                      
          'rule_on_off_sw_select' =>  array ( 
              //dropdown select info
                'select'    => array(
                    'id'    => 'rule-on-off-sw-select',
                    'class' => 'select-group clear-left',
                    'name'  => 'rule-on-off-sw-select',
                    'tabindex'  => ''
                ),
              //dropdown options
              'option'  => array(
                  array (
                    'id'       => 'rule-on-off-sw-on',
                    'class'    => '',
                    'value'    => 'on',
                    'title'    => __('Rule is ON', 'vtwpr')    
                   ),                   
                  array (
                    'id'       => 'rule-on-off-sw-on-forever',
                    'class'    => '',
                    'value'    => 'onForever',
                    'title'    => __('Rule is ON Always', 'vtwpr')    
                   ),                                     
                  array (
                    'id'       => 'rule-on-off-sw-off',
                    'class'    => '',
                    'value'    => 'off',
                    'title'    => __('Rule is OFF', 'vtwpr')   
                   ) 
                 )
            ),
                      
          'wizard_on_off_sw_select' =>  array ( 
              //dropdown select info
                'select'    => array(
                    'id'    => 'wizard-on-off-sw-select',
                    'class' => 'select-group clear-left',
                    'name'  => 'wizard-on-off-sw-select',
                    'tabindex'  => ''
                ),
              //dropdown options
              'option'  => array(
                  array (
                    'id'       => 'wizard-on-off-sw-on',
                    'class'    => '',
                    'value'    => 'on',
                    'title'    => __('Wizard is On', 'vtwpr')    
                   ),                                                        
                  array (
                    'id'       => 'wizard-on-off-sw-off',
                    'class'    => '',
                    'value'    => 'off',
                    'title'    => __('Wizard is Off', 'vtwpr')   
                   ) 
                 )
            ),
                            
      //REST of the  discount_rule_max stuff in the deal_info template...  
      'discount_rule_max_amt_msg' => array (   
          'id'    => 'discount_rule_max_amt_msg',
          'class'  => 'msg-text',
          'type'  => 'text',
          'name'  => 'discount_rule_max_amt_msg'
        ),                 
         
      //REST of the  discount_lifetime_max stuff in the deal_info template...  
      'discount_lifetime_max_amt_msg' => array (   
          'id'    => 'discount_lifetime_max_amt_msg',
          'class'  => 'msg-text',
          'type'  => 'text',
          'name'  => 'discount_lifetime_max_amt_msg'
        ),            
         
      //REST of the  discount_cum_max stuff in the deal_info template...  
      'discount_rule_cum_max_amt_msg' => array (   
          'id'    => 'discount_rule_cum_max_amt_msg',
          'class'  => 'msg-text',
          'type'  => 'text',
          'name'  => 'discount_rule_cum_max_amt_msg'
        ),       
                   
      'discount_product_short_msg' => array (   
          'id'    => 'discount_product_short_msg',
          'class'  => 'msg-text',
          'type'  => 'text',
          'name'  => 'discount_product_short_msg'
        ),
      'discount_product_full_msg' => array (   
          'id'    => 'discount_product_full_msg',
          'class'  => 'msg-text',
          'type'  => 'text',
          'name'  => 'discount_product_full_msg'
        ),  
       
    
        //Cumulative Pricing Switches   =>  Always Required
       //Apply this Rule Discount in Addition to Other Rule Discounts?
      'cumulativeRulePricing' => array(   // yes / no (def)  / in addtion to     
            //dropdown select info
            'select'    => array(                                                                            
                  'id'    => 'cumulativeRulePricing',
                  'class' => '',
                  'name'  => 'cumulativeRulePricing'
              ),
            //dropdown options
            'option'  => array( 
                 array (
                  'id'       => 'cumulativeRulePricingNo',
                  'class'    => 'cumulativeRulePricingOptions',
                  'value'    => 'no',
                  'title'    => __('No ', 'vtwpr')
                 ),                
                array (
                  'id'       => 'cumulativeRulePricingYes',
                  'class'    => 'cumulativeRulePricingOptions',
                  'value'    => 'yes',
                  'title'    => __('Yes ', 'vtwpr')
                 )
              )
           ),
      //Rule Discount and Product Sale Pricing:
      'cumulativeSalePricing' => array(   // yes / no (def)  / in addtion to     
            //dropdown select info
            'select'    => array(                                                                            
                  'id'    => 'cumulativeSalePricing',
                  'class' => '',
                  'name'  => 'cumulativeSalePricing'
              ),
            //dropdown options
            'option'  => array(
                 array (
                  'id'       => 'cumulativeSalePricingNo',
                  'class'    => 'cumulativeSalePricingOptions',
                  'value'    => 'no',
                  'title'    => __(' No - if product already on Sale, no further discount  ', 'vtwpr')
                 ),              
                 array (
                  'id'       => 'cumulativeSalePricingAddTo',
                  'class'    => 'cumulativeSalePricingOptions',
                  'value'    => 'addToSalePrice',
                  'title'    => __('Apply Discount to Product Sale Price  ', 'vtwpr')
                 ),
                 array (
                  'id'       => 'cumulativeSalePricingReplace',
                  'class'    => 'cumulativeSalePricingOptions',
                  'value'    => 'replaceSalePrice',
                  'title'    => __('Apply Discount to Regular Price, if Less than Sale Price  ', 'vtwpr')  . '&nbsp;&nbsp;'
                 )                 
              )
           ),
           
      //Apply Rule Discount in addition to Coupon Discount?
      'cumulativeCouponPricing' => array(   // yes / no (def)  / in addtion to     
            //dropdown select info
            'select'    => array(                                                                            
                  'id'    => 'cumulativeCouponPricing',
                  'class' => '',
                  'name'  => 'cumulativeCouponPricing'
              ),
            //dropdown options
            'option'  => array(
                 array (
                  'id'       => 'cumulativeCouponPricingNo',
                  'class'    => 'cumulativeCouponPricingOptions',
                  'value'    => 'no',
                  'title'    => __('No ', 'vtwpr')
                 ),
                array (
                  'id'       => 'cumulativeCouponPricingYes',
                  'class'    => 'cumulativeCouponPricingOptions',
                  'value'    => 'yes',
                  'title'    => __('Yes ', 'vtwpr')
                 )               
              )
           ),
      
      // always optional, defaults to 5!
      'ruleApplicationPriority_num' => array (       //highest number first (1-n) : decides which rule is applied first. default' => 5.
              'id'    => 'ruleApplicationPriority_num',
              'class'  => 'amt',
              'type'  => 'text',
              'name'  => 'ruleApplicationPriority_num',
              'title'    => __('Rule Application Priority, used if multiple rules process together (lower number processed first) ', 'vtwpr')                    
          ),
       
             
       //*****************
       //  inPop
      //*****************
      'inPop' =>  array (
          //dropdown select info
          'select'    => array(                                                                            
                'id'    => 'popChoiceIn',
                'class' => 'popChoice',
                'name'  => 'popChoiceIn'
            ),
          //dropdown options
          'option'  => array(
              array (
                'id'       => 'popChoiceInTitle',
                'class'    => 'popChoiceInOptions',
                'value'    => '0',
                'title'    => __('- Group Filter Type -  ', 'vtwpr')
               ),  
              array (
                'id'       => 'cartChoiceIn',
                'class'    => 'popChoiceInOptions',
                'value'    => 'wholeStore',
                'title'    => __('Any Product  ', 'vtwpr'),
                'title2'   => __('Any Catalog Product  ', 'vtwpr')    
               ),               
               array (
                'id'       => 'groupChoiceIn',
                'class'    => 'popChoiceInOptions',
                'value'    => 'groups',
                'title'    => __('By Category / Logged-in Role / Plugin Category ... (pro only) ...', 'vtwpr') . '&nbsp;&nbsp;',   
                'title2'   => __('By Category / Logged-in Role / Plugin Category ... (pro only) ...', 'vtwpr') . '&nbsp;&nbsp;',   //free version
                'title3'   => __('By Category / Logged-in Role / Plugin Category', 'vtwpr') . '&nbsp;&nbsp;',    //pro version
                'title4'   => __('By Category / Logged-in Role / Plugin Category', 'vtwpr') . '&nbsp;&nbsp;'      //pro version   
               ),
               array (
                'id'       => 'varChoiceIn',
                'class'    => 'popChoiceInOptions',
                'value'    => 'vargroup',
                'title'    => __('By Single Product with Variations ... (pro only) ...', 'vtwpr'),
                'title2'   => __('By Single Product with Variations ... (pro only) ...', 'vtwpr'),   //free version
                'title3'   => __('By Single Product with Variations  ', 'vtwpr'),    //pro version
                'title4'   => __('By Single Product with Variations ', 'vtwpr')      //pro version  
               ),
               array (
                'id'       => 'singleChoiceIn',
                'class'    => 'popChoiceInOptions',
                'value'    => 'single',
                'title'    => __('By Single Product ... (pro only) ...', 'vtwpr'),
                'title2'   => __('By Single Product ... (pro only) ...', 'vtwpr'),   //free version
                'title3'   => __('By Single Product ', 'vtwpr'),    //pro version
                'title4'   => __('By Single Product ', 'vtwpr')      //pro version      
               )             
            )          
        ),
              
       'inPop_varProdID' => array (     
          'id'    => 'inVarProdID',
          'class'  => 'text',
          'type'  => 'text',
          'name'  => 'inVarProdID'         
        ),
        
       'inPop_singleProdID' => array (     
          'id'    => 'singleProdID_in',
          'class'  => 'amt',
          'type'  => 'text',
          'name'  => 'singleProdID_in'      
        ),

         
       'role_and_or_in' =>  array ( //role and/or as combined with cats
          array(  
              'id'    => 'andChoiceIn',
              'class'  => '',
              'type'   => 'radio',
              'name'    => 'andorChoiceIn',  
              'value'  => 'and', //checked, selected, contents, etc 
              'label'  =>  __(' And', 'vtwpr')
          ) , 
           array(  
              'id'    => 'orChoiceIn',
              'class'  => '',
              'type'   => 'radio',
              'name'    => 'andorChoiceIn',  
              'value'  => 'or', //checked, selected, contents, etc 
              'label'  =>  __(' Or', 'vtwpr')
          )  
        ),
      
  
    //   'inPop_group_is_based_on,
        //don't need a 'role_in_checked_name' array, as there can only ever be 1 per user, and the user role name will not be used in the error message. 
       'specChoice_in' =>  array (   
          //dropdown select info
          'select'    => array(                                                                            
                'id'    => 'specChoice_in',
                'class' => 'anyChosen',
                'name'  => 'specChoice_in'
            ),
          //dropdown options
          'option'  => array(
              array (
                'id'       => 'specChoice_inTitle',
                'class'    => 'specChoice_inOptions',
                'value'    => '0',
                'title'    => __(' - Applies to the Buy Population... - ', 'vtwpr'),
                'title2'   => __(' - Applies to the Discount Population... - ', 'vtwpr')
               ),
              array (
                'id'       => 'allChoiceIn',
                'class'    => 'specChoice_inOptions',
                'value'    => 'all',
                'title'    => __('Applies to all Buy Products as a group ', 'vtwpr'),
                'title2'   => __('Applies to all Discount Products as a group ', 'vtwpr')
               ),               
               array (
                'id'       => 'eachChoiceIn',
                'class'    => 'specChoice_inOptions',
                'value'    => 'each',
                'title'    => __('Applies to each individual Buy Product ', 'vtwpr'),
                'title2'   => __('Applies to each individual Discount Product ', 'vtwpr')
               ), 
               array (
                'id'       => 'anyChoiceIn',
                'class'    => 'specChoice_inOptions',
                'value'    => 'any',
                'title'    => __('Applies to each individual Buy Product, limited by a count ', 'vtwpr'),
                'title2'   => __('Applies to each individual Discount Product, limited by a count ', 'vtwpr')
               )                   
            )               
        ),
       
      'anyChoiceIn_max' => array ( 
              'id'    => 'anyChoiceIn-max',
              'class'  => 'text',
              'type'  => 'text',
              'name'  => 'anyChoiceIn-max'       
            ),
      'amtSelectedIn' => array  (
          array(  
              'id'    =>  'qtySelectedIn',
              'class'  => 'qtySelectedInClass',
              'type'   => 'radio',
              'name'    => 'amtSelectedIn',  
              'value'  => 'quantity', 
              'label'  =>  __('Apply to Quantity Total ', 'vtwpr')
          ) , 
          array( 
              'id'    => 'amtSelectedIn',
              'class'  => 'amtSelectedInClass',
              'type'   => 'radio',
              'name'    => 'amtSelectedIn',  
              'value'  => 'currency', 
              'label'  =>  __('Apply to Price ', 'vtwpr')
          ) 
        ),
      'inPop_threshHold_amt' => array ( 
              'id'    => 'amtChoiceIn-count',
              'class'  => 'text',
              'type'  => 'text',
              'name'  => 'amtChoiceIn-count'                      
            ),
            
       //END inPop     
            
              
       //*****************
       //  actionPop
      //*****************

      'actionPop' =>  array (
          //dropdown select info
          'select'    => array(                                                                            
                'id'    => 'popChoiceOut',
                'class' => 'popChoice',
                'name'  => 'popChoiceOut'
            ),
          //dropdown options
    
          'option'  => array(
              array (
                'id'       => 'popChoiceOutTitle',
                'class'    => 'popChoiceOutOptions',
                'value'    => '0',
                'title'    => __('- Group Filter Type -  ', 'vtwpr')
               ), 
              array (                    //SAME AS inPop
                'id'       => 'sameChoiceOut',
                'class'    => 'popChoiceOutOptions',
                'value'    => 'sameAsInPop',
                'title'    => __('Discount Group Same as  Product Filter ', 'vtwpr')  . '&nbsp;&nbsp;'   
               ),               
             
              array (
                'id'       => 'cartChoiceOut',
                'class'    => 'popChoiceOutOptions',
                'value'    => 'wholeStore',
                'title'    => __('Any Product ', 'vtwpr')
               ),
               array (
                'id'       => 'groupChoiceOut',
                'class'    => 'popChoiceOutOptions',
                'value'    => 'groups',
                'title'    => __('By Category / Plugin Category ... (pro only) ...', 'vtwpr'),   //free version
                'title3'   => __('By Category / Plugin Category', 'vtwpr')    //pro version   
               ),
               array (
                'id'       => 'varChoiceOut',
                'class'    => 'popChoiceOutOptions',
                'value'    => 'vargroup',
                'title'    => __('By Single Product with Variations ... (pro only) ...', 'vtwpr'),   //free version
                'title3'   => __('By Single Product with Variations ', 'vtwpr')    //pro version 
               ),
               array (
                'id'       => 'singleChoiceOut',
                'class'    => 'popChoiceOutOptions',
                'value'    => 'single',
                'title'    => __('By Single Product ... (pro only) ...', 'vtwpr'),   //free version
                'title3'   => __('By Single Product ', 'vtwpr')    //pro version    
               )             
            )          
        ),
              
       'actionPop_varProdID' => array (     
          'id'    => 'outVarProdID',
          'class'  => 'text',
          'type'  => 'text',
          'name'  => 'outVarProdID'         
        ),

        
       'actionPop_singleProdID' => array (     
          'id'    => 'singleProdID_out',
          'class'  => 'amt',
          'type'  => 'text',
          'name'  => 'singleProdID_out'     
        ),


       'role_and_or_out' =>  array ( //role and/or as combined with cats
          array(  
              'id'    => 'andChoiceOut',
              'class'  => '',
              'type'   => 'radio',
              'name'    => 'andorChoiceOut',  
              'value'  => 'and', //checked, selected, contents, etc 
              'label'  =>  __(' And', 'vtwpr')
          ) , 
           array(  
              'id'    => 'orChoiceOut',
              'class'  => '',
              'type'   => 'radio',
              'name'    => 'andorChoiceOut',  
              'value'  => 'or', //checked, selected, contents, etc 
              'label'  =>  __(' Or', 'vtwpr')
          )  
        ),
 
  
    //   'actionPop_group_is_based_on,
        //don't need a 'role_out_checked_name' array, as there can only ever be 1 per user, and the user role name will not be used in the error message. 
       'specChoice_out' =>  array (   
          //dropdown select info
          'select'    => array(                                                                            
                'id'    => 'specChoice_out ',
                'class' => 'anyChosen',
                'name'  => 'specChoice_out '
            ),
          //dropdown options
          'option'  => array(
              array (
                'id'       => 'allChoiceOut',
                'class'    => 'specChoice_outOptions',
                'value'    => 'all',
                'title'    => __('Applies to the Buy Population as a group ', 'vtwpr')
               ),
               array (
                'id'       => 'eachChoiceOut',
                'class'    => 'specChoice_outOptions',
                'value'    => 'each',
                'title'    => __('Applies to each member of the Buy Population ', 'vtwpr')
               ), 
               array (
                'id'       => 'anyChoiceOut',
                'class'    => 'specChoice_outOptions',
                'value'    => 'any',
                'title'    => __('Applies to some members of the Buy Population, limited by a count ', 'vtwpr')
               )                   
            )               
        ),
       
      'anyChoiceOut_max' => array ( 
              'id'    => 'anyChoiceOut-max',
              'class'  => 'text',
              'type'  => 'text',
              'name'  => 'anyChoiceOut-max'       
            ),
      'amtSelectedOut' => array  (
          array(  
              'id'    => 'qtySelectedOut',
              'class'  => 'qtySelectedOutClass',
              'type'   => 'radio',
              'name'    => 'amtSelectedOut',  
              'value'  => 'quantity', 
              'label'  =>  __(' Apply to Quantity Total', 'vtwpr')
          ) ,
          array( 
              'id'    => 'amtSelectedOut',
              'class'  => 'amtSelectedOutClass',
              'type'   => 'radio',
              'name'    => 'amtSelectedOut',  
              'value'  => 'currency', 
              'label'  =>  __(' Apply to Price', 'vtwpr') 
          ) 
        ),

      'actionPop_threshHold_amt' => array ( 
          'id'    => 'amtChoiceOut-count',
          'class'  => 'text',
          'type'  => 'text',
          'name'  => 'amtChoiceOut-count'    
        ),
    
       //******************************************
       //END actionPop 
       //******************************************
  
      //******************************************
      //Product include/exclude switch, on the PRODUCT screen
      //*      Meta box added to PRODUCT in rules-ui.php 
      //******************************************

        'includeOrExclude' => array (  // none / count / next (next group) / all (all qty/$$ remaining / none ) / or (if qty/$$ reached, this supersedes previous condition)
              //dropdown select info
              'select'    => array(
                    'id'    => 'includeOrExclude',
                    'class' => 'includeOrExclude',
                    'name'  => 'includeOrExclude',
                    'tabindex'  => ''
                ),
              //dropdown options

              'option'  => array(
                  array (
                    'id'       => 'includeOrExclude_includeAll',
                    'class'    => 'includeOrExclude_includeAll',
                    'value'    => 'includeAll',
                    'title'    => __('Allow product', 'vtwpr')  . '&nbsp;&nbsp;&nbsp;' . __('In Rules as normal', 'vtwpr')  
                   ),                                                       
                   array (
                    'id'       => 'includeOrExclude_excludeAll',
                    'class'    => 'includeOrExclude_excludeAll',
                    'value'    => 'excludeAll',
                    'title'    => __('Exclude product', 'vtwpr')  . '&nbsp;&nbsp;&nbsp;' . __('From All Rules Forever', 'vtwpr')  
                   ),
                  array (
                    'id'       => 'includeOrExclude_includeList',
                    'class'    => 'includeOrExclude_includeList',
                    'value'    => 'includeList',
                    'title'    => __('Allow product', 'vtwpr')  . '&nbsp;&nbsp;&nbsp;' . __('In the Checked Rules below', 'vtwpr') 
                   ),  
                  array (
                    'id'       => 'includeOrExclude_excludeList',
                    'class'    => 'includeOrExclude_excludeList',
                    'value'    => 'excludeList',
                    'title'    => __('Exclude product', 'vtwpr')  . '&nbsp;&nbsp;&nbsp;' . __('From the Checked Rules below', 'vtwpr') 
                   )
               )  
            )
 ); // end $vtwpr_rule_display_framework         
    
    
      
    /*
    This framework drives the contruction of the deal options offered,
    and their matching logic requirements during processing.
    */ 
        

   // value = C  => C/D Cart or Display  
 
    $vtwpr_rule_template_framework = array(        
      //dropdown select info
      'select'    => array(
            'id'    => 'rule_template_framework',
            'class' => 'rule_template_framework',
            'name'  => 'rule_template_framework',
            'tabindex'  => '',
            'optionsMax'  => '14'
        ),
        //dropdown options
        'option'  => array(                      
          array (
            'id'       => 'optgroup-template-choice',
            'class'    => 'optgroup-class',
            'value'    => '',
            'title'    => ' ' //  __('Wholesale Pricing Template Choice', 'vtwpr')
           ),
          array (
            'id'       => 'store-wide-discount-display-optgroup',
            'class'    => 'rule-type-enable-this',
            'value'    => '0',
            'title'    => '&nbsp;&nbsp;&nbsp;&nbsp;' . __('- Please Choose a Wholesale Pricing Template Type -', 'vtwpr')
           ),
          array (
            'id'       => 'optgroup-realtime',
            'class'    => 'optgroup-class',
            'value'    => '',
            'title'    => '&nbsp;' . __('Immediate Price Reduction - ', 'vtwpr') .'&nbsp;&nbsp;&nbsp;'. __('Catalog Display Pricing', 'vtwpr')
           ),                                                   
          array (
            'id'       => 'store-wide-discount-display',
            'class'    => 'rule-type-enable-this',
            'value'    => 'D-storeWideSale',
            'title'    =>  __('Store-Wide Sale with a Percentage or $ Value Off', 'vtwpr') .'&nbsp;&nbsp;&nbsp;'. __('applied when PRODUCT DISPLAYS', 'vtwpr') .'&nbsp;&nbsp;&nbsp;'. __('- Realtime -', 'vtwpr')
           ),  
/*           array (
            'id'       => 'membership-discount-display',
            'class'    => 'rule-type-enable-this',
            'value'    => 'D00-N2',
            'title'    =>  __('Membership Discount in the Buy Pool Group, at Catalog Display Time &nbsp;&nbsp;-Realtime-', 'vtwpr')
           ),
           array (
            'id'       => 'wholesale-discount-display',
            'class'    => 'rule-type-enable-this',
            'value'    => 'D00-N3',
            'title'    =>  __('Wholesale Discount in the Buy Pool Group, at Catalog Display Time &nbsp;&nbsp;-Realtime-', 'vtwpr')
           ),  */
           array (
            'id'       => 'any-combo-display',
            'class'    => 'rule-type-enable-this',
            'value'    => 'D-simpleDiscount',
            'title'    =>  __('Simple Discount', 'vtwpr') .'&nbsp;&nbsp;&nbsp;'.  
                           __('(by Membership / Wholesale  / Product Category / Wholesale Pricing Category / Product / Variation)', 'vtwpr')
                             .'&nbsp;&nbsp;&nbsp;'.  __('- Realtime -', 'vtwpr')
           ),
        /* array (
           'id'       => 'upcharge-display',
            'class'    => 'rule-type-disable-this',
            'value'    => 'D00-N5',
            'title'    =>  __('Coming soon - UpCharge (Base Price + UpCharge) by any Buy Pool Group Criteria, including pricing by variation', 'vtwpr')
           ),  */         
          array (
            'id'       => 'optgroup-addToCart-simple',
            'class'    => 'optgroup-class',
            'value'    => '',
            'title'    => '&nbsp;' . __('Simple Deals for add to Cart Pricing', 'vtwpr')
           ),            
          array (
            'id'       => 'store-wide-discount-cart',
            'class'    => 'rule-type-enable-this',
            'value'    => 'C-storeWideSale',
            'title'    =>  __('Store-Wide Sale with a Percentage or $ Value Off', 'vtwpr') .'&nbsp;&nbsp;&nbsp;'.  __('- Cart -', 'vtwpr')
           ),
 /*         array (
            'id'       => 'membership-discount-cart',
            'class'    => 'rule-type-enable-this',
            'value'    => 'C00-N2',
            'title'    =>  __('Membership Discount for all Products in the Buy Pool Group in the Cart', 'vtwpr')
           ),
          array (
            'id'       => 'wholesale-discount-cart',
            'class'    => 'rule-type-enable-this',
            'value'    => 'C00-N3',
            'title'    =>  __('Wholesale Discount for all Products in the Buy Pool Group in the Cart', 'vtwpr')
           ),  */
          array (     
            'id'       => 'any-combo-cart',
            'class'    => 'rule-type-enable-this',
            'value'    => 'C-simpleDiscount',
            'title'    =>  __('Simple Discount', 'vtwpr') .'&nbsp;&nbsp;&nbsp;'.
                           __('(by Membership / Wholesale  / Product Category / Wholesale Pricing Category / Product / Variation)', 'vtwpr') 
                           .'&nbsp;&nbsp;&nbsp;'.  __('- Cart -', 'vtwpr')
           ),             
          array (
            'id'       => 'optgroup-addToCart-already-in-cart',
            'class'    => 'optgroup-class',
            'value'    => '',
            'title'    => '&nbsp;' .  __('Discount for purchases *already in Cart*', 'vtwpr')
           ), 
          array (
            'id'       => 'same-group-discount',
            'class'    => 'rule-type-enable-this',
            'value'    => 'C-discount-inCart',
            'title'    => __('Buy 5/$500, Get a discount for Some/All of Those 5', 'vtwpr')
                          . '&nbsp;&nbsp;' . '-'. '&nbsp;&nbsp;' .
                          __('*BOGO*', 'vtwpr')
           ),
           array (
            'id'       => 'forThePriceOf-same-group-discount',
            'class'    => 'rule-type-enable-this',
            'value'    => 'C-forThePriceOf-inCart',
            'title'    => __('Buy 5, Get them for the price of 4/$400', 'vtwpr')
                          . '&nbsp;&nbsp;' . '-'. '&nbsp;&nbsp;' .
                          __('*PACKAGE PRICING*', 'vtwpr')
           ),
           array (
            'id'       => 'most-expensive-same-group-discount',
            'class'    => 'rule-type-enable-this',
            'value'    => 'C-cheapest-inCart',
            'title'    => __('Buy 5/$500, Get the cheapest/most expensive of those at a discount', 'vtwpr')
           ),
          array (
            'id'       => 'optgroup-addToCart-next-in-cart',
            'class'    => 'optgroup-class',
            'value'    => '',
            'title'    =>  '&nbsp;' . __('Discount for *Next* Purchases added to Cart', 'vtwpr')
           ),           
           array (
            'id'       => 'other-group-discount',
            'class'    => 'rule-type-enable-this',
            'value'    => 'C-discount-Next',
            'title'    => __('Buy 6/$600, Get a discount on Next 4/$400 added to Cart', 'vtwpr')
                          . '&nbsp;&nbsp;' . '-'. '&nbsp;&nbsp;' .
                          __('*BOGO*', 'vtwpr')
           ),
           array (
            'id'       => 'forThePriceOf-other-group-discount',
            'class'    => 'rule-type-enable-this',
            'value'    => 'C-forThePriceOf-Next',
            'title'    => __('Buy 6/$600, Get Next 3 for the price of 2/$200', 'vtwpr')
                          . '&nbsp;&nbsp;' . '-'. '&nbsp;&nbsp;' .
                          __('*PACKAGE PRICING*', 'vtwpr')
           ),
          array (
            'id'       => 'most-expensive-other-group-discount',
            'class'    => 'rule-type-enable-this',
            'value'    => 'C-cheapest-Next',
            'title'    => __('Buy 6/$600, Get a discount on the cheapest/most expensive in Next 5/$500 added to Cart', 'vtwpr')
           ),
          array (
            'id'       => 'nth-other-group-discount',
            'class'    => 'rule-type-enable-this',
            'value'    => 'C-nth-Next',
            'title'    => __('Buy 6/$600, Get the Next Nth added to Cart at a discount', 'vtwpr')
           )           
        )   
                      
    ); //end vtwpr_rule_cart_type_framework
        
    /*
    This framework drives the contruction of the deal options offered,
    and their matching logic requirements during processing.
    */  
    $vtwpr_template_structures_framework = array(  
       //Discount for all products in the catalog from this group
       //           applies_to_rule_field   'required' / 'optional'  / default value
       //           --------------------    ----------

       //
       //    Look up array 1st value 'applies_to_rule_field '  in   $vtwpr_field_edits_framework
       //       copy to  $vtwpr_deal_edits_framework array, replacing required_or_optional with value below.
       
       //    If value not in this array, ignore for this template and BLANK OUT THE VALUE in the rule
      //xxxxxxx ==> $vtwpr_ruleTemplateDisplay_framework/$vtwpr_ruleTemplateCart_framework **framework value**
     /*
       DEFAULT FIELD HANDLING
       Default fields must be listed in the template arrays.  However, as default values require only the dropdown,
       where the array does not directly access the dropdown amt value, only the dropdown has an ACTVIVATING value.
     
     */ 
      //*******************************************
      //  FOR ALL optional / not present DROPDOWNS, if not selected (value = 'title') => default value (usally 'none')
      //*******************************************
        
        //CART Templates   
        //'C-storeWideSale' - Cart => simple percentage/value off, Whole Store - FREE 
        //    Store-Wide Sale with a Percentage or $$ Value Off        
        'C-storeWideSale' =>   array(   
              'buy_repeat_condition'  => array ( 
                'required_or_optional'             => 'required', 
                'allowed_values'                   =>  'all',     //'' = none are allowed, 'all' or an array
                'template_profile_error_msg'       =>  ''
              ),
              'buy_repeat_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'buy_amt_type'  => array (      
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => array('none'),
                'template_profile_error_msg'       =>  __('"Buy group amount" - only "No buy condition" is allowed for this setup type ', 'vtwpr')
              ),
              'buy_amt_count'  => array (
                'required_or_optional'             => '', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       =>  __('"Buy pool amount" - no values allowed for this setup type ', 'vtwpr')
              ),              
              'buy_amt_applies_to'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ), 
            /*  'buy_amt_applies_to'  => array (
                'required_or_optional'             => 'optional',                             
                'allowed_values'                   => array('all'),
                'template_profile_error_msg'       =>  __('"Buy pool amount applies to" - only "All buy pool products" is allowed for this setup type ', 'vtwpr')
              ), */                          
              'buy_amt_mod'  => array (
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => array('none'),
                'template_profile_error_msg'       =>  __('"Buy group min / max" - only "No Discount Group Amount Min / Max " is allowed for this setup type ', 'vtwpr')
              ),                   
              'buy_amt_mod_count'  => array (
                'required_or_optional'             => '', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       =>  __('"Buy group min / max amount" - no values allowed for this setup type ', 'vtwpr')
              ),
              'action_repeat_condition'  => array (                     
                'required_or_optional'             => 'optional', 
                'allowed_values'                   =>  array('none'),
                'template_profile_error_msg'       =>  __('"Get group repeat" - only " No Repeats  (Apply Discount to Discount Group ONCE)" is allowed for this setup type ', 'vtwpr')
              ), 
              'action_repeat_count'  => array (
                'required_or_optional'             => '', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       =>  __('"Get repeat amount" - no values allowed for this setup type ', 'vtwpr')
              ),
              'action_amt_type'  => array (
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => array('none'),
                'template_profile_error_msg'       =>  __('"Get group amount" - only "Discount Each Unit" is allowed for this setup type ', 'vtwpr')
              ),
              'action_amt_count'  => array (
                'required_or_optional'             => '', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       =>  __('"Get pool amount" - no values allowed for this setup type ', 'vtwpr')
              ),
              'action_amt_applies_to'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => array('all'),
                'template_profile_error_msg'       =>  __('"Get pool amount applies to" - only "All action pool products" is allowed for this setup type ', 'vtwpr')
              ),
              'action_amt_mod'  => array (
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => array('none'),          
                'template_profile_error_msg'       =>  __('"Get group min/mx - only "No Discount Group Amount Min / Max " is allowed for this setup type ', 'vtwpr')
              ),     
              'action_amt_mod_count'  => array (
                'required_or_optional'             => '', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       =>  __('"Get pool amount amount" - no values allowed for this setup type ', 'vtwpr')
              ),
              'discount_amt_type'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => array ('percent', 'currency', 'fixedPrice', 'free'),
                'template_profile_error_msg'       =>  __('"Discount Type " - "For the price of" is not allowed for this setup type ', 'vtwpr'),
                'cross_field_edits'                =>  array (    //only there where needed!                
                  array ( 
                    'cross_field_name'            => 'discount_applies_to',
                    'cross_field_insertBefore'    => '#discount_amt_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('fixedPrice', 'free'),
                    'cross_allowed_values'        => array ('each','cheapest','most_expensive'),
                    'cross_error_msg'             => __('When "Discount Type" is "Fixed Price" or "Free", "Discount Applies To" must be "Each", "Cheapest" or "Most Expensive" ', 'vtwpr')
                  )
                )
              ),
              'discount_amt_count'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''                 
              ),
              'discount_auto_add_free_product'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       => __('"Automatically Add Free Product to Cart" not allowed for this deal type', 'vtwpr')
              ),
          /*    'discount_for_the_price_of_count'  => array (
                'required_or_optional'             => '', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       =>  __('"Discount for the price of" - no values allowed for this setup type ', 'vtwpr')
              ),  */         
              'discount_applies_to'  => array (
                'required_or_optional'             => 'required',                         
                'allowed_values'                   => array('each', 'all'),
                'template_profile_error_msg'       =>  __('"Discount applies to" - "each product" or "all products" only allowed for this setup type ', 'vtwpr')
              ),              
            //  'discount_applies_to_nth_count'      => '',
              'discount_rule_max_amt_type'  => array (              //only used in 1st iteration   
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''              
              ),
              'discount_rule_max_amt_count'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_lifetime_max_amt_type'  => array (              //only used in 1st iteration     
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_lifetime_max_amt_count'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_rule_cum_max_amt_type'  => array (              //only used in 1st iteration   
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_rule_cum_max_amt_count'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              //not in Deal Line, used in general processing, NOT an array!!!... 
              'discountAppliesWhere'        	     => 'inCurrentInPopOnly', //'allActionPop', 
              'inPopAllowed'                       => 'wholeStore',   // wholeStore / groups / vargroup / single   OR  any
              'actionPopAllowed'                   => 'sameAsInPop', //sameAsInPop / wholeStore / groups / vargroup / single   OR  any                
              'cumulativeRulePricingAllowed'       => 'yes',
              'cumulativeSalePricingAllowed'       => 'yes',
              'replaceSalePricingAllowed'          => 'yes',
              'cumulativeCouponPricingAllowed'     => 'yes'
         ),          
                                 
        //'C-simpleDiscount' - Cart => simple percentage/value off, *by inPop* , ==>>> applies to the whole 'C00-N' series
        //      Population Discount in the Buy Pool Group           
        'C-simpleDiscount' =>   array( //Cart => simple percentage/value off, by pop           
              /*
              'buy_repeat_condition'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   =>  array('none'),  //'' = none are allowed, 'all' or an array
                'template_profile_error_msg'       =>  __('"Rule repeat condition" - only "Apply Rule Once (in this cart)" is allowed for this setup type ', 'vtwpr')
              ),
              */
              'buy_repeat_condition'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   =>  'all',  //'' = none are allowed, 'all' or an array
                'template_profile_error_msg'       =>  ''
              ),              
              'buy_repeat_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'buy_amt_type'  => array (
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => array('none'),
                'template_profile_error_msg'       =>  __('"Buy group amount" - only "No buy condition" is allowed for this setup type ', 'vtwpr')
              ),
              'buy_amt_count'  => array (
                'required_or_optional'             => '', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       =>  __('"Buy pool amount" - no values allowed for this setup type ', 'vtwpr')
              ),              
              'buy_amt_applies_to'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => array('each'),
                'template_profile_error_msg'       =>  __('"Buy pool amount applies to" - only "Each buy pool product" is allowed for this setup type ', 'vtwpr')
              ),              
              'buy_amt_mod'  => array (
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => array('none'),
                'template_profile_error_msg'       =>  __('"Buy group min / max" - only "No Discount Group Amount Min / Max " is allowed for this setup type ', 'vtwpr')
              ),                   
              'buy_amt_mod_count'  => array (
                'required_or_optional'             => '', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       =>  __('"Buy group min / max amount" - no values allowed for this setup type ', 'vtwpr')
              ),
              'action_repeat_condition'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   =>  array('none'),
                'template_profile_error_msg'       =>  __('"Get repeat condition" - only "Apply the Get Pool Once (No Repeats)" is allowed for this setup type ', 'vtwpr')
              ), 
              'action_repeat_count'  => array (
                'required_or_optional'             => '', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       =>  __('"Get repeat amount" - no values allowed for this setup type ', 'vtwpr')
              ),
              'action_amt_type'  => array (
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => array('none'),
                'template_profile_error_msg'       =>  __('"Get group amount" - only "Discount Each Unit" is allowed for this setup type ', 'vtwpr')
              ),
              'action_amt_count'  => array (
                'required_or_optional'             => '', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       =>  __('"Get pool amount" - no values allowed for this setup type ', 'vtwpr')
              ),
              'action_amt_applies_to'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => array('all'),
                'template_profile_error_msg'       =>  __('"Get pool amount applies to" - only "All action pool products" is allowed for this setup type ', 'vtwpr')
              ),
              'action_amt_mod'  => array (
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => array('none'),
                'template_profile_error_msg'       =>  __('"Get group min/mx - only "No Discount Group Amount Min / Max " is allowed for this setup type ', 'vtwpr')
              ),     
              'action_amt_mod_count'  => array (
                'required_or_optional'             => '', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       =>  __('"Get pool amount amount" - no values allowed for this setup type ', 'vtwpr')
              ),
              'discount_amt_type'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => array ('percent', 'currency', 'fixedPrice', 'free'),
                'template_profile_error_msg'       =>  __('"Discount Type " - "For the price of" is not allowed for this setup type ', 'vtwpr'),
                'cross_field_edits'                =>  array (    //only there where needed!                
                  array ( 
                    'cross_field_name'            => 'discount_applies_to',
                    'cross_field_insertBefore'    => '#discount_amt_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('fixedPrice', 'free'),
                    'cross_allowed_values'        => array ('each','cheapest','most_expensive'),
                    'cross_error_msg'             => __('When "Discount Type" is "Fixed Price" or "Free", "Discount Applies To" must be "Each", "Cheapest" or "Most Expensiv" ', 'vtwpr')
                  )
                )
              ),
              'discount_amt_count'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''                
              ),
              'discount_auto_add_free_product'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       => __('"Automatically Add Free Product to Cart" not allowed for this deal type', 'vtwpr')
              ),
            /*  'discount_for_the_price_of_count'  => array (
                'required_or_optional'             => '', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       =>  __('"Discount for the price of" - no values allowed for this setup type ', 'vtwpr')
              ),  */         
              'discount_applies_to'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => array('each', 'all'),
                'template_profile_error_msg'       =>  __('"Discount applies to" - "each product" or "all products" only allowed for this setup type ', 'vtwpr')
              ),              
            //  'discount_applies_to_nth_count'      => '',
              'discount_rule_max_amt_type'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'discount_rule_max_amt_count'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_lifetime_max_amt_type'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_lifetime_max_amt_count'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_rule_cum_max_amt_type'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_rule_cum_max_amt_count'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              //not in Deal Line, used in general processing... 
              'discountAppliesWhere'        		   => 'inCurrentInPopOnly', //'allActionPop', 
              'inPopAllowed'                       => 'any',   // wholeStore / groups / vargroup / single   OR  any
              'actionPopAllowed'                   => 'sameAsInPop', //sameAsInPop / wholeStore / groups / vargroup / single   OR  any   
              'cumulativeRulePricingAllowed'       => 'yes',
              'cumulativeSalePricingAllowed'       => 'yes',
              'replaceSalePricingAllowed'          => 'yes',
              'cumulativeCouponPricingAllowed'     => 'yes'       
         ),
        
        //'C-discount-inCart' - Cart => buy x/$x get discount on y in same group 
        //      Buy 5/$500, get a discount for Some/All 5  
        'C-discount-inCart' =>  array(            
              'buy_repeat_condition'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   =>  'all',  //'' = none are allowed, 'all' or an array
                'template_profile_error_msg'       =>  '' 
              ),
              'buy_repeat_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'buy_amt_type'  => array (
                'required_or_optional'             => 'required',   
                'allowed_values'                   => 'all', //array('one','quantity','currency','nthQuantity'),
                'template_profile_error_msg'       => __('Please choose a valid Group Amount Type', 'vtwpr')
              ),
              'buy_amt_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => '' 
              ),              
              'buy_amt_applies_to'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),              
              'buy_amt_mod'  => array (
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),                   
              'buy_amt_mod_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => '' 
              ),
              'action_repeat_condition'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   =>  'all',
                'template_profile_error_msg'       =>  '' 
              ), 
              'action_repeat_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => '' 
              ),
              'action_amt_type'  => array (
                'required_or_optional'             => 'required',  
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'action_amt_count'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => '' 
              ),
              'action_amt_applies_to'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => '' 
              ),
              'action_amt_mod'  => array (
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),     
              'action_amt_mod_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'discount_amt_type'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => array ('percent', 'currency', 'fixedPrice', 'free'),
                'template_profile_error_msg'       =>  __('"Discount Type " - "For the price of" is not allowed for this setup type ', 'vtwpr'),
                'cross_field_edits'                =>  array (    //only there where needed!                
                  array ( 
                    'cross_field_name'            => 'discount_applies_to',
                    'cross_field_insertBefore'    => '#discount_amt_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('fixedPrice', 'free'),
                    'cross_allowed_values'        => array ('each','cheapest','most_expensive'),
                    'cross_error_msg'             => __('When "Discount Type" is "Fixed Price" or "Free", "Discount Applies To" must be "Each", "Cheapest" or "Most Expensive" ', 'vtwpr')
                  )
                )
              ),
              'discount_amt_count'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''                 
              ),
              'discount_auto_add_free_product'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => '',              
                'cross_field_edits'                =>  array (    //only there where needed!     "Discount a Single Product Only"           
                  /*array ( 
                    'cross_field_name'            => 'buy_amt_type',
                    'cross_field_insertBefore'    => '#buy_amt_type',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'one', 'quantity'),
                    'cross_error_msg'             => __('" Product Filter Amount" may not be: "Buy $$ Value" or "Nth Quantity" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ), */                 
                  array ( 
                    'cross_field_name'            => 'popChoiceOut',
                    'cross_field_insertBefore'    => '#action_group_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('single', 'vargroup', 'sameAsInPop'),   //additional edit added to check inpop for single/single var...
                    'cross_error_msg'             => __('"Get (Discount) Group Filter" must be either a single product, or a single product variation, When "Automatically Add Free Product to Cart" is Selected.', 'vtwpr') .'<br><br>'. __('Choose either "Discount a Single Product Only", or "Discount a Single Product with Variations" and select a *Single* Variation. ', 'vtwpr') .'<br><br>'. __('Otherwise the Auto add does not know which product to add. ', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'action_amt_type',
                    'cross_field_insertBefore'    => '#action_amt_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'zero', 'one', 'quantity'),
                    'cross_error_msg'             => __('"Get (Discount) Group Amount" may not be: "Discount $$ Value" or "Nth Quantity" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ),                  
                  array ( 
                    'cross_field_name'            => 'discount_rule_max_amt_type',
                    'cross_field_insertBefore'    => '#discount_rule_max_amt_type_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'quantity'),
                    'cross_error_msg'             => __('"Discount Maximum for Rule" may only be: "No Discount Rule Max" or "Maximum Number of Times" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'discount_lifetime_max_amt_type',
                    'cross_field_insertBefore'    => '#discount_lifetime_max_amt_type_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'quantity'),
                    'cross_error_msg'             => __('"Customer Rule Limit" may only be: "None" or "Max Rule Usage Count per Customer?" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'discount_rule_cum_max_amt_type',
                    'cross_field_insertBefore'    => '#discount_rule_cum_max_amt_type_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'quantity'),
                    'cross_error_msg'             => __('"Discount per Product Maximum" may only be: "No Cumulative Product Max" or "Unit Quantity - Cumulative Product Max" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'cumulativeCouponPricing',
                    'cross_field_insertBefore'    => '#cumulativeCouponPricing',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('yes'),
                    'cross_error_msg'             => __('"Apply this Rule Discount in Addition to Coupon Discount" must be: "Yes" , When "Automatically Add Free Product to Cart" is Selected.' , 'vtwpr')
                                                     . '<br><br>'.
                                                     __('In this case, the Rule will auto add the Free product, and the Coupon will be ignored. ' , 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'action_repeat_condition',
                    'cross_field_insertBefore'    => '#action_repeat_condition_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none'),
                    'cross_error_msg'             => __('"Discount Group Repeat" must be: "No Repeats (Apply Discount to Discount Group ONCE)" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  )                      
                )                
              ),
           /*   'discount_for_the_price_of_count'  => array (
                'required_or_optional'             => '', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       =>  __('"Discount for the price of" - no values allowed for this setup type ', 'vtwpr')
              ),  */         
              'discount_applies_to'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),              
              'discount_rule_max_amt_type'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'discount_rule_max_amt_count'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   =>  'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_lifetime_max_amt_type'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'discount_lifetime_max_amt_count'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_rule_cum_max_amt_type'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_rule_cum_max_amt_count'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              //not in Deal Line, used in general processing... 
              'discountAppliesWhere'        	     => 'inCurrentInPopOnly', 
              'inPopAllowed'                       => 'any',   // wholeStore / groups / vargroup / single   OR  any
              'actionPopAllowed'                   => 'sameAsInPop', //sameAsInPop / wholeStore / groups / vargroup / single   OR  any                      
              'cumulativeRulePricingAllowed'       => 'yes',
              'cumulativeSalePricingAllowed'       => 'yes',
              'replaceSalePricingAllowed'          => 'yes',
              'cumulativeCouponPricingAllowed'     => 'yes'     
        	),
        //Cart => buy x get them for the price of y/$y in same group  
        //      Buy 5/$500, get them for the price of 4/$400
        'C-forThePriceOf-inCart' =>  array(           
              'buy_repeat_condition'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   =>  'all',       //'' = none are allowed, 'all' or an array
                'template_profile_error_msg'       =>  '' 
                
              ),
              'buy_repeat_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  '' 
              ),
              'buy_amt_type'  => array (
                'required_or_optional'             => 'required',     
                'allowed_values'                   => array('quantity'),
                'template_profile_error_msg'       => __('"Group Amount Type " - Only "buy Unit Quantity" allowed for this setup type ', 'vtwpr')
              ),
              'buy_amt_count'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),              
              'buy_amt_applies_to'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => '' 
              ),              
              'buy_amt_mod'  => array (
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),                   
              'buy_amt_mod_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => '' 
              ),
              'action_repeat_condition'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   =>  'all',
                'template_profile_error_msg'       =>  '' 
              ), 
              'action_repeat_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'action_amt_type'  => array (
                'required_or_optional'             => 'required',  
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => '' 
              ),
              'action_amt_count'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => '' 
              ),
              'action_amt_applies_to'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'action_amt_mod'  => array (
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),     
              'action_amt_mod_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'discount_amt_type'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => array ('forThePriceOf_Units','forThePriceOf_Currency'),
                'template_profile_error_msg'       =>  __('"Discount Type " - "For the price of" is required for this template ', 'vtwpr'),                 
                'cross_field_edits'                =>  array (    //only there where needed!                
                  array ( 
                    'cross_field_name'            => 'discount_applies_to',
                    'cross_field_insertBefore'    => '#discount_amt_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('forThePriceOf_Units','forThePriceOf_Currency'),
                    'cross_allowed_values'        => array ('all'),
                    'cross_error_msg'             => __('When "For the price of" is selected, "Discount Applies To" must be "all" ', 'vtwpr')
                  )  
                )
              ),
              'discount_amt_count'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => __('"for the price of units " is required for this template ', 'vtwpr')
              ),
              'discount_auto_add_free_product'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => '',                 
                'cross_field_edits'                =>  array (    //only there where needed!     "Discount a Single Product Only"           
                 /* array ( 
                    'cross_field_name'            => 'buy_amt_type',
                    'cross_field_insertBefore'    => '#buy_amt_type',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'one', 'quantity'),
                    'cross_error_msg'             => __('" Product Filter Amount" may not be: "Buy $$ Value" or "Nth Quantity" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ), */                 
                  array ( 
                    'cross_field_name'            => 'popChoiceOut',
                    'cross_field_insertBefore'    => '#action_group_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('single', 'vargroup', 'sameAsInPop'),   //additional edit added to check inpop for single/single var...
                    'cross_error_msg'             => __('"Get (Discount) Group Filter" must be either a single product, or a single product variation, When "Automatically Add Free Product to Cart" is Selected.', 'vtwpr') .'<br><br>'. __('Choose either "Discount a Single Product Only", or "Discount a Single Product with Variations" and select a *Single* Variation. ', 'vtwpr') .'<br><br>'. __('Otherwise the Auto add does not know which product to add. ', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'action_amt_type',
                    'cross_field_insertBefore'    => '#action_amt_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'zero', 'one', 'quantity'),
                    'cross_error_msg'             => __('"Get (Discount) Group Amount" may not be: "Discount $$ Value" or "Nth Quantity" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'discount_rule_max_amt_type',
                    'cross_field_insertBefore'    => '#discount_rule_max_amt_type_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'quantity'),
                    'cross_error_msg'             => __('"Discount Maximum for Rule" may only be: "No Discount Rule Max" or "Maximum Number of Times" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'discount_lifetime_max_amt_type',
                    'cross_field_insertBefore'    => '#discount_lifetime_max_amt_type_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'quantity'),
                    'cross_error_msg'             => __('"Customer Rule Limit" may only be: "None" or "Max Rule Usage Count per Customer?" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'discount_rule_cum_max_amt_type',
                    'cross_field_insertBefore'    => '#discount_rule_cum_max_amt_type_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'quantity'),
                    'cross_error_msg'             => __('"Discount per Product Maximum" may only be: "No Cumulative Product Max" or "Unit Quantity - Cumulative Product Max" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'cumulativeCouponPricing',
                    'cross_field_insertBefore'    => '#cumulativeCouponPricing',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('yes'),
                    'cross_error_msg'             => __('"Apply this Rule Discount in Addition to Coupon Discount" must be: "Yes" , When "Automatically Add Free Product to Cart" is Selected.' , 'vtwpr')
                                                     . '<br><br>'.
                                                     __('In this case, the Rule will auto add the Free product, and the Coupon will be ignored. ' , 'vtwpr')                                    
                  ),
                  array ( 
                    'cross_field_name'            => 'action_repeat_condition',
                    'cross_field_insertBefore'    => '#action_repeat_condition_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none'),
                    'cross_error_msg'             => __('"Discount Group Repeat" must be: "No Repeats (Apply Discount to Discount Group ONCE)" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  )                         
                )                
              ),
           /*   'discount_for_the_price_of_count'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ), */          
              'discount_applies_to'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),              
              'discount_rule_max_amt_type'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'discount_rule_max_amt_count'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   =>  'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_lifetime_max_amt_type'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'discount_lifetime_max_amt_count'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_rule_cum_max_amt_type'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_rule_cum_max_amt_count'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              //not in Deal Line, used in general processing... 
              'discountAppliesWhere'        	     => 'inCurrentInPopOnly', 
              'inPopAllowed'                       => 'any',   // wholeStore / groups / vargroup / single   OR  any
              'actionPopAllowed'                   => 'sameAsInPop', //sameAsInPop / wholeStore / groups / vargroup / single   OR  any                      
              'cumulativeRulePricingAllowed'       => 'yes',
              'cumulativeSalePricingAllowed'       => 'yes',
              'replaceSalePricingAllowed'          => 'yes',
              'cumulativeCouponPricingAllowed'     => 'yes'   
        	),
        //Cart => buy x/$x get the highest/lowest at a discount same group
        //      Buy 5/$500, get the cheapest/most expensive at a discount 
        'C-cheapest-inCart' =>  array(             
              'buy_repeat_condition'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   =>  'all',    //'' = none are allowed, 'all' or an array
                'template_profile_error_msg'       =>  ''
              ),
              'buy_repeat_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'buy_amt_type'  => array (
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => '' 
              ),
              'buy_amt_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),              
              'buy_amt_applies_to'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),              
              'buy_amt_mod'  => array (
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => '' 
              ),                   
              'buy_amt_mod_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'action_repeat_condition'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   =>  'all',
                'template_profile_error_msg'       =>  ''
              ), 
              'action_repeat_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => '' 
              ),
              'action_amt_type'  => array (
                'required_or_optional'             => 'required',  
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'action_amt_count'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'action_amt_applies_to'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'action_amt_mod'  => array (
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),     
              'action_amt_mod_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'discount_amt_type'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => array ('percent', 'currency', 'fixedPrice', 'free'),
                'template_profile_error_msg'       =>  __('"Discount Type " - "For the price of" is not allowed for this setup type ', 'vtwpr'),
                'cross_field_edits'                =>  array (    //only there where needed!                
                  array ( 
                    'cross_field_name'            => 'discount_applies_to',
                    'cross_field_insertBefore'    => '#discount_amt_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('fixedPrice', 'free'),
                    'cross_allowed_values'        => array ('each','cheapest','most_expensive'),
                    'cross_error_msg'             => __('When "Discount Type" is "Fixed Price" or "Free", "Discount Applies To" must be "Each", "Cheapest" or "Most Expensive" ', 'vtwpr')
                  )
                )
              ),
              'discount_amt_count'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''               
              ),
              'discount_auto_add_free_product'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => '',                 
                'cross_field_edits'                =>  array (    //only there where needed!     "Discount a Single Product Only"           
                  /*array ( 
                    'cross_field_name'            => 'buy_amt_type',
                    'cross_field_insertBefore'    => '#buy_amt_type',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'one', 'quantity'),
                    'cross_error_msg'             => __('" Product Filter Amount" may not be: "Buy $$ Value" or "Nth Quantity" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ), */                 
                  array ( 
                    'cross_field_name'            => 'popChoiceOut',
                    'cross_field_insertBefore'    => '#action_group_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('single', 'vargroup', 'sameAsInPop'),   //additional edit added to check inpop for single/single var...
                    'cross_error_msg'             => __('"Get (Discount) Group Filter" must be either a single product, or a single product variation, When "Automatically Add Free Product to Cart" is Selected.', 'vtwpr') .'<br><br>'. __('Choose either "Discount a Single Product Only", or "Discount a Single Product with Variations" and select a *Single* Variation. ', 'vtwpr') .'<br><br>'. __('Otherwise the Auto add does not know which product to add. ', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'action_amt_type',
                    'cross_field_insertBefore'    => '#action_amt_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'zero', 'one', 'quantity'),
                    'cross_error_msg'             => __('"Get (Discount) Group Amount" may not be: "Discount $$ Value" or "Nth Quantity" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'discount_rule_max_amt_type',
                    'cross_field_insertBefore'    => '#discount_rule_max_amt_type_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'quantity'),
                    'cross_error_msg'             => __('"Discount Maximum for Rule" may only be: "No Discount Rule Max" or "Maximum Number of Times" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'discount_lifetime_max_amt_type',
                    'cross_field_insertBefore'    => '#discount_lifetime_max_amt_type_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'quantity'),
                    'cross_error_msg'             => __('"Customer Rule Limit" may only be: "None" or "Max Rule Usage Count per Customer?" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'discount_rule_cum_max_amt_type',
                    'cross_field_insertBefore'    => '#discount_rule_cum_max_amt_type_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'quantity'),
                    'cross_error_msg'             => __('"Discount per Product Maximum" may only be: "No Cumulative Product Max" or "Unit Quantity - Cumulative Product Max" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'cumulativeCouponPricing',
                    'cross_field_insertBefore'    => '#cumulativeCouponPricing_0',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('yes'),
                    'cross_error_msg'             => __('"Apply this Rule Discount in Addition to Coupon Discount" must be: "Yes" , When "Automatically Add Free Product to Cart" is Selected.' , 'vtwpr')
                                                     . '<br><br>'.
                                                     __('In this case, the Rule will auto add the Free product, and the Coupon will be ignored. ' , 'vtwpr')                                    
                  ),
                  array ( 
                    'cross_field_name'            => 'action_repeat_condition',
                    'cross_field_insertBefore'    => '#action_repeat_condition_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none'),
                    'cross_error_msg'             => __('"Discount Group Repeat" must be: "No Repeats (Apply Discount to Discount Group ONCE)" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  )                          
                )                
              ),
           /*   'discount_for_the_price_of_count'  => array (
                'required_or_optional'             => '', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       =>  __('"Discount for the price of" - no values allowed for this setup type ', 'vtwpr')
              ),  */         
              'discount_applies_to'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => array('cheapest', 'most_expensive'),
                'template_profile_error_msg'       => __('"Discount applies to" - only "Cheapest" or "Most Expensive" allowed for this setup type ', 'vtwpr')
              ),              
              'discount_rule_max_amt_type'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'discount_rule_max_amt_count'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   =>  'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_lifetime_max_amt_type'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'discount_lifetime_max_amt_count'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_rule_cum_max_amt_type'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_rule_cum_max_amt_count'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              //not in Deal Line, used in general processing... 
              'discountAppliesWhere'        	     => 'inCurrentInPopOnly', 
              'inPopAllowed'                       => 'any',   // wholeStore / groups / vargroup / single   OR  any
              'actionPopAllowed'                   => 'sameAsInPop', //sameAsInPop / wholeStore / groups / vargroup / single   OR  any                      
              'cumulativeRulePricingAllowed'       => 'yes',
              'cumulativeSalePricingAllowed'       => 'yes',
              'replaceSalePricingAllowed'          => 'yes',
              'cumulativeCouponPricingAllowed'     => 'yes'  
        	),
        //Cart => buy x/$x get discount on y/$y in a same/different group 
        //    Buy 5/$500, get a discount on Next 5/$500               
        'C-discount-Next' =>  array(           
              'buy_repeat_condition'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   =>  'all',       //'' = none are allowed, 'all' or an array
                'template_profile_error_msg'       =>  ''
              ),
              'buy_repeat_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'buy_amt_type'  => array (
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'buy_amt_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),              
              'buy_amt_applies_to'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),              
              'buy_amt_mod'  => array (
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),                   
              'buy_amt_mod_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => '' 
              ),
              'action_repeat_condition'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   =>  'all',
                'template_profile_error_msg'       =>  ''
              ), 
              'action_repeat_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'action_amt_type'  => array (
                'required_or_optional'             => 'required',  
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'action_amt_count'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => '' 
              ),
              'action_amt_applies_to'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'action_amt_mod'  => array (
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),     
              'action_amt_mod_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'discount_amt_type'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => array ('percent', 'currency', 'fixedPrice', 'free'),
                'template_profile_error_msg'       =>  __('"Discount Type " - "For the price of" is not allowed for this setup type ', 'vtwpr'),
                'cross_field_edits'                =>  array (    //only there where needed!                
                  array ( 
                    'cross_field_name'            => 'discount_applies_to',
                    'cross_field_insertBefore'    => '#discount_amt_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('fixedPrice', 'free'),
                    'cross_allowed_values'        => array ('each','cheapest','most_expensive'),
                    'cross_error_msg'             => __('When "Discount Type" is "Fixed Price" or "Free", "Discount Applies To" must be "Each", "Cheapest" or "Most Expensive" ', 'vtwpr')
                  )                  
                )
              ),
              'discount_amt_count'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_auto_add_free_product'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => '',
                'cross_field_edits'                =>  array (    //only there where needed!     "Discount a Single Product Only"           
                /*  array ( 
                    'cross_field_name'            => 'buy_amt_type',
                    'cross_field_insertBefore'    => '#buy_amt_type',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'one', 'quantity'),
                    'cross_error_msg'             => __('" Product Filter Amount" may not be: "Buy $$ Value" or "Nth Quantity" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ),  */                
                  array ( 
                    'cross_field_name'            => 'popChoiceOut',
                    'cross_field_insertBefore'    => '#action_group_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('single', 'vargroup', 'sameAsInPop'),   //additional edit added to check inpop for single/single var...
                    'cross_error_msg'             => __('"Get (Discount) Group Filter" must be either a single product, or a single product variation, When "Automatically Add Free Product to Cart" is Selected.', 'vtwpr') .'<br><br>'. __('Choose either "Discount a Single Product Only", or "Discount a Single Product with Variations" and select a *Single* Variation. ', 'vtwpr') .'<br><br>'. __('Otherwise the Auto add does not know which product to add. ', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'action_amt_type',
                    'cross_field_insertBefore'    => '#action_amt_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'zero', 'one', 'quantity'),
                    'cross_error_msg'             => __('"Get (Discount) Group Amount" may not be: "Discount $$ Value" or "Nth Quantity" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'discount_rule_max_amt_type',
                    'cross_field_insertBefore'    => '#discount_rule_max_amt_type_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'quantity'),
                    'cross_error_msg'             => __('"Discount Maximum for Rule" may only be: "No Discount Rule Max" or "Maximum Number of Times" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'discount_lifetime_max_amt_type',
                    'cross_field_insertBefore'    => '#discount_lifetime_max_amt_type_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'quantity'),
                    'cross_error_msg'             => __('"Customer Rule Limit" may only be: "None" or "Max Rule Usage Count per Customer?" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'discount_rule_cum_max_amt_type',
                    'cross_field_insertBefore'    => '#discount_rule_cum_max_amt_type_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'quantity'),
                    'cross_error_msg'             => __('"Discount per Product Maximum" may only be: "No Cumulative Product Max" or "Unit Quantity - Cumulative Product Max" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'cumulativeCouponPricing',
                    'cross_field_insertBefore'    => '#cumulativeCouponPricing_0',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('yes'),
                    'cross_error_msg'             => __('"Apply this Rule Discount in Addition to Coupon Discount" must be: "Yes" , When "Automatically Add Free Product to Cart" is Selected.' , 'vtwpr')
                                                     . '<br><br>'.
                                                     __('In this case, the Rule will auto add the Free product, and the Coupon will be ignored. ' , 'vtwpr')                                    
                  ),
                  array ( 
                    'cross_field_name'            => 'action_repeat_condition',
                    'cross_field_insertBefore'    => '#action_repeat_condition_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none'),
                    'cross_error_msg'             => __('"Discount Group Repeat" must be: "No Repeats (Apply Discount to Discount Group ONCE)" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  )                                          
                )                
              ),
           /*   'discount_for_the_price_of_count'  => array (
                'required_or_optional'             => '', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       =>  __('"Discount for the price of" - no values allowed for this setup type ', 'vtwpr')
              ),  */         
              'discount_applies_to'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),              
              'discount_rule_max_amt_type'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'discount_rule_max_amt_count'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   =>  'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_lifetime_max_amt_type'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'discount_lifetime_max_amt_count'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_rule_cum_max_amt_type'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_rule_cum_max_amt_count'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              //not in Deal Line, used in general processing...
              'discountAppliesWhere'        	     => 'nextInActionPop', 
              'inPopAllowed'                       => 'any',   // wholeStore / groups / vargroup / single   OR  any
              'actionPopAllowed'                   => 'any', //sameAsInPop / wholeStore / groups / vargroup / single   OR  any                       
              'cumulativeRulePricingAllowed'       => 'yes',
              'cumulativeSalePricingAllowed'       => 'yes',
              'replaceSalePricingAllowed'          => 'yes',
              'cumulativeCouponPricingAllowed'     => 'yes' 
        	),
        //Cart => buy x/$x get them for the price of y from same/different group  
        //       Buy 5/$500, get next 3/$300 for the price of 2/$200
        'C-forThePriceOf-Next' =>  array(            
              'buy_repeat_condition'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   =>  'all',     //'' = none are allowed, 'all' or an array
                'template_profile_error_msg'       =>  '' 
              ),
              'buy_repeat_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'buy_amt_type'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ), 
              'buy_amt_count'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),              
              'buy_amt_applies_to'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),              
              'buy_amt_mod'  => array (
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),                   
              'buy_amt_mod_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'action_repeat_condition'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   =>  'all',
                'template_profile_error_msg'       =>  ''
              ), 
              'action_repeat_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'action_amt_type'  => array (
                'required_or_optional'             => 'required',     
                'allowed_values'                   => array('quantity'),
                'template_profile_error_msg'       => __('"Group Amount Type " - Only "buy Unit Quantity" allowed for this setup type ', 'vtwpr')
              ),
              'action_amt_count'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'action_amt_applies_to'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'action_amt_mod'  => array (
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),     
              'action_amt_mod_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'discount_amt_type'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => array ('forThePriceOf_Units','forThePriceOf_Currency'),
                'template_profile_error_msg'       =>  __('"Discount Type " - "For the price of" is required for this template ', 'vtwpr'),
                'cross_field_edits'                =>  array (    //only there where needed!                
                  array ( 
                    'cross_field_name'            => 'discount_applies_to',
                    'cross_field_insertBefore'    => '#discount_amt_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'   => array ('forThePriceOf_Units','forThePriceOf_Currency'),
                    'cross_allowed_values'        => array ('all'),
                    'cross_error_msg'             => __('When "For the price of" is selected, "Discount Applies To" must be "all" ', 'vtwpr')
                  )
                )
              ),
              'discount_amt_count'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => __('"for the price of units " is required for this template ', 'vtwpr')
              ),
              'discount_auto_add_free_product'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => '',
                'cross_field_edits'                =>  array (    //only there where needed!     "Discount a Single Product Only"           
                 /* array ( 
                    'cross_field_name'            => 'buy_amt_type',
                    'cross_field_insertBefore'    => '#buy_amt_type',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'one', 'quantity'),
                    'cross_error_msg'             => __('" Product Filter Amount" may not be: "Buy $$ Value" or "Nth Quantity" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ),*/                  
                  array ( 
                    'cross_field_name'            => 'popChoiceOut',
                    'cross_field_insertBefore'    => '#action_group_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('single', 'vargroup', 'sameAsInPop'),   //additional edit added to check inpop for single/single var...
                    'cross_error_msg'             => __('"Get (Discount) Group Filter" must be either a single product, or a single product variation, When "Automatically Add Free Product to Cart" is Selected.', 'vtwpr') .'<br><br>'. __('Choose either "Discount a Single Product Only", or "Discount a Single Product with Variations" and select a *Single* Variation. ', 'vtwpr') .'<br><br>'. __('Otherwise the Auto add does not know which product to add. ', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'action_amt_type',
                    'cross_field_insertBefore'    => '#action_amt_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'zero', 'one', 'quantity'),
                    'cross_error_msg'             => __('"Get (Discount) Group Amount" may not be: "Discount $$ Value" or "Nth Quantity" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'discount_rule_max_amt_type',
                    'cross_field_insertBefore'    => '#discount_rule_max_amt_type_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'quantity'),
                    'cross_error_msg'             => __('"Discount Maximum for Rule" may only be: "No Discount Rule Max" or "Maximum Number of Times" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'discount_lifetime_max_amt_type',
                    'cross_field_insertBefore'    => '#discount_lifetime_max_amt_type_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'quantity'),
                    'cross_error_msg'             => __('"Customer Rule Limit" may only be: "None" or "Max Rule Usage Count per Customer?" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'discount_rule_cum_max_amt_type',
                    'cross_field_insertBefore'    => '#discount_rule_cum_max_amt_type_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'quantity'),
                    'cross_error_msg'             => __('"Discount per Product Maximum" may only be: "No Cumulative Product Max" or "Unit Quantity - Cumulative Product Max" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'cumulativeCouponPricing',
                    'cross_field_insertBefore'    => '#cumulativeCouponPricing_0',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('yes'),
                    'cross_error_msg'             => __('"Apply this Rule Discount in Addition to Coupon Discount" must be: "Yes" , When "Automatically Add Free Product to Cart" is Selected.' , 'vtwpr')
                                                     . '<br><br>'.
                                                     __('In this case, the Rule will auto add the Free product, and the Coupon will be ignored. ' , 'vtwpr')                                    
                  ),
                  array ( 
                    'cross_field_name'            => 'action_repeat_condition',
                    'cross_field_insertBefore'    => '#action_repeat_condition_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none'),
                    'cross_error_msg'             => __('"Discount Group Repeat" must be: "No Repeats (Apply Discount to Discount Group ONCE)" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  )                         
                )                
              ),
            /*  'discount_for_the_price_of_count'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),  */         
              'discount_applies_to'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),              
              'discount_rule_max_amt_type'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'discount_rule_max_amt_count'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   =>  'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_lifetime_max_amt_type'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'discount_lifetime_max_amt_count'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_rule_cum_max_amt_type'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_rule_cum_max_amt_count'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              //not in Deal Line, used in general processing...
              'discountAppliesWhere'        	     => 'nextInActionPop', 
              'inPopAllowed'                       => 'any',   // wholeStore / groups / vargroup / single   OR  any
              'actionPopAllowed'                   => 'any', //sameAsInPop / wholeStore / groups / vargroup / single   OR  any                       
              'cumulativeRulePricingAllowed'       => 'yes',
              'cumulativeSalePricingAllowed'       => 'yes',
              'replaceSalePricingAllowed'          => 'yes',
              'cumulativeCouponPricingAllowed'     => 'yes'   
        	),
        //Cart => Buy 5/$500  get the highest/lowest get a same/discount from a different group 
        //      Buy 5/$500, get a discount on the cheapest/most expensive when next 5/$500 purchased
        'C-cheapest-Next' =>  array(             
              'buy_repeat_condition'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   =>  'all',         //'' = none are allowed, 'all' or an array
                'template_profile_error_msg'       =>  '' 
              ),
              'buy_repeat_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'buy_amt_type'  => array (
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => '' 
              ),
              'buy_amt_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => '' 
              ),              
              'buy_amt_applies_to'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),              
              'buy_amt_mod'  => array (
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),                   
              'buy_amt_mod_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'action_repeat_condition'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   =>  'all',
                'template_profile_error_msg'       =>  '' 
              ), 
              'action_repeat_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'action_amt_type'  => array (
                'required_or_optional'             => 'required',  
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'action_amt_count'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => '' 
              ),
              'action_amt_applies_to'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => '' 
              ),
              'action_amt_mod'  => array (
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),     
              'action_amt_mod_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'discount_amt_type'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => array ('percent', 'currency', 'fixedPrice', 'free'),
                'template_profile_error_msg'       =>  __('"Discount Type " - "For the price of" is not allowed for this setup type ', 'vtwpr'),
                'cross_field_edits'                =>  array (    //only there where needed!                
                  array ( 
                    'cross_field_name'            => 'discount_applies_to',
                    'cross_field_insertBefore'    => '#discount_amt_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('fixedPrice', 'free'),
                    'cross_allowed_values'        => array ('each','cheapest','most_expensive'),
                    'cross_error_msg'             => __('When "Discount Type" is "Fixed Price" or "Free", "Discount Applies To" must be "Each", "Cheapest" or "Most Expensive" ', 'vtwpr')
                  )                  
                )
              ),
              'discount_amt_count'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_auto_add_free_product'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => '',
                'cross_field_edits'                =>  array (    //only there where needed!     "Discount a Single Product Only"           
                /*  array ( 
                    'cross_field_name'            => 'buy_amt_type',
                    'cross_field_insertBefore'    => '#buy_amt_type',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'one', 'quantity'),
                    'cross_error_msg'             => __('" Product Filter Amount" may not be: "Buy $$ Value" or "Nth Quantity" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ),  */                
                  array ( 
                    'cross_field_name'            => 'popChoiceOut',
                    'cross_field_insertBefore'    => '#action_group_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('single', 'vargroup', 'sameAsInPop'),   //additional edit added to check inpop for single/single var...
                    'cross_error_msg'             => __('"Get (Discount) Group Filter" must be either a single product, or a single product variation, When "Automatically Add Free Product to Cart" is Selected.', 'vtwpr') .'<br><br>'. __('Choose either "Discount a Single Product Only", or "Discount a Single Product with Variations" and select a *Single* Variation. ', 'vtwpr') .'<br><br>'. __('Otherwise the Auto add does not know which product to add. ', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'action_amt_type',
                    'cross_field_insertBefore'    => '#action_amt_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'zero', 'one', 'quantity'),
                    'cross_error_msg'             => __('"Get (Discount) Group Amount" may not be: "Discount $$ Value" or "Nth Quantity" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'discount_rule_max_amt_type',
                    'cross_field_insertBefore'    => '#discount_rule_max_amt_type_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'quantity'),
                    'cross_error_msg'             => __('"Discount Maximum for Rule" may only be: "No Discount Rule Max" or "Maximum Number of Times" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'discount_lifetime_max_amt_type',
                    'cross_field_insertBefore'    => '#discount_lifetime_max_amt_type_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'quantity'),
                    'cross_error_msg'             => __('"Customer Rule Limit" may only be: "None" or "Max Rule Usage Count per Customer?" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'discount_rule_cum_max_amt_type',
                    'cross_field_insertBefore'    => '#discount_rule_cum_max_amt_type_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'quantity'),
                    'cross_error_msg'             => __('"Discount per Product Maximum" may only be: "No Cumulative Product Max" or "Unit Quantity - Cumulative Product Max" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'cumulativeCouponPricing',
                    'cross_field_insertBefore'    => '#cumulativeCouponPricing_0',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('yes'),
                    'cross_error_msg'             => __('"Apply this Rule Discount in Addition to Coupon Discount" must be: "Yes" , When "Automatically Add Free Product to Cart" is Selected.' , 'vtwpr')
                                                     . '<br><br>'.
                                                     __('In this case, the Rule will auto add the Free product, and the Coupon will be ignored. ' , 'vtwpr')                                    
                  ),
                  array ( 
                    'cross_field_name'            => 'action_repeat_condition',
                    'cross_field_insertBefore'    => '#action_repeat_condition_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none'),
                    'cross_error_msg'             => __('"Discount Group Repeat" must be: "No Repeats (Apply Discount to Discount Group ONCE)" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  )                         
                )                
              ),
            /*  'discount_for_the_price_of_count'  => array (
                'required_or_optional'             => '', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       =>  __('"Discount for the price of" - no values allowed for this setup type ', 'vtwpr')
              ), */          
              'discount_applies_to'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => array('cheapest', 'most_expensive'),
                'template_profile_error_msg'       => __('"Discount applies to" - only "Cheapest" or "Most Expensive" allowed for this setup type ', 'vtwpr')
              ),              
              'discount_rule_max_amt_type'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'discount_rule_max_amt_count'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   =>  'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_lifetime_max_amt_type'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'discount_lifetime_max_amt_count'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_rule_cum_max_amt_type'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_rule_cum_max_amt_count'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              //not in Deal Line, used in general processing... 
              'discountAppliesWhere'        	     => 'nextInActionPop', 
              'inPopAllowed'                       => 'any',   // wholeStore / groups / vargroup / single   OR  any
              'actionPopAllowed'                   => 'any', //sameAsInPop / wholeStore / groups / vargroup / single   OR  any                       
              'cumulativeRulePricingAllowed'       => 'yes',
              'cumulativeSalePricingAllowed'       => 'yes',
              'replaceSalePricingAllowed'          => 'yes',
              'cumulativeCouponPricingAllowed'     => 'yes' 
        	),
        //Cart => buy x/$x get every Nth purchase in a same/different group for a discount
        //     Buy 5/$500, get the following Nth at a discount
        'C-nth-Next' =>  array(             
              'buy_repeat_condition'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   =>  'all',         //'' = none are allowed, 'all' or an array
                'template_profile_error_msg'       =>  ''
              ),
              'buy_repeat_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'buy_amt_type'  => array (
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'buy_amt_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),              
              'buy_amt_applies_to'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),              
              'buy_amt_mod'  => array (
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => '' 
              ),                   
              'buy_amt_mod_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'action_repeat_condition'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   =>  'all',
                'template_profile_error_msg'       =>  '' 
              ), 
              'action_repeat_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => '' 
              ),
              'action_amt_type'  => array (
                'required_or_optional'             => 'required',  
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'action_amt_count'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'action_amt_applies_to'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => '' 
              ),
              'action_amt_mod'  => array (
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),     
              'action_amt_mod_count'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'discount_amt_type'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => array ('percent', 'currency', 'fixedPrice', 'free'),
                'template_profile_error_msg'       =>  __('"Discount Type " - "For the price of" is not allowed for this setup type ', 'vtwpr'),
                'cross_field_edits'                =>  array (    //only there where needed!                
                  array ( 
                    'cross_field_name'            => 'discount_applies_to',
                    'cross_field_insertBefore'    => '#discount_amt_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('fixedPrice', 'free'),
                    'cross_allowed_values'        => array ('each','cheapest','most_expensive'),
                    'cross_error_msg'             => __('When "Discount Type" is "Fixed Price" or "Free", "Discount Applies To" must be "Each", "Cheapest" or "Most Expensive" ', 'vtwpr')
                  )                  
                )
              ),
              'discount_amt_count'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_auto_add_free_product'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => '',
                'cross_field_edits'                =>  array (    //only there where needed!     "Discount a Single Product Only"           
                /*  array ( 
                    'cross_field_name'            => 'buy_amt_type',
                    'cross_field_insertBefore'    => '#buy_amt_type',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'one', 'quantity'),
                    'cross_error_msg'             => __('" Product Filter Amount" may not be: "Buy $$ Value" or "Nth Quantity" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ),   */               
                  array ( 
                    'cross_field_name'            => 'popChoiceOut',
                    'cross_field_insertBefore'    => '#action_group_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('single', 'vargroup', 'sameAsInPop'),   //additional edit added to check inpop for single/single var...
                    'cross_error_msg'             => __('"Get (Discount) Group Filter" must be either a single product, or a single product variation, When "Automatically Add Free Product to Cart" is Selected.', 'vtwpr') .'<br><br>'. __('Choose either "Discount a Single Product Only", or "Discount a Single Product with Variations" and select a *Single* Variation. ', 'vtwpr') .'<br><br>'. __('Otherwise the Auto add does not know which product to add. ', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'action_amt_type',
                    'cross_field_insertBefore'    => '#action_amt_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'zero', 'one', 'quantity'),
                    'cross_error_msg'             => __('"Get (Discount) Group Amount" may not be: "Discount $$ Value" or "Nth Quantity" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'discount_rule_max_amt_type',
                    'cross_field_insertBefore'    => '#discount_rule_max_amt_type_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'quantity'),
                    'cross_error_msg'             => __('"Discount Maximum for Rule" may only be: "No Discount Rule Max" or "Maximum Number of Times" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'discount_lifetime_max_amt_type',
                    'cross_field_insertBefore'    => '#discount_lifetime_max_amt_type_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'quantity'),
                    'cross_error_msg'             => __('"Customer Rule Limit" may only be: "None" or "Max Rule Usage Count per Customer?" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'discount_rule_cum_max_amt_type',
                    'cross_field_insertBefore'    => '#discount_rule_cum_max_amt_type_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none', 'quantity'),
                    'cross_error_msg'             => __('"Discount per Product Maximum" may only be: "No Cumulative Product Max" or "Unit Quantity - Cumulative Product Max" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  ),
                  array ( 
                    'cross_field_name'            => 'cumulativeCouponPricing',
                    'cross_field_insertBefore'    => '#cumulativeCouponPricing_0',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('yes'),
                    'cross_error_msg'             => __('"Apply this Rule Discount in Addition to Coupon Discount" must be: "Yes" , When "Automatically Add Free Product to Cart" is Selected.' , 'vtwpr')
                                                     . '<br><br>'.
                                                     __('In this case, the Rule will auto add the Free product, and the Coupon will be ignored. ' , 'vtwpr')                                    
                  ),
                  array ( 
                    'cross_field_name'            => 'action_repeat_condition',
                    'cross_field_insertBefore'    => '#action_repeat_condition_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('yes'),
                    'cross_allowed_values'        => array ('none'),
                    'cross_error_msg'             => __('"Discount Group Repeat" must be: "No Repeats (Apply Discount to Discount Group ONCE)" , When "Automatically Add Free Product to Cart" is Selected', 'vtwpr')
                  )                         
                )                
              ),
           /*   'discount_for_the_price_of_count'  => array (
                'required_or_optional'             => '', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  __('"Discount for the price of" - no values allowed for this setup type ', 'vtwpr')
              ), */          
              'discount_applies_to'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),              
              'discount_rule_max_amt_type'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'discount_rule_max_amt_count'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   =>  'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_lifetime_max_amt_type'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ),
              'discount_lifetime_max_amt_count'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_rule_cum_max_amt_type'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_rule_cum_max_amt_count'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              //not in Deal Line, used in general processing... 
              'discountAppliesWhere'        	     => 'nextInActionPop', 
              'inPopAllowed'                       => 'any',   // wholeStore / groups / vargroup / single   OR  any
              'actionPopAllowed'                   => 'any', //sameAsInPop / wholeStore / groups / vargroup / single   OR  any                       
              'cumulativeRulePricingAllowed'       => 'yes',
              'cumulativeSalePricingAllowed'       => 'yes',
              'replaceSalePricingAllowed'          => 'yes',
              'cumulativeCouponPricingAllowed'     => 'yes' 
        	),
          
           
        //DISPLAY Templates  =>  very limited, as applied at catalog display time.
        //'D-storeWideSale' - Display => simple percentage/value off, Whole Store - FREE  
        //        Store-Wide Sale with a Percentage or $$ Value Off, at Catalog Display Time
        'D-storeWideSale' =>   array(      
              'buy_repeat_condition'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   =>  array('none'),     //'' = none are allowed, 'all' or an array
                'template_profile_error_msg'       =>  __('"Buy Repeat" - only "Apply the Rule Once (No Repeats)" is allowed for this setup type ', 'vtwpr')
              ),
              'buy_repeat_count'  => array (
                'required_or_optional'             => '', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       =>  __('"Rule repeat amount" - no values allowed for this setup type ', 'vtwpr')
              ),
              'buy_amt_type'  => array (
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => array('none'),
                'template_profile_error_msg'       =>  __('"Buy group amount" - only "No buy condition" is allowed for this setup type ', 'vtwpr')
              ),
              'buy_amt_count'  => array (
                'required_or_optional'             => '', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       =>  __('"Buy pool amount" - no values allowed for this setup type ', 'vtwpr')
              ),              
              'buy_amt_applies_to'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       => ''
              ), 
            /*  'buy_amt_applies_to'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => array('all'),
                'template_profile_error_msg'       =>  __('"Buy pool amount applies to" - only "All buy pool products" is allowed for this setup type ', 'vtwpr')
              ), */               
              'buy_amt_mod'  => array (
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => array('none'),
                'template_profile_error_msg'       =>  __('"Buy group min / max" - only "No Discount Group Amount Min / Max " is allowed for this setup type ', 'vtwpr')
              ),                   
              'buy_amt_mod_count'  => array (
                'required_or_optional'             => '', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       =>  __('"Buy group min / max amount" - no values allowed for this setup type ', 'vtwpr')
              ),
              'action_repeat_condition'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   =>  array('none'),
                'template_profile_error_msg'       =>  __('"Get repeat condition" - only "Apply the Get Pool Once (No Repeats)" is allowed for this setup type ', 'vtwpr')
              ), 
              'action_repeat_count'  => array (
                'required_or_optional'             => '', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       =>  __('"Get repeat amount" - no values allowed for this setup type ', 'vtwpr')
              ),
              'action_amt_type'  => array (
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => array('none'),
                'template_profile_error_msg'       =>  __('"Get group amount" - only "Discount Each Unit" is allowed for this setup type ', 'vtwpr')
              ),
              'action_amt_count'  => array (
                'required_or_optional'             => '', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       =>  __('"Get pool amount" - no values allowed for this setup type ', 'vtwpr')
              ),
              'action_amt_applies_to'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => array('all'),
                'template_profile_error_msg'       =>  __('"Get pool amount applies to" - only "All action pool products" is allowed for this setup type ', 'vtwpr')
              ),
              'action_amt_mod'  => array (
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => array('none'),
                'template_profile_error_msg'       =>  __('"Get group min/mx - only "No Discount Group Amount Min / Max " is allowed for this setup type ', 'vtwpr')
              ),     
              'action_amt_mod_count'  => array (
                'required_or_optional'             => '', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       =>  __('"Get pool amount amount" - no values allowed for this setup type ', 'vtwpr')
              ),
              'discount_amt_type'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => array ('percent', 'currency', 'fixedPrice', 'free'),
                'template_profile_error_msg'       =>  __('"Discount Type " - "For the price of" is not allowed for this setup type ', 'vtwpr'),
                'cross_field_edits'                =>  array (    //only there where needed!                
                  array ( 
                    'cross_field_name'            => 'discount_applies_to',
                    'cross_field_insertBefore'    => '#discount_amt_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('fixedPrice', 'free'),
                    'cross_allowed_values'        => array ('each','cheapest','most_expensive'),
                    'cross_error_msg'             => __('When "Discount Type" is "Fixed Price" or "Free", "Discount Applies To" must be "Each", "Cheapest" or "Most Expensive" ', 'vtwpr')
                  )                  
                )
              ),
              'discount_amt_count'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_auto_add_free_product'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       => __('"Automatically Add Free Product to Cart" not allowed for this deal type', 'vtwpr')
              ),
          /*    'discount_for_the_price_of_count'  => array (
                'required_or_optional'             => '', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       =>  __('"Discount for the price of" - no values allowed for this setup type ', 'vtwpr')
              ),   */        
              'discount_applies_to'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => array('each'),
                'template_profile_error_msg'       =>  __('"Discount applies to" - "each product" only allowed for this setup type ', 'vtwpr')
              ),              
            //  'discount_applies_to_nth_count'      => '',
              'discount_rule_max_amt_type'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_rule_max_amt_count'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_lifetime_max_amt_type'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_lifetime_max_amt_count'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_rule_cum_max_amt_type'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_rule_cum_max_amt_count'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              //not in Deal Line, used in general processing... 
              'discountAppliesWhere'        	     => 'allActionPop', 
              'inPopAllowed'                       => 'wholeStore',   // wholeStore / groups / vargroup / single   OR  any
              'actionPopAllowed'                   => 'sameAsInPop', //sameAsInPop / wholeStore / groups / vargroup / single   OR  any                    
              'cumulativeRulePricingAllowed'       => 'yes',
              'cumulativeSalePricingAllowed'       => 'yes',
              'replaceSalePricingAllowed'          => 'yes',
              'cumulativeCouponPricingAllowed'     => 'yes'  
        	),                        
        
        //'D-simpleDiscount' - Display => simple percentage/value off, *by inPop* , ==>>>  applies to the whole 'D00-N' series 
        //      Population Discount in the Buy Pool Group, at Catalog Display Time
        'D-simpleDiscount' =>   array(        
              'buy_repeat_condition'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   =>  array('none'),  //'' = none are allowed, 'all' or an array
                'template_profile_error_msg'       =>  __('"Rule repeat condition" - only "Just a Single Rule Usage" is allowed for this setup type ', 'vtwpr')
              ),
              'buy_repeat_count'  => array (
                'required_or_optional'             => '', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       =>  __('"Rule repeat amount" - no values allowed for this setup type ', 'vtwpr')
              ),
              'buy_amt_type'  => array (
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => array('none'),
                'template_profile_error_msg'       =>  __('"Buy group amount" - only "No buy condition" is allowed for this setup type ', 'vtwpr')
              ),
              'buy_amt_count'  => array (
                'required_or_optional'             => '', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       =>  __('"Buy pool amount" - no values allowed for this setup type ', 'vtwpr')
              ),              
              'buy_amt_applies_to'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => array('each'),
                'template_profile_error_msg'       =>  __('"Buy pool amount applies to" - only "Each buy pool product" is allowed for this setup type ', 'vtwpr')
              ),              
              'buy_amt_mod'  => array (
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => array('none'),
                'template_profile_error_msg'       =>  __('"Buy group min / max" - only "No Discount Group Amount Min / Max " is allowed for this setup type ', 'vtwpr')
              ),                   
              'buy_amt_mod_count'  => array (
                'required_or_optional'             => '', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       =>  __('"Buy group min / max amount" - no values allowed for this setup type ', 'vtwpr')
              ),
              'action_repeat_condition'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   =>  array('none'),
                'template_profile_error_msg'       =>  __('"Get repeat condition" - only "Apply the Get Pool Once (No Repeats)" is allowed for this setup type ', 'vtwpr')
              ), 
              'action_repeat_count'  => array (
                'required_or_optional'             => '', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       =>  __('"Get repeat amount" - no values allowed for this setup type ', 'vtwpr')
              ),
              'action_amt_type'  => array (
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => array('none'),
                'template_profile_error_msg'       =>  __('"Get group amount" - only "Discount Each Unit" is allowed for this setup type ', 'vtwpr')
              ),
              'action_amt_count'  => array (
                'required_or_optional'             => '', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       =>  __('"Get pool amount" - no values allowed for this setup type ', 'vtwpr')
              ),
              'action_amt_applies_to'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => array('all'),
                'template_profile_error_msg'       =>  __('"Get pool amount applies to" - only "All action pool products" is allowed for this setup type ', 'vtwpr')
              ),
              'action_amt_mod'  => array (
                'required_or_optional'             => 'optional',  
                'allowed_values'                   => array('none'),
                'template_profile_error_msg'       =>  __('"Get group min/mx - only "No Discount Group Amount Min / Max " is allowed for this setup type ', 'vtwpr')
              ),     
              'action_amt_mod_count'  => array (
                'required_or_optional'             => '', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       =>  __('"Get pool amount amount" - no values allowed for this setup type ', 'vtwpr')
              ),
              'discount_amt_type'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => array ('percent', 'currency', 'fixedPrice', 'free'),
                'template_profile_error_msg'       =>  __('"Discount Type " - "For the price of" is not allowed for this setup type ', 'vtwpr'),
                'cross_field_edits'                =>  array (    //only there where needed!                
                  array ( 
                    'cross_field_name'            => 'discount_applies_to',
                    'cross_field_insertBefore'    => '#discount_amt_box',
                    'cross_required_or_optional'  => 'required', 
                    'applies_to_this_field_values'  => array ('fixedPrice', 'free'),
                    'cross_allowed_values'        => array ('each','cheapest','most_expensive'),
                    'cross_error_msg'             => __('When "Discount Type" is "Fixed Price" or "Free", "Discount Applies To" must be "Each", "Cheapest" or "Most Expensive" ', 'vtwpr')
                  )                  
                )
              ),
              'discount_amt_count'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_auto_add_free_product'  => array (
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       => __('"Automatically Add Free Product to Cart" not allowed for this deal type', 'vtwpr')
              ),
          /*    'discount_for_the_price_of_count'  => array (
                'required_or_optional'             => '', 
                'allowed_values'                   => '',
                'template_profile_error_msg'       =>  __('"Discount for the price of" - no values allowed for this setup type ', 'vtwpr')
              ),  */         
              'discount_applies_to'  => array (
                'required_or_optional'             => 'required', 
                'allowed_values'                   => array('each'),
                'template_profile_error_msg'       =>  __('"Discount applies to" - "each product" only allowed for this setup type ', 'vtwpr')
              ),              
            //  'discount_applies_to_nth_count'      => '',
              'discount_rule_max_amt_type'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'required', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_rule_max_amt_count'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_lifetime_max_amt_type'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_lifetime_max_amt_count'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_rule_cum_max_amt_type'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              'discount_rule_cum_max_amt_count'  => array (              //only used in 1st iteration
                'required_or_optional'             => 'optional', 
                'allowed_values'                   => 'all',
                'template_profile_error_msg'       =>  ''
              ),
              //not in Deal Line, used in general processing...
              'discountAppliesWhere'        	     => 'allActionPop', 
              'inPopAllowed'                       => 'any',   // wholeStore / groups / vargroup / single   OR  any
              'actionPopAllowed'                   => 'sameAsInPop', //sameAsInPop / wholeStore / groups / vargroup / single   OR  any 
              'cumulativeRulePricingAllowed'       => 'yes',
              'cumulativeSalePricingAllowed'       => 'yes',
              'replaceSalePricingAllowed'          => 'yes',
              'cumulativeCouponPricingAllowed'     => 'yes' 
        	)                   
                         
    ); //end vtwpr_rule_edits_framework        
   
    $vtwpr_deal_edits_framework = array(            
      'buy_repeat_condition'  => array (
            'edit_is_active'                =>  '',
            'required_or_optional'          =>  '',
            'field_type'                    =>  'dropdown',   //dropdown / amt / text [last 2 are enterable]
            'matching_dropdown_label'       =>  '', // empty = no matching dropdown
            'matching_dropdown_label_id'    =>  '', // empty = no matching dropdown
            'insert_error_before_selector'  =>  '#buy_repeat_box',
            'field_label'                   =>  __('Rule Usage Count', 'vtwpr'),
            'allowed_values'                =>  '',
            'template_profile_error_msg'    =>  '',
            'cross_field_edits'             =>  array(    //only there where needed!
               /*     FOR EXAMPLE                         
                  array ( 
                    'cross_field_name'              => 'discount_applies_to',
                    'cross_field_insertBefore'    => '#discount_applies_to',
                    'cross_required_or_optional'    => 'required', 
                    'applies_to_this_field_values'  => array ('forThePriceOf_Units', 'forThePriceOf_Currency'),
                    'cross_allowed_values'          => 'all'   //'', 'all' or an array
                    'cross_error_msg'               => __('When "For the price of" is selected, "Discount Applies To" must be "all" ', 'vtwpr'),
                  )                
                */
            )
          ), 
      'buy_repeat_count'  => array (
            'edit_is_active'                =>  '',
            'required_or_optional'          =>  '',
            'field_type'                    =>  'amt',   //dropdown / amt / text [last 2 are enterable]
            'matching_dropdown_label'       =>  __('Buy Repeat Type', 'vtwpr'), // empty = no matching dropdown
            'matching_dropdown_label_id'    =>  '#buy_repeat_title_anchor', // empty = no matching dropdown
            'insert_error_before_selector'  =>  '#buy_repeat_box',
            'field_label'                   =>  __('Rule Usage Count', 'vtwpr'),
            'allowed_values'                =>  '',
            'template_profile_error_msg'    =>  '',
            'cross_field_edits'             =>  array()
          ),                  
      'buy_amt_type'  => array (
            'edit_is_active'                =>  '',        
            'required_or_optional'          =>  '',
            'field_type'                    =>  'dropdown',   //dropdown / amt / text [last 2 are enterable]
            'matching_dropdown_label'       =>  '', // empty = no matching dropdown
            'matching_dropdown_label_id'    =>  '', // empty = no matching dropdown
            'insert_error_before_selector'  =>  '#buy_amt_box',
            'field_label'                   =>  __('Group Amount Type', 'vtwpr'),
            'allowed_values'                =>  '',
            'template_profile_error_msg'    =>  'error1',  //mwnteest
            'cross_field_edits'             =>  array()
          ), 
      'buy_amt_count'  => array (
            'edit_is_active'                =>  '',            
            'required_or_optional'          =>  '',
            'field_type'                    =>  'amt',   //dropdown / amt / text [last 2 are enterable]
            'matching_dropdown_label'       =>  __('Group Amount Type', 'vtwpr'), // empty = no matching dropdown
            'matching_dropdown_label_id'    =>  '#buy_amt_title_anchor', // empty = no matching dropdown
            'insert_error_before_selector'  =>  '#buy_amt_box',
            'field_label'                   =>  __('Group Amount Count', 'vtwpr'),
            'allowed_values'                =>  '',
            'template_profile_error_msg'    =>  'error2',
            'cross_field_edits'             =>  array()
          ),
      'buy_amt_applies_to'  => array (
            'edit_is_active'                =>  '',            
            'required_or_optional'          =>  '',
            'field_type'                    =>  'dropdown',   //dropdown / amt / text [last 2 are enterable]
            'matching_dropdown_label'       =>  '', // empty = no matching dropdown
            'matching_dropdown_label_id'    =>  '', // empty = no matching dropdown
            'insert_error_before_selector'  =>  '#buy_amt_box',
            'field_label'                   =>  __('Group Amount Applies To', 'vtwpr'),
            'allowed_values'                =>  '',
            'template_profile_error_msg'    =>  'error3',
            'cross_field_edits'             =>  array()
          ),          
      'buy_amt_mod'  => array (
            'edit_is_active'                =>  '',
            'required_or_optional'          =>  '',
            'field_type'                    =>  'dropdown',   //dropdown / amt / text [last 2 are enterable]
            'matching_dropdown_label'       =>  '', // empty = no matching dropdown
            'matching_dropdown_label_id'    =>  '', // empty = no matching dropdown
            'insert_error_before_selector'  =>  '#buy_amt_mod_box',
            'field_label'                   =>  __('Group Amount Min/Max', 'vtwpr'),
            'allowed_values'                =>  '',
            'template_profile_error_msg'    =>  '',
            'cross_field_edits'             =>  array()
          ), 
      'buy_amt_mod_count'  => array (            
            'edit_is_active'                =>  '',         
            'required_or_optional'          =>  '',
            'field_type'                    =>  'amt',   //dropdown / amt / text [last 2 are enterable]
            'matching_dropdown_label'       =>  'Group Amount Type', // empty = no matching dropdown
            'matching_dropdown_label_id'    =>  '#buy_amt_mod_title_anchor', // empty = no matching dropdown
            'insert_error_before_selector'  =>  '#buy_amt_mod_box',
            'field_label'                   =>  __('Group Amount Min/Max Count', 'vtwpr'),
            'allowed_values'                =>  '',
            'template_profile_error_msg'    =>  '',
            'cross_field_edits'             =>  array()
          ),  
      'action_repeat_condition'  => array (
            'edit_is_active'                =>  '',
            'required_or_optional'          =>  '',
            'field_type'                    =>  'dropdown',   //dropdown / amt / text [last 2 are enterable]
            'matching_dropdown_label'       =>  '', // empty = no matching dropdown
            'matching_dropdown_label_id'    =>  '', // empty = no matching dropdown
            'insert_error_before_selector'  =>  '#action_repeat_condition_box',
            'field_label'                   =>  __('Group Repeat', 'vtwpr'),
            'allowed_values'                =>  '',
            'template_profile_error_msg'    =>  '',
            'cross_field_edits'             =>  array()
          ), 
      'action_repeat_count'  => array (
            'edit_is_active'                =>  '',
            'required_or_optional'          =>  '',
            'field_type'                    =>  'amt',   //dropdown / amt / text [last 2 are enterable]
            'matching_dropdown_label'       =>  __('Group Repeat Type', 'vtwpr'), // empty = no matching dropdown
            'matching_dropdown_label_id'    =>  '#action_repeat_condition_title_anchor', // empty = no matching dropdown
            'insert_error_before_selector'  =>  '#action_repeat_condition_box',
            'field_label'                   =>  __('Group Repeat Count', 'vtwpr'),
            'allowed_values'                =>  '',
            'template_profile_error_msg'    =>  '',
            'cross_field_edits'             =>  array()
          ), 
      'action_amt_type'  => array (
            'edit_is_active'                =>  '',
            'required_or_optional'          =>  '',
            'field_type'                    =>  'dropdown',   //dropdown / amt / text [last 2 are enterable]
            'matching_dropdown_label'       =>  '', // empty = no matching dropdown
            'matching_dropdown_label_id'    =>  '', // empty = no matching dropdown
            'insert_error_before_selector'  =>  '#action_amt_box',
            'field_label'                   =>  __('Group Amount Type', 'vtwpr'),
            'allowed_values'                =>  '',
            'template_profile_error_msg'    =>  '',
            'cross_field_edits'             =>  array()
          ), 
      'action_amt_count'  => array (
            'edit_is_active'                =>  '',
            'required_or_optional'          =>  '',
            'field_type'                    =>  'amt',   //dropdown / amt / text [last 2 are enterable]
            'matching_dropdown_label'       =>  __('Group Amount Type', 'vtwpr'), // empty = no matching dropdown
            'matching_dropdown_label_id'    =>  '#action_amt_title_anchor', // empty = no matching dropdown
            'insert_error_before_selector'  =>  '#action_amt_box',
            'field_label'                   =>  __('Group Amount Type Count', 'vtwpr'),
            'allowed_values'                =>  '',
            'template_profile_error_msg'    =>  '',
            'cross_field_edits'             =>  array()
          ),
      'action_amt_applies_to'  => array (
            'edit_is_active'                =>  '',            
            'required_or_optional'          =>  '',
            'field_type'                    =>  'dropdown',   //dropdown / amt / text [last 2 are enterable]
            'matching_dropdown_label'       =>  '', // empty = no matching dropdown
            'matching_dropdown_label_id'    =>  '', // empty = no matching dropdown
            'insert_error_before_selector'  =>  '#action_amt_box',
            'field_label'                   =>  __('Group Amount Applies To', 'vtwpr'),
            'allowed_values'                =>  '',
            'template_profile_error_msg'    =>  '',
            'cross_field_edits'             =>  array()
          ),
      'action_amt_mod'  => array (
            'edit_is_active'                =>  '',
            'required_or_optional'          =>  '',
            'field_type'                    =>  'dropdown',   //dropdown / amt / text [last 2 are enterable]
            'matching_dropdown_label'       =>  '', // empty = no matching dropdown
            'matching_dropdown_label_id'    =>  '', // empty = no matching dropdown
            'insert_error_before_selector'  =>  '#action_amt_mod_box',
            'field_label'                   =>  __('Group Amount Min/Max', 'vtwpr'),
            'allowed_values'                =>  '',
            'template_profile_error_msg'    =>  '',
            'cross_field_edits'             =>  array()
          ), 
      'action_amt_mod_count'  => array (            
            'edit_is_active'                =>  '',         
            'required_or_optional'          =>  '',
            'field_type'                    =>  'amt',   //dropdown / amt / text [last 2 are enterable]
            'matching_dropdown_label'       =>  __('Group Amount Type', 'vtwpr'), // empty = no matching dropdown
            'matching_dropdown_label_id'    =>  '#action_amt_mod_title_anchor', // empty = no matching dropdown
            'insert_error_before_selector'  =>  '#action_amt_mod_box',
            'field_label'                   =>  __(' Group Amount Min/Max Count', 'vtwpr'),
            'allowed_values'                =>  '',
            'template_profile_error_msg'    =>  '',
            'cross_field_edits'             =>  array()
          ),                        
      'discount_amt_type'  => array (
            'edit_is_active'                =>  '',
            'required_or_optional'          =>  '',
            'field_type'                    =>  'dropdown',   //dropdown / amt / text [last 2 are enterable]
            'matching_dropdown_label'       =>  '', // empty = no matching dropdown
            'matching_dropdown_label_id'    =>  '', // empty = no matching dropdown
            'insert_error_before_selector'  =>  '#discount_amt_box',
            'field_label'                   =>  __('Discount Amount Type', 'vtwpr'),
            'allowed_values'                =>  '',
            'template_profile_error_msg'    =>  '',
            'cross_field_edits'             =>  array()
          ), 
      'discount_amt_count' => array (
            'edit_is_active'                =>  '',
            'required_or_optional'          =>  '',
            'field_type'                    =>  'amt',   //dropdown / amt / text [last 2 are enterable]
            'matching_dropdown_label'       =>  __('Discount Type', 'vtwpr'), // empty = no matching dropdown
            'matching_dropdown_label_id'    =>  '#discount_action_title_anchor', // empty = no matching dropdown
            'insert_error_before_selector'  =>  '#discount_amt_box',
            'field_label'                   =>  __('Discount Amount Count', 'vtwpr'),
            'allowed_values'                =>  '',
            'template_profile_error_msg'    =>  '',
            'cross_field_edits'             =>  array()
          ),
       'discount_auto_add_free_product' => array (
            'edit_is_active'                =>  '',
            'required_or_optional'          =>  '',
            'field_type'                    =>  'text', //(really checkbox...)   //dropdown / amt / text [last 2 are enterable]
            'matching_dropdown_label'       =>  __('Discount Type', 'vtwpr'), // empty = no matching dropdown
            'matching_dropdown_label_id'    =>  '#discount_action_title_anchor', // empty = no matching dropdown
            'insert_error_before_selector'  =>  '#discount_amt_box',
            'field_label'                   =>  __('Automatically Add Free Product to Cart', 'vtwpr') ,
            'allowed_values'                =>  '',
            'template_profile_error_msg'    =>  '',
            'cross_field_edits'             =>  array()
          ),         
   /*   'discount_for_the_price_of_count' => array (
            'edit_is_active'                =>  '',
            'required_or_optional'          =>  '',
            'field_type'                    =>  'amt',   //dropdown / amt / text [last 2 are enterable]
            'matching_dropdown_label'       =>  'Discount Type', // empty = no matching dropdown
            'insert_error_before_selector'  =>  '#discount_amt_type_dropdown',
            'field_label'                   =>  'For the Price Of',
            'allowed_values'                =>  '',
            'template_profile_error_msg'    =>  '',
            'cross_field_edits'             =>  array()
          ), */         
      'discount_applies_to'  => array (
            'edit_is_active'                =>  '',
            'required_or_optional'          =>  '',
            'field_type'                    =>  'dropdown',   //dropdown / amt / text [last 2 are enterable]
            'matching_dropdown_label'       =>  '', // empty = no matching dropdown
            'matching_dropdown_label_id'    =>  '', // empty = no matching dropdown
            'insert_error_before_selector'  =>  '#discount_applies_to_box',
            'field_label'                   =>  __('Discount Applies To', 'vtwpr'),
            'allowed_values'                =>  '',
            'template_profile_error_msg'    =>  '',
            'cross_field_edits'             =>  array()
          ), 
    /*  'discount_applies_to_nth_count'  => array (
            'edit_is_active'                =>  '',
            'required_or_optional'          =>  '',
            'field_type'                    =>  'amt',   //dropdown / amt / text [last 2 are enterable]
            'matching_dropdown_label'       =>  'Discount Applies to which Group', // empty = no matching dropdown
            'insert_error_before_selector'  =>  '#rule_deal_info_line',
            'field_label'                   =>  'Discount Applies to Nth Count: ',
            'allowed_values'                =>  '',
            'template_profile_error_msg'    =>  '',
            'cross_field_edits'             =>  array()
          ), */
      'discount_rule_max_amt_type'  => array (
            'edit_is_active'                =>  '',
            'required_or_optional'          =>  '',
            'field_type'                    =>  'dropdown',   //dropdown / amt / text [last 2 are enterable]
            'matching_dropdown_label'       =>  '', // empty = no matching dropdown
            'matching_dropdown_label_id'    =>  '', // empty = no matching dropdown
            'insert_error_before_selector'  =>  '#discount_rule_max_amt_type_box',
            'field_label'                   =>  __('Maximum Rule Discount Type', 'vtwpr'),
            'allowed_values'                =>  '',
            'template_profile_error_msg'    =>  '',
            'cross_field_edits'             =>  array()
          ), 
      'discount_rule_max_amt_count'  => array (
            'edit_is_active'                =>  '',
            'required_or_optional'          =>  '',
            'field_type'                    =>  'amt',   //dropdown / amt / text [last 2 are enterable]
            'matching_dropdown_label'       =>  __('Discount Maximum for Rule Type', 'vtwpr'), // empty = no matching dropdown
            'matching_dropdown_label_id'    =>  '#discount_rule_max_title_anchor', // empty = no matching dropdown
            'insert_error_before_selector'  =>  '#discount_rule_max_amt_type_box',
            'field_label'                   =>  __('Maximum Rule Discount Count ', 'vtwpr'),
            'allowed_values'                =>  '',
            'template_profile_error_msg'    =>  '',
            'cross_field_edits'             =>  array()
          ),
      'discount_lifetime_max_amt_type'  => array (
            'edit_is_active'                =>  '',
            'required_or_optional'          =>  '',
            'field_type'                    =>  'dropdown',   //dropdown / amt / text [last 2 are enterable]
            'matching_dropdown_label'       =>  '', // empty = no matching dropdown
            'matching_dropdown_label_id'    =>  '', // empty = no matching dropdown
            'insert_error_before_selector'  =>  '#discount_lifetime_max_amt_type_box',
            'field_label'                   =>  __('Maximum Lifetime Discount Type', 'vtwpr'),
            'allowed_values'                =>  '',
            'template_profile_error_msg'    =>  '',
            'cross_field_edits'             =>  array()
          ), 
      'discount_lifetime_max_amt_count'  => array (
            'edit_is_active'                =>  '',
            'required_or_optional'          =>  '',
            'field_type'                    =>  'amt',   //dropdown / amt / text [last 2 are enterable]
            'matching_dropdown_label'       =>  __('Discount Maximum for Lifetime Type', 'vtwpr'), // empty = no matching dropdown
            'matching_dropdown_label_id'    =>  '#discount_lifetime_max_title_anchor', // empty = no matching dropdown
            'insert_error_before_selector'  =>  '#discount_lifetime_max_amt_type_box',
            'field_label'                   =>  __('Maximum Lifetime Discount Count ', 'vtwpr'),
            'allowed_values'                =>  '',
            'template_profile_error_msg'    =>  '',
            'cross_field_edits'             =>  array()
          ),
      'discount_rule_cum_max_amt_type'  => array (
            'edit_is_active'                =>  '',
            'required_or_optional'          =>  '',
            'field_type'                    =>  'dropdown',   //dropdown / amt / text [last 2 are enterable]
            'matching_dropdown_label'       =>  '', // empty = no matching dropdown
            'matching_dropdown_label_id'    =>  '', // empty = no matching dropdown
            'insert_error_before_selector'  =>  '#discount_rule_cum_max_amt_type_box',
            'field_label'                   =>  __('Maximum Rule Cumulative Discount Type', 'vtwpr'),
            'allowed_values'                =>  '',
            'template_profile_error_msg'    =>  '',
            'cross_field_edits'             =>  array()
          ), 
      'discount_rule_cum_max_amt_count'  => array (
            'edit_is_active'                =>  '',
            'required_or_optional'          =>  '',
            'field_type'                    =>  'amt',   //dropdown / amt / text [last 2 are enterable]
            'matching_dropdown_label'       =>  __('Discount Maximum for Rule Type', 'vtwpr'), // empty = no matching dropdown
            'matching_dropdown_label_id'    =>  '#discount_rule_cum_max_title', // empty = no matching dropdown
            'insert_error_before_selector'  =>  '#discount_rule_cum_max_amt_type_box',
            'field_label'                   =>  __('Maximum Rule Cumulative Discount Count ', 'vtwpr'),
            'allowed_values'                =>  '',
            'template_profile_error_msg'    =>  '',
            'cross_field_edits'             =>  array()
          ),
    ); //end vtwpr_deal_edits_framework                                                 
    /*
    occurrence of rule deal info
    *********************************************************************
    'select id', 'select name', 'options id' 
        all get an occurrence number appended in the rule-ui.php **and** rules-update.php
    *********************************************************************
    */  
    $vtwpr_deal_structure_framework = array (  
             //discount occurs 5 times

          'buy_repeat_condition'             => '',
          'buy_repeat_count'                 => '',
          'buy_amt_type'                     => '',
          'buy_amt_count'                    => '',
          'buy_amt_applies_to'               => '',
          'buy_amt_mod'                      => '',     
          'buy_amt_mod_count'                => '',
          'action_repeat_condition'          => '', 
          'action_repeat_count'              => '',
          'action_amt_type'                  => '',
          'action_amt_count'                 => '',
          'action_amt_applies_to'            => '',
          'action_amt_mod'                   => '',     
          'action_amt_mod_count'             => '',
          'discount_amt_type'                => '',
          'discount_amt_count'               => '',
          'discount_auto_add_free_product'   => '',
      /*    'discount_for_the_price_of_count'  => '',   */        
          'discount_applies_to'              => '',           
         // 'discount_applies_to_nth_count'    => '',
          'discount_rule_max_amt_type'       => '',  //only used in 1st iteration
          'discount_rule_max_amt_count'      => '',  //only used in 1st iteration
          'discount_lifetime_max_amt_type'   => '',  //only used in 1st iteration
          'discount_lifetime_max_amt_count'  => '',  //only used in 1st iteration
          'discount_rule_cum_max_amt_type'   => '',  //only used in 1st iteration
          'discount_rule_cum_max_amt_count'  => ''  //only used in 1st iteration           
         
          //used during update processing
//          'active_field_count'    => ''
                             
    ); //end $vtwpr_deal_structure_framework


    $vtwpr_deal_screen_framework = array (  
             //discount occurs 5 times
          'buy_repeat_condition' => array (  // none / count / next (next group) / all (all qty/$$ remaining / none ) / or (if qty/$$ reached, this supersedes previous condition)
              //dropdown select info
              'select'    => array(
                    'id'    => 'buy_repeat_condition',
                    'class' => 'buy_repeat_condition',
                    'name'  => 'buy_repeat_condition',
                    'tabindex'  => ''
                ),
              //dropdown options
              'option'  => array(
                  array (          //needs to default to NONE
                    'id'       => 'buy_repeat_condition_heading',
                    'class'    => 'buy_repeat_condition_options',
                    'value'    => '0',
                    'title'    => __(' - Rule Usage Count Type - ', 'vtwpr')    
                   ),   
                  array (
                    'id'       => 'buy_repeat_condition_none',
                    'class'    => 'buy_repeat_condition_none',
                    'value'    => 'none',
                    'title'    => __('Apply Rule Once per Cart', 'vtwpr'),   //only applies once
                    'title-catalog'   => __('Unlimited, Apply to All in Catalog Filter Group', 'vtwpr')
           //         'title2'   => __('Apply Rule Once per Cart', 'vtwpr')  
          //          'title'    => __('No Buy Repeats', 'vtwpr') .'&nbsp;&nbsp;'. __('(Apply Rule Once)', 'vtwpr'),   //only applies once
          //          'title2'   => __('No Discount Repeats', 'vtwpr') .'&nbsp;&nbsp;'. __('(Apply Rule Once)', 'vtwpr') 
                   ),
                  array (
                    'id'       => 'buy_repeat_condition_unlimited',
                    'class'    => 'buy_repeat_condition_unlimited',
                    'value'    => 'unlimited',
                    'title'    => __('Unlimited Rule Usage per Cart ', 'vtwpr')  . '&nbsp;&nbsp;'
                   ),                                     
                   array (
                    'id'       => 'buy_repeat_condition_count',
                    'class'    => 'buy_repeat_condition_count',
                    'value'    => 'count',
                    'title'    => __('Rule Usage Times, per Cart ', 'vtwpr')    //if count is selected, buy_repeat_count is unprotected => otherwise reverts to protected
                   )  /*,
                      
                      //**************************************************
                      //these others are future options only, must be greyed out on display
                      //**************************************************
                array (
                    'id'       => 'buy_repeat_condition_next',
                    'class'    => 'buy_repeat_condition_options',
                    'value'    => 'next',
                    'title'    => __('Next ', 'vtwpr')
                   ),
                  array (
                    'id'       => 'buy_repeat_condition_all',
                    'class'    => 'buy_repeat_condition_options',
                    'value'    => 'all',
                    'title'    => __('All (all remaining) ', 'vtwpr') 
                   ),                                                          
                  array (               
                    'id'       => 'buy_repeat_condition_or',
                    'class'    => 'buy_repeat_condition_options',
                    'value'    => 'or',
                    'title'    => __('Or (ascending table) ', 'vtwpr')     
                   )  */
                 )
            ),
        //  'buy_repeat_condition'    => '',
          'buy_repeat_count'    => array (    //times to be repeated ==>> repeat_condition must be 'count'.  if not, this is protected and greyed out...
            'id'    => 'buy_repeat_count',
            'class'  => 'amt  buy_repeat_count',
            'type'  => 'text',
            'name'  => 'buy_repeat_count'     
            ),
          
                //buy 5 get 5% off, buy next 5 get 10% off
                //buy 5 get 5% off, buy any more get 10% off
                //buy 5 get 5% off, or buy 10 get 10% off
                          
          'buy_amt_type' => array (  // none / count / next (next group) / all (all qty/$$ remaining / none ) / or (if qty/$$ reached, this supersedes previous condition)
              //dropdown select info
              'select'    => array(
                    'id'    => 'buy_amt_type',
                    'class' => 'buy_amt_type clear-both',
                    'name'  => 'buy_amt_type',
                    'tabindex'  => ''
                ),
              //dropdown options
              'option'  => array(
                  array (
                    'id'       => 'buy_amt_type_heading',
                    'class'    => 'buy_amt_type_options',
                    'value'    => '0',
                    'title'    => __(' - Group Amount Type - ', 'vtwpr')     //if count is selected, buy_repeat_count is unprotected => otherwise reverts to protected
                   ),  
                   array (
                    'id'       => 'buy_amt_type_none',
                    'class'    => 'buy_amt_type_none',
                    'value'    => 'none',
                    'title'    => __('Buy Each Unit', 'vtwpr'),   //Buy Each Unit
                    'title-catalog'   => __('Each Product in Catalog Filter Group', 'vtwpr')
                //   'title2'   => __('Discount Each Unit', 'vtwpr')     
                   ),   
                  /* NOT NECESSARY!!! - For the Buy Amt, Quantity handles this!!!!!!!!!!!
                    HOWEVER, rest of the logic in the system left intact for the moment
                   array (
                    'id'       => 'buy_amt_type_one',
                    'class'    => 'buy_amt_type_one',
                    'value'    => 'one',
                    'title'    => __('Buy One Unit', 'vtwpr'),
                    'title2'   => __('Discount One Unit', 'vtwpr')    
                   ), 
                   */                                  
                  array (                                        
                    'id'       => 'buy_amt_type_qty',
                    'class'    => 'buy_amt_type_qty',
                    'value'    => 'quantity',
                    'title'    => __('Buy Unit Quantity ', 'vtwpr') 
                 //   'title2'   => __('Discount Unit Quantity ', 'vtwpr')  
                   ),                                                          
                  array (               
                    'id'       => 'buy_amt_type_currency',
                    'class'    => 'buy_amt_type_currency',
                    'value'    => 'currency',
                    'title'    => __('Buy $$ Value ', 'vtwpr')
                  //  'title2'   => __('Discount $$ Value ', 'vtwpr')  
                   ) /*,
                  array (
                    'id'       => 'buy_amt_type_nthQty',
                    'class'    => 'buy_amt_type_nthQty',
                    'value'    => 'nthQuantity',
                    'title'    => __('Buy Nth Unit', 'vtwpr'),
                    'title2'   => __('Discount Nth Unit', 'vtwpr') 
                   ),                                                          
                  array (               
                    'id'       => 'buy_amt_type_nthCurrency',
                    'class'    => 'buy_amt_type_options',
                    'value'    => 'nthCurrency',
                    'title'    => __('Nth $$ Value ', 'vtwpr')  
                   )*/
                )   
            ),
        //  'buy_amt_type'    => '',
          'buy_amt_count'    => array (    //times to be repeated ==>> repeat_condition must be 'count'.  if not, this is protected and greyed out...
            'id'    => 'buy_amt_count',
            'class'  => 'amt  buy_amt_count',
            'type'  => 'text',
            'name'  => 'buy_amt_count'        
            ),

          'buy_amt_applies_to' => array (  // none / count / next (next group) / all (all qty/$$ remaining / none ) / or (if qty/$$ reached, this supersedes previous condition)
              //dropdown select info
              'select'    => array(
                    'id'    => 'buy_amt_applies_to',
                    'class' => 'buy_amt_applies_to',
                    'name'  => 'buy_amt_applies_to',
                    'tabindex'  => ''
                ),
              //dropdown options
              'option'  => array(
                  array (
                    'id'       => 'buy_amt_applies_to_heading',
                    'class'    => 'buy_amt_applies_to_options',
                    'value'    => '0',
                    'title'    => __(' - Applies to Type - ', 'vtwpr')     //if count is selected, buy_repeat_count is unprotected => otherwise reverts to protected
                   ),  
                   array (
                    'id'       => 'buy_amt_applies_to_all',
                    'class'    => 'buy_amt_applies_to_all',
                    'value'    => 'all',
                    'title'    => __('All Products', 'vtwpr') .'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.  __('(total by group)', 'vtwpr')
                 //   'title2'   => __('All Products', 'vtwpr') .'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.  __('(total by group)', 'vtwpr') 
                  /*  'title'    => __('Group Amount applies to ALL Buy products as a group', 'vtwpr'),
                    'title2'   => __('Discount ALL products as a group', 'vtwpr')  */ 
                   ),                   
                  array (
                    'id'       => 'buy_amt_applies_to_each',
                    'class'    => 'buy_amt_applies_to_each',
                    'value'    => 'each',
                    'title'    => __('Each Product', 'vtwpr') .'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.    __('(total by product)', 'vtwpr') . '&nbsp;&nbsp;'
                 //   'title2'   => __('Each Product', 'vtwpr') .'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.    __('(total by product)', 'vtwpr') . '&nbsp;&nbsp;'
                    /*'title'    => __('Group Amount applies to EACH Buy product item total', 'vtwpr'),
                    'title2'   => __('Discount EACH product item total', 'vtwpr')*/  
                   )
                 )
            ),

                 //***********************************
                // buy 5 FOR A MINIMUM OF $300 get ... 
                //***********************************          
          'buy_amt_mod' =>  array (       // min / max / equal /none (def)
              //dropdown select info
              'select'    => array(
                    'id'    => 'buy_amt_mod',
                    'class' => 'buy_amt_mod',
                    'name'  => 'buy_amt_mod',
                    'tabindex'  => ''
                ),
              //dropdown options
              'option'  => array(
                  array (
                    'id'       => 'buy_amt_mod_heading',
                    'class'    => 'buy_amt_mod_options',
                    'value'    => '0',
                    'title'    => __(' - Min / Max Type - ', 'vtwpr')            //if none, buy_amt_mod_amt and buy_amt_mod_amt_type protected and greyed out 
                   ),
                  array (
                    'id'       => 'buy_amt_mod_none',
                    'class'    => 'buy_amt_mod_none',
                    'value'    => 'none',
                    'title'    => __('None ', 'vtwpr'),           //if none, buy_amt_mod_amt and buy_amt_mod_amt_type protected and greyed out
                    'title2'   => __('None ', 'vtwpr')
                    /*'title'    => __('No  Product Filter Amount Min / Max ', 'vtwpr'),           //if none, buy_amt_mod_amt and buy_amt_mod_amt_type protected and greyed out
                    'title2'   => __('No Discount Group Amount Min / Max ', 'vtwpr')*/ 
                   ),                   
                /*  array (
                    'id'       => 'buy_amt_mod_minQuantity',
                    'class'    => 'buy_amt_mod_options',
                    'value'    => 'minQuantity',
                    'title'    => __('Minimum Unit Quantity ', 'vtwpr')    
                   ),   */
                  array (
                    'id'       => 'buy_amt_mod_minCurrency',
                    'class'    => 'buy_amt_mod_minCurrency',
                    'value'    => 'minCurrency',
                    'title'    => __('Minimum $$ Value ', 'vtwpr'), 
                    'title2'   => __('Minimum $$ Value ', 'vtwpr')
                    /*'title'    => __(' Product Filter Amount Minimum $$ Value ', 'vtwpr'), 
                    'title2'   => __('Discount Group Amount Minimum $$ Value ', 'vtwpr')*/   
                   ),                                     
               /*  array (
                    'id'       => 'buy_amt_mod_maxQuantity',
                    'class'    => 'buy_amt_mod_options',
                    'value'    => 'maxQuantity',
                    'title'    => __('Maximum Unit Quantity ', 'vtwpr') 
                   ), */
                  array (
                    'id'       => 'buy_amt_mod_maxCurrency',
                    'class'    => 'buy_amt_mod_maxCurrency',
                    'value'    => 'maxCurrency',
                    'title'    => __('Maximum  $$ Value ', 'vtwpr'),
                    'title2'   => __('Maximum  $$ Value ', 'vtwpr')
                    /*'title'    => __(' Product Filter  Amount Maximum  $$ Value ', 'vtwpr'),
                    'title2'   => __('Discount Group Amount Maximum  $$ Value ', 'vtwpr')*/    
                   ) /*,                   
                  array (
                    'id'       => 'buy_amt_mod_equalQuantity',
                    'class'    => 'buy_amt_mod_options',
                    'value'    => 'equalQuantity',
                    'title'    => __('Equal Unit Quantity ', 'vtwpr')  
                   ),
                  array (
                    'id'       => 'buy_amt_mod_equalCurrency',
                    'class'    => 'buy_amt_mod_options',
                    'value'    => 'equalCurrency',
                    'title'    => __('Equal $$ Value ', 'vtwpr')   
                   ) */
                 )
            ),
       //   'buy_amt_mod'    => '',     
        //   'buy_amt_mod_amt_type'    => '', 
           'buy_amt_mod_count'    => array (   //if none [above ], buy_amt_mod_amt and buy_amt_mod_amt_type protected and greyed out
            'id'    => 'buy_amt_mod_count',
            'class'  => 'amt  buy_amt_mod_count',
            'type'  => 'text',
            'name'  => 'buy_amt_mod_count'    
            ),
            
            
           //GET INFO
                //discount occurs 5 times
                     //discount occurs 5 times
          'action_repeat_condition' => array (  // none / count / next (next group) / all (all qty/$$ remaining / none ) / or (if qty/$$ reached, this supersedes previous condition)
              //dropdown select info
              'select'    => array(
                    'id'    => 'action_repeat_condition',
                    'class' => 'action_repeat_condition',
                    'name'  => 'action_repeat_condition',
                    'tabindex'  => ''
                ),
              //dropdown options
              'option'  => array(
                  array (
                    'id'       => 'action_repeat_condition_heading',
                    'class'    => 'action_repeat_condition_options',
                    'value'    => '0',
                    'title'    => __(' - Group Repeat Type - ', 'vtwpr')  
                   ), 
                  array (
                    'id'       => 'action_repeat_condition_none',
                    'class'    => 'action_repeat_condition_none',
                    'value'    => 'none',
                    'title'    => __('None', 'vtwpr')  //only applies once per Discount group
                   ),
                  array (
                    'id'       => 'action_repeat_condition_unlimited',
                    'class'    => 'action_repeat_condition_unlimited',
                    'value'    => 'unlimited',
                    'title'    => __('Unlimited Discount Group Repeats', 'vtwpr')  . '&nbsp;&nbsp;' // . __('(after  Product Filter found 1st time) ', 'vtwpr')  
                   ),                                     
                   array (
                    'id'       => 'action_repeat_condition_count',
                    'class'    => 'action_repeat_condition_count',
                    'value'    => 'count',
                    'title'    => __('Discount Group Repeat Count', 'vtwpr') //.'&nbsp;&nbsp;'. __('(after  Product Filter found 1st time)  ', 'vtwpr')    //if count is selected, buy_repeat_count is unprotected => otherwise reverts to protected
                   )  /*,
                   array (
                    'id'       => 'action_repeat_condition_any',
                    'class'    => 'action_repeat_condition_options',
                    'value'    => 'any',
                    'title'    => __('Any ', 'vtwpr')    
                   )  ,                      
                      //**************************************************
                      //these others are future options only, must be greyed out on display
                      //**************************************************
                  array (
                    'id'       => 'action_repeat_condition_next',
                    'class'    => 'action_repeat_condition_options',
                    'value'    => 'next',
                    'title'    => __('Next ', 'vtwpr')
                   ),
                  array (
                    'id'       => 'action_repeat_condition_all',
                    'class'    => 'action_repeat_condition_options',
                    'value'    => 'all',
                    'title'    => __('All (all remaining) ', 'vtwpr')
                   ),                                                          
                  array (               
                    'id'       => 'action_repeat_condition_or',
                    'class'    => 'action_repeat_condition_options',
                    'value'    => 'or',
                    'title'    => __('Or (ascending table) ', 'vtwpr') 
                   ) */
                 )
            ),
        // 'action_repeat_condition'    => '', 
          'action_repeat_count'    => array (    //times to be repeated ==>> repeat_condition must be 'count'.  if not, this is protected and greyed out...
            'id'    => 'action_repeat_count',
            'class'  => 'amt  action_repeat_count',
            'type'  => 'text',
            'name'  => 'action_repeat_count'     
            ),
                
                          
          'action_amt_type' => array (  // none / count / next (next group) / all (all qty/$$ remaining / none ) / or (if qty/$$ reached, this supersedes previous condition)
              //dropdown select info
              'select'    => array(
                    'id'    => 'action_amt_type',
                    'class' => 'action_amt_type clear-left',
                    'name'  => 'action_amt_type',
                    'tabindex'  => ''
                ),
              //dropdown options
              //****************************************************************
              // These Titles are changed on the fly in admin-script.js, adding in 'Next' to the text
              //****************************************************************
              'option'  => array(                  
                   array (
                    'id'       => 'action_amt_type_heading',
                    'class'    => 'action_amt_type_Options',
                    'value'    => '0',
                    'title'    => __(' - Discount Amount Type - ', 'vtwpr')    
                   ),
                   array (
                    'id'       => 'action_amt_type_none',
                    'class'    => 'action_amt_type_none',
                    'value'    => 'none',
                    'title'    => __('Discount Each Unit', 'vtwpr')    
                   ),
                /*    REALLY KIND OF REDUNDANT.  
                      ALL OTHER CODE RELEVANT TO THIS STILL THERE, ONLY COMMENTED HERE and in the JS
                  array (
                    'id'       => 'action_amt_type_zero',      //only works with 'buy one' or 'buy nth'
                    'class'    => 'action_amt_type_zero',
                    'value'    => 'zero',
                    'title'    => __('Discount This One', 'vtwpr')    
                   ),
                  */
                  array (
                    'id'       => 'action_amt_type_one',
                    'class'    => 'action_amt_type_one',
                    'value'    => 'one',
                    'title'    => __('Discount Next One (single unit)', 'vtwpr')  . '&nbsp;&nbsp;'   
                   ),                   
                  array (
                    'id'       => 'action_amt_type_qty',
                    'class'    => 'action_amt_type_qty',
                    'value'    => 'quantity',
                    'title'    => __('Discount Unit Quantity', 'vtwpr'),
                    'title2'   => __('Discount Next Unit Quantity', 'vtwpr')    
                   ),                                                          
                  array (               
                    'id'       => 'action_amt_type_currency',
                    'class'    => 'action_amt_type_currency',
                    'value'    => 'currency',
                    'title'    => __('Discount $$ Value', 'vtwpr'),
                    'title2'   => __('Discount Next $$ Value', 'vtwpr')  
                   ) ,                   
                  array (
                    'id'       => 'action_amt_type_nthQty',
                    'class'    => 'action_amt_type_nthQty',
                    'value'    => 'nthQuantity',
                    'title'    => __('Discount Nth Unit', 'vtwpr'),
                    'title2'   => __('Discount Next Nth Unit', 'vtwpr') 
                   )/* ,                                                          
                  array (               
                    'id'       => 'action_amt_type_nthCurrency',
                    'class'    => 'action_amt_type_options',
                    'value'    => 'nthCurrency',
                    'title'    => __('Nth $$ Value ', 'vtwpr')  
                   )  */
                 )
            ),
        //  'action_amt_type'    => '',
          'action_amt_count'    => array (    //times to be repeated ==>> repeat_condition must be 'count'.  if not, this is protected and greyed out...
            'id'    => 'action_amt_count',
            'class'  => 'amt  action_amt_count',
            'type'  => 'text',
            'name'  => 'action_amt_count'        
            ),


          'action_amt_applies_to' => array (  // none / count / next (next group) / all (all qty/$$ remaining / none ) / or (if qty/$$ reached, this supersedes previous condition)
              //dropdown select info
              'select'    => array(
                    'id'    => 'action_amt_applies_to',
                    'class' => 'action_amt_applies_to',
                    'name'  => 'action_amt_applies_to',
                    'tabindex'  => ''
                ),
              //dropdown options
              'option'  => array(
                  array (
                    'id'       => 'action_amt_applies_to_heading',
                    'class'    => 'action_amt_applies_to_options',
                    'value'    => '0',
                    'title'    => __(' - Applies to Type - ', 'vtwpr')     //if count is selected, action_repeat_count is unprotected => otherwise reverts to protected
                   ),  
                   array (
                    'id'       => 'action_amt_applies_to_all',
                    'class'    => 'action_amt_applies_to_all',
                    'value'    => 'all',
                    'title'    => __('All Products', 'vtwpr') .'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.  __('(total by group) ', 'vtwpr')  . '&nbsp;&nbsp;'
                    //'title'    => __('Discount Amount Applies to ALL Discount products as a group ', 'vtwpr')     
                   ),                   
                  array (
                    'id'       => 'action_amt_applies_to_each',
                    'class'    => 'action_amt_applies_to_each',
                    'value'    => 'each',
                    'title'    => __('Each Product', 'vtwpr')  .'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.   __('(total by product) ', 'vtwpr')  . '&nbsp;&nbsp;'
                    //'title'    => __('Discount Amount Applies to ALL Discount products as a group ', 'vtwpr')   
                   )
                 )
            ),

          'action_amt_mod' =>  array (       // min / max / equal /none (def)
              //dropdown select info
              'select'    => array(
                    'id'    => 'action_amt_mod',
                    'class' => 'action_amt_mod',
                    'name'  => 'action_amt_mod',
                    'tabindex'  => ''
                ),
              //dropdown options
              'option'  => array(
                  array (
                    'id'       => 'action_amt_mod_heading',
                    'class'    => 'action_amt_mod_options',
                    'value'    => '0',
                    'title'    => __(' - Min / Max Type - ', 'vtwpr')            //if none, action_amt_mod_amt and action_amt_mod_amt_type protected and greyed out
                   ),
                  array (
                    'id'       => 'action_amt_mod_none',
                    'class'    => 'action_amt_mod_none',
                    'value'    => 'none',
                    'title'    => __('None', 'vtwpr')           //if none, action_amt_mod_amt and action_amt_mod_amt_type protected and greyed out
                  //'title'    => __('No Discount Group Amount Min / Max', 'vtwpr') 
                   ),                   
                /*  array (
                    'id'       => 'action_amt_mod_minQuantity',
                    'class'    => 'action_amt_mod_options',
                    'value'    => 'minQuantity',
                    'title'    => __('Minimum Unit Quantity ', 'vtwpr')    
                   ), */
                  array (
                    'id'       => 'action_amt_mod_minCurrency',
                    'class'    => 'action_amt_mod_minCurrency',
                    'value'    => 'minCurrency',
                    'title'    => __('Minimum $$ Value', 'vtwpr')
                    //'title'    => __('Discount Group Amount Minimum $$ Value', 'vtwpr')     
                   ),                                     
              /*    array (
                    'id'       => 'action_amt_mod_maxQuantity',
                    'class'    => 'action_amt_mod_options',
                    'value'    => 'maxQuantity',
                    'title'    => __('Maximum Unit Quantity ', 'vtwpr') 
                   ),   */
                  array (
                    'id'       => 'action_amt_mod_maxCurrency',
                    'class'    => 'action_amt_mod_maxCurrency',
                    'value'    => 'maxCurrency',
                    'title'    => __('Maximum $$ Value', 'vtwpr') 
                    //'title'    => __('Discount Group Amount Maximum $$ Value', 'vtwpr')   
                   ) /*,                   
                  array (
                    'id'       => 'action_amt_mod_equalQuantity',
                    'class'    => 'action_amt_mod_options',
                    'value'    => 'equalQuantity',
                    'title'    => __('Equal Unit Quantity ', 'vtwpr')  
                   ),
                  array (
                    'id'       => 'action_amt_mod_equalCurrency',
                    'class'    => 'action_amt_mod_options',
                    'value'    => 'equalCurrency',
                    'title'    => __('Equal $$ Value ', 'vtwpr')   
                   ) */
                 )
            ),
       //   'action_amt_mod'    => '',     
        //   'action_amt_mod_amt_type'    => '', 
           'action_amt_mod_count'    => array (   //if none [above ], action_amt_mod_amt and action_amt_mod_amt_type protected and greyed out
            'id'    => 'action_amt_mod_count',
            'class'  => 'amt  action_amt_mod_count',
            'type'  => 'text',
            'name'  => 'action_amt_mod_count'    
            ),
                   
          'discount_amt_type' => array (    // each/all
              //dropdown select info
              'select'    => array(
                    'id'    => 'discount_amt_type',
                    'class' => 'discount_amt_type',
                    'name'  => 'discount_amt_type',
                    'tabindex'  => ''
                ),
              //dropdown options
              'option'  => array(
                   array (
                    'id'       => 'discount_amt_type_heading',
                    'class'    => 'discount_amt_type_heading',
                    'value'    => '0',
                    'title'    => __(' - Discount Amount Type - ', 'vtwpr')   
                   ),
                   array (   //WHEN amount is a PERCENT, divide by 100 before using!
                    'id'       => 'discount_amt_type_percent',
                    'class'    => 'discount_amt_type_percent',
                    'value'    => 'percent',
                    'title'    => __('% Off ', 'vtwpr')  
                   ),                   
                   array (
                    'id'       => 'discount_amt_type_currency',
                    'class'    => 'discount_amt_type_currency',
                    'value'    => 'currency',
                    'title'    => __('$$ Off ', 'vtwpr')   
                   ),
                   array (
                    'id'       => 'discount_amt_type_fixedPrice',
                    'class'    => 'discount_amt_type_fixedPrice',
                    'value'    => 'fixedPrice',
                    'title'    => __('Fixed Unit Price $$ ', 'vtwpr')
                   ), 
                    
                   array (
                    'id'       => 'discount_amt_type_free',
                    'class'    => 'discount_amt_type_free',
                    'value'    => 'free',
                    'title'    => __('Free', 'vtwpr') .'&nbsp;&nbsp;&nbsp;'. __('[Discount = product price]', 'vtwpr')  
                   )
                                   
                 )
            ),
          'discount_amt_count'    => array (    //times to be amted ==>> amt_condition must be 'count'.  if not, this is protected and greyed out...
            'id'    => 'discount_amt_count',
            'class'  => 'amt  discount_amt_count',
            'type'  => 'text',
            'name'  => 'discount_amt_count'   
            ), 
          'discount_auto_add_free_product'    => array (  
            'id'    => 'discount_auto_add_free_product',
            'class'  => 'checkbox  discount_auto_add_free_product',
            'type'  => 'text',
            'name'  => 'discount_auto_add_free_product'   
            ),                       
          'discount_auto_add_free_product' => array (   
              'label'    => array(
                    'id'    => 'discount_auto_add_free_product_label',
                    'class' => 'discount_auto_add_free_product_label hideMe',
                    'title'  => '<span class="text-float">' 
                              . __('  Automatically Add Free Product to Cart', 'vtwpr') 
                              .'</span>'
                              .'&nbsp;&nbsp;<span class="discount_auto_add_free_product_label_caution">'
                              . __('(discount group must be an individual product)', 'vtwpr')
                              .'</span>'
                ),
              'checkbox'  => array(
                    'id'       => 'discount_auto_add_free_product',
                    'class'    => 'discount_auto_add_free_product',
                    'name'     => 'discount_auto_add_free_product',
                    'value'    => 'yes'                
                 )
            ),       
            
       /*   'discount_for_the_price_of_count'    => array (    //times to be for_the_price_ofed ==>> for_the_price_of_condition must be 'count'.  if not, this is protected and greyed out...
            'id'    => 'discount_for_the_price_of_count',
            'class'  => 'amt  discount_for_the_price_of_count',
            'type'  => 'text',
            'name'  => 'discount_for_the_price_of_count'
            ), */            
       
           
          'discount_applies_to' => array (    // each/all
              //dropdown select info
              'select'    => array(
                    'id'    => 'discount_applies_to',
                    'class' => 'discount_applies_to',
                    'name'  => 'discount_applies_to',
                    'tabindex'  => ''
                ),
              //dropdown options
              'option'  => array(
                   array (
                    'id'       => 'discount_applies_to_heading',
                    'class'    => 'discount_applies_to_heading',
                    'value'    => '0',
                    'title'    => __(' - Applies to Type -', 'vtwpr')    
                   ), 
                   array (
                    'id'       => 'discount_applies_to_each',
                    'class'    => 'discount_applies_to_each',
                    'value'    => 'each',
                    'title'    => __('Each Product', 'vtwpr')  .'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'. __('(each product unit singly)', 'vtwpr')  . '&nbsp;&nbsp;' 
                   ),                                                                          
                  array (               
                    'id'       => 'discount_applies_to_all',
                    'class'    => 'discount_applies_to_all',
                    'value'    => 'all',
                    'title'    => __('All Products', 'vtwpr')   .'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.  __('(all products as a group)', 'vtwpr') 
                   ),
                  array (
                    'id'       => 'discount_applies_to_cheapest',
                    'class'    => 'discount_applies_to_cheapest',
                    'value'    => 'cheapest',
                    'title'    => __('Cheapest Product', 'vtwpr')    
                   ),
                  array (
                    'id'       => 'discount_applies_to_most_expensive',
                    'class'    => 'discount_applies_to_most_expensive',
                    'value'    => 'most_expensive',
                    'title'    => __('Most Expensive Product', 'vtwpr')  
                   )/*, 
                  array (
                    'id'       => 'discount_applies_to_nth',
                    'class'    => 'discount_applies_to_options',
                    'value'    => 'nth',
                    'title'    => __('Every Nth Product in the Get Pool as a whole ', 'vtwpr')  
                   ),
                  array (
                    'id'       => 'discount_applies_to_each_nth',
                    'class'    => 'discount_applies_to_options',
                    'value'    => 'eachNth',
                    'title'    => __('Every Nth in the unit counts for each Product in the Get Pool ', 'vtwpr')  
                   )  */                   
                 )
            ),
       //   'discount_applies_to'    => '', 
          
       /*   'discount_applies_to_nth_count'    => array (    //times to be repeated ==>> repeat_condition must be 'count'.  if not, this is protected and greyed out...
            'id'    => 'discount_applies_to_nth_count',
            'class'  => 'text',
            'type'  => 'text',
            'name'  => 'discount_applies_to_nth_count'
            ),  */
        /*  
          'discount_pop_group' => array (    // each/all
              //dropdown select info
              'select'    => array(
                    'id'    => 'discount_pop_group',
                    'class' => 'discount_pop_group',
                    'name'  => 'discount_pop_group',
                    'tabindex'  => ''
                ),
              //dropdown options
              'option'  => array(
                   array (
                    'id'       => 'discount_pop_group_heading',
                    'class'    => 'discount_pop_group_options',
                    'value'    => '0',
                    'title'    => __(' - Discount Applies to which Group... - ', 'vtwpr')
                   ),
                   array (
                    'id'       => 'discount_pop_group_any',
                    'class'    => 'discount_pop_group_options',
                    'value'    => 'any',
                    'title'    => __('Discount Applies => to any product ', 'vtwpr')
                   ), 
                   array (
                    'id'       => 'discount_pop_group_inSameGroup',
                    'class'    => 'discount_pop_group_options',
                    'value'    => 'inSameGroup',
                    'title'    => __('Discount Applies => Within same Buy group ', 'vtwpr')
                   ),                                       
                   array (
                    'id'       => 'discount_pop_group_nextInBuyGroup',
                    'class'    => 'discount_pop_group_options',
                    'value'    => 'nextInBuyGroup',
                    'title'    => __('Discount Applies => to Next in Buy group ', 'vtwpr')
                   ),                                                                         
                  array (               
                    'id'       => 'discount_pop_group_nextInActionGroup',
                    'class'    => 'discount_pop_group_options',
                    'value'    => 'nextInActionGroup',
                    'title'    => __('Discount Applies => to Next in Get group ', 'vtwpr')
                   )
                 )
            ),
           */ 
            'discount_rule_max_amt_type' => array (  // none / count / next (next group) / all (all qty/$$ remaining / none ) / or (if qty/$$ reached, this supersedes previous condition)
                  //dropdown select info
                  'select'    => array(
                        'id'    => 'discount_rule_max_amt_type',
                        'class' => 'discount_rule_max_amt_type',
                        'name'  => 'discount_rule_max_amt_type',
                        'tabindex'  => ''
                    ),
                  //dropdown options
                  'option'  => array(
                       array (
                        'id'       => 'discount_rule_max_amt_type_heading',
                        'class'    => 'discount_rule_max_amt_type_options',
                        'value'    => '0',
                        'title'    => __(' - Cart Limit Type - ', 'vtwpr')    
                       ),
                       array (
                        'id'       => 'discount_rule_max_amt_type_none',
                        'class'    => 'discount_rule_max_amt_type_none',
                        'value'    => 'none',
                        'title'    => __('None ', 'vtwpr')   .'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.  __('- Unlimited Discount Usage', 'vtwpr')  .'&nbsp;&nbsp;&nbsp;&nbsp;'.   __(' - Per Cart ', 'vtwpr') .'&nbsp;&nbsp;&nbsp;'
                       ),                   
                      array (      //DIVIDE  by 100 when in use!!
                        'id'       => 'discount_rule_max_amt_type_percent',
                        'class'    => 'discount_rule_max_amt_type_percent',
                        'value'    => 'percent',                                              
                        'title'    => __('Cart Discount Max - Percentage of Total Value', 'vtwpr') 
                        //'title'    => __('Maximum Percentage Discount Value for the rule across the cart - Rule Max', 'vtwpr')  
                       ), 
                      array (
                        'id'       => 'discount_rule_max_amt_type_qty',
                        'class'    => 'discount_rule_max_amt_type_qty',
                        'value'    => 'quantity',
                        'title'    => __('Cart Discount Max - Number of Times Used', 'vtwpr')
                        //'title'    => __('Maximum Number of times the Discount may be employed for the rule across the cart - Rule Max', 'vtwpr')  
                       ),                                                                          
                      array (               
                        'id'       => 'discount_rule_max_amt_type_currency',
                        'class'    => 'discount_rule_max_amt_type_currency',
                        'value'    => 'currency',
                        'title'    => __('Cart Discount Max - $$ Value', 'vtwpr')  
                        //'title'    => __('Maximum $$ Value Discount the rule may create across the cart  - Rule Max', 'vtwpr')   
                       )
                     )
                ),
                
           //   'discount_rule_max_amt_type'    => '',
            'discount_rule_max_amt_count'    => array (    //times to be repeated ==>> repeat_condition must be 'count'.  if not, this is protected and greyed out...
                'id'    => 'discount_rule_max_amt_count',
                'class'  => 'amt  discount_rule_max_amt_count',
                'type'  => 'text',
                'name'  => 'discount_rule_max_amt_count',
                ),      
            
            'discount_lifetime_max_amt_type' => array (  // none / count / next (next group) / all (all qty/$$ remaining / none ) / or (if qty/$$ reached, this supersedes previous condition)
                  //dropdown select info
                  'select'    => array(
                        'id'    => 'discount_lifetime_max_amt_type',
                        'class' => 'discount_lifetime_max_amt_type',
                        'name'  => 'discount_lifetime_max_amt_type',
                        'tabindex'  => ''
                    ),
                  //dropdown options
                  'option'  => array(
                       array (
                        'id'       => 'discount_lifetime_max_amt_type_heading',
                        'class'    => 'discount_lifetime_max_amt_type_options',
                        'value'    => '0',
                        'title'    => __(' - Customer Lifetime Limit Type - ', 'vtwpr')  
                       ),
                       array (
                        'id'       => 'discount_lifetime_max_amt_type_none',
                        'class'    => 'discount_lifetime_max_amt_type_none',
                        'value'    => 'none',
                        'title'    => __('None ', 'vtwpr')  .'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.   __('- Unlimited Discount Usage', 'vtwpr')  .'&nbsp;&nbsp;&nbsp;&nbsp;'.  __(' - Per Customer ', 'vtwpr')   
                       ),                                                                                             
                      array (                    
                        'id'       => 'discount_lifetime_max_amt_type_quantity',
                        'class'    => 'discount_lifetime_max_amt_type_quantity',
                        'value'    => 'quantity', 
                        'title'    => __('Max Rule repeats per Customer?', 'vtwpr') .'&nbsp;'.  __('(Ever)', 'vtwpr') .'&nbsp;'.  __(' (pro only)', 'vtwpr'),  //free version 
                        'title3'   => __('Max Rule repeats per Customer?', 'vtwpr') .'&nbsp;&nbsp;&nbsp;'.  __('(Ever)', 'vtwpr')   //pro version                          
                       ), 
                      array (               
                        'id'       => 'discount_lifetime_max_amt_type_currency',
                        'class'    => 'discount_lifetime_max_amt_type_currency',
                        'value'    => 'currency',
                        'title'    => __('Max $$ Discount the Customer can receive?', 'vtwpr') .'&nbsp;'. __('(Ever)', 'vtwpr') .'&nbsp;'.   __('(pro only)', 'vtwpr'),  //free version 
                        'title3'   => __('Max $$ Discount the Customer can receive?', 'vtwpr') .'&nbsp;&nbsp;&nbsp;'.  __('(Ever)', 'vtwpr')  . '&nbsp;&nbsp;'  //pro version   
                       )
                     )
                ),
                
           //   'discount_lifetime_max_amt_type'    => '',
            'discount_lifetime_max_amt_count'    => array (    //times to be repeated ==>> repeat_condition must be 'count'.  if not, this is protected and greyed out...
                'id'    => 'discount_lifetime_max_amt_count',
                'class'  => 'amt',
                'type'  => 'text',
                'name'  => 'discount_lifetime_max_amt_count',
                ),  
                
            'discount_rule_cum_max_amt_type' => array (  // none / count / next (next group) / all (all qty/$$ remaining / none ) / or (if qty/$$ reached, this supersedes previous condition)
                  //dropdown select info
                  'select'    => array(
                        'id'    => 'discount_rule_cum_max_amt_type',
                        'class' => 'discount_rule_cum_max_amt_type',
                        'name'  => 'discount_rule_cum_max_amt_type',
                        'tabindex'  => ''
                    ),
                  //dropdown options
                  'option'  => array(
                       array (
                        'id'       => 'discount_rule_cum_max_amt_type_heading',
                        'class'    => 'discount_rule_cum_max_amt_type_options',
                        'value'    => '0',
                        'title'    => __(' - Product Limit Type - ', 'vtwpr')    
                       ),
                       array (
                        'id'       => 'discount_rule_cum_max_amt_type_none',
                        'class'    => 'discount_rule_cum_max_amt_type_none',
                        'value'    => 'none',
                        'title'    => __('None ', 'vtwpr')   .'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.   __('- Unlimited Discount Usage', 'vtwpr')  .'&nbsp;&nbsp;&nbsp;&nbsp;'.  __(' - Per Product ', 'vtwpr')  .'&nbsp;&nbsp;&nbsp;'
                       ),                   
                      array (      //DIVIDE  by 100 when in use!!
                        'id'       => 'discount_rule_cum_max_amt_type_percent',
                        'class'    => 'discount_rule_cum_max_amt_type_percent',
                        'value'    => 'percent',                                              
                        'title'    => __('Product Discount Max - Percentage of Total Value', 'vtwpr')  
                       ), 
                      array (
                        'id'       => 'discount_rule_cum_max_amt_type_qty',
                        'class'    => 'discount_rule_cum_max_amt_type_qty',
                        'value'    => 'quantity',
                        'title'    => __('Product Discount Max - Number of Times Used', 'vtwpr')  
                       ),                                                                          
                      array (               
                        'id'       => 'discount_rule_cum_max_amt_type_currency',
                        'class'    => 'discount_rule_cum_max_amt_type_currency',
                        'value'    => 'currency',
                        'title'    => __('Product Discount Max - $$ Value', 'vtwpr')   
                       )
                     )
                ),
                
           //   'discount_rule_cum_max_amt_type'    => '',
            'discount_rule_cum_max_amt_count'    => array (    //times to be repeated ==>> repeat_condition must be 'count'.  if not, this is protected and greyed out...
                'id'    => 'discount_rule_cum_max_amt_count',
                'class'  => 'amt',
                'type'  => 'text',
                'name'  => 'discount_rule_cum_max_amt_count',
                ),      
                             
    ); //end $vtwpr_deal_screen_framework
  
	}	

  
} //end class
$vtwpr_rules_ui_framework = new VTWPR_Rules_UI_Framework;
