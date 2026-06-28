@extends('layouts.app')

@section('content')
    <section class="section">
        <div class="grid cols-2 fade-in" style="align-items: start; gap: 24px;">
            <div style="display: flex; flex-direction: column; gap: 24px;">
                <div class="card">
                    <div class="tag">Contact hospital</div>
                    <h1>Get in touch</h1>
                    <p>Use this page for appointments, department inquiries, and general hospital support.</p>
                    <div class="list" style="margin-top:8px;">
                        <a href="tel:999" class="list-item" style="display:flex;gap:12px;align-items:center;text-decoration:none;color:inherit;transition:background 0.2s;">
                            <div style="width:40px;height:40px;border-radius:12px;background:var(--badge-red-bg);display:grid;place-items:center;flex-shrink:0;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--badge-red-text)" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            </div>
                            <div>
                                <strong>Emergency</strong>
                                <p style="margin:0;">999 or the hospital emergency desk</p>
                            </div>
                        </a>
                        <a href="tel:+8801234567890" class="list-item" style="display:flex;gap:12px;align-items:center;text-decoration:none;color:inherit;transition:background 0.2s;">
                            <div style="width:40px;height:40px;border-radius:12px;background:var(--accent);display:grid;place-items:center;flex-shrink:0;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            </div>
                            <div>
                                <strong>Reception</strong>
                                <p style="margin:0;">+880 1234 567890</p>
                            </div>
                        </a>
                        <a href="mailto:care@hellomed.test" class="list-item" style="display:flex;gap:12px;align-items:center;text-decoration:none;color:inherit;transition:background 0.2s;">
                            <div style="width:40px;height:40px;border-radius:12px;background:var(--accent);display:grid;place-items:center;flex-shrink:0;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            </div>
                            <div>
                                <strong>Email</strong>
                                <p style="margin:0;">care@hellomed.test</p>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="card fade-in fade-in-delay-1">
                    <div style="display:flex;gap:10px;align-items:center;margin-bottom:16px;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
                        <h3 style="margin:0;">Visit hours</h3>
                    </div>
                    <div class="list">
                        <div class="list-item">
                            <strong>Saturday to Thursday</strong>
                            <p style="margin:0;">8:00 AM - 10:00 PM</p>
                        </div>
                        <div class="list-item">
                            <strong>Friday</strong>
                            <p style="margin:0;">Emergency only</p>
                        </div>
                    </div>
                    <p style="margin-top:16px;">Offline services can be booked online through doctor profiles and appointment forms.</p>
                    <a class="button" href="{{ route('doctors.index') }}" style="margin-top:8px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 7a4 4 0 1 0 8 0 4 4 0 1 0-8 0"/><path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/></svg>
                        Find a doctor
                    </a>
                </div>
            </div>
            
            <div class="card fade-in fade-in-delay-1" style="height: 100%;">
                <div class="tag">Message us</div>
                <h2 style="margin: 0 0 16px 0;">Advanced Contact Form</h2>
                @auth
                    @if(session('success'))
                        <div style="background:var(--badge-green-bg);color:var(--badge-green-text);padding:12px;border-radius:12px;margin-bottom:16px;">
                            {{ session('success') }}
                        </div>
                    @endif
                    <form action="{{ route('contact.store') }}" method="POST" style="display:flex;flex-direction:column;gap:16px;">
                        @csrf
                        <div class="form-group">
                            <label>Subject</label>
                            <input type="text" name="subject" class="input" required style="width: 100%;">
                        </div>
                        <div class="form-group">
                            <label>Message</label>
                            <textarea name="message" class="input" rows="7" required style="width: 100%; resize: vertical;"></textarea>
                        </div>
                        <button class="button" type="submit" style="background: var(--primary); color: white;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                            Send Message
                        </button>
                    </form>
                @else
                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 300px; text-align: center; gap: 16px; background: var(--bg); border-radius: 12px; padding: 24px;">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--text-muted)" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        <p style="margin: 0; color: var(--text-muted);">Please log in to send a message directly to the hospital desk.</p>
                        <a href="{{ route('login') }}" class="button" style="background: var(--primary); color: white;">Log In</a>
                    </div>
                @endauth
            </div>
        </div>
        
        <div class="card fade-in fade-in-delay-2" style="margin-top:24px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 16px;">
                <div>
                    <div class="tag">Location</div>
                    <h2 style="margin: 0;">Find Us</h2>
                </div>
                <a href="https://maps.google.com/?q=Dhaka,Bangladesh" target="_blank" class="button">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    Get Directions
                </a>
            </div>
            <div style="border-radius:16px; overflow:hidden; height: 350px;">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14608.036944850553!2d90.3671072!3d23.74705!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755b8b087026b81%3A0x8fa563bbdd5904c2!2sDhaka%2C%20Bangladesh!5e0!3m2!1sen!2sus!4v1711234567890!5m2!1sen!2sus" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </section>
@endsection
