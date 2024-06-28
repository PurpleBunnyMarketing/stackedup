<div class="subheader py-6 py-lg-12 subheader-transparent" id="kt_subheader">
</div>

<div class="container">
    <div class="card">
        <div class="card-body">
            <table class="table table-hover table-bordered" id="facebook_posts_table">
            </table>
        </div>
    </div>
</div>
@push('extra-js-scripts')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script>
    $(document).ready(function(){

        var urlParams           =   new URLSearchParams(window.location.search);
        var page_ids            =   urlParams.get('page_id');
        var start_date          =   urlParams.get('start_date');
        var end_date            =   urlParams.get('end_date');

        const loading_text                          =   'Loading...';
        const no_data_text                          =   'No details found';


        setDataTable();
        function setDataTable(){
            var url = "{{ route('analytics.instagram.ajax', ['type' => 'instagram_posts_table']) }}";
            $('#facebook_posts_table').DataTable({
                responsive  :   true,
                processing  :   false,
                serverSide  :   false,
                searching   :   false,
                ordering    :   true,
                "bPaginate" :   true,
                "bInfo"     :   true,
                "info"      :   true,
                ajax: {
                    type    :   'POST',
                    url     :   url,
                    data    :   {
                        _token: '{{csrf_token()}}',
                        start_date,
                        end_date,
                        page_ids
                    },
                    error: function(jqXHR, textStatus, errorThrown){
                        $('#facebook_posts_table').DataTable().clear().draw();
                    }
                },
                dataSrc:function(response){
                    return response.data;
                },
                columns :   [
                    { data:'date',},
                    {
                        data:'caption',
                        render: function (data, type, full, meta) {
                            var caption = data;
                            var count = 80;

                            var result = caption.slice(0, count) + (caption.length > count ? "..." : "");
                            if(full.media_type == 'VIDEO'){
                                var html = `<div class="d-flex justify-content-between">
                                    <video src="${full.media_url}" style="object-fit: cover; width: 60px; aspect-ratio: 1; object-position: center; "></video>
                                    <div class="ml-3 flex-grow-1">
                                        ${result}
                                    </div>
                                </div>`;
                            }else{
                                var html = `<div class="d-flex justify-content-between">
                                    <img src="${full.media_url}" alt="Profile Image"
                                    style="object-fit: cover; width: 60px; aspect-ratio: 1; object-position: center; " >
                                    <div class="ml-3 flex-grow-1">
                                        ${result}
                                    </div>
                                </div>`;
                            }
                            return html;
                        },
                    },
                    { data:'like_count',},
                    { data:'comments_count',},
                ],
                columnDefs: [
                    {
                        targets     :   0,
                        title       :   'Date',
                        orderable   :   true
                    },{
                        targets     :   1,
                        title       :   'Posts',
                        orderable   :   true
                    },{
                        targets     :   2,
                        title       :   'Likes',
                        orderable   :   true
                    },{
                        targets     :   3,
                        title       :   'Comments',
                        orderable   :   true
                    },
                ],
                order: [[2, 'desc']]
            });
        }
    })
    // $("#facebook_posts_table").dataTable({
    //     searching: false,
    //     paging: false,
    //     info: false,
    //     ordering: false
    // });
</script>
@endpush