@php
    $id??="global-message";
@endphp
@if(session()->has('message')&&$message=session()->pull('message'))
    <div class="message message-{{$message['status']??'info'}}" id="{{$id}}">{{$message['data']??''}}</div>
@endif
@if($id!=='global-message')
    <div id="{{$id}}"></div>
@endif