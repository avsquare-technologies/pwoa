<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure categories exist
        $categories = [
            ['name' => 'Regional Conference', 'slug' => 'regional-conference'],
            ['name' => 'Technical Workshop', 'slug' => 'technical-workshop'],
            ['name' => 'Community Gathering', 'slug' => 'community-gathering'],
            ['name' => 'Webinar', 'slug' => 'webinar'],
            ['name' => 'Trade Show', 'slug' => 'trade-show'],
        ];

        foreach ($categories as $cat) {
            EventCategory::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        $catIds = EventCategory::pluck('id')->toArray();

        $eventImages = [
            'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1591115765373-5207764f72e7?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1475721027785-f74eccf877e2?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1523580494863-6f3031224c94?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1559223607-180d0c16c333?auto=format&fit=crop&w=800&q=80',
        ];

        $locations = [
            'Austin Convention Center, TX',
            'McCormick Place, Chicago, IL',
            'Javits Center, New York, NY',
            'Los Angeles Convention Center, CA',
            'Georgia World Congress Center, Atlanta, GA',
            'San Diego Convention Center, CA',
            'Orange County Convention Center, Orlando, FL',
            'Online / Virtual Event',
            'Nashville Music City Center, TN',
            'Dallas Convention Center, TX',
        ];

        $titles = [
            'PWOA Annual National Conference',
            'Advanced Surface Cleaning Workshop',
            'Fleet Washing Masterclass',
            'Water Reclaim & Compliance Summit',
            'New Member Networking Night',
            'Chemical Safety & OSHA Training',
            'Pressure Washing Business Growth Summit',
            'Equipment Innovation Expo',
            'Roof Cleaning Certification Boot Camp',
            'Southeast Regional Meetup',
            'Contractor Insurance & Liability Webinar',
            'Holiday Industry Mixer & Awards Gala',
            'Soft Wash Systems Deep Dive',
            'Marketing & Lead Generation for Washers',
            'Environmental Compliance Roundtable',
            'Spring Kickoff Conference',
            'Mid-Year Performance Review Webinar',
            'Safety Standards Update Workshop',
            'Vendor Partnership Showcase',
            'Year-End Industry Forecast Summit',
        ];

        $descriptions = [
            'Join industry professionals for an immersive day of learning, networking, and growth. Featuring keynote speakers, hands-on demonstrations, and vendor exhibits.',
            'A focused, hands-on workshop designed to elevate your technical skills with the latest cleaning methods and equipment innovations.',
            'Connect with fellow PWOA members, share best practices, and build lasting professional relationships in a relaxed setting.',
            'An intensive training session covering the latest OSHA regulations, safety protocols, and compliance requirements for the pressure washing industry.',
            'Explore cutting-edge equipment, chemicals, and technology from leading vendors in the pressure washing space.',
            'Expert-led sessions on growing your pressure washing business, improving profitability, and scaling operations effectively.',
            'Learn advanced techniques for roof cleaning, soft washing, and surface restoration from certified professionals.',
            'A virtual session covering critical updates to environmental regulations and water management best practices.',
            'Celebrate the year\'s achievements, recognize outstanding members, and enjoy an evening of networking and entertainment.',
            'Deep dive into marketing strategies, digital presence, and lead generation tactics specifically tailored for service businesses.',
        ];

        for ($i = 0; $i < 20; $i++) {
            $title = $titles[$i];
            $slug = Str::slug($title) . '-' . Str::random(4);

            // Mix of upcoming and past events
            if ($i < 12) {
                // Upcoming events spread over the next 6 months
                $startsAt = Carbon::now()->addDays(rand(5, 180))->setHour(rand(8, 10))->setMinute(0);
            } else {
                // Past events within the last 6 months
                $startsAt = Carbon::now()->subDays(rand(10, 180))->setHour(rand(8, 10))->setMinute(0);
            }

            $endsAt = (clone $startsAt)->addHours(rand(4, 8));
            $price = $i % 3 === 0 ? 0 : rand(25, 299);

            Event::firstOrCreate(
                ['slug' => $slug],
                [
                    'title' => $title,
                    'slug' => $slug,
                    'description' => '<p>' . $descriptions[$i % count($descriptions)] . '</p>',
                    'location' => $locations[$i % count($locations)],
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                    'capacity' => rand(50, 500),
                    'price' => $price,
                    'is_free_for_members' => $price > 0 && $i % 2 === 0,
                    'image_path' => $eventImages[$i % count($eventImages)],
                    'status' => 'published',
                    'event_category_id' => $catIds[array_rand($catIds)],
                ]
            );
        }
    }
}
