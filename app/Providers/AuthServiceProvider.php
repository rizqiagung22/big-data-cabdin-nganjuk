<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User; // Pastikan Anda mengimpor model User Anda

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // super admin memiliki semua hak akses
        Gate::define('is-super-admin', function (User $user) {
            return $user->role === 'superadmin';
        });

        // admin adalah pegawai yang mengupload file dan menghapus file
        Gate::define('is-admin', function (User $user) {
            return $user->role === 'admin';
        });

        Gate::define('is-super-admin-or-admin', function (User $user) {
            return $user->role === 'superadmin' || $user->role === 'admin';
        });
        // user adalah pimpinan, yang hanya bisa download file yang sudah di upload admin
        Gate::define('is-user', function (User $user) {
            return $user->role === 'user';
        });
    }
}
