<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'user_view', 'guard_name' => 'web', 'group_name' => 'User'],
            ['name' => 'user_create', 'guard_name' => 'web', 'group_name' => 'User'],
            ['name' => 'user_edit', 'guard_name' => 'web', 'group_name' => 'User'],
            ['name' => 'user_trash', 'guard_name' => 'web', 'group_name' => 'User'],
            ['name' => 'user_trash_view', 'guard_name' => 'web', 'group_name' => 'User'],
            ['name' => 'user_restore', 'guard_name' => 'web', 'group_name' => 'User'],

            ['name' => 'role_view', 'guard_name' => 'web', 'group_name' => 'Role'],
            ['name' => 'role_create', 'guard_name' => 'web', 'group_name' => 'Role'],
            ['name' => 'role_edit', 'guard_name' => 'web', 'group_name' => 'Role'],
            ['name' => 'role_trash', 'guard_name' => 'web', 'group_name' => 'Role'],
            ['name' => 'role_trash_view', 'guard_name' => 'web', 'group_name' => 'Role'],
            ['name' => 'role_restore', 'guard_name' => 'web', 'group_name' => 'Role'],

            ['name' => 'user_activity_view', 'guard_name' => 'web', 'group_name' => 'User Activity'],

            ['name' => 'client_view', 'guard_name' => 'web', 'group_name' => 'Client'],
            ['name' => 'client_create', 'guard_name' => 'web', 'group_name' => 'Client'],
            ['name' => 'client_edit', 'guard_name' => 'web', 'group_name' => 'Client'],
            ['name' => 'client_trash', 'guard_name' => 'web', 'group_name' => 'Client'],
            ['name' => 'client_trash_view', 'guard_name' => 'web', 'group_name' => 'Client'],
            ['name' => 'client_restore', 'guard_name' => 'web', 'group_name' => 'Client'],

            ['name' => 'booking_view', 'guard_name' => 'web', 'group_name' => 'Booking'],
            ['name' => 'booking_create', 'guard_name' => 'web', 'group_name' => 'Booking'],
            ['name' => 'booking_show', 'guard_name' => 'web', 'group_name' => 'Booking'],
            ['name' => 'booking_edit', 'guard_name' => 'web', 'group_name' => 'Booking'],
            ['name' => 'booking_trash', 'guard_name' => 'web', 'group_name' => 'Booking'],
            ['name' => 'booking_trash_view', 'guard_name' => 'web', 'group_name' => 'Booking'],
            ['name' => 'booking_restore', 'guard_name' => 'web', 'group_name' => 'Booking'],

            ['name' => 'expense_view', 'guard_name' => 'web', 'group_name' => 'Expense'],
            ['name' => 'expense_create', 'guard_name' => 'web', 'group_name' => 'Expense'],
            ['name' => 'expense_edit', 'guard_name' => 'web', 'group_name' => 'Expense'],
            ['name' => 'expense_trash', 'guard_name' => 'web', 'group_name' => 'Expense'],
            ['name' => 'expense_trash_view', 'guard_name' => 'web', 'group_name' => 'Expense'],
            ['name' => 'expense_restore', 'guard_name' => 'web', 'group_name' => 'Expense'],

            ['name' => 'expense_transaction_view', 'guard_name' => 'web', 'group_name' => 'Expense Transaction'],
            ['name' => 'expense_transaction_create', 'guard_name' => 'web', 'group_name' => 'Expense Transaction'],
            ['name' => 'expense_transaction_edit', 'guard_name' => 'web', 'group_name' => 'Expense Transaction'],
            ['name' => 'expense_transaction_trash', 'guard_name' => 'web', 'group_name' => 'Expense Transaction'],
            ['name' => 'expense_transaction_trash_view', 'guard_name' => 'web', 'group_name' => 'Expense Transaction'],
            ['name' => 'expense_transaction_restore', 'guard_name' => 'web', 'group_name' => 'Expense Transaction'],

            ['name' => 'client_Transactions_view', 'guard_name' => 'web', 'group_name' => 'Client Transactions'],
            ['name' => 'client_Transactions_create', 'guard_name' => 'web', 'group_name' => 'Client Transactions'],
            ['name' => 'client_Transactions_show', 'guard_name' => 'web', 'group_name' => 'Client Transactions'],
            ['name' => 'client_Transactions_edit', 'guard_name' => 'web', 'group_name' => 'Client Transactions'],
            ['name' => 'client_Transactions_trash', 'guard_name' => 'web', 'group_name' => 'Client Transactions'],
            ['name' => 'client_Transactions_trash_view', 'guard_name' => 'web', 'group_name' => 'Client Transactions'],
            ['name' => 'client_Transactions_restore', 'guard_name' => 'web', 'group_name' => 'Client Transactions'],

            ['name' => 'company_view', 'guard_name' => 'web', 'group_name' => 'Company'],
            ['name' => 'company_create', 'guard_name' => 'web', 'group_name' => 'Company'],
            ['name' => 'company_edit', 'guard_name' => 'web', 'group_name' => 'Company'],
            ['name' => 'company_trash', 'guard_name' => 'web', 'group_name' => 'Company'],
            ['name' => 'company_trash_view', 'guard_name' => 'web', 'group_name' => 'Company'],
            ['name' => 'company_restore', 'guard_name' => 'web', 'group_name' => 'Company'],

            ['name' => 'package_view', 'guard_name' => 'web', 'group_name' => 'Package'],
            ['name' => 'package_create', 'guard_name' => 'web', 'group_name' => 'Package'],
            ['name' => 'package_edit', 'guard_name' => 'web', 'group_name' => 'Package'],
            ['name' => 'package_trash', 'guard_name' => 'web', 'group_name' => 'Package'],
            ['name' => 'package_trash_view', 'guard_name' => 'web', 'group_name' => 'Package'],
            ['name' => 'package_restore', 'guard_name' => 'web', 'group_name' => 'Package'],

            ['name' => 'Ledger_Filter_view', 'guard_name' => 'web', 'group_name' => 'Ledger Filter'],

            ['name' => 'hotel_view', 'guard_name' => 'web', 'group_name' => 'Hotel'],
            ['name' => 'hotel_create', 'guard_name' => 'web', 'group_name' => 'Hotel'],
            ['name' => 'hotel_edit', 'guard_name' => 'web', 'group_name' => 'Hotel'],
            ['name' => 'hotel_trash', 'guard_name' => 'web', 'group_name' => 'Hotel'],
            ['name' => 'hotel_trash_view', 'guard_name' => 'web', 'group_name' => 'Hotel'],
            ['name' => 'hotel_restore', 'guard_name' => 'web', 'group_name' => 'Hotel'],

            ['name' => 'vehicle_view', 'guard_name' => 'web', 'group_name' => 'Vehicle'],
            ['name' => 'vehicle_create', 'guard_name' => 'web', 'group_name' => 'Vehicle'],
            ['name' => 'vehicle_edit', 'guard_name' => 'web', 'group_name' => 'Vehicle'],
            ['name' => 'vehicle_trash', 'guard_name' => 'web', 'group_name' => 'Vehicle'],
            ['name' => 'vehicle_trash_view', 'guard_name' => 'web', 'group_name' => 'Vehicle'],
            ['name' => 'vehicle_restore', 'guard_name' => 'web', 'group_name' => 'Vehicle'],

            ['name' => 'airline_view', 'guard_name' => 'web', 'group_name' => 'Airline'],
            ['name' => 'airline_create', 'guard_name' => 'web', 'group_name' => 'Airline'],
            ['name' => 'airline_edit', 'guard_name' => 'web', 'group_name' => 'Airline'],
            ['name' => 'airline_trash', 'guard_name' => 'web', 'group_name' => 'Airline'],
            ['name' => 'airline_trash_view', 'guard_name' => 'web', 'group_name' => 'Airline'],
            ['name' => 'airline_restore', 'guard_name' => 'web', 'group_name' => 'Airline'],

            ['name' => 'train_view', 'guard_name' => 'web', 'group_name' => 'Train'],
            ['name' => 'train_create', 'guard_name' => 'web', 'group_name' => 'Train'],
            ['name' => 'train_edit', 'guard_name' => 'web', 'group_name' => 'Train'],
            ['name' => 'train_trash', 'guard_name' => 'web', 'group_name' => 'Train'],
            ['name' => 'train_trash_view', 'guard_name' => 'web', 'group_name' => 'Train'],
            ['name' => 'train_restore', 'guard_name' => 'web', 'group_name' => 'Train'],
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(
                ['name' => $permission['name'], 'guard_name' => $permission['guard_name']],
                ['group_name' => $permission['group_name']]
            );
        }
    }
}
