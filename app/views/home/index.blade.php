@extends('master')

{{-- Web site Title --}}
@section('content')
<a class="btn btn-default btn-xs" style="display: none" id="back"><span class="glyphicon glyphicon-chevron-left"></span> Back</a>
<h3 id="chosen" style="display: none"></h3>
<p id="info" class="lead"><i class="fa fa-circle-o-notch fa-spin"></i> <span id="msg">locating you...</span></p>
<ul id="venues" style="display: none;"></ul>
<ul id="beers" style="display: none;" class="list-unstyled"></ul>

<script id="venuetmpl" type="text/x-jsrender">
      <li><a href="/beers/@{{:id}}">@{{:name}}</a> <small>~@{{:location.distance}}m  away</small></li>
</script>

<script id="beertmpl" type="text/x-jsrender">
      <li><img src="@{{:label}}" width="50" height="50" class="img-thumbnail" />  @{{:stars}} <strong>@{{:name}} </strong> <small class="muted">(A @{{:abv}}% @{{:style}} by @{{:brewery}})</small></li>
</script>
@stop
