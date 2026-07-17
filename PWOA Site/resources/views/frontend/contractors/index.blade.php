@extends('layouts.front')

@section('title', 'Contractor Directory')

@section('content')
    @livewire('public.public-business-directory', ['type' => 'contractor'])
@endsection

