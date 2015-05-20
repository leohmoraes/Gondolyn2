<?php namespace App\Http\Middleware;

use Closure, Route, Session, Lang, Module;

use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Support\Facades\Config;
use Illuminate\Routing\Redirector;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;

class PermissionsMatrix implements Middleware
{
    protected $app;
    protected $redirector;
    protected $request;

    public function __construct(Application $app, Redirector $redirector, Request $request)
    {
        $this->app = $app;
        $this->redirector = $redirector;
        $this->request = $request;
    }

     /**
      * Handle an incoming request.
      *
      * @param \Illuminate\Http\Request $request
      * @param \Closure $next
      * @return mixed
      */
     public function handle($request, Closure $next)
     {
        $permissionsArray = array();

        $matrix = Config::get('permissions.matrix');

        $default = $matrix['default_role'];
        $groups = $matrix['groups'];
        $roles = $matrix['roles'];

        $route = Route::getRoutes()->match($request);
        $action = $route->getAction();

        // Create the Matrix
        $modulePermissionConfigs = Module::getPermissionConfigs();

        $permissionsArray['roles'] = $roles;
        $permissionsArray['groups'] = $groups;
        $permissionsArray['default_role'] = $default;

        foreach ($modulePermissionConfigs as $permissions) {

            foreach ($permissions['roles'] as $role) {
                $permissionsArray['roles'][] = $role;
                $permissionsArray['roles'] = array_unique($permissionsArray['roles']);
            }

            foreach ($permissions['groups'] as $group => $members) {
                foreach ($members as $member) {
                    $permissionsArray['groups'][$group][] = $member;
                }
                $permissionsArray['groups'][$group] = array_unique($permissionsArray['groups'][$group]);
            }

        }

        Config::set('permissions.matrix', $permissionsArray);

        // Permission Checking
        if (isset($action['role'])) {
            $permission = $action['role'];
            if (Session::get('logged_in') &&  ! $this->checkPermission(Session::get('role'), $permission)) {
                throw new \Exception(Lang::get('notification.general.incorrect-permission'), 1);
            }
        }

        $content = $next($request);
        return $content;
    }

    public function checkPermission($role, $permission)
    {
        $permissionsConfig = Config::get('permissions.matrix');

        if (stristr($permission, 'groups')) {
            $permission = explode('.', $permission);
            $permissionArray = $permissionsConfig['groups'][$permission[1]];

            if (in_array($role, $permissionArray)) {
                return true;
            }
        } else {
            if ($role === $permission) {
                return true;
            }
        }

        // look at group arrays and go from there
        return false;
    }

}