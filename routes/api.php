<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FollowTagController;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostLikeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\resetPassController;
use App\Http\Controllers\SavePostsController;
use App\Http\Controllers\TagController;
use App\Models\FollowTag;
use App\Models\PostLike;
use Illuminate\Support\Facades\Route;

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

    //post like
    Route::post("/like_post", [PostLikeController::class, "likePost"]);

    //save post
    Route::post("/save_post", [SavePostsController::class, "savePost"]);

    //follow post
    Route::post("follow_And_Unfollow_Tag", [FollowTagController::class, "followAndUnFollowTag"]);
    Route::post("get_following_tags", [FollowTagController::class, "getFollowingTag"]);

    // profile
    Route::post("/create_profile", [ProfileController::class, "createProfile"]);
});


//this route are accessible without auth
Route::post("/test", [PostController::class, "testPost"]);
Route::post("/noAuth_get_post", [PostController::class, "noAuthGetPost"]);
Route::post("/get_single_post", [PostController::class, "getSinglePost"]);
Route::post("/getComment", [CommentController::class, "getComment"]);
Route::post("/create_comment", [CommentController::class, "createComment"]);

//Post
Route::post("/get_post_by_tag_name", [PostController::class, "findPostByTag"]);

//tag
// without it will not give is_follow value
Route::post("/get_tag_details_without_auth", [TagController::class, "getTagDetails"]);