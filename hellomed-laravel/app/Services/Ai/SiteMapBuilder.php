<?php

namespace App\Services\Ai;

/**
 * Provides the AI with a complete, structured map of HelloMed's
 * patient-facing routes, navigation structure, and step-by-step
 * workflow guides — enabling the "Website Guide" chat mode.
 */
class SiteMapBuilder
{
    /**
     * Build the full site context payload for the AI system prompt.
     *
     * @return array{
     *   routes: array,
     *   workflows: array,
     *   navigation: array,
     * }
     */
    public function build(): array
    {
        return [
            'routes'     => $this->getPatientRoutes(),
            'workflows'  => $this->getWorkflowGuides(),
            'navigation' => $this->getNavStructure(),
        ];
    }

    /**
     * Every patient-facing URL with label and auth requirement.
     *
     * @return array<array{label: string, url: string, auth: bool, description: string}>
     */
    private function getPatientRoutes(): array
    {
        return [
            // ── Public (no login needed) ──────────────────────────────────────
            [
                'label'       => 'Home',
                'url'         => url('/'),
                'auth'        => false,
                'description' => 'Landing page with featured doctors, departments, and articles.',
            ],
            [
                'label'       => 'Browse Departments (Care)',
                'url'         => url('/departments'),
                'auth'        => false,
                'description' => 'List of all hospital departments (Cardiology, Orthopedics, Dental, Psychiatry, etc.).',
            ],
            [
                'label'       => 'Browse Doctors',
                'url'         => url('/doctors'),
                'auth'        => false,
                'description' => 'Directory of all active doctors, filterable by department. Each card links to the doctor profile.',
            ],
            [
                'label'       => 'Diagnostic Services',
                'url'         => url('/diagnostic-services'),
                'auth'        => false,
                'description' => 'List of all lab tests and diagnostic services available at HelloMed with fees and locations.',
            ],
            [
                'label'       => 'Health Articles',
                'url'         => url('/articles'),
                'auth'        => false,
                'description' => 'Health blog written by HelloMed doctors. Browse by category or read individual articles.',
            ],
            [
                'label'       => 'Q&A Forum',
                'url'         => url('/qna'),
                'auth'        => false,
                'description' => 'Community health Q&A. Anyone can browse; login required to ask a question or post an answer.',
            ],
            [
                'label'       => 'Medicine Shop',
                'url'         => url('/medicines'),
                'auth'        => false,
                'description' => 'Browse and search medicines by name, group, or manufacturer. Add items to cart.',
            ],
            [
                'label'       => 'Medicine Cart',
                'url'         => url('/shop/cart'),
                'auth'        => false,
                'description' => 'Review cart contents, adjust quantities, and proceed to checkout.',
            ],
            [
                'label'       => 'Request Ambulance',
                'url'         => url('/ambulance'),
                'auth'        => false,
                'description' => 'Emergency ambulance request form. NO login required. Fill in name, phone, location, emergency details.',
            ],
            [
                'label'       => 'About HelloMed',
                'url'         => url('/about'),
                'auth'        => false,
                'description' => 'About the hospital, its mission, team, and facilities.',
            ],
            [
                'label'       => 'Contact',
                'url'         => url('/contact'),
                'auth'        => false,
                'description' => 'Contact form and hospital address/phone.',
            ],
            [
                'label'       => 'Login',
                'url'         => url('/login'),
                'auth'        => false,
                'description' => 'Patient login page.',
            ],
            [
                'label'       => 'Register',
                'url'         => url('/register'),
                'auth'        => false,
                'description' => 'Create a new patient account.',
            ],

            // ── Patient authenticated ─────────────────────────────────────────
            [
                'label'       => 'Book Appointment',
                'url'         => url('/appointments/create/{doctor-slug}'),
                'auth'        => true,
                'description' => 'Appointment booking form for a specific doctor. Find the doctor at /doctors, then click "Book Appointment" on their profile.',
            ],
            [
                'label'       => 'My Appointments',
                'url'         => url('/my/appointments'),
                'auth'        => true,
                'description' => 'List of all your booked appointments (pending, confirmed, completed, cancelled).',
            ],
            [
                'label'       => 'Appointment Details',
                'url'         => url('/my/appointments/{id}'),
                'auth'        => true,
                'description' => 'View a single appointment: status, doctor info, prescription, lab test results, chat with doctor.',
            ],
            [
                'label'       => 'Download Prescription PDF',
                'url'         => url('/my/appointments/{id}/prescription-pdf'),
                'auth'        => true,
                'description' => 'Download a printable PDF of the doctor\'s prescription for a completed appointment.',
            ],
            [
                'label'       => 'Buy Prescribed Medicines',
                'url'         => url('/my/appointments/{id}/buy-all-medicines'),
                'auth'        => true,
                'description' => 'Adds all medicines from a prescription directly to your cart in one click.',
            ],
            [
                'label'       => 'My Profile (Medical Info)',
                'url'         => url('/my/profile'),
                'auth'        => true,
                'description' => 'Update your medical profile: date of birth, gender, height, weight, allergies, known conditions, address.',
            ],
            [
                'label'       => 'My Health Records',
                'url'         => url('/my/records'),
                'auth'        => true,
                'description' => 'Overview of your health history: past appointments, prescriptions, and lab test results.',
            ],
            [
                'label'       => 'My Medicine Orders',
                'url'         => url('/my/medicine-orders'),
                'auth'        => true,
                'description' => 'Track all your medicine orders (pending, processing, delivered, cancelled).',
            ],
            [
                'label'       => 'Medicine Order Details',
                'url'         => url('/my/medicine-orders/{id}'),
                'auth'        => true,
                'description' => 'Details of a single medicine order including items, status, and payment info.',
            ],
            [
                'label'       => 'Medicine Order Invoice',
                'url'         => url('/my/medicine-orders/{id}/invoice'),
                'auth'        => true,
                'description' => 'Download a PDF invoice for a medicine order.',
            ],
            [
                'label'       => 'Account Settings',
                'url'         => url('/settings/profile'),
                'auth'        => true,
                'description' => 'Change your account name, email, and password.',
            ],
        ];
    }

    /**
     * Step-by-step workflow guides for common patient tasks.
     *
     * @return array<string, array{title: string, steps: array<string>}>
     */
    private function getWorkflowGuides(): array
    {
        return [

            'book_appointment' => [
                'title'    => 'How to book an appointment with a doctor',
                'triggers' => ['book appointment', 'book a doctor', 'how do i book', 'schedule appointment', 'make appointment', 'consult a doctor'],
                'steps'    => [
                    'Log in to your HelloMed account. If you don\'t have one, click "Register" in the navigation bar.',
                    'Click "Care" in the navigation bar to browse departments → ' . url('/departments'),
                    'Or go directly to the Doctors directory to see all doctors → ' . url('/doctors'),
                    'Use the department filter on the Doctors page to narrow down by specialty.',
                    'Click on a doctor\'s name or card to open their profile.',
                    'Review the doctor\'s specialty, fees (online/offline), available days and times.',
                    'Click the "Book Appointment" button on the doctor\'s profile.',
                    'Choose your consultation mode: Online (video) or Offline (in-person).',
                    'Select an available date and time slot from the calendar.',
                    'Fill in your name, phone number, and reason for visit.',
                    'Choose a payment method (bKash or Nagad), enter your transaction ID and sender number.',
                    'Click "Confirm Booking". You\'ll see a success message.',
                    'Track your appointment status at "My appointments" → ' . url('/my/appointments'),
                ],
            ],

            'buy_medicines' => [
                'title'    => 'How to buy medicines from the pharmacy',
                'triggers' => ['buy medicine', 'order medicine', 'pharmacy', 'medicine shop', 'how to order medicine'],
                'steps'    => [
                    'Click "Medicine shop" in the navigation bar → ' . url('/medicines'),
                    'Browse medicines or use the search bar to search by name, group, or manufacturer.',
                    'Click on a medicine to view its details (strength, dosage, price, stock).',
                    'Click "Add to Cart" on the medicine you want.',
                    'Go to your cart → ' . url('/shop/cart'),
                    'Adjust quantities using the + / - buttons. Remove items you don\'t need.',
                    'Click "Checkout" (you must be logged in).',
                    'Enter your delivery address. You can share your current GPS location for accurate delivery.',
                    'Choose a payment method (bKash or Nagad), enter transaction ID.',
                    'Submit your order. You\'ll get a confirmation.',
                    'Track your order at "My medicine orders" → ' . url('/my/medicine-orders'),
                ],
            ],

            'view_prescription' => [
                'title'    => 'How to see and download your prescription',
                'triggers' => ['prescription', 'see prescription', 'download prescription', 'my prescription', 'doctor prescription'],
                'steps'    => [
                    'Go to "My appointments" → ' . url('/my/appointments'),
                    'Find the appointment where the doctor issued a prescription (it should be marked "Completed").',
                    'Click on that appointment to open its details.',
                    'Scroll down to the "Prescription" section — you\'ll see the medicines, advice, and diagnosis.',
                    'Click "Download Prescription PDF" to get a printable copy.',
                    'If the prescription includes medicines, click "Buy all prescribed medicines" to add them all to your cart in one click.',
                ],
            ],

            'view_lab_results' => [
                'title'    => 'How to view and download lab test results',
                'triggers' => ['lab result', 'test result', 'lab test', 'diagnostic result', 'download result', 'see my test'],
                'steps'    => [
                    'Go to "My appointments" → ' . url('/my/appointments'),
                    'Open the appointment where the doctor requested a lab test.',
                    'Scroll down to the "Lab Tests" section.',
                    'When hospital staff has processed and uploaded your results, a "Download" button will appear.',
                    'Click "Download" to get the PDF report.',
                    'Note: If the result hasn\'t been uploaded yet, the status will show "Pending" or "Processing".',
                ],
            ],

            'request_ambulance' => [
                'title'    => 'How to request an emergency ambulance',
                'triggers' => ['ambulance', 'emergency', 'call ambulance', 'request ambulance', 'emergency service'],
                'steps'    => [
                    'No login is required for ambulance requests — anyone can use this.',
                    'Click the red "🚑 Ambulance" button in the top navigation bar (visible on every page).',
                    'Or go directly to → ' . url('/ambulance'),
                    'Fill in your name, phone number, your current location, and describe the emergency.',
                    'Click "Submit Request".',
                    'Hospital staff will see your request immediately and dispatch a vehicle.',
                    'You\'ll see the request status update on the page. Keep the page open or note down your request reference.',
                ],
            ],

            'use_qna' => [
                'title'    => 'How to ask a health question in the Q&A forum',
                'triggers' => ['ask question', 'q&a', 'qna', 'post question', 'health question', 'forum'],
                'steps'    => [
                    'Click "Q&A" in the navigation bar → ' . url('/qna'),
                    'Browse existing questions — your question may already have been answered.',
                    'To ask a new question, log in first (or register if you don\'t have an account).',
                    'Click "Ask a Question" and fill in a clear title and detailed description.',
                    'Submit your question — doctors and staff can see and respond to it.',
                    'You can also answer other patients\' questions if you have relevant experience.',
                ],
            ],

            'update_profile' => [
                'title'    => 'How to update my medical profile',
                'triggers' => ['update profile', 'my profile', 'medical info', 'health profile', 'edit profile', 'complete profile', 'date of birth', 'blood group'],
                'steps'    => [
                    'Log in and click "My profile" in the navigation bar → ' . url('/my/profile'),
                    'Fill in or update: date of birth, gender, height, weight.',
                    'Add any allergies (e.g., penicillin, peanuts).',
                    'Add known medical conditions (e.g., diabetes, hypertension).',
                    'Add your home address for medicine deliveries.',
                    'Click "Save Profile" to update.',
                    'A complete profile helps doctors during your consultation and ensures accurate medical records.',
                    'Note: If your profile is incomplete, you\'ll see a yellow reminder banner on every page until it\'s complete.',
                ],
            ],

            'leave_review' => [
                'title'    => 'How to leave a review for a doctor',
                'triggers' => ['review', 'rate doctor', 'feedback doctor', 'leave review', 'write review'],
                'steps'    => [
                    'Go to the doctor\'s profile page → ' . url('/doctors'),
                    'Click on the doctor you visited.',
                    'Scroll down to the "Reviews" section at the bottom of their profile.',
                    'Select a star rating (1–5 stars).',
                    'Write your comment about your experience.',
                    'Click "Submit Review". You must be logged in.',
                ],
            ],

            'chat_with_doctor' => [
                'title'    => 'How to chat with my doctor during an appointment',
                'triggers' => ['chat with doctor', 'message doctor', 'send message doctor', 'appointment chat', 'talk to doctor'],
                'steps'    => [
                    'Go to "My appointments" → ' . url('/my/appointments'),
                    'Open a confirmed appointment.',
                    'Scroll down to the "Chat" section at the bottom of the appointment page.',
                    'Type your message in the text box.',
                    'You can also attach files (images, PDFs, documents) using the attach button.',
                    'Click "Send". The doctor will see your message in their panel.',
                    'Note: Chat is only available for confirmed/active appointments, not pending ones.',
                ],
            ],

            'check_appointment_status' => [
                'title'    => 'How to check my appointment status',
                'triggers' => ['appointment status', 'check appointment', 'my appointment', 'is my appointment confirmed'],
                'steps'    => [
                    'Go to "My appointments" → ' . url('/my/appointments'),
                    'Your appointments are listed with their current status:',
                    '  • Pending — waiting for admin/staff to confirm',
                    '  • Confirmed — approved, you can attend / join the online meeting',
                    '  • Completed — consultation is done',
                    '  • Cancelled — appointment was cancelled',
                    'Click on any appointment to view full details including doctor info, meeting link (for online), and prescription.',
                ],
            ],

            'account_settings' => [
                'title'    => 'How to change my account name, email, or password',
                'triggers' => ['change password', 'change email', 'account settings', 'update account', 'settings'],
                'steps'    => [
                    'Click "Account Settings" in the navigation bar → ' . url('/settings/profile'),
                    'Update your display name or email address.',
                    'To change your password, enter your current password, then your new password twice.',
                    'Click "Save Changes".',
                ],
            ],

            'diagnostic_services' => [
                'title'    => 'How to find and book diagnostic tests / lab services',
                'triggers' => ['diagnostic', 'lab test', 'blood test', 'x-ray', 'diagnostic service', 'test booking', 'available tests'],
                'steps'    => [
                    'Click "Diagnostic Services" in the navigation bar → ' . url('/diagnostic-services'),
                    'Browse the list of available tests (blood work, imaging, etc.).',
                    'Click on a test to see: description, fee (in BDT), lab room number, and location.',
                    'Note: Diagnostic tests are typically requested by your doctor during a consultation.',
                    'Your doctor adds a test request to your appointment. Staff will contact you or you can walk into the lab room.',
                    'After tests are done, results appear in your appointment details → ' . url('/my/appointments'),
                ],
            ],

            'how_hellomed_works' => [
                'title'    => 'What is HelloMed and how does it work?',
                'triggers' => ['what is hellomed', 'how does hellomed work', 'explain hellomed', 'what can hellomed do', 'about hellomed'],
                'steps'    => [
                    'HelloMed is a comprehensive digital hospital platform that lets you manage your healthcare online.',
                    'What you can do as a patient:',
                    '  • Book doctor appointments (online video or offline in-person) → ' . url('/doctors'),
                    '  • Order medicines from the integrated pharmacy → ' . url('/medicines'),
                    '  • Request emergency ambulances (no login needed) → ' . url('/ambulance'),
                    '  • Browse health articles written by doctors → ' . url('/articles'),
                    '  • Ask health questions in the community Q&A → ' . url('/qna'),
                    '  • Access lab/diagnostic services → ' . url('/diagnostic-services'),
                    '  • Download digital prescriptions and lab results → ' . url('/my/appointments'),
                    '  • Chat with your doctor during a consultation',
                    'Register for free to get started → ' . url('/register'),
                ],
            ],
        ];
    }

    /**
     * The site's navigation bar structure, described for the AI.
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
                'My profile'          => url('/my/profile'),
                'My appointments'     => url('/my/appointments'),
                'My records'          => url('/my/records'),
                'My medicine orders'  => url('/my/medicine-orders'),
                'Account Settings'    => url('/settings/profile'),
                '🔔 Notifications'    => 'Bell icon in top-right of navigation',
                'Logout'              => 'Logout button',
            ],
        ];
    }
}
