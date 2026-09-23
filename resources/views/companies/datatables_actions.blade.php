{!! Form::open(['route' => ['companies.destroy', $id], 'method' => 'delete']) !!}
<div class='btn-group'>
    <a href="{{ route('companies.show', $id) }}" class='btn btn-success'>
       <i class="fa fa-eye"></i>
    </a>
    <a href="{{ route('companies.edit', $id) }}" class='btn btn-info'>
       <i class="fa fa-edit"></i>
    </a>
    <a href="{{ route('companies.get_company_report', $id) }}" class='btn btn-warning'>
        <i class="fa fa-file"></i>
     </a>
    {!! Form::button('<i class="fa fa-trash"></i>', [
        'type' => 'submit',
        'class' => 'btn btn-danger',
        'onclick' => "return confirm('Are you sure?')"
    ]) !!}
</div>
{!! Form::close() !!}
