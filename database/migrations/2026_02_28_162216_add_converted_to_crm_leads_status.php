<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'Converted' to the status ENUM
        if (DB::getDriverName() === 'sqlite') {
            DB::statement("ALTER TABLE crm_leads ADD COLUMN status_new TEXT NOT NULL DEFAULT 'Inquiry'");
            DB::statement("UPDATE crm_leads SET status_new = status");
            DB::statement("ALTER TABLE crm_leads DROP COLUMN status");
            DB::statement("ALTER TABLE crm_leads RENAME COLUMN status_new TO status");
        } else {
            DB::statement("ALTER TABLE crm_leads MODIFY COLUMN status ENUM('Inquiry', 'Negotiation', 'Approval Sent', 'Closed-Won', 'Lost', 'Converted') NOT NULL DEFAULT 'Inquiry'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'Converted' from the ENUM (revert to original)
        if (DB::getDriverName() === 'sqlite') {
            DB::statement("ALTER TABLE crm_leads ADD COLUMN status_old TEXT NOT NULL DEFAULT 'Inquiry'");
            DB::statement("UPDATE crm_leads SET status_old = CASE WHEN status = 'Converted' THEN 'Inquiry' ELSE status END");
            DB::statement("ALTER TABLE crm_leads DROP COLUMN status");
            DB::statement("ALTER TABLE crm_leads RENAME COLUMN status_old TO status");
        } else {
            DB::statement("ALTER TABLE crm_leads MODIFY COLUMN status ENUM('Inquiry', 'Negotiation', 'Approval Sent', 'Closed-Won', 'Lost') NOT NULL DEFAULT 'Inquiry'");
        }
    }
};
