@extends('layouts.app')

@section('title', 'Find Your Best Friend')

@section('content')
    <main class="hero">
        <div class="hero-text">
            <div class="bg-circle"></div>
            <h1>Find Your Best<br>Friend Today</h1>
            <p>Connect with loving pets waiting for their forever home.</p>
            <a href="{{ route('browse') }}" class="btn-primary">Browse Pets</a>
        </div>

        <div class="hero-images">
            <div class="img-col">
                <img src="https://images.unsplash.com/photo-1543852786-1cf6624b9987?q=80&w=600&auto=format&fit=crop" class="img-short">
                <img src="https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?q=80&w=600&auto=format&fit=crop" class="img-tall">
            </div>
            <div class="img-col">
                <img src="https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?q=80&w=600&auto=format&fit=crop" class="img-tall">
                <img src="https://images.unsplash.com/photo-1537151608828-ea2b11777ee8?q=80&w=600&auto=format&fit=crop" class="img-short">
            </div>
    </main>
@endsection