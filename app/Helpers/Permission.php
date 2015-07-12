<?php namespace App\Helpers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Lang;
use App\Http\Middleware\PermissionsMatrix;
use App\Exceptions\PermissionException;
use App\Exceptions\LoginException;

class Permission
{
    /**
     * Test role permission
     * @param  string $role Role
     * @return void
     */
    public static function role($role)
    {
        Permission::permission(['role' => $role]);
    }

    /**
     * Test subscription plan
     * @param  string $plan Plan name
     * @return void
     */
    public static function subscription($plan)
    {
        Permission::permission(['subscription' => $plan]);
    }

    /**
     * Checks the different types of permission
     * @param  array $config Array of permission types
     * @return void
     */
    public static function permission($config)
    {
        if ( ! isset($config['role']) && ! isset($config['subscription'])) {
            throw new \Exception('Please set either a role or subscription as an array.', 1);
        }

        // Role Checking
        if (isset($config['role'])) {
            $permission = $config['role'];
            if (Session::get('logged_in') && ! PermissionsMatrix::checkRole(Session::get('role'), $permission)) {
                throw new PermissionException(Lang::get('notification.general.incorrect-permission'), 1);
            }
        }

        // Subscription Checking
        if (isset($config['subscription'])) {
            $subscription = $config['subscription'];
            if (Session::get('logged_in') && ! PermissionsMatrix::checkSubscription(Session::get('plan'), $subscription)) {
                throw new PermissionException(Lang::get('notification.subscription.incorrect-subscription'), 1);
            }
        }

        if ( ! Session::get('logged_in')) {
            throw new LoginException(Lang::get('notification.login.expired-session'), 1);
        }
    }

}
