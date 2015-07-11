<?php namespace App\Modules\Sample\Controllers;

use Auth;
use Input;
use Redirect;
use View;
use Config;
use Session;
use Gondolyn;
use Illuminate\Http\Request;
use Validation;
use App\Modules\Sample\Services\SampleService;
use App\Modules\Sample\Models\Samples;

/**
 * Sample Module Controller
 */
class SampleController extends \BaseController
{
    protected $layout = 'layouts.master';

    public function __construct()
    {
        \Log::info("Loading Sample Module");
        $this->middleware('security.guard');
    }

    public function main(Request $request)
    {
        $service = new SampleService;

        $sort = $request->input('sort');
        $data = $service->serviceInformation(Config::get("gondolyn.appInfo"));
        $sorter = SampleService::sorter($sort);

        $data['dir']            = $sorter['dir'];
        $data['samples']        = $service->getSamples($sorter['sortby']);

        return view('sample::sample', $data);
    }

    public function api()
    {
        return Gondolyn::response("success", "You have accessed a module");
    }

    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    */

    public function editRow()
    {
        $validation = Validation::check(Samples::$rules);

        if ($validation['errors']) {
            return Gondolyn::response("error", "Your information was not saved");
        }

        Samples::editSample(Input::get('_id'));
        return Gondolyn::response("success", "Your information was saved.");
    }
}
