<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Article::published()->with('author:id,name');

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        $articles = $query->orderByDesc('published_at')->paginate(12);

        return response()->json($articles);
    }

    public function show(string $slug): JsonResponse
    {
        $article = Article::where('slug', $slug)->published()->firstOrFail();
        $article->increment('view_count');
        $article->load('author:id,name');

        return response()->json(['data' => $article]);
    }

    public function featured(): JsonResponse
    {
        $articles = Article::published()
            ->where('is_featured', true)
            ->with('author:id,name')
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        return response()->json(['data' => $articles]);
    }
}
