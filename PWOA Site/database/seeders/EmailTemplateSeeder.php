<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Welcome Email',
                'type' => 'welcome',
                'subject' => 'Welcome to PWOA!',
                'content' => '<h1>Welcome, {user_name}!</h1><p>Thank you for joining Pressure Washers of America (PWOA). We are thrilled to have you as part of our nationwide community of pressure washing professionals!</p><p>PWOA is dedicated to connecting, educating, and elevating industry experts like you. Here are a few things you can do right now to get started:</p><ul style="padding-left: 20px; margin-bottom: 20px;"><li>Complete your Business Listing profile to start gaining visibility.</li><li>Explore our Learning Center for training and quiz certifications.</li><li>Access exclusive member discounts on equipment and chemicals.</li></ul><p>To access your dashboard and manage your listing, please click the link below:</p><p style="text-align: center; margin: 30px 0;"><a href="{login_link}" style="display: inline-block; padding: 12px 24px; color: #ffffff; background-color: #0095d7; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 16px;">Access Your Account</a></p><p>If you have any questions, feel free to reach out to our support team.</p><p>Best regards,<br><strong>The PWOA Team</strong></p>',
                'available_variables' => ['{user_name}', '{login_link}'],
            ],
            [
                'name' => 'Promo Code Email',
                'type' => 'promo_code',
                'subject' => 'Your Exclusive Promo Code inside!',
                'content' => '<h1>Your Exclusive Promo Code Inside!</h1><p>Dear {user_name},</p><p>Thank you for choosing Pressure Washers of America (PWOA). We are excited to support your business journey and help you grow inside our professional community.</p><p>As a warm welcome, we are pleased to offer you an exclusive discount on your next membership purchase or transaction. Use the promo code below during checkout to claim your offer:</p><div style="background-color: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 20px; text-align: center; margin: 25px 0;"><span style="font-size: 14px; text-transform: uppercase; color: #64748b; display: block; margin-bottom: 5px; font-weight: bold;">Promo Code</span><strong style="font-size: 28px; color: #0f172a; letter-spacing: 2px;">{promo_code}</strong><span style="display: block; margin-top: 10px; font-size: 16px; color: #0284c7; font-weight: bold;">Save {discount_amount} Off Your Next Purchase!</span></div><p>This is a limited-time offer, so make sure to take advantage of it soon to unlock the full potential of your PWOA membership benefits, including verified business listings, quiz certifications, and exclusive member resources.</p><p>If you need any assistance applying your code, please don\'t hesitate to reach out to our dedicated support team.</p><p>Best regards,<br><strong>The PWOA Team</strong></p>',
                'available_variables' => ['{user_name}', '{promo_code}', '{discount_amount}'],
            ],
            [
                'name' => 'Business Approved Email',
                'type' => 'business_approved',
                'subject' => 'Your Business Profile has been Approved!',
                'content' => '<h1>Congratulations!</h1><p>Your business profile for {business_name} has been approved and is now live.</p>',
                'available_variables' => ['{user_name}', '{business_name}', '{profile_link}'],
            ],
            [
                'name' => 'Business Rejected Email',
                'type' => 'business_rejected',
                'subject' => 'Update on your Business Profile',
                'content' => '<h1>Action Required</h1><p>Unfortunately, your business profile for {business_name} could not be approved at this time. Reason: {rejection_reason}</p>',
                'available_variables' => ['{user_name}', '{business_name}', '{rejection_reason}'],
            ],
            [
                'name' => 'Certificate Issued Email',
                'type' => 'certificate_issued',
                'subject' => 'Congratulations on passing {course_name}!',
                'content' => '<h1>Certificate Issued!</h1><p>Congratulations {user_name}! You have successfully passed {course_name} with a score of {score}%.</p><p>Your Certificate Number is {certificate_number}.</p><p>We have also minted an NFT certificate directly into your wallet. You can view your certificate image here: <a href="{certificate_image_url}">{certificate_image_url}</a></p><p><strong>NFT Details:</strong><br>Token ID: {nft_token_id}<br>Transaction Hash: {nft_tx_hash}<br>Explorer Link: <a href="{xrpl_explorer_link}">View on XRPL Explorer</a></p>',
                'available_variables' => ['{user_name}', '{course_name}', '{certificate_number}', '{score}', '{certificate_image_url}', '{nft_token_id}', '{nft_tx_hash}', '{xrpl_explorer_link}'],
            ]
        ];

        foreach ($templates as $template) {
            \App\Models\EmailTemplate::updateOrCreate(
                ['type' => $template['type']],
                $template
            );
        }
    }
}
