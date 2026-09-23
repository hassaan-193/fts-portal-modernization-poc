@include('components.datatables_status', [
    'msg' => $model->status ? 'Cleared' : 'Pending Clearance',
    'type' => $model->status ? 'success' : 'warning',
])
