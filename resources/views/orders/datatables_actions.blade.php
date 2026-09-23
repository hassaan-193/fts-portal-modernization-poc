<div class='btn-group'>
    <a href="{{ route('orders.show', $id) }}" class='btn btn-default btn-xs'>
        <i class="fa fa-eye"></i>
    </a>
    <a href="{{ route('orders.edit', $id) }}" class='btn btn-default btn-xs'>
        <i class="fa fa-edit"></i>
    </a>
    <form action="{{ route('orders.destroy', $id) }}" method="POST" style="display:inline">
        @csrf
        @method('DELETE')
        <button type="submit" class='btn btn-danger btn-xs' onclick="return confirm('Are you sure?')">
            <i class="fa fa-trash"></i>
        </button>
    </form>
</div>
