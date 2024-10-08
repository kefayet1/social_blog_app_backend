<?php

use App\Models\User;
use App\Models\PostLike;
use App\Models\FollowTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostLikeController;
use App\Http\Controllers\FollowTagController;
use App\Http\Controllers\resetPassController;
use App\Http\Controllers\SavePostsController;
use App\Http\Controllers\FollowUserController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post("/login", [AuthController::class, "login"]);
Route::post("/registration", [AuthController::class, "registration"]);

Route::post("/sent_otp", [OTPController::class, "sentOtp"]);
Route::post("/verify_otp", [OTPController::class, "verifyOtp"]);

Route::post("/reset_password", [resetPassController::class, "resetPassword"]);

Route::middleware(['tokenVerify'])->group(function () {
    //tag
    Route::post("/get_tags", [TagController::class, "getAllTag"]);
    Route::post("/get_tag_with_name", [TagController::class, "getTagWithUser"]);
    Route::post("/create_tag", [TagController::class, "createTag"]);
    Route::post("/tag_delete", [TagController::class, "tagDelete"]);
    Route::post("/update_tag", [TagController::class, "updateTag"]);
    Route::post("/search_tag", [TagController::class, "searchTag"]);
    Route::post("/get_tag_details", [TagController::class, "getTagDetails"]);

    //Post route
    Route::post("/create_post", [PostController::class, "createPost"]);
    Route::post("/get_posts", [PostController::class, "getPosts"]);
    Route::post("/delete_post", [PostController::class, "postDelete"]);
    Route::post("/update_post", [PostController::class, "postUpdate"]);
    Route::post("/noAuth_get_post_with_auth", [PostController::class, "getSinglePostWithAuth"]);

    //post frontend
    Route::post("/get_relevant_post", [PostController::class, "getRelevantPost"]);
 
    
    //post like
    Route::post("/like_post", [PostLikeController::class, "likePost"]);

    //save post
    Route::post("/save_post", [SavePostsController::class, "savePost"]);
    Route::post("/get_save_post", [SavePostsController::class, "getSavePost"]);

    //follow post
    Route::post("follow_And_Unfollow_Tag", [FollowTagController::class, "followAndUnFollowTag"]);
    Route::post("get_following_tags", [FollowTagController::class, "getFollowingTag"]);

    // profile
    Route::post("/create_profile", [ProfileController::class, "createProfile"]);
    Route::post("/get_profile_details", [ProfileController::class, "getUserProfile"]);

    // following users
    Route::post("/follow_unFollow_user", [FollowUserController::class, "followAndUnfollowUser"]);
    Route::post("/get_followers", [FollowUserController::class, "getFollowers"]);
    Route::post("/get_following", [FollowUserController::class, "getFollowing"]);
    Route::post("/get_follow_user", [FollowUserController::class, "getUserFollowOrUnFollow"]);

    //notification
    Route::post("/get_notification", [NotificationController::class, "getNotification"]);

    //comment
    Route::post("/create_comment", [CommentController::class, "createComment"]);

});


//this route are accessible without auth
Route::post("/test", [PostController::class, "testPost"]);
Route::post("/noAuth_get_post", [PostController::class, "noAuthGetPost"]);
Route::post("/get_single_post", [PostController::class, "getSinglePost"]);
Route::post("/getComment", [CommentController::class, "getComment"]);


//Post
Route::post("/get_post_by_tag_name", [PostController::class, "findPostByTag"]);
Route::post("/get_post_by_user_id", [PostController::class, "getPostByUserId"]);
   Route::post("/get_top_post", [PostController::class, "getTopPost"]);

//tag
// without it will not give is_follow value
Route::post("/get_tag_details_without_auth", [TagController::class, "getTagDetails"]);

Route::post("/get_tag_with_post_count", [TagController::class, "getTagWithPostCount"]);

//profile
// Route::post("/get_profile_details", [ProfileController::class, "getUserProfile"]);

