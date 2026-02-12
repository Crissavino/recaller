<?php

return [
    'back' => 'Go back',
    'last_updated' => 'Last updated',

    'terms' => [
        'title' => 'Terms of Service',
        'summary' => 'By using Recaller, you agree to these terms. Please read them carefully before creating an account.',

        'section1_title' => 'Acceptance of Terms',
        'section1_content' => 'By accessing or using Recaller ("Service"), you agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use our Service.',

        'section2_title' => 'Description of Service',
        'section2_content' => 'Recaller provides automated WhatsApp follow-up services for dental clinics to recover missed calls. Our Service includes:',
        'section2_item1' => 'Automated WhatsApp messaging to patients who called when the clinic was unavailable',
        'section2_item2' => 'A unified inbox to manage patient conversations',
        'section2_item3' => 'Analytics and reporting on recovered patients',
        'section2_item4' => 'Integration with phone systems via Twilio or Vonage',

        'section3_title' => 'User Responsibilities',
        'section3_content' => 'You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account. You agree to use the Service in compliance with all applicable laws, including HIPAA and TCPA regulations for healthcare communications and WhatsApp messaging.',

        'section4_title' => 'Payment and Billing',
        'section4_content' => 'Subscription fees are billed in advance on a monthly or annual basis. You authorize us to charge your payment method for all fees incurred. Prices may change with 30 days notice. Refunds are provided at our discretion.',

        'section5_title' => 'Data and Privacy',
        'section5_content' => 'We take data protection seriously. Patient data is encrypted and stored securely. We do not sell or share your data with third parties except as required to provide the Service. See our Privacy Policy for more details.',

        'section6_title' => 'Limitation of Liability',
        'section6_content' => 'Recaller is provided "as is" without warranties of any kind. We are not liable for any indirect, incidental, or consequential damages arising from your use of the Service. Our total liability is limited to the amount you paid for the Service in the past 12 months.',

        'section7_title' => 'Termination',
        'section7_content' => 'You may cancel your subscription at any time. We may terminate or suspend your account for violations of these terms. Upon termination, your right to use the Service ceases immediately.',

        'section8_title' => 'Contact',
        'section8_content' => 'For questions about these Terms, contact us at',
    ],

    'privacy' => [
        'title' => 'Privacy Policy',
        'summary' => 'Your privacy matters to us. This policy explains how we collect, use, and protect your information.',

        'section1_title' => 'Information We Collect',
        'section1_content' => 'We collect information you provide directly (account details, clinic information) and information generated through your use of the Service (call logs, messages, analytics data).',

        'section2_title' => 'How We Use Your Information',
        'section2_content' => 'We use your information to:',
        'section2_item1' => 'Provide and improve our Service',
        'section2_item2' => 'Send automated WhatsApp messages on your behalf',
        'section2_item3' => 'Generate analytics and reports',
        'section2_item4' => 'Communicate with you about your account',

        'section3_title' => 'Data Security',
        'section3_content' => 'We implement industry-standard security measures including encryption at rest and in transit, secure data centers, and regular security audits. Access to patient data is strictly controlled and logged.',

        'section4_title' => 'Data Retention',
        'section4_content' => 'We retain your data for as long as your account is active and for a reasonable period afterward for legal and business purposes. You may request deletion of your data at any time.',

        'section5_title' => 'Third-Party Services',
        'section5_content' => 'We use third-party services (Twilio, Vonage, Stripe) to provide our Service. These providers have their own privacy policies and we only share the minimum data necessary.',

        'section6_title' => 'Contact',
        'section6_content' => 'For privacy-related questions, contact us at',
    ],

    'gdpr' => [
        'title' => 'GDPR Compliance',
        'summary' => 'Recaller is committed to protecting your data in compliance with the EU General Data Protection Regulation (GDPR). This page explains how we handle personal data.',

        'controller_title' => 'Data Controller',
        'controller_content' => 'The data controller for the Recaller platform is:',

        'data_collected_title' => 'Personal Data We Collect',
        'data_collected_content' => 'We collect and process the following categories of personal data:',
        'data_category' => 'Category',
        'data_examples' => 'Examples',
        'data_cat1' => 'Clinic Account Data',
        'data_ex1' => 'Name, email, clinic name, phone number, business hours',
        'data_cat2' => 'Patient Contact Data',
        'data_ex2' => 'Phone number, WhatsApp messages, call timestamps',
        'data_cat3' => 'Payment Data',
        'data_ex3' => 'Processed by Stripe — we do not store card numbers',
        'data_cat4' => 'Usage Data',
        'data_ex4' => 'Login times, feature usage, message counts',

        'legal_basis_title' => 'Legal Basis for Processing',
        'legal_basis_content' => 'We process personal data under the following legal bases (Article 6 GDPR):',
        'basis_contract' => 'Performance of contract',
        'basis_contract_desc' => 'Processing necessary to deliver the Recaller service to subscribing clinics.',
        'basis_legitimate' => 'Legitimate interest',
        'basis_legitimate_desc' => 'Analytics and service improvement, fraud prevention, and security.',
        'basis_consent' => 'Consent',
        'basis_consent_desc' => 'Marketing communications and optional cookies (you may withdraw consent at any time).',

        'processors_title' => 'Sub-processors',
        'processors_content' => 'We use the following third-party sub-processors to deliver our Service:',
        'processor_name' => 'Provider',
        'processor_purpose' => 'Purpose',
        'processor_location' => 'Location',
        'processor_twilio' => 'Voice calls and WhatsApp messaging',
        'processor_stripe' => 'Payment processing and billing',
        'processor_resend' => 'Transactional email delivery',
        'processor_hetzner' => 'Server hosting and data storage',
        'location_us' => 'United States (EU SCCs)',
        'location_eu' => 'European Union',
        'processors_safeguards' => 'For US-based processors, we rely on EU Standard Contractual Clauses (SCCs) to ensure adequate data protection.',

        'transfers_title' => 'International Data Transfers',
        'transfers_content' => 'Your data may be transferred to countries outside the EU/EEA. When this occurs, we ensure appropriate safeguards are in place, including EU Standard Contractual Clauses and adequacy decisions where available.',

        'retention_title' => 'Data Retention',
        'retention_content' => 'We retain personal data only as long as necessary:',
        'retention_active' => 'Account data: retained while your subscription is active',
        'retention_deleted' => 'After account deletion: data is erased within 30 days',
        'retention_logs' => 'Call and message logs: retained for 12 months after the last activity',

        'rights_title' => 'Your Rights Under GDPR',
        'rights_content' => 'As a data subject in the EU, you have the following rights:',
        'right_access' => 'Right of Access',
        'right_access_desc' => 'Request a copy of all personal data we hold about you.',
        'right_rectification' => 'Right to Rectification',
        'right_rectification_desc' => 'Request correction of inaccurate personal data.',
        'right_erasure' => 'Right to Erasure',
        'right_erasure_desc' => 'Request deletion of your personal data ("right to be forgotten").',
        'right_portability' => 'Right to Data Portability',
        'right_portability_desc' => 'Receive your data in a structured, machine-readable format.',
        'right_restriction' => 'Right to Restriction',
        'right_restriction_desc' => 'Request limitation of processing in certain circumstances.',
        'right_objection' => 'Right to Object',
        'right_objection_desc' => 'Object to processing based on legitimate interests.',
        'rights_exercise' => 'To exercise any of these rights, contact us at',

        'breach_title' => 'Data Breach Notification',
        'breach_content' => 'In the event of a personal data breach that poses a risk to your rights, we will notify the relevant supervisory authority within 72 hours and affected individuals without undue delay, as required by Articles 33 and 34 of the GDPR.',

        'dpo_title' => 'Contact & Data Protection',
        'dpo_content' => 'For any GDPR-related inquiries or to exercise your data rights, contact us at',
    ],

    'cookies' => [
        'title' => 'Cookie Policy',
        'summary' => 'Recaller uses minimal cookies that are strictly necessary for the platform to function. We do not use tracking or advertising cookies.',

        'what_title' => 'What Are Cookies',
        'what_content' => 'Cookies are small text files stored on your device when you visit a website. They help the site remember your preferences and session information.',

        'cookies_we_use_title' => 'Cookies We Use',
        'cookies_we_use_content' => 'We only use cookies that are strictly necessary for the platform to work:',
        'cookie_name' => 'Cookie',
        'cookie_purpose' => 'Purpose',
        'cookie_duration' => 'Duration',
        'cookie_session' => 'Session',
        'cookie_session_purpose' => 'Keeps you logged in and stores your preferences (language, CSRF protection)',
        'cookie_session_duration' => 'Until browser is closed or 2 hours of inactivity',
        'cookie_remember' => 'Remember me',
        'cookie_remember_purpose' => 'Keeps you logged in between browser sessions (only if you check "Remember me")',
        'cookie_remember_duration' => '30 days',

        'no_tracking_title' => 'No Tracking Cookies',
        'no_tracking_content' => 'Recaller does not use any analytics, tracking, advertising, or third-party cookies. We do not track you across websites and do not share browsing data with any third party.',

        'manage_title' => 'Managing Cookies',
        'manage_content' => 'Since we only use strictly necessary cookies, no consent banner is required under GDPR. You can delete cookies at any time through your browser settings, but this will log you out of the platform.',

        'contact_title' => 'Contact',
        'contact_content' => 'For questions about our cookie usage, contact us at',
    ],

    'dpa' => [
        'title' => 'Data Processing Agreement',
        'summary' => 'This DPA governs how Recaller (Processor) handles personal data on behalf of your clinic (Controller) under GDPR.',

        'parties_title' => 'Parties',
        'parties_content' => 'This Data Processing Agreement ("DPA") is entered into between:',
        'parties_controller' => 'The clinic subscribing to Recaller (the "Controller" or "Clinic")',
        'parties_processor' => 'Jackcode FZ-LLC, operating Recaller (the "Processor")',

        'scope_title' => 'Scope of Processing',
        'scope_content' => 'The Processor processes personal data solely to provide the Recaller service to the Controller. This includes:',
        'scope_item1' => 'Receiving and forwarding missed calls from patient phone numbers',
        'scope_item2' => 'Sending automated WhatsApp follow-up messages to patients on behalf of the clinic',
        'scope_item3' => 'Storing conversation history between the clinic and patients',
        'scope_item4' => 'Generating reports and analytics from call and messaging data',

        'data_types_title' => 'Types of Personal Data Processed',
        'data_types_content' => 'The following personal data is processed:',
        'data_type1' => 'Patient phone numbers (E.164 format)',
        'data_type2' => 'WhatsApp message content (sent and received)',
        'data_type3' => 'Call metadata (timestamps, duration, call status)',
        'data_type4' => 'Lead status and outcome information',

        'obligations_title' => 'Processor Obligations',
        'obligations_content' => 'The Processor agrees to:',
        'obligation1' => 'Process personal data only on documented instructions from the Controller',
        'obligation2' => 'Ensure that persons authorized to process data are bound by confidentiality',
        'obligation3' => 'Implement appropriate technical and organizational security measures',
        'obligation4' => 'Not engage another processor without prior written authorization from the Controller',
        'obligation5' => 'Assist the Controller in responding to data subject requests (access, erasure, portability)',
        'obligation6' => 'Delete or return all personal data upon termination of the service',
        'obligation7' => 'Make available all information necessary to demonstrate compliance with GDPR Article 28',

        'security_title' => 'Security Measures',
        'security_content' => 'The Processor implements the following security measures:',
        'security1' => 'Encryption of data in transit (TLS 1.2+) and at rest',
        'security2' => 'Access controls with role-based permissions',
        'security3' => 'Regular security updates and vulnerability monitoring',
        'security4' => 'Multi-tenant data isolation (each clinic can only access its own data)',
        'security5' => 'Automated backups with encrypted storage',

        'subprocessors_title' => 'Sub-processors',
        'subprocessors_content' => 'The Controller authorizes the use of the following sub-processors:',

        'breach_title' => 'Data Breach Notification',
        'breach_content' => 'The Processor will notify the Controller without undue delay (and no later than 48 hours) after becoming aware of a personal data breach. The notification will include the nature of the breach, categories of data affected, and measures taken.',

        'termination_title' => 'Termination and Data Deletion',
        'termination_content' => 'Upon termination of the service agreement, the Processor will delete all personal data within 30 days, unless retention is required by applicable law. The Controller may request a data export before termination.',

        'contact_title' => 'Contact',
        'contact_content' => 'For DPA-related inquiries, contact us at',
    ],

    'accept_terms' => 'I agree to the',
    'terms_link' => 'Terms of Service',
    'and' => 'and',
    'privacy_link' => 'Privacy Policy',
    'gdpr_link' => 'GDPR Compliance',
    'cookies_link' => 'Cookie Policy',
    'dpa_link' => 'Data Processing Agreement',
    'must_accept_terms' => 'You must accept the Terms of Service and Privacy Policy.',
];
