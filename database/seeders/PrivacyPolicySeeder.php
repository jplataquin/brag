<?php

namespace Database\Seeders;

use App\Models\PrivacyPolicy;
use Illuminate\Database\Seeder;

class PrivacyPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PrivacyPolicy::create([
            'content' => '<h1>Privacy Policy</h1><p>Welcome to Brag. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our platform.</p><h2>1. Information We Collect</h2><p>We collect information that you provide directly to us when you create an account, forge cards, or communicate with us.</p><h2>2. How We Use Your Information</h2><p>We use your information to provide, maintain, and improve our services, and to process transactions and send related information.</p><h2>3. Security</h2><p>We use reasonable measures to help protect information about you from loss, theft, misuse and unauthorized access.</p>',
        ]);
    }
}
