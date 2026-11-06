<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // ── 1. USER MANAGEMENT ──────────────────────────────
            ['name' => 'user_view', 'guard_name' => 'web', 'group_name' => 'User'],
            ['name' => 'user_create', 'guard_name' => 'web', 'group_name' => 'User'],
            ['name' => 'user_edit', 'guard_name' => 'web', 'group_name' => 'User'],
            ['name' => 'user_trash', 'guard_name' => 'web', 'group_name' => 'User'],
            ['name' => 'user_trash_view', 'guard_name' => 'web', 'group_name' => 'User'],
            ['name' => 'user_restore', 'guard_name' => 'web', 'group_name' => 'User'],

            // ── 2. ROLE MANAGEMENT ──────────────────────────────
            ['name' => 'role_view', 'guard_name' => 'web', 'group_name' => 'Role'],
            ['name' => 'role_create', 'guard_name' => 'web', 'group_name' => 'Role'],
            ['name' => 'role_edit', 'guard_name' => 'web', 'group_name' => 'Role'],
            ['name' => 'role_trash', 'guard_name' => 'web', 'group_name' => 'Role'],
            ['name' => 'role_trash_view', 'guard_name' => 'web', 'group_name' => 'Role'],
            ['name' => 'role_restore', 'guard_name' => 'web', 'group_name' => 'Role'],

            // ── 3. USER ACTIVITY ────────────────────────────────
            ['name' => 'user_activity_view', 'guard_name' => 'web', 'group_name' => 'User Activity'],

            // ── 4. LEAD MANAGEMENT ──────────────────────────────
            ['name' => 'lead_view', 'guard_name' => 'web', 'group_name' => 'Lead'],
            ['name' => 'lead_create', 'guard_name' => 'web', 'group_name' => 'Lead'],
            ['name' => 'lead_show', 'guard_name' => 'web', 'group_name' => 'Lead'],
            ['name' => 'lead_edit', 'guard_name' => 'web', 'group_name' => 'Lead'],
            ['name' => 'lead_trash', 'guard_name' => 'web', 'group_name' => 'Lead'],
            ['name' => 'lead_trash_view', 'guard_name' => 'web', 'group_name' => 'Lead'],
            ['name' => 'lead_restore', 'guard_name' => 'web', 'group_name' => 'Lead'],
            ['name' => 'lead_followup', 'guard_name' => 'web', 'group_name' => 'Lead'],

            // ── 5. CLIENT MANAGEMENT ────────────────────────────
            ['name' => 'client_view', 'guard_name' => 'web', 'group_name' => 'Client'],
            ['name' => 'client_create', 'guard_name' => 'web', 'group_name' => 'Client'],
            ['name' => 'client_show', 'guard_name' => 'web', 'group_name' => 'Client'],
            ['name' => 'client_edit', 'guard_name' => 'web', 'group_name' => 'Client'],
            ['name' => 'client_trash', 'guard_name' => 'web', 'group_name' => 'Client'],
            ['name' => 'client_trash_view', 'guard_name' => 'web', 'group_name' => 'Client'],
            ['name' => 'client_restore', 'guard_name' => 'web', 'group_name' => 'Client'],

            // ── 6. COMPANY MANAGEMENT ───────────────────────────
            ['name' => 'company_view', 'guard_name' => 'web', 'group_name' => 'Company'],
            ['name' => 'company_create', 'guard_name' => 'web', 'group_name' => 'Company'],
            ['name' => 'company_show', 'guard_name' => 'web', 'group_name' => 'Company'],
            ['name' => 'company_edit', 'guard_name' => 'web', 'group_name' => 'Company'],
            ['name' => 'company_trash', 'guard_name' => 'web', 'group_name' => 'Company'],
            ['name' => 'company_trash_view', 'guard_name' => 'web', 'group_name' => 'Company'],
            ['name' => 'company_restore', 'guard_name' => 'web', 'group_name' => 'Company'],

            // ── 7. SUPPLIERS & VENDORS ──────────────────────────
            ['name' => 'supplier_view', 'guard_name' => 'web', 'group_name' => 'Supplier'],
            ['name' => 'supplier_create', 'guard_name' => 'web', 'group_name' => 'Supplier'],
            ['name' => 'supplier_show', 'guard_name' => 'web', 'group_name' => 'Supplier'],
            ['name' => 'supplier_edit', 'guard_name' => 'web', 'group_name' => 'Supplier'],
            ['name' => 'supplier_trash', 'guard_name' => 'web', 'group_name' => 'Supplier'],
            ['name' => 'supplier_trash_view', 'guard_name' => 'web', 'group_name' => 'Supplier'],
            ['name' => 'supplier_restore', 'guard_name' => 'web', 'group_name' => 'Supplier'],

            // ── 8. BOOKING MANAGEMENT ───────────────────────────
            ['name' => 'booking_view', 'guard_name' => 'web', 'group_name' => 'Booking'],
            ['name' => 'booking_create', 'guard_name' => 'web', 'group_name' => 'Booking'],
            ['name' => 'booking_show', 'guard_name' => 'web', 'group_name' => 'Booking'],
            ['name' => 'booking_edit', 'guard_name' => 'web', 'group_name' => 'Booking'],
            ['name' => 'booking_trash', 'guard_name' => 'web', 'group_name' => 'Booking'],
            ['name' => 'booking_trash_view', 'guard_name' => 'web', 'group_name' => 'Booking'],
            ['name' => 'booking_restore', 'guard_name' => 'web', 'group_name' => 'Booking'],

            // ── 9. PACKAGES ─────────────────────────────────────
            ['name' => 'package_view', 'guard_name' => 'web', 'group_name' => 'Package'],
            ['name' => 'package_create', 'guard_name' => 'web', 'group_name' => 'Package'],
            ['name' => 'package_show', 'guard_name' => 'web', 'group_name' => 'Package'],
            ['name' => 'package_edit', 'guard_name' => 'web', 'group_name' => 'Package'],
            ['name' => 'package_trash', 'guard_name' => 'web', 'group_name' => 'Package'],
            ['name' => 'package_trash_view', 'guard_name' => 'web', 'group_name' => 'Package'],
            ['name' => 'package_restore', 'guard_name' => 'web', 'group_name' => 'Package'],

            // ── 10. HOTELS ──────────────────────────────────────
            ['name' => 'hotel_view', 'guard_name' => 'web', 'group_name' => 'Hotel'],
            ['name' => 'hotel_create', 'guard_name' => 'web', 'group_name' => 'Hotel'],
            ['name' => 'hotel_show', 'guard_name' => 'web', 'group_name' => 'Hotel'],
            ['name' => 'hotel_edit', 'guard_name' => 'web', 'group_name' => 'Hotel'],
            ['name' => 'hotel_trash', 'guard_name' => 'web', 'group_name' => 'Hotel'],
            ['name' => 'hotel_trash_view', 'guard_name' => 'web', 'group_name' => 'Hotel'],
            ['name' => 'hotel_restore', 'guard_name' => 'web', 'group_name' => 'Hotel'],

            // ── 11. ROOM TYPES ──────────────────────────────────
            ['name' => 'room_type_view', 'guard_name' => 'web', 'group_name' => 'Room Type'],
            ['name' => 'room_type_create', 'guard_name' => 'web', 'group_name' => 'Room Type'],
            ['name' => 'room_type_edit', 'guard_name' => 'web', 'group_name' => 'Room Type'],
            ['name' => 'room_type_trash', 'guard_name' => 'web', 'group_name' => 'Room Type'],
            ['name' => 'room_type_trash_view', 'guard_name' => 'web', 'group_name' => 'Room Type'],
            ['name' => 'room_type_restore', 'guard_name' => 'web', 'group_name' => 'Room Type'],

            // ── 12. FLIGHTS ─────────────────────────────────────
            ['name' => 'flight_view', 'guard_name' => 'web', 'group_name' => 'Flight'],
            ['name' => 'flight_create', 'guard_name' => 'web', 'group_name' => 'Flight'],
            ['name' => 'flight_show', 'guard_name' => 'web', 'group_name' => 'Flight'],
            ['name' => 'flight_edit', 'guard_name' => 'web', 'group_name' => 'Flight'],
            ['name' => 'flight_trash', 'guard_name' => 'web', 'group_name' => 'Flight'],
            ['name' => 'flight_trash_view', 'guard_name' => 'web', 'group_name' => 'Flight'],
            ['name' => 'flight_restore', 'guard_name' => 'web', 'group_name' => 'Flight'],

            // ── 13. AIRLINES ────────────────────────────────────
            ['name' => 'airline_view', 'guard_name' => 'web', 'group_name' => 'Airline'],
            ['name' => 'airline_create', 'guard_name' => 'web', 'group_name' => 'Airline'],
            ['name' => 'airline_edit', 'guard_name' => 'web', 'group_name' => 'Airline'],
            ['name' => 'airline_trash', 'guard_name' => 'web', 'group_name' => 'Airline'],
            ['name' => 'airline_trash_view', 'guard_name' => 'web', 'group_name' => 'Airline'],
            ['name' => 'airline_restore', 'guard_name' => 'web', 'group_name' => 'Airline'],

            // ── 14. ROUTES ──────────────────────────────────────
            ['name' => 'route_view', 'guard_name' => 'web', 'group_name' => 'Route'],
            ['name' => 'route_create', 'guard_name' => 'web', 'group_name' => 'Route'],
            ['name' => 'route_edit', 'guard_name' => 'web', 'group_name' => 'Route'],
            ['name' => 'route_trash', 'guard_name' => 'web', 'group_name' => 'Route'],
            ['name' => 'route_trash_view', 'guard_name' => 'web', 'group_name' => 'Route'],
            ['name' => 'route_restore', 'guard_name' => 'web', 'group_name' => 'Route'],

            // ── 15. TRAVEL ROUTES ───────────────────────────────
            ['name' => 'travel_route_view', 'guard_name' => 'web', 'group_name' => 'Travel Route'],
            ['name' => 'travel_route_create', 'guard_name' => 'web', 'group_name' => 'Travel Route'],
            ['name' => 'travel_route_show', 'guard_name' => 'web', 'group_name' => 'Travel Route'],
            ['name' => 'travel_route_edit', 'guard_name' => 'web', 'group_name' => 'Travel Route'],
            ['name' => 'travel_route_trash', 'guard_name' => 'web', 'group_name' => 'Travel Route'],
            ['name' => 'travel_route_trash_view', 'guard_name' => 'web', 'group_name' => 'Travel Route'],
            ['name' => 'travel_route_restore', 'guard_name' => 'web', 'group_name' => 'Travel Route'],

            // ── 16. TRAINS ──────────────────────────────────────
            ['name' => 'train_view', 'guard_name' => 'web', 'group_name' => 'Train'],
            ['name' => 'train_create', 'guard_name' => 'web', 'group_name' => 'Train'],
            ['name' => 'train_edit', 'guard_name' => 'web', 'group_name' => 'Train'],
            ['name' => 'train_trash', 'guard_name' => 'web', 'group_name' => 'Train'],
            ['name' => 'train_trash_view', 'guard_name' => 'web', 'group_name' => 'Train'],
            ['name' => 'train_restore', 'guard_name' => 'web', 'group_name' => 'Train'],

            // ── 17. VEHICLES ────────────────────────────────────
            ['name' => 'vehicle_view', 'guard_name' => 'web', 'group_name' => 'Vehicle'],
            ['name' => 'vehicle_create', 'guard_name' => 'web', 'group_name' => 'Vehicle'],
            ['name' => 'vehicle_edit', 'guard_name' => 'web', 'group_name' => 'Vehicle'],
            ['name' => 'vehicle_trash', 'guard_name' => 'web', 'group_name' => 'Vehicle'],
            ['name' => 'vehicle_trash_view', 'guard_name' => 'web', 'group_name' => 'Vehicle'],
            ['name' => 'vehicle_restore', 'guard_name' => 'web', 'group_name' => 'Vehicle'],

            // ── 18. TRAINING SESSIONS ───────────────────────────
            ['name' => 'training_session_view', 'guard_name' => 'web', 'group_name' => 'Training Session'],
            ['name' => 'training_session_create', 'guard_name' => 'web', 'group_name' => 'Training Session'],
            ['name' => 'training_session_edit', 'guard_name' => 'web', 'group_name' => 'Training Session'],
            ['name' => 'training_session_trash', 'guard_name' => 'web', 'group_name' => 'Training Session'],

            // ── 19. EXPENSES ────────────────────────────────────
            ['name' => 'expense_view', 'guard_name' => 'web', 'group_name' => 'Expense'],
            ['name' => 'expense_create', 'guard_name' => 'web', 'group_name' => 'Expense'],
            ['name' => 'expense_edit', 'guard_name' => 'web', 'group_name' => 'Expense'],
            ['name' => 'expense_trash', 'guard_name' => 'web', 'group_name' => 'Expense'],
            ['name' => 'expense_trash_view', 'guard_name' => 'web', 'group_name' => 'Expense'],
            ['name' => 'expense_restore', 'guard_name' => 'web', 'group_name' => 'Expense'],

            // ── 20. EXPENSE TRANSACTIONS & FILTER ───────────────
            ['name' => 'expense_transaction_view', 'guard_name' => 'web', 'group_name' => 'Expense Transaction'],
            ['name' => 'expense_transaction_create', 'guard_name' => 'web', 'group_name' => 'Expense Transaction'],
            ['name' => 'expense_transaction_edit', 'guard_name' => 'web', 'group_name' => 'Expense Transaction'],
            ['name' => 'expense_transaction_trash', 'guard_name' => 'web', 'group_name' => 'Expense Transaction'],
            ['name' => 'expense_transaction_trash_view', 'guard_name' => 'web', 'group_name' => 'Expense Transaction'],
            ['name' => 'expense_transaction_restore', 'guard_name' => 'web', 'group_name' => 'Expense Transaction'],
            ['name' => 'expense_filter_view', 'guard_name' => 'web', 'group_name' => 'Expense Transaction'],

            // ── 21. CLIENT & COMPANY TRANSACTIONS ───────────────
            ['name' => 'client_Transactions_view', 'guard_name' => 'web', 'group_name' => 'Client Transactions'],
            ['name' => 'client_Transactions_create', 'guard_name' => 'web', 'group_name' => 'Client Transactions'],
            ['name' => 'client_Transactions_show', 'guard_name' => 'web', 'group_name' => 'Client Transactions'],
            ['name' => 'client_Transactions_edit', 'guard_name' => 'web', 'group_name' => 'Client Transactions'],
            ['name' => 'client_Transactions_trash', 'guard_name' => 'web', 'group_name' => 'Client Transactions'],
            ['name' => 'client_Transactions_trash_view', 'guard_name' => 'web', 'group_name' => 'Client Transactions'],
            ['name' => 'client_Transactions_restore', 'guard_name' => 'web', 'group_name' => 'Client Transactions'],
            ['name' => 'company_ledger_filter_view', 'guard_name' => 'web', 'group_name' => 'Client Transactions'],
            ['name' => 'Ledger_Filter_view', 'guard_name' => 'web', 'group_name' => 'Ledger Filter'],

            // ── 22. ARRIVAL GROUPS ──────────────────────────────
            ['name' => 'arrival_group_view', 'guard_name' => 'web', 'group_name' => 'Arrival Group'],
            ['name' => 'arrival_group_create', 'guard_name' => 'web', 'group_name' => 'Arrival Group'],
            ['name' => 'arrival_group_show', 'guard_name' => 'web', 'group_name' => 'Arrival Group'],
            ['name' => 'arrival_group_edit', 'guard_name' => 'web', 'group_name' => 'Arrival Group'],
            ['name' => 'arrival_group_trash', 'guard_name' => 'web', 'group_name' => 'Arrival Group'],

            // ── 23. DEPARTURE GROUPS ────────────────────────────
            ['name' => 'departure_group_view', 'guard_name' => 'web', 'group_name' => 'Departure Group'],
            ['name' => 'departure_group_create', 'guard_name' => 'web', 'group_name' => 'Departure Group'],
            ['name' => 'departure_group_show', 'guard_name' => 'web', 'group_name' => 'Departure Group'],
            ['name' => 'departure_group_edit', 'guard_name' => 'web', 'group_name' => 'Departure Group'],
            ['name' => 'departure_group_trash', 'guard_name' => 'web', 'group_name' => 'Departure Group'],

            // ── 24. HAJI PILGRIM GROUPS ─────────────────────────
            ['name' => 'haji_group_view', 'guard_name' => 'web', 'group_name' => 'Haji Group'],
            ['name' => 'haji_group_create', 'guard_name' => 'web', 'group_name' => 'Haji Group'],
            ['name' => 'haji_group_show', 'guard_name' => 'web', 'group_name' => 'Haji Group'],
            ['name' => 'haji_group_edit', 'guard_name' => 'web', 'group_name' => 'Haji Group'],
            ['name' => 'haji_group_trash', 'guard_name' => 'web', 'group_name' => 'Haji Group'],
            ['name' => 'haji_group_split', 'guard_name' => 'web', 'group_name' => 'Haji Group'],

            // ── 25. ROOM INVENTORY & STOCK ──────────────────────
            ['name' => 'room_inventory_view', 'guard_name' => 'web', 'group_name' => 'Room Inventory'],
            ['name' => 'room_inventory_create', 'guard_name' => 'web', 'group_name' => 'Room Inventory'],
            ['name' => 'room_inventory_edit', 'guard_name' => 'web', 'group_name' => 'Room Inventory'],
            ['name' => 'room_inventory_trash', 'guard_name' => 'web', 'group_name' => 'Room Inventory'],

            // ── 26. HOTEL SALE INVOICES ─────────────────────────
            ['name' => 'hotel_sale_view', 'guard_name' => 'web', 'group_name' => 'Hotel Sale'],
            ['name' => 'hotel_sale_create', 'guard_name' => 'web', 'group_name' => 'Hotel Sale'],
            ['name' => 'hotel_sale_show', 'guard_name' => 'web', 'group_name' => 'Hotel Sale'],
            ['name' => 'hotel_sale_edit', 'guard_name' => 'web', 'group_name' => 'Hotel Sale'],
            ['name' => 'hotel_sale_trash', 'guard_name' => 'web', 'group_name' => 'Hotel Sale'],

            // ── 27. HAJJ APPLICATIONS ───────────────────────────
            ['name' => 'hajj_application_view', 'guard_name' => 'web', 'group_name' => 'Hajj Application'],
            ['name' => 'hajj_application_show', 'guard_name' => 'web', 'group_name' => 'Hajj Application'],
            ['name' => 'hajj_application_edit', 'guard_name' => 'web', 'group_name' => 'Hajj Application'],
            ['name' => 'hajj_application_trash', 'guard_name' => 'web', 'group_name' => 'Hajj Application'],

            // ── 28. REPORTS & MANIFESTS ─────────────────────────
            ['name' => 'person_report_view', 'guard_name' => 'web', 'group_name' => 'Reports'],
            ['name' => 'person_report_export', 'guard_name' => 'web', 'group_name' => 'Reports'],
            ['name' => 'flight_report_view', 'guard_name' => 'web', 'group_name' => 'Reports'],
            ['name' => 'flight_report_export', 'guard_name' => 'web', 'group_name' => 'Reports'],
            ['name' => 'rooming_list_report_view', 'guard_name' => 'web', 'group_name' => 'Reports'],
            ['name' => 'rooming_list_report_export', 'guard_name' => 'web', 'group_name' => 'Reports'],
            ['name' => 'rooming_list_assign', 'guard_name' => 'web', 'group_name' => 'Reports'],
            ['name' => 'check_in_report_view', 'guard_name' => 'web', 'group_name' => 'Reports'],
            ['name' => 'check_in_report_export', 'guard_name' => 'web', 'group_name' => 'Reports'],
            ['name' => 'hotel_person_report_view', 'guard_name' => 'web', 'group_name' => 'Reports'],
            ['name' => 'hotel_person_report_export', 'guard_name' => 'web', 'group_name' => 'Reports'],
            ['name' => 'maktab_report_view', 'guard_name' => 'web', 'group_name' => 'Reports'],
            ['name' => 'maktab_report_export', 'guard_name' => 'web', 'group_name' => 'Reports'],

            // ── 29. BUS ASSIGNMENT & TRANSPORT ──────────────────
            ['name' => 'bus_assignment_view', 'guard_name' => 'web', 'group_name' => 'Bus Assignment'],
            ['name' => 'bus_assignment_create', 'guard_name' => 'web', 'group_name' => 'Bus Assignment'],
            ['name' => 'bus_assignment_assign', 'guard_name' => 'web', 'group_name' => 'Bus Assignment'],
            ['name' => 'bus_assignment_export', 'guard_name' => 'web', 'group_name' => 'Bus Assignment'],

            // ── 30. PILGRIM PASS / BADGES ───────────────────────
            ['name' => 'pilgrim_badge_view', 'guard_name' => 'web', 'group_name' => 'Pilgrim Pass'],

            // ── 31. MOBILE OPERATIONS DASHBOARD & INQUIRIES ─────
            ['name' => 'mobile_ops_dashboard_view', 'guard_name' => 'web', 'group_name' => 'Mobile Operations'],
            ['name' => 'mobile_ops_inquiry_view', 'guard_name' => 'web', 'group_name' => 'Mobile Operations'],
            ['name' => 'mobile_ops_inquiry_edit', 'guard_name' => 'web', 'group_name' => 'Mobile Operations'],

            // ── 32. GALLERY & ZIARAT ────────────────────────────
            ['name' => 'gallery_view', 'guard_name' => 'web', 'group_name' => 'Gallery & Ziarat'],
            ['name' => 'gallery_create', 'guard_name' => 'web', 'group_name' => 'Gallery & Ziarat'],
            ['name' => 'gallery_edit', 'guard_name' => 'web', 'group_name' => 'Gallery & Ziarat'],
            ['name' => 'gallery_trash', 'guard_name' => 'web', 'group_name' => 'Gallery & Ziarat'],
            ['name' => 'gallery_trash_view', 'guard_name' => 'web', 'group_name' => 'Gallery & Ziarat'],
            ['name' => 'gallery_restore', 'guard_name' => 'web', 'group_name' => 'Gallery & Ziarat'],

            // ── 33. HAJJ BOOKS ──────────────────────────────────
            ['name' => 'book_view', 'guard_name' => 'web', 'group_name' => 'Hajj Books'],
            ['name' => 'book_create', 'guard_name' => 'web', 'group_name' => 'Hajj Books'],
            ['name' => 'book_edit', 'guard_name' => 'web', 'group_name' => 'Hajj Books'],
            ['name' => 'book_trash', 'guard_name' => 'web', 'group_name' => 'Hajj Books'],
            ['name' => 'book_trash_view', 'guard_name' => 'web', 'group_name' => 'Hajj Books'],
            ['name' => 'book_restore', 'guard_name' => 'web', 'group_name' => 'Hajj Books'],

            // ── 34. HUJJAJ GUIDELINES ───────────────────────────
            ['name' => 'guideline_view', 'guard_name' => 'web', 'group_name' => 'Hujjaj Guidelines'],
            ['name' => 'guideline_create', 'guard_name' => 'web', 'group_name' => 'Hujjaj Guidelines'],
            ['name' => 'guideline_edit', 'guard_name' => 'web', 'group_name' => 'Hujjaj Guidelines'],
            ['name' => 'guideline_trash', 'guard_name' => 'web', 'group_name' => 'Hujjaj Guidelines'],
            ['name' => 'guideline_trash_view', 'guard_name' => 'web', 'group_name' => 'Hujjaj Guidelines'],
            ['name' => 'guideline_restore', 'guard_name' => 'web', 'group_name' => 'Hujjaj Guidelines'],

            // ── 35. VIDEO TUTORIALS ─────────────────────────────
            ['name' => 'video_view', 'guard_name' => 'web', 'group_name' => 'Video Tutorials'],
            ['name' => 'video_create', 'guard_name' => 'web', 'group_name' => 'Video Tutorials'],
            ['name' => 'video_edit', 'guard_name' => 'web', 'group_name' => 'Video Tutorials'],
            ['name' => 'video_trash', 'guard_name' => 'web', 'group_name' => 'Video Tutorials'],
            ['name' => 'video_trash_view', 'guard_name' => 'web', 'group_name' => 'Video Tutorials'],
            ['name' => 'video_restore', 'guard_name' => 'web', 'group_name' => 'Video Tutorials'],

            // ── 36. VISA COUNTRIES ──────────────────────────────
            ['name' => 'visa_country_view', 'guard_name' => 'web', 'group_name' => 'Visa Country'],
            ['name' => 'visa_country_create', 'guard_name' => 'web', 'group_name' => 'Visa Country'],
            ['name' => 'visa_country_edit', 'guard_name' => 'web', 'group_name' => 'Visa Country'],
            ['name' => 'visa_country_trash', 'guard_name' => 'web', 'group_name' => 'Visa Country'],
            ['name' => 'visa_country_trash_view', 'guard_name' => 'web', 'group_name' => 'Visa Country'],
            ['name' => 'visa_country_restore', 'guard_name' => 'web', 'group_name' => 'Visa Country'],

            // ── 37. VISA INQUIRIES ──────────────────────────────
            ['name' => 'visa_inquiry_view', 'guard_name' => 'web', 'group_name' => 'Visa Inquiry'],
            ['name' => 'visa_inquiry_show', 'guard_name' => 'web', 'group_name' => 'Visa Inquiry'],
            ['name' => 'visa_inquiry_edit', 'guard_name' => 'web', 'group_name' => 'Visa Inquiry'],
            ['name' => 'visa_inquiry_trash', 'guard_name' => 'web', 'group_name' => 'Visa Inquiry'],

            // ── 38. UMRAH PACKAGES ──────────────────────────────
            ['name' => 'umrah_package_view', 'guard_name' => 'web', 'group_name' => 'Umrah Package'],
            ['name' => 'umrah_package_create', 'guard_name' => 'web', 'group_name' => 'Umrah Package'],
            ['name' => 'umrah_package_edit', 'guard_name' => 'web', 'group_name' => 'Umrah Package'],
            ['name' => 'umrah_package_trash', 'guard_name' => 'web', 'group_name' => 'Umrah Package'],
            ['name' => 'umrah_package_trash_view', 'guard_name' => 'web', 'group_name' => 'Umrah Package'],
            ['name' => 'umrah_package_restore', 'guard_name' => 'web', 'group_name' => 'Umrah Package'],

            // ── 39. DOMESTIC PACKAGES ───────────────────────────
            ['name' => 'domestic_package_view', 'guard_name' => 'web', 'group_name' => 'Domestic Package'],
            ['name' => 'domestic_package_create', 'guard_name' => 'web', 'group_name' => 'Domestic Package'],
            ['name' => 'domestic_package_edit', 'guard_name' => 'web', 'group_name' => 'Domestic Package'],
            ['name' => 'domestic_package_trash', 'guard_name' => 'web', 'group_name' => 'Domestic Package'],
            ['name' => 'domestic_package_trash_view', 'guard_name' => 'web', 'group_name' => 'Domestic Package'],
            ['name' => 'domestic_package_restore', 'guard_name' => 'web', 'group_name' => 'Domestic Package'],

            // ── 40. INTERNATIONAL PACKAGES ──────────────────────
            ['name' => 'international_package_view', 'guard_name' => 'web', 'group_name' => 'International Package'],
            ['name' => 'international_package_create', 'guard_name' => 'web', 'group_name' => 'International Package'],
            ['name' => 'international_package_edit', 'guard_name' => 'web', 'group_name' => 'International Package'],
            ['name' => 'international_package_trash', 'guard_name' => 'web', 'group_name' => 'International Package'],
            ['name' => 'international_package_trash_view', 'guard_name' => 'web', 'group_name' => 'International Package'],
            ['name' => 'international_package_restore', 'guard_name' => 'web', 'group_name' => 'International Package'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name'], 'guard_name' => $permission['guard_name']],
                ['group_name' => $permission['group_name']]
            );
        }

        // Grant ALL permissions to Admin role automatically
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        // Clear permission cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
