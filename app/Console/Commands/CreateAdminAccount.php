<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MongoDB\Client;
use Illuminate\Support\Facades\Hash;

class CreateAdminAccount extends Command
{
    protected $signature = 'create:admin-account';
    protected $description = 'Create admin accounts in MongoDB';

    public function handle()
    {
        $client = new Client(env('MONGODB_URI'));

        $adminCollection = $client->bullyproof->admins;

        $guidanceData = [
            'first_name' => 'Ryan',
            'last_name' => 'Corda',
            'username' => 'guidance-admin',
            'contact_number' => '091025987453',
            'email' => 'guidance-account@gmail.com',
            'password' => Hash::make('adminpassword'),
            'role' => 'guidance',
            'created_at' => new \MongoDB\BSON\UTCDateTime(),
        ];

        $disciplineData = [
            'first_name' => 'Shiloh',
            'last_name' => 'Eugenio',
            'username' => 'discipline-admin',
            'contact_number' => '09785621789',
            'email' => 'discipline-account@gmail.com',
            'password' => Hash::make('adminpassword'),
            'role' => 'discipline',
            'created_at' => new \MongoDB\BSON\UTCDateTime(),
        ];

        $superAdminData = [
            'first_name' => 'Paul Angelo',
            'last_name' => 'Derige',
            'username' => 'super-admin',
            'contact_number' => '09168759399',
            'email' => 'super-admin@gmail.com',
            'password' => Hash::make('adminpassword'),
            'role' => 'superadmin',
            'created_at' => new \MongoDB\BSON\UTCDateTime(),
        ];

        $adminCollection->insertOne($guidanceData);
        $adminCollection->insertOne($superAdminData);
        $adminCollection->insertOne($disciplineData);

        $this->info("Admin accounts for guidance and discipline created successfully!");
    }
}
