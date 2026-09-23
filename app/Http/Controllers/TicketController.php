<?php

namespace App\Http\Controllers;
use App\DataTables\ViewTicketDataTable;
use App\Models\Ticket;
use App\Models\StafProfile;
use Illuminate\Http\Request;
use App\DataTables\TicketDataTable;

class TicketController extends Controller
{

    public function index(TicketDataTable $dataTable)
    {
        return $dataTable->render('tickets.index');
    }

    public function create()
    {
        $staffProfiles = StafProfile::all(); 
        return view('tickets.create', compact('staffProfiles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'staf_id' => 'required|exists:staf_profile,id',
            'ticket_date' => 'required|date',
            'agent_name' => 'required|string',
            'travel_type' => 'required|string|in:One Way,Two Way',
            'amount' => 'required|numeric',
            'payment_status' => 'required|string',
        ]);

        $vat = $request->amount * 0.05;
        $totalValue = $request->amount + $vat;

        Ticket::create([
            'staf_id' => $request->staf_id,
            'ticket_date' => $request->ticket_date,
            'agent_name' => $request->agent_name,
            'travel_type' => $request->travel_type,
            'travel_date' => $request->travel_date, // Always required
            'return_date' => $request->travel_type == 'Two Way' ? $request->return_date : null, // Required only for 'Two Way'
            'amount' => $request->amount,
            'vat' => $vat,
            'total_value' => $totalValue,
            'payment_status' => $request->payment_status,
        ]);

        return redirect()->route('tickets.index')->with('success', 'Ticket created successfully.');
    }

    public function show($id)
    {
        $ticket = Ticket::findOrFail($id);
        return view('tickets.show', compact('ticket'));
    }

    public function edit($id)
    {
        $ticket = Ticket::findOrFail($id);
    
        $staffProfiles = StafProfile::all();
    
        return view('tickets.edit', compact('ticket', 'staffProfiles'));
    }
    

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'staf_id' => 'required|exists:staf_profile,id',
            'ticket_date' => 'required|date',
            'agent_name' => 'required|string',
            'travel_type' => 'required|in:One Way,Two Way',
            'travel_date' => 'required_if:travel_type,One Way|nullable|date',
            'return_date' => 'required_if:travel_type,Two Way|nullable|date',
            'amount' => 'required|numeric',
            'payment_status' => 'nullable|string',
        ]);
        
        $ticket = Ticket::findOrFail($id);
    
        $ticket->staf_id = $validatedData['staf_id'];
        $ticket->ticket_date = $validatedData['ticket_date'];
        $ticket->agent_name = $validatedData['agent_name'];
        $ticket->travel_type = $validatedData['travel_type'];
        
        if ($validatedData['travel_type'] == 'One Way') {
            $ticket->travel_date = $validatedData['travel_date'];
            $ticket->return_date = null;
        } else {
            $ticket->travel_date = $validatedData['travel_date'];
            $ticket->return_date = $validatedData['return_date'];
        }
    
        $ticket->amount = $validatedData['amount'];
        $ticket->vat = $ticket->amount * 0.05; // VAT is 5% of the amount
        $ticket->total_value = $ticket->amount + $ticket->vat; 
        $ticket->payment_status = $validatedData['payment_status'] ?? $ticket->payment_status;
        $ticket->save();
    
        return redirect()->route('tickets.index')->with('success', 'Ticket updated successfully');
    }

    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();
        return redirect()->route('tickets.index')->with('success', 'Ticket deleted successfully.');
    }

    public function statusIndex(ViewTicketDataTable $dataTable)
    {
        return $dataTable->render('tickets.status');
    }

    /**
     * Update the ticket status to "release".
     *
     * Route: POST tickets/update-status/{id}
     */
    public function updateStatus(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->payment_status = $request->status; // typically "release"
        $ticket->save();

        return redirect()->route('tickets.status')
            ->with('success', 'Ticket status updated successfully.');
    }


}
