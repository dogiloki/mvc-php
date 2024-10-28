<!DOCTYPE html>
<html>
<head>
    <title>{{$title??env('APP_NAME')}}</title>
    <meta charset="utf-8">
    <meta name="_token" content="{{csrfToken()}}">
    <meta name="api-users-show" content="{{route('api-users-show')}}">
    <link rel="stylesheet" href="{{url('css/normalize.css')}}">
</head>
<body>