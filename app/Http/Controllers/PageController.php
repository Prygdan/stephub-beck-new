<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\SanitizesFields;
use App\Http\Requests\Page\Store;
use App\Http\Requests\Page\Update;
use App\Models\Page;
use App\Services\NextRevalidateService;

class PageController extends Controller
{
    use SanitizesFields;

    public function index()
    {
        return response()->json(Page::orderBy('slug', 'asc')->get(), 200);
    }

    public function show(Page $page)
    {
        return response()->json($page, 200);
    }

    public function store(Store $request, NextRevalidateService $revalidate)
    {
        $page = Page::create($this->sanitizeData($request->validated()));
        $revalidate->tags([
            'page',
        ]);

        return response()->json($page, 200);
    }

    public function update(Update $request, Page $page, NextRevalidateService $revalidate)
    {
        $data = $this->sanitizeData($request->validated());
        $page->update($data);
        $revalidate->tags([
            'page',
        ]);

        return response()->json($page);
    }

    public function destroy(Page $page, NextRevalidateService $revalidate)
    {
        $page->delete();
        $revalidate->tags([
            'page',
        ]);

        return response()->noContent();
    }
}
