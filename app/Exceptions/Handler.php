<?php namespace App\Exceptions;

use Exception;
use Gondolyn;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        'Symfony\Component\HttpKernel\Exception\HttpException'
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    // *
    //  * Render an exception into an HTTP response.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @param  \Exception  $e
    //  * @return \Illuminate\Http\Response

    // public function render($request, Exception $e)
    // {
    //  return parent::render($request, $e);
    // }

    /**
     * Render an exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if (is_a($e, 'Symfony\Component\HttpKernel\Exception\NotFoundHttpException')) {
            return redirect('errors/general');
        }

        if (is_a($e, 'App\Exceptions\LoginException') || is_a($e, 'App\Exceptions\PermissionException')) {
            Gondolyn::notification($e->getMessage());
            return redirect('errors/general');
        }

        if (stristr($request->url(), 'api')) {
            return \Gondolyn::response('error', $e->getMessage());
        }

        return parent::render($request, $e);
    }

}
