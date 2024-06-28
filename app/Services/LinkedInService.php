<?php

namespace App\Services;

use App\Models\MediaPage;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Analytics;
use App\Models\SocialMediaDetail;
use Illuminate\Support\Facades\Log;

class LinkedInService
{
    public function checkPage($social_ID, $token, $request, $media_page_id)
    {
        foreach ($media_page_id as $pageId) {
            if ($request['images'] !== null) {
                if ($pageId == $social_ID) {
                    $name = 'person';
                    self::linkedinPersonregisterUpload($pageId, $token, $request, $name);
                } else {
                    $name = 'organization';
                    self::linkedinregisterUpload($pageId, $token, $request, $name);
                }
            } else {
                if ($pageId == $social_ID) {
                    $name = 'person';
                } else {
                    $name = 'organization';
                }
                self::publishPost($pageId, $token, $request, null, null, $name);
            }
        }
    }
    public function linkedinregisterUpload($pageId, $token, $request, $name)
    {

        $files = $request['images'] ?? [];
        if ($files && count($files) > 1) {
            self::publishCourosalPost($pageId, $token, $request, $name);
        } else if ($files && count($files) == 1) {
            $image_type = isset($files) ?  File::extension($files[0]) : "";
            if ($image_type == "mp4") {
                $fileType = 'video';
            } else {
                $fileType = 'image';
            }
            $url = 'https://api.linkedin.com/v2/assets?action=registerUpload';
            $type = 'POST';
            $headers =  array(
                'Content-Type: application/json',
                'x-li-format: json',
                'X-Restli-Protocol-Version: 2.0.0',
                'Accept: application/json',
                'Authorization: Bearer ' . $token . ' ',
                'Cookie: lidc="b=VB39:s=V:r=V:a=V:p=V:g=3491:u=9:x=1:i=1664264192:t=1664342966:v=2:sig=AQGzZQ2tzrzDL75a9M7N67zTNJ5M-9cA"; bcookie="v=2&85655da9-8de9-46d8-81df-59920214fb38"'
            );

            $data = '{
            "registerUploadRequest": {
                "recipes": [
                    "urn:li:digitalmediaRecipe:feedshare-' . $fileType . '"
                ],
                 "owner":"urn:li:' . $name . ':' . $pageId . '",
                "serviceRelationships": [
                    {
                        "relationshipType": "OWNER",
                        "identifier": "urn:li:userGeneratedContent"
                    }
                ]
            }
            }';
            $response = fireCURL($url, $type, $headers, $data);
            if ($response && isset($response['value'])) {
                $upload_url = $response['value']['uploadMechanism']['com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest']['uploadUrl'];

                $asset = $response['value']['asset'];

                if ($asset && $upload_url) {

                    self::uploadVideo($pageId, $token, $request, $upload_url, $asset, $fileType, $name);
                } else return flash('Please try again uploadArticle registered failed')->error();
            }
        } else {
            self::publishPost($pageId, $token, $request, null, null, $name);
        }
    }
    public function linkedinPersonregisterUpload($pageId, $token, $request, $name)
    {
        $files = $request['images'] ?? [];
        if ($files && count($files) > 1) {
            self::publishCourosalPost($pageId, $token, $request, $name);
        } else if ($files && count($files) == 1) {
            $image_type = File::extension($request['images'][0]) ?? "";

            if ($image_type == "mp4") {
                $fileType = 'video';
            } else {
                $fileType = 'image';
            }
            $url = 'https://api.linkedin.com/v2/assets?action=registerUpload';
            $type = 'POST';
            $headers =  array(
                'Content-Type: application/json',
                'x-li-format: json',
                'X-Restli-Protocol-Version: 2.0.0',
                'Accept: application/json',
                'Authorization: Bearer ' . $token . ' ',
                'Cookie: lidc="b=VB39:s=V:r=V:a=V:p=V:g=3491:u=9:x=1:i=1664264192:t=1664342966:v=2:sig=AQGzZQ2tzrzDL75a9M7N67zTNJ5M-9cA"; bcookie="v=2&85655da9-8de9-46d8-81df-59920214fb38"'
            );

            $data = '{
            "registerUploadRequest": {
                "recipes": [
                    "urn:li:digitalmediaRecipe:feedshare-' . $fileType . '"
                ],
                 "owner":"urn:li:' . $name . ':' . $pageId . '",
                "serviceRelationships": [
                    {
                        "relationshipType": "OWNER",
                        "identifier": "urn:li:userGeneratedContent"
                    }
                ]
            }
        }';
            $response = fireCURL($url, $type, $headers, $data);
            if ($response && isset($response['value'])) {
                $upload_url = $response['value']['uploadMechanism']['com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest']['uploadUrl'];

                $asset = $response['value']['asset'];

                if ($asset && $upload_url) {

                    self::uploadVideo($pageId, $token, $request, $upload_url, $asset, $fileType, $name);
                } else return flash('Please try again uploadArticle registered failed')->error();
            }
        } else {
            self::publishPost($pageId, $token, $request, '', '', '', $name);
        }
    }

    public function uploadVideo($pageId, $token, $request, $upload_url, $asset, $fileType, $name)
    {
        $file = $request['images'][0] ?? [];
        $encdata = file_get_contents(Storage::url($file));
        $url = $upload_url;
        $type = 'POST';
        $headers =  array(
            'Authorization: Bearer ' . $token . ' ',
            'Content-Type: image/jpeg',
            'Cookie: lidc="b=VB39:s=V:r=V:a=V:p=V:g=3491:u=9:x=1:i=1664272469:t=1664342966:v=2:sig=AQFTlDtbNbhNB_YZUGqbftQYcCVvJ8a4"; bcookie="v=2&85655da9-8de9-46d8-81df-59920214fb38"; lang=v=2&lang=en-us'
        );

        $data = $encdata;

        $response = fireCURL($url, $type, $headers, $data);
        if ($response == null) self::publishPost($pageId, $token, $request, $asset, $fileType, $name);
        else return flash('Please try again uploadArticle')->error();
    }

    public function publishPost($pageId, $token, $request, $asset, $fileType, $name)
    {
        $text = [
            'text' => $request['caption'] .' '. $request['hashtag'],
        ];
        // dd(json_encode($text),'text');
        $url = 'https://api.linkedin.com/v2/ugcPosts';
        $type = 'POST';
        $headers =  array(
            'Content-Type: application/json',
            'x-li-format: json',
            'X-Restli-Protocol-Version: 2.0.0',
            'Accept: application/json',
            'Authorization: Bearer ' . $token . '',
            'Cookie: lidc="b=VB39:s=V:r=V:a=V:p=V:g=3491:u=9:x=1:i=1664273052:t=1664342966:v=2:sig=AQG0xuB2f1mUlkBe48IZv08hKKPZC-7t"; bcookie="v=2&85655da9-8de9-46d8-81df-59920214fb38"; lang=v=2&lang=en-us'
        );
        if ($request['images'] != null) {
            $fileType = ($fileType == 'image') ? 'IMAGE' : 'VIDEO';
            $data = '{
                "author": "urn:li:' . $name . ':' . $pageId . '",
                "lifecycleState": "PUBLISHED",
                "specificContent": {
                    "com.linkedin.ugc.ShareContent": {
                        "shareCommentary": '.json_encode($text).',
                        "shareMediaCategory": "' . $fileType . '",
                        "media": [
                            {
                                "status": "READY",
                                "description": {
                                    "text": "Center stage!"
                                },
                                "media": "' . $asset . '",
                                "title": {
                                    "text": "' . $request['hashtag'] . '"
                                }
                            }
                        ]
                    }
                },
                "visibility": {
                    "com.linkedin.ugc.MemberNetworkVisibility": "PUBLIC"
                }
            }';
        } else {
            $data = '{
                "author": "urn:li:' . $name . ':' . $pageId . '",
                "lifecycleState": "PUBLISHED",
                "specificContent": {
                    "com.linkedin.ugc.ShareContent": {
                        "shareCommentary": '.json_encode($text).',
                        "shareMediaCategory": "NONE"
                    }
                },
                "visibility": {
                    "com.linkedin.ugc.MemberNetworkVisibility": "PUBLIC"
                }
            }';
        }
        $response = fireCURL($url, $type, $headers, $data);
        if ($response) return $response;
        else return flash('Please try after sometime published post failed')->error();
    }

    public function publishCourosalPost($pageId, $token, $request, $name)
    {
        $text = [
            'text' => $request['caption'] .' '. $request['hashtag'],
        ];

        $image_files = $request['images'] ?? [];
        $url = 'https://api.linkedin.com/v2/ugcPosts';
        $type = 'POST';
        $headers =  array(
            'Content-Type: application/json',
            'x-li-format: json',
            'X-Restli-Protocol-Version: 2.0.0',
            'Accept: application/json',
            'Authorization: Bearer ' . $token . '',
            'Cookie: lidc="b=VB39:s=V:r=V:a=V:p=V:g=3491:u=9:x=1:i=1664273052:t=1664342966:v=2:sig=AQG0xuB2f1mUlkBe48IZv08hKKPZC-7t"; bcookie="v=2&85655da9-8de9-46d8-81df-59920214fb38"; lang=v=2&lang=en-us'
        );
        $uploaded_Image_data = self::getUploadedImageData($image_files, $token, $name, $pageId);
        $data = '{
            "author": "urn:li:' . $name . ':' . $pageId . '",
            "lifecycleState": "PUBLISHED",
            "specificContent": {
                "com.linkedin.ugc.ShareContent": {
                    "shareCommentary": '.json_encode($text).',
                    "shareMediaCategory": "IMAGE",
                    "media": [
                        ' . $uploaded_Image_data . '
                    ]
                }
            },
            "visibility": {
                "com.linkedin.ugc.MemberNetworkVisibility": "PUBLIC"
            }
        }';
        // dd($data);
        $response = fireCURL($url, $type, $headers, $data);
        Log::debug(json_encode($response));
        if ($response) return $response;
        else return flash('Please try after sometime published post failed')->error();
    }

    public function getUploadedImageData($images, $token, $name, $pageId): string
    {
        $imageData = [];
        foreach ($images as $image) {
            $url = 'https://api.linkedin.com/v2/assets?action=registerUpload';
            $type = 'POST';
            $headers =  array(
                'Content-Type: application/json',
                'x-li-format: json',
                'X-Restli-Protocol-Version: 2.0.0',
                'Accept: application/json',
                'Authorization: Bearer ' . $token . '',
                'Cookie: lidc="b=VB39:s=V:r=V:a=V:p=V:g=3491:u=9:x=1:i=1664273052:t=1664342966:v=2:sig=AQG0xuB2f1mUlkBe48IZv08hKKPZC-7t"; bcookie="v=2&85655da9-8de9-46d8-81df-59920214fb38"; lang=v=2&lang=en-us'
            );
            $data = '{
                "registerUploadRequest":{
                    "owner":"urn:li:' . $name . ':' . $pageId . '",
                    "recipes":[
                        "urn:li:digitalmediaRecipe:feedshare-image"
                    ],
                    "serviceRelationships":[
                        {
                            "identifier":"urn:li:userGeneratedContent",
                            "relationshipType":"OWNER"
                        }
                    ],
                    "supportedUploadMechanism":[
                        "SYNCHRONOUS_UPLOAD"
                    ]
                }
            }';
            $response = fireCURL($url, $type, $headers, $data);
            // dd($response);
            $image_upload_url = isset($response['value']) ? $response['value']['uploadMechanism']['com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest']['uploadUrl'] : '';
            $imageURN = isset($response['value']) ? $response['value']['asset'] : '';
            $image_url = Storage::url($image);
            $uploadedImage = self::uploadImage($image_upload_url, $image_url, $token);
            if ($uploadedImage) {
                $imageData[] = '{
                    "status": "READY",
                    "media": "' . $imageURN . '",
                    "title": {
                        "text": ""
                    }
                }';
            }
        }

        return implode(',', $imageData);
    }

    public function uploadImage($imageUploadUrl, $image_url, $token): bool
    {
        $url = $imageUploadUrl;
        $type = 'POST';
        $headers =  array(
            'Authorization: Bearer ' . $token . ' ',
            'Content-Type: image/jpeg',
            'Cookie: lidc="b=VB39:s=V:r=V:a=V:p=V:g=3491:u=9:x=1:i=1664272469:t=1664342966:v=2:sig=AQFTlDtbNbhNB_YZUGqbftQYcCVvJ8a4"; bcookie="v=2&85655da9-8de9-46d8-81df-59920214fb38"; lang=v=2&lang=en-us'
        );
        // Get Image Data
        $data = file_get_contents($image_url);
        $response = fireCURL($url, $type, $headers, $data);
        return $response == null ? true : false;
    }

    public function prepareType($social_id)
    {

        $page_id = (request()->page_id && request()->page_id != 'all') ? explode(',', request()->page_id) : null;
        $user = auth()->user()->parentUser ?? auth()->user();

        $mediaPagesArr = MediaPage::where([
            'user_id' => $user->id,
            'media_id' => 2
        ])->whereHas('userMediaPages', function ($q) use ($user) {
            $q->where('user_id', $user->id);
            $q->where('is_deleted', 'n');
        })->when($page_id, function ($query) use ($page_id) {
            $query->whereIn('id', $page_id);
        })->pluck('page_id')->toArray();

        return $mediaPagesArr;
        // $media_page = MediaPage::where('id', $page_id)->first();
        // if(isset($media_page->id)){
        //     if($media_page->page_id == $social_id){
        //         return 'all';
        //     }else{
        //         return $media_page->page_id;
        //     }
        // }else{
        //     return 'all';
        // }

    }

    public function getOrgIds($social_id)
    {
        $host = 'https://api.linkedin.com/rest/';
        $orgID = [];

        $pageId = self::prepareType($social_id);

        /**
         * check page ids available or not
         * if not found then call api and get ids
         * */
        if (empty($pageId)) {
            $url = 'https://api.linkedin.com/v2/organizationAcls?q=roleAssignee';
            $type = 'GET';
            $headers = array(
                'Authorization: Bearer ' . $token . '',
                'Cookie: lidc="b=VB39:s=V:r=V:a=V:p=V:g=3491:u=11:x=1:i=1664445440:t=1664453856:v=2:sig=AQFjPaItq05VsgdPzJxozvfVeQYoLymL"; bcookie="v=2&85655da9-8de9-46d8-81df-59920214fb38"'
            );

            $response = fireCURL($url, $type, $headers);

            $organizationID = [];

            if (isset($response['elements'])) {
                foreach ($response['elements'] as $values) {
                    $organizationID[] = $values['organization'];
                }
            }

            foreach ($organizationID as $value) {
                $id = explode(":", $value);
                $orgID[] = $id[3];
            }
        }

        $orgID = $pageId;

        return $orgID;
    }

    public function getFollowerData($social_id, $token)
    {
        $host = 'https://api.linkedin.com/rest/';
        //Followers
        $followdata = [];

        $orgID = self::getOrgIds($social_id);
        // dd($orgID);
        foreach ($orgID as $ID) {
            // Followers Statistics by Different areas
            $url = $host . 'organizationalEntityFollowerStatistics?q=organizationalEntity&organizationalEntity=' . urlencode('urn:li:organization:' . $ID);
            $type = 'GET';
            $headers = array(
                'Content-Type: application/json',
                'x-li-format: json',
                'X-Restli-Protocol-Version: 2.0.0',
                'Accept: application/json',
                'LinkedIn-Version: 202210',
                'Authorization: Bearer ' . $token . ' ',
                'Cookie: lidc="b=VB39:s=V:r=V:a=V:p=V:g=3491:u=9:x=1:i=1664264192:t=1664342966:v=2:sig=AQGzZQ2tzrzDL75a9M7N67zTNJ5M-9cA"; bcookie="v=2&85655da9-8de9-46d8-81df-59920214fb38"'
            );

            $response = fireCURL($url, $type, $headers);
            if (isset($response['elements']) && count($response['elements']) > 0) {
                $followdata[] = $response['elements'];
            }
        }
        return [
            'followdata' => $followdata
        ];
    }

    public function getTotalFollowerData($social_id, $token, $orgID = [])
    {
        $host = 'https://api.linkedin.com/rest/';
        //Followers
        $totalFollowerData = [];

        // dd($orgID);
        foreach ($orgID as $ID) {

            $url = 'https://api.linkedin.com/rest/networkSizes/' . urlencode('urn:li:organization:' . $ID) . '?edgeType=CompanyFollowedByMember';
            $type = 'GET';
            $headers = array(
                'Content-Type: application/json',
                'x-li-format: json',
                'X-Restli-Protocol-Version: 2.0.0',
                'Accept: application/json',
                'LinkedIn-Version: 202210',
                'Authorization: Bearer ' . $token . ' ',
                'Cookie: lidc="b=VB39:s=V:r=V:a=V:p=V:g=3491:u=9:x=1:i=1664264192:t=1664342966:v=2:sig=AQGzZQ2tzrzDL75a9M7N67zTNJ5M-9cA"; bcookie="v=2&85655da9-8de9-46d8-81df-59920214fb38"'
            );

            $response = fireCURL($url, $type, $headers);
            // if (isset($response['firstDegreeSize']) && count($response['elements']) > 0) {
            if (isset($response['firstDegreeSize'])) {
                $totalFollowerData[] = $response['firstDegreeSize'];
            }
        }
        return [
            'totalFollowerData' => $totalFollowerData
        ];
    }

    public function getTimeFollowData($social_id, $token, $orgID = [], $start, $end)
    {
        $host = 'https://api.linkedin.com/rest/';
        //Followers
        $timefollowdata = [];
        // dd($orgID);
        foreach ($orgID as $ID) {

            $url = $host . 'organizationalEntityFollowerStatistics?q=organizationalEntity&organizationalEntity=' . urlencode('urn:li:organization:' . $ID) . '&timeIntervals=(timeRange:(start:' . $start . ',end:' . $end . '),timeGranularityType:DAY)';
            $type = 'GET';
            $headers = array(
                'Content-Type: application/json',
                'x-li-format: json',
                'X-Restli-Protocol-Version: 2.0.0',
                'Accept: application/json',
                'LinkedIn-Version: 202210',
                'Authorization: Bearer ' . $token . ' ',
                'Cookie: lidc="b=VB39:s=V:r=V:a=V:p=V:g=3491:u=9:x=1:i=1664264192:t=1664342966:v=2:sig=AQGzZQ2tzrzDL75a9M7N67zTNJ5M-9cA"; bcookie="v=2&85655da9-8de9-46d8-81df-59920214fb38"'
            );

            $response = fireCURL($url, $type, $headers);
            if (isset($response['elements']) && count($response['elements']) > 0) {
                $timefollowdata[] = $response['elements'];
            }
        }
        return [
            'timefollowdata' => $timefollowdata
        ];
    }

    public function getClickData($social_id, $token, $orgID = [], $start, $end)
    {
        $host = 'https://api.linkedin.com/rest/';
        //Followers
        $clickdata = [];

        // dd($orgID);
        foreach ($orgID as $ID) {

            //Click

            $url = $host . 'organizationalEntityShareStatistics?q=organizationalEntity&organizationalEntity=' . urlencode('urn:li:organization:' . $ID) . '&timeIntervals=(timeRange:(start:' . $start . ',end:' . $end . '),timeGranularityType:DAY)';
            // $url = 'https://api.linkedin.com/rest/organizationPageStatistics?q=organization&organization=' . urlencode('urn:li:organization:' . $ID) . '&timeIntervals.timeGranularityType=DAY&timeIntervals.timeRange.start=' . $start . '&timeIntervals.timeRange.end=' . $end . '';
            $type = 'GET';
            $headers = array(
                'Content-Type: application/json',
                'x-li-format: json',
                'X-Restli-Protocol-Version: 2.0.0',
                'Accept: application/json',
                'LinkedIn-Version: 202210',
                'Authorization: Bearer ' . $token . ' ',
                'Cookie: lidc="b=VB39:s=V:r=V:a=V:p=V:g=3491:u=9:x=1:i=1664264192:t=1664342966:v=2:sig=AQGzZQ2tzrzDL75a9M7N67zTNJ5M-9cA"; bcookie="v=2&85655da9-8de9-46d8-81df-59920214fb38"'
            );

            $response = fireCURL($url, $type, $headers);
            // dd('click',$response);
            if (isset($response['elements']) && count($response['elements']) > 0) {
                $clickdata[] = $response['elements'];
            }
        }
        return [
            'clickdata' => $clickdata
        ];
    }

    public function getEnagagmentData($social_id, $token, $start, $end)
    {

        $host = 'https://api.linkedin.com/rest/';
        //Followers
        $followdata = [];
        $totalFollowerData = [];
        $timefollowdata = [];
        $clickdata = [];
        $orgID = [];

        $pageId = self::prepareType($social_id);

        /**
         * check page ids available or not
         * if not found then call api and get ids
         * */
        if (empty($pageId)) {
            $url = 'https://api.linkedin.com/v2/organizationAcls?q=roleAssignee';
            $type = 'GET';
            $headers = array(
                'Authorization: Bearer ' . $token . '',
                'Cookie: lidc="b=VB39:s=V:r=V:a=V:p=V:g=3491:u=11:x=1:i=1664445440:t=1664453856:v=2:sig=AQFjPaItq05VsgdPzJxozvfVeQYoLymL"; bcookie="v=2&85655da9-8de9-46d8-81df-59920214fb38"'
            );

            $response = fireCURL($url, $type, $headers);

            $organizationID = [];

            if (isset($response['elements'])) {
                foreach ($response['elements'] as $values) {
                    $organizationID[] = $values['organization'];
                }
            }

            foreach ($organizationID as $value) {
                $id = explode(":", $value);
                $orgID[] = $id[3];
            }
        }

        $orgID = $pageId;
        // dd($orgID);
        foreach ($orgID as $ID) {

            //Total Follwer
            // $url = $host . 'organizationalEntityFollowerStatistics?q=organizationalEntity&organizationalEntity=' . urlencode('urn:li:organization:' . $ID);
            $url = 'https://api.linkedin.com/rest/networkSizes/' . urlencode('urn:li:organization:' . $ID) . '?edgeType=CompanyFollowedByMember';
            $type = 'GET';
            $headers = array(
                'Content-Type: application/json',
                'x-li-format: json',
                'X-Restli-Protocol-Version: 2.0.0',
                'Accept: application/json',
                'LinkedIn-Version: 202210',
                'Authorization: Bearer ' . $token . ' ',
                'Cookie: lidc="b=VB39:s=V:r=V:a=V:p=V:g=3491:u=9:x=1:i=1664264192:t=1664342966:v=2:sig=AQGzZQ2tzrzDL75a9M7N67zTNJ5M-9cA"; bcookie="v=2&85655da9-8de9-46d8-81df-59920214fb38"'
            );

            $response = fireCURL($url, $type, $headers);
            // dd($response);
            // if (isset($response['firstDegreeSize']) && count($response['elements']) > 0) {
            if (isset($response['firstDegreeSize'])) {
                $totalFollowerData[] = $response['firstDegreeSize'];
            }
            // Followers Statistics by Different areas
            $url = $host . 'organizationalEntityFollowerStatistics?q=organizationalEntity&organizationalEntity=' . urlencode('urn:li:organization:' . $ID);
            $type = 'GET';
            $headers = array(
                'Content-Type: application/json',
                'x-li-format: json',
                'X-Restli-Protocol-Version: 2.0.0',
                'Accept: application/json',
                'LinkedIn-Version: 202210',
                'Authorization: Bearer ' . $token . ' ',
                'Cookie: lidc="b=VB39:s=V:r=V:a=V:p=V:g=3491:u=9:x=1:i=1664264192:t=1664342966:v=2:sig=AQGzZQ2tzrzDL75a9M7N67zTNJ5M-9cA"; bcookie="v=2&85655da9-8de9-46d8-81df-59920214fb38"'
            );

            $response = fireCURL($url, $type, $headers);
            if (isset($response['elements']) && count($response['elements']) > 0) {
                $followdata[] = $response['elements'];
            }

            //Follower by time
            $url = $host . 'organizationalEntityFollowerStatistics?q=organizationalEntity&organizationalEntity=' . urlencode('urn:li:organization:' . $ID) . '&timeIntervals=(timeRange:(start:' . $start . ',end:' . $end . '),timeGranularityType:DAY)';
            $type = 'GET';
            $headers = array(
                'Content-Type: application/json',
                'x-li-format: json',
                'X-Restli-Protocol-Version: 2.0.0',
                'Accept: application/json',
                'LinkedIn-Version: 202210',
                'Authorization: Bearer ' . $token . ' ',
                'Cookie: lidc="b=VB39:s=V:r=V:a=V:p=V:g=3491:u=9:x=1:i=1664264192:t=1664342966:v=2:sig=AQGzZQ2tzrzDL75a9M7N67zTNJ5M-9cA"; bcookie="v=2&85655da9-8de9-46d8-81df-59920214fb38"'
            );

            $response = fireCURL($url, $type, $headers);
            if (isset($response['elements']) && count($response['elements']) > 0) {
                $timefollowdata[] = $response['elements'];
            }
            //Click

            $url = $host . 'organizationalEntityShareStatistics?q=organizationalEntity&organizationalEntity=' . urlencode('urn:li:organization:' . $ID) . '&timeIntervals=(timeRange:(start:' . $start . ',end:' . $end . '),timeGranularityType:DAY)';
            // $url = 'https://api.linkedin.com/rest/organizationPageStatistics?q=organization&organization=' . urlencode('urn:li:organization:' . $ID) . '&timeIntervals.timeGranularityType=DAY&timeIntervals.timeRange.start=' . $start . '&timeIntervals.timeRange.end=' . $end . '';
            $type = 'GET';
            $headers = array(
                'Content-Type: application/json',
                'x-li-format: json',
                'X-Restli-Protocol-Version: 2.0.0',
                'Accept: application/json',
                'LinkedIn-Version: 202210',
                'Authorization: Bearer ' . $token . ' ',
                'Cookie: lidc="b=VB39:s=V:r=V:a=V:p=V:g=3491:u=9:x=1:i=1664264192:t=1664342966:v=2:sig=AQGzZQ2tzrzDL75a9M7N67zTNJ5M-9cA"; bcookie="v=2&85655da9-8de9-46d8-81df-59920214fb38"'
            );

            $response = fireCURL($url, $type, $headers);
            // dd('click',$response);
            if (isset($response['elements']) && count($response['elements']) > 0) {
                $clickdata[] = $response['elements'];
            }
        }
        $posts = Self::userPost($social_id, $token, $start, $end);
        $getSocialActionsData = Self::getSocialActionsData($social_id, $token, $start, $end);
        $geoData = Self::getGeodata($followdata, $token);
        $getSenioritdata = Self::getSenioritdata($followdata, $token);
        $getFunctiondata = Self::getFunctiondata($followdata, $token);
        $getIndustryData = Self::getIndustryData($followdata, $token);
        return [
            'totalFollowerData' => $totalFollowerData,
            'timefollowdata' => $timefollowdata,
            'followdata' => $followdata,
            'clickdata' => $clickdata,
            'geoData' => $geoData,
            'seniorityData' => $getSenioritdata,
            'functionData' => $getFunctiondata,
            'industryData' => $getIndustryData,
            'socialActionData' => $getSocialActionsData,
            'posts' => $posts
        ];
    }

    public function getGeodata($linkedin_follower, $token)
    {
        $geo = [];
        $location = [];
        foreach ($linkedin_follower as $lf) {
            foreach ($lf as $value) {
                if (isset($value['followerCountsByGeoCountry'])) {
                    foreach ($value['followerCountsByGeoCountry'] as $fc) {
                        $geo[] = $fc['geo'];
                    }
                }
            }
        }

        if (count($geo) > 0) {
            $ids = [];
            foreach ($geo as $val) {
                $arr = explode(':', $val);
                if (isset($arr[count($arr) - 1])) {
                    $ids[] = $arr[count($arr) - 1];
                }
            }

            if (count($ids) > 0) {
                $ids = implode(',', $ids);
                //Follower by time
                $url = 'https://api.linkedin.com/v2/geo?ids=List(' . $ids . ')';
                $type = 'GET';
                $headers = array(
                    'Content-Type: application/json',
                    'x-li-format: json',
                    'X-Restli-Protocol-Version: 2.0.0',
                    'Accept: application/json',
                    'LinkedIn-Version: 202210',
                    'Authorization: Bearer ' . $token . ' ',
                    'Cookie: lidc="b=VB39:s=V:r=V:a=V:p=V:g=3491:u=9:x=1:i=1664264192:t=1664342966:v=2:sig=AQGzZQ2tzrzDL75a9M7N67zTNJ5M-9cA"; bcookie="v=2&85655da9-8de9-46d8-81df-59920214fb38"'
                );

                $response = fireCURL($url, $type, $headers);
                if (isset($response['results']) && count($response['results']) > 0) {

                    foreach ($response['results'] as $res) {
                        if (isset($res['defaultLocalizedName']['value']) && isset($res['id'])) {
                            $location['urn:li:geo:' . $res['id']] = $res['defaultLocalizedName']['value'];
                        }
                    }
                }
            }
        }
        return $location;
    }

    public function getSenioritdata($linkedin_follower, $token)
    {

        $seniority = [];
        $sen = [];
        foreach ($linkedin_follower as $lf) {
            foreach ($lf as $value) {
                if (isset($value['followerCountsBySeniority'])) {
                    foreach ($value['followerCountsBySeniority'] as $fc) {
                        $seniority[] = $fc['seniority'];
                    }
                }
            }
        }

        if (count($seniority) > 0) {
            foreach ($seniority as $val) {
                $arr = explode(':', $val);
                if (isset($arr[count($arr) - 1])) {
                    $url = 'https://api.linkedin.com/v2/seniorities/' . $arr[count($arr) - 1];
                    $type = 'GET';
                    $headers = array(
                        'Content-Type: application/json',
                        'x-li-format: json',
                        'X-Restli-Protocol-Version: 2.0.0',
                        'Accept: application/json',
                        'LinkedIn-Version: 202210',
                        'Authorization: Bearer ' . $token . ' ',
                        'Cookie: lidc="b=VB39:s=V:r=V:a=V:p=V:g=3491:u=9:x=1:i=1664264192:t=1664342966:v=2:sig=AQGzZQ2tzrzDL75a9M7N67zTNJ5M-9cA"; bcookie="v=2&85655da9-8de9-46d8-81df-59920214fb38"'
                    );

                    $response = fireCURL($url, $type, $headers);
                    if (isset($response['id']) && isset($response['name']['localized']['en_US'])) {
                        $sen['urn:li:seniority:' . $response['id']] =  $response['name']['localized']['en_US'];
                    }
                }
            }
            return $sen;
        }

        return [];
    }

    public function getFunctiondata($linkedin_follower, $token)
    {

        $function = [];
        $sen = [];
        foreach ($linkedin_follower as $lf) {
            foreach ($lf as $value) {
                // dump($value);
                if (isset($value['followerCountsByFunction'])) {
                    $function = array_column($value['followerCountsByFunction'], 'function');
                    // foreach ($value['followerCountsByFunction'] as $fc) {
                    //     $function[] = $fc['function'];
                    // }
                }
            }
        }
        if (count($function) > 0) {
            foreach ($function as $val) {
                $arr = explode(':', $val);
                if (isset($arr[count($arr) - 1])) {
                    $url = 'https://api.linkedin.com/v2/functions/' . $arr[count($arr) - 1];
                    $type = 'GET';
                    $headers = array(
                        'Content-Type: application/json',
                        'x-li-format: json',
                        'X-Restli-Protocol-Version: 2.0.0',
                        'Accept: application/json',
                        'LinkedIn-Version: 202210',
                        'Authorization: Bearer ' . $token . ' ',
                        'Cookie: lidc="b=VB39:s=V:r=V:a=V:p=V:g=3491:u=9:x=1:i=1664264192:t=1664342966:v=2:sig=AQGzZQ2tzrzDL75a9M7N67zTNJ5M-9cA"; bcookie="v=2&85655da9-8de9-46d8-81df-59920214fb38"'
                    );

                    $response = fireCURL($url, $type, $headers);
                    // dump($response);
                    if (isset($response['id']) && isset($response['name']['localized']['en_US'])) {
                        $sen['urn:li:function:' . $response['id']] =  $response['name']['localized']['en_US'];
                    }
                }
            }
            return $sen;
        }

        return [];
    }
    public function getIndustryData($linkedin_follower, $token)
    {

        $function = [];
        $sen = [];
        foreach ($linkedin_follower as $lf) {
            foreach ($lf as $value) {
                if (isset($value['followerCountsByIndustry'])) {
                    foreach ($value['followerCountsByIndustry'] as $fc) {
                        $function[] = $fc['industry'];
                    }
                }
            }
        }

        if (count($function) > 0) {
            foreach ($function as $val) {
                $arr = explode(':', $val);
                if (isset($arr[count($arr) - 1])) {
                    $url = 'https://api.linkedin.com/v2/industryTaxonomyVersions/DEFAULT/industries/' . $arr[count($arr) - 1];
                    $type = 'GET';
                    $headers = array(
                        'Content-Type: application/json',
                        'x-li-format: json',
                        'X-Restli-Protocol-Version: 2.0.0',
                        'Accept: application/json',
                        'LinkedIn-Version: 202210',
                        'Authorization: Bearer ' . $token . ' ',
                        'Cookie: lidc="b=VB39:s=V:r=V:a=V:p=V:g=3491:u=9:x=1:i=1664264192:t=1664342966:v=2:sig=AQGzZQ2tzrzDL75a9M7N67zTNJ5M-9cA"; bcookie="v=2&85655da9-8de9-46d8-81df-59920214fb38"'
                    );

                    $response = fireCURL($url, $type, $headers);
                    // dump($response);
                    if (isset($response['id']) && isset($response['name']['localized']['en_US'])) {
                        $sen['urn:li:industry:' . $response['id']] =  $response['name']['localized']['en_US'];
                    }
                }
            }
            // dd('out');
            return $sen;
        }

        return [];
    }

    public function userPost($social_id, $token, $start, $end, $orgIds = [])
    {
        // try {
        //     $posts = [];


        //     if (!empty($orgIds)) {
        //         $page_ids = $orgIds;
        //     } else {
        //         $page_ids = self::prepareType($social_id);
        //     }

        //     $media_pages = MediaPage::whereIn('page_id', $page_ids)->where('media_id', 2)->pluck('page_id')->toArray();

        //     $host = 'https://api.linkedin.com/v2/';

        //     $headers = array(
        //         'Content-Type: application/json',
        //         'x-li-format: json',
        //         'X-Restli-Protocol-Version: 2.0.0',
        //         'Accept: application/json',
        //         'LinkedIn-Version: 202210',
        //         'Authorization: Bearer ' . $token . ' ',
        //         'Cookie: lidc="b=VB39:s=V:r=V:a=V:p=V:g=3491:u=9:x=1:i=1664264192:t=1664342966:v=2:sig=AQGzZQ2tzrzDL75a9M7N67zTNJ5M-9cA"; bcookie="v=2&85655da9-8de9-46d8-81df-59920214fb38"'
        //     );
        //     foreach ($media_pages as $ID) {
        //         $url = $host . 'ugcPosts?q=authors&authors=List(' . urlencode('urn:li:organization:' . $ID) . ')&count=100';
        //         $type = 'GET';
        //         $response = fireCURL($url, $type, $headers);
        //         $postsResponse = isset($response['elements']) ? $response['elements'] : [];
        //         foreach ($postsResponse as $key => $post) {
        //             $post_created_timestamp = $post['created']['time'];
        //             if ($post_created_timestamp >= $start && $post_created_timestamp <= $end) {
        //                 // Get Post Created Date
        //                 $posts[$ID][$key]['created_date'] = Carbon::parse($post['created']['time'] / 1000)->format('j M, Y');

        //                 // Get Post Account Id
        //                 $posts[$ID][$key]['account_url'] = 'https://www.linkedin.com/company/' . $ID;
        //                 $posts[$ID][$key]['account_id'] = $ID;

        //                 // Get Account Name
        //                 $url = 'https://api.linkedin.com/rest/organizations/' . $ID;
        //                 $response = fireCURL($url, $type, $headers);

        //                 $posts[$ID][$key]['account_name'] = $response['localizedName'];
        //                 $posts[$ID][$key]['account_url_name'] = $response['vanityName'];

        //                 // Get Posts Text
        //                 $posts[$ID][$key]['content'] = $post['specificContent']['com.linkedin.ugc.ShareContent']['shareCommentary']['text'];

        //                 // Get Posts Id
        //                 $posts[$ID][$key]['id'] = $post['id'];

        //                 // // get Post Media
        //                 $posts[$ID][$key]['image_url'] = '';
        //                 if (isset($post['specificContent']['com.linkedin.ugc.ShareContent']['media'][0]['originalUrl'])) {
        //                     $posts[$ID][$key]['image_url'] = $post['specificContent']['com.linkedin.ugc.ShareContent']['media'][0]['originalUrl'];
        //                 }


        //                 // // Get Hash Tags
        //                 $tags = $post['specificContent']['com.linkedin.ugc.ShareContent']['shareFeatures']['hashtags'];
        //                 $hash_tags = [];
        //                 foreach ($tags as $tag) {
        //                     $hash_tags[] = '#' . explode(":", $tag)[3];
        //                 }
        //                 $posts[$ID][$key]['hash_tags'] = implode(' ', $hash_tags);

        //                 // Get Posts Likes
        //                 $url = $host . 'socialActions/' . urlencode($post['id']) . '';
        //                 $response = fireCURL($url, $type, $headers);
        //                 $posts[$ID][$key]['likes_count'] = isset($response['likesSummary'])  ? $response['likesSummary']['totalLikes'] ?? 0 : 0;

        //                 // Only Get the First Level Comment count, Change if you need.
        //                 $posts[$ID][$key]['comment_count'] = isset($response['commentsSummary'])  ? $response['commentsSummary']['totalFirstLevelComments'] ?? 0 : 0;


        //                 $posts[$ID][$key]['url'] = 'https://www.linkedin.com/feed/update/' . $post['id'];
        //             }
        //         }
        //     }
        //     return $posts;
        // } catch (\Throwable $th) {
        //     return false;
        // }
        try {
            $posts = [];


            if (!empty($orgIds)) {
                $page_ids = $orgIds;
            } else {
                $page_ids = self::prepareType($social_id);
            }

            $media_pages = MediaPage::whereIn('page_id', $page_ids)->where('media_id', 2)->pluck('page_id')->toArray();

            $host = 'https://api.linkedin.com/v2/';

            $headers = array(
                'Content-Type: application/json',
                'x-li-format: json',
                'Accept: application/json',
                'LinkedIn-Version: 202210',
                'Authorization: Bearer ' . $token . ' ',
                'Cookie: lidc="b=VB39:s=V:r=V:a=V:p=V:g=3491:u=9:x=1:i=1664264192:t=1664342966:v=2:sig=AQGzZQ2tzrzDL75a9M7N67zTNJ5M-9cA"; bcookie="v=2&85655da9-8de9-46d8-81df-59920214fb38"'
            );
            foreach ($page_ids as $parent_key =>  $ID) {
                // $url = $host . 'ugcPosts?q=authors&authors=List(' . urlencode('urn:li:organization:' . $ID) . ')&count=100';
                $url = 'https://api.linkedin.com/rest/posts?author=' . urlencode('urn:li:organization:' . $ID) . '&q=author&count=10';
                $type = 'GET';
                $response = fireCURL($url, $type, $headers);
                $postsResponse = isset($response['elements']) ? $response['elements'] : [];
                // dd($postsResponse);
                foreach ($postsResponse as $key => $post) {
                    // if ($key < 15 && $parent_key !== 0) dump($post);
                    $post_created_timestamp = $post['createdAt'];
                    if ($post_created_timestamp >= $start && $post_created_timestamp <= $end) {
                        // Get Post Created Date
                        $posts[$ID][$key]['created_date'] = Carbon::parse($post['createdAt'] / 1000)->format('j M, Y');

                        // Get Post Account Id
                        $posts[$ID][$key]['account_url'] = 'https://www.linkedin.com/company/' . $ID;
                        $posts[$ID][$key]['account_id'] = $ID;

                        // Get Account Name
                        $url = 'https://api.linkedin.com/rest/organizations/' . $ID;
                        $response = fireCURL($url, $type, $headers);

                        $posts[$ID][$key]['account_name'] = $response['localizedName'];
                        $posts[$ID][$key]['account_url_name'] = $response['vanityName'];

                        // Get Posts Text
                        $posts[$ID][$key]['content'] = $post['commentary'];

                        // Get Posts Id
                        $posts[$ID][$key]['id'] = $post['id'];

                        // // get Post Media

                        $posts[$ID][$key]['image_url'] = '';
                        if (isset($post['content']['media'])) {
                            $mediaType = explode(':', $post['content']['media']['id'])[2];
                            if ($mediaType == 'image') {
                                $url = 'https://api.linkedin.com/rest/images/' . $post['content']['media']['id'];
                                $type = 'GET';
                                $response = fireCURL($url, $type, $headers);
                                $imageResponse = isset($response['status'])  && $response['status'] == 'AVAILABLE' ? $response : [];
                                $posts[$ID][$key]['image_url'] = $imageResponse ? $imageResponse['downloadUrl'] : '';
                            } else if ($mediaType == 'video') {
                                $url = 'https://api.linkedin.com/rest/videos/' . $post['content']['media']['id'];
                                $type = 'GET';
                                $response = fireCURL($url, $type, $headers);
                                $videoResponse = isset($response['status']) && $response['status'] == 'AVAILABLE' ? $response : [];
                                $posts[$ID][$key]['image_url'] = $videoResponse ?  $videoResponse['thumbnail'] : '';
                            }
                        } else if ($post['content']['multiImage']) {
                            $image_id = $post['content']['multiImage']['images'][0]['id'];
                            $url = 'https://api.linkedin.com/rest/images/' . $image_id;
                            $type = 'GET';
                            $response = fireCURL($url, $type, $headers);
                            $imageResponse = isset($response['status'])  && $response['status'] == 'AVAILABLE' ? $response : [];

                            $posts[$ID][$key]['image_url'] = $imageResponse ? $imageResponse['downloadUrl'] : '';
                        }
                        $tags = explode(' ', substr($post['commentary'], strpos($post['commentary'], '{hashtag')));
                        $hash_tags = [];
                        foreach ($tags as $tag) {
                            // {hashtag|#|namehashtag}
                            $hash_tags[] = ' ' . str_replace(['|', '}', '{'], '', substr($tag, strrpos($tag, '#')));
                        }
                        $posts[$ID][$key]['hash_tags'] = $hash_tags;

                        // Get Posts Likes
                        $url = $host . 'socialActions/' . urlencode($post['id']) . '';
                        $response = fireCURL($url, $type, $headers);
                        $posts[$ID][$key]['likes_count'] = isset($response['likesSummary'])  ? $response['likesSummary']['totalLikes'] ?? 0 : 0;

                        // Only Get the First Level Comment count, Change if you need.
                        $posts[$ID][$key]['comment_count'] = isset($response['commentsSummary'])  ? $response['commentsSummary']['totalFirstLevelComments'] ?? 0 : 0;


                        $posts[$ID][$key]['url'] = 'https://www.linkedin.com/feed/update/' . $post['id'];
                    }
                }
            }
            // dd('out');
            return $posts;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function getSocialActionsData($social_id, $token, $start, $end, $orgIds = [])
    {
        try {
            $socialActionsData = [];

            $headers = array(
                'Content-Type: application/json',
                'x-li-format: json',
                'X-Restli-Protocol-Version: 2.0.0',
                'Accept: application/json',
                'LinkedIn-Version: 202210',
                'Authorization: Bearer ' . $token . ' ',
                'Cookie: lidc="b=VB39:s=V:r=V:a=V:p=V:g=3491:u=9:x=1:i=1664264192:t=1664342966:v=2:sig=AQGzZQ2tzrzDL75a9M7N67zTNJ5M-9cA"; bcookie="v=2&85655da9-8de9-46d8-81df-59920214fb38"'
            );
            $host = 'https://api.linkedin.com/v2/';

            if (!empty($orgIds)) {
                $page_ids = $orgIds;
            } else {
                $page_ids = self::prepareType($social_id);
            }

            $media_pages = MediaPage::whereIn('page_id', $page_ids)->where('media_id', 2)->pluck('page_id')->toArray();
            foreach ($media_pages as $ID) {
                $url = $host . 'organizationalEntityShareStatistics?q=organizationalEntity&organizationalEntity=' . urlencode('urn:li:organization:' . $ID) . '&&timeIntervals=(timeRange:(start:' . $start . ',end:' . $end . '),timeGranularityType:DAY)';
                $type = 'GET';
                $response = fireCURL($url, $type, $headers);
                $socialActionsData[$ID] = $response['elements'];
            }
            return $socialActionsData;
        } catch (Exception $e) {
            logger()->error('getSocialActionData' . $e->getMessage());
            flash('Something Went Wrong')->error();
            return false;
        }
    }

    /** Linkedin data sservices  */
    public function getLinkedintotalFollowerData($orgIds, $page_update = 0)
    {
        $data = [];
        $user = auth()->user()->parentUser ?? auth()->user();

        if ($page_update) {
            $social_media = SocialMediaDetail::where('user_id', $user->id)->where('media_id', 2)->first();
            if (isset($social_media->id)) {
                $data = self::getTotalFollowerData($social_media->social_id, $social_media->token, $orgIds);
                Analytics::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'linkedin_total_followers' => json_encode($data['totalFollowerData'])
                    ]
                );
            }
        } else {
            $analytic = Analytics::where('user_id', $user->id)->first('linkedin_total_followers');
            if ($analytic) {
                $data['totalFollowerData'] = json_decode($analytic->linkedin_total_followers, true);
            }
        }

        return $data;
    }

    public function getLinkedinTimeFollowData($orgIds, $start, $end, $update_data = 0)
    {
        $finalArray = [
            'organicFollowerCount' => 0,
            'paidFollowerCount' => 0,
            'new_followers' => 0,
            'new_followers_keys' => [],
            'new_followers_values' => []
        ];
        $organicFollowerCount = 0;
        $paidFollowerCount = 0;
        $netflarr = [];
        $linkedin_time_follower = [];
        $user = auth()->user()->parentUser ?? auth()->user();

        if ($update_data) {
            $social_media = SocialMediaDetail::where('user_id', $user->id)->where('media_id', 2)->first();
            if (isset($social_media->id)) {
                $data = self::getTimeFollowData($social_media->social_id, $social_media->token, $orgIds, $start, $end);
                Analytics::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'linkedin_time_follower' => json_encode($data['timefollowdata'])
                    ]
                );
                $linkedin_time_follower = $data['timefollowdata'];
            }
        } else {
            $analytic = Analytics::where('user_id', $user->id)->first('linkedin_time_follower');
            if ($analytic) {
                $linkedin_time_follower = json_decode($analytic->linkedin_time_follower ?? '{}', true);
            }
        }

        foreach ($linkedin_time_follower as $lrf) {
            $organicFollowerCount +=  isset($lrf) ? array_sum(array_column(array_column($lrf, 'followerGains'), 'organicFollowerGain')) : 0;
            $paidFollowerCount += isset($lrf) ? array_sum(array_column(array_column($lrf, 'followerGains'), 'paidFollowerGain')) : 0;
            foreach ($lrf as $val) {
                $key = \Carbon\Carbon::parse($val['timeRange']['start'] / 1000)->format('M d');
                $netflarr[$key] = ($netflarr[$key] ?? 0) + $val['followerGains']['organicFollowerGain'];
            }
        }
        $finalArray = [
            'organicFollowerCount' => $organicFollowerCount,
            'paidFollowerCount' => $paidFollowerCount,
            'new_followers' => count($netflarr),
            'new_followers_keys' => array_keys($netflarr),
            'new_followers_values' => array_values($netflarr)
        ];
        return $finalArray;
    }

    public function getLinkedinClickData($orgIds, $start, $end, $update_data = 0)
    {
        $finalArray = [
            'clicks' => 0,
            'click_keys' => [],
            'click_values' => [],
            'viewers' => 0,
            'viewers_keys' => [],
            'viewers_values' => []
        ];
        $clickarr = [];
        $viewarr = [];
        $linkedin_time_click = [];
        $user = auth()->user()->parentUser ?? auth()->user();

        if ($update_data) {
            $social_media = SocialMediaDetail::where('user_id', $user->id)->where('media_id', 2)->first();
            if (isset($social_media->id)) {
                $data = self::getClickData($social_media->social_id, $social_media->token, $orgIds, $start, $end);
                Analytics::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'linkedin_time_click' => json_encode($data['clickdata'])
                    ]
                );

                $linkedin_time_click = $data['clickdata'];
            }
        } else {
            $analytic = Analytics::where('user_id', $user->id)->first('linkedin_time_click');
            if ($analytic) {
                $linkedin_time_click = json_decode($analytic->linkedin_time_click ?? '{}', true);
            }
        }

        // dd($linkedin_time_click);
        foreach ($linkedin_time_click as $lrf) {
            foreach ($lrf as $val) {
                $key = \Carbon\Carbon::parse($val['timeRange']['start'] / 1000)->format('M d');
                // $clickSum = $val['totalPageStatistics']['clicks']['careersPageClicks']['careersPageBannerPromoClicks']+
                // $val['totalPageStatistics']['clicks']['careersPageClicks']['careersPagePromoLinksClicks']+
                // $val['totalPageStatistics']['clicks']['careersPageClicks']['careersPageEmployeesClicks']+
                // $val['totalPageStatistics']['clicks']['careersPageClicks']['careersPageJobsClicks'];
                $clickSum = $val['totalShareStatistics']['clickCount'];
                // $clickSum = array_sum($val['totalShareStatistics']['clicks']['careersPageClicks']);
                $clickarr[$key] = ($clickarr[$key] ?? 0) + $clickSum;

                // $view = $val['totalShareStatistics']['views']['allPageViews']['pageViews'];
                $view = $val['totalShareStatistics']['impressionCount'];
                $viewarr[$key] = ($viewarr[$key] ?? 0) + $view;
            }
        }
        $finalArray = [
            'clicks' => count($clickarr),
            'click_keys' => array_keys($clickarr),
            'click_values' => array_values($clickarr),
            'viewers' => count($viewarr),
            'viewers_keys' => array_keys($viewarr),
            'viewers_values' => array_values($viewarr)
        ];
        return $finalArray;
    }

    public function getLinkedinSocialActionsData($orgIds, $start, $end, $update_data = 0)
    {
        $finalArray = [
            'social_count' => 0,
            'social_actions_keys' => [],
            'likes_count' => [],
            'comment_count' => [],
            'share_count' => []
        ];
        $social_actions_keys = [];
        $likes_count_array = [];
        $comments_count_array = [];
        $share_count_array = [];
        $linkedin_social_action_data = [];
        $user = auth()->user()->parentUser ?? auth()->user();

        if ($update_data) {
            $social_media = SocialMediaDetail::where('user_id', $user->id)->where('media_id', 2)->first();
            if (isset($social_media->id)) {
                $linkedin_social_action_data = self::getSocialActionsData($social_media->social_id, $social_media->token, $start, $end, $orgIds);
                Analytics::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'linkedin_social_action_data' => json_encode($linkedin_social_action_data),
                    ]
                );
            }
        } else {
            $analytic = Analytics::where('user_id', $user->id)->first('linkedin_social_action_data');
            if ($analytic) {
                $linkedin_social_action_data = json_decode($analytic->linkedin_social_action_data ?? '{}', true);
            }
        }

        if ($linkedin_social_action_data) {
            foreach ($linkedin_social_action_data as  $single_page_data) {
                foreach ($single_page_data as  $data) {
                    $social_actions_keys[] = \Carbon\Carbon::parse($data['timeRange']['start'] / 1000)->format('M d');
                    $likes_count_array[] = $data['totalShareStatistics']['likeCount'];
                    $comments_count_array[] = $data['totalShareStatistics']['commentCount'];
                    $share_count_array[] = $data['totalShareStatistics']['shareCount'];
                }
            }
        }
        $finalArray = [
            'social_count' => count($social_actions_keys),
            'social_actions_keys' => $social_actions_keys,
            'likes_count' => $likes_count_array,
            'comment_count' => $comments_count_array,
            'share_count' => $share_count_array
        ];
        return $finalArray;
    }

    public function getLinkedinGeoData($analytic_id, $page_update = 0)
    {
        $finalArray = [
            'tfc' => 0,
            'followerCountsByGeoCountry' => [],
            'linkedin_geo_data' => []
        ];
        $followerCountsByGeoCountry = [];
        $tfc = 0;
        $linkedin_geo_data = [];
        $linkedin_follower = [];
        $user = auth()->user()->parentUser ?? auth()->user();

        $analytic = Analytics::find($analytic_id, ['linkedin_follower', 'linkedin_geo_data']);
        if ($analytic) {
            $linkedin_follower = json_decode($analytic->linkedin_follower ?? '{}', true);
            $linkedin_geo_data = json_decode($analytic->linkedin_geo_data ?? '{}', true);
        }

        if ($page_update) {
            $social_media = SocialMediaDetail::where('user_id', $user->id)->where('media_id', 2)->first();
            if (isset($social_media->id)) {
                if ($analytic->linkedin_follower) {
                    $linkedin_geo_data = self::getGeodata($linkedin_follower, $social_media->token);
                    Analytics::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'linkedin_geo_data' => json_encode($linkedin_geo_data)
                        ]
                    );
                }
            }
        }

        foreach ($linkedin_follower as $lf) {

            foreach ($lf as $key => $value) {

                //followerCountsByGeoCountry
                if (isset($value['followerCountsByGeoCountry'])) {
                    foreach ($value['followerCountsByGeoCountry'] as $fc) {
                        $tfc += (isset($fc['followerCounts']['organicFollowerCount']) ? $fc['followerCounts']['organicFollowerCount'] : 0);
                        $followerCountsByGeoCountry[$fc['geo']] = ($followerCountsByGeoCountry[$fc['geo']] ?? 0) + (isset($fc['followerCounts']['organicFollowerCount']) ? $fc['followerCounts']['organicFollowerCount'] : 0);
                    }
                }
            }
        }
        $finalArray = [
            'tfc' => $tfc,
            'followerCountsByGeoCountry' => $followerCountsByGeoCountry,
            'linkedin_geo_data' => $linkedin_geo_data
        ];
        return $finalArray;
    }

    public function getLinkedinCompanyData($analytic_id, $page_update = 0)
    {
        $finalArray = [
            'tfcs' => 0,
            'followerCountsByStaffCountRange' => []
        ];
        $followerCountsByStaffCountRange = [];
        $tfcs = 0;
        $analytic = Analytics::find($analytic_id, ['linkedin_follower', 'linkedin_geo_data']);
        if ($analytic->linkedin_follower) {
            $linkedin_follower = json_decode($analytic->linkedin_follower ?? '{}', true);
            $linkedin_geo_data = json_decode($analytic->linkedin_geo_data ?? '{}', true);
            foreach ($linkedin_follower as $lf) {

                foreach ($lf as $key => $value) {

                    //followerCountsByStaffCountRange
                    if (isset($value['followerCountsByStaffCountRange'])) {
                        foreach ($value['followerCountsByStaffCountRange'] as $fc) {

                            $tfcs += (isset($fc['followerCounts']['organicFollowerCount']) ? $fc['followerCounts']['organicFollowerCount'] : 0);
                            $followerCountsByStaffCountRange[$fc['staffCountRange']] =
                                ($followerCountsByStaffCountRange[$fc['staffCountRange']] ?? 0) + (isset($fc['followerCounts']['organicFollowerCount']) ? $fc['followerCounts']['organicFollowerCount'] : 0);
                        }
                    }
                }
            }
        }

        $finalArray = [
            'tfcs' => $tfcs,
            'followerCountsByStaffCountRange' => $followerCountsByStaffCountRange
        ];
        return $finalArray;
    }

    public function getLinkedinSenorityData($analytic_id, $page_update = 0)
    {
        $finalArray = [
            'tfcl' => 0,
            'followerCountsBySeniority' => [],
            'linkedin_seniority_data' => []
        ];
        $followerCountsBySeniority = [];
        $tfcl = 0;
        $linkedin_seniority_data = [];
        $linkedin_follower = [];

        $user = auth()->user()->parentUser ?? auth()->user();

        $analytic = Analytics::find($analytic_id, ['linkedin_follower', 'linkedin_seniority_data']);
        if ($analytic) {
            $linkedin_follower = json_decode($analytic->linkedin_follower ?? '{}', true);
            $linkedin_seniority_data = json_decode($analytic->linkedin_seniority_data ?? '{}', true);
        }

        if ($page_update) {
            $social_media = SocialMediaDetail::where('user_id', $user->id)->where('media_id', 2)->first();
            if (isset($social_media->id)) {

                if ($analytic->linkedin_follower) {

                    $linkedin_seniority_data = self::getSenioritdata($linkedin_follower, $social_media->token);
                    Analytics::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'linkedin_seniority_data' => json_encode($linkedin_seniority_data)
                        ]
                    );
                }
            }
        }

        foreach ($linkedin_follower as $lf) {

            foreach ($lf as $key => $value) {

                //followerCountsBySeniority
                if (isset($value['followerCountsBySeniority'])) {
                    foreach ($value['followerCountsBySeniority'] as $fc) {

                        $tfcl += (isset($fc['followerCounts']['organicFollowerCount']) ? $fc['followerCounts']['organicFollowerCount'] : 0);
                        $followerCountsBySeniority[$fc['seniority']] =
                            ($followerCountsBySeniority[$fc['seniority']] ?? 0) + (isset($fc['followerCounts']['organicFollowerCount']) ? $fc['followerCounts']['organicFollowerCount'] : 0);
                    }
                }
            }
        }
        $finalArray = [
            'tfcl' => $tfcl,
            'followerCountsBySeniority' => $followerCountsBySeniority,
            'linkedin_seniority_data' => $linkedin_seniority_data
        ];
        return $finalArray;
    }

    public function getLinkedinFunctionData($analytic_id, $page_update = 0)
    {
        $finalArray = [
            'function_count' => 0,
            'function_keys' => [],
            'function_values' => []
        ];
        $followerCountsByFunction = [];
        $linkedin_follower = [];
        $linkedin_function_data = [];

        $user = auth()->user()->parentUser ?? auth()->user();

        $analytic = Analytics::find($analytic_id, ['linkedin_follower', 'linkedin_function_data']);
        if ($analytic) {
            $linkedin_follower = json_decode($analytic->linkedin_follower ?? '{}', true);
            $linkedin_function_data = json_decode($analytic->linkedin_function_data ?? '{}', true);
        }

        if ($page_update) {
            $social_media = SocialMediaDetail::where('user_id', $user->id)->where('media_id', 2)->first();
            if (isset($social_media->id)) {

                if ($analytic->linkedin_follower) {
                    $linkedin_function_data = self::getFunctiondata($linkedin_follower, $social_media->token);
                    Analytics::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'linkedin_function_data' => json_encode($linkedin_function_data)
                        ]
                    );
                }
            }
        }

        foreach ($linkedin_follower as $lf) {

            foreach ($lf as $key => $value) {

                //followerCountsByFunction
                if (isset($value['followerCountsByFunction'])) {
                    foreach ($value['followerCountsByFunction'] as $fc) {
                        // dump($linkedin_function_data);
                        if ($fc['followerCounts']['organicFollowerCount'] > 3) {
                            if (isset($linkedin_function_data[$fc['function']])) {
                                $followerCountsByFunction[$linkedin_function_data[$fc['function']]] =
                                    ($followerCountsByFunction[$fc['function']] ?? 0) + (isset($fc['followerCounts']['organicFollowerCount']) ? $fc['followerCounts']['organicFollowerCount'] : 0);
                            } else {

                                $followerCountsByFunction['null'] =
                                    ($followerCountsByFunction[$fc['function']] ?? 0) + (isset($fc['followerCounts']['organicFollowerCount']) ? $fc['followerCounts']['organicFollowerCount'] : 0);
                            }
                        }
                    }
                }
            }
        }
        $function_keys = array_keys($followerCountsByFunction);
        $function_values = array_values($followerCountsByFunction);

        $function_keys = array_map(function ($val) {
            return strlen($val) > 10 ?  substr($val, 0, 20) . "..." : $val;
        }, $function_keys);

        $finalArray = [
            'function_count' => count($followerCountsByFunction),
            'function_keys' => $function_keys,
            'function_values' => $function_values
        ];
        return $finalArray;
    }

    public function getLinkedinIndustryData($analytic_id, $page_update = 0)
    {
        $finalArray = [
            'industry_count' => 0,
            'industry_keys' => [],
            'industry_values' => []
        ];
        $followerCountsByIndustry = [];
        $linkedin_follower = [];
        $linkedin_industry_data = [];

        $user = auth()->user()->parentUser ?? auth()->user();
        $analytic = Analytics::find($analytic_id, ['linkedin_follower', 'linkedin_industry_data']);
        if ($analytic) {
            $linkedin_follower = json_decode($analytic->linkedin_follower ?? '{}', true);
            $linkedin_industry_data = json_decode($analytic->linkedin_industry_data ?? '{}', true);
        }

        if ($page_update) {
            $social_media = SocialMediaDetail::where('user_id', $user->id)->where('media_id', 2)->first();
            if (isset($social_media->id)) {

                if ($analytic->linkedin_follower) {

                    $linkedin_industry_data = self::getIndustryData($linkedin_follower, $social_media->token);
                    Analytics::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'linkedin_industry_data' => json_encode($linkedin_industry_data)
                        ]
                    );
                }
            }
        }

        foreach ($linkedin_follower as $lf) {

            foreach ($lf as $key => $value) {

                //followerCountsByIndustry
                if (isset($value['followerCountsByIndustry'])) {
                    foreach ($value['followerCountsByIndustry'] as $fc) {
                        if ($fc['followerCounts']['organicFollowerCount'] > 3) {
                            if (isset($linkedin_industry_data[$fc['industry']])) {
                                $followerCountsByIndustry[$linkedin_industry_data[$fc['industry']]] =
                                    ($followerCountsByIndustry[$fc['industry']] ?? 0) + (isset($fc['followerCounts']['organicFollowerCount']) ? $fc['followerCounts']['organicFollowerCount'] : 0);
                            } else {
                                $followerCountsByIndustry['null'] =
                                    ($followerCountsByIndustry[$fc['industry']] ?? 0) + (isset($fc['followerCounts']['organicFollowerCount']) ? $fc['followerCounts']['organicFollowerCount'] : 0);
                            }
                        }
                    }
                }
            }
        }

        $industry_keys = array_keys($followerCountsByIndustry);
        $industry_values = array_values($followerCountsByIndustry);

        $industry_keys = array_map(function ($val) {
            return strlen($val) > 10 ?  substr($val, 0, 20) . "..." : $val;
        }, $industry_keys);

        $finalArray = [
            'industry_count' => count($followerCountsByIndustry),
            'industry_keys' => $industry_keys,
            'industry_values' => $industry_values
        ];

        return $finalArray;
    }

    public function LinkedInPost($start, $end, $update_data = 0, $orgIds = [])
    {
        $linkedin_posts_data_arr = [];
        try {
            $user = auth()->user()->parentUser ?? auth()->user();
            $linkedin_posts = [];
            if ($update_data) {
                $social_media = SocialMediaDetail::where('user_id', $user->id)->where('media_id', 2)->first();

                $linkedin_posts_data = LinkedInService::userPost($social_media->social_id, $social_media->token, $start, $end, $orgIds);

                if (!$linkedin_posts_data) throw new Exception();
                Analytics::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'linkedin_posts' => json_encode($linkedin_posts_data)
                    ]
                );

                $linkedin_posts = $linkedin_posts_data;
            } else {
                $analytic = Analytics::where('user_id', $user->id)->first('linkedin_posts');
                if ($analytic) {
                    $linkedin_posts = json_decode($analytic->linkedin_posts ?? '{}', true);
                }
            }

            if (!empty($linkedin_posts)) {
                $linkedin_posts = call_user_func_array('array_merge', $linkedin_posts);
                array_multisort(array_column($linkedin_posts, 'likes_count'), SORT_DESC, $linkedin_posts);
                $linkedin_posts_data_arr = $linkedin_posts;
            }
        } catch (\Exception $e) {
            logger()->error('linkedin Post' . $e->getMessage());
            flash('Something Went Wrong')->error();
            return redirect()->back();
        }
        return $linkedin_posts_data_arr;
        // return redirect()->back();
    }
}
