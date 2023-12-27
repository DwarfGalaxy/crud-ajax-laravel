<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- csrf token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Category</title>
    {{-- jquery cdn --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    {{-- data table css link --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    {{-- tostify css --}}
    @toastifyCss
    {{-- Bootstrap link --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body>
    {{-- add/Update category --}}
    <div class="container mt-3">
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" id="addButton" data-bs-toggle="modal"
            data-bs-target="#addCategory">
            Add Category
        </button>

        <!-- Modal -->
        <div class="modal fade" id="addCategory" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="addCategoryLabel" aria-hidden="true">
            <form id="ajaxForm">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="model-title"></h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            {{-- get the current id on edit --}}
                            <input type="hidden" name="category_id" id="category_id">
                            {{-- name --}}
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control " name="name" id="name"
                                    value="">
                                <span id="nameError" class="text-danger"></span>
                            </div>
                            {{-- type --}}
                            <div class="mb-3">
                                <label for="option" class="form-label">Type</label>
                                <select class="form-select" name="type" id="type">
                                    <option selected disabled>Choose Option</option>
                                    <option value="electronic">Electronic</option>
                                </select>
                                <span id="typeError" class="text-danger"></span>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="saveBtn"></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    {{-- end of add/Update category --}}

    {{-- display category --}}
    <div class="container mt-3">
        <table id="category-table" class="table">
            <thead>
                <tr>
                    <th scope="col">S.No</th>
                    <th scope="col">Name</th>
                    <th scope="col">Type</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody></tbody>

        </table>
    </div>
    {{-- end of display category --}}

    {{-- jquery --}}
    <script>
        $(document).ready(function() {

            // setup csrf token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // ======read data from db=======
            var table = $('#category-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('categories.index') }}",
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'type'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            // adding title 
            $('#addButton').click(function() {
                $('#model-title').html('Add Category');
                $('#saveBtn').html('save Category');
                $('#ajaxForm')[0].reset();
            });


            // ======adding data to db=====
            var form = $('#ajaxForm')[0];
            $('#saveBtn').click(function() {
                // setting empty text on error
                $('#nameError').html('');
                $('#typeError').html('');
                // getting form data
                var formData = new FormData(form);
                // console.log(formData);
                $.ajax({
                    url: "{{ route('categories.store') }}",
                    method: 'POST',
                    processData: false,
                    contentType: false,
                    data: formData,

                    success: function(response) {
                        // reload the latest row after added
                        table.draw();
                        //    hide model if success
                        $('#addCategory').modal('hide');

                        // clear form after successfully submitting
                        $('#ajaxForm')[0].reset();

                        // display success message if form submitted
                        toastify().success(response.success);
                    },
                    error: function(error) {
                        // console.log(error);
                        let errorMessage = error.responseJSON.errors;
                        console.log(errorMessage);
                        // displaying error message

                        if (errorMessage.name) {
                            $('#nameError').html(errorMessage.name[0]);

                        }
                        if (errorMessage.type) {
                            $('#typeError').html(errorMessage.type[0]);
                        }


                    }
                });
            })

            // ======edit item============
            $('body').on('click', '.editButton', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: '{{ url('categories', '') }}' + '/' + id + '/edit',
                    method: 'GET',
                    success: function(response) {
                        // console.log(response);
                        // display model
                        $('#addCategory').modal('show');
                        // adding title
                        $('#model-title').html('Edit Category');
                        $('#saveBtn').html('Update Category');
                        // fill current data on form
                        $('#name').val(response.name);
                        $('#type').empty().append('<option selected value="' + response.type +
                            '">' + response.type + '</option>');
                        // inserted current id value in form
                        $('#category_id').val(response.id);
                    },
                    error: function(error) {
                        console.log(error);
                    }
                })
            });

            // =====Delete Item===========
            $('body').on('click', '.delButton', function() {
                var id = $(this).data('id');

                if (confirm('Are you sure you want to delete it')) {
                    $.ajax({
                        url: '{{ url('categories/destroy', '') }}' + '/' + id,
                        method: 'DELETE',
                        success: function(response) {
                            // refresh the table after delete
                            table.draw();
                            // display the delete success message
                            toastify().success(response.success);
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });
                }
            });

            // model close
            $('#addCategory').on('hidden.bs.modal', function() {
                console.log('closed');
            });


        })
    </script>
    {{-- end of jquery --}}

    {{-- tostify js --}}
    @toastifyJs

    {{-- data table cdn --}}
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    {{-- Bootstrap cdn --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>


</body>

</html>
