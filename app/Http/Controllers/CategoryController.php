<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function getAllCategory(Request $request)
    {
        try {
            $categoryNames = Category::distinct()->pluck("title", "id");
            return $categoryNames;
        } catch (\Exception $e) {
            return response()->json([
                "error" => get_class($e)
            ]);
        }

    }

    public function getCategoryWithUserName(Request $request)
    {
        try {
            $itemsPerPage = 10;

            //Get the total count of items
            $totalItems = DB::table("categories")->count();
            $totalPages = ceil($totalItems / $itemsPerPage);

            $categoryWithName = DB::table('users')
                ->join('categories', 'users.id', "=", "categories.user_id")
                ->select('categories.id', 'categories.title', 'categories.hashtag', 'categories.thumbnail', 'users.name')
                ->orderBy("categories.id", "desc")
                ->limit(10)
                ->offset($request->input("offset"))
                ->get();
            return response()->json([
                'status' => 'success',
                "data" => $categoryWithName,
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

    public function createCategory(Request $request)
    {
        try {
            $img = $request->file('file');
            $t = time();
            $file_name = $img->getClientOriginalName();
            $img_name = "{$request->header('user_id')}-{$t}-{$file_name}";
            $img_url = "uploads/{$img_name}";

            // Upload File
            $img->move(public_path('uploads'), $img_url);
            $categoryCreated = Category::create([
                "title" => $request->input('title'),
                "body" => $request->input("body"),
                "hashtag" => $request->input("hashtag"),
                "thumbnail" => $img_url,
                "user_id" => $request->header('user_id'),
            ]);

            if ($categoryCreated) {
                return response()->json([
                    "status" => "success",
                    "message" => "category has created"
                ], 201);
            } else {
                return response()->json([
                    "status" => "failed",
                    "message" => "Category creation failed"
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

    public function categoryDelete(Request $request)
    {
        try {
            $deletedCategory = Category::where("user_id", $request->header("user_id"))
                ->where("id", $request->input("category_id"))->delete();
            if ($deletedCategory) {
                return response()->json([
                    "status" => "success",
                    "message" => "category has been deleted"
                ], 200);
            } else {
                return response()->json([
                    "status" => "failed",
                    "message" => "Category deletion failed."
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

    public function updateCategory(Request $request)
    {
        try {
            $categoryUpdated = Category::where("id", $request->input("category_id"))
                ->where("user_id", $request->header("user_id"))
                ->update(
                    [
                        "title" => $request->input("title"),
                        // "thumbnail" => $request->input("thumbnail"),
                        "body" => $request->input("body"),
                        "hashtag" => $request->input("hashtag")
                    ]
                );

            if ($categoryUpdated) {
                return response()->json([
                    "status" => "success",
                    "message" => "category has created"
                ], 201);
            } else {
                return response()->json([
                    "status" => "failed",
                    "message" => "Category creation failed"
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

    public function searchCategory(Request $request)
    {
        $itemPerPage = 10;

        $category = $request->input("search");
        $searchedValue = DB::table("categories")->
            where("title", 'LIKE', "%" . $category . "%");
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
}
