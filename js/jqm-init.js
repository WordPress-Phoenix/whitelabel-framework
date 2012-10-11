function setCookie(c_name,value,exdays) {var exdate=new Date();exdate.setDate(exdate.getDate() + exdays);var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());document.cookie=c_name + "=" + c_value + ';path=/;';}
function getCookie(c_name) {var i,x,y,ARRcookies=document.cookie.split(";");for (i=0;i<ARRcookies.length;i++) {x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);x=x.replace(/^\s+|\s+$/g,"");if (x==c_name) {return unescape(y);}}}
function getQuerystring(variable) {var query = window.location.search.substring(1);var vars = query.split("&");for(var i=0;i<vars.length;i++){var pair = vars[i].split("=");if (pair[0] == variable){return pair[1];}}return '';}//var author_value = getQuerystring('author');
function setDefaultTransition(){
	var winwidth = $( window ).width(),	trans ="slide";		
	if( winwidth >= 1000 ){	trans = "none"; }
	else if( winwidth >= 650 ){	trans = "fade";	}
	jQuery.mobile.defaultPageTransition = trans;
}

//initial page methods and options
jQuery(function($){
	jQuery.mobile.ajaxEnabled = false;
	jQuery.mobile.hashListeningEnabled = false;
	
	/* This function fixes the issue created by jquerymobile, by allowing "hash anchor linking" otherwise known as "deep-linking"
	 * it goes through all <a> tags and converts hrefs to "hash" attritubes and then uses the attribute to scroll to the location 
	 * on the page. This will allow you to "keep you html content the same" instead of forcing you to change all your hyperlinks 
	 * by class or doing crazy javascript overrides 
	 */
	jQuery('a').each(function(i){if(this.hash){jQuery(this).attr('hash',this.hash).removeAttr('href'); jQuery(this).bind('click', function() { if(jQuery(this).attr('hash') == '#top') $.mobile.silentScroll(0); else {scrollTarget = jQuery( jQuery(this).attr('hash') ).get(0).offsetTop; $.mobile.silentScroll(scrollTarget);} return false; }); } });
	
	setDefaultTransition();
	jQuery( window ).bind( "throttledresize", setDefaultTransition );
	jQuery('#top').live('click', function() {  $.mobile.silentScroll(0); return false; });
	jQuery('#bookmark').live('click', function() {  jQuery('#menubox').toggle();jQuery('#stickyButtons').toggle();return false; });
	jQuery('#bookmarkit').live('click', function() {  
		var pageName=window.location.href;var nameArr =pageName.split("?");pageName=nameArr[0] + "?" + nameArr[1];
		if (window.sidebar){window.sidebar.addPanel(document.title,pageName,"");} 
		else if(document.all){ window.external.AddFavorite(pageName,document.title);} 
		else if(navigator.userAgent.toLowerCase().indexOf('iphone')!=-1) {alert('please press the \'+\' button on your browser to bookmark this site.');} 
		else if(navigator.userAgent.toLowerCase().indexOf('webkit')!=-1) {alert('please press ctrl + D to bookmark');} 
		else {return true;}
	 });
});