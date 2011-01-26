jQuery(document).ready(function(){

     jQuery('#page-nav li:last-child').css('border','none');

// Slider 
        var panels_amount = jQuery('.panel').length;
        var panel_width = (jQuery('#categories-slider .panel').width()) + 12 ;
        var panels_width =   ((panels_amount *  panel_width) * -1) + (panel_width * 6);
        var panels_shift =    (panel_width * 3)*-1;
        var current_shift = 0;
        
        if(panels_amount > 6){
        if(current_shift == 0){
            
                jQuery('.carousel-nav .back').animate({
                    opacity:0 
                },2000,'easeInOutSine')
         }

        jQuery('.carousel-nav .next').click(function(){
            
            if( current_shift + panels_shift < panels_width  ) //End
            {
                 current_shift =  panels_width;
                 jQuery(this).animate({
                    opacity: 0
                 },2000,'easeInOutSine')
                 jQuery('.carousel-nav .back').animate({
                    opacity:1 
                },2000,'easeInOutSine')
                  //alert('1');                
            }     
            else{
                   current_shift = current_shift +  panels_shift;
                   jQuery(this).animate({
                        opacity: 1
                     },1500,'easeInOutSine');
                jQuery('.carousel-nav .back').animate({
                    opacity:1 
                },2000,'easeInOutSine');
                //alert('2');
            }

        jQuery('#categories-slider').animate({ marginLeft: current_shift }, 1500,'easeInOutSine'); //Action

        });
        
        jQuery('.carousel-nav .back').click(function(){
        
       
           if( current_shift - panels_shift > 0){
                    current_shift = 0;
                    
                jQuery('.carousel-nav .back').animate({
                    opacity:0 
                },2000,'easeInOutSine')   
             jQuery('.carousel-nav .next').animate({
                    opacity:1 
                },2000,'easeInOutSine')
                    
                    //alert('3'); 
            }
               else{
                   current_shift = current_shift -  panels_shift;
               jQuery('.carousel-nav .next').animate({
                    opacity:1 
                },2000,'easeInOutSine')
               
            if(current_shift == 0){
            
                jQuery('.carousel-nav .back').animate({
                    opacity:0 
                },2000,'easeInOutSine') 
         }
                   //alert('4'); 
            } 

       jQuery('#categories-slider').animate({ marginLeft: current_shift }, 1500,'easeInOutSine'); //Action

        });
        }  
        else {
             jQuery('.carousel-nav').hide();
        }     

        $('#poll-button').click(function(){
             $.post($('form[name=poll]').attr("action"),$('form[name=poll]').serialize(),function(data){$('#poll').html(data);});
             return false;
        });
});
