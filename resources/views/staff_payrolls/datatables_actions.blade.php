{!! Form::open(['route' => ['staffPayrolls.destroy', $id], 'method' => 'delete']) !!}
<div class='btn-group'>
    <a href="{{ route('staffPayrolls.exportPdf', $id) }}" class='btn btn-success' title="Export PDF">
        <i class="fa fa-file-pdf-o"></i> Report
    </a>
    {!! Form::button('<i class="fa fa-trash"></i>', [
        'type' => 'submit',
        'class' => 'btn btn-danger',
        'onclick' => "return confirm('Are you sure?')"
    ]) !!}
</div>
{!! Form::close() !!}
