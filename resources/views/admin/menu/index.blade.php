@extends('layouts.app')

@push('head-script')
    <link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
@endpush

@if (in_array('add_menu', $userPermissions))
    @section('create-button')
        <a href="{{ route('admin.menu.create') }}" class="btn btn-primary"><i
                class="fa fa-plus-circle"></i> @lang('app.createNew')</a>
    @endsection
@endif


@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive m-t-40">
                        <table id="myTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>

                                    <th>S.No</th>
                                    <th>@lang('app.menus.title')</th>
                                    <th>@lang('app.menus.link')</th>
                                    <th>@lang('app.menus.position')</th>
                                       <th>@lang('app.menus.section')</th>
                                    <th class="noExport">@lang('app.action')</th>

                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer-script')
    <script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>

    <script>
        var table = $('#myTable').dataTable({
            responsive: true,
            serverSide: true,
            ajax: '{!! route('admin.menu.data') !!}',
            pageLength: 100,
            order: [[4, 'desc']],
            language: languageOptions(),
            "fnDrawCallback": function(oSettings) {
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            },

          
            columns: [{
                    "targets": 0,
                    "data": null,
                    "render": function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: 'title',
                    name: 'title',
                    width:'19%'
                },
                {
                    data: 'link',
                    name: 'link',
                    width:'24%'
                   
                },
                {
                    data: 'position',
                    name: 'position',
                    width:'22%'
                   
                },
               {
                    data: 'section',
                    name: 'section',
                    width:'20%'
                   
                },
                
                {
                    data: 'action',
                    name: 'action',
                    width:'10%'
                }
            ],


        });


        new $.fn.dataTable.FixedHeader(table);



        $('body').on('click', '.sa-params', function() {
            var id = $(this).data('row-id');
            var deleteUrl = $(this).data('url');
            swal({
                title: "@lang('app.messages.areYouSure')",
                text: "@lang('app.messages.deleteWarning')",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('app.delete')",
                cancelButtonText: "@lang('app.cancel')",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm) {
                if (isConfirm) {

                    var url = "{{ route('admin.menu.destroy', ':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {
                            '_token': token,
                            '_method': 'DELETE'
                        },
                        success: function(response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                //                                    swal("Deleted!", response.message, "success");
                                table._fnDraw();
                            }
                        }
                    });
                }
            });
        })
    </script>
@endpush