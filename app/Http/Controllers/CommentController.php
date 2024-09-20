<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    //
    // public function getComment(Request $request)
    // {
    //     $parentComments = DB::table("posts")
    //         ->leftJoin('comments as c1', 'posts.id', '=', 'c1.post_id')
    //         ->leftJoin('users', 'c1.user_id', '=', 'users.id')
    //         ->where('posts.id', '=', $request->input('id'))
    //         ->join('comments as c2', 'c1.id', '=', 'c2.parent_id')
    //         ->where('posts.id', '=', $request->input('id'))
    //         ->select('c1.*', 'users.name')
    //         ->get();
    //     $childComments = DB::table("posts")
    //         ->leftJoin('comments as c1', 'posts.id', '=', 'c1.post_id')
    //         ->leftJoin('users', 'c1.user_id', '=', 'users.id')
    //         ->where('posts.id', '=', $request->input('id'))
    //         ->join('comments as c2', 'c1.id', '=', 'c2.parent_id')
    //         ->where('posts.id', '=', $request->input('id'))
    //         ->select('c2.*', 'users.name')
    //         ->get();

    //     foreach($parentComments as $pComnt){
    //         $pComnt->childComments = collect();
    //         foreach($childComments as $cComnt){
    //             if($pComnt->id == $cComnt->parent_id){
    //                 $pComnt->childComments[] = $cComnt;
    //             }
    //         }
    //     }

    //     dd($parentComments);
    //     return $parentComment;
    // }

    public function getComment(Request $request)
    {
        $comments = DB::table("comments")
            ->leftJoin('users', 'comments.user_id', "=", 'users.id')
            ->leftJoin("profiles", "comments.user_id", "=", "profiles.user_id")
            ->where('comments.post_id', '=', $request->input('id'))
            ->select('comments.*', 'users.name', "profiles.profile_image")
            ->get();
        $groupComments = $comments->groupBy('parent_id');

        function buildCommentTree($comments, $parentId = null)
        {
            $branch = collect();

            if ($comments->has($parentId)) {
                foreach ($comments->get($parentId) as $comment) {
                    $children = buildCommentTree($comments, $comment->id);
                    if ($children->isNotEmpty()) {
                        $comment->childComments = $children;
                    } else {
                        $comment->childComments = collect();
                    }
                    $branch->push($comment);
                }
            }

            return $branch;
        }

        $commentTree = buildCommentTree($groupComments, null);
        return $commentTree;
    }

    public function createComment(Request $request) {
        $comment = Comment::create([
            'comment' => $request->input("comment"),
            'post_id' => $request->input('postId'),
            'user_id' => $request->header('user_id'),
            'parent_id' => $request->input('parentId'),
        ]);

         return $comment;
    }


}
