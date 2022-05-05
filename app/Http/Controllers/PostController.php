<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
   /**
     * @OA\Get(
     *      path="/api/posts",
     *      operationId="getPostList",
     *      tags={"Post"},
     *      summary="Get list of post",
     *      description="Returns list of projects",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function index()
    {
        $posts = auth()->user()->posts;
        // $posts=Post::all();

        return response()->json([
            'success' => true,
            'data' => $posts
        ]);
    }

   /**
     * @OA\Post(
     *      path="/api/posts",
     *      operationId="createPost",
     *      tags={"Post"},
     *      summary="Create new post",
     *      description="Returns post data",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(),
     *          @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"title", "description"},
     *               @OA\Property(property="title", type="text"),
     *               @OA\Property(property="description", type="longText")
     *            ),
     *        ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */

    
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required'
        ]);
        
        $post = new Post();
        $post->title = $request->title;
        $post->description = $request->description;
        
        if (auth()->user()->posts()->save($post))
        return response()->json([
            'success' => true,
            'data' => $post->toArray()
        ]);
        else
        return response()->json([
            'success' => false,
            'message' => 'Post not added'
        ], 500);
    }
   /**
     * @OA\Get(
     *      path="/api/posts/{id}",
     *      operationId="getPostById",
     *      tags={"Post"},
     *      summary="Get post information",
     *      description="Returns post data",
     *      @OA\Parameter(
     *          name="id",
     *          description="Post id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
    public function show($id)
    {
        $post = auth()->user()->posts()->find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found '
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $post->toArray()
        ], 400);
    }

   /**
     * @OA\Put(
     *      path="/api/posts/{id}",
     *      operationId="updatePost",
     *      tags={"Post"},
     *      summary="Update existing post",
     *      description="Returns updated project data",
     *      @OA\Parameter(
     *          name="id",
     *          description="Project id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent()
     *      ),
     *      @OA\Response(
     *          response=202,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */

    public function update(Request $request, $id)
    {
        $post = auth()->user()->posts()->find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 400);
        }

        $updated = $post->fill($request->all())->save();

        if ($updated)
            return response()->json([
                'success' => true
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Post can not be updated'
            ], 500);
    }

   /**
     * @OA\Delete(
     *      path="/api/posts/{id}",
     *      operationId="deletePost",
     *      tags={"Post"},
     *      summary="Delete existing post",
     *      description="Deletes a post and returns no content",
     *      @OA\Parameter(
     *          name="id",
     *          description="Post id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function destroy($id)
    {
        $post = auth()->user()->posts()->find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 400);
        }

        if ($post->delete()) {
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Post can not be deleted'
            ], 500);
        }
    }
}
