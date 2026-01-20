<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\FacebookPage;
use App\Services\FacebookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ConversationController extends Controller
{
    protected $facebookService;

    public function __construct(FacebookService $facebookService)
    {
        $this->facebookService = $facebookService;
    }

    /**
     * Specific page ke sab conversations with search
     */
    public function index(Request $request, $pageId)
    {
        try {
            $page = FacebookPage::find($pageId);

            if (!$page || $page->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Page not found'
                ], 404);
            }

            // Build query
            $query = Conversation::where('page_id', $pageId);

            // Search filter
            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('customer_name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('customer_psid', 'like', '%' . $searchTerm . '%')
                      ->orWhere('last_message_preview', 'like', '%' . $searchTerm . '%');
                });
            }

            // Archive filter
            $showArchived = $request->get('archived', false);
            $query->where('is_archived', $showArchived);

            // Order by last message time
            $conversations = $query->orderBy('last_message_time', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $conversations
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Facebook se conversations sync karo aur database mein save karo
     */
    public function syncConversations($pageId)
    {
        try {
            Log::info('Sync conversations called', [
                'page_id' => $pageId,
                'user_id' => Auth::id(),
                'authenticated' => Auth::check()
            ]);

            $page = FacebookPage::find($pageId);

            if (!$page || $page->user_id !== Auth::id()) {
                Log::error('Page not found or unauthorized', [
                    'page_id' => $pageId,
                    'user_id' => Auth::id(),
                    'page_exists' => $page !== null,
                    'page_user_id' => $page->user_id ?? null
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Page not found'
                ], 404);
            }

            // Facebook se conversations fetch karo
            $fbConversations = $this->facebookService->getPageConversations(
                $page->page_access_token,
                50
            );

            if (empty($fbConversations)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Koi naye conversations nahi',
                    'count' => 0
                ]);
            }

            $syncedCount = 0;

            foreach ($fbConversations as $fbConv) {
                // Check if conversation already exists
                $existing = Conversation::where('conversation_id', $fbConv['id'])->first();

                if (!$existing) {
                    // Get first message for preview and customer PSID
                    $messages = $this->facebookService->getConversationMessages(
                        $fbConv['id'],
                        $page->page_access_token,
                        5 // Get a few messages to find customer PSID
                    );

                    $lastMessage = $messages[0] ?? null;

                    // Get customer info from senders or from messages
                    $customerName = isset($fbConv['senders']['data'][0]['name'])
                        ? $fbConv['senders']['data'][0]['name']
                        : (isset($fbConv['senders'][0]['name']) ? $fbConv['senders'][0]['name'] : 'Unknown Customer');

                    $customerPsid = isset($fbConv['senders']['data'][0]['id'])
                        ? $fbConv['senders']['data'][0]['id']
                        : (isset($fbConv['senders'][0]['id']) ? $fbConv['senders'][0]['id'] : null);

                    // Try to extract real Facebook ID from profile picture URL
                    $customerFbId = null;
                    if ($customerPsid) {
                        $customerFbId = $this->facebookService->extractRealFbId($customerPsid, $page->page_access_token);
                    }

                    // Get customer profile picture from senders data
                    // If picture is not available in senders, construct Facebook profile picture URL using PSID
                    $customerProfilePic = isset($fbConv['senders']['data'][0]['picture']['data']['url'])
                        ? $fbConv['senders']['data'][0]['picture']['data']['url']
                        : (isset($fbConv['senders'][0]['picture']['data']['url']) ? $fbConv['senders'][0]['picture']['data']['url'] : null);

                    // Fallback: Use Facebook's PSID-based profile picture URL
                    if (!$customerProfilePic && $customerPsid) {
                        $customerProfilePic = "https://graph.facebook.com/{$customerPsid}/picture?type=large";
                    }

                    // If PSID not found in senders, try to get from messages
                    if (!$customerPsid && !empty($messages)) {
                        foreach ($messages as $msg) {
                            if (isset($msg['from']['id']) && $msg['from']['id'] !== $page->page_id) {
                                $customerPsid = $msg['from']['id'];
                                if (isset($msg['from']['name'])) {
                                    $customerName = $msg['from']['name'];
                                }
                                break;
                            }
                        }
                    }

                    Log::info('Creating conversation', [
                        'conversation_id' => $fbConv['id'],
                        'customer_name' => $customerName,
                        'customer_psid' => $customerPsid,
                        'customer_fb_id' => $customerFbId,
                        'has_senders' => isset($fbConv['senders']),
                        'has_profile_pic' => !empty($customerProfilePic)
                    ]);

                    Conversation::create([
                        'user_id' => Auth::id(),
                        'page_id' => $pageId,
                        'conversation_id' => $fbConv['id'],
                        'customer_name' => $customerName,
                        'customer_psid' => $customerPsid,
                        'customer_fb_id' => $customerFbId,
                        'customer_profile_pic' => $customerProfilePic,
                        'last_message_preview' => $lastMessage['message'] ?? 'No message',
                        'last_message_time' => now(),
                    ]);

                    $syncedCount++;
                } else {
                    // Update existing conversation
                    $messages = $this->facebookService->getConversationMessages(
                        $fbConv['id'],
                        $page->page_access_token,
                        1
                    );

                    $lastMessage = $messages[0] ?? null;

                    // Get customer profile picture and FB ID from senders data if not already set
                    $updateData = [
                        'last_message_preview' => $lastMessage['message'] ?? 'No message',
                        'last_message_time' => now(),
                    ];

                    // Try to extract real FB ID if not already set
                    if (!$existing->customer_fb_id && $existing->customer_psid) {
                        $realFbId = $this->facebookService->extractRealFbId($existing->customer_psid, $page->page_access_token);
                        if ($realFbId) {
                            $updateData['customer_fb_id'] = $realFbId;
                        }
                    }

                    if (!$existing->customer_profile_pic) {
                        if (isset($fbConv['senders']['data'][0]['picture']['data']['url'])) {
                            $updateData['customer_profile_pic'] = $fbConv['senders']['data'][0]['picture']['data']['url'];
                        } elseif (isset($fbConv['senders'][0]['picture']['data']['url'])) {
                            $updateData['customer_profile_pic'] = $fbConv['senders'][0]['picture']['data']['url'];
                        } elseif ($existing->customer_psid) {
                            // Fallback: Use Facebook's PSID-based profile picture URL
                            $updateData['customer_profile_pic'] = "https://graph.facebook.com/{$existing->customer_psid}/picture?type=large";
                        }
                    }

                    $existing->update($updateData);
                }
            }

            return response()->json([
                'success' => true,
                'message' => $syncedCount . ' naye conversations synced',
                'count' => $syncedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Sync conversations error', [
                'page_id' => $pageId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Conversation ko archive karo
     */
    public function archive($conversationId)
    {
        try {
            $conversation = Conversation::find($conversationId);

            if (!$conversation || $conversation->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conversation not found'
                ], 404);
            }

            $conversation->update(['is_archived' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Conversation archived'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Conversation ko un-archive karo
     */
    public function unarchive($conversationId)
    {
        try {
            $conversation = Conversation::find($conversationId);

            if (!$conversation || $conversation->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conversation not found'
                ], 404);
            }

            $conversation->update(['is_archived' => false]);

            return response()->json([
                'success' => true,
                'message' => 'Conversation unarchived'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}