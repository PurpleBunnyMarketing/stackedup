@extends('admin.layouts.app')
@push('breadcrumb')
{!! Breadcrumbs::render('faqs_update', $faq->id) !!}
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
        <form id="frmAddFaqs" method="POST" action="{{ route('admin.faqs.update', $faq->id) }}"
            enctype="multipart/form-data">
            @csrf
            @method('put')
            <div class="card-body">
                {{-- Question --}}
                <div class="form-group">
                    <label for="question">Question{!!$mend_sign!!}</label>
                    <input type="text" class="form-control @error('question') is-invalid @enderror" id="question"
                        name="question" value="{{ old('question') != null ? old('question') : $faq->question }}"
                        placeholder="Enter Question" autocomplete="question" spellcheck="false"
                        autocapitalize="sentences" tabindex="0" autofocus />
                    @if ($errors->has('question'))
                    <span class="help-block">
                        <strong class="form-text">{{ $errors->first('question') }}</strong>
                    </span>
                    @endif
                </div>

                {{-- Answer --}}
                <div class="form-group">
                    <label for="answer">Answer{!!$mend_sign!!}</label>
                    <textarea class="form-control @error('answer') is-invalid @enderror" rows="10" id="answer"
                        name="answer" placeholder="Enter Answer" autocomplete="answer" spellcheck="false"
                        autocapitalize="sentences" tabindex="0" autofocus />{{ old('answer') != null ? old('answer') :
                    $faq->answer }}</textarea>
                    @if ($errors->has('answer'))
                    <span class="help-block">
                        <strong class="form-text">{{ $errors->first('answer') }}</strong>
                    </span>
                    @endif
                </div>



            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2"> Update {{ $custom_title }}</button>
                <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
        <!--end::Form-->
    </div>
</div>
@endsection

@push('extra-js-scripts')
<script>
    $(document).ready(function () {
    $("#frmAddFaqs").validate({
        rules: {
            question: {
                required: true,
                not_empty: true
            },
            answer: {
                required: true,
                not_empty: true
            },
            
        },
        messages: {
            question: {
                required: "@lang('validation.required',['attribute'=>'question'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'question'])"
            },
            answer: {
                required: "@lang('validation.required',['attribute'=>'answer'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'answer'])"
            }
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
    $('#frmAddFaqs').submit(function () {
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