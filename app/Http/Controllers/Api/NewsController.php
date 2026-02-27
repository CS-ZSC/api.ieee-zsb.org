<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ImageUploadTrait;

class NewsController extends Controller
{
    use ImageUploadTrait;
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
            'main_photo' => $this->getImageValidationRules('default'),
            'sections' => 'nullable|array',
            'sections.*.heading' => 'required|string|max:255',
            'sections.*.descriptions' => 'required|array',
            'sections.*.descriptions.*' => 'string',
            'sections.*.photo' => $this->getImageValidationRules('default'),
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

        // Handle main photo upload
        if ($request->hasFile('main_photo')) {
            $data['main_photo'] = $this->uploadImage($request->file('main_photo'), 'images/news');
        }

        $news = News::create($data);

        foreach ($sectionsData as $index => $section) {
            $sectionPhoto = null;
            if ($request->hasFile("sections.{$index}.photo")) {
                $sectionPhoto = $this->uploadImage($request->file("sections.{$index}.photo"), 'images/news');
            }

            $news->sections()->create([
                'heading' => $section['heading'],
                'descriptions' => $section['descriptions'],
                'photo' => $sectionPhoto,
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
            'main_photo' => $this->getImageValidationRules('default'),
            'sections' => 'nullable|array',
            'sections.*.heading' => 'required|string|max:255',
            'sections.*.descriptions' => 'required|array',
            'sections.*.descriptions.*' => 'string',
            'sections.*.photo' => $this->getImageValidationRules('default'),
            'sections.*.photo_description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Handle main photo upload
        if ($request->hasFile('main_photo')) {
            $this->deleteOldImage($news->main_photo);
            $data['main_photo'] = $this->uploadImage($request->file('main_photo'), 'images/news');
        }

        // If sections are provided, replace them all
        if (isset($data['sections'])) {
            $sectionsData = $data['sections'];
            unset($data['sections']);

            // Delete old section images
            foreach ($news->sections as $oldSection) {
                $this->deleteOldImage($oldSection->photo);
            }
            $news->sections()->delete();

            foreach ($sectionsData as $index => $section) {
                $sectionPhoto = null;
                if ($request->hasFile("sections.{$index}.photo")) {
                    $sectionPhoto = $this->uploadImage($request->file("sections.{$index}.photo"), 'images/news');
                }

                $news->sections()->create([
                    'heading' => $section['heading'],
                    'descriptions' => $section['descriptions'],
                    'photo' => $sectionPhoto,
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

        $this->deleteOldImage($news->main_photo);
        foreach ($news->sections as $section) {
            $this->deleteOldImage($section->photo);
        }
        $news->delete();

        return response()->json([
            'message' => 'News deleted successfully',
        ], 200);
    }
}
