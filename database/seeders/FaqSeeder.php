<?php

namespace Database\Seeders;

use App\Models\Common\Faq;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Faq::create([
            'question' => 'What is the return policy?',
            'answer' => 'You can return any item within 30 days of purchase for a full refund.'
        ]);

        Faq::create([
            'question' => 'How can I track my order?',
            'answer' => 'You can track your order by logging into your account and visiting the "My Orders" section.'
        ]);
    }
}
