<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Task Assigned</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
</head>
<body>
    <div class="row">
        <div class="col-md-12" style="height: 300px;overflow: scroll;">
            <h3><strong>Task Detail</strong></h3>
            <table id="example2" class="bg-white table-bordered table-striped table-sm" role="grid" aria-describedby="example1_info" style="width:100%;">
                <thead>
                    <tr role="row">
                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" >Start Date</th>
                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" >End Date	</th>
                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" >Title</th>
                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" >Description</th>
                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" >Status</th>
                    </tr>
                </thead>
                <tbody>
                        <tr class="odd">
                            <td>{{$task->start_date}}</td>
                            <td>{{$task->end_date}}</td>
                            <td>{{$task->title}}</td>
                            <td>{{$task->description}}</td>
                            <td>@include('components.datatables_status', [
                                'msg' => ($task->status == 2) ? 'complete' : 'open',
                                'type' => ($task->status == 2) ? 'success' : 'danger',
                                ])
                            </td>
                        </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
