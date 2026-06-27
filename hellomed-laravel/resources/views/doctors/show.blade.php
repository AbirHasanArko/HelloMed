@extends('layouts.app')

@section('content')
    <section class="section fade-in">
        <div class="grid cols-2">
            <div class="card" style="text-align:center;">
                <div class="tag">{{ $doctor->department?->name }}</div>
                @if ($doctor->photo_path)
                    <img src="{{ Storage::url($doctor->photo_path) }}" alt="{{ $doctor->name }}" class="avatar-image" style="width:140px;height:140px;margin:0 auto 16px;">
                @else
                    <div style="width:120px;height:120px;border-radius:50%;background:linear-gradient(135deg, var(--gradient-start), var(--gradient-end));display:grid;place-items:center;margin:0 auto 16px;">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" opacity="0.7"><path d="M8 7a4 4 0 1 0 8 0 4 4 0 1 0-8 0"/><path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/></svg>
                    </div>
                @endif
                <h1 style="font-size:1.8rem;">{{ $doctor->name }}</h1>
                <p style="font-size:1.05rem;">{{ $doctor->specialty }}</p>
                <p>{{ $doctor->bio }}</p>
                <div class="meta-row" style="justify-content:center;margin-top:8px;">
                    <span class="pill" style="background:var(--accent);color:var(--primary);border-color:transparent;">{{ $doctor->qualification }}</span>
                    <span class="pill" style="background:var(--accent);color:var(--primary);border-color:transparent;">{{ $doctor->experience_years }} years experience</span>
                </div>
            </div>
            <div class="card fade-in fade-in-delay-1">
                <h3>Service details</h3>
                <div class="list">
                    <div class="list-item" style="display:flex;align-items:center;gap:10px;">
                        <span style="font-size:18px;">⭐</span>
                        <div><strong>Average rating:</strong> {{ $averageRating > 0 ? $averageRating.'/5' : 'No ratings yet' }}</div>
                    </div>
                    
                    @php
                        $onlineDays = !empty($doctor->online_available_days) ? $doctor->online_available_days : $doctor->available_days;
                        $onlineFrom = $doctor->online_available_from ?: $doctor->available_from;
                        $onlineTo = $doctor->online_available_to ?: $doctor->available_to;

                        $offlineDays = !empty($doctor->offline_available_days) ? $doctor->offline_available_days : $doctor->available_days;
                        $offlineFrom = $doctor->offline_available_from ?: $doctor->available_from;
                        $offlineTo = $doctor->offline_available_to ?: $doctor->available_to;
                    @endphp

                    @if ($doctor->online_available)
                    <div class="list-item">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                            <span style="font-size:18px;">💻</span>
                            <strong>Online Consultation</strong>
                        </div>
                        <div style="padding-left:28px;font-size:0.95rem;color:var(--muted);">
                            <div style="margin-bottom:4px;"><strong style="color:var(--text);">Days:</strong> {{ is_array($onlineDays) && count($onlineDays) > 0 ? implode(', ', array_map('ucfirst', $onlineDays)) : 'Contact for schedule' }}</div>
                            <div style="margin-bottom:4px;"><strong style="color:var(--text);">Hours:</strong> {{ $onlineFrom ? \Carbon\Carbon::parse($onlineFrom)->format('h:i A') . ' - ' . \Carbon\Carbon::parse($onlineTo)->format('h:i A') : 'N/A' }}</div>
                            <div><strong style="color:var(--text);">Fee:</strong> <span class="price" style="font-size:1.05rem;">BDT {{ number_format((float) ($doctor->online_fee ?: $doctor->consultation_fee), 2) }}</span></div>
                        </div>
                    </div>
                    @endif

                    @if ($doctor->offline_available)
                    <div class="list-item">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                            <span style="font-size:18px;">🏥</span>
                            <strong>Offline / In-Person</strong>
                        </div>
                        <div style="padding-left:28px;font-size:0.95rem;color:var(--muted);">
                            <div style="margin-bottom:4px;"><strong style="color:var(--text);">Location:</strong> {{ $doctor->clinic_address ?: 'Hospital schedule on request' }}</div>
                            <div style="margin-bottom:4px;"><strong style="color:var(--text);">Days:</strong> {{ is_array($offlineDays) && count($offlineDays) > 0 ? implode(', ', array_map('ucfirst', $offlineDays)) : 'Contact for schedule' }}</div>
                            <div style="margin-bottom:4px;"><strong style="color:var(--text);">Hours:</strong> {{ $offlineFrom ? \Carbon\Carbon::parse($offlineFrom)->format('h:i A') . ' - ' . \Carbon\Carbon::parse($offlineTo)->format('h:i A') : 'N/A' }}</div>
                            <div><strong style="color:var(--text);">Fee:</strong> <span class="price" style="font-size:1.05rem;">BDT {{ number_format((float) ($doctor->offline_fee ?: $doctor->consultation_fee), 2) }}</span></div>
                        </div>
                    </div>
                    @endif
                </div>
                <a class="button" href="{{ route('appointments.create', $doctor) }}" style="width:100%;justify-content:center;margin-top:20px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Request appointment
                </a>
            </div>
        </div>
    </section>

    <section class="section fade-in">
        <div class="card">
            <h3>Patient ratings and comments</h3>
            @auth
                @if ($canReview)
                    <form method="POST" action="{{ route('doctors.reviews.store', $doctor) }}" style="margin-bottom:20px;padding:20px;background:var(--surface-hover);border-radius:14px;">
                        @csrf

                        {{-- Hidden input that holds the actual value --}}
                        <input type="hidden" name="rating" id="rating-value" value="0" required>

                        <div style="margin-bottom:16px;">
                            <div style="font-size:0.85rem;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px;">Your Rating</div>
                            <div class="star-picker" id="star-picker" style="display:flex;gap:6px;align-items:center;">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button type="button"
                                        class="star-btn"
                                        data-value="{{ $i }}"
                                        aria-label="{{ $i }} star{{ $i > 1 ? 's' : '' }}"
                                        style="background:none;border:none;padding:2px;cursor:pointer;line-height:1;transition:transform .15s;">
                                        <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="star-svg" style="color:#d1d5db;transition:color .15s,fill .15s;">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                        </svg>
                                    </button>
                                @endfor
                                <span id="star-label" style="font-size:0.9rem;color:var(--muted);margin-left:8px;">Select a rating</span>
                            </div>
                        </div>

                        <label>
                            Comment
                            <textarea name="comment" placeholder="Share your experience (optional)..."></textarea>
                        </label>
                        <button class="button" type="submit" id="review-submit-btn" disabled style="opacity:.5;cursor:not-allowed;">Submit review</button>
                    </form>
                @elseif (auth()->user()->role === 'patient')
                    <p class="muted" style="margin-bottom:16px;padding:14px 18px;background:var(--surface-hover);border-radius:10px;font-size:0.95rem;">
                        ⚠️ You can only leave a review after completing an appointment with this doctor.
                    </p>
                @endif
            @else
                <p class="muted" style="margin-bottom:16px;padding:14px 18px;background:var(--surface-hover);border-radius:10px;font-size:0.95rem;">
                    <a href="{{ route('login') }}">Log in</a> as a patient to leave a review.
                </p>
            @endauth

            <div class="list">
                @forelse ($doctor->reviews as $review)
                    <div class="list-item" style="display:flex;gap:14px;align-items:flex-start;">
                        <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg, var(--gradient-start), var(--gradient-end));display:grid;place-items:center;flex-shrink:0;color:white;font-weight:700;font-size:13px;">
                            {{ strtoupper(substr($review->user?->name ?? '?', 0, 1)) }}
                        </div>
                        <div>
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:2px;">
                                <strong>{{ $review->user?->name }}</strong>
                                <span style="display:flex;gap:2px;">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="{{ $i <= $review->rating ? '#f59e0b' : 'none' }}" stroke="{{ $i <= $review->rating ? '#f59e0b' : '#d1d5db' }}" stroke-width="1.8">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                        </svg>
                                    @endfor
                                </span>
                            </div>
                            <p style="margin-bottom:0;color:var(--muted);font-size:0.93rem;">{{ $review->comment ?: 'No comment.' }}</p>
                        </div>
                    </div>
                @empty
                    <div class="list-item muted">No reviews yet.</div>
                @endforelse
            </div>
        </div>
    </section>

    <style>
        .star-btn:hover svg, .star-btn:focus svg { transform: scale(1.18); }
        .star-btn svg.filled { color: #f59e0b; fill: #f59e0b; }
        .star-btn svg.hovered { color: #fbbf24; fill: #fbbf24; }
    </style>

    <script>
        (function () {
            const picker   = document.getElementById('star-picker');
            if (!picker) return;

            const input    = document.getElementById('rating-value');
            const label    = document.getElementById('star-label');
            const submitBtn = document.getElementById('review-submit-btn');
            const btns     = Array.from(picker.querySelectorAll('.star-btn'));
            const labels   = ['Terrible', 'Poor', 'Okay', 'Good', 'Excellent'];

            let selected = 0;

            function paint(hovered) {
                btns.forEach((btn, idx) => {
                    const svg = btn.querySelector('svg');
                    const filled = idx < selected;
                    const highlight = hovered !== null && idx <= hovered;
                    svg.classList.toggle('filled',  !hovered && filled);
                    svg.classList.toggle('hovered', highlight);
                    if (!highlight) {
                        svg.style.color = filled ? '#f59e0b' : '#d1d5db';
                        svg.style.fill  = filled ? '#f59e0b' : 'none';
                    }
                });
            }

            btns.forEach((btn, idx) => {
                btn.addEventListener('mouseenter', () => {
                    paint(idx);
                    label.textContent = (idx + 1) + ' - ' + labels[idx];
                });
                btn.addEventListener('mouseleave', () => {
                    paint(null);
                    label.textContent = selected ? selected + ' — ' + labels[selected - 1] : 'Select a rating';
                });
                btn.addEventListener('click', () => {
                    selected = idx + 1;
                    input.value = selected;
                    paint(null);
                    label.textContent = selected + ' — ' + labels[selected - 1];
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '1';
                    submitBtn.style.cursor = 'pointer';
                });
            });
        })();
    </script>
@endsection
