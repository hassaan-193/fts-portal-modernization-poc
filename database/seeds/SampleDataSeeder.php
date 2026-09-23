<?php

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\StafProfile;
use App\Models\Inquiry;
use App\Models\Project;
use App\Models\ProjectType;
use App\User;
use Carbon\Carbon;

class SampleDataSeeder extends Seeder
{
    public function run()
    {
        $admin = User::first();
        $adminId = $admin ? $admin->id : 1;

        // 1. Sample Companies / Clients
        $c1 = Company::firstOrCreate(['name' => 'Al Futtaim Engineering'], [
            'email' => 'projects@alfuttaim-sample.ae',
            'contact_person' => 'Ahmed Al-Maktoum',
            'contact_no' => '+971501234567',
            'vat_no' => '100234567800003',
            'location' => 'Dubai Silicon Oasis, Dubai, UAE',
            'billing_address' => 'Floor 12, Tower B, Business Bay, Dubai',
            'billing_contact_person' => 'Ahmed Al-Maktoum',
            'billing_email' => 'accounts@alfuttaim-sample.ae',
        ]);

        $c2 = Company::firstOrCreate(['name' => 'Emaar Properties PJSC'], [
            'email' => 'facilities@emaar-sample.ae',
            'contact_person' => 'Sarah Jenkins',
            'contact_no' => '+971509876543',
            'vat_no' => '100456789000003',
            'location' => 'Downtown Dubai, UAE',
            'billing_address' => 'Emaar Square, Bldg 3, Downtown Dubai',
            'billing_contact_person' => 'Sarah Jenkins',
            'billing_email' => 'billing@emaar-sample.ae',
        ]);

        $c3 = Company::firstOrCreate(['name' => 'Damac Properties'], [
            'email' => 'info@damac-sample.ae',
            'contact_person' => 'Khalid Mansoor',
            'contact_no' => '+971552233445',
            'vat_no' => '100987654300003',
            'location' => 'Damac Hills, Dubai, UAE',
            'billing_address' => 'Damac Executive Heights, Barsha Heights, Dubai',
            'billing_contact_person' => 'Khalid Mansoor',
            'billing_email' => 'finance@damac-sample.ae',
        ]);

        // 2. Sample Vendors
        Vendor::firstOrCreate(['name' => 'Hikvision Middle East'], [
            'email' => 'sales@hikvision-sample.ae',
            'contact_person' => 'Michael Chen',
            'contact_no' => '+97148881234',
            'vat_no' => '100111222300003',
            'location' => 'JAFZA South, Dubai',
            'payment_preference' => 'Transfer',
            'vendor_specialization' => 'CCTV and Surveillance',
        ]);

        Vendor::firstOrCreate(['name' => 'Ducab Cable Solutions'], [
            'email' => 'orders@ducab-sample.ae',
            'contact_person' => 'Rami Haddad',
            'contact_no' => '+97148885678',
            'vat_no' => '100222333400003',
            'location' => 'Industrial Area 1, Jebel Ali, Dubai',
            'payment_preference' => 'Cheque',
            'vendor_specialization' => 'Cables & Networking',
        ]);

        // 3. Sample Products
        $products = [
            ['name' => 'Hikvision 4MP IP Dome Camera (DS-2CD2143G2-I)', 'price' => 380.00],
            ['name' => 'Hikvision 16-Ch 4K NVR with PoE (DS-7616NI-I2/16P)', 'price' => 1450.00],
            ['name' => 'Schneider 42U Server Rack Cabinet 800x1000', 'price' => 2800.00],
            ['name' => 'Ducab Cat6 UTP Cable Drum 305m (LSZH)', 'price' => 420.00],
            ['name' => 'ZKTeco ProCapture-T Biometric Access Controller', 'price' => 890.00],
            ['name' => 'Cisco Catalyst 24-Port Gigabit PoE+ Switch', 'price' => 3200.00],
        ];

        foreach ($products as $p) {
            Product::firstOrCreate(['name' => $p['name']], ['price' => $p['price']]);
        }

        // 4. Sample Staff Profiles
        StafProfile::firstOrCreate(['name' => 'Tariq', 'last_name' => 'Mahmood'], [
            'staf_type' => 'Staff',
            'nationality' => 'Pakistani',
            'gender' => 'Male',
            'joining_date' => Carbon::now()->subYears(2)->toDateString(),
            'dob' => '1990-05-15',
            'passport_expiry' => Carbon::now()->addYears(2)->toDateString(),
            'visa_expiry' => Carbon::now()->addMonths(8)->toDateString(),
            'emirates_id_expiry' => Carbon::now()->addMonths(10)->toDateString(),
            'basic_salary' => 6000,
            'total_salary' => 8500,
            'overtime_rate' => 35.0,
            'mobile_no' => '+971501122334',
        ]);

        StafProfile::firstOrCreate(['name' => 'Rashid', 'last_name' => 'Khan'], [
            'staf_type' => 'Labor',
            'nationality' => 'Indian',
            'gender' => 'Male',
            'joining_date' => Carbon::now()->subYear()->toDateString(),
            'dob' => '1995-08-20',
            'passport_expiry' => Carbon::now()->addYear()->toDateString(),
            'visa_expiry' => Carbon::now()->addMonths(6)->toDateString(),
            'emirates_id_expiry' => Carbon::now()->addMonths(6)->toDateString(),
            'basic_salary' => 3000,
            'total_salary' => 4200,
            'overtime_rate' => 20.0,
            'mobile_no' => '+971559988776',
        ]);

        // 5. Sample Inquiries
        Inquiry::firstOrCreate(['inquiry_no' => 'INQ-2026-001'], [
            'created_by' => $adminId,
            'client_name' => 'Al Futtaim Engineering',
            'phone' => '+971501234567',
            'email' => 'projects@alfuttaim-sample.ae',
            'location' => 'Dubai Silicon Oasis HQ',
            'inquiry_type' => 'Project',
            'source' => 'Email',
            'expected_price' => 45000.00,
            'status' => 'New',
            'priority' => 'High',
            'follow_up_date' => Carbon::now()->addDays(3)->toDateString(),
            'expected_closing_date' => Carbon::now()->addMonth()->toDateString(),
            'notes' => 'Complete IP CCTV surveillance and access control setup for new facility wing.',
        ]);

        Inquiry::firstOrCreate(['inquiry_no' => 'INQ-2026-002'], [
            'created_by' => $adminId,
            'client_name' => 'Emaar Properties PJSC',
            'phone' => '+971509876543',
            'email' => 'facilities@emaar-sample.ae',
            'location' => 'Downtown Boulevard',
            'inquiry_type' => 'AMC',
            'source' => 'Referral',
            'expected_price' => 120000.00,
            'status' => 'Assigned',
            'priority' => 'Medium',
            'follow_up_date' => Carbon::now()->addDays(5)->toDateString(),
            'expected_closing_date' => Carbon::now()->addMonths(2)->toDateString(),
            'notes' => 'Annual Maintenance Contract for BMS and fire alarms across 3 residential towers.',
        ]);

        // 6. Sample Project
        $projectType = ProjectType::first();
        $projectTypeId = $projectType ? $projectType->id : 1;

        Project::firstOrCreate(['subject' => 'Burj View Tower CCTV Upgrade'], [
            'date' => Carbon::now()->subMonths(1)->toDateString(),
            'project_type_id' => $projectTypeId,
            'payment_terms' => '50% Advance, 40% on Delivery, 10% on Handover',
            'labour_charges' => 8500,
            'material_charges' => 24000,
            'project_source' => 'Direct Client',
            'project_estimation' => '32500',
            'user_id' => $adminId,
            'note' => 'Installation of 32 HD IP Cameras, 2 NVRs, and fiber optic backbone cable.',
            'category' => 'CCTV & Security',
            'visits' => 6,
        ]);
    }
}
