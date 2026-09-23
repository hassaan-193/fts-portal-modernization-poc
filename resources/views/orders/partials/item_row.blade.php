<tr>
    <td>
        <input type="text" name="items[{{ $index }}][item_description]" value="{{ $item->item_description ?? '' }}" class="form-control" required>
    </td>
    <td>
        <input type="text" name="items[{{ $index }}][unit]" value="{{ $item->unit ?? '' }}" class="form-control">
    </td>
    <td>
        <input type="number" name="items[{{ $index }}][quantity]" value="{{ $item->quantity ?? '' }}" class="form-control qty item-calc" step="any" required>
    </td>
    <td>
        <input type="number" name="items[{{ $index }}][unit_price]" value="{{ $item->unit_price ?? '' }}" class="form-control price item-calc" step="any" required>
    </td>
    <td>
        <input type="number" name="items[{{ $index }}][total]" class="form-control total" readonly>
    </td>
    <td>
        <button type="button" class="btn btn-danger removeRow">Remove</button>
    </td>
</tr>
