<!DOCTYPE html>
<html>
<head>
    <title>{{$title??env('APP_NAME')}}</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="{{url('css/normalize.css')}}">
    <link rel="stylesheet" href="{{scss('app.scss')}}">
</head>
<body>
@extends('layouts.header')