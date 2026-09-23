@extends('layouts.master')

@section('content')
    <div class="container">
        <h1>Ticket Details</h1>
        <div class="card">
            <div  class="card-body " >
                <h5 class="card-title">Ticket Information</h5>
                <br>
                <br>

                <p><strong>Staff Name:</strong> {{ $ticket->stafProfile->name }}</p>
                <p><strong>Ticket Date:</strong> {{ $ticket->ticket_date }}</p>
                <p><strong>Agent Name:</strong> {{ $ticket->agent_name }}</p>
                <p><strong>Travel Type:</strong> {{ $ticket->travel_type }}</p>
                <p><strong>Travel Date:</strong> {{ $ticket->travel_date }}</p>

                @if($ticket->travel_type == 'Two Way')
                    <p><strong>Return Date:</strong> {{ $ticket->return_date }}</p>
                @endif

                <p><strong>Amount:</strong> {{ $ticket->amount }}</p>
                <p><strong>VAT (5%):</strong> {{ $ticket->vat }}</p>
                <p><strong>Total Value:</strong> {{ $ticket->total_value }}</p>
                <p><strong>Payment Status:</strong> {{ $ticket->payment_status ?? 'Not set' }}</p>

                <a href="{{ route('tickets.index') }}" class="btn btn-primary">Back to Tickets</a>
            </div>
        </div>
    </div>
@endsection
