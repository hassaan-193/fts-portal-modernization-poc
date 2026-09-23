<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <h3>Members:</h3>
        </div>
    </div>
    <div class="col-md-12">
        <table class="table" width="100%">
            <tr>
                <th><input type="checkbox" id="checkAll"/></th>
                <th>@lang('models/staff_payrolls.fields.name')</th>
                <th>@lang('models/staff_payrolls.fields.total_salary')</th>
                <th>@lang('models/staff_payrolls.fields.overtime_rate')</th>
                <th>@lang('models/staff_payrolls.fields.advance')</th>
                <th>@lang('models/staff_payrolls.fields.absents')</th>
                <th>@lang('models/staff_payrolls.fields.hours')</th>
                <th>Overtime Amount</th>
                <th>@lang('models/staff_payrolls.fields.plus_adjustment')</th>
                <th>@lang('models/staff_payrolls.fields.minus_adjustment')</th>
                <th>@lang('models/staff_payrolls.fields.total_amount')</th>
                <th>@lang('models/staff_payrolls.fields.note')</th>
            </tr>
            <tbody id="memberTableBody"></tbody>
        </table>
    </div>
</div>
@section('scripts')
@parent
<script type="text/javascript">
    $('#search_memebers').click(function (){
        $(this).attr('disabled',true);
        $('#memberTableBody').html('');

        $.ajax({
            type:'GET',
            url: window.baseUrl(`/staffPayrolls/members/${$('#staf_type').val()}/list/${$('#date').val()}`),
            success:function(data){
                data.forEach((item,key) => {
                    var row = `
                    <tr>
                        <td>
                            <input type="hidden" value="${item.id}" name="id[]">
                            <input class="checkbox" type="checkbox">
                        </td>
                        <td>
                            ${item.name}
                        </td>
                        <td>
                            ${item.total_salary}
                        </td>
                        <td>
                           ${item.overtime_rate}
                        </td>

                        <td>
                           
                        </td>
                         <td>
                            <input type="number" class="form-control calculate" name="absents[]" value="0" />
                        </td>
                        <td>
                            <input type="number" class="form-control calculate" name="hours[]" value="0" />
                        </td>
                        <td>
                            <input type="number" class="form-control overtime-amount" value="0" readonly />
                        </td>
                        <td>
                            <input type="number" class="form-control calculate" name="plus_adjustment[]" value="0" />
                        </td>
                        <td>
                            <input type="number" class="form-control calculate" name="minus_adjustment[]" value="0" />
                        </td>
                        <td>
                            <input type="number" class="form-control" name="total_amount[]" value="0" />
                        </td>
                        <td>
                            <input type="text" class="form-control" name="note[]" value="" />
                        </td>
                    </tr>
                    `;
                    $('#memberTableBody').append(row)
                });
                $("#search_memebers").attr('disabled',false);
            }
        });
    });
    $("#checkAll").click(function(){
        $('input:checkbox').not(this).prop('checked', this.checked);
    });
    $('body').on("change", ".calculate", function() {
        var $element = $(this).parents('tr');
        var salary = parseFloat($element.find("td:eq(2)").text());
        var overtime = parseFloat($element.find("td:eq(3)").text());
        var absents = parseFloat($element.find("[name^=absents]").val()) || 0;
        var hours = parseFloat($element.find("[name^=hours]").val()) || 0;
        var plus_adjustment = parseFloat($element.find("[name^=plus_adjustment]").val()) || 0;
        var minus_adjustment = parseFloat($element.find("[name^=minus_adjustment]").val()) || 0;
        
        // Calculate overtime amount
        var overtimeAmount = overtime * hours;
        $element.find(".overtime-amount").val(overtimeAmount);
        
        // Formula: Total Salary + Overtime Amount - Absents + Plus Adjustment - Minus Adjustment
        var total = (salary + overtimeAmount - absents + plus_adjustment) - minus_adjustment;
        $element.find("[name^=total_amount]").val(total);
    });
    $("form").submit(function(e){
        e.preventDefault();
        var $elem = $("input[type=checkbox].checkbox");
        var result = '';
        $elem.each(function(){
            if (this.checked)
                result = result + $elem.index(this) + ',';
        });
        $(this).append(`<input type="hidden" value="${result}" name="checked_indexes">`);

        this.submit();
    });
</script>
@endsection
