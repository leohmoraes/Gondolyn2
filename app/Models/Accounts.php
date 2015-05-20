<?php

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Contracts\Billable as BillableContract;
use Carbon\Carbon;

class Accounts extends Eloquent implements AuthenticatableContract, CanResetPasswordContract, BillableContract
{
    use Billable;
    use Authenticatable;
    use CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('user_passwd', 'remember_token');

    /**
     * Special dates for subscriptions
     * @var array
     */
    protected $dates = ['trial_ends_at', 'subscription_ends_at'];

    public static function getAccount($id)
    {
        return Accounts::findOrFail($id);
    }

    public static function getAccountByEmail($email)
    {
        return Accounts::where('user_email', '=', $email)->firstOrFail();
    }

    public static function getAllMembers()
    {
        return Accounts::where('user_role', '=', 'member')->get();
    }

    public static function getMember($id)
    {
        $users = Accounts::where('id', '=', $id)->get();
        return $users[0];
    }

    public static function updateAccount($id)
    {
        $user = Accounts::findOrFail($id);

        $user->user_email = Input::get("email");
        $user->user_name = Input::get("username");
        $user->user_alt_email = Input::get("alt_email");
        $user->user_role = Input::get("role") ?: 'member';

        foreach ($user['attributes'] as $column => $value) {
            if ( ! is_null(Input::get($column))) {
                $user->$column = Input::get($column);
            }
        }

        $user->save();

        return true;
    }

    public function generateNewPassword($id)
    {
        $user = Accounts::findOrFail($id);

        $newPassword = Utilities::addSalt(20);

        $user->user_passwd = Crypt::encrypt($user->user_salt.hash("sha256", $newPassword));
        $user->save();

        return $newPassword;
    }

    /*
    |--------------------------------------------------------------------------
    | Subscriptions
    |--------------------------------------------------------------------------
    */

    public static function updateMyPassword($id)
    {
        $user = Accounts::findOrFail($id);

        $pwd = Crypt::decrypt($user->user_passwd);

        $realPasswd = substr($pwd, 10);

        if ($realPasswd == hash("sha256", Input::get("old_password"))) {
            $user->user_passwd = Crypt::encrypt($user->user_salt.hash("sha256", Input::get("new_password")));
            $result = $user->save();

            return $result;
        }

        return false;
    }

    public static function setAccountSubscription($id, $plan)
    {
        $trial = Config::get("gondolyn.trial");
        $packages = Config::get("gondolyn.packages");

        $myplan = $packages[$plan];

        $user = Accounts::findOrFail($id);

        $creditCardToken = Input::get("stripeToken");

        if ($user->cancelled()) $user->subscription($myplan['stripe_id'])->resume($creditCardToken);
        else $user->subscription($myplan['stripe_id'])->create($creditCardToken);

        if ($trial > 0) {
            $user->trial_ends_at = Carbon::now()->addDays($trial);
            $user->save();
        }

        return true;
    }

    public function updateAccountSubscription($id, $plan)
    {
        $user = Accounts::findOrFail($id);

        $packages = Config::get("gondolyn.packages");

        $myplan = $packages[$plan];

        $user->subscription($myplan['stripe_id'])->swap();

        return true;
    }

    public function cancelSubscription($id)
    {
        $user = Accounts::find($id);
        $user->subscription()->cancel();

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    */

    public function loginWithEmail($useremail, $password, $remember_me)
    {
        if ($useremail === '' || $password === '') {
            throw new Exception("Missing login credentials", 1);
        }

        $user = Accounts::where('user_email', '=', $useremail)->first();

        if (is_object($user)) {
            return $this->login($user, $useremail, $password, $remember_me);
        } elseif (is_null($user) && Config::get("gondolyn.signup")) {
            $data = array(
                'email' => $useremail,
                'password' => $password,
            );
            return $this->makeNewAccount($data, "email", $remember_me);
        } else {
            return false;
        }
    }

    public function loginWithSocialMedia($data, $account)
    {
        // Typecast the data because everyone likes to give back different things.
        $data = (array) $data;

        $account_id = "user_".$account."_id";

        $userID = (isset($data['user']['id'])) ? $data['user']['id'] : $data['id'];

        // Attempt to find a matching ID
        $user = Accounts::where($account_id, '=', $userID)->first();

        if (is_object($user) && ! is_null($user)) {
            return $user;
        } else {
            // Look for a matching Email
            if (isset($data['user']['email'])) {
                $user = Accounts::where('user_email', '=', $data['user']['email'])->first();

                if (is_object($user) && $user->user_role !== 'inactive') {
                    return $user;
                } else if (Config::get("gondolyn.signup") && ! is_object($user)) {
                    return $this->makeNewAccount($data, $account);
                } else {
                    throw new Exception("We're sorry either your account has been deactivated or you have not registered with us before.", 1);
                }
            } else {
                // means the service doesn't have an email provided by the API
                return true;
            }
        }
    }

    public function login($user, $useremail, $password, $remember_me = null)
    {
        $pwd = Crypt::decrypt($user->user_passwd);
        $exp = substr($pwd, 0, 10);
        $realPasswd = substr($pwd, 10);

        // Check for remember me
        $remember = ($remember_me === "on") ? TRUE : FALSE;

        // Minutes for the remember me
        $minutes = Config::get("gondolyn.remember_me_duration");

        if ($exp == $user->user_salt && $realPasswd == hash("sha256", $password) && $user->user_role !== 'inactive') {
            // Utilize Laravel Auth
            Auth::login($user, $remember);

            if ($remember) {
                Cookie::queue('email', $useremail, $minutes);
                Cookie::queue('password', $password, $minutes);
            }

            // Set the API auth token
            $user->user_api_token = md5(Utilities::addSalt(30));
            $user->save();

            return $user;
        } else {
            return false;
        }
    }

    public function verifyAccountEmail($email, $account)
    {
        $data = array();

        $account_id = "user_".$account."_id";

        $user = Accounts::where('user_email', '=', $email)->first();

        if (is_object($user)) {
            $user->$account_id = Session::get("twitterID");
            $user->save();

            Session::forget("twitterID");

            return $user;
        } else {
            $data['email'] = $email;
            $data['id'] = Session::get("twitterID");
            $data['screen_name'] = Session::get("twitterScreenName");

            Session::forget("twitterID");
            Session::forget("twitterScreenName");

            if (Config::get("gondolyn.signup")) {
                return $this->makeNewAccount($data, $account);
            } else {
                throw new Exception("We're sorry we cannot find you and this application is not currently accepting sign ups.", 1);
            }
        }
    }

    public static function modifyUserStatus($id, $status)
    {
        $user = Accounts::findOrFail($id);

        $user->user_active = $status;
        $user->save();

        return true;
    }

    public function deleteMyAccount($id)
    {
        if (Session::get("id") == $id) {
            $user = Accounts::find($id);
            return $user->delete();
        } else return false;
    }

    public static function deleteAccount($id)
    {
        if (Session::get("role") == "admin") {
            $user = Accounts::find($id);
            return $user->delete();
        } else return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Private Methods
    |--------------------------------------------------------------------------
    */

    private function makeNewAccount($data, $account, $remember_me = false)
    {
        // Check for remember me
        $remember = ($remember_me === "on") ? TRUE : FALSE;

        $currentUserCount = DB::table('users')->get();

        $account_id = "user_".$account."_id";

        $pwd = Utilities::addSalt(20);
        $userSalt = Utilities::addSalt(10);

        $user = new Accounts;

        if ($account != "email")    $user->$account_id = $data['id'];
        if ($account == "twitter")  $user->user_name = $data['screen_name'];
        if ($account == "email")    $pwd = $data['password'];

        $user->user_email       = $data['email'];
        $user->user_salt        = $userSalt;
        $user->user_active      = "active";
        $user->user_api_token   = md5(Utilities::addSalt(30));
        $user->user_passwd      = Crypt::encrypt($userSalt.hash("sha256", $pwd));
        $user->user_role        = (count($currentUserCount) == 0) ? "admin" : Config::get('permissions.matrix.default_role');

        $user->save();

        $data['newPassword'] = $pwd;

        // Utilize Laravel Auth
        Auth::login($user, $remember);

        Session::flash("email", $data['email']);

        if ($account != "email") {
            Mail::send('emails.newpassword', $data, function ($message) {
                $user = $this->getAccountByEmail(Session::get("email"));
                $message->to($user->user_email)->subject('New Password!');
            });
        }

        return $user;
    }

}
