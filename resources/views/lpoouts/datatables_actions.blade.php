@if(auth()->user()->hasRole('Lpoouts-QR-Viewer'))
    <div class='btn-group'>
        <a href="{{ route('lpoouts.show', $id) }}" class='btn btn-success'>
           <i class="fa fa-eye"></i>
        </a>
    </div>
@else
    {!! Form::open(['route' => ['lpoouts.destroy', $id], 'method' => 'delete']) !!}
    <div class='btn-group'>
        <a href="{{ route('lpoouts.show', $id) }}" class='btn btn-success'>
           <i class="fa fa-eye"></i>
        </a>
        <a href="{{ route('invoiceRequests.create_with_lpoout', $id) }}" class='btn btn-warning' title="Request Payment">
           <i class="fa fa-file-invoice"></i>
        </a>
        <a href="{{ route('lpoouts.edit', $id) }}" class='btn btn-info'>
           <i class="fa fa-edit"></i>
        </a>
        {!! Form::button('<i class="fa fa-trash"></i>', [
            'type' => 'submit',
            'class' => 'btn btn-danger',
            'onclick' => "return confirm('Are you sure?')"
        ]) !!}
        <a href="{{ route('lpoouts.print', $id) }}" target="_blank" class='btn btn-secondary' title="Print">
            <i class="fa fa-print"></i>
        </a>
        <a href="{{ route('lpoouts.downloadPdf', $id) }}" class='btn btn-primary' title="Download Merged PDF">
            <i class="fa fa-file"></i>
        </a>
    </div>
    {!! Form::close() !!}
@endif
