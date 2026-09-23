<div class="btn-group" role="group">

    <!-- Contribute button -->
    <a href="{{ route('drawing-receiveds.contribute-form', $id) }}" class="btn btn-sm btn-success" title="Contribute">
        <i class="fa fa-pencil-alt"></i>
    </a>

    <!-- View contributions history button -->
    <a href="{{ route('drawing-receiveds.contributions', $id) }}" class="btn btn-sm btn-info" title="Contributions">
        <i class="fa fa-history"></i>
    </a>

    <!-- Optional: view details if you have a show route -->
    {{-- 
    <a href="{{ route('drawing-receiveds.show', $id) }}" class="btn btn-sm btn-primary" title="View Details">
        <i class="fa fa-eye"></i>
    </a>
    --}}
</div>