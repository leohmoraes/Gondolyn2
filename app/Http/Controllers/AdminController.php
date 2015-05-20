<?php

use App\Services\AppServices;

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

        $data['users'] = Accounts::getAllMembers();

        return view('admin.users', $data);
    }

    public function editor($id)
    {
        $data = Config::get("gondolyn.basic-app-info");

        $user = Accounts::getMember(Crypto::decrypt($id));

        Session::set("userInEditor", $user);

        $gravatarHash = md5( strtolower( trim( $user->user_email ) ) );

        $data['user']               = $user;
        $data['gravatar']           = $gravatarHash;
        $data['notification']       = Session::get("notification") ?: "";
        $data['options']            = Config::get("permissions.matrix.roles");
        $data['adminEditorMode']    = true;
        $data['shippingColumns']    = Config::get('forms.shipping');

        // $data["adminModals"]  = View::make('admin.modals');
        // $data["selectRole"]  = View::make('account.selectRole');

        return view('account.settings', $data);
    }

    public function update()
    {
        try {
            $user = Session::get("userInEditor");
            $status = Accounts::updateAccount($user->id);

            if ($status) {
                Session::flash("notification", "The profile was successfully updated.");
            } else {
                Session::flash("notification", "The profile failed to update.");
            }
        } catch (Exception $e) {
            Session::flash("notification", "We seem to have encountered an error");
            return redirect('errors/general');
        }

        return redirect('admin/editor/'.Crypto::encrypt($user->id));
    }

    public function deactivate()
    {
        $user = Session::get("userInEditor");

        Accounts::modifyUserStatus($user->id, "inactive");

        return redirect('admin/editor/'.Crypto::encrypt($user->id));
    }

    public function activate()
    {
        $user = Session::get("userInEditor");

        Accounts::modifyUserStatus($user->id, "active");

        return redirect('admin/editor/'.Crypto::encrypt($user->id));
    }

    /**
     * Delete User Account
     *
     * @return void
     */
    public function delete()
    {
        $user = Session::get("userInEditor");

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
            dd("Successful Submission");
        } else {
            return $validation['redirect'];
        }
    }
}
