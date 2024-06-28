@push('extra-css-styles')
<style>
    /* Bar Graph Horizontal */
    .bar-graph .year {
        -webkit-animation: fade-in-text 2.2s 0.1s forwards;
        -moz-animation: fade-in-text 2.2s 0.1s forwards;
        animation: fade-in-text 2.2s 0.1s forwards;
        opacity: 0;
    }

    .bar-graph-horizontal {
        max-width: 380px;
    }

    .bar-graph-horizontal>div {
        float: left;
        margin-bottom: 8px;
        width: 100%;
    }

    .bar-graph-horizontal .year {
        float: left;
        margin-top: 18px;
        width: 50px;
    }

    .bar-graph-horizontal .bar {
        border-radius: 3px;
        height: 55px;
        float: left;
        overflow: hidden;
        position: relative;
        width: 0;
    }

    .bar-graph-one .bar::after {
        -webkit-animation: fade-in-text 2.2s 0.1s forwards;
        -moz-animation: fade-in-text 2.2s 0.1s forwards;
        animation: fade-in-text 2.2s 0.1s forwards;
        color: #fff;
        content: attr(data-percentage);
        font-weight: 700;
        position: absolute;
        right: 16px;
        top: 17px;
    }

    .bar-graph-one .bar-one .bar {
        background-color: #64b2d1;
        -webkit-animation: show-bar-one 1.2s 0.1s forwards;
        -moz-animation: show-bar-one 1.2s 0.1s forwards;
        animation: show-bar-one 1.2s 0.1s forwards;
    }

    .bar-graph-one .bar-two .bar {
        background-color: #5292ac;
        -webkit-animation: show-bar-two 1.2s 0.2s forwards;
        -moz-animation: show-bar-two 1.2s 0.2s forwards;
        animation: show-bar-two 1.2s 0.2s forwards;
    }

    .bar-graph-one .bar-three .bar {
        background-color: #407286;
        -webkit-animation: show-bar-three 1.2s 0.3s forwards;
        -moz-animation: show-bar-three 1.2s 0.3s forwards;
        animation: show-bar-three 1.2s 0.3s forwards;
    }

    .bar-graph-one .bar-four .bar {
        background-color: #2e515f;
        -webkit-animation: show-bar-four 1.2s 0.4s forwards;
        -moz-animation: show-bar-four 1.2s 0.4s forwards;
        animation: show-bar-four 1.2s 0.4s forwards;
    }

    /* Bar Graph Horizontal Animations */
    @-webkit-keyframes show-bar-one {
        0% {
            width: 0;
        }

        100% {
            width: 69.6%;
        }
    }

    @-webkit-keyframes show-bar-two {
        0% {
            width: 0;
        }

        100% {
            width: 71%;
        }
    }

    @-webkit-keyframes show-bar-three {
        0% {
            width: 0;
        }

        100% {
            width: 74.7%;
        }
    }

    @-webkit-keyframes show-bar-four {
        0% {
            width: 0;
        }

        100% {
            width: 76.8%;
        }
    }

    @-webkit-keyframes fade-in-text {
        0% {
            opacity: 0;
        }

        100% {
            opacity: 1;
        }
    }
</style>
@endpush

<div class="subheader py-6 py-lg-12 subheader-transparent" id="kt_subheader">
    {{-- <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <div class="d-flex align-items-center flex-wrap mr-1">
            <div class="d-flex  post-type">
                <a href="javascript:;" class="post-link btn font-size-h3
                        font-weight-bold my-2 mr-5" data-type="posts">GMB - Posts</a>
            </div>
        </div>
    </div> --}}
</div>

<div class="container">
    <div class="card">
        <div class="card-body">
            {{-- Datatable Start --}}
            <table class="table table-bordered table-hover table-checkable" id="google_business_posts_table"
                style="margin-top: 13px !important"></table>
            {{-- Datatable End --}}
        </div>
    </div>
</div>


@push('extra-js-scripts')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script>
    $(document).ready(function() {
        var urlParams = new URLSearchParams(window.location.search);

        const page_ids = urlParams.get('page_id');
        const start_date = urlParams.get('start_date');
        const end_date = urlParams.get('end_date');
        // datatable
        $('#google_business_posts_table').DataTable({
            responsive: true,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            searching: false,
            ordering: false,
            "bPaginate": false,
            "bInfo" : false,
            ajax: {
                type: 'POST',
                url: "{{ route('analytics.google-business.ajax', ['type' => 'posts']) }}",
                data: {
                    _token: '{{csrf_token()}}',
                    columnsDef: ['created_date','image_url','summery', 'post_type', 'state'],
                    start_date,
                    end_date,
                    page_ids
                },
            },
            columns: [
                {
                    data: 'created_date'
                },
                {
                    data: 'image_url',
                    render: function (data, type, full, meta) {
                        var text = full.summary;
                        var count = 80;
                        var html = `<div class="d-flex justify-content-between">`;
                        var result = text.slice(0, count) + (text.length > count ? "..." : "");

                        if(data){
                            html += `<img src="${data}" alt="Profile Image"
                                style="object-fit: cover; width: 60px; aspect-ratio: 1; object-position: center; ">`;
                        }
                        html += `<div class="ml-3 flex-grow-1">
                                    ${result}
                                </div>
                            </div>`;
                        return html;
                    },
                },
                {
                    data: 'post_type'
                },
                {
                    data: 'state'
                }
            ],
            columnDefs: [
                {
                    targets: 0,
                    title: 'DATE',
                    orderable: false
                },
                {
                    targets: 1,
                    title: 'SUMMARY',
                    orderable: false
                },
                {
                    targets: 2,
                    title: 'POST_TYPE',
                    orderable: false
                },
                {
                    targets: 3,
                    title: 'STATE',
                    orderable: false
                }
            ],
            order: [
                [0, 'desc']
            ]
        });
    });
</script>
@endpush
