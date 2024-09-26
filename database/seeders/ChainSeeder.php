<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Chain;

class ChainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define initial chains data
        $chains = [
            ['name' => 'Aeon', 'logo_url' => 'https://example.com/logos/aeon.png', 'contact_info' => 'info@aeon.com'],
            ['name' => 'Fresco', 'logo_url' => 'https://example.com/logos/fresco.png', 'contact_info' => 'info@fresco.com'],
            ['name' => 'Life', 'logo_url' => 'https://example.com/logos/life.png', 'contact_info' => 'info@life.com'],
                ['name' => 'Maruetsu', 'logo_url' => 'https://example.com/logos/maruetsu.png', 'contact_info' => 'info@maruetsu.com'],
                ['name' => 'Coop', 'logo_url' => 'https://example.com/logos/coop.png', 'contact_info' => 'info@coop.com'],
                ['name' => 'Independent Store', 'logo_url' => null, 'contact_info' => null], // Example of an independent store
            ];
    
            // Insert chains data into the chains table
            foreach ($chains as $chain) {
                Chain::updateOrCreate(
                    ['name' => $chain['name']], // Unique key to check
                    ['logo_url' => $chain['logo_url'], 'contact_info' => $chain['contact_info']] // Data to insert or update
                );
            }
    
            $this->command->info('ChainSeeder: Seeded chains data.');
        }
    }
    