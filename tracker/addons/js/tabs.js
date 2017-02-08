(function($){

	$.fn.tabber = function(options){
		options = $.extend({
			control : 'top',  // .left or .top
			tabClass : '.t',
			tabActive : '1',
			controlClass : '.c'
		},options);

		return this.each(function(){

			var tabber = $(this);
			var tabs = tabber.children(options.tabClass);
			var control = tabber.children(options.controlClass);
			var lines = control.children('li');
			var i = 1;
			var c = 0;
			var active_tab = 0;

			$(this).addClass('tabber');
			control.css('display', 'block').addClass(options.control);

			active_tab=control.children('li.active').index();

			if(!options.tabActive || active_tab==-1)
			{
				control.children('li:first').addClass('active first');
			}
			else
			{
				control.children('li:first').addClass('first');

				options.tabActive=active_tab+1;
 			}
			control.children('li:last').addClass('last');

			lines.each(function(){
				var content = $(this).text();
				$(this).html('<a href="#">'+content+'</a>');
				$(this).attr('data-tab', 'tab'+i);
				i=i+1;
				c=c+1;
			});
			i=1;

			tabs.each(function(){
				if(c!=1 && i!=options.tabActive)
					$(this).hide();
				$(this).attr('data-tab', 'tab'+i).addClass(options.control);
				i=i+1;
			});

			lines.click(function(){
				if(!$(this).hasClass('active')){

					lines.removeClass('active');

					$(this).addClass('active');

					tabs.hide();

					var tab = $(this).attr('data-tab');

					tabs.each(function(){
						if($(this).attr('data-tab') == tab)
							$(this).show();
					});
				}
				return false
			});



		});
	}

})(jQuery);

jQuery(document).ready(function($){
	$("div[id^='tabber']").tabber();
});
