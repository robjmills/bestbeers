@extends('master')

{{-- Web site Title --}}
@section('content')
<a class="btn btn-default btn-xs" style="display: none" id="back"><span class="glyphicon glyphicon-chevron-left"></span> Back</a>
<h3 id="chosen" style="display: none"></h3>
<p id="info" class="lead"><i class="fa fa-circle-o-notch fa-spin"></i> <span id="msg">locating you...</span></p>
<ul id="venues" style="display: none;" class="list-unstyled"></ul>
<ul id="beers" style="display: none;" class="list-unstyled"></ul>

<script id="venuetmpl" type="text/x-jsrender">
      <li><a href="/beers/@{{:id}}">@{{:name}}</a> <small>~@{{:location.distance}}m  away</small></li>
</script>

<script id="beertmpl" type="text/x-jsrender">
      <li>
          <div class="media">
            <img src="@{{:label}}" width="50" height="50" class="img-thumbnail pull-left" />
            <div class="media-body">
              <h4 class="media-heading">@{{:name}} @{{:stars}}</h4>
                  <small class="muted">A @{{:abv}}% @{{:style}} by <strong>@{{:brewery}}</strong>
                  <em>(Last seen: @{{:last_seen}})</em>
                  </small>
            </div>
          </div>
      </li>
</script>
@stop
