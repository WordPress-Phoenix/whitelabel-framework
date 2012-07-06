document.write(['<script src="http://maps.google.com/maps/api/js?sensor=false"text/javascript"><\/script>'].join(''));

function loadMaps(lat,lon){
	
	var latlng = new google.maps.LatLng(lat, lon);
	var opt =	{
		center:latlng,
		zoom:10,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		disableAutoPan:false,
		scrollwheel:false,
		navigationControl:true,
		navigationControlOptions: {style:google.maps.NavigationControlStyle.SMALL },
		mapTypeControl:true,
		mapTypeControlOptions: {style:google.maps.MapTypeControlStyle.DROPDOWN_MENU}
	};
		
	var map = new google.maps.Map(document.getElementById("googlemap"),opt);
	var iconImg = 'http://anthem.edu/wp-content/external-library/google-maps/map-pin.png';
	var marker= new google.maps.Marker({
		position: new google.maps.LatLng(lat, lon),
		title: "CodeGlobe",
		clickable: true,
		map: map,
		icon: iconImg
	});
	
	// Add address information to marker
	var address = jQuery("#collegeBrand").text() + ', ' + jQuery('#addressL1').text() + ', ' + jQuery('#addressL2').text();
	var address = address.replace(/#/ig, '');
	google.maps.event.addListener(marker, 'click', function() {
	  //infowindow.open(map,marker);
	  window.location = 'http://local.google.com/maps?q=' + address;
	});

	try{console.info('http://local.google.com/maps?q=' + address)}catch(e){}
}