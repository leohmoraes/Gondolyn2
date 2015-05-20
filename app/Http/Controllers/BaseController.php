<?php

use App\Services\AccountServices;

class BaseController extends Controller
{
    public function __construct()
    {
        // Check for remember me
        AccountServices::isAccountRemembered();
    }

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout()
    {
        if ( ! is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }

}
