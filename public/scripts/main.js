(function(){

    'use strict';

    // geolocation options
    var options = {
        enableHighAccuracy: true,
        timeout: 5000,
        maximumAge: 0
    };

    // detect user location
    navigator.geolocation.getCurrentPosition(success, error, options);

    // back button controls
    $('#back').click(function(){
        $('#venues').show();
        $('#beers, #chosen, #back').hide();
    });

    // listen for user clicking back/forward
    window.addEventListener("popstate", function(e) {
        
        // load location fragment - what about rest of the page?
        if(location.pathname === '/location'){
            loadLocation(location.pathname+location.search);
        }
    });

    function error(err) {
        console.warn('ERROR(' + err.code + '): ' + err.message);
    }

    function success(pos) {
        var crd = pos.coords;
        var location = "/location?lat="+crd.latitude+"&long="+crd.longitude;
        loadLocation(location);
    }

    function loadLocation(location){
        $.ajax({
            url: location
        }).done(function ( data ) {

            var locations = jQuery.parseJSON( data );
            var venues = locations.response.venues;

            var template = $.templates("#venuetmpl");
            template.link("#venues", venues);
            $('#info').hide();
            $('#venues').show();

            // set URL to location
            history.pushState(null, null, location);

            $('#venues a').bind('click',function(event){
                event.preventDefault();
                $('#chosen').html($(this).text()).show();
                $('#msg').html('finding their best beers...');
                $('#info').show();
                $('#venues').hide();
                $(this).addClass('selected');
                var venue = this.href;
                loadVenue(venue);
            });
        });
    }

    function loadVenue(venue){

        $.ajax({
            url: venue 
        }).done(function ( data ) {
            var btemplate = $.templates("#beertmpl");
            btemplate.link("#beers", data);
            $('#beers').show();
            $('#info').hide();
            $('#back').show();

            // set URL to location
            history.pushState(null, null, venue);
        });
    }

})();