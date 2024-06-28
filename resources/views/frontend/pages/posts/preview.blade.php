
@if(!empty($pages))
<div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
	<!--begin::Title-->
	<h2 class="font-weight-bold my-2 mr-5">PREVIEW POST</h2>
	<!--end::Title-->
</div>
<div class="add-post-card-group">
	<div class="add-post-card-list">
		@foreach($pages as $value)
			@if($value->media->name == 'Instagram')
				<div class="add-post-card p-4 mb-6 bg-white">
					<div class="head d-flex justify-content-between mb-6">
						<div class="d-flex justify-content-between align-items-center w-100">
							<div class="d-flex align-items-center">
								<div class="social-icon">
									<img src="{{ $value->image_url ?? asset('frontend/images/default_profile.jpg') }}"
										alt="social-icon">
								</div>
								<div class="author-detail ml-8">
									<h5 class="mb-2">{{ $value->page_name ?? ""}}</h5>

									<span class="text-muted schedule-date-text">{{
										\Carbon\Carbon::parse($schedule_date)->format('d
										M, Y') }}</span>
									<span class="text-muted schedule-time-text">{{ ' - '. $schedule_time }}</span>
								</div>
							</div>
							<div class="social-media-icon">
								<img src="{{ $value->media->image_url ?? asset('frontend/images/default_profile.jpg') }}"
									alt="social-icon">
							</div>
						</div>
					</div>
					<div class="add-post-image mb-4">
						@if(!empty($upload_file))
							@if(\File::extension(Storage::url($upload_file[0] ?? "")) == 'mp4' )
								<img src="{{ $thumbnail }}" alt="post-image" class="thumbnail_image">
							@else

								<div class="d-flex justify-content-center align-items-center flex-wrap">
									@foreach ($upload_file as $file)
										<img src="{{ \Storage::url($file) }}" alt="post-image" class="thumbnail_image"
										style="height: 120px;width:120px;">
									@endforeach
								</div>
							{{-- <img src="{{ \Storage::url($upload_file) }}" alt="post-image" class="thumbnail_image"> --}}
							@endif
						@else
							<div class="d-flex justify-content-center align-items-center flex-wrap" id="imageContainer"
									style="display:none">
								</div>
						{{-- <img src="{{ $thumbnail }}" alt="post-image" class="thumbnail_image" style="display: none;"> --}}
						@endif
					</div>
					<h4 class="mb-4 caption-text">{{ $caption ?? ""}}</h4>
					<h4 class="mb-4 hashtag-text">{{ $hashtag ?? "" }}</h4>
				</div>
			@else
				<div class="add-post-card p-4 mb-6 bg-white">
					<div class="head d-flex justify-content-between mb-6">
						<div class="d-flex justify-content-between align-items-center w-100">
							<div class="d-flex align-items-center">
								<div class="social-icon">
									<img src="{{ $value->image_url ?? asset('frontend/images/default_profile.jpg') }}"
										alt="social-icon">
								</div>
								<div class="author-detail ml-6">
									<h5 class="mb-2">{{ $value->page_name ?? ""}}</h5>
									<span class="text-muted schedule-date-text">{{
										\Carbon\Carbon::parse($schedule_date)->format('d
										M, Y')}}</span>
									<span class="text-muted schedule-time-text">{{ ' - '. $schedule_time }}</span>
								</div>
							</div>
							<div class="social-media-icon">
								<img src="{{ $value->media->image_url ?? asset('frontend/images/default_profile.jpg') }}"
									alt="social-icon">
							</div>
						</div>
					</div>
					<h4 class="mb-4 caption-text">{!! nl2br($caption) ?? "" !!}</h4>
					<h4 class="mb-4 hashtag-text">{{ $hashtag ?? "" }}</h4>

					<div class="add-post-image">
						@if(!empty($upload_file))
							@if(\File::extension(\Storage::url($upload_file[0] ?? "")) == 'mp4' )
							<div class="d-flex justify-content-center align-items-center flex-wrap" id="imageContainer">
								<img src="{{ $thumbnail }}" alt="social-icon" class="thumbnail_image" style="height: 120px;width:120px;">
							</div>

							@else
								<div class="d-flex justify-content-center align-items-center flex-wrap" id="imageContainer">
									@foreach ($upload_file as $file)
									<img src="{{ \Storage::url($file) }}" alt="social-icon" class="thumbnail_image my-4"
										style="height: 120px;width:120px;">
									@endforeach
								</div>
							@endif
						@else
							<div class="d-flex justify-content-center align-items-center flex-wrap" id="imageContainer"
								style="display:none">
							</div>
						@endif
					</div>
				</div>
			@endif
		@endforeach
	</div>
</div>
@endif
