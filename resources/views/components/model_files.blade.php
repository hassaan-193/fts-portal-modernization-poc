<div class="col-md-3 ">
    <div class="card">
        <div class="card-body">
            <p class="text-muted text-center"> <b>Files</b></p>
            <ul class="list-group list-group-unbordered mb-3">
                @foreach ($model->getMedia() as $item)
                    <li class="list-group-item">
                        <a href="{{ $item->getFullUrl() }}" download>{{ $item->name }}</a>
                        @can(['media', 'deletes'])
                            <a href="{{ route('media.delete_file', $item->id) }}" class="float-right"
                                onclick="return confirm('Are you sure?')">
                                <i class="fa fa-trash"></i>
                            </a>
                        @endcan
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
