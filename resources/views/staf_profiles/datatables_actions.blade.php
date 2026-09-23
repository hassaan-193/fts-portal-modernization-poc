{!! Form::open(['route' => ['staf.destroy', $id], 'method' => 'delete']) !!}
<div class='btn-group'>
    <a href="{{ route('staf.show', $id) }}" class='btn btn-success'>
       <i class="fa fa-eye"></i>
    </a>
    @if($staf_type == 'Office Staf')
        <a href="{{ route('show_staff_request_form', $id) }}" class='btn btn-secondary'>
            <i class="fa fa-clipboard"></i>
        </a>
    @endif
    <a href="{{ route('staf.edit', $id) }}" class='btn btn-info'>
       <i class="fa fa-edit"></i>
    </a>
    <a href="{{ route('staf_date', $id) }}" class='btn btn-warning'>
        <i class="fa fa-calendar-day"></i>
    </a>
    {!! Form::button('<i class="fa fa-trash"></i>', [
        'type' => 'submit',
        'class' => 'btn btn-danger',
        'onclick' => "return confirm('Are you sure?')"
    ]) !!}
</div>
{!! Form::close() !!}
