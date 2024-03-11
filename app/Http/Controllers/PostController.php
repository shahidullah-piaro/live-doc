<?php

namespace App\Http\Controllers;

use App\Exceptions\GeneralJsonException;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use App\Notifications\PostSharedNotification;
use App\Repositories\PostRepository;
use App\Rules\IntegerArray;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;

class PostController extends Controller
{
    /**
     * Display a listing of posts.
     *
     * Gets a list of posts.
     *
     * @queryParam page_size int Size per page. Defaults to 20. Example: 20
     * @queryParam page int Page to view. Example: 1
     *
     * @apiResourceCollection App\Http\Resources\PostResource
     * @apiResourceModel App\Models\Post
     * @return ResourceCollection
     */
    public function index(Request $request)
    {
        $pageSize = $request->page_size ?? 20;
        $posts = Post::query()->paginate($pageSize);

        return PostResource::collection($posts);
    }

    /**
     * Store a newly created post in storage.
     * @bodyParam title string required Title of the post. Example: Amazing Post
     * @bodyParam body string[] required Body of the post. Example: ["This post is super beautiful"]
     * @bodyParam user_ids int[] required The author ids of the post. Example: [1, 2]
     * @apiResource App\Http\Resources\PostResource
     * @apiResourceModel App\Models\Post
     * @param StorePostRequest  $request
     * @return PostResource
     */
    public function store(StorePostRequest $request, PostRepository $repository)
    {
        $payload = $request->only([
            'title',
            'body',
            'user_ids'
        ]);
        Validator::validate($payload, [
            'title' => 'string|required',
            'body' => ['array','required'],
            'user_ids' => [
                'array',
                'required',
                function($attribute, $value, $fail){
                    $integerOnly = collect($value)->every(fn ($element) => is_int($element));
 
                    if(!$integerOnly){
                        $fail($attribute . ' can only be integers.');
                    }
                }
            ]
        ], [
            'body.required' => "Please enter a value for body.",
            'title.string' => 'HEYYYY use a string',
        ], [
            'user_ids' => 'USERR IDDD'
        ]);


        $created = $repository->create($payload);

        return new PostResource($created);
    }

    /**
     * Display the specified post.
     * @apiResource App\Http\Resources\PostResource
     * @apiResourceModel App\Models\Post
     * @param  \App\Models\Post  $post
     * @return PostResource
     */
    public function show(Post $post)
    {
        return new PostResource($post);
    }

    /**
     * Update the specified post in storage.
     * @bodyParam title string required Title of the post. Example: Amazing Post
     * @bodyParam body string[] required Body of the post. Example: ["This post is super beautiful"]
     * @bodyParam user_ids int[] required The author ids of the post. Example: [1, 2]
     * @apiResource App\Http\Resources\PostResource
     * @apiResourceModel App\Models\Post
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return PostResource | JsonResponse
     */
    public function update(Request $request, Post $post, PostRepository $repository)
    {

        $payload = $request->only([
            'title',
            'body',
            'user_ids'
        ]);
        Validator::validate($payload, [
            'title' => 'string|required',
            'body' => ['array','required'],
            'user_ids' => [
                'array',
                'required',
                function($attribute, $value, $fail){
                    $integerOnly = collect($value)->every(fn ($element) => is_int($element));
 
                    if(!$integerOnly){
                        $fail($attribute . ' can only be integers.');
                    }
                }
            ]
        ], [
            'body.required' => "Please enter a value for body.",
            'title.string' => 'HEYYYY use a string',
        ], [
            'user_ids' => 'USERR IDDD'
        ]);
        

        $post = $repository->update($post, $payload);
        return new PostResource($post);

    }

    /**
     * Remove the specified post from storage.
     * @response 200 {
        "data": "success"
     * }
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Post $post, PostRepository $repository)
    {
        $post = $repository->forceDelete($post);
        return new JsonResponse([
            'data' => 'success'
        ]);
    }
}
