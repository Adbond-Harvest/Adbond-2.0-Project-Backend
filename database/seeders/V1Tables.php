<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use app\Models\TableMigration;

class V1Tables extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tables = [
            'age_groups', 'assessments', 'banks', 'bank_accounts', 'bank_payment_references', 'card_payment_channels', 'categories',
            'cities', 'comments', 'comment_replies', 'commission_rates', 'company_info', 'countries', 'customers', 'customer_documents', 
            'customer_document_types', 'customer_next_of_kins', 'customer_packages', 'customer_packages_offers', 'email_verification_tokens', 
            'events', 'failed_jobs', 'fcm_tokens', 'features', 'files', 'forum_links', 'general_visitation_days', 'hybrid_staff_draws', 
            'inspection_dates', 'inspection_days', 'inspection_requests', 'loyalty_placements', 'loyalty_winners', 'measuring_units', 
            'monthly_week_days', 'news', 'notifications', 'notification_types', 'offers', 'offer_bids', 'orders', 'order_discounts', 
            'packages', 'package_items', 'package_photos', 'pages', 'page_photos', 'password_resets', 'payments', 'payment_evidence',
            'payment_modes', 'payment_period_statuses', 'payment_statuses', 'personal_access_tokens', 'posts', 'post_tags', 'projects', 
            'project_locations', 'project_photos', 'promos', 'promo_products', 'questions', 'question_options', 'reactions', 'roles', 
            'sales_offer_bank_payment_reference', 'sales_offer_payments', 'staff_ratings', 'staff_types', 'states',  'tags', 'tests', 
            'users', 'user_commissions', 'user_commission_payments', 'user_password_reset_tokens', 'virtual_staff_assessments', 
            'virtual_staff_assessment_answers', 'weeks'
        ];

        foreach($tables as $table) {
            TableMigration::firstOrCreate(["name" => $table]);
        }
    }
}
