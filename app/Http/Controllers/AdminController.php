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

        $layoutData = [
            "metadata"          => View::make('metadata', $data),
            "general"           => View::make('common', $data),
            "nav_bar"           => View::make('navbar', $data),
            "form"              => View::make('admin.form'),
            "content"           => View::make('admin.home', $data),
        ];

        return view($this->layout, $layoutData);
    }

    public function users()
    {
        Session::set("userInEditor", null);

        $data = Config::get("gondolyn.basic-app-info");

        $data['users'] = Users::getAllMembers();

        $layoutData = [
            "metadata"          => View::make('metadata', $data),
            "general"           => View::make('common', $data),
            "nav_bar"           => View::make('navbar', $data),
            "content"           => View::make('admin.users', $data),
        ];

        return view($this->layout, $layoutData);
    }

    public function editor($id)
    {
        $data = Config::get("gondolyn.basic-app-info");

        $user = Users::getMember(Crypto::decrypt($id));

        Session::set("userInEditor", $user);

        $gravatarHash = md5( strtolower( trim( $user->user_email ) ) );

        $data['user']               = $user;
        $data['gravatar']           = $gravatarHash;
        $data['notification']       = Session::get("notification") ?: "";
        $data['options']            = Config::get("permissions.matrix.roles");
        $data['adminEditorMode']    = true;

        $layoutData = [
            "metadata"          => View::make('metadata', $data),
            "general"           => View::make('common', $data),
            "nav_bar"           => View::make('navbar', $data),
            "adminModals"       => View::make('admin.modals', $data),
            "selectRole"        => View::make('user.selectRole', $data),
            "content"           => View::make('user.settings', $data),
        ];

        return view($this->layout, $layoutData);
    }

    public function update()
    {
        try {
            $user = Session::get("userInEditor");
            $status = Users::updateProfile($user->id);

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

        Users::modifyUserStatus($user->id, "inactive");

        return redirect('admin/editor/'.Crypto::encrypt($user->id));
    }

    public function activate()
    {
        $user = Session::get("userInEditor");

        Users::modifyUserStatus($user->id, "active");

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

        Users::deleteAccount($user->id);

        Session::flash("notification", "The user was successfully deleted");

        return redirect('admin/users');
    }

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
