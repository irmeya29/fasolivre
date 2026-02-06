<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        // Base: 2000/mois
        // 6 mois: -15% => 12,000 -> 10,200
        // 1 an : -30% => 24,000 -> 16,800

        $plans = [
            [
                'name' => 'Mensuel',
                'description' => 'Accès à tous les livres “Abonnement” pendant 30 jours.',
                'price' => 2000,
                'currency' => 'XOF',
                'duration_days' => 30,
                'is_active' => true,
            ],
            [
                'name' => '6 mois',
                'description' => 'Accès pendant 180 jours. Économie ~15%.',
                'price' => 10200,
                'currency' => 'XOF',
                'duration_days' => 180,
                'is_active' => true,
            ],
            [
                'name' => 'Annuel',
                'description' => 'Accès pendant 365 jours. Économie ~30%.',
                'price' => 16800,
                'currency' => 'XOF',
                'duration_days' => 365,
                'is_active' => true,
            ],
        ];

        foreach ($plans as $p) {
            SubscriptionPlan::updateOrCreate(
                ['name' => $p['name']],
                $p
            );
        }
    }
}
