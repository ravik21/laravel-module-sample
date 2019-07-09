<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProfileFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('title')->unsigned()->nullable()->after('marketing');
            $table->boolean('is_urgent')->default(false)->after('last_name');
            $table->tinyInteger('approval_status')->unsigned()->index()->after('is_urgent');
            $table->string('company_name')->nullable()->after('approval_status');
            $table->string('parent_company_name')->nullable()->after('company_name');
            $table->string('company_number')->nullable()->after('parent_company_name');
            $table->string('company_street')->nullable()->after('company_number');
            $table->string('company_town')->nullable()->after('company_street');
            $table->string('company_region')->nullable()->after('company_town');
            $table->string('company_postcode')->nullable()->after('company_region');
            $table->integer('company_country_id')->nullable()->unsigned()->index()->after('company_postcode');
            $table->string('company_vat_registered')->nullable()->after('company_country_id');
            $table->string('company_vat_no')->nullable()->after('company_vat_registered');
            $table->string('company_phone_contact')->nullable()->after('company_vat_no');
            $table->string('company_position')->nullable()->after('company_phone_contact');
            $table->string('timezone')->nullable()->after('company_position');
            $table->decimal('hour_rate', 8, 2)->nullable()->after('timezone');
            $table->decimal('day_rate', 8, 2)->nullable()->after('hour_rate');
            $table->text('languages')->nullable()->after('day_rate');
            $table->text('availability')->nullable()->after('languages');
            $table->text('professional_experience')->nullable()->after('availability');
            $table->text('past_projects')->nullable()->after('professional_experience');
            $table->text('education')->nullable()->after('past_projects');
            $table->text('memberships')->nullable()->after('education');
            $table->text('references')->nullable()->after('memberships');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'is_urgent',
                'approval_status',
                'company_name',
                'parent_company_name',
                'company_number',
                'company_street',
                'company_town',
                'company_region',
                'company_postcode',
                'company_country_id',
                'company_vat_registered',
                'company_vat_no',
                'company_phone_contact',
                'company_position',
                'timezone_id',
                'languages',
                'availability',
                'hour_rate',
                'day_rate',
                'professional_experience',
                'past_projects',
                'education',
                'memberships'
            ]);
        });
    }
}
