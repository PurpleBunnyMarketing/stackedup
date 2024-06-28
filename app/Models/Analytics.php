<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analytics extends Model
{
    use HasFactory;

    protected $table = 'analytics';
    protected $fillable = [
        'custom_id', 'linkedin_industry_data', 'linkedin_function_data', 'linkedin_seniority_data', 'linkedin_geo_data', 'linkedin_time_click', 'linkedin_time_follower', 'linkedin_follower', 'user_id', 'twitter_tweet_analytic', 'twitter_follower_analytic', 'facebook_like_analytic', 'twitter_analytic', 'facebook_engaged_analytic', 'facebook_react_analytic', 'facebook_post', 'facebook_review', 'linkedin_total_followers', 'linkedin_posts', 'linkedin_social_action_data', 'instagram_total_followers', 'instagram_followers_gained', 'instagram_reach', 'instagram_impression', 'instagram_profile_visits', 'instagram_website_clicks', 'instagram_clicks_data', 'instagram_age_gender', 'instagram_geo', 'instagram_posts', 'linkedin_last_updated_at', 'google_last_updated', 'google_plateform_device', 'google_rating_and_review_count', 'google_calls_website_direction', 'google_messages_bookings_food_order', 'google_reviews', 'google_posts','facebook_last_updated_at','facebook_reach_last_updated_at','facebook_engagement_last_updated_at'
    ];
}
