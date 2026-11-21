<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class LeadBulkSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Total records to generate
        $totalRecords = 200000;

        // Insert records in batches
        $batchSize = 1000;

        // Fetch existing foreign key IDs
        $organizations = DB::table('organizations')->pluck('id')->toArray();
        $users = DB::table('users')->pluck('id')->toArray();
        $teams = DB::table('teams')->pluck('id')->toArray();
        $leadSources = DB::table('lead_sources')->pluck('id')->toArray();
        $leadStatuses = DB::table('lead_statuses')->pluck('id')->toArray();

        if (
            empty($organizations) ||
            empty($users) ||
            empty($teams) ||
            empty($leadSources) ||
            empty($leadStatuses)
        ) {
            throw new \RuntimeException(
                'Required organization, user, team, source or status records are missing.'
            );
        }

        $batch = [];

        for ($i = 1; $i <= $totalRecords; $i++) {

            $batch[] = [
                'organization_id' => $faker->randomElement($organizations),
                'assigned_user_id' => $faker->randomElement($users),
                'team_id' => $faker->randomElement($teams),
                'lead_source_id' => $faker->randomElement($leadSources),
                'lead_status_id' => $faker->randomElement($leadStatuses),

                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'company' => $faker->company,
                'designation' => $faker->jobTitle,

                'email' => "loadtest+{$i}@example.test",
                'secondary_email' => $faker->optional()->safeEmail,

                'phone' => $faker->phoneNumber,
                'mobile' => $faker->optional()->phoneNumber,
                'website' => $faker->optional()->url,

                'industry' => $faker->randomElement([
                    'Technology',
                    'Healthcare',
                    'Finance',
                    'Manufacturing',
                    'Education',
                    'Retail',
                    'Telecommunications',
                    'Real Estate',
                    'Automotive',
                    'Media'
                ]),

                'annual_revenue' => $faker->randomFloat(2, 10000, 100000000),
                'employee_count' => $faker->numberBetween(1, 50000),

                'rating' => $faker->randomElement([
                    'Hot', 'Warm', 'Cold'
                ]),

                'street' => $faker->streetAddress,
                'city' => $faker->city,
                'state' => $faker->state,
                'postal_code' => $faker->postcode,
                'country' => $faker->country,

                'description' => $faker->optional()->paragraph,

                'is_converted' => 0,
                'converted_at' => null,
                'converted_contact_id' => null,
                'converted_account_id' => null,
                'converted_deal_id' => null,

                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ];

            // Insert every 1000 records
            if (count($batch) >= $batchSize) {
                DB::table('leads')->insert($batch);

                $batch = [];

                echo "Inserted {$i} leads\n";
            }
        }

        // Insert remaining records
        if (!empty($batch)) {
            DB::table('leads')->insert($batch);
        }

        echo "Successfully generated {$totalRecords} fake leads!\n";
    }
}
