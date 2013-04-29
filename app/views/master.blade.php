<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Beershere</title>
        <meta name="viewport" content="width=device-width">
        <link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/css/bootstrap-combined.min.css" rel="stylesheet">
        {{ Html::style('css/main.css') }}
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
    {{ Html::script('scripts/geoPosition.js') }}
    <script type="text/javascript">
    if(geoPosition.init()){  // Geolocation Initialisation
            geoPosition.getCurrentPosition(success_callback,error_callback,{enableHighAccuracy:true});
    }else{
            // You cannot use Geolocation in this device
            console.log('no geo');
    }

    // p : geolocation object
    function success_callback(p){
    	console.log(p);
        // p.latitude : latitude value
        // p.longitude : longitude value
        	console.log(p.coords.latitude);
        	console.log(p.coords.longitude);

        $.ajax({
			url: "http://localhost:8000/location?lat="+p.coords.latitude+"&long="+p.coords.longitude
        }).done(function ( data ) {
        	console.log(data);
        });
    }

    function error_callback(p){
        // p.message : error message
        console.log(p.message);
    }
</script>
    </body>

</html>