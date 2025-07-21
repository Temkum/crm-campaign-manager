<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CampaignDeploymentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CampaignDeploymentController extends Controller
{
    protected CampaignDeploymentService $deploymentService;

    public function __construct(CampaignDeploymentService $deploymentService)
    {
        $this->deploymentService = $deploymentService;
    }

    /**
     * Get all campaigns ready for deployment
     * 
     * @return JsonResponse
     */
    public function getDeployableCampaigns(): JsonResponse
    {
        try {
            $campaigns = $this->deploymentService->prepareCampaignsForDeployment();

            return response()->json([
                'success' => true,
                'data' => $campaigns,
                'count' => count($campaigns),
                'message' => 'Campaigns prepared for deployment successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to prepare campaigns for deployment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get campaigns for specific website deployment
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getCampaignsForWebsite(Request $request): JsonResponse
    {
        $request->validate([
            'website_id' => 'required|integer|exists:websites,id',
        ]);

        try {
            $campaigns = $this->deploymentService->prepareCampaignsForWebsiteDeployment(
                $request->integer('website_id')
            );

            return response()->json([
                'success' => true,
                'data' => $campaigns,
                'count' => count($campaigns),
                'website_id' => $request->integer('website_id'),
                'message' => 'Website-specific campaigns prepared successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to prepare website campaigns',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Deploy specific campaigns (forced deployment)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function deploySpecificCampaigns(Request $request): JsonResponse
    {
        $request->validate([
            'campaign_ids' => 'required|array|min:1',
            'campaign_ids.*' => 'integer|exists:campaigns,id',
        ]);

        try {
            $campaignIds = $request->input('campaign_ids');

            // Validate campaigns first
            $validation = $this->deploymentService->validateCampaignsForDeployment($campaignIds);

            if ($validation['invalid_count'] > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Some campaigns failed validation',
                    'validation_results' => $validation,
                ], 422);
            }

            // Prepare campaigns for deployment
            $campaigns = $this->deploymentService->prepareCampaignsForForcedDeployment($campaignIds);

            return response()->json([
                'success' => true,
                'data' => $campaigns,
                'count' => count($campaigns),
                'deployed_campaign_ids' => $campaignIds,
                'message' => 'Specific campaigns prepared for deployment successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to deploy specific campaigns',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get deployment statistics
     * 
     * @return JsonResponse
     */
    public function getDeploymentStats(): JsonResponse
    {
        try {
            $stats = $this->deploymentService->getDeploymentStatistics();

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Deployment statistics retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve deployment statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Validate campaigns before deployment
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function validateCampaigns(Request $request): JsonResponse
    {
        $request->validate([
            'campaign_ids' => 'required|array|min:1',
            'campaign_ids.*' => 'integer|exists:campaigns,id',
        ]);

        try {
            $validation = $this->deploymentService->validateCampaignsForDeployment(
                $request->input('campaign_ids')
            );

            return response()->json([
                'success' => true,
                'data' => $validation,
                'message' => 'Campaign validation completed',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to validate campaigns',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
