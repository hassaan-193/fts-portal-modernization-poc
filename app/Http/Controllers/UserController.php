<?php

namespace App\Http\Controllers;

use Flash;
use Response;
use App\Http\Requests;
use App\DataTables\UserDataTable;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\ProfileUserRequest;
use App\Http\Controllers\AppBaseController;

class UserController extends AppBaseController
{
    /** @var  UserRepository */
    private $UserRepository;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the SystemUser.
     *
     * @param UserDataTable $userDataTable
     * @return Response
     */
    public function index(UserDataTable $userDataTable)
    {
        return $userDataTable->render('users.index');
    }

    /**
     * Show the form for creating a new SystemUser.
     *
     * @return Response
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created SystemUser in storage.
     *
     * @param CreateSystemUserRequest $request
     *
     * @return Response
     */
    public function store(CreateUserRequest $request)
    {
        $input = $request->all();

        $user = $this->userRepository->createUser($input);

        Flash::success('User saved successfully.');

        return redirect(route('users.index'));
    }

    /**
     * Display the specified SystemUser.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $user = $this->userRepository->find($id);

        if (empty($user)) {
            Flash::error('User not found');

            return redirect(route('users.index'));
        }

        return view('users.show')->with('user', $user);
    }

    /**
     * Show the form for editing the specified SystemUser.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $user = $this->userRepository->find($id);
        if (empty($user)) {
            Flash::error('User not found');

            return redirect(route('users.index'));
        }

        return view('users.edit')->with('user', $user);
    }

    /**
     * Update the specified SystemUser in storage.
     *
     * @param  int              $id
     * @param UpdateSystemUserRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUserRequest $request)
    {
        $user = $this->userRepository->find($id);

        if (empty($user)) {
            Flash::error('User not found');

            return redirect(route('users.index'));
        }

        $user = $this->userRepository->updateUser($request, $user);

        Flash::success('User updated successfully.');

        return redirect(route('users.index'));
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $user = $this->userRepository->find($id);

        if (empty($user)) {
            Flash::error('User not found');

            return redirect(route('users.index'));
        }

        $status = $this->userRepository->delete($id);
        if($status)
            Flash::success('User deleted successfully.');
        else
            Flash::error(__('messages.permisssion_error'));


        return redirect(route('users.index'));
    }

    // Get Collectors Details
    public function get_collectors()
    {
        $users = $this->userRepository->getCollectors();

        return response()->json($users);
    }
    // Get User Profile
    public function user_profile($id)
    {
        if(Auth::user()->id != $id)
            return abort(404);

        $user = $this->userRepository->find($id);
        return view('users.profile')->with('user', $user);
    }
    // Update User Profile
    public function update_user_profile($id, ProfileUserRequest $request)
    {
        if(Auth::user()->id != $id)
            return abort(404);

        $user = $this->userRepository->find($id);
        if (empty($user)) {
            Flash::error('User not found');
            return redirect()->back();
        }

        $user = $this->userRepository->updateUserProfile($request, $user);

        Flash::success('Profile updated successfully.');
        return redirect()->back();
    }
}
