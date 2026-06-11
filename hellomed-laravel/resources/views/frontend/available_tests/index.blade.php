@extends('layouts.app')

@section('content')
<div class="hero">
    <div class="hero-text fade-in">
        <h1>Available Lab Tests</h1>
        <p style="font-size: 1.15rem; max-width: 500px;">Browse our comprehensive catalog of diagnostic and laboratory tests. High-quality imaging, blood work, and pathology services available directly at our hospital.</p>
    </div>
    <div class="hero-visual fade-in fade-in-delay-1">
        <div class="hero-visual-pattern"></div>
        <div style="position: relative; z-index: 1; text-align: center; color: white;">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 16px;">
                <path d="M10 2v7.31"></path><path d="M14 9.3V1.99"></path><path d="M8.5 2h7"></path><path d="M14 9.3a6.5 6.5 0 1 1-4 0"></path><path d="M5.52 16h12.96"></path>
            </svg>
            <h2 style="color: white; background: none; -webkit-text-fill-color: white;">Diagnostics Center</h2>
        </div>
    </div>
</div>

<section class="section fade-in fade-in-delay-2">
    <div class="grid cols-3">
        @forelse ($tests as $test)
            <a class="card" href="{{ route('available-tests.show', $test) }}" style="display: flex; flex-direction: column;">
                <h3>{{ $test->name }}</h3>
                <div style="flex-grow: 1;">
                    @if($test->description)
                        <p style="font-size: 14px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $test->description }}</p>
                    @endif
                </div>
                <div style="margin-top: 16px; border-top: 1px solid var(--border); padding-top: 12px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-size: 12px; color: var(--muted);">Fee</div>
                        <div style="font-weight: 700; color: var(--primary);">BDT {{ number_format($test->fee_bdt, 2) }}</div>
                    </div>
                    <span class="ghost-button" style="padding: 4px 10px; font-size: 12px;">View Details</span>
                </div>
            </a>
        @empty
            <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                <p class="muted">No lab tests are currently available in the catalog.</p>
            </div>
        @endforelse
    </div>
</section>
@endsection
