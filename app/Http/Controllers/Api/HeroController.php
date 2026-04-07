<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use App\Models\HeroStatistic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class HeroController extends Controller
{
    /**
     * GET /api/hero
     *
     * Returns active slides + statistics for a given site.
     * Query param: ?site=waleda|manzuma   (default: waleda)
     *
     * Response is cached for 5 minutes. Cache is busted on any write operation.
     */
    public function index(Request $request): JsonResponse
    {
        $site = $request->query('site', 'waleda');

        if (! in_array($site, ['waleda', 'manzuma'])) {
            return response()->json(['message' => 'Invalid site parameter.'], 422);
        }

        $cacheKey = "hero:{$site}";

        $data = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($site) {
            $slides = HeroSlide::active()
                ->forSite($site)
                ->get()
                ->map(fn (HeroSlide $s) => $this->slideResource($s));

            $statistics = HeroStatistic::active()
                ->forSite($site)
                ->get()
                ->map(fn (HeroStatistic $s) => $this->statisticResource($s));

            return compact('slides', 'statistics');
        });

        return response()->json($data);
    }

    /**
     * GET /api/hero/slides
     *
     * Returns paginated slides list (admin / internal use).
     */
    public function slides(Request $request): JsonResponse
    {
        $site = $request->query('site', 'waleda');

        $slides = HeroSlide::query()
            ->when($site !== 'all', fn ($q) => $q->forSite($site))
            ->orderBy('sort_order')
            ->paginate(20);

        return response()->json($slides);
    }

    /**
     * GET /api/hero/slides/{id}
     */
    public function showSlide(int $id): JsonResponse
    {
        $slide = HeroSlide::findOrFail($id);

        return response()->json($this->slideResource($slide));
    }

    /**
     * POST /api/hero/slides
     * Body: all slide fields
     */
    public function storeSlide(Request $request): JsonResponse
    {
        $data = $this->validateSlide($request);

        // Handle file uploads if sent as base64 or multipart
        if ($request->hasFile('background_image')) {
            $data['background_image'] = $request->file('background_image')
                ->store('hero/desktop', 'public');
        }

        if ($request->hasFile('background_image_mobile')) {
            $data['background_image_mobile'] = $request->file('background_image_mobile')
                ->store('hero/mobile', 'public');
        }

        $slide = HeroSlide::create($data);

        $this->bustCache($slide->site);

        return response()->json($this->slideResource($slide), 201);
    }

    /**
     * PUT /api/hero/slides/{id}
     */
    public function updateSlide(Request $request, int $id): JsonResponse
    {
        $slide = HeroSlide::findOrFail($id);
        $oldSite = $slide->site;

        $data = $this->validateSlide($request, $id);

        if ($request->hasFile('background_image')) {
            // Delete old image
            if ($slide->background_image) {
                Storage::disk('public')->delete($slide->background_image);
            }
            $data['background_image'] = $request->file('background_image')
                ->store('hero/desktop', 'public');
        }

        if ($request->hasFile('background_image_mobile')) {
            if ($slide->background_image_mobile) {
                Storage::disk('public')->delete($slide->background_image_mobile);
            }
            $data['background_image_mobile'] = $request->file('background_image_mobile')
                ->store('hero/mobile', 'public');
        }

        $slide->update($data);

        // Bust cache for old and new site if site changed
        $this->bustCache($oldSite);
        $this->bustCache($slide->site);

        return response()->json($this->slideResource($slide->fresh()));
    }

    /**
     * DELETE /api/hero/slides/{id}
     */
    public function destroySlide(int $id): JsonResponse
    {
        $slide = HeroSlide::findOrFail($id);
        $site  = $slide->site;

        // Delete associated media from storage
        if ($slide->background_image) {
            Storage::disk('public')->delete($slide->background_image);
        }
        if ($slide->background_image_mobile) {
            Storage::disk('public')->delete($slide->background_image_mobile);
        }

        $slide->delete(); // soft delete

        $this->bustCache($site);

        return response()->json(['message' => 'Slide deleted successfully.']);
    }

    /**
     * PATCH /api/hero/slides/reorder
     * Body: { "order": [3, 1, 5, 2] }  — array of slide IDs in new order
     */
    public function reorderSlides(Request $request): JsonResponse
    {
        $request->validate([
            'order'   => ['required', 'array'],
            'order.*' => ['integer', 'exists:hero_slides,id'],
            'site'    => ['required', Rule::in(['waleda', 'manzuma'])],
        ]);

        foreach ($request->order as $index => $slideId) {
            HeroSlide::where('id', $slideId)->update(['sort_order' => $index]);
        }

        $this->bustCache($request->site);

        return response()->json(['message' => 'Slides reordered successfully.']);
    }

    /**
     * PATCH /api/hero/slides/{id}/toggle
     * Toggles is_active flag.
     */
    public function toggleSlide(int $id): JsonResponse
    {
        $slide = HeroSlide::findOrFail($id);
        $slide->update(['is_active' => ! $slide->is_active]);

        $this->bustCache($slide->site);

        return response()->json([
            'id'        => $slide->id,
            'is_active' => $slide->is_active,
        ]);
    }

    // ─── Statistics endpoints ──────────────────────────────────────────────────

    /**
     * GET /api/hero/statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $site = $request->query('site', 'waleda');

        $stats = HeroStatistic::active()
            ->forSite($site)
            ->get()
            ->map(fn (HeroStatistic $s) => $this->statisticResource($s));

        return response()->json($stats);
    }

    /**
     * POST /api/hero/statistics
     */
    public function storeStat(Request $request): JsonResponse
    {
        $data = $request->validate([
            'site'       => ['required', Rule::in(['waleda', 'manzuma', 'both'])],
            'icon'       => ['nullable', 'string', 'max:100'],
            'value'      => ['required', 'string', 'max:50'],
            'label_ar'   => ['required', 'string', 'max:100'],
            'label_en'   => ['required', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer'],
            'is_active'  => ['boolean'],
        ]);

        $stat = HeroStatistic::create($data);

        $this->bustCache($stat->site);

        return response()->json($this->statisticResource($stat), 201);
    }

    /**
     * PUT /api/hero/statistics/{id}
     */
    public function updateStat(Request $request, int $id): JsonResponse
    {
        $stat = HeroStatistic::findOrFail($id);
        $oldSite = $stat->site;

        $data = $request->validate([
            'site'       => ['required', Rule::in(['waleda', 'manzuma', 'both'])],
            'icon'       => ['nullable', 'string', 'max:100'],
            'value'      => ['required', 'string', 'max:50'],
            'label_ar'   => ['required', 'string', 'max:100'],
            'label_en'   => ['required', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer'],
            'is_active'  => ['boolean'],
        ]);

        $stat->update($data);

        $this->bustCache($oldSite);
        $this->bustCache($stat->site);

        return response()->json($this->statisticResource($stat->fresh()));
    }

    /**
     * DELETE /api/hero/statistics/{id}
     */
    public function destroyStat(int $id): JsonResponse
    {
        $stat = HeroStatistic::findOrFail($id);
        $site = $stat->site;

        $stat->delete();

        $this->bustCache($site);

        return response()->json(['message' => 'Statistic deleted successfully.']);
    }

    /**
     * PATCH /api/hero/statistics/reorder
     * Body: { "order": [2, 1, 3], "site": "waleda" }
     */
    public function reorderStats(Request $request): JsonResponse
    {
        $request->validate([
            'order'   => ['required', 'array'],
            'order.*' => ['integer', 'exists:hero_statistics,id'],
            'site'    => ['required', Rule::in(['waleda', 'manzuma'])],
        ]);

        foreach ($request->order as $index => $statId) {
            HeroStatistic::where('id', $statId)->update(['sort_order' => $index]);
        }

        $this->bustCache($request->site);

        return response()->json(['message' => 'Statistics reordered successfully.']);
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    private function slideResource(HeroSlide $slide): array
    {
        return [
            'id'                          => $slide->id,
            'site'                        => $slide->site,
            'title_ar'                    => $slide->title_ar,
            'title_en'                    => $slide->title_en,
            'subtitle_ar'                 => $slide->subtitle_ar,
            'subtitle_en'                 => $slide->subtitle_en,
            'background_image_url'        => $slide->background_image_url,
            'background_image_mobile_url' => $slide->background_image_mobile_url,
            'background_video_url'        => $slide->background_video_url,
            'overlay_color'               => $slide->overlay_color,
            'overlay_opacity'             => $slide->overlay_opacity,
            'overlay_css'                 => $slide->overlay_css,
            'transition_effect'           => $slide->transition_effect,
            'display_duration'            => $slide->display_duration,
            'cta_primary'  => [
                'text_ar' => $slide->cta_primary_text_ar,
                'text_en' => $slide->cta_primary_text_en,
                'url'     => $slide->cta_primary_url,
                'style'   => $slide->cta_primary_style,
            ],
            'cta_secondary' => [
                'visible' => $slide->cta_secondary_visible,
                'text_ar' => $slide->cta_secondary_text_ar,
                'text_en' => $slide->cta_secondary_text_en,
                'url'     => $slide->cta_secondary_url,
            ],
            'controls' => [
                'autoplay'    => $slide->autoplay,
                'loop'        => $slide->loop,
                'show_arrows' => $slide->show_arrows,
                'show_dots'   => $slide->show_dots,
            ],
            'sort_order' => $slide->sort_order,
            'is_active'  => $slide->is_active,
            'updated_at' => $slide->updated_at?->toISOString(),
        ];
    }

    private function statisticResource(HeroStatistic $stat): array
    {
        return [
            'id'         => $stat->id,
            'site'       => $stat->site,
            'icon'       => $stat->icon,
            'value'      => $stat->value,
            'label_ar'   => $stat->label_ar,
            'label_en'   => $stat->label_en,
            'sort_order' => $stat->sort_order,
            'is_active'  => $stat->is_active,
        ];
    }

    private function validateSlide(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'site'                   => ['required', Rule::in(['waleda', 'manzuma', 'both'])],
            'title_ar'               => ['required', 'string', 'max:255'],
            'title_en'               => ['required', 'string', 'max:255'],
            'subtitle_ar'            => ['nullable', 'string'],
            'subtitle_en'            => ['nullable', 'string'],
            'background_image'       => ['nullable', 'image', 'max:10240'],
            'background_image_mobile'=> ['nullable', 'image', 'max:10240'],
            'background_video_url'   => ['nullable', 'url', 'max:500'],
            'overlay_color'          => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'overlay_opacity'        => ['nullable', 'integer', 'min:0', 'max:100'],
            'transition_effect'      => ['nullable', Rule::in(['fade', 'slide', 'zoom', 'ken_burns'])],
            'display_duration'       => ['nullable', 'integer', 'min:2', 'max:30'],
            'cta_primary_text_ar'    => ['nullable', 'string', 'max:255'],
            'cta_primary_text_en'    => ['nullable', 'string', 'max:255'],
            'cta_primary_url'        => ['nullable', 'url'],
            'cta_primary_style'      => ['nullable', Rule::in(['solid', 'outline', 'ghost'])],
            'cta_secondary_text_ar'  => ['nullable', 'string', 'max:255'],
            'cta_secondary_text_en'  => ['nullable', 'string', 'max:255'],
            'cta_secondary_url'      => ['nullable', 'url'],
            'cta_secondary_visible'  => ['boolean'],
            'autoplay'               => ['boolean'],
            'loop'                   => ['boolean'],
            'show_arrows'            => ['boolean'],
            'show_dots'              => ['boolean'],
            'sort_order'             => ['nullable', 'integer', 'min:0'],
            'is_active'              => ['boolean'],
        ]);
    }

    /**
     * Bust cache for a site. Also busts 'both' since it overlaps.
     */
    private function bustCache(string $site): void
    {
        Cache::forget("hero:{$site}");
        Cache::forget('hero:waleda');
        Cache::forget('hero:manzuma');
    }
}