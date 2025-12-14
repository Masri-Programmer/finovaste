<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_name', 'Ranalp');
        $this->migrator->add('general.site_active', true);
        $this->migrator->add('general.logo_url', null);
        $this->migrator->add('general.contact_email', 'admin@example.com');
        $this->migrator->add('general.per_page', '15');
    }
};
