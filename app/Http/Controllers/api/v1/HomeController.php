<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\PostMedia;
use App\User;
use Carbon\Carbon;
use App\Http\Resources\v1\PostListResource;

class HomeController extends Controller
{
    private $version = "v.1.0";
    public function getVersion()
    {
        return $this->version;
    }

    public function dashboard(Request $request)
    {
        $rules = [
            'start_date'     =>  'nullable|date',
            'end_date'       =>  'nullable|date|after_or_equal:start_date',
            'type'           =>  'required|in:posts,scheduled_posts',
            'limit'          =>  'nullable|gte:0',
            'offset'         =>  'nullable|gte:0',
        ];
        if ($this->apiValidator($request->all(), $rules)) {
            $user = $request->user();
            $parent_ids = null;
            $schedulePosts = collect([]);
            $posts = collect([]);
            $startDate = date("Y-m-d ", strtotime($request->start_date));
            $endDate = date("Y-m-d ", strtotime($request->end_date));
            $type = $request->type;

            try {

                $parent_id = $user->parent_id ?? "";

                if (!empty($parent_id)) {
                    $parent_user = User::where('id', $parent_id)->first();
                    $parent_ids = $parent_user->parents->pluck('id')->toArray();
                    $parent_ids[] = $parent_user->id;
                } else {
                    $parent_ids = $user->parents->pluck('id')->toArray();
                    $parent_ids[] = $user->id;;
                }

                $limit = ($request->limit) ? $request->limit : 4;
                $offset = ($request->offset) ? $request->offset : config('utility.pagination.offset');

                $posts = Post::where('is_active', 'y');
                $posts = $posts->whereIn('user_id', $parent_ids);


                $var = (Carbon::parse(now())->format('Y-m-d H:i:s'));

                if (($type == 'posts') && empty($request->start_date)  && empty($request->end_date)) {
                    // $posts = $posts->whereBetween('created_at',[Carbon::now()->subDays(7)->toDateString(), Carbon::parse(now())->toDateString()]);
                    $posts = $posts->where(function ($q) use ($var) {
                        $q->where('schedule_date_time', '<=', $var)->orWhere('schedule_date_time', null);
                    });
                    $posts = $posts->orderBy('created_at', 'desc');
                } elseif (($type == 'scheduled_posts') && empty($request->start_date)  && empty($request->end_date)) {
                    // $posts = $posts->whereBetween('schedule_date',[Carbon::parse(now())->toDateString(), Carbon::today()->addDays(7)]);
                    $posts = $posts->where(function ($q) use ($var) {
                        $q->where('schedule_date_time', '>=', $var);
                    });
                    $posts = $posts->orderBy('schedule_date_time', 'desc');
                } elseif ((!empty($request->start_date)  && !empty($request->end_date)) && $type == 'posts') {
                    $posts = $posts->whereBetween('created_at', [$startDate, $endDate]);
                    $posts = $posts->where(function ($q) use ($var) {
                        $q->where('schedule_date_time', '<=', $var)->orWhere('schedule_date_time', null);
                    });
                    $posts = $posts->orderBy('created_at', 'desc');
                } elseif (!empty($request->start_date)  && !empty($request->end_date) && $type == 'scheduled_posts') {
                    $posts = $posts->whereBetween('schedule_date', [$startDate, $endDate]);
                    $posts = $posts->where(function ($q) use ($var) {
                        $q->where('schedule_date_time', '>=', $var);
                    });
                    $posts = $posts->orderBy('schedule_date_time', 'desc');
                }
                $count = $posts->latest()->count();
                $posts = $posts->with('postMediaList')->with(['postMedia', 'images']);
                $posts = $posts->limit($limit)->offset($offset)->get();

                $dateOfLast30Day = \Carbon\Carbon::today()->subDays(30);
                $postsCount = Post::whereIn('user_id', $parent_ids)->where('created_at', '>=', $dateOfLast30Day)->whereNull('schedule_date_time')->count();
                // $postsCount = $postsCount->where(function ($q) use ($var) {
                //     $q->where('schedule_date_time', '<=', $var)->orWhere('schedule_date_time', null);
                // });
                // $scheduleCount = Post::whereIn('user_id', $parent_ids)->where('schedule_date_time', '>=', $var)->count();
                $scheduleCount = Post::whereIn('user_id', $parent_ids)->where('schedule_date_time', '<>', null)->count();
                return (new PostListResource($posts))
                    ->additional([
                        'meta' => [
                            'countPosts'  => $postsCount,
                            'countSchedules' => $scheduleCount,
                            'message' => trans('api.list', ['entity' => 'Post list']),
                            'count' => $count,
                        ]
                    ]);
                // $posts = Post::whereIn('user_id',$parent_ids)->whereNull('schedule_date_time')->orderBy('created_at', 'desc')->limit(4)->get();
                // $schedulePosts = Post::whereIn('user_id', $parent_ids)->whereNotNull('schedule_date_time')->orderBy('created_at', 'desc')->limit(4)->get();
            } catch (\Exception $ex) {
                $this->status = $this->statusArr['something_wrong'];
                $this->response['message'] = trans('api.error');
            }
            $this->response['meta']['api'] = $this->version;
            $this->response['meta']['url'] = url()->current();
        }
        return $this->return_response();
    }
}
