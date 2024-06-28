@extends('admin.layouts.app')
@push('breadcrumb')
    {!! Breadcrumbs::render('cms_update', $page->id) !!}
@endpush

@section('content')
<div class="container">
    <div class="card card-custom">
        <div class="card-header">
            <div class="card-title">
                <span class="card-icon">
                    <i class="{{$icon}} text-primary"></i>
                </span>
                <h3 class="card-label text-uppercase">Edit {{ $custom_title }}</h3>
            </div>
        </div>
       
        <!--begin::Form-->
        <form id="frmEditcms" method="POST" action="{{ route('admin.pages.update', $page->id) }}" enctype="multipart/form-data">
            @csrf
            @method('put')
            <div class="card-body">

                {{--  Name --}}
                <div class="form-group">
                    <label for="title">Title{!!$mend_sign!!}</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') != null ? old('title') : $page->title }}" placeholder="Enter title" autocomplete="title" spellcheck="false" autocapitalize="sentences" tabindex="0" autofocus />
                    @if ($errors->has('title'))
                        <span class="help-block">
                            <strong class="form-text">{{ $errors->first('title') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Description --}}
                <div class="form-group">
                    <label for="description">{!!$mend_sign!!}Description:</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" name="description" id="description" rows="5" autocomplete="description" spellcheck="false" autocapitalize="sentences" tabindex="0" data-error-container="#error-description">{{ old('description',$page->description) }}</textarea>
                    <span id="error-description"></span>
                    @if ($errors->has('description'))
                        <span class="help-block">
                            <strong class="form-text">{{ $errors->first('description') }}</strong>
                        </span>
                    @endif
                </div>

              
                

            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2">Update {{ $custom_title }}</button>
                <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
        <!--end::Form-->
    </div>
</div>
@endsection
@push('extra-js-scripts')
<script type="text/javascript">
    var summernoteImageUpload = '{{ route('admin.summernote.imageUpload') }}';
    var summernoteMediaDelete = '{{ route('admin.summernote.mediaDelete') }}';
</script>
<script src="{{ asset('admin/plugins/summernote/summernotecustom.js') }}"></script>
<script>
$(document).ready(function () {

    var summernoteElement = $('#description');
    var imagePath = 'summernote/cms/image';
    summernoteElement.summernote({
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                //['table', ['table']],
                ['insert', ['link', 'picture']],
                // ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']],
            ],
            height: 300,
            callbacks: {
                onImageUpload : function(files, editor, welEditable) {
                     for(var i = files.length - 1; i >= 0; i--) {
                             sendFile(files[i], this,imagePath);
                    }
                },
                onMediaDelete : function(target) {
                    deleteFile(target[0].src);
                },
            }
    });
    
    $("#frmEditcms").validate({
        ignore:[],
        rules: {
           
            title: {
                required: true,
                not_empty: true
            },
            description: {
                required: true,
                not_empty: true,
                minlength: 3,
            },
        },
        messages: {
           
            title: {
                required: "@lang('validation.required',['attribute'=>'answer'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'answer'])"
            },
             description: {
                required: "@lang('validation.required',['attribute'=>'description'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'description'])",
                minlength:"@lang('validation.min.string',['attribute'=>'description','min'=>3])",
            },
        },
        errorClass: 'invalid-feedback',
        errorElement: 'span',
        highlight: function (element) {
            $(element).addClass('is-invalid');
            $(element).siblings('label').addClass('text-danger'); // For Label
        },
        unhighlight: function (element) {
            $(element).removeClass('is-invalid');
            $(element).siblings('label').removeClass('text-danger'); // For Label
        },
        errorPlacement: function (error, element) {
            if (element.attr("data-error-container")) {
                error.appendTo(element.attr("data-error-container"));
            } else {
                error.insertAfter(element);
            }
        }
    });
    $('#frmEditcms').submit(function (e) {
        
        if(summernoteElement.summernote('isEmpty')) {
            e.preventDefault();
            $('#description-error').remove();
            $('<span class="text-danger" id="description-error"><strong class="form-text">The description field is required.</strong></span>').insertAfter('.note-editor');
            return false;
        }else {
            if ($(this).valid()) {
                addOverlay();
                $("input[type=submit], input[type=button], button[type=submit]").prop("disabled", "disabled");
                return true;
            } else {
                return false;
            }
        }
    });
});
</script>
@endpush


{{-- @push('extra-js-scripts')
<script type="text/javascript">
    var summernoteImageUpload = '{{ route('admin.summernote.imageUpload') }}';
    var summernoteMediaDelete = '{{ route('admin.summernote.mediaDelete') }}';
</script>
<script src="{{ asset('admin/plugins/summernote/summernotecustom.js') }}"></script>
<script>
$(document).ready(function () {
    var summernoteElement = $('#description');
    var imagePath = 'summernote/cms/image';
    summernoteElement.summernote({
            height: 300,
            callbacks: {
                onImageUpload : function(files, editor, welEditable) {
                     for(var i = files.length - 1; i >= 0; i--) {
                             sendFile(files[i], this,imagePath);
                    }
                },
                onMediaDelete : function(target) {
                    deleteFile(target[0].src);
                },
            }
    });
    $("#frmEditPackages").validate({
        ignore:[],
        rules: {
            amount: {
                required: true,
                not_empty: true
            },
            package_type: {
                required: true,
                not_empty: true
            },
            description: {
                required: true,
                not_empty: true,
                minlength: 3,
            },
        },
        messages: {
            amount: {
                required: "@lang('validation.required',['attribute'=>'amount'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'amount'])"
            },
            package_type: {
                required: "@lang('validation.required',['attribute'=>'answer'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'answer'])"
            },
             description: {
                required: "@lang('validation.required',['attribute'=>'description'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'description'])",
                minlength:"@lang('validation.min.string',['attribute'=>'description','min'=>3])",
            },
        },
        errorClass: 'invalid-feedback',
        errorElement: 'span',
        highlight: function (element) {
            $(element).addClass('is-invalid');
            $(element).siblings('label').addClass('text-danger'); // For Label
        },
        unhighlight: function (element) {
            $(element).removeClass('is-invalid');
            $(element).siblings('label').removeClass('text-danger'); // For Label
        },
        errorPlacement: function (error, element) {
            if (element.attr("data-error-container")) {
                error.appendTo(element.attr("data-error-container"));
            } else {
                error.insertAfter(element);
            }
        }
    });
    $('#frmEditPackages').submit(function () {
        if(summernoteElement.summernote('isEmpty')) {
            e.preventDefault();
            $('#description-error').remove();
            $('<span class="text-danger" id="description-error"><strong class="form-text">The description field is required.</strong></span>').insertAfter('.note-editor');
            return false;
        }else {
            if ($(this).valid()) {
                addOverlay();
                $("input[type=submit], input[type=button], button[type=submit]").prop("disabled", "disabled");
                return true;
            } else {
                return false;
            }
        }
    });

    //tell the validator to ignore Summernote elements
    $('form').each(function () {
        if ($(this).data('validator'))
            $(this).data('validator').settings.ignore = ".note-editor *";
    });
});
</script>
@endpush
 --}}