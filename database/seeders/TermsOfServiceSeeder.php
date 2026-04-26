<?php

namespace Database\Seeders;

use App\Models\TermsOfService;
use Illuminate\Database\Seeder;

class TermsOfServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TermsOfService::create([
            'content' => '<h1>Welcome to Brag</h1><p>By using this platform, you agree to the following terms:</p><ul><li>Be respectful to other players.</li><li>No cheating or exploiting bugs.</li><li>Digital cards are virtual items with no real-world monetary value outside the platform.</li></ul><p>Enjoy the arena!</p>',
        ]);
    }
}
