<?php

namespace App\Http\Controllers;

use App\Events\SendNotification;
use App\Models\FollowTag;
use App\Models\FollowUser;
use App\Models\Notification;
use DateInterval;
use Carbon\Carbon;
use App\Models\Tag;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\PostTags;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Stmt\TryCatch;

class PostController extends Controller
{
    //
    public function createPost(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:300',
            'tags' => 'array|max:5',
            'active' => 'boolean',
            'body' => 'required',
            'date' => 'nullable:date',
            'thumbnail' => "image|mimes:png,jpg,jpeg,gif,svg|max:2048"
        ]);

        $img = $request->file("thumbnail");
        $t = time();

        $file_name = $img->getClientOriginalName();
        $img_name = "{$request->header('user_id')}-{$t}-{$file_name}";
        $img_url = "uploads/{$img_name}";

        // Upload File
        $img->move(public_path('uploads'), $img_url);

        // published_at date converting into database type
        $published_at = $request->input('published_at') ? Carbon::parse($request->input('date')) : null;

        // active converting into number
        $active = $request->input("active") === true || $request->input("active") === "true" ? 1 : 0;


        $post = Post::create([
            "title" => $request->input("title"),
            "active" => $active,
            "body" => $request->input("body"),
            "user_id" => $request->header("user_id"),
            "published_at" => $published_at,
            "thumbnail" => $img_url
        ]);

        //notifications
        //1st priority of notification is. if user is following article write the user will get notification.
        //2nd priority: if user is not following article writer the user will not get notification.
        //3rd priority: if user is not following article writer but follow article writer written particular tag.The user will get notification from tag

        //those who are following the user
        $followingUserId = FollowUser::where("following_user_id", "=", $request->header("user_id"))
            ->where("is_follow", "=", true)->get()->pluck('user_id');

        //create notification
        foreach ($followingUserId as $id) {
            $notification = Notification::create([
                "text" => "new Post added now",
                "type" => "post",
                "user_id" => $id,
                "actor_id" => $request->header("user_id"),
                "post_id" => $post->id,
            ]);
            event(new SendNotification(
                [
                    [
                        "id" => $notification->id,
                        "title" => $notification->title,
                        "user_id" => $notification->user_id,
                        "actor_id" => $notification->actor_id,
                        "created_at" => $notification->created_at,
                        "is_seen" => $notification->is_seen,
                        "type" => $notification->type,
                        "name" => $request->header("name"),
                        "post_id" => $post->id,
                        "event" => 'first_event'
                    ]
                ]
            ));
        }

        // if user given tags are not exist in the database then it will insert to database
        if ($post) {
            $tags = array_map(fn($value) => str_replace(['\'', '"'], '', $value), explode(',', trim($request->input('tags'), '[]')));

            foreach ($tags as $tag) {
                $tagId = Tag::where("title", "=", $tag)->first('id')['id'];

                //retrieving user those who are following the tag. article writer written particular tag 
                $userFollowingTag = FollowTag::where("tag_id", "=", $tagId)->get()->pluck("user_id");

                //finding those user who are not present in the $followingUserId. bcz the notification already created for them
                // in there are those who are not following article writer but following the particular tag
                $uniqueValue = array_merge(array_diff(json_decode($userFollowingTag), json_decode($followingUserId)));

                //creating notification for them
                foreach ($uniqueValue as $id) {
                    $isNotifiAvlable = Notification::where("post_id", "=", $post->id)
                        ->where("user_id", "=", $id)
                        ->first();
                    if (!$isNotifiAvlable) {
                        $notification = Notification::create([
                            "user_id" => $id,
                            "actor_id" => $request->header("user_id"),
                            "tag_id" => $tagId,
                            "type" => "tag_post",
                            "name" => $request->header("name"),
                            "post_id" => $post->id,
                            "is_seen" => false,
                            "tag_title" => $tag,
                        ]);
                        event(new SendNotification([
                            [
                                "id" => $notification->id,
                                "title" => $notification->title,
                                "user_id" => $notification->user_id,
                                "actor_id" => $notification->actor_id,
                                "created_at" => $notification->created_at,
                                "tag_id" => $notification->tag_id,
                                "type" => $notification->type,
                                "name" => $request->header("name"),
                                "post_id" => $post->id,
                                "tag_title" => $tag,
                                "event" => 'second_event'
                            ]
                        ]));
                    }
                }

                if (!$tagId) {
                    $createdTag = Tag::create([
                        "title" => $tag,
                        "hashtag" => "#" . "{$tag}",
                        "user_id" => $request->header("user_id")
                    ]);

                    if ($createdTag) {
                        PostTags::create([
                            "post_id" => $post['id'],
                            "tag_id" => $createdTag['id']
                        ]);
                    } else {
                        return response()->json([
                            "status" => "failed",
                            "message" => "failed to tag creation"
                        ]);
                    }
                } else {
                    PostTags::create([
                        "post_id" => $post->id,
                        "tag_id" => $tagId
                    ]);
                }


            }
        } else {
            return response()->json([
                "status" => "fail",
                "message" => "post is not created"
            ]);
        }
        return response()->json([
            'status' => "success",
            "message" => "post is created"
        ]);

    }

    public function getPosts(Request $request)
    {
        $posts = DB::table('posts')
            ->join('users', 'users.id', '=', 'posts.user_id')
            ->leftJoin('post_tags', 'posts.id', '=', 'post_tags.post_id')
            ->leftJoin('tags', 'post_tags.tag_id', '=', 'tags.id')
            ->select(
                'posts.id',
                'posts.title',
                'posts.thumbnail',
                'posts.body',
                'posts.active',
                'posts.published_at',
                'users.name',
                'users.email',
                DB::raw('GROUP_CONCAT(tags.title) as tags')
            )
            ->groupBy(
                'posts.id',
                'posts.title',
                'posts.thumbnail',
                'posts.body',
                'posts.active',
                'posts.published_at',
                'users.name',
                'users.email'
            )
            ->paginate(10);
        return $posts;
    }

    public function postDelete(Request $request)
    {
        $id = $request->input("id");

        $postDelete = Post::where("id", "=", $id)
            ->delete();
        if ($postDelete) {
            return response()->json([
                "status" => "success",
                "message" => "Post has been deleted successfully"
            ]);
        }

        return response()->json([
            "status" => "failed",
            "message" => "Request have been failed"
        ]);
    }

    public function postUpdate(Request $request)
    {
        // user didn't change post image it's mean thumbnail will come with string if changed it will come with array
        if ($request->hasFile('thumbnail')) {
            $request->has("oldThumbnail") ? $getImage = Storage::delete($request->input("oldThumbnail")) : null;

            $file = $request->file("thumbnail");
            $t = time();
            $fileName = $file->getClientOriginalName();
            $img_name = "{$request->header('user_id')}-{$t}-{$fileName}";
            $img_url = "uploads/{$img_name}";

            $file->move(public_path("uploads"), $img_url);
        } else {
            $img_url = null;
        }



        // published_at date converting into database type
        if ($request->has('published_at')) {
            $published_at = $request->input('published_at') ? Carbon::parse($request->input('published_at'), "Asia/Dhaka") : null;
        } else {
            $published_at = null;
        }


        // active converting into number
        $active = ($request->input("active") !== 1 && $request->input("active") !== "1" && $request->input("active") !== "0" && $request->input("active") !== "0")
            ? (($request->input("active") === true || $request->input("active") === "true") ? 1 : 0)
            : $request->input("active");

        $updateData = [
            "title" => $request->input("title"),
            "body" => $request->input("body"),
            "active" => $active
        ];

        if ($request->has('published_at') && $published_at !== null) {
            $updateData["published_at"] = $published_at;

        }


        if ($request->hasFile('thumbnail') && $img_url !== null) {
            $updateData["thumbnail"] = $img_url;

        }

        $updatePost = DB::table("posts")->where("id", "=", $request->input('id'))->update($updateData);


        if ($updatePost) {
            $removeAllTag = PostTags::where("post_id", "=", $request->input("id"))->delete();
            $tags = explode(",", $request->input("tags"));
            foreach ($tags as $tag) {
                $tagId = Tag::where("title", "=", $tag)->pluck('id')->first();
                if (!$tagId) {
                    $createdTag = Tag::create([
                        "title" => $tag,
                        "hashtag" => "#" . "{$tag}",
                        "user_id" => $request->header("user_id")
                    ]);

                    if ($createdTag) {
                        PostTags::create([
                            "post_id" => $request->input('id'),
                            "tag_id" => $createdTag['id']
                        ]);
                    } else {
                        return response()->json([
                            "status" => "failed",
                            "message" => "failed to tag creation"
                        ]);
                    }
                } else {
                    PostTags::create([
                        "post_id" => $request->input('id'),
                        "tag_id" => $tagId
                    ]);
                }
            }
            return response()->json([
                "status" => "success",
                "message" => "post is successfully updated",
                "delete" => $removeAllTag
            ]);
        } else {
            return response()->json([
                "status" => "failed",
                "message" => "post is not updated"
            ]);
        }
    }

    public function testPost()
    {
        $postDate = fake()->dateTimeBetween('-1 month', 'now');

        // random number between 7
        $randomDay = rand(1, 7);

        //post published date
        $postPublishObj = clone $postDate;
        $postPublishDate = $postPublishObj->add(new DateInterval("P{$randomDay}D"));
        // dd([$postDate, $postPublishDate]);
    }

    public function noAuthGetPost()
    {
        $posts = DB::table('posts')
            ->join('users', 'users.id', '=', 'posts.user_id')
            ->leftJoin('post_tags', 'posts.id', '=', 'post_tags.post_id')
            ->leftJoin('tags', 'post_tags.tag_id', '=', 'tags.id')
            ->leftJoin("profiles", "posts.user_id", "=", "profiles.user_id")
            ->leftJoin("post_likes", "posts.id", "=", "post_likes.post_id")
            ->where("posts.published_at", "<", Carbon::now())
            ->where("active", "=", true)
            ->select(
                'posts.id',
                'posts.title',
                'posts.thumbnail',
                'posts.body',
                'posts.active',
                'posts.published_at',
                'posts.user_id',
                'users.name',
                'users.email',
                "profiles.profile_image",
                DB::raw('GROUP_CONCAT(DISTINCT tags.title) as tags'),
                DB::raw("COUNT(DISTINCT CASE WHEN post_likes.is_like = 1 THEN 1 END) as totalLike"),
            )
            ->groupBy(
                'posts.id',
                'posts.title',
                'posts.thumbnail',
                'posts.body',
                'posts.active',
                'posts.published_at',
                "posts.user_id",
                'users.name',
                'users.email',
                "profiles.profile_image",
            )
            ->orderByRaw("DATE(posts.published_at) = CURDATE() DESC")
            ->orderBy("posts.published_at", 'DESC')
            ->paginate(10);

        //plucking user id into array
        $postId = $posts->pluck("id")->toArray();

        //finding all comment on post
        $comments = DB::table("comments")
            ->leftJoin("users", "comments.user_id", "=", "users.id")
            ->leftJoin("profiles", "comments.user_id", "=", "profiles.user_id")
            ->where("comments.parent_id", "=", NULL)
            ->whereIn("comments.post_id", $postId)
            ->select(
                "comments.post_id",
                "comments.comment",
                "comments.created_at",
                "profiles.profile_image",
                "users.id as user_id",
                "users.name",
                "comments.post_id"
            )
            ->get();

        //grouping comments by post_id
        $commentByPost = $comments->groupBy("post_id");
        foreach ($posts as $post) {
            $post->comments = [
                'totalComment' => count($commentByPost->get($post->id) ?? []),
                'last_comment' => count($commentByPost->get($post->id) ?? []) > 0 ? $commentByPost->get($post->id)[0] : [],
            ];
        }
        return $posts;
    }



    public function getSinglePost(Request $request)
    {
        $post = DB::table("posts")
            ->leftJoin("post_tags", 'posts.id', "=", "post_tags.post_id")
            ->leftJoin("tags", 'post_tags.tag_id', "=", 'tags.id')
            ->leftJoin("users", "posts.user_id", "=", "users.id")
            ->leftJoin("comments", "posts.id", "=", "comments.post_id")
            ->leftJoin("post_likes", "posts.id", "=", "post_likes.post_id")
            ->leftJoin("save_posts", "posts.id", "=", "save_posts.post_id")
            ->where("posts.id", "=", $request->input('id'))
            ->select(
                "users.name",
                "posts.title",
                "posts.body",
                "posts.published_at",
                "posts.thumbnail",
                DB::raw('GROUP_CONCAT(DISTINCT tags.title) as tags'),
                DB::raw("COUNT(DISTINCT comments.id) as totalComment"),
                DB::raw("COUNT(DISTINCT CASE WHEN post_likes.is_like = 1 THEN 1 END) as totalLike"),
                DB::raw("COUNT(DISTINCT CASE WHEN save_posts.is_save = 1 THEN 1 END) as totalSave")
            )
            ->groupBy(
                "posts.id",
                "users.name",
                "posts.title",
                "posts.body",
                "posts.published_at",
                "posts.thumbnail",
            )
            ->first();

        return $post;
    }

    public function getSinglePostWithAuth(Request $request)
    {
        $post = DB::table("posts")
            ->leftJoin("post_tags", 'posts.id', "=", "post_tags.post_id")
            ->leftJoin("tags", 'post_tags.tag_id', "=", 'tags.id')
            ->leftJoin("users", "posts.user_id", "=", "users.id")
            ->leftJoin("comments", "posts.id", "=", "comments.post_id")
            ->leftJoin("post_likes", function ($join) use ($request) {
                $join->on("posts.id", "=", "post_likes.post_id")
                    ->where("post_likes.user_id", "=", $request->header("user_id"));
            })
            ->leftJoin("save_posts", function ($join) use ($request) {
                $join->on("posts.id", "=", "save_posts.post_id")
                    ->where("save_posts.user_id", "=", $request->header("user_id"));
            })
            ->where("posts.id", "=", $request->input('id'))
            ->select(
                "users.name",
                "posts.title",
                "posts.body",
                "posts.published_at",
                "posts.thumbnail",
                "post_likes.is_like",
                "save_posts.is_save",
                DB::raw('GROUP_CONCAT(DISTINCT tags.title) as tags'),
                DB::raw("COUNT(DISTINCT comments.id) as totalComment"),
                DB::raw("COUNT(DISTINCT CASE WHEN post_likes.is_like = 1 THEN 1 END) as totalLike"),
                DB::raw("COUNT(DISTINCT CASE WHEN save_posts.is_save = 1 THEN 1 END) as totalSave")
            )
            ->groupBy(
                "posts.id",
                "users.name",
                "posts.title",
                "posts.body",
                "posts.published_at",
                "posts.thumbnail",
                "post_likes.is_like",
                "save_posts.is_save"
            )
            ->first();

        return $post;
    }

    public function findPostByTag(Request $request)
    {
        $tagId = Tag::where("title", "=", $request->input("tag_name"))->first()->id;

        //get post id associated with the given tag
        $postIds = DB::table("post_tags")
            ->where("tag_id", "=", $tagId)
            ->pluck("post_id");

        $posts = DB::table('posts')
            ->join('users', 'users.id', '=', 'posts.user_id')
            ->leftJoin('post_tags', 'posts.id', '=', 'post_tags.post_id')
            ->leftJoin('tags', 'post_tags.tag_id', '=', 'tags.id')
            ->leftJoin("profiles", "posts.user_id", "=", "profiles.user_id")
            ->whereIn("post_tags.post_id", $postIds)
            ->where("active", "=", true)
            ->select(
                'posts.id',
                'posts.title',
                'posts.thumbnail',
                'posts.body',
                'posts.active',
                'posts.published_at',
                'users.name',
                'users.email',
                "profiles.profile_image",
                DB::raw('GROUP_CONCAT(DISTINCT tags.title) as tags')
            )
            ->groupBy(
                'posts.id',
                'posts.title',
                'posts.thumbnail',
                'posts.body',
                'posts.active',
                'posts.published_at',
                'users.name',
                'users.email',
                "profiles.profile_image",
            )
            ->paginate(10);
        return $posts;
    }

    public function getPostByUserId(Request $request)
    {
        $posts = DB::table("posts")
            ->leftJoin("post_tags", "posts.id", "=", "post_tags.post_id")
            ->leftJoin("tags", "post_tags.tag_id", "=", "tags.id")
            ->leftJoin("users", "posts.user_id", "=", "users.id")
            ->leftJoin("profiles", "posts.user_id", "=", "profiles.user_id")
            ->where("posts.user_id", "=", $request->input('id'))
            ->select(
                'posts.id',
                'posts.title',
                'posts.thumbnail',
                'posts.body',
                'posts.active',
                'posts.published_at',
                'users.name',
                'users.email',
                "posts.user_id",
                "profiles.profile_image",
                DB::raw('GROUP_CONCAT(DISTINCT tags.title) as tags')
            )
            ->groupBy(
                'posts.id',
                'posts.title',
                'posts.thumbnail',
                'posts.body',
                'posts.active',
                'posts.published_at',
                "posts.user_id",
                'users.name',
                'users.email',
                "profiles.profile_image"
            )
            ->paginate(10);

        return $posts;
    }

    public function getRelevantPost(Request $request)
    {
        // First Query
        $posts = DB::table('posts')
            ->leftJoinSub(
                DB::table('post_tags')
                    ->leftJoin('follow_tags', 'post_tags.tag_id', '=', 'follow_tags.tag_id')
                    ->where('follow_tags.user_id', '=', $request->header("user_id"))
                    ->select('post_tags.post_id'),
                'filtered_post_tags',
                'posts.id',
                'filtered_post_tags.post_id'
            )

            ->leftJoin('follow_users', function ($join) use ($request) {
                $join->on('follow_users.following_user_id', '=', 'posts.user_id')
                    ->where('follow_users.user_id', '=', $request->header("user_id"));
            })

            ->leftJoin('post_tags as pt2', 'posts.id', '=', 'pt2.post_id')
            ->leftJoin('tags as t2', 'pt2.tag_id', '=', 't2.id')
            ->leftJoin('profiles', 'posts.user_id', '=', 'profiles.user_id')
            ->leftJoin('users', 'posts.user_id', '=', 'users.id')
            ->select(
                'posts.id',
                'posts.title',
                'posts.thumbnail',
                'posts.body',
                'posts.active',
                'posts.published_at',
                'posts.user_id',
                'users.name',
                'users.email',
                'profiles.profile_image',
                DB::raw('GROUP_CONCAT(DISTINCT t2.title) as tags')
            )
            ->groupBy(
                'posts.id',
                'posts.title',
                'posts.thumbnail',
                'posts.body',
                'posts.active',
                'posts.published_at',
                'posts.user_id',
                'users.name',
                'users.email',
                'profiles.profile_image'
            )
            ->paginate(10);

        // Fetching comments
        $postIds = $posts->pluck('id')->toArray();

        $comments = DB::table('comments')
            ->leftJoin('users', 'comments.user_id', '=', 'users.id')
            ->leftJoin('profiles', 'comments.user_id', '=', 'profiles.user_id')
            ->whereNull('comments.parent_id')
            ->whereIn('comments.post_id', $postIds)
            ->select(
                'comments.post_id',
                'comments.comment',
                'comments.created_at',
                'profiles.profile_image',
                'users.id as user_id',
                'users.name'
            )
            ->get();

        // // Group comments by post_id
        $commentByPost = $comments->groupBy('post_id');

        foreach ($posts as $post) {
            $postId = $post->id;
            $post->comments = [
                'totalComment' => $commentByPost->has($postId) ? $commentByPost[$postId]->count() : 0,
                'last_comment' => $commentByPost->has($postId) ? $commentByPost[$postId]->first() : [],
            ];
        }

        return $posts;
    }

    public function getTopPost(Request $request)
    {
        $posts = DB::table("posts")
            ->leftJoin('post_likes', 'posts.id', '=', 'post_likes.post_id')
            ->leftJoin("users", "posts.user_id", "=", "users.id")
            ->leftJoin("profiles", "posts.user_id", "=", "profiles.user_id")
            ->select(
                'posts.id',
                'posts.title',
                'posts.thumbnail',
                'posts.body',
                'posts.active',
                'posts.published_at',
                'posts.user_id',
                'users.name',
                'users.email',
                'profiles.profile_image',
                DB::raw('COUNT(DISTINCT post_likes.user_id) as total_likes')
            )
            ->groupBy(
                'posts.id',
                'posts.title',
                'posts.thumbnail',
                'posts.body',
                'posts.active',
                'posts.published_at',
                'posts.user_id',
                'users.name',
                'users.email',
                'profiles.profile_image',
            )
            ->orderByDesc('total_likes')
            ->paginate(10);
        // Fetching comments
        $postIds = $posts->pluck('id')->toArray();

        $comments = DB::table('comments')
            ->leftJoin('users', 'comments.user_id', '=', 'users.id')
            ->leftJoin('profiles', 'comments.user_id', '=', 'profiles.user_id')
            ->whereNull('comments.parent_id')
            ->whereIn('comments.post_id', $postIds)
            ->select(
                'comments.post_id',
                'comments.comment',
                'comments.created_at',
                'profiles.profile_image',
                'users.id as user_id',
                'users.name'
            )
            ->get();

        // // Group comments by post_id
        $commentByPost = $comments->groupBy('post_id');

        foreach ($posts as $post) {
            $postId = $post->id;
            $post->comments = [
                'totalComment' => $commentByPost->has($postId) ? $commentByPost[$postId]->count() : 0,
                'last_comment' => $commentByPost->has($postId) ? $commentByPost[$postId]->first() : [],
            ];
        }

        return $posts;
    }

}

