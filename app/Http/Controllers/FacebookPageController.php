<?php

namespace App\Http\Controllers;

use App\Models\FacebookPage;
use App\Services\FacebookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FacebookPageController extends Controller
{
    protected $facebookService;

    public function __construct(FacebookService $facebookService)
    {
        $this->facebookService = $facebookService;
    }

    /**
     * Sab connected pages dikhao
     */
    public function index()
    {
        $pages = Auth::user()->facebookPages()->where('is_active', true)->get();
        return response()->json($pages);
    }

    /**
     * Facebook se naye pages fetch aur connect karo
     */
    public function connectPages()
    {
        try {
            $fbPages = $this->facebookService->getAllPages();
            
            if (empty($fbPages)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Koi pages nahi mile'
                ], 400);
            }

            $connectedPages = [];

            foreach ($fbPages as $page) {
                // Agar page already connected nahi hai to save karo
                $existing = FacebookPage::where('page_id', $page['id'])->first();
                
                if (!$existing) {
                    $newPage = FacebookPage::create([
                        'user_id' => Auth::id(),
                        'page_id' => $page['id'],
                        'page_name' => $page['name'],
                        'page_access_token' => $page['access_token'],
                        'page_profile_pic' => $page['picture']['data']['url'] ?? null,
                        'is_active' => true,
                        'connected_at' => now()
                    ]);
                    
                    $connectedPages[] = $newPage;
                } else {
                    // Agar pehle se hai to activate karo
                    $existing->update(['is_active' => true]);
                    $connectedPages[] = $existing;
                }
            }

            return response()->json([
                'success' => true,
                'message' => count($connectedPages) . ' pages connected',
                'pages' => $connectedPages
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Page ko disconnect karo
     */
    public function disconnectPage($pageId)
    {
        try {
            $page = FacebookPage::where('id', $pageId)
                ->where('user_id', Auth::id())
                ->first();

            if (!$page) {
                return response()->json([
                    'success' => false,
                    'message' => 'Page not found'
                ], 404);
            }

            $page->update(['is_active' => false]);

            return response()->json([
                'success' => true,
                'message' => 'Page disconnected'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Specific page details
     */
    public function show($pageId)
    {
        $page = FacebookPage::where('id', $pageId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found'
            ], 404);
        }

        return response()->json($page);
    }
}