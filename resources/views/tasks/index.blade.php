<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo List</title> 
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Todo List</h1>
        <form id="task-form">
            @csrf
            <div class="form-group">
                <input type="text" id="task-name" name="name" class="form-control" placeholder="New Task">
            </div>
            <button type="submit" class="btn btn-primary">Add Task</button>
        </form>
        <div class="row text-right">
        <button id="show-all" class="btn btn-primary">Show All Tasks</button>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>                   
                    <th>Task</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="task-list">
                @php $count = 1; @endphp
                @foreach ($tasks as $task)
                <tr data-id="{{ $task->id }}" class="{{ $task->status ? 'status' : '' }}">
                    <td > {{ $count++}} </td>
                    <td>{{ $task->name }}</td>
                    <td>
                       
                    </td>
                    <td>
                    <input type="checkbox" class="complete-task" {{ $task->status ? 'checked' : '' }}>  <button class="delete-task btn btn-danger">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#task-form').on('submit', function(event) {
                event.preventDefault();
                  
                $.ajax({
                    url: '{{ url('/tasks') }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        let newIndex = $('#task-list tr').length + 1;
                        $('#task-list').append('<tr data-id="' + response.id + '">'  +
                            '<td> '+  newIndex + '</td>' +
                            '<td>' + response.name + '</td>' +
                            '<td></td>' +
                            '<td><input type="checkbox" class="complete-task"> <button class="delete-task btn btn-danger">Delete</button></td>' +
                            '</tr>');
                        $('#task-name').val('');
                    },
                    error: function(response) {
                        alert('Error: ' + response.responseJSON.message);
                    }
                });
            });

            $(document).on('click', '.complete-task', function() {
                let taskId = $(this).closest('tr').data('id');
                $.ajax({
                    url: '{{ url('/tasks') }}/' + taskId + '/complete',
                    method: 'POST',
                    success: function(response) {
                        let row = $('tr[data-id="' + response.id + '"]');
                        row.addClass('completed');
                        row.find('.complete-task').replaceWith('<button class="btn btn-success"><i class="fa fa-check-circle" aria-hidden="true"></i></button>');
                    },
                    error: function(response) {
                        alert('Error: ' + response.responseJSON.message);
                    }
                });
            });

            $(document).on('click', '.delete-task', function() {
                if (confirm('Are you sure to delete this task?')) {
                    let taskId = $(this).closest('tr').data('id');
                    $.ajax({
                        url: '{{ url('/tasks') }}/' + taskId,
                        method: 'DELETE',
                        success: function(response) {
                            $('tr[data-id="' + taskId + '"]').remove();
                        },
                        error: function(response) {
                            alert('Error: ' + response.responseJSON.message);
                        }
                    });
                }
            });

            $('#show-all').on('click', function() {
                $.ajax({
                    url: '{{ url('/tasks/all') }}',
                    method: 'GET',
                    success: function(response) {
                        $('#task-list').empty();
                        var sr = 1;
                        response.forEach(function(task) {
                            $('#task-list').append(
                                '<tr data-id="' + task.id + '" class="' + (task.status ? 'status' : '') + '">' +
                                    '<td>' + sr++ + '</td>' +
                                    '<td>' + task.name + '</td>' +
                                    '<td><span class="' + (task.status ? 'label label-success' : 'label label-warning') + '">' + (task.status ? 'Completed' : 'Pending') + '</span></td>' +
                                    '<td>' +
                                        (task.status
                                            ? '<button class="btn btn-success"><i class="fa fa-check-circle" aria-hidden="true"></i></button>'
                                            : '<input type="checkbox" class="complete-task">') +
                                        ' <button class="delete-task btn btn-danger">Delete</button>' +
                                    '</td>' +
                                '</tr>'
                            );
                        });
                    },
                    error: function(response) {
                        alert('Error: ' + response.responseJSON.message);
                    }
                });
            });
        });
    </script>

    <style>
        .completed td {
            text-decoration: line-through;
        }
    </style>
</body>
</html>
