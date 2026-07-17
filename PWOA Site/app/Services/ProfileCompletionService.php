<?php

namespace App\Services;

use App\Models\Business;

class ProfileCompletionService
{
    /**
     * Calculate profile completion data for a business listing.
     */
    public function getCompletionData(Business $business): array
    {
        $type = $business->type;

        // Common sections
        $sections = [
            'company_info' => [
                'name' => 'Fill Company Information',
                'weight' => 20,
                'is_completed' => !empty($business->name) && !empty($business->email) && !empty($business->phone),
                'edit_step' => 1,
            ],
            'address' => [
                'name' => 'Add Business Address',
                'weight' => 10,
                'is_completed' => !empty($business->address) && !empty($business->city_id) && !empty($business->state_id) && !empty($business->zip),
                'edit_step' => 1,
            ],
            'description' => [
                'name' => 'Add Business Description',
                'weight' => 10,
                'is_completed' => !empty($business->description),
                'edit_step' => 2,
            ],
            'categories' => [
                'name' => 'Add Directory Categories',
                'weight' => 15,
                'is_completed' => $business->categories()->exists(),
                'edit_step' => 3,
            ],
            'logo' => [
                'name' => 'Upload Business Logo',
                'weight' => 10,
                'is_completed' => !empty($business->logo_path),
                'edit_step' => $type === 'vendor' ? 4 : 5,
            ],
            'cover_photo' => [
                'name' => 'Upload Cover Photo',
                'weight' => 10,
                'is_completed' => !empty($business->cover_photo_path),
                'edit_step' => $type === 'vendor' ? 4 : 5,
            ],
            'socials' => [
                'name' => 'Add Social Media Links',
                'weight' => 5,
                'is_completed' => !empty($business->facebook) || !empty($business->instagram) || !empty($business->linkedin) || !empty($business->youtube) || !empty($business->tiktok),
                'edit_step' => $type === 'vendor' ? 5 : 6,
            ],
        ];

        // Type-specific sections
        if ($type === 'contractor') {
            $sections['certifications'] = [
                'name' => 'Select Certifications & Fleet',
                'weight' => 10,
                // Consider it complete if they have equipment/certs, OR if they've at least filled contractor details
                'is_completed' => $business->directoryCertifications()->exists() || $business->directoryEquipments()->exists() || $business->contractorDetail()->exists(),
                'edit_step' => 4,
            ];
            $sections['service_info'] = [
                'name' => 'Specify Service Range',
                'weight' => 10,
                // service_radius_id is optional, so we just check if the contractorDetail record exists (meaning they visited and saved step 5/6)
                'is_completed' => $business->contractorDetail()->exists(),
                'edit_step' => 6,
            ];
        } else {
            // Vendor has Vendor Details & Features (20%)
            $sections['vendor_details'] = [
                'name' => 'Specify Vendor Features',
                'weight' => 20,
                // years_in_business is optional, so just check if the vendorDetail record exists
                'is_completed' => $business->vendorDetail()->exists(),
                'edit_step' => 5,
            ];
        }

        $totalPercentage = 0;
        $missingItems = [];
        $completedItems = [];
        $nextIncompleteEditStep = null;

        foreach ($sections as $key => $section) {
            if ($section['is_completed']) {
                $totalPercentage += $section['weight'];
                $completedItems[] = $section['name'];
            } else {
                $missingItems[] = [
                    'key' => $key,
                    'name' => $section['name'],
                    'edit_step' => $section['edit_step'],
                ];
                if ($nextIncompleteEditStep === null) {
                    $nextIncompleteEditStep = $section['edit_step'];
                }
            }
        }

        // Cap at 100% just in case of rounding/allocation anomalies
        $totalPercentage = min(100, $totalPercentage);

        // If all complete, next step defaults to step 1
        if ($nextIncompleteEditStep === null) {
            $nextIncompleteEditStep = 1;
        }

        // Strength labels: Incomplete, Good Progress, Strong Profile, Complete Profile
        if ($totalPercentage < 40) {
            $statusLabel = 'Incomplete';
            $statusColor = 'red';
            $colorHex = '#ef4444';
            $statusClass = 'danger';
        } elseif ($totalPercentage < 80) {
            $statusLabel = 'Good Progress';
            $statusColor = 'orange';
            $colorHex = '#f97316';
            $statusClass = 'warning';
        } elseif ($totalPercentage < 100) {
            $statusLabel = 'Strong Profile';
            $statusColor = 'blue';
            $colorHex = '#3b82f6';
            $statusClass = 'info';
        } else {
            $statusLabel = 'Complete Profile';
            $statusColor = 'green';
            $colorHex = '#22c55e';
            $statusClass = 'success';
        }

        return [
            'percentage' => $totalPercentage,
            'status_label' => $statusLabel,
            'status_color' => $statusColor,
            'color_hex' => $colorHex,
            'status_class' => $statusClass,
            'missing_items' => $missingItems,
            'completed_items' => $completedItems,
            'next_incomplete_edit_step' => $nextIncompleteEditStep,
            'sections' => $sections,
        ];
    }
}
