{!! Form::open(['route' => ['cheques.destroy', $id], 'method' => 'delete']) !!}
<div class='btn-group'>
    <a href="{{ route('cheques.edit', $id) }}" class='btn btn-info'>
       <i class="fa fa-edit"></i>
    </a>
</div>
{!! Form::close() !!}
