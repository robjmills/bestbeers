'use strict';

function error(err) {
    console.warn('ERROR(' + err.code + '): ' + err.message);
};

function success(pos) {

    var crd = pos.coords;

    $.ajax({
        url: "/location?lat="+crd.latitude+"&long="+crd.longitude
    }).done(function ( data ) {

        var locations = jQuery.parseJSON( data );
        var venues = locations.response.venues;

        var template = $.templates("#venuetmpl");
        template.link("#venues", venues);
        $('#info').hide();
        $('#venues').show();

        $('#venues a').bind('click',function(event){
            event.preventDefault();
            $('#chosen').html($(this).text()).show();
            $('#msg').html('finding their best beers...');
            $('#info').show();
            $('#venues').hide()
            $(this).addClass('selected');
            $.ajax({
                url: this.href
            }).done(function ( data ) {
                var btemplate = $.templates("#beertmpl");
                btemplate.link("#beers", data);
                $('#beers').show();
                $('#info').hide();
                $('#back').show();
            });
        });
    });
};

(function(){

    var options = {
        enableHighAccuracy: true,
        timeout: 5000,
        maximumAge: 0
    };

    navigator.geolocation.getCurrentPosition(success, error, options);

    $('#back').click(function(){
        $('#venues').show();
        $('#beers, #chosen, #back').hide();
    });

})();