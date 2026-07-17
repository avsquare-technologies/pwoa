<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Lesson;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $tracks = [
            ['name' => 'Professional Pressure Washing', 'slug' => 'professional-pressure-washing', 'description' => 'Master the fundamentals and advanced techniques of professional pressure washing operations.'],
            ['name' => 'Business & Operations', 'slug' => 'business-operations', 'description' => 'Learn how to build, manage, and scale a successful pressure washing business.'],
            ['name' => 'Safety & Compliance', 'slug' => 'safety-compliance', 'description' => 'Stay compliant with OSHA, EPA, and local regulations through certified safety training.'],
            ['name' => 'Soft Wash Specialist', 'slug' => 'soft-wash-specialist', 'description' => 'Become a certified expert in soft wash systems, chemicals, and application methods.'],
        ];

        foreach ($tracks as $track) {
            CourseCategory::firstOrCreate(['slug' => $track['slug']], $track);
        }

        $thumbnails = [
            'https://images.unsplash.com/photo-1621905252507-b35492cc74b4?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1553877522-43269d4ea984?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=600&q=80',
        ];

        $courses = [
            [
                'title' => 'Introduction to Pressure Washing',
                'cat' => 'professional-pressure-washing',
                'hours' => 2,
                'mins' => 30,
                'free' => true,
                'desc' => '<p>A comprehensive introduction covering equipment basics, water flow dynamics, nozzle selection, and your first commercial job. Perfect for newcomers to the industry.</p>',
                'lessons' => ['Equipment Overview & Safety', 'Understanding PSI & GPM', 'Nozzle Types & Applications', 'Surface Assessment Techniques', 'Your First Job Walkthrough']
            ],

            [
                'title' => 'Surface Cleaning Mastery',
                'cat' => 'professional-pressure-washing',
                'hours' => 4,
                'mins' => 0,
                'free' => false,
                'desc' => '<p>Deep dive into surface cleaning for concrete, brick, wood, and composite materials. Learn chemical pre-treatment, dwell times, and post-treatment sealing.</p>',
                'lessons' => ['Concrete Cleaning Techniques', 'Brick & Stone Restoration', 'Wood Deck Cleaning & Brightening', 'Chemical Pre-Treatment Methods', 'Sealing & Post-Treatment']
            ],

            [
                'title' => 'Roof Cleaning Certification',
                'cat' => 'soft-wash-specialist',
                'hours' => 3,
                'mins' => 45,
                'free' => false,
                'desc' => '<p>Earn your roof cleaning certification. Covers shingle types, soft wash chemistry, application methods, safety harness protocols, and customer communication.</p>',
                'lessons' => ['Roof Types & Material Science', 'Soft Wash Chemical Mixing', 'Application Techniques & Equipment', 'Safety Harness & Fall Protection', 'Customer Inspection Reports']
            ],

            [
                'title' => 'Building a Profitable Wash Business',
                'cat' => 'business-operations',
                'hours' => 5,
                'mins' => 15,
                'free' => false,
                'desc' => '<p>From business registration to your first $100K year. Covers pricing strategies, insurance requirements, hiring your first crew, and fleet management.</p>',
                'lessons' => ['Business Structure & Registration', 'Insurance & Liability Coverage', 'Pricing Strategies That Work', 'Hiring & Training Your First Crew', 'Fleet Management & Routing', 'Scaling to Six Figures']
            ],

            [
                'title' => 'OSHA Safety Standards for Washers',
                'cat' => 'safety-compliance',
                'hours' => 2,
                'mins' => 0,
                'free' => true,
                'desc' => '<p>Essential safety training covering OSHA requirements, chemical handling, PPE selection, and emergency response protocols for pressure washing operations.</p>',
                'lessons' => ['OSHA Compliance Overview', 'Personal Protective Equipment (PPE)', 'Chemical Handling & SDS Sheets', 'Emergency Response Procedures']
            ],

            [
                'title' => 'Digital Marketing for Service Businesses',
                'cat' => 'business-operations',
                'hours' => 3,
                'mins' => 30,
                'free' => false,
                'desc' => '<p>Master Google Business Profile, social media, review management, and paid advertising to generate consistent leads for your washing business.</p>',
                'lessons' => ['Google Business Profile Optimization', 'Social Media Content Strategy', 'Review Management & Reputation', 'Facebook & Google Ads Setup', 'SEO for Local Service Businesses']
            ],

            [
                'title' => 'Soft Wash Systems Deep Dive',
                'cat' => 'soft-wash-specialist',
                'hours' => 4,
                'mins' => 30,
                'free' => false,
                'desc' => '<p>Complete guide to soft wash systems including pump selection, plumbing, chemical proportioning, and maintenance schedules for peak performance.</p>',
                'lessons' => ['Soft Wash vs. Pressure Washing', 'Pump Selection & Plumbing', 'Chemical Proportioning Systems', 'System Maintenance & Troubleshooting', 'Advanced Application Methods']
            ],

            [
                'title' => 'Water Reclaim & Environmental Compliance',
                'cat' => 'safety-compliance',
                'hours' => 2,
                'mins' => 45,
                'free' => false,
                'desc' => '<p>Navigate EPA regulations, local stormwater permits, and water reclaim systems. Essential for commercial contracts and government work.</p>',
                'lessons' => ['EPA Clean Water Act Overview', 'Stormwater Permits & Best Practices', 'Water Reclaim System Setup', 'Documentation & Compliance Records']
            ],

            [
                'title' => 'Fleet & Vehicle Washing Techniques',
                'cat' => 'professional-pressure-washing',
                'hours' => 3,
                'mins' => 0,
                'free' => false,
                'desc' => '<p>Specialized training for fleet washing including trucks, trailers, heavy equipment, and DOT compliance requirements for commercial clients.</p>',
                'lessons' => ['Fleet Washing Market Overview', 'Chemical Selection for Vehicles', 'Two-Step Washing Process', 'DOT Compliance Requirements', 'Landing Fleet Contracts']
            ],

            [
                'title' => 'Estimating & Bidding Commercial Jobs',
                'cat' => 'business-operations',
                'hours' => 2,
                'mins' => 15,
                'free' => true,
                'desc' => '<p>Learn to accurately estimate commercial jobs, create professional proposals, and win contracts with confidence. Includes real-world bid examples.</p>',
                'lessons' => ['Measuring & Calculating Square Footage', 'Cost Analysis & Profit Margins', 'Writing Winning Proposals', 'Contract Negotiation Tips']
            ],
        ];

        foreach ($courses as $i => $courseData) {
            $cat = CourseCategory::where('slug', $courseData['cat'])->first();

            $course = Course::firstOrCreate(
                ['slug' => Str::slug($courseData['title'])],
                [
                    'title' => $courseData['title'],
                    'slug' => Str::slug($courseData['title']),
                    'description' => $courseData['desc'],
                    'thumbnail_path' => $thumbnails[$i % count($thumbnails)],
                    'is_published' => true,
                    'course_category_id' => $cat?->id,
                    'is_free' => $courseData['free'],
                    'duration_hours' => $courseData['hours'],
                    'duration_minutes' => $courseData['mins'],
                ]
            );

            foreach ($courseData['lessons'] as $order => $lessonTitle) {
                Lesson::firstOrCreate(
                    ['course_id' => $course->id, 'slug' => Str::slug($lessonTitle)],
                    [
                        'title' => $lessonTitle,
                        'slug' => Str::slug($lessonTitle),
                        'content' => '<p>This lesson covers the key concepts and practical applications of ' . $lessonTitle . '. Follow along with the video content and complete the practice exercises before moving on.</p><p>Take notes on the key takeaways and be prepared for the quiz at the end of this course.</p>',
                        'order' => $order,
                        'is_restricted' => !$courseData['free'] && $order > 1,
                    ]
                );
            }
        }
    }
}
