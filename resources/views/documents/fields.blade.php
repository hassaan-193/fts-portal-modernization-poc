<h3>Expiry Documents Upload :</h3>
<br>
@if (str_contains(url()->current(), '/create'))
<a class="btn btn-success mb-2" id="add_new_exp_field"><i class="fa fa-plus"></i> Add Document</a>
<input type="hidden" name="total_row" value=1 id="total_row">
@endif
<div class="row" id='exp_file_row'>
    @include(strtolower(__('models/document.plural')) . '.expiry_docs')
</div>

<div class="row">
    <div class="col-sm-12">
        <!-- Submit Field -->
        <div class="form-group">
            {!! Form::submit('Save', ['class' => 'btn btn-danger btn-flat btn-lg']) !!}
            <a href="{{ route('document.index') }}" class="btn btn-outline-danger btn-flat btn-lg text-maroon">Cancel</a>
        </div>
    </div>
</div>
@section('scripts')
    @parent
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
    {{-- -- exp_doc uplode field name change script-- --}}
       <script type="text/javascript">
        $(document).ready(function() {
            var totalRow=$('#total_row').val();
            var row=$('#exp_file_row').clone(false);
            $('#add_new_exp_field').click(function() {
                totalRow++;
                $('#total_row').val(totalRow);
                var copy=row.clone(false);
                var fileLabel = copy.find('#exp_file_label');
                var fileName = copy.find('#exp_file_name');
                fileName.attr('name','file_'+totalRow);
                var fileType = copy.find('#exp_file_type');
                fileType.attr('name','type_'+totalRow);
                var expDocName=copy.find('#doc_name');
                expDocName.attr('name','name_'+totalRow);
                var expDocDate=copy.find('#doc_date');
                expDocDate.attr('name','date_'+totalRow);

                copy.find('#exp_del').click(function() {
                    copy.remove();
                });
                fileType.on('change', function() {
                    var optionSelected = $("option:selected", this);
                    var valueSelected = this.value;
                    fileLabel.text(valueSelected);
                    // var updateExpDocName='exp_name_'+valueSelected;
                    // expDocName.attr('name',updateExpDocName);
                });
                $('#add_new_exp_field').after(copy);
                //add date picker to coppied row
                expDocDate.daterangepicker({
                    singleDatePicker: true,
                    timePicker: false,
                    locale: {
                        format: 'YYYY-MM-DD'
                    }
                });
            });
            //add date picker to first row
            $('[id=doc_date]').daterangepicker({
                    singleDatePicker: true,
                    timePicker: false,
                    locale: {
                        format: 'YYYY-MM-DD'
                    }
            });
        });
    </script>
        {{-- -- exp_doc uplode field name change script end-- --}}
@endsection
