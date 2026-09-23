@foreach (session('flash_notification', collect())->toArray() as $message)
    @if (!$message['overlay'])
        @section('scripts')
        @parent
            <script>
                var level = {!! json_encode($message['level']) !!};
                var message = {!! json_encode($message['message']) !!};

                $(document).ready(function() {
                    toast.fire({
                        type: level,
                        title: message
                    });
                });
        </script>
        @endsection
    @endif
@endforeach
{{ session()->forget('flash_notification') }}
