<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateData extends Command
{
    protected $signature = 'data:migrate';
    protected $description = 'Migrate data from old MySQL database to new PostgreSQL database';

    public function handle()
    {
        if (!env('DB_DATABASE_OLD')) {
            $this->error('The DB_DATABASE_OLD environment variable is not set. Please set it in your .env file.');
            return 1;
        }

        try {
            DB::connection('mysql_old')->getPdo();
            $this->info('Successfully connected to the old MySQL database.');
        } catch (\Exception $e) {
            $this->error('Could not connect to the old MySQL database. Please check your DB_HOST_OLD, DB_PORT_OLD, DB_DATABASE_OLD, DB_USERNAME_OLD, and DB_PASSWORD_OLD in your .env file.');
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }

        $this->info('Starting data migration...');

        $tables = [
            'users',
            'roles',
            'permissions',
            'brands',
            'pages',
            'menus',
            'settings',
            'data_types',
            'translations',
            'cars',
            'gallery',
            'say_hello',
            'menu_items',
            'data_rows',
            'permission_role',
            'user_roles',
            'failed_jobs',
            'password_resets',
            'personal_access_tokens',
        ];

        DB::statement("SET session_replication_role = 'replica';");

        foreach ($tables as $table) {
            try {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                    $this->info('Truncated table: ' . $table);
                }

                $this->info("Migrating table: " . $table);
                $items = DB::connection('mysql_old')->table($table)->get();
                $this->info("Fetched " . $items->count() . " items from old database for table " . $table);

                foreach ($items as $item) {
                    $data = (array)$item;

                    // Handle specific columns for 'cars' table
                    if ($table === 'cars') {
                        if (!isset($data['variant_price']) || $data['variant_price'] === '') {
                            $data['variant_price'] = null;
                        }
                        if (!isset($data['body_html']) || $data['body_html'] === '') {
                            $data['body_html'] = null;
                        }
                        if (!isset($data['gallery']) || $data['gallery'] === '') {
                            $data['gallery'] = null;
                        }
                    }

                    // Handle specific columns for 'gallery' table
                    if ($table === 'gallery') {
                        if (!isset($data['image']) || $data['image'] === '') {
                            $data['image'] = null;
                        }
                        if (!isset($data['car_id']) || $data['car_id'] === '') {
                            $data['car_id'] = null;
                        }
                    }

                    DB::table($table)->insert($data);
                }
                $this->info("Migrated " . $items->count() . " items for table " . $table);
            } catch (\Exception $e) {
                $this->error("Error migrating table " . $table . ": " . $e->getMessage());
                // Continue to the next table even if one fails
            }
        }

        DB::statement("SET session_replication_role = 'origin';");

        $this->info('Data migration completed successfully!');
    }
}
