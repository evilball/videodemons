/* @version 2.1 fixedMenu
 * @author Lucas Forchino
 * @webSite: http://www.jqueryload.com
 * jquery top fixed menu
 */
(function($){
    $.fn.fixedMenu=function(){
        return this.each(function(){
			var linkClicked= false;
            var menu= $(this);
			$('body').bind('click',function(){

					if(menu.find('.menu_active').size()>0 && !linkClicked)
					{
						menu.find('.menu_active').removeClass('menu_active');
					}
					else
					{
						linkClicked = false;
					}
			});

            menu.find('ul li > a').bind('click',function(){
				linkClicked = true;
				if ($(this).parent().hasClass('menu_active')){
					$(this).parent().removeClass('menu_active');
				}
				else{
					$(this).parent().parent().find('.menu_active').removeClass('menu_active');
					$(this).parent().addClass('menu_active');
				}
            })
        });
    }
})(jQuery);

jQuery(document).ready(function($){
	$('.menu').fixedMenu();
});
