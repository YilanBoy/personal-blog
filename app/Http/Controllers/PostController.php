<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Services\PostService;
use App\Services\FormatTransferService;

class PostController extends Controller
{
    protected $post, $category, $postService, $formatTransferService;

    public function __construct(
        Post $post,
        Category $category,
        PostService $postService,
        FormatTransferService $formatTransferService
    ) {
        $this->post = $post;
        $this->category = $category;
        $this->postService = $postService;
        $this->formatTransferService = $formatTransferService;
    }

    // 文章列表
    public function index(Request $request)
    {
        $posts = $this->post->withOrder($request->order)
            ->with('user', 'category', 'tags') // 預加載防止 N+1 問題
            ->paginate(10);

        return view('posts.index', ['posts' => $posts]);
    }

    // 文章內容
    public function show(Request $request, Post $post)
    {
        // URL 修正，使用帶 slug 的網址
        if (!empty($post->slug) && $post->slug !== $request->slug) {
            return redirect($post->link_with_slug, 301);
        }

        return view('posts.show', ['post' => $post]);
    }

    // 新增文章
    public function store(PostRequest $request)
    {
        $this->post->fill($request->validated());
        $this->post->user_id = auth()->user()->id;
        $this->post->slug = $this->postService->makeSlug($request->title);
        $this->post->save();

        // 將傳過來的 JSON 資料轉成 array
        $tagIdsArray = $this->formatTransferService->tagsJsonToTagIdsArray($request->tags);

        // 在關聯表新增關聯
        $this->post->tags()->attach($tagIdsArray);

        return redirect()->to($this->post->link_with_slug)->with('success', '成功新增文章！');
    }

    // 文章編輯頁面
    public function edit(Post $post)
    {
        // 只能編輯自己發佈的文章，規則寫在 PostPolicy
        $this->authorize('update', $post);

        // 生成包含 tag ID 與 tag name 的 Array
        // [["id" => "2","value" => "C#"], ["id" => "5","value" => "Dart"]]
        $tagsArray = $post->tags->map(function ($tag) {
            return ['id' => $tag->id, 'value' => $tag->name];
        })->all();

        // 轉成 tagify 的 JSON 格式
        // [{"id":"2","value":"C#"},{"id":"5","value":"Dart"}]
        $post->tagsJson = json_encode($tagsArray, JSON_UNESCAPED_UNICODE);

        return view('posts.edit', ['post' => $post]);
    }

    // 更新文章
    public function update(PostRequest $request, Post $post)
    {
        $this->authorize('update', $post);

        $post->slug = $this->postService->makeSlug($request->title);
        $post->update($request->validated());

        $tagIdsArray = $this->formatTransferService->tagsJsonToTagIdsArray($request->tags);

        // 關聯表更新
        $post->tags()->sync($tagIdsArray);

        return redirect()->to($post->link_with_slug)->with('success', '成功更新文章！');
    }

    // 刪除文章
    public function destroy(Post $post)
    {
        $this->authorize('destroy', $post);

        $post->delete();

        return redirect()->route('posts.index')->with('success', '成功刪除文章！');
    }
}
