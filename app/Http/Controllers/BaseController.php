<?php

use App\Services\AccountServices;

class BaseController extends Controller
{
    /**
     * Sharing is caring!
     */
    public function __construct()
    {
        View::share(Config::get("gondolyn.basic-app-info"));
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

    public function validateRequest($request, $rules)
    {
        $validator = $this->getValidationFactory()->make($request->all(), $rules);

        if ($validator->fails()) {
            $msg = "";

            foreach ($validator->errors()->getMessages() as $field => $errorMsg) {
                $msg .= $errorMsg[0] . ". ";
            }

            $msg = substr($msg, 0, strlen($msg) - 1);

            throw new HttpResponseException(Response::json(ResponseManager::makeError(ERROR_CODE_VALIDATION_FAILED, $msg)));
        }
    }

    public function throwRecordNotFoundException($message, $code = 0)
    {
        throw new HttpResponseException(Response::json(ResponseManager::makeError($code, $message)));
    }

}
