<?php

namespace App\Http\Controllers\API\Site;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\User;  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SiteController extends Controller
{
    /**
     * Constructor - Apply middleware
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    // ============================================
    // GET ALL SITES
    // ============================================

    /**
     * Get all sites for the engineer
     *
     * GET /api/v1/sites
     * GET /api/v1/sites? search=site_name&limit=50&page=1
     */
public function index(Request $request)
{
    try {
        $engineer = $request->user(); // Authenticated engineer

        $limit = (int) $request->input('limit', 10); // Items per page
        $page = (int) $request->input('page', 1); // Current page
        $search = $request->input('search', null); // Search query
        $sortKey = $request->input('sort_key', 'created_at'); // Default sorting column
        $sortDirection = $request->input('sort_direction', 'desc'); // Default sorting direction

        $query = Site::where('engineer_id', $engineer->id);

        // Apply search if provided
        if (!empty($search)) {
            $query->where('site_name', 'like', "%{$search}%");
        }

        // Apply sorting
        $allowedSortingKeys = ['site_name', 'created_at'];
        if (in_array($sortKey, $allowedSortingKeys)) {
            $query->orderBy($sortKey, $sortDirection);
        }

        // Paginate results
        $sites = $query->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => $sites->items(), // Paginated site data
            'pagination' => [
                'total' => $sites->total(), // Total number of records
                'per_page' => $sites->perPage(), // Items per page
                'current_page' => $sites->currentPage(), // Current page
                'last_page' => $sites->lastPage(), // Total pages
            ],
        ], 200);
    } catch (\Exception $e) {
        Log::error('SiteController@index error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}

    // ============================================
    // CREATE SITE
    // ============================================

    /**
     * Create a new site
     *
     * POST /api/v1/sites
     * {
     *     "site_name": "Construction Site A"
     * }
     */
    public function store(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'site_name' => 'required|string|max:255|unique:sites,site_name',
            ]);

            $engineer = $request->user();

            // Create site
            $site = Site::create([
                'site_name' => $validated['site_name'],
                'engineer_id' => $engineer->id,
            ]);

            Log::info('Site created: ' . $site->site_name . ' by engineer: ' . $engineer->id);

            return response()->json([
                'success' => true,
                'message' => 'Site created successfully',
                'data' => [
                    'id' => $site->id,
                    'site_name' => $site->site_name,
                    'engineer_name' => $site->engineer->name,
                    'created_at' => $site->created_at->format('Y-m-d H:i:s'),
                ],
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('store site error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create site',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // ============================================
    // GET SINGLE SITE
    // ============================================

    /**
     * Get a single site by ID
     *
     * GET /api/v1/sites/{id}
     */
    public function show(Request $request, $siteId)
    {
        try {
            $engineer = $request->user();

            // Find site and verify ownership
            $site = Site::where('id', $siteId)
                ->where('engineer_id', $engineer->id)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $site->id,
                    'site_name' => $site->site_name,
                    'engineer_name' => $site->engineer->name,
                    'created_at' => $site->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $site->updated_at->format('Y-m-d H:i:s'),
                ],
                'message' => 'Site fetched successfully',
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Site not found',
            ], 404);

        } catch (\Exception $e) {
            Log::error('show site error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch site',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // ============================================
    // UPDATE SITE
    // ============================================

    /**
     * Update a site
     *
     * PUT /api/v1/sites/{id}
     * {
     *     "site_name": "Updated Site Name"
     * }
     */
    public function update(Request $request, $siteId)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'site_name' => 'required|string|max:255|unique:sites,site_name,' . $siteId,
            ]);

            $engineer = $request->user();

            // Find site and verify ownership
            $site = Site::where('id', $siteId)
                ->where('engineer_id', $engineer->id)
                ->firstOrFail();

            // Update site
            $site->update([
                'site_name' => $validated['site_name'],
            ]);

            Log::info('Site updated: ' . $site->site_name . ' by engineer: ' . $engineer->id);

            return response()->json([
                'success' => true,
                'message' => 'Site updated successfully',
                'data' => [
                    'id' => $site->id,
                    'site_name' => $site->site_name,
                    'engineer_name' => $site->engineer->name,
                    'updated_at' => $site->updated_at->format('Y-m-d H:i:s'),
                ],
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Site not found',
            ], 404);

        } catch (\Exception $e) {
            Log::error('update site error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update site',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // ============================================
    // DELETE SITE
    // ============================================

    /**
     * Delete a site
     *
     * DELETE /api/v1/sites/{id}
     */
    public function destroy(Request $request, $siteId)
    {
        try {
            $engineer = $request->user();

            // Find site and verify ownership
            $site = Site::where('id', $siteId)
                ->where('engineer_id', $engineer->id)
                ->firstOrFail();

            $siteName = $site->site_name;

            // Delete site
            $site->delete();

            Log::info('Site deleted: ' . $siteName . ' by engineer: ' . $engineer->id);

            return response()->json([
                'success' => true,
                'message' => 'Site deleted successfully',
                'data' => [
                    'id' => $siteId,
                    'site_name' => $siteName,
                ],
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Site not found',
            ], 404);

        } catch (\Exception $e) {
            Log::error('destroy site error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete site',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}