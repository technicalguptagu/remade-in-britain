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
			
jQuery(function($) {
  $('#cartHeader .block-content').hide();
  $('#loginheader').hide();
  $("#informationhead").hide();
  $('#cartHeader .block-title').click(function(){
  if ($(window).width() < 966) {
 $('#cartHeader .block-content').toggle();
 $('#cartHeader').find('.block-title').toggleClass('active');
 $('#login-head').removeClass('active');
 $('#loginheader').hide();
  $('#info-head').removeClass('active');
  $('#informationhead').hide();
 }
 }); 
 $('#login-head').click(function(){
  if ($(window).width() < 966) {
  $('#loginheader').toggle();
  $('#login-head').toggleClass('active');
  $('#cartHeader').find('.block-title').removeClass('active');
  $('#cartHeader .block-content').hide();
  $('#info-head').removeClass('active');
  $('#informationhead').hide();
  }
  });
 $('#info-head').click(function(){
  if ($(window).width() < 966) {
  $('#informationhead').toggle();
  $('#info-head').toggleClass('active');
  $('#cartHeader').find('.block-title').removeClass('active');
  $('#cartHeader .block-content').hide();
  $('#login-head').removeClass('active');
  $('#loginheader').hide();
  }
  });
  $('#search').click(function(){
  if ($(window).width() < 966) {
  $('#search').toggleClass('active');
  }
});

  });


jQuery(document).ready(function(){	
var submenuhtml2 = '';
submenuhtml2=jQuery('.main-container .main .col-left .market-title').html();
jQuery('.main-container .main .col-left .market-title').remove();
jQuery('.main-container .main .breadcrumb-links').after(submenuhtml2);
 });


/*EOF developer.21*/

					
			
