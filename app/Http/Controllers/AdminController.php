<?php

use App\Services\AppServices;
use App\Services\AccountServices;

class AdminController extends BaseController
{
    protected $layout = 'layouts.master';

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function home()
    {
        $data = Config::get("gondolyn.basic-app-info");

        $user = Session::get("username");

        $data['message'] = AppServices::welcomeMessage($user);

        $data['form'] = View::make('admin.form');

        return view('admin.home', $data);
    }

    public function users()
    {
        Session::set("userInEditor", null);

        $data = Config::get("gondolyn.basic-app-info");

        $data['users'] = Accounts::getAllAccounts();

        return view('admin.users', $data);
    }

    public function editor($id)
    {
        $data = Config::get("gondolyn.basic-app-info");

        $user = Accounts::getAccount(Crypto::decrypt($id));

        Session::set("userInEditor", $user);

        $gravatarHash = md5( strtolower( trim( $user->user_email ) ) );

        $profileImage = ($user->profile == "") ? null : Utilities::fileAsPublicAsset($user->profile);

        $data['user']               = $user;
        $data['profileImage']       = $profileImage ?: 'http://www.gravatar.com/avatar/'.$gravatarHash.'?s=300';
        $data['inAppNotifications'] = ($user->in_app_notifications) ? 'checked' : '';
        $data['notification']       = Session::get("notification") ?: "";
        $data['options']            = Config::get("permissions.matrix.roles");
        $data['adminEditorMode']    = true;
        $data['shippingColumns']    = Config::get('forms.shipping');

        return view('account.settings', $data);
    }

    public function update()
    {
        try {
            $user = Session::get("userInEditor");
            $status = Accounts::updateAccount($user->id);

            if ($status) {
                Session::flash("notification", Lang::get("notification.profile.admin_update_success"));
            } else {
                Session::flash("notification", Lang::get("notification.profile.admin_update_failed"));
            }
        } catch (Exception $e) {
            Session::flash("notification", $e->getMessage());
        }

        return redirect('admin/editor/'.Crypto::encrypt($user->id));
    }

    public function deactivate()
    {
        $user = Session::get("userInEditor");

        Accounts::modifyAccountStatus($user->id, "inactive");

        return redirect('admin/editor/'.Crypto::encrypt($user->id));
    }

    public function activate()
    {
        $user = Session::get("userInEditor");

        Accounts::modifyAccountStatus($user->id, "active");

        return redirect('admin/editor/'.Crypto::encrypt($user->id));
    }

    /**
     * Delete User Account
     *
     * @return void
     */
    public function delete($id = null)
    {
        if ($id) {
            $user = Accounts::getAccount(Crypto::decrypt($id));
        } else {
            $user = Session::get("userInEditor");
        }

        Accounts::deleteAccount($user->id);

        Session::flash("notification", "The user was successfully deleted");

        return redirect('admin/users');
    }

    /*
    |--------------------------------------------------------------------------
    | FormMaker Demo
    |--------------------------------------------------------------------------
    */

    public function formSubmission()
    {
        $validation = Validation::check('admin');

        if ( ! $validation['errors']) {
            $file = Utilities::saveFile('profile');
            dd("Successful Submission");
        } else {
            return $validation['redirect'];
        }
    }
}
