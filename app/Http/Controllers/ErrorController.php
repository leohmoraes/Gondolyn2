<?php

class ErrorController extends BaseController {

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
            "page_assets" => "",
            "page_details" => ""
        );

        $error = Session::get("notification");

        $error = (strpos($error, "NotFoundHttpException") > 0) ? Lang::get("notification.error.lost") : $error;

        $data['error'] =  $error ?: Lang::get("notification.fourofour.general");

        $layoutData = [
            "metadata"          => View::make('metadata', $data),
            "general"           => View::make('common', $data),
            "gondolyn_login"    => View::make('user.login-panel', $data),
            "nav_bar"           => View::make('navbar', $data),
            "content"           => View::make('errors.general', $data),
        ];

        return view($this->layout, $layoutData);
	}

    public function critical()
    {
        $data = array(
            "page_title" => "Critical Error",
            "page_keywords" => "",
            "page_description" => "",
            "page_assets" => "",
            "page_details" => ""
        );

        try
        {
            if ( ! Session::has("data"))
            {
                throw new Exception("You do not have an error", 1);
            }

            $data['error'] = Session::get("data");

            Mail::send('emails.critical', $data, function($message)
            {
                $message->to('mattlantz@gmail.com', 'Matt Lantz')->subject('Critical Error!');
            });
        }
        catch (Exception $e)
        {
            Session::flash("data", $e->getMessage());
            return Redirect::to('errors/general');
        }

        $data['error'] = "You've encountered a critical error. A notification has been sent to the AFBS IT team, we're sorry for any inconvenience";

        Session::flush();

        $layoutData = [
            "metadata"          => View::make('metadata', $data),
            "general"           => View::make('common', $data),
            "gondolyn_login"    => View::make('user.login-panel', $data),
            "nav_bar"           => View::make('navbar', $data),
            "content"           => View::make('errors.critical', $data),
        ];

        return view($this->layout, $layoutData);
    }

}