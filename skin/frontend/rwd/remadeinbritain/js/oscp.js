jQuery(function($) {
	
				$('.collapsible').each(function(index) {
			$(this).prepend('<span class="opener">&nbsp;</span>');
			if ($(this).hasClass('active'))
			{
				$(this).children('.block-content').css('display', 'block');
			}
			else
			{
				$(this).children('.block-content').css('display', 'none');
			}			
		});
		$('.collapsible .block-title').click(function() {
			if ($(window).width() < 801) {			
				var parent = $(this).parent();
				if (parent.hasClass('active'))
				{
					$(this).siblings('.block-content').stop(true).slideUp(300, "easeOutCubic");
					parent.removeClass('active');	
				}
				else
				{	
					$('.collapsible.mobile-collapsible').removeClass('active');
					$('.collapsible.mobile-collapsible').find('.block-content').stop(true).slideUp(300, "easeOutCubic");
					$(this).siblings('.block-content').stop(true).slideDown(300, "easeOutCubic");
					parent.addClass('active');
				}
			}			
		});
	});

/*BOF developer.21*/
			
jQuery(document).ready(function()
 {
  jQuery('#cartHeader .block-content').hide();
  jQuery('#loginheader').hide();
  jQuery("#informationhead").hide();
  jQuery('#cartHeader .block-title').click(function(){
 jQuery('#cartHeader .block-content').toggle();
 jQuery('#cartHeader').find('.block-title').toggleClass('active');
 jQuery('#login-head').removeClass('active');
 jQuery('#loginheader').hide();
  jQuery('#info-head').removeClass('active');
  jQuery('#informationhead').hide();
 }); 
 jQuery('#login-head').click(function(){
  jQuery('#loginheader').toggle();
  jQuery('#login-head').toggleClass('active');
  jQuery('#cartHeader').find('.block-title').removeClass('active');
  jQuery('#cartHeader .block-content').hide();
  jQuery('#info-head').removeClass('active');
  jQuery('#informationhead').hide();
  
  });
 jQuery('#info-head').click(function(){
  jQuery('#informationhead').toggle();
  jQuery('#info-head').toggleClass('active');
  jQuery('#cartHeader').find('.block-title').removeClass('active');
  jQuery('#cartHeader .block-content').hide();
  jQuery('#login-head').removeClass('active');
  jQuery('#loginheader').hide();
  });
  jQuery('#search').click(function(){
  jQuery('#search').toggleClass('active');
});

  });

jQuery(document).ready(function(){	
var submenuhtml2 = '';
submenuhtml2=jQuery('.customer-account-index .main-container .main .col-left .block-title').html();
jQuery('.customer-account-index .main-container .main .col-left .block-title').remove();
jQuery('.customer-account-index .main-container .main .breadcrumb-links .breadcrumbs').after(submenuhtml2);
 });

/*EOF developer.21*/

					
			
