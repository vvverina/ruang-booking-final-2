@extends('layouts.error')

@section('title', '404 - Page Not Found')

@section('content')
<div class="min-h-screen flex items-center justify-center">
    <div class="text-center">
        <div class="mb-8">
            <h1 class="text-9xl font-bold text-gray-300">404</h1>
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Page Not Found</h2>
            <p class="text-gray-600 mb-8 max-w-md mx-auto">
                The page you are looking for might have been removed, had its name changed, 
                or is temporarily unavailable.
            </p>
        </div>
        
        <div class="space-x-4">
            <a href="{{ url('/') }}" 
               class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded transition duration-200">
                Go Home
            </a>
            <a href="javascript:history.back()" 
               class="inline-block bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded transition duration-200">
                Go Back
            </a>
        </div>
    </div>
</div>
@endsection