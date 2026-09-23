<!-- Filters Card -->
<div class="card card-primary mb-3">
    <div class="card-header" style="background-color: #d81b60;">
        <h3 class="card-title">Filters</h3>
    </div>
    <div class="card-body">
        <form id="filterForm" class="filter-form">
            <div class="row">
                <!-- Date From -->
                <div class="col-md-3">
                    <label>Date From</label>
                    <input type="date" name="date_from" id="dateFrom" class="form-control form-control-sm" 
                           value="{{ request('date_from') }}">
                </div>

                <!-- Date To -->
                <div class="col-md-3">
                    <label>Date To</label>
                    <input type="date" name="date_to" id="dateTo" class="form-control form-control-sm" 
                           value="{{ request('date_to') }}">
                </div>

                <!-- Labor Name -->
                <div class="col-md-3">
                    <label>Labor Name</label>
                    <select name="labor_id" id="laborId" class="form-control form-control-sm">
                        <option value="">-- All Labors --</option>
                        @foreach($laborers as $labor)
                            <option value="{{ $labor->id }}" 
                                @if(request('labor_id') == $labor->id) selected @endif>
                                {{ $labor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Buttons -->
                <div class="col-md-3">
                    <label>&nbsp;</label>
                    <div class="btn-group btn-block" role="group">
                        <button type="button" class="btn btn-primary btn-sm" id="filterBtn">
                            <i class="fas fa-filter"></i> Apply
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" id="resetBtn">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .filter-form {
        padding: 0;
    }

    .btn-group {
        display: flex;
        gap: 5px;
    }

    .btn-sm {
        margin-right: 0;
    }
</style>
