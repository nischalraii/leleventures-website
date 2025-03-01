@extends('layouts.app')

@push('head-script')
    <link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}">
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">@lang('app.edit')</h4>

                    <form id="editSettings" class="ajax-form">
                        @csrf
                        @method('PUT')

                        <div class="col-md-4">
                            <div class="form-group">
                                    <label for="name">@lang('app.partner.name')</label>
                                    <input type="text" class="form-control fs-5" id="name" name="name" value="{{ $partnerData->name }}">
                                </div>
                            </div>

                        <div class  ="col-md-4">

                                    <div class="form-group">
                                        <label for="exampleInputPassword1">@lang('app.partner.image')</label>
                                        <div class="card">
                                            <div class="card-body">
                                                <input type="file" id="input-file-now" name="image" accept=".png,.jpg,.jpeg" class="dropify" data-default-file="{{ old('image') ? asset('user-uploads/partners/'.old('image')) : asset('user-uploads/partners/'.$partnerData->image) }}"  />
                                            </div>
                                        </div>
                                    </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                    <label for="slug">@lang('app.partner.slug')</label>
                                    <input type="text" class="form-control fs-5" id="slug" name="slug" value="{{ $partnerData->slug }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                        <label for="url">@lang('app.partner.url')</label>
                                        <input type="text" class="form-control fs-5" id="url" name="url" value="{{ $partnerData->url }}">
                                    </div>
                                </div>
                            
               

                        <button type="button" id="save-form"
                                class="btn btn-success waves-effect waves-light m-r-10">
                            @lang('app.save')
                        </button>
                        <button type="reset" class="btn btn-inverse waves-effect waves-light">@lang('app.reset')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer-script')
    <script src="{{ asset('assets/node_modules/dropify/dist/js/dropify.min.js') }}" type="text/javascript"></script>
    <script>
        $('.dropify').dropify({
            messages: {
                default: '@lang("app.dragDrop")',
                replace: '@lang("app.dragDropReplace")',
                remove: '@lang("app.remove")',
                error: '@lang('app.largeFile')'
            }
        });

        $('#save-form').click(function () {
            $.easyAjax({
                url: '{{route('admin.partner.update', $partnerData->id)}}',
                container: '#editSettings',
                type: "POST",
                redirect: true,
                file: true
            })
        });
    </script>

@endpush