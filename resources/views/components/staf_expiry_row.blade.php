<tr class="odd" id="staff_table">
    <td class="dtr-control sorting_1" tabindex="0">{{ $number }}</td>
    <td>{{ $name }}</td>
    <td>{{ $type }}</td>
    <td>{{ $date }}</td>
    <td>
        <div class='btn-group'>
            <a href="{{ route('staf.show', $id) }}" class='btn btn-success'>
                <i class="fa fa-eye"></i>
            </a>
            <a href="{{ route('staf.edit', $id) }}" class='btn btn-info'>
                <i class="fa fa-edit"></i>
            </a>
        </div>
    </td>
</tr>
