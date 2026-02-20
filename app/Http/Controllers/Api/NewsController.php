<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::with('sections')->get();

        if ($news->isEmpty()) {
            return response()->json([
                'message' => 'No news found',
                'data' => []
            ], 200);
        }

        return response()->json($news);
    }

    public function store(Request $request)
    {
        if (!$request->user()->hasPermission('manage news')) {
            abort(403, 'You do not have permission to manage news.');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date_created' => 'required|date',
            'author' => 'required|string|max:255',
            'home_item' => 'sometimes|boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'main_photo' => 'nullable|string',
            'sections' => 'nullable|array',
            'sections.*.heading' => 'required|string|max:255',
            'sections.*.descriptions' => 'required|array',
            'sections.*.descriptions.*' => 'string',
            'sections.*.photo' => 'nullable|string',
            'sections.*.photo_description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $sectionsData = $data['sections'] ?? [];
        unset($data['sections']);

        $news = News::create($data);

        foreach ($sectionsData as $index => $section) {
            $news->sections()->create([
                'heading' => $section['heading'],
                'descriptions' => $section['descriptions'],
                'photo' => $section['photo'] ?? null,
                'photo_description' => $section['photo_description'] ?? null,
                'sort_order' => $index,
            ]);
        }

        return response()->json([
            'message' => 'News created successfully',
            'data' => $news->load('sections'),
        ], 201);
    }

    public function show($id)
    {
        $news = News::with('sections')->find($id);

        if (!$news) {
            return response()->json([
                'message' => 'News not found',
                'data' => null
            ], 404);
        }

        return response()->json($news);
    }

    public function update(Request $request, News $news)
    {
        if (!$request->user()->hasPermission('manage news')) {
            abort(403, 'You do not have permission to manage news.');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'date_created' => 'sometimes|required|date',
            'author' => 'sometimes|required|string|max:255',
            'home_item' => 'sometimes|boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'main_photo' => 'nullable|string',
            'sections' => 'nullable|array',
            'sections.*.heading' => 'required|string|max:255',
            'sections.*.descriptions' => 'required|array',
            'sections.*.descriptions.*' => 'string',
            'sections.*.photo' => 'nullable|string',
            'sections.*.photo_description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // If sections are provided, replace them all
        if (isset($data['sections'])) {
            $sectionsData = $data['sections'];
            unset($data['sections']);

            $news->sections()->delete();
            foreach ($sectionsData as $index => $section) {
                $news->sections()->create([
                    'heading' => $section['heading'],
                    'descriptions' => $section['descriptions'],
                    'photo' => $section['photo'] ?? null,
                    'photo_description' => $section['photo_description'] ?? null,
                    'sort_order' => $index,
                ]);
            }
        }

        $news->update($data);
        $news->refresh();

        return response()->json([
            'message' => 'News updated successfully',
            'data' => $news->load('sections'),
        ]);
    }

    public function destroy(Request $request, News $news)
    {
        if (!$request->user()->hasPermission('manage news')) {
            abort(403, 'You do not have permission to manage news.');
        }

        $news->delete();

        return response()->json([
            'message' => 'News deleted successfully',
        ], 200);
    }
}
