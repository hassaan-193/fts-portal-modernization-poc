<?php

namespace App\Http\Controllers;

use Flash;
use Response;
use Carbon\Carbon;
use App\Models\Quotation;
use App\Models\Project;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\DataTables\VisitTrackingDataTable;
use App\DataTables\ProjectDataTable;
use App\Repositories\ProjectRepository;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\CreateProjectRequest;
use App\Http\Requests\ProjectLpooutRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Requests\ProjectExtensionRequest;
use App\Services\VisitTrackingService;
use App\Models\VisitSchedule;
use App\Models\ProjectReport;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VisitTrackingExport;

class ProjectController extends AppBaseController
{
    /** @var  ProjectRepository */
    private $projectRepository;
    private $visitTrackingService;

    public function __construct(ProjectRepository $projectRepo, VisitTrackingService $visitTrackingService)
    {
        $this->middleware('can:projects');
        $this->projectRepository = $projectRepo;
        $this->visitTrackingService = $visitTrackingService;
    }

    /**
     * Display a listing of the Project.
     *
     * @param ProjectDataTable $projectDataTable
     * @return Response
     */
    public function index(ProjectDataTable $projectDataTable)
    {
        return $projectDataTable->render('projects.index');
    }

    /**
     * Show the form for creating a new Project.
     *
     * @return Response
     */
    public function create()
    {
        $amcQuotations = \App\Models\Quotation::with('lpoins')
        ->where('category', 'amc')
        ->whereHas('lpoins')          
        ->whereDoesntHave('project')  
        ->get()
        ->mapWithKeys(function ($q) {
            $lpoinRef = $q->lpoins->ref_no;
            $label = $lpoinRef . ' | ' . $q->ref_no . ' | ' . $q->subject;
            return [$q->id => $label];
        });
    
        $normalQuotations = \App\Models\Quotation::with('lpoins')
            ->where(function ($query) {
                $query->where('category', 'normal')
                    ->orWhereNull('category');
            })
            ->whereHas('lpoins')          
            ->whereDoesntHave('project')  
            ->get()
            ->mapWithKeys(function ($q) {
                $lpoinRef = $q->lpoins->ref_no;
                $label = $lpoinRef . ' | ' . $q->ref_no . ' | ' . $q->subject;
                return [$q->id => $label];
            });
        return view('projects.create', compact('amcQuotations', 'normalQuotations'));
    }
    

    /**
     * Store a newly created Project in storage.
     *
     * @param CreateProjectRequest $request
     *
     * @return Response
     */
    public function store(CreateProjectRequest $request)
    {
        $input = $request->all();
        // If a quotation is provided, get the category and number_of_visits
        if (isset($input['quotation_id'])) {
            $quotation = Quotation::find($input['quotation_id']);
            if ($quotation) {
                // Assign quotation's category and number_of_visits to project
                $input['category'] = $quotation->category ?? 'normal';
                $input['visits'] = $quotation->number_of_visits ; 
            }
        }
        if ($input['category'] === 'amc') {
            $visits = $input['visits'] ?? 4; // Default to 4 if not provided
            $startDate = Carbon::parse($input['date']);
            $visitSchedule = [];
            $visitSchedule[] = $startDate->toDateString(); // First visit on the creation date
            if ($visits > 1) {
                if ($visits <= 4) {
                    // Distribute the remaining visits every 3 months
                    for ($i = 1; $i < $visits; $i++) {
                        $visitSchedule[] = $startDate->copy()->addMonths($i * 3)->toDateString();
                    }
                } else {
                    // Distribute visits evenly across 365 days
                    $intervalDays = 365 / $visits;
                    for ($i = 1; $i < $visits; $i++) {
                        $visitSchedule[] = $startDate->copy()->addDays($i * $intervalDays)->toDateString();
                    }
                }
            }
            $input['visit_schedule'] = json_encode($visitSchedule);
        }
        // Create the project
        $project = $this->projectRepository->create($input);
        $currentDate = Carbon::now();

        // If the project category is 'amc', create individual visit schedule records in the visit_schedules table
        if ($input['category'] === 'amc') {
            foreach ($visitSchedule as $date) {
                $visitDate = Carbon::parse($date);
                $status = $visitDate->isPast() ? 'pending' : ($visitDate->isFuture() ? 'upcoming' : 'pending');
                VisitSchedule::create([
                    'project_id' => $project->id,
                    'visit_date' => $date,
                    'status' => $status,
                    'file_uploaded' => false,
                ]);
            }
        }

        Flash::success(__('messages.saved', ['model' => __('models/projects.singular')]));

        return redirect(route('projects.index'));
    }


    /**
     * Display the specified Project.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $project = $this->projectRepository->find($id);
        if (empty($project)) {
            Flash::error(__('models/projects.singular').' '.__('messages.not_found'));
    
            return redirect(route('projects.index'));
        }
    
        // Fetch the visit schedules for this project
        $visitSchedules = VisitSchedule::where('project_id', $id)
        ->with(['projectReport' => function($query) {
            $query->where('status', 'approved');
        }])

                                        ->get();
                                        // dd($visitSchedules);

        // Pass all data to the view
        return view('projects.show', [
            'project' => $project,
            'visitSchedules' => $visitSchedules,
        ]);
    }

    /**
     * Show the form for editing the specified Project.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $project = $this->projectRepository->find($id);

        if (empty($project)) {
            Flash::error(__('messages.not_found', ['model' => __('models/projects.singular')]));

            return redirect(route('projects.index'));
        }
        $lpoins = "";
        if($project->quotation && $project->quotation->lpoins)
            $lpoins = 'Lpoin Ref #:  '.$project->quotation->lpoins->ref_no.' ( Quotation: '.$project->quotation->name.' )';

        return view('projects.edit',[
            'project' => $project,
            'lpoin' => $lpoins,
        ]);
    }

    /**
     * Update the specified Project in storage.
     *
     * @param  int              $id
     * @param UpdateProjectRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateProjectRequest $request)
    {
        $project = $this->projectRepository->find($id);
    
        if (empty($project)) {
            Flash::error(__('messages.not_found', ['model' => __('models/projects.singular')]));
            return redirect(route('projects.index'));
        }
    
        $input = $request->all();

        if ($project->category === 'amc' && $input['category'] === 'normal') {
            $input['visit_schedule'] = null;
        } elseif ($project->category === 'normal' && $input['category'] === 'amc') {
            $visits = $input['visits'] ?? 4;
            $startDate = Carbon::parse($input['date']);
            $visitSchedule = [];
            $visitSchedule[] = $startDate->toDateString();
            if ($visits > 1) {
                $intervalDays = (365 - 1) / ($visits - 1);
                for ($i = 1; $i < $visits; $i++) {
                    $visitSchedule[] = $startDate->copy()->addDays($i * $intervalDays)->toDateString();
                }
            }
    
            $input['visit_schedule'] = json_encode($visitSchedule);
        }
        $project = $this->projectRepository->update($input, $id);
    
        Flash::success(__('messages.updated', ['model' => __('models/projects.singular')]));
    
        return redirect(route('projects.index'));
    }
    

    /**
     * Remove the specified Project from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $project = $this->projectRepository->find($id);

        if (empty($project)) {
            Flash::error(__('messages.not_found', ['model' => __('models/projects.singular')]));

            return redirect(route('projects.index'));
        }

        $status = $this->projectRepository->delete($id);
        if($status)
            Flash::success(__('messages.deleted', ['model' => __('models/projects.singular')]));
        else
            Flash::error(__('messages.permisssion_error'));


        return redirect(route('projects.index'));
    }

    // Get extensions
    public function get_extensions($id)
    {
        $project = $this->projectRepository->find($id);
        if (empty($project)) {
            Flash::error(__('models/projects.singular').' '.__('messages.not_found'));

            return redirect(route('projects.index'));
        }

        // get other quotations
        $quotationItems = \App\Models\Quotation::doesnthave('project')->pluck('name','id')->toArray();

        return view('projects.projects_partials.project_extension')->with([
            'project' => $project,
            'quotationItems' => $quotationItems,
        ]);
    }

    // Store extensions
    public function store_extensions($id, ProjectExtensionRequest $request)
    {
        $project = $this->projectRepository->find($id);

        if (empty($project)) {
            Flash::error(__('models/projects.singular').' '.__('messages.not_found'));

            return redirect(route('projects.index'));
        }
        $project = $this->projectRepository->addExtension($project, $request->except('_token'));

        Flash::success(__('messages.saved', ['model' => __('models/projects.singular')]));

        return redirect()->back();
    }

    // View extension detail
    public function view_extensions($id)
    {
        $extension = \App\Models\ProjectExtension::find($id);
        if (empty($extension)) {
            Flash::error(__('models/extensions.singular').' '.__('messages.not_found'));

            return redirect(route('projects.index'));
        }
        return view('projects.projects_partials.project_extension_view')->with('extension',$extension);
    }

    // Delete extensions
    public function delete_extensions($id, Request $request)
    {
        $project = $this->projectRepository->find($id);

        $this->projectRepository->removeExtension($project, $request->except('_token'));
        Flash::success(__('messages.deleted', ['model' => __('models/projects.singular')]));

        return redirect()->back();
    }

    // Get lpoout
    public function get_lpoout($id)
    {
        $project = $this->projectRepository->find($id);
        if (empty($project)) {
            Flash::error(__('models/projects.singular').' '.__('messages.not_found'));

            return redirect(route('projects.index'));
        }

        return view('projects.projects_partials.project_lpoout')->with('project', $project);
    }

    // Store lpoout
    public function store_lpoout($id, ProjectLpooutRequest $request)
    {
        $project = $this->projectRepository->find($id);

        if (empty($project)) {
            Flash::error(__('models/projects.singular').' '.__('messages.not_found'));

            return redirect(route('projects.index'));
        }
        $project = $this->projectRepository->addLpoout($project, $request->except('_token'));

        Flash::success(__('messages.saved', ['model' => __('models/projects.singular')]));

        return redirect()->back();
    }

    // Delete lpoout
    public function delete_lpoout($id, Request $request)
    {
        $project = $this->projectRepository->find($id);

        $this->projectRepository->removeLpoout($project, $request->except('_token'));
        Flash::success(__('messages.deleted', ['model' => __('models/projects.singular')]));

        return redirect()->back();
    }

    // Store Comment
    public function store_comment(Request $request)
    {
        $input = $request->all();

        $comment = $this->projectRepository->storeComment($input);
        if($comment)
            Flash::success('Comment saved successfully.');
        else
            Flash::error("Unable to add comment");

        return redirect()->back();

    }
    
    public function visitTracking(VisitTrackingDataTable $dataTable)
    {
        return $dataTable->render('projects.visit_tracking');
    }
    
    public function showForm()
    {
        return view('projects.form');
    }


    public function visitTrackingExport(Request $request)
    {
        $filter = $request->get('expiry_filter', '');
        $now = Carbon::now();
        
        // Use same query logic as DataTable
        $query = Project::query()
            ->with(['quotation.company', 'visitSchedules'])
            ->where('category', 'amc')
            ->where(function ($q) {
                $q->whereHas('visitSchedules', function ($subQ) {
                    $subQ->whereIn('status', ['pending', 'upcoming']);
                })
                ->orWhere(function ($innerQ) {
                    $innerQ->whereDoesntHave('visitSchedules', function ($subQ) {
                        $subQ->whereIn('status', ['pending', 'upcoming']);
                    })
                    ->whereDate('date', '>', now()->subYear());
                });
            });

        // Apply same filters as DataTable
        if ($filter === 'this_month') {
            $start = $now->copy()->startOfMonth()->toDateString();
            $end = $now->copy()->endOfMonth()->toDateString();
            $query->whereRaw("DATE_ADD(`date`, INTERVAL 1 YEAR) BETWEEN ? AND ?", [$start, $end]);
        } 
        elseif ($filter === 'next_month') {
            $nextMonth = $now->copy()->addMonth();
            $start = $nextMonth->startOfMonth()->toDateString();
            $end = $nextMonth->endOfMonth()->toDateString();
            $query->whereRaw("DATE_ADD(`date`, INTERVAL 1 YEAR) BETWEEN ? AND ?", [$start, $end]);
        } 
        elseif ($filter === 'visit_this_month') {
            $start = $now->copy()->startOfMonth()->toDateString();
            $end = $now->copy()->endOfMonth()->toDateString();
            $query->whereHas('visitSchedules', function($q) use ($start, $end) {
                $q->where('status', '!=', 'done')
                ->whereBetween('visit_date', [$start, $end]);
            });
        } 
        elseif ($filter === 'visit_next_month') {
            $nextMonth = $now->copy()->addMonth();
            $start = $nextMonth->startOfMonth()->toDateString();
            $end = $nextMonth->endOfMonth()->toDateString();
            $query->whereHas('visitSchedules', function($q) use ($start, $end) {
                $q->where('status', '!=', 'done')
                ->whereBetween('visit_date', [$start, $end]);
            });
        }

        $projects = $query->orderBy('id', 'desc')->get();

        return PDF::loadView('exports.visit_tracking_pdf', compact('projects', 'filter'))
            ->setPaper('a4')
            ->setOrientation('landscape')
            ->setOption('margin-right', 10)
            ->setOption('margin-left', 10)
            ->download('visit_tracking_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

public function visitTrackingExportExcel(Request $request)
{
    $filter = $request->get('expiry_filter', '');
    $now = Carbon::now();
    
    // Use same query logic as visitTrackingExport
    $query = Project::query()
        ->with('quotation', 'visitSchedules')
        ->where('category', 'amc')
        ->where(function ($q) {
            $q->whereHas('visitSchedules', function ($subQ) {
                $subQ->whereIn('status', ['pending', 'upcoming']);
            })
            ->orWhere(function ($innerQ) {
                $innerQ->whereDoesntHave('visitSchedules', function ($subQ) {
                    $subQ->whereIn('status', ['pending', 'upcoming']);
                })
                ->whereDate('date', '>', now()->subYear());
            });
        });

    // Apply same filters as PDF export
    if ($filter === 'this_month') {
        $start = $now->copy()->startOfMonth()->toDateString();
        $end = $now->copy()->endOfMonth()->toDateString();
        $query->whereRaw("DATE_ADD(`date`, INTERVAL 1 YEAR) BETWEEN ?  AND ?", [$start, $end]);
    } 
    elseif ($filter === 'next_month') {
        $nextMonth = $now->copy()->addMonth();
        $start = $nextMonth->startOfMonth()->toDateString();
        $end = $nextMonth->endOfMonth()->toDateString();
        $query->whereRaw("DATE_ADD(`date`, INTERVAL 1 YEAR) BETWEEN ? AND ?", [$start, $end]);
    } 
    elseif ($filter === 'visit_this_month') {
        $start = $now->copy()->startOfMonth()->toDateString();
        $end = $now->copy()->endOfMonth()->toDateString();
        $query->whereHas('visitSchedules', function($q) use ($start, $end) {
            $q->where('status', '!=', 'done')
            ->whereBetween('visit_date', [$start, $end]);
        });
    } 
    elseif ($filter === 'visit_next_month') {
        $nextMonth = $now->copy()->addMonth();
        $start = $nextMonth->startOfMonth()->toDateString();
        $end = $nextMonth->endOfMonth()->toDateString();
        $query->whereHas('visitSchedules', function($q) use ($start, $end) {
            $q->where('status', '!=', 'done')
            ->whereBetween('visit_date', [$start, $end]);
        });
    }

    $projects = $query->orderBy('id', 'desc')->get();

    return Excel::download(
        new VisitTrackingExport($projects, $filter),
        'visit_tracking_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
    );
}


}
