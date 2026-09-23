<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tasks;


use Flash;
use Response;
use App\Http\Requests;
use Illuminate\Validation\Validator;
use App\DataTables\TaskDataTable;
use App\Http\Controllers\AppBaseController;
use App\Repositories\TasksRepository;
use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\UpdateStafProfileRequest;
use App\User;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /** @var  TasksRepository */
    private $taskRepo;

    public function __construct(TasksRepository $taskRepo)
    {
        $this->middleware('can:tasks');
        $this->taskRepo = $taskRepo;
    }

    public function index(TaskDataTable $taskDataTable)
    {
        return $taskDataTable->render('tasks.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // if (!in_array( auth()->user()->email, Tasks::$allowedEmails)) {
        //     Flash::error(__('No Permisssion'));
        //     return redirect(route('task.index'));
        // }
        $users= User::pluck('name','id')->toArray();
        return view('tasks.create')->with(compact('users'));
    }

   /**
     * Store a newly created Quotation in storage.
     *
     * @param CreateTaskRequest $request
     *
     * @return Response
     */
    public function store(CreateTaskRequest $request)
    {
        // if (!in_array( auth()->user()->email, Tasks::$allowedEmails)) {
        //     Flash::error(__('No Permisssion'));
        //     return redirect(route('task.index'));
        // }
        $input = $request->all();
        $task = $this->taskRepo->create($input);
        Flash::success(__('messages.saved', ['model' => __('models/taskprofile.singular')]));
        return redirect(route('task.index'));


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // if (!in_array( auth()->user()->email, Tasks::$allowedEmails)) {
        //     Flash::error(__('No Permisssion'));
        //     return redirect(route('task.index'));
        // }
        $task = $this->taskRepo->find($id);
        $users= User::pluck('name','id')->toArray();

        if (empty($task)) {
            Flash::error(__('messages.not_found', ['model' => __('models/staftask.singular')]));

            return redirect(route('staf.index'));
        }

        return view('tasks.edit')->with(compact('task','users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // if (!in_array( auth()->user()->email, Tasks::$allowedEmails)) {
        //     Flash::error(__('No Permisssion'));
        //     return redirect(route('task.index'));
        // }
        $task = $this->taskRepo->find($id);

        if (empty($task)) {
            Flash::error(__('messages.not_found', ['model' => __('models/task.singular')]));

            return redirect(route('task.index'));
        }

        $task = $this->taskRepo->update($request->all(), $id);

        Flash::success(__('messages.updated', ['model' => __('models/task.singular')]));

        return redirect(route('task.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $task = $this->taskRepo->find($id);

        if (empty($task)) {
            Flash::error(__('messages.not_found', ['model' => __('models/task.singular')]));

            return redirect(route('task.index'));
        }

        $status = $this->taskRepo->delete($id);
        if($status)
            Flash::success(__('messages.deleted', ['model' => __('models/task.singular')]));
        else
            Flash::error(__('messages.permisssion_error'));


        return redirect(route('task.index'));
    }
}
