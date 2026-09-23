<script>
$(window).on('load', function() {
    $("div.table-filter").html(`
        <div class="row">
            <div class="col-sm-12">
                <span class="filter-icon"><i class="fa fa-filter"></i></span>
                <div class="filter-group">
                    <label>Search By</label>
                    <select class="form-control" id="search_type" onchange="setSearchType(this)" name="search_type">
                        <option value="date" >Date Range</option>
                        <option value=1 >Complete</option>
                        <option value=0 >Pending</option>
                    </select>
                </div>
                <div class="filter-group" >
                    <input type="text" class="form-control" id="daterange" name="daterange">
                    <input type="hidden" id="from_date" name="from_date">
                    <input type="hidden" id="to_date" name="to_date">
                    <input type="hidden" id="request_status" name="request_status">
                </div>
                <div class="filter-group">
                    <label></label>
                    <button type="button" onclick="search()" class="btn btn-primary" >Search </button>
                    <button type="button" onclick="clearFilter()" class="btn btn-danger" >Clear </button>
                </div>
            </div>
        </div>`
    );

    //Date range picker with time picker
    $('#daterange').daterangepicker({
        autoUpdateInput: false,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    $('#daterange').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        $('#from_date').val(picker.startDate.format('YYYY-MM-DD'));
        $('#to_date').val(picker.endDate.format('YYYY-MM-DD'));
    });

    $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
        $('#datepicker').val('');
        $('#from_date').val('');
        $('#to_date').val('');
    });

});

function setSearchType(sel){
    if(sel.value==1||sel.value==0){
        $('#request_status').val(sel.value);
    }
    else{
        $('#request_status').attr('value',null);
    }
}

function search(){
    if($('#daterange').val() != '' || $('#request_status').val() != '' )
        window.LaravelDataTables["dataTableBuilder"].draw(false);
}
function clearFilter(){
    $('#daterange').val('');
    $('#from_date').val('');
    $('#to_date').val('');
    $('#request_status').val('');

    // Search Type filter
    document.getElementById('search_type').selectedIndex = 0;

    window.LaravelDataTables["dataTableBuilder"].draw(false);
}
</script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
