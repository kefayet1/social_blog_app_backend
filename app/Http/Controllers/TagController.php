<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TagController extends Controller
{
    public function getAllTag(Request $request)
    {
        try {
            $tagNames = Tag::all(["id", "title"]);
            return $tagNames;
        } catch (\Exception $e) {
            return response()->json([
                "error" => get_class($e)
            ]);
        }

    }

    public function getTagWithUser(Request $request)
    {
        try {
            $itemsPerPage = 10;

            //Get the total count of items
            $totalItems = DB::table("tags")->count();
            $totalPages = ceil($totalItems / $itemsPerPage);

            $tagWithName = DB::table('users')
                ->join('tags', 'users.id', "=", "tags.user_id")
                ->select('tags.id', 'tags.title', 'tags.hashtag', 'tags.thumbnail', 'users.name')
                ->orderBy("tags.id", "desc")
                ->limit(10)
                ->offset($request->input("offset"))
                ->get();
            return response()->json([
                'status' => 'success',
                "data" => $tagWithName,
                "totalPage" => $totalPages
            ], 200);
        } catch (\Exception $e) {
            Log::error("Database query failed", ['exception' => $e]);


            response()->json([
                "status" => "failed",
                "message" => "not founded",
                "classMessage" => get_class($e)
            ], 404);
        }
    }

    public function createTag(Request $request)
    {
        try {
            $img = $request->file('file');
            $t = time();

            $file_name = $img->getClientOriginalName();
            $img_name = "{$request->header('user_id')}-{$t}-{$file_name}";
            $img_url = "uploads/{$img_name}";

            // Upload File
            $img->move(public_path('uploads'), $img_url);


            $tagCreated = Tag::create([
                "title" => $request->input('title'),
                "body" => $request->input("body"),
                "hashtag" => $request->input("hashtag"),
                "thumbnail" => $img_url,
                "user_id" => $request->header('user_id'),
            ]);

            if ($tagCreated) {
                return response()->json([
                    "status" => "success",
                    "message" => "tag has created"
                ], 201);
            } else {
                return response()->json([
                    "status" => "failed",
                    "message" => "tag creation failed"
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                "status" => "failed",
                "message" => "not created",
                "classMessage" => get_class($e)
            ], 404);
        }
    }

    public function tagDelete(Request $request)
    {
        try {
            $deletedTag = Tag::where("user_id", $request->header("user_id"))
                ->where("id", $request->input("tag_id"))->delete();
            if ($deletedTag) {
                return response()->json([
                    "status" => "success",
                    "message" => "tag has been deleted"
                ], 200);
            } else {
                return response()->json([
                    "status" => "failed",
                    "message" => "tag deletion failed."
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                "status" => "failed",
                "message" => "Unauthorized request",
                "className" => get_class($e)
            ], 404);
        }
    }

    public function updateTag(Request $request)
    {
        try {
            $tagUpdated = Tag::where("id", $request->input("tag_id"))
                ->where("user_id", $request->header("user_id"))
                ->update([
                    "title" => $request->input("title"),
                    // "thumbnail" => $request->input("thumbnail"),
                    "body" => $request->input("body"),
                    "hashtag" => $request->input("hashtag")
                ]);
            if ($tagUpdated) {
                return response()->json([
                    "status" => "success",
                    "message" => "Tag has updated"
                ], 201);
            } else {
                return response()->json([
                    "status" => "failed",
                    "message" => "failed to update"
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                "status" => "failed",
                "message" => "Unauthorized request",
                "className" => get_class($e)
            ], 404);
        }
    }

    public function searchTag(Request $request)
    {
        $itemPerPage = 10;

        $tag = $request->input("search");
        $searchedValue = DB::table("tags")->
            where("title", 'LIKE', "%" . $tag . "%");
        $totalItem = $searchedValue->count();
        $totalPages = ceil($totalItem / $itemPerPage);
        $getSearchItem = $searchedValue
            ->limit(10)
            ->offset($request->input('offset'))
            ->get();
        if ($searchedValue) {
            return response()->json([
                "status" => "success",
                "message" => "Search it successfully",
                "data" => $getSearchItem,
                "totalPage" => $totalPages
            ]);
        }
        return response()->json([
            "status" => "failed",
            "message" => "not found!"
        ], 404);
    }

    public function getTagDetails(Request $request)
    {
        $tagId = DB::table("tags")
            ->leftJoin("follow_tags", "tags.id", "=", "follow_tags.tag_id")
            ->where("tags.title", "=", $request->input("tag_name"))
            ->select(
                "tags.id",
                "tags.title",
                "tags.hashtag",
                "tags.body",
                "tags.thumbnail",
                DB::raw("COUNT(CASE WHEN follow_tags.is_follow = 1 THEN 1 END) as totalFollow")
            )
            ->groupBy("tags.id", "tags.title", "tags.hashtag", "tags.body", "tags.thumbnail", "follow_tags.is_follow")
            ->first();

        if ($request->header("user_id")) {
            $isUserFollow = DB::table("follow_tags")
                ->where("user_id", "=", $request->header("user_id"))
                ->where("tag_id", "=", $tagId->id)
                ->first()->is_follow;
            $combineArray = [
                ...(array) $tagId,
                'is_follow' => $isUserFollow
            ];
        }
        // dd($isUserFollow->is_follow);
        if ($tagId !== (object) []) {
            return response()->json([
                'status' => 'success',
                'data' => $request->header("user_id") ? $combineArray : $tagId
            ]);
        }

        return response()->json([
            'status' => 'failed',
            'data' => "something is wrong!"
        ]);
    }
}
