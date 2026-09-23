<script>
$(window).on('load', function() {
    $("div.table-filter").html(`
        <div class="row">
            <div class="col-sm-12">
                <span class="filter-icon"><i class="fa fa-filter"></i></span>
                <div class="filter-group">
                    <label>Search By</label>
                    <select class="form-control" id="search_type" onchange="setSearchType(this)" name="search_type">
                        <option value="date" >Date</option>
                    </select>
                </div>
                <div class="filter-group" >
                    <input type="text" class="form-control" id="date" name="date">
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
    $('#date').daterangepicker({
        singleDatePicker: true,
        timePicker: false,
        locale: {
            format: 'YYYY-MM'
        }
    });
});

function search(){
    if($('#date').val() != '')
        window.LaravelDataTables["dataTableBuilder"].draw(false);
}
function clearFilter(){
    $('#date').val('');

    // Search Type filter
    document.getElementById('search_type').selectedIndex = 0;

    window.LaravelDataTables["dataTableBuilder"].draw(false);
}
</script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
