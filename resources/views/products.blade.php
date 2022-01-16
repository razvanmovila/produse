@extends('adminlte::page')

@section('title', 'Products')

@section('content_header')
    <h1>Lista produse</h1>

    @include('editProductModal')
    @include('saveProductModal')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://editor.datatables.net/extensions/Editor/js/dataTables.editor.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.0/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>
    <script src="https://cdn.datatables.net/datetime/1.1.1/js/dataTables.dateTime.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://adminlte.io/themes/dev/AdminLTE/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">

@stop

@section('content')
    <div class="pb-3 pt-0">

        <div class="row">
            <div class="col-sm-12 col-md-6">
                <button id="addProductBtn" class="btn btn-success btn-sm"><i class="fas fa-plus mr-1"></i> Add new product</button>
            </div>
            <div class="col-sm-12 col-md-6" style="text-align: right">
                    <span>
                        <label for="min">Minimum Price:</label> <input id="min" name="min" class="form-control-sm">
                    </span>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-6">

            </div>
            <div class="col-sm-12 col-md-6" style="text-align: right">
                <span>
                    <label for="max">Maximum Price:</label> <input type="text" id="max" name="max" class="form-control-sm">
                </span>
            </div>
        </div>

        <table class="table table-bordered data-table dt[-head|-body]-center">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Picture</th>
                <th>Price</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
@stop

@section('js')
    <script type="text/javascript">

        $(function () {

            const table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('products') }}",
                    data: function (data) {
                        data.min = $('#min').val()
                        data.max = $('#max').val()
                        data.search = $('input[type="search"]').val()
                    }
                },
                columnDefs: [
                    {targets: '_all', className: 'text-center'}
                ],
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {
                        data: 'picture', name: 'picture', render: function (data, type, row, meta) {
                            const slice = data.slice(0,4);
                            if (slice === 'http') {
                                return '<img src="' + data + '" height="50" width="50"/>';
                            }
                            else {
                                return '<img src="storage/files/' + data + '" height="50" width="50"/>';
                            }
                        }
                    },
                    {data: 'price', name: 'price', orderable: true, searchable: true},
                    {data: 'status', name: 'status', render: function (data, type, row, meta) {
                        if (data === 'active')
                            return '<span class="badge badge-success">active</span>';
                        else return '<span class="badge badge-danger">inactive</span>';
                        }},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'updated_at', name: 'updated_at'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });

            $('#min, #max').keyup( function () {
                table.draw();
            });

            toastr.options.preventDuplicates = true;

            $.ajaxSetup({
                headers:{
                    'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                }
            });

            $(function () {
                bsCustomFileInput.init();
            });

            //ADD: MODAL AUTOFILL FORM
            $(document).on('click', '#addProductBtn', function() {
                $('.saveProduct').find('form')[0].reset();
                $('.saveProduct').find('span.error-text').text('');
                const img_holder = $('.img-holder');
                $(img_holder).empty();
                $('#holderLabelAdd').text("");
                $('.saveProduct').modal('show');
            });

            //CLEAR BUTTON ADD
            $(document).on('click', '#clearInputFileAdd', function() {
                const form = $('.saveProduct').find('form');
                $(form).find('#submitSave').prop('disabled', false);
                $(form).find('span.error-text').text('');
                $(form).find('input[name="picture"]').val('');
                $('#picture').next('label').html('Choose image (max size: 1 MB)');
                $('#holderLabelAdd').text('');
                $(form).find('.img-holder').hide();
            })

            //ADD
            $('#save-product-form').on('submit', function(e) {
                e.preventDefault();
                const form = this;
                $.ajax({
                    url:$(form).attr('action'),
                    method:$(form).attr('method'),
                    data:new FormData(form),
                    processData:false,
                    dataType:'json',
                    contentType:false,
                    beforeSend: function() {
                        $(form).find('span.error-text').text('');
                    },
                    success: function(data) {
                        if(data.code === 0) {
                            $.each(data.error, function(prefix, val){
                                $(form).find('span.'+prefix+'_error').text(val[0]);
                            });
                        }else {
                            $('.data-table').DataTable().ajax.reload(null, false);
                            $('.saveProduct').modal('hide');
                            $('.saveProduct').find('form')[0].reset();
                            swal.fire(
                                'Saved!',
                                'The product has been saved',
                                'success'
                            ).then(function() {
                                window.location.reload();
                            })
                        }
                    }
                });
            });

            //Reset input file
            $('input[type="file"][id="picture"]').val('');
            //Image preview
            $('input[type="file"][id="picture"]').on('change', function() {
                //Check image size, max 1 MB allowed, else error message span
                const form = $('.saveProduct').find('form');
                $(form).find('#submitSave').prop('disabled', false);
                $(form).find('span.error-text').text('');
                $(form).find('.img-holder').show();
                if (this.files[0].size >= 1000000) {
                    $(form).find('span.'+'picture'+'_error').text('Product image too big, maximum size allowed: 1 MB');
                    $(form).find('#submitSave').prop('disabled', true);
                    $('#holderLabelAdd').text('');
                    $(form).find('.img-holder').hide();
                }
                else {
                    const img_path = $(this)[0].value;
                    const img_holder = $('.img-holder');
                    const extension = img_path.substring(img_path.lastIndexOf('.') + 1).toLowerCase();
                    if (extension === 'jpeg' || extension === 'jpg' || extension === 'png') {
                        if (typeof (FileReader) != 'undefined') {
                            img_holder.empty();
                            const reader = new FileReader();
                            reader.onload = function (e) {
                                $('#holderLabelAdd').text("Preview");
                                $('<img/>', {
                                    'src': e.target.result,
                                    'class': 'img-fluid',
                                    'style': 'max-width:100px;margin-bottom:10px;'
                                }).appendTo(img_holder);
                            }
                            img_holder.show();
                            reader.readAsDataURL($(this)[0].files[0]);
                        } else {
                            $(img_holder).html('This browser does not support FileReader');
                        }
                    } else {
                        $(img_holder).empty();
                    }
                }
            });

        //UPDATE: MODAL AUTOFILL FORM
            $(document).on('click', '#editProductBtn', function() {
                const product_id = $(this).data('id');
                $('.editProduct').find('form')[0].reset();
                $('.editProduct').find('span.error-text').text('');
                const img_holder = $('.img-holder-update');
                $(img_holder).empty();
                $(img_holder).show();
                $('#holderLabelEdit').text("");
                $.post('<?= route("get.product.details") ?>', {product_id:product_id}, function(data) {
                    $('.editProduct').find('input[name="pid"]').val(data.details.id);
                    $('.editProduct').find('input[name="name"]').val(data.details.name);
                    $('.editProduct').find('input[name="price"]').val(data.details.price);
                    $('.editProduct').find('select[name="status"]').val(data.details.status);
                    const slice = data.details.picture.slice(0,4);
                    if (slice === 'http') {
                        $('.editProduct').find('.img-holder-update').html(
                            '<img src="'+data.details.picture+'" class="img-fluid" style="max-width:100px; margin-bottom:10px;">');
                    }
                    else {
                        $('.editProduct').find('.img-holder-update').html(
                            '<img src="storage/files/'+data.details.picture+'" class="img-fluid" style="max-width:100px; margin-bottom:10px;">');
                    }
                    if (slice === 'http') {
                        $('.editProduct').find('input[type="file"]').attr(
                            'data-value', '<img src="'+data.details.picture+'" class="img-fluid" style="max-width:100px; margin-bottom:10px;">');
                    }
                    else {
                        $('.editProduct').find('input[type="file"]').attr(
                            'data-value', '<img src="storage/files/'+data.details.picture+'" class="img-fluid" style="max-width:100px; margin-bottom:10px;">');
                    }
                    $('.editProduct').find('input[name="edit_picture"]').val('');
                    $('#holderLabelEdit').text("Preview");
                    $('.editProduct').find('span.error-text').val('');
                    $('.editProduct').modal('show');
                }, 'json');
            });

            //CLEAR BUTTON EDIT
            $(document).on('click', '#clearInputFile', function() {
                const form = $('.editProduct').find('form');
                $(form).find('#submitEdit').prop('disabled', false);
                $(form).find('span.error-text').text('');
                $(form).find('input[name="edit_picture"]').val('');
                $('#edit_picture').next('label').html('Choose image (max size: 1 MB)');
                $('#holderLabelEdit').text('');
                $(form).find('.img-holder-update').hide();
            })

        //UPDATE
            $('#update-product-form').on('submit', function(e) {
                e.preventDefault();
                const form = this;
                $.ajax({
                    url:$(form).attr('action'),
                    method:$(form).attr('method'),
                    data:new FormData(form),
                    processData:false,
                    dataType:'json',
                    contentType:false,
                    beforeSend: function(){
                        $(form).find('span.error-text').text('');
                    },
                    success: function(data) {
                        if(data.code === 0) {
                            $.each(data.error, function(prefix, val){
                                $(form).find('span.'+prefix+'_error').text(val[0]);
                            });
                        }else {
                            $('.data-table').DataTable().ajax.reload(null, false);
                            $('.editProduct').modal('hide');
                            $('.editProduct').find('form')[0].reset();
                            swal.fire(
                                'Updated!',
                                'The product has been updated',
                                'success'
                            ).then(function() {
                                window.location.reload();
                            })
                        }
                    }
                });
            });

            //Reset input file
            $('input[type="file"][id="edit_picture"]').val('');
            //Image preview
            $('input[type="file"][id="edit_picture"]').on('change', function() {
                //Check image size, max 1 MB allowed, else error message span
                const form = $('.editProduct').find('form');
                $(form).find('#submitEdit').prop('disabled', false);
                $(form).find('span.error-text').text('');
                $(form).find('.img-holder-update').show();
                if (this.files[0].size >= 1000000) {
                    $(form).find('span.'+'edit_picture'+'_error').text('Product image too big, maximum size allowed: 1 MB');
                    $(form).find('#submitEdit').prop('disabled', true);
                    $('#holderLabelEdit').text('');
                    $(form).find('.img-holder-update').hide();
                }
                else {
                    const img_path = $(this)[0].value;
                    const img_holder = $('.img-holder-update');
                    const extension = img_path.substring(img_path.lastIndexOf('.') + 1).toLowerCase();
                    if (extension === 'jpeg' || extension === 'jpg' || extension === 'png') {
                        if (typeof (FileReader) != 'undefined') {
                            img_holder.empty();
                            const reader = new FileReader();
                            reader.onload = function (e) {
                                $('#holderLabelEdit').text("Preview");
                                $('<img/>', {
                                    'src': e.target.result,
                                    'class': 'img-fluid',
                                    'style': 'max-width:100px;margin-bottom:10px;'
                                }).appendTo(img_holder);
                            }
                            img_holder.show();
                            reader.readAsDataURL($(this)[0].files[0]);
                        } else {
                            $(img_holder).html('This browser does not support FileReader');
                        }
                    } else {
                        $(img_holder).empty();
                    }
                }
            });

            //DELETE
            $(document).on('click','#deleteProductBtn', function() {
                const product_id = $(this).data('id');
                const url = '<?= route("delete.product") ?>';
                swal.fire({
                    title:'Are you sure?',
                    text: "You won't be able to revert the delete!",
                    icon: 'warning',
                    showCancelButton:true,
                    showCloseButton:true,
                    cancelButtonText:'Cancel',
                    confirmButtonText:'Yes, Delete',
                    cancelButtonColor:'#d33',
                    confirmButtonColor:'#556ee6',
                    width:300,
                    allowOutsideClick:false
                }).then(function(result){
                    if(result.value){
                        $.post(url,{product_id:product_id}, function(data){
                            if(data.code === 1){
                                $('.data-table').DataTable().ajax.reload(null, false);
                                swal.fire(
                                    'Deleted!',
                                    'The product has been deleted',
                                    'success'
                                )
                            }else{
                                swal.fire(
                                    'Error!',
                                    'The product could not be deleted',
                                    'error'
                                )
                            }
                        },'json');
                    }
                });
            });
        });
    </script>
@stop
