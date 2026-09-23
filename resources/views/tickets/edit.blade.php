@extends('layouts.master')

@section('content')
    <div class="container">
        <h1>Edit Ticket</h1>
        <form action="{{ route('tickets.update', $ticket->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="staf_id">Staff</label>
                <select name="staf_id" class="form-control" required>
                    @foreach($staffProfiles as $staff)
                        <option value="{{ $staff->id }}" 
                            @if($ticket->staf_id == $staff->id) selected @endif>
                            {{ $staff->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="ticket_date">Ticket Date</label>
                <input type="date" name="ticket_date" class="form-control" value="{{ $ticket->ticket_date }}" required>
            </div>
            <div class="form-group">
                <label for="agent_name">Agent Name</label>
                <input type="text" name="agent_name" class="form-control" value="{{ $ticket->agent_name }}" required>
            </div>
            <div class="form-group">
                <label for="travel_type">Travel Type</label>
                <select name="travel_type" class="form-control" required>
                    <option value="One Way" @if($ticket->travel_type == 'One Way') selected @endif>One Way</option>
                    <option value="Two Way" @if($ticket->travel_type == 'Two Way') selected @endif>Two Way</option>
                </select>
            </div>
            <div class="form-group" id="travel_date_div">
                <label for="travel_date">Travel Date</label>
                <input type="date" name="travel_date" class="form-control" value="{{ $ticket->travel_date }}">
            </div>
            <div class="form-group" id="return_date_div" @if($ticket->travel_type == 'One Way') style="display:none;" @endif>
                <label for="return_date">Return Date</label>
                <input type="date" name="return_date" class="form-control" value="{{ $ticket->return_date }}">
            </div>
            <div class="form-group">
                <label for="amount">Amount</label>
                <input type="number" name="amount" class="form-control" value="{{ $ticket->amount }}" id="amount" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="vat">VAT (5%)</label>
                <input type="number" name="vat" class="form-control" value="{{ $ticket->vat }}" id="vat" readonly>
            </div>
            <div class="form-group">
                <label for="total_value">Total Value</label>
                <input type="number" name="total_value" class="form-control" value="{{ $ticket->total_value }}" id="total_value" readonly>
            </div>
            <div class="form-group">
                <label for="payment_status">Payment Status</label>
                <input type="text" name="payment_status" class="form-control" value="{{ $ticket->payment_status }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Ticket</button>
        </form>
    </div>
    <script>
        function calculateVATAndTotal() {
            const amountInput = document.getElementById('amount');
            const vatInput = document.getElementById('vat');
            const totalValueInput = document.getElementById('total_value');
            
            const amount = parseFloat(amountInput.value) || 0;
            const vat = amount * 0.05; // 5% VAT
            const totalValue = amount + vat;

            vatInput.value = vat.toFixed(2);
            totalValueInput.value = totalValue.toFixed(2);
        }

        // Trigger calculations on amount input change
        document.getElementById('amount').addEventListener('input', calculateVATAndTotal);

        // Handle travel type change
        document.querySelector('select[name="travel_type"]').addEventListener('change', function() {
            if (this.value === 'One Way') {
                document.getElementById('return_date_div').style.display = 'none';
            } else {
                document.getElementById('return_date_div').style.display = 'block';
            }
        });

        // Initialize calculations
        document.querySelector('select[name="travel_type"]').dispatchEvent(new Event('change'));
        calculateVATAndTotal();
    </script>
@endsection
