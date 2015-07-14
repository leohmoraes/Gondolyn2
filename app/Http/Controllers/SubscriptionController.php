<?php

use App\Services\AccountServices;

class SubscriptionController extends BaseController
{
    protected $layout = 'layouts.master';

    public function __construct()
    {
        $this->middleware('security.guard');
    }

    public function subscription()
    {
        $data = Config::get("gondolyn.appInfo");
        $data['page_title'] = Lang::get('titles.subscription');

        $user = Accounts::getAccount(Session::get("id"));

        $gravatarHash = md5(strtolower(trim($user->user_email)));
        $profileImage = ($user->profile == "") ? null : Utilities::fileAsPublicAsset($user->profile);

        $data['profileImage'] = $profileImage ?: 'http://www.gravatar.com/avatar/'.$gravatarHash.'?s=300';
        $data['user'] = $user;
        $data['packages'] = Config::get("gondolyn.packages");
        $data['subscriptionPostURL'] = URL::to('account/settings/set/subscription');

        if ($data['user']->subscribed() && $user->stripe_active == 1) {
            $view = view('account.subscription-change', $data);
        } else if ($data['user']->subscribed() && $user->stripe_active == 0) {
            $view = view('account.subscription-set', $data);
        } else {
            $view = view('account.subscription-set', $data);
        }
        return $view;
    }

    public function subscriptionChangeCard()
    {
        $data = Config::get("gondolyn.appInfo");
        $data['page_title'] = Lang::get('titles.subscription');

        $user = Accounts::getAccount(Session::get("id"));

        $gravatarHash = md5(strtolower(trim($user->user_email)));
        $profileImage = ($user->profile == "") ? null : Utilities::fileAsPublicAsset($user->profile);

        $data['profileImage']       = $profileImage ?: 'http://www.gravatar.com/avatar/'.$gravatarHash.'?s=300';
        $data['changeCard']         = true;
        $data['user']               = $user;
        $data['packages']           = Config::get("gondolyn.packages");
        $data['subscriptionPostURL'] = URL::to('account/settings/change-card/subscription');

        return view('account.subscription-set', $data);
    }

    public function subscriptionInvoices()
    {
        $data = Config::get("gondolyn.appInfo");
        $data['page_title'] = Lang::get('titles.subscription-invoices');

        $user = Accounts::getAccount(Session::get("id"));
        $gravatarHash = md5(strtolower(trim($user->user_email)));
        $profileImage = ($user->profile == "") ? null : Utilities::fileAsPublicAsset($user->profile);

        $invoices = $user->invoices();

        $data['profileImage'] = $profileImage ?: 'http://www.gravatar.com/avatar/'.$gravatarHash.'?s=300';
        $data['invoices'] = View::make('account.invoice', array('invoices' => $invoices));

        return view('account.subscription-invoices', $data);
    }

    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    */

    public function setSubscription()
    {
        $user = Auth::user();

        if (empty($user->country) || empty($user->state)) {
            Gondolyn::notification(Lang::get("notification.subscription.missing_info"), 'danger');
            return redirect('account/settings');
        }

        // check if user has country and state set
        try {
            $users = new Accounts;
            $status = $users->setAccountSubscription(Session::get("id"), Input::get("plan"));

            if ($status) {
                Gondolyn::notification(Lang::get("notification.subscription.success"), 'success');
            } else {
                Gondolyn::notification(Lang::get("notification.subscription.failed"), 'danger');
            }
        } catch (Exception $e) {
            Gondolyn::notification($e->getMessage(), 'danger');
            return redirect('errors/general');
        }

        return redirect('account/settings');
    }

    public function updateSubscription()
    {
        $user = Auth::user();

        if (empty($user->country) || empty($user->state)) {
            Gondolyn::notification(Lang::get("notification.subscription.missing_info"), 'danger');
            return redirect('account/settings');
        }

        try {
            $users = new Accounts;
            $status = $users->updateAccountSubscription(Session::get("id"), Input::get("plan"));

            if ($status) {
                Gondolyn::notification(Lang::get("notification.subscription.success"), 'success');
            } else {
                Gondolyn::notification(Lang::get("notification.subscription.failed"), 'danger');
            }
        } catch (Exception $e) {
            Gondolyn::notification(Lang::get("notification.general.error"), 'danger');
            return redirect('errors/general');
        }

        return redirect('account/settings');
    }

    public function changeCardSubscription()
    {
        $user = Auth::user();

        if (empty($user->country) || empty($user->state)) {
            Gondolyn::notification(Lang::get("notification.subscription.missing_info"), 'danger');
            return redirect('account/settings');
        }

        try {
            $users = new Accounts;
            $status = $users->changeCardAccountSubscription(Session::get("id"), Input::get("stripeToken"));

            if ($status) {
                Gondolyn::notification(Lang::get("notification.subscription.success"), 'success');
            } else {
                Gondolyn::notification(Lang::get("notification.subscription.failed"), 'danger');
            }
        } catch (Exception $e) {
            Gondolyn::notification(Lang::get("notification.general.error"), 'danger');
            return redirect('errors/general');
        }

        return redirect('account/settings');
    }

    public function cancelSubscription()
    {
        try {
            $users = new Accounts;
            $status = $users->cancelSubscription(Session::get("id"));

            if ($status) {
                Gondolyn::notification(Lang::get("notification.subscription.cancel_success"), 'success');
            } else {
                Gondolyn::notification(Lang::get("notification.subscription.cancel_failed"), 'danger');
            }
        } catch (Exception $e) {
            Gondolyn::notification(Lang::get("notification.general.error"), 'danger');
            return redirect('errors/general');
        }

        return redirect('account/settings');
    }

    public function downloadInvoice($id)
    {
        $user = Accounts::getAccount(Session::get("id"));

        $invoice = Crypto::decrypt($id);
        $plan = Config::get('gondolyn.packages.'.Session::get('plan'));

        return $user->downloadInvoice($invoice, [
            'vendor'    => Config::get("gondolyn.company"),
            'street'    => Config::get("gondolyn.street"),
            'location'  => Config::get("gondolyn.location"),
            'phone'     => Config::get("gondolyn.phone"),
            'url'       => Config::get("gondolyn.url"),
            'product'   => Config::get("gondolyn.product"),
            'description'   => 'Subscription ('.$plan['name'].')',
        ]);
    }
}
