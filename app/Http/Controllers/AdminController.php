<?php

use App\Services\AppServices;
use App\Services\AccountServices;

class AdminController extends BaseController
{
    protected $layout = 'layouts.master';

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('security.guard');
    }

    public function home()
    {
        $data = Config::get("gondolyn.appInfo");
        $data['page_title'] = Lang::get('titles.admin-home');

        $user = Session::get("username");

        $data['message'] = AppServices::welcomeMessage($user);

        $data['form'] = View::make('admin.form');

        return view('admin.home', $data);
    }

    public function users()
    {
        Session::set("userInEditor", null);

        $data = Config::get("gondolyn.appInfo");
        $data['page_title'] = Lang::get('titles.admin-user-manager');

        $data['users'] = Accounts::getAllAccounts();

        return view('admin.users', $data);
    }

    public function creator()
    {
        $data = Config::get("gondolyn.appInfo");

        $accounts = new Accounts;
        $settings = [
            'user_email' => '',
            'user_name' => '',
        ];

        $data['page_title']         = Lang::get('titles.admin-user-editor');
        $data['user']               = $accounts->newFromBuilder($settings);
        $data['profileImage']       = null;
        $data['inAppNotifications'] = 'checked';
        $data['notification']       = Session::get("notification") ?: "";
        $data['options']            = Config::get("permissions.matrix.roles");
        $data['adminEditorMode']    = true;
        $data['newAccount']         = true;
        $data['shippingColumns']    = Config::get('forms.shipping');

        return view('account.settings', $data);
    }

    public function editor($id)
    {
        $data = Config::get("gondolyn.appInfo");
        $data['page_title'] = Lang::get('titles.admin-user-editor');

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

    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    */

    public function createAccount()
    {
        // Validation
        $validation = Validation::check('conditions.create_account');

        // Validation errors
        if ($validation['errors']) {
            return $validation['redirect'];
        }

        try {
            $account = [];
            $account['email'] = $validation['inputs']['user_email'];
            $account['password'] = Utilities::addSalt(10);
            $account['role'] = $validation['inputs']['role'];

            $Users = new Accounts;
            $user = $Users->makeNewAccount($account, 'email', false, true);

        } catch (Exception $e) {
            Gondolyn::notification($e->getMessage(), 'danger');
            return redirect('errors/general');
        }

        return redirect('admin/users/editor/'.Crypto::encrypt($user->id));
    }

    public function update()
    {
        try {
            $user = Session::get("userInEditor");
            $status = Accounts::updateAccount($user->id);

            if ($status) {
                Gondolyn::notification(Lang::get("notification.profile.admin_update_success"), 'success');
            } else {
                Gondolyn::notification(Lang::get("notification.profile.admin_update_failed"), 'danger');
            }
        } catch (Exception $e) {
            Gondolyn::notification($e->getMessage(), 'danger');
        }

        return redirect('admin/users/editor/'.Crypto::encrypt($user->id));
    }

    public function deactivate()
    {
        $user = Session::get("userInEditor");

        if ( ! $user) {
            return redirect('errors/general');
        }

        Accounts::modifyAccountStatus($user->id, "inactive");

        return redirect('admin/users/editor/'.Crypto::encrypt($user->id));
    }

    public function activate()
    {
        $user = Session::get("userInEditor");

        if ( ! $user) {
            return redirect('errors/general');
        }

        Accounts::modifyAccountStatus($user->id, "active");

        return redirect('admin/users/editor/'.Crypto::encrypt($user->id));
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

        Gondolyn::notification(Lang::get("notification.profile.admin_delete"), 'success');

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
