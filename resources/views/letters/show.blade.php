@extends('layouts.master')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item active">Letter</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    @include('flash::message')
    <div class="letter-container">
        <style>




            .letter-container {
                width: 900px;
                margin: 0 auto;
                border: 2px solid #efefef;
                padding: 46px;
                /* background-image: url('/dist/img/j.jpg'); */
                /* background-size: cover; */
                /* background-repeat: no-repeat; */
                /* background-position: center; */
                background-color:white;
            }
            header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                height: 160px;
            }
            h1 { text-align: center; color: #000; margin-top: 0px; margin-bottom: 0; }
            h1.heading-e { font-size: 16px; }
            h1.heading-a { font-size: 24px; }
            .info-section, .employee-section { margin: 20px 0; }
            .employee-section p { display: flex; align-items: center; white-space: pre; }
            input { border: none; background: transparent; }
            .content { margin-top: 20px; text-align: justify; }
            .arabic { direction: rtl; text-align: right; font-family: "Arial", sans-serif; font-size: 15px; }
            .signature { margin-top: 40px; border-top: 2px solid #005ea2; padding-top: 20px; }
            .signature p { margin: 0px 0 0; }
            .attachments { margin-top: 40px; }
            .attachments h5 { font-size: 18px; margin-bottom: 10px; color: #333; }
            .attachments ul { list-style: none; padding: 0; }
            .attachments li { margin-bottom: 8px; }
            .attachments a { text-decoration: none; color: #007bff; }
            .attachments a:hover { text-decoration: underline; }
        </style>
        <header>
            <img class="fts_img images" src="{{asset('dist/img/fts_latter_head.jpeg')}}" style="width:100%;margin:auto;" />
            <img class="expert_img images" src="{{asset('dist/img/experts_letter_head.jpeg')}}" style="width:100%; margin:auto; display:none;" />
            <img class="ftsits_img images" src="{{asset('dist/img/ftsits_letter_head.jpeg')}}" style="width:100%;margin:auto; display:none;" />
        </header>

        <div class="info-section">
            <br>
            <br>
            <p><strong>Date:</strong>
                {!! Form::text('issued_at', \Carbon\Carbon::parse($letter->issued_at)->format('Y-m-d'), ['readonly']) !!}
            </p>
            <p><strong>Ref No:</strong>
                {!! Form::text('title', $letter->ref_no, ['readonly', 'style' => 'background-color:transparent;']) !!}
            </p>
            @if($letter->type === 'warning' && $letter->days_deduct)
                <p><strong>Days to Deduct:</strong>
                    {!! Form::text('days_deduct', $letter->days_deduct, ['readonly', 'style' => 'background-color:transparent;']) !!}
                </p>
            @endif
        </div>

        @if(strtolower($letter->type) === 'appreciation')
            <h1 class="heading-e">SUBJECT: OFFICIAL APPRECIATION LETTER</h1>
            <br>
            <h1 class="heading-a">الموضوع: خطاب تقدير رسمي</h1>
        @elseif(strtolower($letter->type) === 'warning')
            <h1 class="heading-e">SUBJECT: OFFICIAL WARNING LETTER</h1>
            <br>
            <h1 class="heading-a">الموضوع: خطاب تحذير رسمي</h1>
        @elseif(strtolower($letter->type) === 'general_notice')
            <h1 class="heading-e">SUBJECT: GENERAL NOTICE</h1>
            <br>
            <h1 class="heading-a">الموضوع: إشعار عام</h1>
        @elseif(strtolower($letter->type) === 'poor_performance_notice')
            <h1 class="heading-e">SUBJECT: POOR PERFORMANCE NOTICE</h1>
            <br>
            <h1 class="heading-a">الموضوع: إشعار الأداء الضعيف</h1>
        @elseif(strtolower($letter->type) === 'accommodation_notice')
            <h1 class="heading-e">SUBJECT: ACCOMMODATION NOTICE</h1>
            <br>
            <h1 class="heading-a">الموضوع: إشعار السكن</h1>
        @elseif(strtolower($letter->type) === 'vehicle_notice')
            <h1 class="heading-e">SUBJECT: VEHICLE NOTICE</h1>
            <br>
            <h1 class="heading-a">الموضوع: إشعار المركبة</h1>
        @elseif(strtolower($letter->type) === 'attendance_notice')
            <h1 class="heading-e">SUBJECT: ATTENDANCE NOTICE</h1>
            <br>
            <h1 class="heading-a">الموضوع: إشعار الحضور</h1>
        @elseif(strtolower($letter->type) === 'weather_notice')
            <h1 class="heading-e">SUBJECT: WEATHER NOTICE</h1>
            <br>
            <h1 class="heading-a">الموضوع: إشعار الطقس</h1>
        @elseif(strtolower($letter->type) === 'eid_holidays_notice')
            <h1 class="heading-e">SUBJECT: EID HOLIDAYS NOTICE</h1>
            <br>
            <h1 class="heading-a">الموضوع: إشعار عطل العيد</h1>
        @endif

        <div class="employee-section">
            <p><strong>Name:</strong>
                {!! Form::text('staff_profile_id', $letter->stafProfile->name ?? 'N/A', ['readonly']) !!}
                <strong>السيد</strong>
            </p>
            <p><strong>Designation:</strong>
                {!! Form::text('staff_profile_id', $letter->stafProfile->staf_type ?? 'N/A', ['readonly']) !!}
                <strong>المسمى الوظيفي</strong>
            </p>
        </div>

        <div class="content">
            <p>This letter is an Official Warning from the management for unsatisfactory job performance, due to the below reason:</p>
            <p class="arabic">هذه الرسالة هي تحذير رسمي من الإدارة، بشأن الأداء الوظيفي غير المرضي، وذلك للسبب التالي:</p>
            <p><strong>Reason:</strong>
                {!! Form::text('title', $letter->title, ['readonly', 'style' => 'width:100%; background-color:transparent;']) !!}
            </p>
            <br>
            {!! Form::textarea('content', $letter->content, [
                'class' => 'form-control auto-resize',
                'readonly',
                'style' => 'overflow:hidden; resize:none; width:100%; min-height:100px; border:none; background-color:transparent;'
            ]) !!}
        </div>

        {{--  Attachments Section --}}
        <div class="attachments">
            <h5><i class="fa fa-paperclip"></i> Attached Files</h5>
            @php $files = $letter->getMedia(); @endphp
            @if($files->count() > 0)
                <ul>
                    @foreach($files as $file)
                        <li>
                            <a href="{{ $file->getUrl() }}" target="_blank">
                                {{ $file->file_name }}
                            </a>
                            <small class="text-muted">({{ strtoupper($file->mime_type) }})</small>
                            <a href="{{ $file->getUrl() }}" download class="btn btn-sm btn-outline-primary ml-2">
                                <i class="fa fa-download"></i> Download
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted">No files attached.</p>
            @endif
        </div>

        <div class="signature">
            <p><strong>From: {!! Form::text('issued_by', $letter->issued_by ?? 'N/A', ['readonly']) !!}</strong></p>
            <p><strong>TO: {!! Form::text('staff_profile_id', $letter->stafProfile->name ?? 'N/A', ['readonly']) !!}</strong></p>
            <br>
            <p><strong>Acknowledgement & Acceptance</strong></p>
            <p>I have read, understood and accepted the above terms & conditions.</p>
            <p>Accepted & Noted <strong style="float: right;">Finger Print</strong></p>
            <p>Signature: ________________________________</p>
            <p>Date: ____________________________________</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.auto-resize').forEach(function(textarea) {
        textarea.style.height = textarea.scrollHeight + 'px';
    });
});
</script>
@endsection
