
jQuery.fn.dataTableExt.oSort['num-html-asc']  = function(a,b) {
var x = a.replace( /<span.*?>/g, "" );
x = x.replace( /<\/span>.*?$/g, "" );
var y = b.replace( /<span.*?>/g, "" );
y = y.replace( /<\/span>.*?$/g, "" );
if(x.match(/^-?\d{1,}(\.\d{1,})?$/) && y.match(/^-?\d{1,}(\.\d{1,})?$/))
{
	x = parseFloat( x );
	y = parseFloat( y );
}
else
{
	x=x.toLowerCase();
	y=y.toLowerCase();
}
return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};

jQuery.fn.dataTableExt.oSort['num-html-desc'] = function(a,b) {
var x = a.replace( /<span.*?>/g, "" );
x = x.replace( /<\/span>.*?$/g, "" );
var y = b.replace( /<span.*?>/g, "" );
y = y.replace( /<\/span>.*?$/g, "" );
if(x.match(/^-?\d{1,}(\.\d{1,})?$/) && y.match(/^-?\d{1,}(\.\d{1,})?$/))
{
	x = parseFloat( x );
	y = parseFloat( y );
}
else
{
	x=x.toLowerCase();
	y=y.toLowerCase();
}
return ((x < y) ?  1 : ((x > y) ? -1 : 0));
};
