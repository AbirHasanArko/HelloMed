# 🔌 HelloMed API Documentation

HelloMed primarily uses standard server-side rendering (SSR) via Laravel Blade templates. However, highly interactive features, such as the AI Health Assistant, rely on robust RESTful JSON API endpoints.

## 🤖 AI Health Assistant APIs

The AI chat widget communicates with the Laravel backend via the following JSON endpoints. The backend then orchestrates communication with the locally hosted Ollama instance.

### 1. Send Chat Message
**Endpoint:** `POST /api/ai/chat`  
**Description:** Processes a patient's natural language message, performs RAG (Retrieval-Augmented Generation) against hospital data, and returns a structured AI response containing text, doctor suggestions, and workflow navigation links.

#### Request Headers
- `Content-Type: application/json`
- `Accept: application/json`
- `X-CSRF-TOKEN`: (Required for CSRF protection)

#### Request Body
```json
{
  "message": "I have severe chest pain",
  "history": [
    {"role": "user", "content": "Hi"},
    {"role": "assistant", "content": "Hello! How can I help you today?"}
  ]
}
```
**Parameters:**
- `message` *(string, required, max: 1000)* — The current message from the user.
- `history` *(array, optional, max: 12)* — The previous conversation context to maintain memory.

#### Success Response (`200 OK`)
```json
{
  "message": "I'm sorry to hear that. Since chest pain can be serious, please seek immediate help...",
  "intent": "health",
  "urgency": "high",
  "doctors": [
    {
      "id": 4,
      "name": "Dr. Mahmud Hasan",
      "specialty": "Cardiology",
      "photo_url": "/storage/doctors/mahmud.jpg"
    }
  ],
  "articles": [],
  "tests": [],
  "navigation_steps": [],
  "follow_up": "Would you like me to connect you with Dr. Mahmud Hasan?"
}
```

---

### 2. Check AI Status
**Endpoint:** `GET /api/ai/chat/status`  
**Description:** Health check to verify if the local Ollama instance is running and if the configured LLM model is downloaded and available. The frontend uses this to gracefully hide the chat widget if the AI server is offline.

#### Success Response (`200 OK`)
```json
{
  "available": true,
  "model": "mistral",
  "models": ["mistral", "phi3:mini"],
  "host": "http://localhost:11434"
}
```

---

### 3. Submit Chat Feedback
**Endpoint:** `POST /api/ai/chat/feedback`  
**Description:** Allows patients to rate AI responses (thumbs up/down) to help administrators improve the system prompt and context matching algorithms over time.

#### Request Body
```json
{
  "session_id": "sess_12345abcde",
  "rating": "helpful",
  "comment": "It gave me the exact doctor I needed!"
}
```
**Parameters:**
- `session_id` *(string, required, max: 64)* — Unique identifier for the chat session.
- `rating` *(string, required)* — Must be exactly `helpful` or `not_helpful`.
- `comment` *(string, optional, max: 500)* — Additional qualitative feedback.

#### Success Response (`200 OK`)
```json
{
  "success": true
}
```

---

## 📅 Doctor Scheduling APIs

Used to dynamically load and display a doctor's availability and booked slots on their public profile for appointment booking.

### 1. Get Doctor Schedule
**Endpoint:** `GET /api/doctors/{doctor}/schedule` *(or equivalent web route)*  
**Description:** Returns the doctor's configured online/offline availability hours, slot durations, and an array of their currently booked/unavailable upcoming slots (next 14 days).

#### Success Response (`200 OK`)
```json
{
  "online_available": true,
  "online_days": ["Monday", "Wednesday", "Friday"],
  "online_from": "09:00",
  "online_to": "14:00",
  "offline_available": true,
  "slot_minutes": 30,
  "booked_slots": [
    {
      "start": "2026-06-21 09:30:00",
      "start_formatted": "Jun 21, 2026 09:30 AM",
      "end_formatted": "10:00 AM"
    }
  ]
}
```

---

## 💬 Appointment Chat APIs

Used within the secured patient and doctor appointment panels to facilitate real-time messaging and file sharing.

### 1. Load Chat Messages
**Endpoint:** `GET /my/appointments/{appointment}/chat` *(or equivalent web route)*  
**Description:** Returns all messages for a confirmed appointment. Access is strictly authorized only to the specific patient and the assigned doctor.

#### Success Response (`200 OK`)
```json
{
  "enabled": true,
  "messages": [
    {
      "id": 1,
      "sender_id": 15,
      "sender_name": "John Doe",
      "is_mine": true,
      "message": "Here is my past medical report.",
      "created_at": "Jun 20, 2026 10:15 AM",
      "read_at": "Jun 20, 2026 10:16 AM",
      "attachment_url": "http://127.0.0.1:8000/storage/appointment-chat-attachments/report.pdf",
      "attachment_name": "report.pdf"
    }
  ]
}
```

### 2. Mark Messages as Read
**Endpoint:** `POST /my/appointments/{appointment}/chat/read`  
**Description:** Marks all unread messages sent by the other party as read. Returns the count of messages updated.

#### Success Response (`200 OK`)
```json
{
  "updated": 2
}
```

---

## 🔔 Notification Polling APIs

Used by the top navigation bar to periodically poll for unread notifications and update the notification drawer.

### 1. Fetch Notifications
**Endpoint:** `GET /api/notifications` *(or equivalent web route)*  
**Description:** Returns the authenticated user's unread notification count and their 10 most recent notifications (both read and unread).

#### Success Response (`200 OK`)
```json
{
  "unread_count": 3,
  "notifications": [
    {
      "id": "uuid-string",
      "data": {
        "title": "New Appointment",
        "message": "You have a new booking from John Doe.",
        "severity": "important",
        "action_url": "http://127.0.0.1:8000/doctor/appointments/45"
      },
      "read_at": null,
      "created_at": "2026-06-20T14:10:00.000000Z"
    }
  ]
}
```

### 2. Mark Notification as Read
**Endpoint:** `POST /api/notifications/{id}/read`  
**Description:** Marks a single specific notification as read.

### 3. Mark All as Read
**Endpoint:** `POST /api/notifications/read-all`  
**Description:** Marks all unread notifications for the user as read.

---

## 🔒 Security & Authentication
- **Session-Based:** All API routes currently rely on Laravel's built-in session authentication (cookie-based). 
- **CSRF Protection:** Every POST/PUT/DELETE request must include the `X-CSRF-TOKEN` header. This is automatically handled if you use Laravel's Axios wrapper or extract the token from the `<meta name="csrf-token">` tag.
- **Rate Limiting:** Guest usage is heavily throttled (`throttle:60,1`) to prevent abuse and LLM resource exhaustion.
