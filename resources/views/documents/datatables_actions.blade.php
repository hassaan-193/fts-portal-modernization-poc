@php
    use App\Models\Document;
    $file=Document::find($id);
    if (isset($file->getMedia()[0])) {
        $url=$file->getMedia()[0]->getUrl();
    }
    else{
        $url="";
    }
@endphp
{!! Form::open(['route' => ['document.destroy', $id], 'method' => 'delete']) !!}
<div class='btn-group'>
    <a href="{{$url}}" download class="btn btn-success">
        <i class="fa fa-download"></i> 
    </a>
    <a href="{{route('document.edit',$id)}}" class="btn btn-info">
        <i class="fa fa-edit"></i> 
    </a>
    {!! Form::button('<i class="fa fa-trash"></i>', [
        'type' => 'submit',
        'class' => 'btn btn-danger',
        'onclick' => "return confirm('Are you sure?')"
    ]) !!}
</div>
{!! Form::close() !!}
