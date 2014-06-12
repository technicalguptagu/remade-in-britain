/*jQuery(function($) {
	
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
	});*/
