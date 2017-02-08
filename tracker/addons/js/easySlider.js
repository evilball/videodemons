/*
 * 	Easy Slider 1.5 - jQuery plugin
 *	written by Alen Grakalic
 *	http://cssglobe.com/post/4004/easy-slider-15-the-easiest-jquery-plugin-for-sliding
 *
 *	Copyright (c) 2009 Alen Grakalic (http://cssglobe.com)
	Modified By PPK (C) 2009
 *	Dual licensed under the MIT (MIT-LICENSE.txt)
 *	and GPL (GPL-LICENSE.txt) licenses.
 *
 *	Built for jQuery library
 *	http://jquery.com
 *
 */

/*
 *	markup example for $("#slider").easySlider();
 *
 * 	<div id="slider">
 *		<ul>
 *			<li><img src="images/01.jpg" alt="" /></li>
 *			<li><img src="images/02.jpg" alt="" /></li>
 *			<li><img src="images/03.jpg" alt="" /></li>
 *			<li><img src="images/04.jpg" alt="" /></li>
 *			<li><img src="images/05.jpg" alt="" /></li>
 *		</ul>
 *	</div>
 *
 */

(function($) {

	$.fn.easySlider = function(options){

		// default configuration properties
		var defaults = {
			prevId: 		'prevBtn',
			prevText: 		' <img src="./tracker/addons/images/slider/go-previous-document.png" alt="Previous" /> ',
			nextId: 		'nextBtn',
			nextText: 		' <img src="./tracker/addons/images/slider/go-next-document.png" alt="Next" /> ',
			controlsShow:	true,
			controlsBefore:	'',
			controlsMiddle:	'',
			controlsAfter:	'',
			controlsFade:	false,
			firstId: 		'firstBtn',
			firstText: 		' <img src="./tracker/addons/images/slider/go-first.png" alt="First" /> ',
			firstShow:		false,
			lastId: 		'lastBtn',
			lastText: 		' <img src="./tracker/addons/images/slider/go-last.png" alt="Last" /> ',
			lastShow:		false,
			vertical:		false,
			speed: 			800,
			auto:			false,
			pause:			2000,
			continuous:		true
		};

		var options = $.extend(defaults, options);

		this.each(function() {
			var obj = $(this);
			var s = $("li", obj).length;
			var w = $("li", obj).width();
			var h = $("li", obj).height();
			obj.width(w);
			//obj.height(h);
			obj.css("overflow","hidden");
			var ts = s-1;
			var t = 0;
			$("ul", obj).css('width',s*w);
			if(!options.vertical) $("li", obj).css('float','left');

			options.firstId=options.firstId + $(this).attr('id');
			options.lastId=options.lastId + $(this).attr('id');
			options.prevId=options.prevId + $(this).attr('id');
			options.nextId=options.nextId + $(this).attr('id');

			if(options.controlsShow){
				var html = options.controlsBefore;
				if(options.firstShow) html += '<span id="'+ options.firstId +'"><a href=\"javascript:void(0);\">'+ options.firstText +'</a></span>';
				html += ' <span id="'+ options.prevId +'"><a href=\"javascript:void(0);\">'+ options.prevText +'</a></span>';
				if(options.controlsMiddle) html += options.controlsMiddle;
				html += ' <span id="'+ options.nextId +'"><a href=\"javascript:void(0);\">'+ options.nextText +'</a></span>';
				if(options.lastShow) html += ' <span id="'+ options.lastId +'"><a href=\"javascript:void(0);\">'+ options.lastText +'</a></span>';
				html += options.controlsAfter;
				if(ts!=0)
				{
					$(this).before(html);
				}
			};

			$("a","#"+options.nextId).click(function(){
				animate("next",true);
			});
			$("a","#"+options.prevId).click(function(){
				animate("prev",true);
			});
			$("a","#"+options.firstId).click(function(){
				animate("first",true);
			});
			$("a","#"+options.lastId).click(function(){
				animate("last",true);
			});

			function animate(dir,clicked){
				var ot = t;
				switch(dir){
					case "next":
						t = (ot>=ts) ? (options.continuous ? 0 : ts) : t+1;
						break;
					case "prev":
						t = (t<=0) ? (options.continuous ? ts : 0) : t-1;
						break;
					case "first":
						t = 0;
						break;
					case "last":
						t = ts;
						break;
					default:
						break;
				};

				var diff = Math.abs(ot-t);
				var speed = diff*options.speed;
				if(!options.vertical) {
					p = (t*w*-1);
					$("ul",obj).animate(
						{ marginLeft: p },
						speed
					);
				} else {
					p = (t*h*-1);
					$("ul",obj).animate(
						{ marginTop: p },
						speed
					);
				};

				if(!options.continuous && options.controlsFade){
					if(t==ts){
						$("a","#"+options.nextId).hide();
						$("a","#"+options.lastId).hide();
					} else {
						$("a","#"+options.nextId).show();
						$("a","#"+options.lastId).show();
					};
					if(t==0){
						$("a","#"+options.prevId).hide();
						$("a","#"+options.firstId).hide();
					} else {
						$("a","#"+options.prevId).show();
						$("a","#"+options.firstId).show();
					};
				};

				if(clicked) clearTimeout(timeout);
				if(options.auto && dir=="next" && !clicked){;
					timeout = setTimeout(function(){
						animate("next",false);
					},diff*options.speed+options.pause);
				};

			};
			// init
			var timeout;
			if(options.auto){;
				timeout = setTimeout(function(){
					animate("next",false);
				},options.pause);
			};

			if(!options.continuous && options.controlsFade){
				$("a","#"+options.prevId).hide();
				$("a","#"+options.firstId).hide();
			};

		});

	};

})(jQuery);

jQuery(document).ready(function($){
	$("div[id^='slider']").easySlider();
});

