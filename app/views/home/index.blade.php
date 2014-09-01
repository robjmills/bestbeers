@extends('master')

{{-- Web site Title --}}
@section('content')
<a class="btn btn-default btn-xs" style="display: none" id="back"><span class="glyphicon glyphicon-chevron-left"></span> Back</a>
<h3 id="chosen" style="display: none"></h3>
<p id="info" class="lead"><i class="fa fa-circle-o-notch fa-spin"></i> <span id="msg">locating you...</span></p>
<ul id="venues" style="display: none;"></ul>
<ul id="beers" style="display: none;"></ul>

<script id="venuetmpl" type="text/x-jsrender">
      <li><a href="/beers/@{{:id}}">@{{:name}}</a> <small>~@{{:location.distance}}m  away</small></li>
</script>

<script id="beertmpl" type="text/x-jsrender">
      <li><strong>@{{:name}}</strong> <em>by @{{:brewery}}</em> <small>@{{:rating}}</small></li>
</script>
@stop
