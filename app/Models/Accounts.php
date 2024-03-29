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

    public static $rules = [
        'user_email' => 'email|required',
        'user_alt_email' => 'email',
        'two_factor_phone' => 'required_if:two_factor_enabled,on',
    ];

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

    public function getBillableName()
    {
        $user = Accounts::getAccount(Session::get("id"));
        return $user->user_email;
    }

    /**
     * Get Tax Percentage for subscriptions
     * @return float
     */
    public function getTaxPercent()
    {
        $rates = Config::get('gondolyn.tax');
        $user = Auth::user();

        if (isset($user) && $user->country === 'CA') {
            return $rates[strtolower($user->state)];
        }

        return 0.00;
    }

    /**
     * Get account
     * @param  integer $id Account ID
     * @return mixed
     */
    public static function getAccount($id)
    {
        return Accounts::findOrFail($id);
    }

    /**
     * Get an account by email
     * @param  string $email Email address
     * @return mixed
     */
    public static function getAccountByEmail($email)
    {
        return Accounts::where('user_email', '=', $email)->firstOrFail();
    }

    /**
     * Get all Accounts
     * @return array
     */
    public static function getAllAccounts()
    {
        return Accounts::where('user_role', '=', 'member')->paginate(25);
    }

    /**
     * Update Account
     * @param  integer $id User Id
     * @return bool
     */
    public static function updateAccount($id)
    {
        $user = Accounts::findOrFail($id);
        $shouldBeMe = Accounts::where('user_email', '=', Input::get("email"))->first();

        if (is_null($shouldBeMe) || $shouldBeMe->user_passwd === $user->user_passwd) {
            $file = Utilities::saveFile('profile', 'profiles/', ['jpg', 'jpeg', 'png', 'gif']);

            $user->user_email               = Input::get("email");
            $user->user_name                = Input::get("username");
            $user->user_alt_email           = Input::get("alt_email");
            $user->in_app_notifications     = Input::get("in_app_notifications");
            $user->user_role                = Input::get("role") ?: 'member';

            if ($file) {
                $user->profile = $file['name'];
            }

            foreach ($user['attributes'] as $column => $value) {
                if ( ! is_null(Input::get($column))) {
                    $user->$column = Input::get($column);
                }
            }

            if (Config::get('gondolyn.twoFactorAuthentication.enabled')) {
                $user->two_factor_enabled       = Input::get("two_factor_enabled");
                $user->two_factor_phone       = preg_replace('/[^\dxX]/', '', Input::get("two_factor_phone"));

                if (Input::get("two_factor_enabled") === "on" && strlen(Input::get("two_factor_phone")) < 8) {
                    return false;
                }

                if ($user->two_factor_enabled) {
                    $code = rand(111111, 999999);
                    AccountServices::sendTwoFactorAuthenticationCode($code, $user->two_factor_phone);
                    Accounts::setTwoFactorCode($user->id, $code);
                }
            }

            return $user->save();
        } else {
            return false;
        }
    }

    /**
     * Generate new password
     * @param  integer $id User Id
     * @return string
     */
    public static function generateNewPassword($id)
    {
        $user = Accounts::findOrFail($id);
        $newPassword = Utilities::addSalt(20);

        $user->user_passwd = Crypt::encrypt($user->user_salt.hash("sha256", $newPassword));
        $user->save();

        return $newPassword;
    }

    /**
     * Set the two factor authentication code
     * @param  integer $id User Id
     * @param  string $code Two Factor Code
     * @return string
     */
    public static function setTwoFactorCode($id, $code)
    {
        $user = Accounts::findOrFail($id);
        $user->two_factor_code = $code;

        return $user->save();
    }

    /**
     * Update Password
     * @param  integer $id User Id
     * @return bool
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

    /*
    |--------------------------------------------------------------------------
    | Subscriptions
    |--------------------------------------------------------------------------
    */

    /**
     * Set the account's subscription
     * @param integer $id   Account Id
     * @param string $plan Plan Id
     *
     * @return  bool
     */
    public static function setAccountSubscription($id, $plan)
    {
        $trial = Config::get("gondolyn.trial");
        $packages = Config::get("gondolyn.packages");

        $myplan = $packages[$plan];

        $user = Accounts::findOrFail($id);

        $creditCardToken = Input::get("stripeToken");

        if ($user->cancelled()) {
            $user->subscription($myplan['stripe_id'])->resume($creditCardToken);
        } else {
            $user->subscription($myplan['stripe_id'])->create($creditCardToken);
            Session::put(["subscribed" => true, "plan" => $myplan['stripe_id']], null);
        }

        if ($trial > 0) {
            $user->trial_ends_at = Carbon::now()->addDays($trial);
            $user->save();
        }

        return true;
    }

    /**
     * Change Credit Card for Subscription
     * @param  integer $id User id
     * @param  string $creditCardToken Credit Card token
     * @return bool
     */
    public function changeCardAccountSubscription($id, $creditCardToken)
    {
        $user = Accounts::findOrFail($id);

        $user->updateCard($creditCardToken);

        return true;
    }

    /**
     * Update Subscription
     * @param  integer $id   User Id
     * @param  string $plan Plan ID
     * @return bool
     */
    public function updateAccountSubscription($id, $plan)
    {
        $user = Accounts::findOrFail($id);

        $packages = Config::get("gondolyn.packages");

        $myplan = $packages[$plan];

        $user->subscription($myplan['stripe_id'])->swap();

        Session::put("plan", $myplan['stripe_id']);

        return true;
    }

    /**
     * Cancel the subscription
     * @param  integer $id User Id
     * @return bool
     */
    public function cancelSubscription($id)
    {
        $user = Accounts::find($id);
        $user->subscription()->cancel();
        Session::put("plan", null);

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    */

    /**
     * Login with Email
     * @param  string $useremail   Email address
     * @param  string $password    Password
     * @param  mixed $remember_me Remember me
     * @return mixed
     */
    public function loginWithEmail($useremail, $password, $remember_me)
    {
        if ($useremail === '' || $password === '') {
            throw new Exception(Lang::get('notifications.login.missing'), 1);
        }

        $user = Accounts::where('user_email', '=', $useremail)->first();

        $data = array(
            'email' => $useremail,
            'password' => $password,
        );

        $checkedUser = $this->checkAccountStatus($user, $data, "email");

        return $this->login($checkedUser, $useremail, $password, $remember_me);
    }

    /**
     * Login with Social Media
     * @param  array $data    SM data
     * @param  string $account Account type
     * @return mixed
     */
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
                return $this->checkAccountStatus($user, $data, $account);
            } else {
                // means the service doesn't have an email provided by the API
                return true;
            }
        }
    }

    /**
     * Check the account statis
     * @param  object $user User Object
     * @param  array  $data User data
     * @param  string $account Account type
     * @return mixed
     */
    public function checkAccountStatus($user, $data, $account)
    {
        if (is_object($user) && $user->user_active !== 'inactive') {
            $result = $user;
        } else if (is_object($user) && $user->user_active === 'inactive') {
            throw new Exception(Lang::get('notification.login.deactivated'), 1);
        } else {
            $result = $this->makeNewAccount($data, $account);
        }

        return $result;
    }

    /**
     * Perform a login
     * @param  object $user        User Object
     * @param  string $useremail   User email
     * @param  string $password    User password
     * @param  mixed $remember_me Remember me
     * @return mixed
     */
    public function login($user, $useremail, $password, $remember_me = null)
    {
        $pwd = Crypt::decrypt($user->user_passwd);
        $exp = substr($pwd, 0, 10);
        $realPasswd = substr($pwd, 10);

        // Check for remember me
        $remember = ($remember_me === "on") ? TRUE : FALSE;

        // Minutes for the remember me
        $minutes = Config::get("gondolyn.rememberMeDuration");

        if ($exp == $user->user_salt && $realPasswd == hash("sha256", $password) && $user->user_role !== 'inactive') {

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

    /**
     * Verify Account By Email
     * @param  string $email   Email address
     * @param  string $account Account type
     * @return object
     */
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

            return $this->makeNewAccount($data, $account);
        }
    }

    /**
     * Modify Account Status
     * @param  integer $id     User ID
     * @param  string $status Account Status
     * @return bool
     */
    public static function modifyAccountStatus($id, $status)
    {
        $user = Accounts::findOrFail($id);

        $user->user_active = $status;
        $user->save();

        return true;
    }

    /**
     * Delete the account
     * @param  integer $id User ID
     * @return bool
     */
    public static function deleteAccount($id)
    {
        $user = Accounts::find($id);

        if (Session::get("id") == $id || Session::get("role") == "admin") {
            return $user->delete();
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Private Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Make a new Account
     * @param  array  $data        Account data
     * @param  string  $account     Account type
     * @param  boolean $remember_me Remember me?
     * @param  boolean $sendMail Send an email?
     * @return object               User
     */
    public function makeNewAccount($data, $account, $remember_me = false, $sendMail = false)
    {
        $socialMedia = false;

        // Do we allow new accounts?
        if ( ! Config::get("gondolyn.signUp")) {
            throw new Exception(Lang::get('notification.login.denied'), 1);
        }

        // Check for remember me
        $remember = ($remember_me === "on") ? TRUE : FALSE;

        $minutes = Config::get("gondolyn.rememberMeDuration");

        if ($remember) {
            Cookie::queue('email', $data['email'], $minutes);
            Cookie::queue('password', $data['password'], $minutes);
        }

        $currentUserCount = DB::table('users')->get();

        $user = new Accounts;

        if ($account !== 'email') {
            $account_id = "user_".$account."_id";
            $pwd = Utilities::addSalt(20);
            $user->$account_id = $data['id'];
            $sendMail = true;
            $socialMedia = true;
        } else {
            $pwd = $data['password'];
        }

        $userSalt = Utilities::addSalt(10);

        $user->user_email       = $data['email'];
        $user->user_salt        = $userSalt;
        $user->user_active      = (Config::get('gondolyn.confirmEmail')) ? "active" : "inactive";
        $user->user_api_token   = md5(Utilities::addSalt(30));
        $user->user_passwd      = Crypt::encrypt($userSalt.hash("sha256", $pwd));

        if ( ! isset($data['role'])) {
            $user->user_role        = (count($currentUserCount) == 0) ? "admin" : Config::get('permissions.matrix.default_role');
        } else {
            $user->user_role        = $data['role'];
        }

        $user->in_app_notifications = 'on';

        $user->save();

        $data['newPassword'] = $pwd;

        $email = $data['email'];

        $data['socialMedia'] = $socialMedia;

        if ($sendMail || $socialMedia) {
            Mail::send('emails.new-account', $data, function($message) use ($email) {
                $user = $this->getAccountByEmail($email);
                $message->to($user->user_email)->subject('New Account!');
            });
        }

        return $user;
    }

}
