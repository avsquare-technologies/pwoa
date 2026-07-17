@extends('layouts.front')

@section('title', 'Vendor Directory')

@section('content')
    @livewire('public.public-business-directory', ['type' => 'vendor'])
@endsection