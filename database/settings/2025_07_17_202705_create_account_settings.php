<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('account.user_registration', true);
        $this->migrator->add('account.user_email_verification', true);
        $this->migrator->add('account.user_password_reset', true);

        $this->migrator->add('account.auth_google_enabled', false);
        $this->migrator->add('account.auth_google_registration', true);
        $this->migrator->add('account.auth_google_scopes', []);
        $this->migrator->add('account.auth_google_stateless', false);
        $this->migrator->add('account.auth_google_client_id', '');
        $this->migrator->add('account.auth_google_client_secret', '');

        $this->migrator->add('account.auth_oauth0_enabled', false);
        $this->migrator->add('account.auth_oauth0_registration', true);
        $this->migrator->add('account.auth_oauth0_title', 'Auth0');
        $this->migrator->add('account.auth_oauth0_color', '#3097d1');
        $this->migrator->add('account.auth_oauth0_scopes', []);
        $this->migrator->add('account.auth_oauth0_extra_parameters', []);
        $this->migrator->add('account.auth_oauth0_stateless', false);
        $this->migrator->add('account.auth_oauth0_client_id', '');
        $this->migrator->add('account.auth_oauth0_client_secret', '');
        $this->migrator->add('account.auth_oauth0_base_url', '');

        $this->migrator->add('account.auth_laravelpassport_enabled', false);
        $this->migrator->add('account.auth_laravelpassport_registration', true);
        $this->migrator->add('account.auth_laravelpassport_title', 'Laravel Passport');
        $this->migrator->add('account.auth_laravelpassport_color', '#3097d1');
        $this->migrator->add('account.auth_laravelpassport_scopes', []);
        $this->migrator->add('account.auth_laravelpassport_extra_parameters', []);
        $this->migrator->add('account.auth_laravelpassport_stateless', false);
        $this->migrator->add('account.auth_laravelpassport_client_id', '');
        $this->migrator->add('account.auth_laravelpassport_client_secret', '');
        $this->migrator->add('account.auth_laravelpassport_host', '');
        $this->migrator->add('account.auth_laravelpassport_authorize_uri', 'oauth/authorize');
        $this->migrator->add('account.auth_laravelpassport_token_uri', 'oauth/token');
        $this->migrator->add('account.auth_laravelpassport_userinfo_uri', 'api/user');
    }
};
