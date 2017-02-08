jQuery(document).ready(
	function($)
	{
		//$(".torr_sl").hide();
		//$(".taltlink_sl").hide();
		//$(".post_sl").hide();
		//$(".scr_sl").hide();
		//$(".opt_sl").hide();
		//$(".vote_sl").hide();

		/*$(".torr_cl").click(function () {
			var isVisible = $('.torr_sl').is(':visible');
			if(isVisible)
			{
				$(".torr_sl").hide();
			}
			else
			{
				$(".torr_sl").show();
			}
			return false;
		});
		$(".post_cl").click(function () {
			var isVisible = $('.post_sl').is(':visible');
			if(isVisible)
			{
				$(".post_sl").hide();
			}
			else
			{
				$(".post_sl").show();
			}
			return false;
		});
		$(".scr_cl").click(function () {
			var isVisible = $('.scr_sl').is(':visible');
			if(isVisible)
			{
				$(".scr_sl").hide();
			}
			else
			{
				$(".scr_sl").show();
			}
			return false;
		});
		$(".opt_cl").click(function () {
			var isVisible = $('.opt_sl').is(':visible');
			if(isVisible)
			{
				$(".opt_sl").hide();
			}
			else
			{
				$(".opt_sl").show();
			}
			return false;
		});
		$(".vote_cl").click(function () {
			var isVisible = $('.vote_sl').is(':visible');
			if(isVisible)
			{
				$(".vote_sl").hide();
			}
			else
			{
				$(".vote_sl").show();
			}
			return false;
		});

		$(".torrhide").click(function () {
			var isVisible = $('.torrblock').is(':visible');
			if(isVisible)
			{
				$(".torrblock").fadeOut('slow');
				$(".torrhide").html('+');
			}
			else
			{
				$(".torrblock").fadeIn('slow');
				$(".torrhide").html('-');
			}
			return false;
		});*/

		$("a[rel^='prettyPhoto']").prettyPhoto({modal: false, show_title: false, social_tools: false, theme: prettyphoto_style});
	}
);

function htmlspecialchars(html)
{
      html = html.replace(/&/g, "&amp;");
      html = html.replace(/</g, "&lt;");
      html = html.replace(/>/g, "&gt;");
      html = html.replace(/"/g, "&quot;");

      return html;
}

function toggle_block(id)
{
	var el = document.getElementById(id);
	el.style.display = (el.style.display == 'none') ? '' : 'none';
}
