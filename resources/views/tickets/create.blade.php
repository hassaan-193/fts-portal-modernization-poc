@extends('layouts.master')

@section('content')
    <div class="container">
        <h1>Create Ticket</h1>
        <form action="{{ route('tickets.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="staf_id">Staff</label>
                <select name="staf_id" class="form-control" required>
                    @foreach($staffProfiles as $staff)
                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="ticket_date">Ticket Date</label>
                <input type="date" name="ticket_date" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="agent_name">Agent Name</label>
                <input type="text" name="agent_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="travel_type">Travel Type</label>
                <select name="travel_type" class="form-control" required id="travel_type">
                    <option value="One Way">One Way</option>
                    <option value="Two Way">Two Way</option>
                </select>
            </div>
            <div class="form-group" id="travel_date_group">
                <label for="travel_date">Travel Date</label>
                <input type="date" name="travel_date" class="form-control" required>
            </div>
            <div class="form-group" id="return_date_group">
                <label for="return_date">Return Date</label>
                <input type="date" name="return_date" class="form-control">
            </div>
            <div class="form-group">
                <label for="amount">Amount</label>
                <input type="number" name="amount" class="form-control" id="amount" required>
            </div>
            <div class="form-group">
                <label for="vat">VAT (5%)</label>
                <input type="number" name="vat" class="form-control" id="vat" readonly>
            </div>
            <div class="form-group">
                <label for="total_value">Total Value (Amount + VAT)</label>
                <input type="number" name="total_value" class="form-control" id="total_value" readonly>
            </div>
            <div class="form-group">
                <input type="text" value="pending" hidden name="payment_status" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Create Ticket</button>
        </form>
    </div>

<script>
    const travelTypeSelect = document.getElementById('travel_type');
    const returnDateGroup = document.getElementById('return_date_group');
    const amountInput = document.getElementById('amount');
    const vatInput = document.getElementById('vat');
    const totalValueInput = document.getElementById('total_value');

    function toggleReturnDate() {
        if (travelTypeSelect.value === 'One Way') {
            returnDateGroup.style.display = 'none';  // Hide return date
        } else {
            returnDateGroup.style.display = 'block';  // Show return date
        }
    }

    function updateAmountFields() {
        const amount = parseFloat(amountInput.value) || 0;
        const vat = amount * 0.05;  // 5% VAT
        const totalValue = amount + vat;  // Amount + VAT

        vatInput.value = vat.toFixed(2);  // Set VAT value
        totalValueInput.value = totalValue.toFixed(2);  // Set Total Value
    }

    // Initial call to set the correct state based on the selected travel type
    toggleReturnDate();

    // Event listener for travel type change
    travelTypeSelect.addEventListener('change', toggleReturnDate);

    // Event listener for amount input change to calculate VAT and Total Value
    amountInput.addEventListener('input', updateAmountFields);
</script>
@endsection
