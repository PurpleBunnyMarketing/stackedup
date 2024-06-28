@extends('admin.layouts.app')

@push('breadcrumb')
{!! Breadcrumbs::render('users_create') !!}
@endpush

@section('content')
<div class="container">
    <div class="card card-custom">
        <div class="card-header">
            <div class="card-title">
                <span class="card-icon">
                    <i class="fas fa-user-plus text-primary"></i>
                </span>
                <h3 class="card-label text-uppercase">ADD {{ $custom_title }}</h3>
            </div>
        </div>
        {{-- @dd($settings); --}}
        <!--begin::Form-->
        <form id="frmAddUser" method="POST" action="{{ route('admin.update-settings.change-setting') }}"
            enctype="multipart/form-data">
            @csrf
            <div class="card-body">

                @foreach ($settings as $setting)

                {{-- build_version --}}
                <div class="form-group">
                    <label for="build_version[{{$setting->slug}}]">{!!$mend_sign!!} {{ucfirst($setting->slug)}} Build
                        Version :</label>
                    <input type="text" value="{{$setting->build_version}}"
                        class="form-control @error('build_version[]') is-invalid @enderror" id="build_version"
                        name="build_version[{{$setting->slug}}]]" value="{{ old('build_version[]') }}"
                        placeholder="Enter Build Version" autocomplete="build_version[]" spellcheck="false"
                        autocapitalize="sentences" tabindex="0" autofocus />
                    @if ($errors->has('build_version[{{$setting->slug}}]'))
                    <span class="help-block">
                        <strong class="form-text">{{ $errors->first('build_version[]') }}</strong>
                    </span>
                    @endif
                </div>

                {{-- app_version --}}
                <div class="form-group">
                    <label for="app_version">{!!$mend_sign!!} {{ucfirst($setting->slug)}} App Version:</label>
                    <input type="text" value="{{$setting->app_version}}"
                        class="form-control @error('app_version[]') is-invalid @enderror" id="app_version"
                        name="app_version[{{$setting->slug}}]" value="{{ old('app_version[]') }}"
                        placeholder="Enter last name" autocomplete="app_version[]" spellcheck="false"
                        autocapitalize="sentences" tabindex="0" autofocus />
                    @if ($errors->has('app_version[{{$setting->slug}}]'))
                    <span class="help-block">
                        <strong class="form-text">{{ $errors->first('app_version') }}</strong>
                    </span>
                    @endif
                </div>

                {{-- force_update chekbox --}}
                <div class="form-group">
                    {{-- <label for="is_force_update">{!!$mend_sign!!} {{ucfirst($setting->slug)}} Force Update:
                    </label>
                    <input type="text" value="{{$setting->is_force_update}}"
                        class="form-control @error('is_force_update[]') is-invalid @enderror" id="is_force_update"
                        name="is_force_update[{{$setting->slug}}]" value="{{ old('is_force_update[]') }}"
                        placeholder="Enter last name" autocomplete="is_force_update[]" spellcheck="false"
                        autocapitalize="sentences" tabindex="0" autofocus />
                    @if ($errors->has('is_force_update[{{$setting->slug}}]'))
                    <span class="help-block">
                        <strong class="form-text">{{ $errors->first('is_force_update') }}</strong>
                    </span>
                    @endif --}}

                    <label class="checkbox">
                        <input type="checkbox" name="is_force_update[{{$setting->slug}}]" @if ($setting->is_force_update
                        == 1) checked="checked" @endif />
                        <span></span>
                        &nbsp;Force Update {{ucfirst($setting->slug)}}
                    </label>
                </div>


                @endforeach


            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2 text-uppercase"> Add {{ $custom_title }}</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary text-uppercase">Cancel</a>
            </div>
        </form>
        <!--end::Form-->
    </div>
</div>
@endsection

@push('extra-js')
<script>
    $(document).ready(function () {
    $("#frmAddUser").validate({
        rules: {
            'build_version[]': {
                required: true,
                not_empty: true,
                minlength: 3,
            },
            'app_version[]': {
                required: true,
                not_empty: true,
                minlength: 3,
            },

        },
        messages: {
            'build_version[]': {
                required: "@lang('validation.required',['attribute'=>'first name'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'first name'])",
                minlength:"@lang('validation.min.string',['attribute'=>'first name','min'=>3])",
            },
            'app_version[]': {
                required: "@lang('validation.required',['attribute'=>'last name'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'last name'])",
                minlength:"@lang('validation.min.string',['attribute'=>'last name','min'=>3])",
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
    $('#frmAddUser').submit(function () {
        if ($(this).valid()) {
            addOverlay();
            $("input[type=submit], input[type=button], button[type=submit]").prop("disabled", "disabled");
            return true;
        } else {
            return false;
        }
    });
});
</script>
@endpush