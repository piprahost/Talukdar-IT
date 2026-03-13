@extends('layouts.dashboard')

@section('title', 'Edit Profile')
@section('page-title', 'Edit Profile')

@section('content')
{{-- Redirect to profile show which now has inline edit forms --}}
<script>window.location.href = '{{ route("profile.show") }}';</script>
@endsection

