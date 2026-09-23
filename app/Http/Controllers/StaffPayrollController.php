<?php

namespace App\Http\Controllers;

use App\DataTables\StaffPayrollDataTable;
use App\Http\Requests;
use App\Http\Requests\CreateStaffPayrollRequest;
use App\Http\Requests\UpdateStaffPayrollRequest;
use App\Repositories\StaffPayrollRepository;
use App\Services\StaffPayrollPdfService;
use App\Models\StaffPayroll;
use Flash;
use App\Http\Controllers\AppBaseController;
use Response;

class StaffPayrollController extends AppBaseController
{
    /** @var  StaffPayrollRepository */
    private $staffPayrollRepository;

    public function __construct(StaffPayrollRepository $staffPayrollRepo)
    {
        $this->staffPayrollRepository = $staffPayrollRepo;
    }

    /**
     * Display a listing of the StaffPayroll.
     *
     * @param StaffPayrollDataTable $staffPayrollDataTable
     * @return Response
     */
    public function index(StaffPayrollDataTable $staffPayrollDataTable)
    {
        return $staffPayrollDataTable->render('staff_payrolls.index');
    }

    /**
     * Show the form for creating a new StaffPayroll.
     *
     * @return Response
     */
    public function create()
    {
        return view('staff_payrolls.create');
    }

    /**
     * Store a newly created StaffPayroll in storage.
     *
     * @param CreateStaffPayrollRequest $request
     *
     * @return Response
     */
    public function store(CreateStaffPayrollRequest $request)
    {
        $input = $request->all();

        $staffPayroll = $this->staffPayrollRepository->create($input);

        if($staffPayroll)
            Flash::success(__('messages.saved', ['model' => __('models/staff_payrolls.title')]));
        else
            Flash::error(__('messages.db_error'));

        return redirect(route('staffPayrolls.index'));
    }

    /**
     * Display the specified StaffPayroll.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $staffPayroll = $this->staffPayrollRepository->find($id);

        if (empty($staffPayroll)) {
            Flash::error(__('models/staff_payrolls.title').' '.__('messages.not_found'));

            return redirect(route('staffPayrolls.index'));
        }

        return view('staff_payrolls.show')->with('staffPayroll', $staffPayroll);
    }

    /**
     * Show the form for editing the specified StaffPayroll.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $staffPayroll = $this->staffPayrollRepository->find($id);

        if (empty($staffPayroll)) {
            Flash::error(__('messages.not_found', ['model' => __('models/staff_payrolls.title')]));

            return redirect(route('staffPayrolls.index'));
        }

        return view('staff_payrolls.edit')->with('staffPayroll', $staffPayroll);
    }

    /**
     * Update the specified StaffPayroll in storage.
     *
     * @param  int              $id
     * @param UpdateStaffPayrollRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateStaffPayrollRequest $request)
    {
        $staffPayroll = $this->staffPayrollRepository->find($id);

        if (empty($staffPayroll)) {
            Flash::error(__('messages.not_found', ['model' => __('models/staff_payrolls.title')]));

            return redirect(route('staffPayrolls.index'));
        }

        $staffPayroll = $this->staffPayrollRepository->update($request->all(), $id);

        Flash::success(__('messages.updated', ['model' => __('models/staff_payrolls.title')]));

        return redirect(route('staffPayrolls.index'));
    }

    /**
     * Remove the specified StaffPayroll from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $staffPayroll = $this->staffPayrollRepository->find($id);

        if (empty($staffPayroll)) {
            Flash::error(__('messages.not_found', ['model' => __('models/staff_payrolls.title')]));

            return redirect(route('staffPayrolls.index'));
        }

        $this->staffPayrollRepository->delete($id);

        Flash::success(__('messages.deleted', ['model' => __('models/staff_payrolls.title')]));

        return redirect(route('staffPayrolls.index'));
    }
    // get members list
    public function get_members_list($type, $date)
    {
        $members = $this->staffPayrollRepository->getMembersList($type, $date);

        return response()->json($members);
    }

    /**
     * Export staff payroll as PDF payslip.
     *
     * @param int $id
     * @param StaffPayrollPdfService $pdfService
     * @return Response
     */
    public function exportPdf($id, StaffPayrollPdfService $pdfService)
    {
        try {
            $staffPayroll = $this->staffPayrollRepository->find($id);

            if (empty($staffPayroll)) {
                Flash::error(__('messages.not_found', ['model' => __('models/staff_payrolls.title')]));
                return redirect(route('staffPayrolls.index'));
            }

            $pdfPath = $pdfService->generatePayslipPdf($staffPayroll);
            $filename = 'payslip_' . $staffPayroll->profile->name . '_' . $staffPayroll->date->format('Y-m') . '.pdf';

            return response()->download($pdfPath, $filename, [
                'Content-Type' => 'application/pdf',
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            \Log::error('Staff Payroll PDF export failed: ' . $e->getMessage());
            Flash::error('Failed to generate PDF: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
