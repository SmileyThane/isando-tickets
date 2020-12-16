<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserSeeder::class);
         $this->call(CurrencySeeder::class);
        $this->call(PlanSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(TicketPrioritySeeder::class);
        $this->call(TicketStatusSeeder::class);
        $this->call(TicketTypesSeeder::class);
        $this->call(LanguageSeeder::class);
        $this->call(CountriesTableSeeder::class);
        $this->call(TimeZonesTableSeeder::class);
        $this->call(LanguagesTableSeeder::class);
        $this->call(CategorySeeder::class);
//        $this->call(AddressTypeSeeder::class);
//        $this->call(PhoneTypeSeeder::class);
//        $this->call(SocialTypeSeeder::class);
//        $this->call(TypesSeeder::class);

    }
}
