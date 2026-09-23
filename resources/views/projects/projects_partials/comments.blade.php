<div class="card direct-chat card-outline card-primary direct-chat-warning">
    <div class="card-header">
    <h3 class="card-title">Comments</h3>
    </div>
    <div class="card-body">
    <div class="direct-chat-messages">
        @foreach($project->comments as $comment)
            <div class="direct-chat-msg">
                <div class="direct-chat-infos clearfix">
                <span class="direct-chat-name float-left">
                    {{$comment->user->name}}
                </span>
                <span class="direct-chat-timestamp float-right">{{$comment->created_at}}</span>
                </div>
                <div class="direct-chat-text m-0" >{!! $comment->comments !!}</div>
            </div>
        @endforeach
    </div>
    </div>
    <!-- /.card-body -->
    <div class="card-footer">
    {!! Form::open(['route' => 'projects.store_comment', "onsubmit" => "if( confirm('Are you sure') ) { submit.disabled = true; return true; } return false;"]) !!}
        <div class="input-group">
        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
        <input type="hidden" name="id" value="{{ $project->id }}">
        <input type="text" name="comments" placeholder="Type Message ..." class="form-control">
        <span class="input-group-append">
            <button type="submit"  name="submit" class="btn btn-primary">Save</button>
        </span>
        </div>
    {!! Form::close() !!}
    </div>
    <!-- /.card-footer-->
</div>
