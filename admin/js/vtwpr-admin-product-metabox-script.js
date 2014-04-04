
        jQuery.noConflict();
        jQuery(document).ready(function($) {                                                        
           //tried to define the div elsewhere and then shift it in the DOM using insertAfter,
           //  but the textnodes (titles) didn't move with the html

            //Include/Exclude Redirect , inserted into the plugin taxonomy box            
            newHtml  =  '<div id="vtwpr-redirect"><h3>';
            newHtml +=  $("#vtwpr-sectionTitle").val();    //pick up the literals passed up in the html...
            newHtml +=  '</h3><a href="#vtwpr-wholesale_pricing-info" id="vtwpr-redirect-anchor">+ ';
            newHtml +=  $("#vtwpr-urlTitle").val();        //pick up the literals passed up in the html...
            newHtml +=  '</a></div>';
            
            //Include/Exclude Redirect , inserted into the plugin taxonomy box 
            $(newHtml).insertAfter('div#vtwpr_rule_category-adder');
            
            $('#vtwpr-redirect-anchor').click(function(){ 
                $('div#vtwpr-wholesale_pricing-info.postbox h3 span').css('color', 'blue');                                                  
            });
            
            //free version, protect everything except 'include in all rules as normal'
            $('#includeOrExclude_includeList').attr('disabled', true);
            $('#includeOrExclude_excludeList').attr('disabled', true);
            $('#includeOrExclude_excludeAll').attr('disabled', true);    
            $('.inOrEx-list-checkbox-class').attr('disabled', true);    //checkbox
            $('.inOrEx-list-checkbox-label').css('color', '#AAAAAA');   //label
            $('#includeOrExclude-title').css('color', '#AAAAAA');       //area title


        
        }); //end ready function 
                        