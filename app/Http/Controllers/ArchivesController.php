<?php

use App\Http\Requests;
use App\Http\Requests\CreateArchivesRequest;
use Illuminate\Http\Request;
use App\Services\Repositories\ArchivesRepository;
use App\Models\Archives;

class ArchivesController extends BaseController
{

    /** @var  ArchivesRepository */
    private $archivesRepository;

    function __construct(ArchivesRepository $archivesRepo)
    {
        parent::__construct();
        Permission::role('groups.all');
        $this->archivesRepository = $archivesRepo;
    }

    /**
     * Display a listing of the Archives.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $input = $request->all();

        $result = $this->archivesRepository->search($input);

        $archives = $result[0];

        $attributes = $result[1];

        return view('archives.index')
            ->with('archives', $archives)
            ->with('attributes', $attributes);;
    }

    /**
     * Show the form for creating a new Archives.
     *
     * @return Response
     */
    public function create()
    {
        return view('archives.create');
    }

    /**
     * Store a newly created Archives in storage.
     *
     * @param CreateArchivesRequest $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $validation = Validation::check(Archives::$rules);

        if ( ! $validation['errors']) {
            $archives = $this->archivesRepository->store($request->all());
        } else {
            return $validation['redirect'];
        }

        Gondolyn::notification('Archives saved successfully.');

        return redirect(route('archives.index'));
    }

    /**
     * Display the specified Archives.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $archives = $this->archivesRepository->findArchivesById($id);

        if (empty($archives))
        {
            Gondolyn::notification('Archives not found');
            return redirect(route('archives.index'));
        }

        return view('archives.show')->with('archives', $archives);
    }

    /**
     * Show the form for editing the specified Archives.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $archives = $this->archivesRepository->findArchivesById($id);

        if (empty($archives))
        {
            Gondolyn::notification('Archives not found');
            return redirect(route('archives.index'));
        }

        return view('archives.edit')->with('archives', $archives);
    }

    /**
     * Update the specified Archives in storage.
     *
     * @param  int    $id
     * @param CreateArchivesRequest $request
     *
     * @return Response
     */
    public function update($id, CreateArchivesRequest $request)
    {
        $archives = $this->archivesRepository->findArchivesById($id);

        if (empty($archives))
        {
            Gondolyn::notification('Archives not found');
            return redirect(route('archives.index'));
        }

        $archives = $this->archivesRepository->update($archives, $request->all());

        Gondolyn::notification('Archives updated successfully.');

        return redirect(route('archives.index'));
    }

    /**
     * Remove the specified Archives from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $archives = $this->archivesRepository->findArchivesById($id);

        if (empty($archives))
        {
            Gondolyn::notification('Archives not found');
            return redirect(route('archives.index'));
        }

        $archives->delete();

        Gondolyn::notification('Archives deleted successfully.');

        return redirect(route('archives.index'));
    }

}
