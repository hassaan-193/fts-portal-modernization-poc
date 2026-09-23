<?php
namespace App\Http\Controllers;

use App\Models\VisitSchedule;
use Illuminate\Http\Request;
use App\Models\Project;
use Laracasts\Flash\Flash;
use App\Models\VisitScheduleComment;
use App\Models\VisitScheduleHistory;
use App\DataTables\VisitScheduleHistoryDataTable;
use Yajra\DataTables\Facades\DataTables;

class VisitScheduleController extends Controller
{
    public function uploadFile(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);
        $visitSchedule = VisitSchedule::findOrFail($id);
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('visit_schedules', 'public');
            $visitSchedule->file_uploaded = true;
            $visitSchedule->file_path = $filePath;
            if ($visitSchedule->status != 'done') {
                $visitSchedule->status = 'done';
            }
            $visitSchedule->save();
            // $this->storeVisitHistory($visitSchedule);
            Flash::success('File uploaded successfully and status marked as done!');
        } else {
            Flash::error('No file uploaded.');
        }
        if ($visitSchedule->status !== 'done') {
            if ($visitSchedule->visit_date < now()) {
                $visitSchedule->status = 'pending';
            }
            else {
                $visitSchedule->status = 'upcoming';
            }
            $visitSchedule->save(); 
        }
        return redirect()->back()->with('success', 'File uploaded successfully and status updated!');
    }

    public function updateStatus(Request $request, $id)
    {
        $visitSchedule = VisitSchedule::findOrFail($id);

        if ($visitSchedule->visit_date < now() && $visitSchedule->status != 'done') {
            $visitSchedule->status = 'pending';
        }

        if ($visitSchedule->visit_date > now() && $visitSchedule->status != 'done') {
            $visitSchedule->status = 'upcoming';
        }
        if ($visitSchedule->status === 'done') {
            return redirect()->back()->withErrors('This schedule is already marked as done.');
        }
        $visitSchedule->update(['status' => 'done']);
        // $this->storeVisitHistory($visitSchedule);

        return redirect()->back()->with('success', 'Status updated successfully to done!');
    }

    private function storeVisitHistory($visitSchedule)
    {
        $visitSchedule->load(['project.quotation.company']); 
    
        $project = $visitSchedule->project;
    
        if (!$project) {
            \Log::error("No project found for VisitSchedule ID {$visitSchedule->id}");
            return;
        }
    
        $allDone = $project->visitSchedules()->where('status', '!=', 'done')->count() === 0;
        $alreadyStored = VisitScheduleHistory::where('project_id', $project->id)->exists();
    
        if ($allDone && !$alreadyStored) {
            $quotation = $project->quotation;
            $companyName = $quotation->company->name ?? 'Unknown Company';
    
            VisitScheduleHistory::create([
                'project_id' => $project->id,
                'visit_schedule_id' => $visitSchedule->id,
                'visit_date' => $visitSchedule->visit_date,
                'status' => $visitSchedule->status,
                'company_name' => $companyName,
                'project_name' => $project->subject ?? 'Unnamed Project',
            ]);
        }
    }
    
    


    // public function showHistory()
    // {
    //     $history = VisitScheduleHistory::where('status', 'done')
    //                 ->orderBy('created_at', 'desc')
    //                 ->get();

    //     return view('projects.history', compact('history')); 
    // }

public function showHistory(VisitScheduleHistoryDataTable $dataTable)
{
    return $dataTable->render('projects.history'); // Render the DataTable in the `history.blade.php` view
}




    public function showComments($id)
    {
        $visitSchedule = VisitSchedule::findOrFail($id);
        $comments = $visitSchedule->comments;
        return view('projects.visit-schedules.comments', compact('visitSchedule', 'comments'));
    }

    public function storeComment(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);
        VisitScheduleComment::create([
            'visit_schedule_id' => $id,
            'comment' => $request->comment,
        ]);
        return redirect()->route('visit-schedules.comments', $id)->with('success', 'Comment added successfully!');
    }

    public function deleteComment($visitScheduleId, $commentId)
    {
        $visitSchedule = VisitSchedule::findOrFail($visitScheduleId);
        $comment = $visitSchedule->comments()->findOrFail($commentId);
        $comment->delete();
        return redirect()->route('visit-schedules.comments', $visitScheduleId)
                ->with('success', 'Comment deleted successfully!');
    }



    public function editComment($visitScheduleId, $commentId)
    {
        $visitSchedule = VisitSchedule::findOrFail($visitScheduleId);
        $comment = $visitSchedule->comments()->findOrFail($commentId);
        return view('projects.visit-schedules.edit-comment', compact('visitSchedule', 'comment'));
    }

    public function updateComment(Request $request, $visitScheduleId, $commentId)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);
        $visitSchedule = VisitSchedule::findOrFail($visitScheduleId);
        $comment = $visitSchedule->comments()->findOrFail($commentId);
        $comment->update([
            'comment' => $request->comment,
        ]);
        return redirect()->route('visit-schedules.comments', $visitScheduleId)
                ->with('success', 'Comment updated successfully!');
    }



    
}
