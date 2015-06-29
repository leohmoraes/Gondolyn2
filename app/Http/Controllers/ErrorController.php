<?php

class ErrorController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Error Controller
    |--------------------------------------------------------------------------
    |
    | Error pages that are generated for the users.
    |
    */

    /**
     * The layout that should be used for all regular error responses.
     */
    protected $layout = 'layouts.master';

    public function general()
    {
        $data = array(
            "page_title" => "General Error",
            "page_keywords" => "",
            "page_description" => "",
            "page_details" => ""
        );

        $error = Session::get("notification");

        $error = (strpos($error, "NotFoundHttpException") > 0) ? Lang::get("notification.error.lost") : $error;

        $data['error'] = $error ?: Lang::get("notification.fourofour.general");

        return view('errors.general', $data, [], 404);
    }

    public function critical()
    {
        $data = array(
            "page_title" => "Critical Error",
            "page_keywords" => "",
            "page_description" => "",
            "page_details" => ""
        );

        try {
            if ( ! Session::has("data")) {
                throw new Exception("You do not have an error", 1);
            }

            $data['error'] = Session::get("data");

            Mail::send('emails.critical', $data, function($message) {
                $message->to(Config::get('gondolyn.appAdminEmail'), Config::get('gondolyn.appAdminName'))->subject('Critical Error!');
            });
        } catch (Exception $e) {
            Session::flash("data", $e->getMessage());
            return redirect('errors/general');
        }

        $data['error'] = "You've encountered a critical error. A notification has been sent to the IT team, we're sorry for any inconvenience";

        Session::flush();

        return view('errors.critical', $data, [], 500);
    }

}
