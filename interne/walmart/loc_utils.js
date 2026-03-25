var lng = 0, lat = 0;

function setLongLat(new_lng, new_lat)
{
    lng = new_lng;
    lat = new_lat;
    if(lng != 0 && lat != 0)
    {
        localStorage.setItem('lat', lat);
        localStorage.setItem('lng', lng);
        if(typeof stores !== 'undefined' && typeof getStoreDistance !== 'undefined')
        {
            stores.forEach(function(s) {
                s.dist = -1;
            });
            stores.sort(function(a, b) {
              return getStoreDistance(a) - getStoreDistance(b);
            });

            window.dispatchEvent(new CustomEvent('stores_changed'));
        }
    }
    else
    {
        localStorage.removeItem('lat');
        localStorage.removeItem('lng');
    }
}

function getRandomInt(max) {
  return Math.floor(Math.random() * Math.floor(max));
}

var autocomplete, geocoder;
var geoInit = false;
function setupAutocomplete()
{
    if(typeof paramForm === 'undefined' || !paramForm.getInput("loc") || typeof gkey === 'undefined')
    {
        console.log("Param Form not set");
        return;
    }
    
    $.getScript("https://maps.googleapis.com/maps/api/js?key="+gkey+"&libraries=places&callback=initAutocomplete");
    paramForm.attachEvent("onFocus", function(name){
        if(name == 'loc' && !geoInit)
        {
            geoInit = true;
            if(paramForm.getItemValue("loc").length == 0 || !lat || !lng)
            {
                if (navigator.geolocation)
                {
                    navigator.geolocation.getCurrentPosition(function(position)
                    {
                        setLongLat(position.coords.longitude,position.coords.latitude);
                        
                        if(geocoder)
                        {
                            geocoder.geocode({'location': {lat: lat, lng: lng}}, function(results, status) {
                              if (status === 'OK') {
                                if (results[0]) {
                                    var zip = getZipFromPlace(results[0]);
                                    if(zip)
                                    {
                                        console.log(zip);
                                        paramForm.setItemValue("loc", zip);
                                        localStorage.setItem('loc', zip);
                                    }
                                }
                              }
                            });
                        }
                        setAutocompleteBounds();
                    });
                }
            }
            else if(lat && lng)
            {
                setAutocompleteBounds();
            }
        }
        return true;
    });
}


function getZipFromPlace(place)
{
    if(!place || !place.address_components)
        return;
    for (var i = place.address_components.length -1; i >= 0 ; i--) {
        if(!place.address_components[i].types)
            continue;
      for (var j = 0; j < place.address_components[i].types.length; j++) {
        if (place.address_components[i].types[j] == "postal_code") {
            return place.address_components[i].long_name;
        }
      }
    }
}

function initAutocomplete() {
    autocomplete = new google.maps.places.Autocomplete(
        paramForm.getInput("loc"),
        {types: ['geocode'],
         componentRestrictions: {country: 'ca'},
         fields: ["name", "geometry.location", "formatted_address", "address_components"]});

    // When the user selects an address from the dropdown, populate the address
    // fields in the form.
    autocomplete.addListener('place_changed', fillInAddress);
    
    geocoder = new google.maps.Geocoder;
    
    if(typeof onGeocoderInitComplete !== 'undefined')
    {
        onGeocoderInitComplete();
    }
}
function fillInAddress() {
    // Get the place details from the autocomplete object.
    var place = autocomplete.getPlace();
    var loc = getZipFromPlace(place);
    if(!loc && place.formatted_address)
        loc = place.formatted_address;
    if(!loc)
        loc = paramForm.getItemValue("loc");

    paramForm.setItemValue("loc", loc);
    localStorage.setItem('loc', loc);
    
    if(place.geometry && place.geometry.location && place.geometry.location.lat && place.geometry.location.lng)
    {
        setLongLat(place.geometry.location.lng(), place.geometry.location.lat());
    }
}
function setAutocompleteBounds()
{
    if(!lng || !lat || !autocomplete)
        return;
    
    var geolocation = {
        lat: lat,
        lng: lng
    };
    var circle = new google.maps.Circle({
        center: geolocation,
        radius: 1
    });
    autocomplete.setBounds(circle.getBounds());
}

function getNearestStores(do_next)
{
    if(!lng || !lat)
	{
        getGeoInfo(function(){getNearestStores(function(){	//recursion - it will be called only if lng and lat not 0
			if(do_next != undefined)
				do_next();
			});
		});
		return;
	}
	
    if(do_next != undefined)
        do_next();
}

function failedGeoLocation(mess)
{
	stopLoading();
	if(mess)
		dhtmlx.alert({
			title:"Error",
			type:"alert-error",
			text:mess
		});
}

var bDoGeocodeLocation = false;
var DoAfterGeocode;
function onGeocoderInitComplete()
{
    if(bDoGeocodeLocation && geocoder)
    {
        bDoGeocodeLocation = false;
        if(lat && lng)
        {
            geocoder.geocode({'location': {lat: lat, lng: lng}}, function(results, status) {
              if (status === 'OK') {
                if (results[0]) {
                    var zip = getZipFromPlace(results[0]);
                    if(zip)
                    {
                        console.log(zip);
                        paramForm.setItemValue("loc", zip);
                        localStorage.setItem('loc', zip);
                        if(typeof DoAfterGeocode == 'function')
                        {
                            DoAfterGeocode();
                            DoAfterGeocode = undefined;
                        }
                    }
                }
              }
            });
        }
        else if(paramForm.getItemValue("loc").length > 0)
        {
            geocoder.geocode({'address': paramForm.getItemValue("loc")}, function(results, status) {
                
              if (status === 'OK' && results[0] && results[0].geometry && results[0].geometry.location)
              {
                setLongLat(results[0].geometry.location.lng(), results[0].geometry.location.lat());
                
                if(typeof DoAfterGeocode == 'function')
                {
                    DoAfterGeocode();
                    DoAfterGeocode = undefined;
                }
              }
              else
                failedGeoLocation("Error getting geolocation data from Google. Try a different location, refresh the page or try again later.");
            });
        }
    }
}

function getGeoInfo(do_next)
{
	if(paramForm.getItemValue("loc").length == 0)
	{
		if (navigator.geolocation)
		{
			navigator.geolocation.getCurrentPosition(function(position)
			{
                setLongLat(position.coords.longitude, position.coords.latitude);
                
				paramForm.setItemValue("loc", lat.toFixed(5) + " " + lng.toFixed(5));
				if(do_next != undefined)
				{
					do_next();
				}
                bDoGeocodeLocation = true;
                if(geocoder)
                {
                    onGeocoderInitComplete();
                }
			}, function(){failedGeoLocation("Error getting geolocation info. Specify city name, postal code or address.");});
		}
		else
		{
			failedGeoLocation("Error getting geolocation info. Specify city name, postal code or address.");
		}
	}
	else
	{
        bDoGeocodeLocation = true;
        DoAfterGeocode = do_next;
        if(geocoder)
        {
            onGeocoderInitComplete();
        }
	}
}