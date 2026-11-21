<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Activity;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\DealStage;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\Organization;
use App\Models\Permission;
use App\Models\Pipeline;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Organization
        $org = Organization::create([
            'name' => 'Acme Global Enterprise',
            'slug' => 'acme-global',
            'domain' => 'acme.com',
            'phone' => '+1 (555) 234-5678',
            'email' => 'contact@acme.com',
            'currency' => 'USD',
            'timezone' => 'America/New_York',
            'fiscal_year_start' => 1,
            'is_active' => true,
        ]);

        session(['active_organization_id' => $org->id]);

        // 2. Define Granular Permissions across CRM Modules
        $modules = [
            'leads' => 'Leads Management',
            'contacts' => 'Contacts Management',
            'accounts' => 'Accounts Management',
            'deals' => 'Deals & Pipeline',
            'activities' => 'Activities & Tasks',
            'campaigns' => 'Marketing Campaigns',
            'products' => 'Products & Price Books',
            'quotes' => 'Quotes Management',
            'orders' => 'Sales Orders',
            'invoices' => 'Invoices & Billing',
            'vendors' => 'Vendors & Purchase Orders',
            'reports' => 'Reports & Analytics',
            'workflows' => 'Automation & Workflows',
            'settings' => 'System & Organization Settings',
        ];

        $actions = [
            'view' => 'View',
            'create' => 'Create',
            'edit' => 'Edit',
            'delete' => 'Delete',
            'import' => 'Import',
            'export' => 'Export',
            'share' => 'Share',
            'manage' => 'Manage',
        ];

        $allPermissionIds = [];
        $viewPermissionIds = [];
        $salesPermissionIds = [];
        $financePermissionIds = [];
        $marketingPermissionIds = [];

        foreach ($modules as $moduleKey => $moduleName) {
            foreach ($actions as $actionKey => $actionLabel) {
                $permissionName = "{$moduleKey}.{$actionKey}";
                $perm = Permission::create([
                    'module' => $moduleKey,
                    'name' => $permissionName,
                    'display_name' => "{$actionLabel} {$moduleName}",
                    'description' => "Allow user to {$actionKey} in {$moduleName}",
                ]);

                $allPermissionIds[] = $perm->id;

                if ($actionKey === 'view') {
                    $viewPermissionIds[] = $perm->id;
                }

                if (in_array($moduleKey, ['leads', 'contacts', 'accounts', 'deals', 'activities', 'quotes', 'reports'])) {
                    $salesPermissionIds[] = $perm->id;
                }

                if (in_array($moduleKey, ['products', 'quotes', 'orders', 'invoices', 'vendors', 'reports'])) {
                    $financePermissionIds[] = $perm->id;
                }

                if (in_array($moduleKey, ['leads', 'contacts', 'campaigns', 'activities', 'reports'])) {
                    $marketingPermissionIds[] = $perm->id;
                }
            }
        }

        // 3. Create Default System Roles
        $roles = [
            'super_admin' => ['name' => 'Super Administrator', 'desc' => 'Global system administrator', 'perms' => $allPermissionIds],
            'admin' => ['name' => 'Administrator', 'desc' => 'Full administrative control of the organization', 'perms' => $allPermissionIds],
            'sales_manager' => ['name' => 'Sales Manager', 'desc' => 'Manages sales teams, pipelines, and revenue', 'perms' => $salesPermissionIds],
            'sales_rep' => ['name' => 'Sales Representative', 'desc' => 'Handles leads, contacts, accounts, and deals', 'perms' => array_intersect($salesPermissionIds, $viewPermissionIds)],
            'marketing_manager' => ['name' => 'Marketing Manager', 'desc' => 'Leads campaigns and marketing operations', 'perms' => $marketingPermissionIds],
            'marketing_exec' => ['name' => 'Marketing Executive', 'desc' => 'Executes campaigns and tracks leads', 'perms' => array_intersect($marketingPermissionIds, $viewPermissionIds)],
            'finance_manager' => ['name' => 'Finance Manager', 'desc' => 'Oversees quotes, orders, invoices, and products', 'perms' => $financePermissionIds],
            'support_exec' => ['name' => 'Support Executive', 'desc' => 'Assists customer contacts and manages support tasks', 'perms' => array_intersect($salesPermissionIds, $viewPermissionIds)],
            'read_only' => ['name' => 'Read-only User', 'desc' => 'View-only access across the CRM', 'perms' => $viewPermissionIds],
        ];

        $roleModels = [];
        foreach ($roles as $slug => $data) {
            $role = Role::create([
                'organization_id' => $org->id,
                'name' => $data['name'],
                'slug' => $slug,
                'description' => $data['desc'],
                'is_system' => true,
            ]);
            $role->permissions()->sync($data['perms']);
            $roleModels[$slug] = $role;
        }

        // 4. Create Standard Users
        $defaultPassword = Hash::make('Password123!');

        $adminUser = User::create([
            'organization_id' => $org->id,
            'name' => 'Sarah Connor',
            'email' => 'admin@crm.local',
            'password' => $defaultPassword,
            'phone' => '+1 (555) 901-2345',
            'title' => 'Chief Operations Officer',
            'is_active' => true,
            'is_super_admin' => true,
            'email_verified_at' => now(),
        ]);
        $adminUser->roles()->sync([$roleModels['admin']->id, $roleModels['super_admin']->id]);
        $org->members()->attach($adminUser->id, ['is_default' => true]);

        $salesManager = User::create([
            'organization_id' => $org->id,
            'name' => 'Alex Miller',
            'email' => 'sales.manager@crm.local',
            'password' => $defaultPassword,
            'phone' => '+1 (555) 345-6789',
            'title' => 'VP of Global Sales',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $salesManager->roles()->sync([$roleModels['sales_manager']->id]);
        $org->members()->attach($salesManager->id, ['is_default' => true]);

        $salesRep = User::create([
            'organization_id' => $org->id,
            'name' => 'Elena Rostova',
            'email' => 'sales.rep@crm.local',
            'password' => $defaultPassword,
            'phone' => '+1 (555) 456-7890',
            'title' => 'Senior Account Executive',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $salesRep->roles()->sync([$roleModels['sales_rep']->id]);
        $org->members()->attach($salesRep->id, ['is_default' => true]);

        $financeUser = User::create([
            'organization_id' => $org->id,
            'name' => 'Marcus Vance',
            'email' => 'finance.manager@crm.local',
            'password' => $defaultPassword,
            'phone' => '+1 (555) 567-8901',
            'title' => 'Finance Director',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $financeUser->roles()->sync([$roleModels['finance_manager']->id]);
        $org->members()->attach($financeUser->id, ['is_default' => true]);

        $readOnlyUser = User::create([
            'organization_id' => $org->id,
            'name' => 'Auditor Bob',
            'email' => 'readonly@crm.local',
            'password' => $defaultPassword,
            'phone' => '+1 (555) 678-9012',
            'title' => 'Compliance Reviewer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $readOnlyUser->roles()->sync([$roleModels['read_only']->id]);
        $org->members()->attach($readOnlyUser->id, ['is_default' => true]);

        // 5. Teams
        $enterpriseSales = Team::create([
            'organization_id' => $org->id,
            'name' => 'Enterprise Sales Squad',
            'description' => 'Targeting Fortune 500 enterprise accounts',
            'leader_id' => $salesManager->id,
        ]);
        $enterpriseSales->members()->attach([$salesManager->id, $salesRep->id]);

        $inboundMarketing = Team::create([
            'organization_id' => $org->id,
            'name' => 'Inbound Growth Team',
            'description' => 'Handling digital marketing and qualified inbound leads',
            'leader_id' => $adminUser->id,
        ]);
        $inboundMarketing->members()->attach([$adminUser->id]);

        // 6. Lead Sources and Statuses
        $sources = [
            'Website Form', 'Direct Traffic', 'Google Ads', 'Cold Outreach',
            'Partner Referral', 'Trade Show 2026', 'LinkedIn InMail',
        ];
        $sourceModels = [];
        foreach ($sources as $name) {
            $sourceModels[] = LeadSource::create([
                'organization_id' => $org->id,
                'name' => $name,
                'is_active' => true,
            ]);
        }

        $statuses = [
            ['name' => 'New', 'color' => '#0d6efd', 'converted' => false],
            ['name' => 'Contacted', 'color' => '#0dcaf0', 'converted' => false],
            ['name' => 'Qualified', 'color' => '#198754', 'converted' => false],
            ['name' => 'In Progress', 'color' => '#ffc107', 'converted' => false],
            ['name' => 'Converted', 'color' => '#6610f2', 'converted' => true],
            ['name' => 'Disqualified', 'color' => '#dc3545', 'converted' => false],
        ];
        $statusModels = [];
        foreach ($statuses as $s) {
            $statusModels[$s['name']] = LeadStatus::create([
                'organization_id' => $org->id,
                'name' => $s['name'],
                'color' => $s['color'],
                'is_converted' => $s['converted'],
                'is_active' => true,
            ]);
        }

        // 7. Pipeline and Deal Stages
        $pipeline = Pipeline::create([
            'organization_id' => $org->id,
            'name' => 'Standard Sales Pipeline',
            'is_default' => true,
        ]);

        $stagesData = [
            ['name' => 'Qualification', 'prob' => 10, 'order' => 1, 'cat' => 'open', 'color' => '#6c757d'],
            ['name' => 'Needs Analysis', 'prob' => 20, 'order' => 2, 'cat' => 'open', 'color' => '#0dcaf0'],
            ['name' => 'Value Proposition', 'prob' => 40, 'order' => 3, 'cat' => 'open', 'color' => '#0d6efd'],
            ['name' => 'Proposal/Price Quote', 'prob' => 60, 'order' => 4, 'cat' => 'open', 'color' => '#fd7e14'],
            ['name' => 'Negotiation/Review', 'prob' => 80, 'order' => 5, 'cat' => 'open', 'color' => '#ffc107'],
            ['name' => 'Closed Won', 'prob' => 100, 'order' => 6, 'cat' => 'won', 'color' => '#198754'],
            ['name' => 'Closed Lost', 'prob' => 0, 'order' => 7, 'cat' => 'lost', 'color' => '#dc3545'],
        ];

        $stageModels = [];
        foreach ($stagesData as $sd) {
            $stageModels[$sd['name']] = DealStage::create([
                'organization_id' => $org->id,
                'pipeline_id' => $pipeline->id,
                'name' => $sd['name'],
                'probability' => $sd['prob'],
                'sort_order' => $sd['order'],
                'category' => $sd['cat'],
                'color' => $sd['color'],
            ]);
        }

        // 8. Accounts & Contacts
        $account1 = Account::create([
            'organization_id' => $org->id,
            'owner_id' => $salesRep->id,
            'name' => 'Stark Global Industries',
            'account_type' => 'Customer',
            'industry' => 'Aerospace & Defense',
            'website' => 'https://starkindustries.example.com',
            'phone' => '+1 (555) 777-8899',
            'annual_revenue' => 25000000.00,
            'employees' => 12000,
            'billing_city' => 'New York',
            'billing_country' => 'United States',
            'description' => 'Key enterprise account for cloud CRM transformation.',
        ]);

        $account2 = Account::create([
            'organization_id' => $org->id,
            'owner_id' => $salesManager->id,
            'name' => 'Wayne Enterprises Ltd',
            'account_type' => 'Customer',
            'industry' => 'Conglomerate',
            'website' => 'https://wayneenterprises.example.com',
            'phone' => '+1 (555) 888-9900',
            'annual_revenue' => 48000000.00,
            'employees' => 25000,
            'billing_city' => 'Gotham',
            'billing_country' => 'United States',
            'description' => 'High-value customer for enterprise licensing.',
        ]);

        $contact1 = Contact::create([
            'organization_id' => $org->id,
            'account_id' => $account1->id,
            'owner_id' => $salesRep->id,
            'first_name' => 'Pepper',
            'last_name' => 'Potts',
            'email' => 'pepper.potts@stark.example.com',
            'phone' => '+1 (555) 777-8891',
            'designation' => 'Chief Executive Officer',
            'department' => 'Executive Management',
            'mailing_city' => 'New York',
            'mailing_country' => 'United States',
        ]);

        $contact2 = Contact::create([
            'organization_id' => $org->id,
            'account_id' => $account2->id,
            'owner_id' => $salesManager->id,
            'first_name' => 'Lucius',
            'last_name' => 'Fox',
            'email' => 'l.fox@wayne.example.com',
            'phone' => '+1 (555) 888-9901',
            'designation' => 'Chief Technology Officer',
            'department' => 'R&D',
            'mailing_city' => 'Gotham',
            'mailing_country' => 'United States',
        ]);

        // 9. Deals with Real Pipeline Values
        Deal::create([
            'organization_id' => $org->id,
            'pipeline_id' => $pipeline->id,
            'stage_id' => $stageModels['Closed Won']->id,
            'account_id' => $account1->id,
            'contact_id' => $contact1->id,
            'owner_id' => $salesRep->id,
            'name' => 'Stark Industries Enterprise Rollout',
            'amount' => 185000.00,
            'closing_date' => now()->subDays(10),
            'probability' => 100,
            'expected_revenue' => 185000.00,
            'deal_type' => 'New Business',
            'lead_source' => 'Partner Referral',
            'won_reason' => 'Exceptional system architecture and custom workflow capabilities.',
        ]);

        Deal::create([
            'organization_id' => $org->id,
            'pipeline_id' => $pipeline->id,
            'stage_id' => $stageModels['Negotiation/Review']->id,
            'account_id' => $account2->id,
            'contact_id' => $contact2->id,
            'owner_id' => $salesManager->id,
            'name' => 'Wayne Tech Security Integration',
            'amount' => 240000.00,
            'closing_date' => now()->addDays(20),
            'probability' => 80,
            'expected_revenue' => 192000.00,
            'deal_type' => 'Expansion',
            'lead_source' => 'Direct Traffic',
        ]);

        Deal::create([
            'organization_id' => $org->id,
            'pipeline_id' => $pipeline->id,
            'stage_id' => $stageModels['Value Proposition']->id,
            'account_id' => $account1->id,
            'contact_id' => $contact1->id,
            'owner_id' => $salesRep->id,
            'name' => 'Stark Analytics Add-on License',
            'amount' => 65000.00,
            'closing_date' => now()->addDays(45),
            'probability' => 40,
            'expected_revenue' => 26000.00,
            'deal_type' => 'Up-sell',
            'lead_source' => 'Website Form',
        ]);

        Deal::create([
            'organization_id' => $org->id,
            'pipeline_id' => $pipeline->id,
            'stage_id' => $stageModels['Closed Lost']->id,
            'account_id' => $account2->id,
            'contact_id' => $contact2->id,
            'owner_id' => $salesManager->id,
            'name' => 'Legacy Hardware Migration Deal',
            'amount' => 95000.00,
            'closing_date' => now()->subDays(25),
            'probability' => 0,
            'expected_revenue' => 0.00,
            'deal_type' => 'New Business',
            'lost_reason' => 'Budget deferred to next fiscal quarter.',
        ]);

        // 10. Leads
        Lead::create([
            'organization_id' => $org->id,
            'assigned_user_id' => $salesRep->id,
            'team_id' => $enterpriseSales->id,
            'lead_source_id' => $sourceModels[0]->id,
            'lead_status_id' => $statusModels['Qualified']->id,
            'first_name' => 'Diana',
            'last_name' => 'Prince',
            'company' => 'Themyscira Cultural Foundation',
            'designation' => 'Executive Director',
            'email' => 'diana@themyscira.example.org',
            'phone' => '+1 (555) 321-4321',
            'annual_revenue' => 8500000.00,
            'employee_count' => 150,
            'rating' => 'Hot',
            'city' => 'Washington',
            'country' => 'United States',
            'description' => 'Interested in CRM for donor tracking and global activities.',
        ]);

        Lead::create([
            'organization_id' => $org->id,
            'assigned_user_id' => $salesRep->id,
            'team_id' => $enterpriseSales->id,
            'lead_source_id' => $sourceModels[2]->id,
            'lead_status_id' => $statusModels['New']->id,
            'first_name' => 'Clark',
            'last_name' => 'Kent',
            'company' => 'Daily Planet Media Corp',
            'designation' => 'Senior Investigative Journalist',
            'email' => 'c.kent@dailyplanet.example.com',
            'phone' => '+1 (555) 432-5432',
            'annual_revenue' => 14000000.00,
            'employee_count' => 450,
            'rating' => 'Warm',
            'city' => 'Metropolis',
            'country' => 'United States',
        ]);

        Lead::create([
            'organization_id' => $org->id,
            'assigned_user_id' => $salesManager->id,
            'team_id' => $enterpriseSales->id,
            'lead_source_id' => $sourceModels[1]->id,
            'lead_status_id' => $statusModels['Converted']->id,
            'first_name' => 'Tony',
            'last_name' => 'Stark',
            'company' => 'Stark Global Industries',
            'designation' => 'Chief Technology Architect',
            'email' => 'tony@stark.example.com',
            'phone' => '+1 (555) 777-8890',
            'is_converted' => true,
            'converted_at' => now()->subDays(15),
            'converted_contact_id' => $contact1->id,
            'converted_account_id' => $account1->id,
        ]);

        // 11. Activities (Tasks, Meetings, Calls)
        Activity::create([
            'organization_id' => $org->id,
            'assigned_user_id' => $salesRep->id,
            'subjectable_type' => Deal::class,
            'subjectable_id' => 2,
            'activity_type' => 'task',
            'subject' => 'Send revised master service agreement to Lucius Fox',
            'priority' => 'high',
            'status' => 'in_progress',
            'due_date' => now()->addDays(2),
        ]);

        Activity::create([
            'organization_id' => $org->id,
            'assigned_user_id' => $salesRep->id,
            'subjectable_type' => Lead::class,
            'subjectable_id' => 1,
            'activity_type' => 'call',
            'subject' => 'Discovery call on multi-currency requirements',
            'priority' => 'medium',
            'status' => 'completed',
            'start_time' => now()->subHours(4),
            'duration_minutes' => 30,
            'call_type' => 'outbound',
            'call_result' => 'Interested - Demo scheduled next Tuesday',
            'completed_at' => now()->subHours(3),
        ]);

        Activity::create([
            'organization_id' => $org->id,
            'assigned_user_id' => $salesManager->id,
            'subjectable_type' => Account::class,
            'subjectable_id' => $account2->id,
            'activity_type' => 'meeting',
            'subject' => 'Quarterly Executive Business Review',
            'priority' => 'high',
            'status' => 'not_started',
            'start_time' => now()->addDays(5)->setHour(14)->setMinute(0),
            'duration_minutes' => 60,
            'location' => 'Wayne Tower Boardroom & Microsoft Teams',
        ]);
    }
}
