<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Beershere</title>
        <meta name="viewport" content="width=device-width">
        <link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/css/bootstrap-combined.no-icons.min.css" rel="stylesheet">
        <link href="//netdna.bootstrapcdn.com/font-awesome/3.0.2/css/font-awesome.css" rel="stylesheet">
        {{ HTML::style('css/main.css') }}
        @yield('styles')
    </head>

    <body>
        <div class="navbar navbar-fixed-top">
           <div class="navbar-inner">
             <div class="container">
               <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                 <span class="icon-bar"></span>
                 <span class="icon-bar"></span>
                 <span class="icon-bar"></span>
               </a>
               <a class="brand" href="">Beershere</a>
             </div>
           </div>
         </div>
        <div class="content">
            @if (Session::has('flash-error'))
                <div class="alert alert-error">  
                    <p>{{ Session::get('flash-error') }}</p>
                </div>
            @endif

            {{-- The main content container --}}
            @yield('content')
        </div>
    @yield('scripts')   
    <script src="//code.jquery.com/jquery-1.9.1.min.js"></script>
    {{ HTML::script('scripts/geoPosition.js') }}
    <script type="text/javascript">
    if(geoPosition.init()){  // Geolocation Initialisation
            geoPosition.getCurrentPosition(success_callback,error_callback,{enableHighAccuracy:true});
    }else{
            // You cannot use Geolocation in this device
            console.log('no geo');
    }

    // p : geolocation object
    function success_callback(p){
        $.ajax({
			url: "http://localhost:8000/location?lat="+p.coords.latitude+"&long="+p.coords.longitude
        }).done(function ( data ) {
        	var locations = jQuery.parseJSON( data );
            var venues = locations.response.venues;
            var nearest = venues[1];
            $('#info').html("It looks like you're near " + nearest.name);
            $('#beers').show();
            $.ajax({
                url: "http://localhost:8000/beers/"+nearest.id
            }).done(function ( data ) {
                var beers = "";
                for (var key in data) {
                  if (data.hasOwnProperty(key)) {
                    beers = (beers + (key + " -> " + data[key] + "<br />"));
                  }
                }
                $('#beers').html(beers);
            });
        });
    }

    function error_callback(p){
        // p.message : error message
        console.log(p.message);
    }
</script>
    </body>

</html>