<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'New lead introduction',
                'subject' => 'Welcome to our services - Introduction',
                'type' => 'email',
                'content' => "Hi {client_name},\n\nThank you for reaching out to us. I wanted to formally introduce myself as your dedicated account representative.\n\nAt our company, we help clients build exceptional projects, and I noticed your interest in our solutions. I would love to learn more about your business objectives and how we can support you.\n\nDo you have 10 minutes for a brief introductory call this week?\n\nBest regards,\n{assigned_user}"
            ],
            [
                'name' => 'Follow-up after meeting',
                'subject' => 'Thank you for your time - Follow-up',
                'type' => 'email',
                'content' => "Hi {client_name},\n\nIt was a pleasure meeting with you earlier today. I appreciate you sharing your project goals and requirements with us.\n\nAs discussed, here are the key takeaways from our meeting:\n- Target goals and timelines\n- Key technology stack: {technology}\n- Next steps for both parties\n\nI am working on compiling our detailed proposal and will share it with you shortly. Please let me know if you have any questions in the meantime.\n\nBest regards,\n{assigned_user}"
            ],
            [
                'name' => 'Proposal submission',
                'subject' => 'Proposal Submission - Next Steps',
                'type' => 'email',
                'content' => "Hi {client_name},\n\nI am pleased to submit our formal proposal for your project: {project_link}\n\nThis proposal outlines our recommended approach, timeline, and pricing structure designed specifically to meet your needs. We are confident we can deliver a high-quality solution.\n\nPlease review the details and let me know if we can schedule a quick call to go over any questions you may have.\n\nBest regards,\n{assigned_user}"
            ],
            [
                'name' => 'Renewal reminder',
                'subject' => 'Upcoming Account/Service Renewal Reminder',
                'type' => 'email',
                'content' => "Hi {client_name},\n\nThis is a friendly reminder that your contract or service subscription is coming up for renewal soon.\n\nWe value our partnership and would love to continue supporting your business. To ensure uninterrupted service, please review the renewal details.\n\nIf you would like to discuss any updates to your plan or services, please feel free to reply directly to this message.\n\nBest regards,\n{assigned_user}"
            ],
            [
                'name' => 'Customer satisfaction check-in',
                'subject' => 'How are we doing? - Feedback Request',
                'type' => 'email',
                'content' => "Hi {client_name},\n\nWe hope you are enjoying your experience with our services. Your satisfaction is our top priority.\n\nCould you take a quick minute to share your feedback on our collaboration so far? We are always looking for ways to improve.\n\nIf you have any immediate concerns or need assistance, please let me know right away.\n\nBest regards,\n{assigned_user}"
            ],
            [
                'name' => 'Payment reminder',
                'subject' => 'Friendly Payment Reminder',
                'type' => 'email',
                'content' => "Hi {client_name},\n\nThis is a friendly reminder that invoice payment is due.\n\nPlease check your billing dashboard or the attached details to complete the payment. If you have already processed the payment, please disregard this message.\n\nThank you for your prompt attention to this matter.\n\nBest regards,\n{assigned_user}"
            ],
            [
                'name' => 'Re-engagement of inactive clients',
                'subject' => "We miss you! - Let's reconnect",
                'type' => 'email',
                'content' => "Hi {client_name},\n\nIt has been a while since we last spoke, and we wanted to check in and see how everything is going with your business.\n\nWe have recently introduced some exciting new updates and features that could help you optimize your workflows. I would love to catch up and see if there are new ways we can collaborate.\n\nLet me know if you are free for a quick catch-up call.\n\nBest regards,\n{assigned_user}"
            ],
        ];

        foreach ($templates as $t) {
            Template::updateOrCreate(['name' => $t['name']], $t);
        }
    }
}
