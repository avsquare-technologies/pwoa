<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'name' => 'Membership & Billing',
                'icon' => 'bi-credit-card',
                'faqs' => [
                    ['question' => 'What payment methods are accepted?', 'answer' => 'We accept all major credit cards (Visa, MasterCard, American Express) and ACH bank transfers through our secure Stripe-powered checkout.'],
                    ['question' => 'Can I upgrade my membership later?', 'answer' => 'Yes! You can upgrade from Standard to Gold at any time. The price difference will be prorated for the remainder of your billing cycle.'],
                    ['question' => 'Is there a refund policy?', 'answer' => 'We offer a full refund within the first 30 days of your membership. After that, you can cancel at any time and your access will remain active until the end of your billing period.'],
                    ['question' => 'How do I cancel my membership?', 'answer' => 'You can cancel your membership at any time from your account dashboard. Navigate to Settings → Membership and click "Cancel Membership". Your benefits continue until the end of your current billing cycle.'],
                ],
            ],
            [
                'name' => 'Events & Training',
                'icon' => 'bi-calendar-event',
                'faqs' => [
                    ['question' => 'Do members get event discounts?', 'answer' => 'Yes! Standard members receive 15% off all PWOA events, while Gold members enjoy 30% off plus priority seating and early access to registration.'],
                    ['question' => 'Are training courses included in membership?', 'answer' => 'Standard members get access to core educational content. Gold members receive unlimited access to all courses, certifications, and exclusive masterclass content.'],
                    ['question' => 'Can I earn certifications through PWOA?', 'answer' => 'Absolutely. Our Learning Center offers industry-recognized certifications. Complete the required coursework and pass the assessment to earn your PWOA Professional Certificate.'],
                ],
            ],
            [
                'name' => 'Directory & Listings',
                'icon' => 'bi-building',
                'faqs' => [
                    ['question' => 'How do I get my business listed in the directory?', 'answer' => 'Once you become a member, you can create your business profile from your dashboard. Your listing will appear in our Contractor or Vendor directory after a brief verification process.'],
                    ['question' => 'What is the difference between Standard and Gold directory listings?', 'answer' => 'Gold members receive priority placement in search results, a "Gold Member" badge on their profile, and enhanced listing features including banner images and featured positioning.'],
                    ['question' => 'Can I update my business listing?', 'answer' => 'Yes, you can update your business profile at any time from your member dashboard. Changes are reflected immediately across the directory.'],
                ],
            ],
            [
                'name' => 'Account & Support',
                'icon' => 'bi-person-circle',
                'faqs' => [
                    ['question' => 'How do I contact PWOA support?', 'answer' => 'You can reach our support team via the Contact page, by emailing support@pwoa.org, or through the live chat feature available to all members in their dashboard.'],
                    ['question' => 'I forgot my password. How do I reset it?', 'answer' => 'Click the "Forgot Password" link on the login page. Enter your email address and we will send you a secure link to reset your password within minutes.'],
                    ['question' => 'Is my personal information secure?', 'answer' => 'Yes. We use industry-standard encryption and security practices. Your payment information is processed through Stripe and never stored on our servers. We are fully compliant with data protection regulations.'],
                ],
            ],
        ];

        foreach ($data as $sortOrder => $categoryData) {
            $category = FaqCategory::firstOrCreate(
                ['slug' => Str::slug($categoryData['name'])],
                [
                    'name' => $categoryData['name'],
                    'icon' => $categoryData['icon'],
                    'sort_order' => $sortOrder,
                    'is_active' => true,
                ]
            );

            foreach ($categoryData['faqs'] as $faqOrder => $faqData) {
                Faq::firstOrCreate(
                    ['question' => $faqData['question']],
                    [
                        'faq_category_id' => $category->id,
                        'answer' => $faqData['answer'],
                        'sort_order' => $faqOrder,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
