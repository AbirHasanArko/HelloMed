<?php

namespace App\Services\Ai;

/**
 * Provides the AI with a complete, structured map of HelloMed's
 * patient-facing routes, navigation structure, and step-by-step
 * workflow guides — enabling the "Website Guide" chat mode.
 *
 * IMPORTANT: Steps are stored as structured arrays with real PHP url() calls,
 * not plain strings. This ensures links are always correct — the LLM is never
 * trusted to generate URLs.
 */
class SiteMapBuilder
{
    /**
     * Build the full site context payload for the AI system prompt.
     *
     * @return array{routes: array, workflows: array, navigation: array}
     */
    public function build(): array
    {
        return [
            'routes'     => $this->getPatientRoutes(),
            'workflows'  => $this->summariseWorkflows(),  // summary only (no full steps) for prompt
            'navigation' => $this->getNavStructure(),
        ];
    }

    /**
     * Find the best-matching workflow for a patient's message by checking trigger phrases.
     * Returns the full workflow (with structured steps) or null if nothing matches.
     *
     * @return array{title: string, steps: array}|null
     */
    public function findWorkflow(string $message): ?array
    {
        $lower     = strtolower($message);
        $workflows = $this->getWorkflowGuides();

        foreach ($workflows as $workflow) {
            foreach ($workflow['triggers'] as $trigger) {
                if (str_contains($lower, $trigger)) {
                    return $workflow;
                }
            }
        }

        return null;
    }

    /**
     * Every patient-facing URL with label and auth requirement.
     *
     * @return array<array{label: string, url: string, auth: bool, description: string}>
     */
    public function getPatientRoutes(): array
    {
        return [
            // ── Public ────────────────────────────────────────────────────────
            ['label' => 'Home',                  'url' => url('/'),                       'auth' => false, 'description' => 'Landing page with featured doctors, departments, and articles.'],
            ['label' => 'Browse Departments',    'url' => url('/departments'),            'auth' => false, 'description' => 'All hospital departments (Cardiology, Psychiatry, Nutrition, etc.).'],
            ['label' => 'Browse Doctors',        'url' => url('/doctors'),               'auth' => false, 'description' => 'Directory of all active doctors, filterable by department.'],
            ['label' => 'Diagnostic Services',   'url' => url('/diagnostic-services'),   'auth' => false, 'description' => 'Lab tests and diagnostic services with fees and room numbers.'],
            ['label' => 'Health Articles',       'url' => url('/articles'),              'auth' => false, 'description' => 'Health blog written by HelloMed doctors.'],
            ['label' => 'Q&A Forum',             'url' => url('/qna'),                   'auth' => false, 'description' => 'Community health Q&A. Login to ask or answer.'],
            ['label' => 'Medicine Shop',         'url' => url('/medicines'),             'auth' => false, 'description' => 'Browse and search medicines, add to cart.'],
            ['label' => 'Medicine Cart',         'url' => url('/shop/cart'),             'auth' => false, 'description' => 'Review cart, adjust quantities, proceed to checkout.'],
            ['label' => 'Request Ambulance',     'url' => url('/ambulance'),             'auth' => false, 'description' => 'Emergency ambulance request — NO login required.'],
            ['label' => 'About HelloMed',        'url' => url('/about'),                 'auth' => false, 'description' => 'About the hospital, its mission and facilities.'],
            ['label' => 'Contact',               'url' => url('/contact'),               'auth' => false, 'description' => 'Contact form and hospital address/phone.'],
            ['label' => 'Login',                 'url' => url('/login'),                 'auth' => false, 'description' => 'Patient login page.'],
            ['label' => 'Register',              'url' => url('/register'),              'auth' => false, 'description' => 'Create a new patient account.'],

            // ── Patient authenticated ─────────────────────────────────────────
            ['label' => 'My Appointments',       'url' => url('/my/appointments'),       'auth' => true,  'description' => 'List of all your appointments (pending, confirmed, completed).'],
            ['label' => 'My Profile',            'url' => url('/my/profile'),            'auth' => true,  'description' => 'Update medical profile: DOB, gender, height, weight, allergies.'],
            ['label' => 'My Health Records',     'url' => url('/my/records'),            'auth' => true,  'description' => 'History of appointments, prescriptions, and lab results.'],
            ['label' => 'My Medicine Orders',    'url' => url('/my/medicine-orders'),    'auth' => true,  'description' => 'Track all medicine orders (pending, processing, delivered).'],
            ['label' => 'Account Settings',      'url' => url('/settings/profile'),      'auth' => true,  'description' => 'Change account name, email, and password.'],
        ];
    }

    /**
     * Step-by-step workflow guides for common patient tasks.
     * Steps are structured arrays — links always come from url() helper, never from the LLM.
     *
     * @return array<string, array{title: string, triggers: array<string>, steps: array}>
     */
    public function getWorkflowGuides(): array
    {
        return [

            'book_appointment' => [
                'title'    => 'How to book an appointment with a doctor',
                'triggers' => [
                    'book appointment', 'book a doctor', 'how do i book', 'schedule appointment',
                    'make appointment', 'consult a doctor', 'get appointment', 'book a consultation',
                ],
                'steps' => [
                    ['instruction' => 'Log in to your HelloMed account. If you don\'t have one, register first — it\'s free.', 'link' => url('/login'), 'link_text' => 'Login / Register'],
                    ['instruction' => 'Go to the Doctors directory to browse all available doctors.', 'link' => url('/doctors'), 'link_text' => 'Browse Doctors'],
                    ['instruction' => 'Use the department filter on the Doctors page to narrow down by specialty (e.g. Cardiology, Psychiatry, Nutrition).', 'link' => url('/departments'), 'link_text' => 'Browse Departments'],
                    ['instruction' => 'Click on a doctor\'s card to view their profile — specialty, fees (online/offline), and available days.', 'link' => null, 'link_text' => null],
                    ['instruction' => 'Click the "Book Appointment" button on the doctor\'s profile page.', 'link' => null, 'link_text' => null],
                    ['instruction' => 'Choose your consultation mode: Online (video call) or Offline (in-person visit).', 'link' => null, 'link_text' => null],
                    ['instruction' => 'Select an available date and time slot from the calendar.', 'link' => null, 'link_text' => null],
                    ['instruction' => 'Fill in your name, phone, and reason for visit. Choose payment method (bKash / Nagad), enter transaction ID, and submit.', 'link' => null, 'link_text' => null],
                    ['instruction' => 'Done! Track your appointment status here — it starts as "Pending" and becomes "Confirmed" once approved.', 'link' => url('/my/appointments'), 'link_text' => 'My Appointments'],
                ],
            ],

            'buy_medicines' => [
                'title'    => 'How to buy medicines from the pharmacy',
                'triggers' => [
                    'buy medicine', 'order medicine', 'pharmacy', 'medicine shop',
                    'how to order medicine', 'purchase medicine', 'get medicine',
                ],
                'steps' => [
                    ['instruction' => 'Click "Medicine shop" in the navigation bar to browse the full medicine catalog.', 'link' => url('/medicines'), 'link_text' => 'Medicine Shop'],
                    ['instruction' => 'Search for medicines by name, group (e.g. antibiotics), or manufacturer using the search bar.', 'link' => null, 'link_text' => null],
                    ['instruction' => 'Click on a medicine to view its details: strength, dosage, price, and stock availability.', 'link' => null, 'link_text' => null],
                    ['instruction' => 'Click "Add to Cart" on any medicine you want to order.', 'link' => null, 'link_text' => null],
                    ['instruction' => 'Go to your cart to review items, adjust quantities, or remove medicines.', 'link' => url('/shop/cart'), 'link_text' => 'View Cart'],
                    ['instruction' => 'Click "Checkout" (you must be logged in). Enter your delivery address — you can share your GPS location for accurate delivery.', 'link' => null, 'link_text' => null],
                    ['instruction' => 'Choose payment method (bKash / Nagad) and enter your transaction ID. Submit your order.', 'link' => null, 'link_text' => null],
                    ['instruction' => 'Track your delivery status here.', 'link' => url('/my/medicine-orders'), 'link_text' => 'My Medicine Orders'],
                ],
            ],

            'view_prescription' => [
                'title'    => 'How to see and download your prescription',
                'triggers' => [
                    'prescription', 'see prescription', 'download prescription',
                    'my prescription', 'doctor prescription', 'view prescription',
                ],
                'steps' => [
                    ['instruction' => 'Go to "My appointments" to see all your consultations.', 'link' => url('/my/appointments'), 'link_text' => 'My Appointments'],
                    ['instruction' => 'Find the appointment where the doctor issued a prescription (usually marked "Completed").', 'link' => null, 'link_text' => null],
                    ['instruction' => 'Click on that appointment to open its full details page.', 'link' => null, 'link_text' => null],
                    ['instruction' => 'Scroll down to the "Prescription" section — you\'ll see the diagnosis, medicines, and doctor\'s advice.', 'link' => null, 'link_text' => null],
                    ['instruction' => 'Click "Download Prescription PDF" to get a printable copy.', 'link' => null, 'link_text' => null],
                    ['instruction' => 'If the prescription includes medicines, click "Buy all prescribed medicines" to add them all to your cart in one click.', 'link' => null, 'link_text' => null],
                ],
            ],

            'view_lab_results' => [
                'title'    => 'How to view and download lab test results',
                'triggers' => [
                    'lab result', 'test result', 'lab test', 'diagnostic result',
                    'download result', 'see my test', 'view test result',
                ],
                'steps' => [
                    ['instruction' => 'Go to "My appointments".', 'link' => url('/my/appointments'), 'link_text' => 'My Appointments'],
                    ['instruction' => 'Open the appointment where the doctor requested a lab test.', 'link' => null, 'link_text' => null],
                    ['instruction' => 'Scroll down to the "Lab Tests" section within the appointment details.', 'link' => null, 'link_text' => null],
                    ['instruction' => 'When hospital staff has uploaded your results, a "Download" button will appear. Click it to get the PDF report.', 'link' => null, 'link_text' => null],
                    ['instruction' => 'Note: If the result hasn\'t been uploaded yet, the status will show "Pending" or "Processing" — check back later.', 'link' => null, 'link_text' => null],
                ],
            ],

            'request_ambulance' => [
                'title'    => 'How to request an emergency ambulance',
                'triggers' => [
                    'ambulance', 'call ambulance', 'request ambulance',
                    'emergency ambulance', 'emergency service', 'urgent help',
                ],
                'steps' => [
                    ['instruction' => '🚨 No login is required — anyone can request an ambulance.', 'link' => null, 'link_text' => null],
                    ['instruction' => 'Click the red "🚑 Ambulance" button in the top navigation bar (visible on every page), or go directly here:', 'link' => url('/ambulance'), 'link_text' => '🚑 Request Ambulance'],
                    ['instruction' => 'Fill in your name, phone number, current location, and a brief description of the emergency.', 'link' => null, 'link_text' => null],
                    ['instruction' => 'Click "Submit Request". Hospital staff sees this immediately and dispatches a vehicle.', 'link' => null, 'link_text' => null],
                    ['instruction' => 'Keep the page open to see real-time status updates as the ambulance is dispatched.', 'link' => null, 'link_text' => null],
                ],
            ],

            'use_qna' => [
                'title'    => 'How to ask a health question in the Q&A forum',
                'triggers' => [
                    'ask question', 'q&a', 'qna', 'post question',
                    'health question', 'forum', 'ask a question',
                ],
                'steps' => [
                    ['instruction' => 'Go to the Q&A forum to browse existing questions — yours may already be answered.', 'link' => url('/qna'), 'link_text' => 'Q&A Forum'],
                    ['instruction' => 'Log in to your account to ask a new question or post an answer.', 'link' => url('/login'), 'link_text' => 'Login'],
                    ['instruction' => 'Click "Ask a Question" and write a clear title and detailed description of your question.', 'link' => null, 'link_text' => null],
                    ['instruction' => 'Submit your question — doctors and staff can respond. You\'ll see answers appear under your question.', 'link' => null, 'link_text' => null],
                ],
            ],

            'update_profile' => [
                'title'    => 'How to update my medical profile',
                'triggers' => [
                    'update profile', 'my profile', 'medical info', 'health profile',
                    'edit profile', 'complete profile', 'date of birth', 'blood group',
                    'medical profile',
                ],
                'steps' => [
                    ['instruction' => 'Log in and click "My profile" in the navigation bar.', 'link' => url('/my/profile'), 'link_text' => 'My Profile'],
                    ['instruction' => 'Fill in or update: date of birth, gender, height, and weight.', 'link' => null, 'link_text' => null],
                    ['instruction' => 'Add any known allergies (e.g. penicillin, peanuts) and medical conditions (e.g. diabetes, hypertension).', 'link' => null, 'link_text' => null],
                    ['instruction' => 'Add your home address for medicine deliveries.', 'link' => null, 'link_text' => null],
                    ['instruction' => 'Click "Save Profile". A complete profile helps doctors give you better care.', 'link' => null, 'link_text' => null],
                    ['instruction' => 'Tip: If your profile is incomplete, a yellow reminder banner will appear on every page until you complete it.', 'link' => null, 'link_text' => null],
                ],
            ],

            'leave_review' => [
                'title'    => 'How to leave a review for a doctor',
                'triggers' => [
                    'review', 'rate doctor', 'feedback doctor',
                    'leave review', 'write review', 'doctor review',
                ],
                'steps' => [
                    ['instruction' => 'Go to the Doctors directory and click on the doctor you want to review.', 'link' => url('/doctors'), 'link_text' => 'Browse Doctors'],
                    ['instruction' => 'Scroll down to the "Reviews" section at the bottom of the doctor\'s profile.', 'link' => null, 'link_text' => null],
                    ['instruction' => 'Select a star rating (1–5 stars) and write your feedback about the consultation experience.', 'link' => null, 'link_text' => null],
                    ['instruction' => 'Click "Submit Review" (you must be logged in).', 'link' => null, 'link_text' => null],
                ],
            ],

            'chat_with_doctor' => [
                'title'    => 'How to chat with my doctor',
                'triggers' => [
                    'chat with doctor', 'message doctor', 'send message doctor',
                    'appointment chat', 'talk to doctor', 'contact my doctor',
                ],
                'steps' => [
                    ['instruction' => 'Go to "My appointments" and open a confirmed appointment.', 'link' => url('/my/appointments'), 'link_text' => 'My Appointments'],
                    ['instruction' => 'Scroll down to the "Chat" section at the bottom of the appointment details page.', 'link' => null, 'link_text' => null],
                    ['instruction' => 'Type your message in the text box. You can also attach files (images, PDFs, documents) using the attach button.', 'link' => null, 'link_text' => null],
                    ['instruction' => 'Click "Send". The doctor will see your message in their panel.', 'link' => null, 'link_text' => null],
                    ['instruction' => 'Note: Chat is only available for confirmed/active appointments, not pending ones.', 'link' => null, 'link_text' => null],
                ],
            ],

            'check_appointment_status' => [
                'title'    => 'How to check my appointment status',
                'triggers' => [
                    'appointment status', 'check appointment', 'my appointment',
                    'is my appointment confirmed', 'appointment confirmed',
                ],
                'steps' => [
                    ['instruction' => 'Go to "My appointments" to see all your bookings with their current status.', 'link' => url('/my/appointments'), 'link_text' => 'My Appointments'],
                    ['instruction' => 'Status meanings: Pending = waiting for staff to confirm. Confirmed = approved, attend or join the online meeting. Completed = consultation done. Cancelled = appointment cancelled.', 'link' => null, 'link_text' => null],
                    ['instruction' => 'Click on any appointment to view full details including meeting link (for online), prescription, and lab tests.', 'link' => null, 'link_text' => null],
                ],
            ],

            'account_settings' => [
                'title'    => 'How to change my account name, email, or password',
                'triggers' => [
                    'change password', 'change email', 'account settings',
                    'update account', 'settings', 'reset password',
                ],
                'steps' => [
                    ['instruction' => 'Click "Account Settings" in the navigation bar.', 'link' => url('/settings/profile'), 'link_text' => 'Account Settings'],
                    ['instruction' => 'Update your display name or email address in the form.', 'link' => null, 'link_text' => null],
                    ['instruction' => 'To change your password: enter your current password, then your new password twice.', 'link' => null, 'link_text' => null],
                    ['instruction' => 'Click "Save Changes" to apply.', 'link' => null, 'link_text' => null],
                ],
            ],

            'diagnostic_services' => [
                'title'    => 'How to find and book diagnostic tests / lab services',
                'triggers' => [
                    'diagnostic', 'lab test', 'blood test', 'x-ray',
                    'diagnostic service', 'test booking', 'available tests',
                    'where to do test', 'how to get test',
                ],
                'steps' => [
                    ['instruction' => 'Click "Diagnostic Services" in the navigation bar to browse all available lab tests.', 'link' => url('/diagnostic-services'), 'link_text' => 'Diagnostic Services'],
                    ['instruction' => 'Click on any test to see: description, fee (in BDT), lab room number, and location.', 'link' => null, 'link_text' => null],
                    ['instruction' => 'Note: Diagnostic tests are usually requested by your doctor during a consultation. The doctor adds the test to your appointment record.', 'link' => null, 'link_text' => null],
                    ['instruction' => 'After the test is done, results appear in your appointment details page.', 'link' => url('/my/appointments'), 'link_text' => 'My Appointments'],
                ],
            ],

            'how_hellomed_works' => [
                'title'    => 'What is HelloMed and how does it work?',
                'triggers' => [
                    'what is hellomed', 'how does hellomed work', 'explain hellomed',
                    'what can hellomed do', 'about hellomed', 'what can i do',
                    'what does this website', 'features of hellomed',
                ],
                'steps' => [
                    ['instruction' => 'HelloMed is a comprehensive digital hospital platform for managing your healthcare online.', 'link' => url('/'), 'link_text' => 'Visit Home'],
                    ['instruction' => 'Book doctor appointments (online video or offline in-person) — browse all doctors here:', 'link' => url('/doctors'), 'link_text' => 'Browse Doctors'],
                    ['instruction' => 'Order medicines from the integrated e-pharmacy:', 'link' => url('/medicines'), 'link_text' => 'Medicine Shop'],
                    ['instruction' => 'Request emergency ambulances — no login needed:', 'link' => url('/ambulance'), 'link_text' => '🚑 Ambulance'],
                    ['instruction' => 'Read health articles written by doctors:', 'link' => url('/articles'), 'link_text' => 'Health Articles'],
                    ['instruction' => 'Browse and book diagnostic lab tests:', 'link' => url('/diagnostic-services'), 'link_text' => 'Diagnostic Services'],
                    ['instruction' => 'Ask health questions in the community forum:', 'link' => url('/qna'), 'link_text' => 'Q&A Forum'],
                    ['instruction' => 'Register for free to get started:', 'link' => url('/register'), 'link_text' => 'Register Now'],
                ],
            ],

        ];
    }

    /**
     * Returns a simplified summary of workflows (titles + triggers only) for the LLM system prompt.
     * The full steps are never sent to the LLM — they're returned directly from PHP.
     *
     * @return array<string, array{title: string, triggers: array<string>}>
     */
    private function summariseWorkflows(): array
    {
        $guides = [];
        foreach ($this->getWorkflowGuides() as $key => $workflow) {
            $guides[$key] = [
                'title'    => $workflow['title'],
                'triggers' => $workflow['triggers'],
            ];
        }
        return $guides;
    }

    /**
     * The site's navigation bar structure.
     *
     * @return array<string, mixed>
     */
    private function getNavStructure(): array
    {
        return [
            'always_visible' => [
                '🚑 Ambulance (red button)' => url('/ambulance'),
                'Home'                      => url('/'),
                'Care (Departments)'        => url('/departments'),
                'Diagnostic Services'       => url('/diagnostic-services'),
                'Q&A'                       => url('/qna'),
                'About'                     => url('/about'),
                'Medicine shop'             => url('/medicines'),
                'Contact'                   => url('/contact'),
            ],
            'guest_only' => [
                'Login'    => url('/login'),
                'Register' => url('/register'),
            ],
            'patient_logged_in' => [
                'My profile'         => url('/my/profile'),
                'My appointments'    => url('/my/appointments'),
                'My records'         => url('/my/records'),
                'My medicine orders' => url('/my/medicine-orders'),
                'Account Settings'   => url('/settings/profile'),
                '🔔 Notifications'   => 'Bell icon in top-right of navigation bar',
                'Logout'             => 'Logout button in navigation bar',
            ],
        ];
    }
}
