# 🚀 Campaign Deployment Service Usage Guide

## Overview

The `CampaignDeploymentService` transforms campaign data into a standardized JavaScript object format ready for deployment to websites or external services.

## Installation & Setup

### Step 1: Create the Service

Place the `CampaignDeploymentService.php` file in `app/Services/`

### Step 2: Register Service (Optional)

Add to `AppServiceProvider.php`:

```php
public function register()
{
    $this->app->singleton(CampaignDeploymentService::class);
}
```

### Step 3: Add API Routes

In `routes/api.php`:

```php
Route::prefix('campaigns/deployment')->group(function () {
    Route::get('/', [CampaignDeploymentController::class, 'getDeployableCampaigns']);
    Route::get('/website', [CampaignDeploymentController::class, 'getCampaignsForWebsite']);
    Route::post('/deploy', [CampaignDeploymentController::class, 'deploySpecificCampaigns']);
    Route::get('/stats', [CampaignDeploymentController::class, 'getDeploymentStats']);
    Route::post('/validate', [CampaignDeploymentController::class, 'validateCampaigns']);
});
```

## Main Methods

### 1. **Basic Deployment Preparation**

```php
use App\Services\CampaignDeploymentService;

$service = new CampaignDeploymentService();

// Get all deployable campaigns (active/scheduled, within date range)
$campaigns = $service->prepareCampaignsForDeployment();
```

### 2. **Force Deploy Specific Campaigns**

```php
// Deploy specific campaigns regardless of status
$campaignIds = [1, 5, 10];
$campaigns = $service->prepareCampaignsForForcedDeployment($campaignIds);
```

### 3. **Website-Specific Deployment**

```php
// Get campaigns for a specific website
$websiteId = 3;
$campaigns = $service->prepareCampaignsForWebsiteDeployment($websiteId);
```

### 4. **Validation Before Deployment**

```php
// Validate campaigns before deploying
$validation = $service->validateCampaignsForDeployment([1, 2, 3]);

if ($validation['invalid_count'] > 0) {
    // Handle validation errors
    foreach ($validation['invalid_campaigns'] as $campaignId => $issues) {
        echo "Campaign {$issues['campaign_name']}: " . implode(', ', $issues['issues']);
    }
}
```

## API Endpoints

### **GET** `/api/campaigns/deployment`

Get all deployable campaigns

```json
{
    "success": true,
    "data": [
        {
            "id": 5,
            "name": "Campaign 5",
            "start_at": "2025-07-12T06:14:00.000000Z",
            "end_at": "2025-08-01T03:26:00.000000Z",
            "status": "active",
            "priority": 1,
            "dom_selector": "ozj,d",
            "custom_affiliate_url": "https://google.fr",
            "timer_offset": 1,
            "trigers": [
                {
                    "id": 9,
                    "type": "browser",
                    "value": "3",
                    "operator": "equals"
                }
            ]
        }
    ],
    "count": 1
}
```

### **GET** `/api/campaigns/deployment/website?website_id=3`

Get campaigns for specific website

```json
{
  "success": true,
  "data": [...],
  "website_id": 3,
  "count": 2
}
```

### **POST** `/api/campaigns/deployment/deploy`

Deploy specific campaigns

```json
// Request
{
  "campaign_ids": [1, 2, 3]
}

// Response
{
  "success": true,
  "data": [...],
  "deployed_campaign_ids": [1, 2, 3],
  "count": 3
}
```

### **POST** `/api/campaigns/deployment/validate`

Validate campaigns

```json
// Request
{
  "campaign_ids": [1, 2, 3]
}

// Response
{
  "success": true,
  "data": {
    "valid_campaigns": [1, 2],
    "invalid_campaigns": {
      "3": {
        "campaign_name": "Test Campaign",
        "issues": ["No triggers configured", "Campaign has already ended"]
      }
    },
    "valid_count": 2,
    "invalid_count": 1
  }
}
```

## Output Format

Each campaign is transformed into this standardized format:

```json
{
    "id": 5,
    "name": "Campaign Name",
    "start_at": "2025-07-12T06:14:00.000000Z",
    "end_at": "2025-08-01T03:26:00.000000Z",
    "status": "active",
    "priority": 1,
    "dom_selector": "css-selector",
    "custom_affiliate_url": "https://example.com",
    "timer_offset": 1,
    "trigers": [
        {
            "id": 9,
            "type": "browser",
            "value": "3",
            "operator": "equals"
        }
    ]
}
```

## Configuration Options

### **Deployment Criteria**

Campaigns are considered deployable if they:

-   Have status `active` or `scheduled`
-   Start date is today or in the past
-   End date is in the future
-   Have at least one trigger configured
-   Have at least one website configured

### **Priority Handling**

-   Campaigns are ordered by priority (descending) then start date (ascending)
-   Website-specific configurations override campaign defaults
-   Highest priority website configuration is used when multiple websites exist

### **Data Sources**

-   `dom_selector`: Website-specific > Campaign default
-   `custom_affiliate_url`: Website-specific only
-   `timer_offset`: Website-specific > Campaign duration
-   `priority`: Website-specific > Campaign priority

## Advanced Usage

### **Scheduled Deployment**

```php
// In a scheduled command or job
class DeployCampaignsCommand extends Command
{
    public function handle(CampaignDeploymentService $service)
    {
        $campaigns = $service->prepareCampaignsForDeployment();

        foreach ($campaigns as $campaign) {
            // Send to deployment service
            $this->deployCampaignToWebsites($campaign);
        }

        $this->info('Deployed ' . count($campaigns) . ' campaigns');
    }
}
```

### **Website Integration**

```php
// Deploy to specific website
$websiteId = 5;
$campaigns = $service->prepareCampaignsForWebsiteDeployment($websiteId);

// Send formatted data to website API
Http::post("https://website.com/api/campaigns", [
    'campaigns' => $campaigns,
    'website_id' => $websiteId,
]);
```

## Best Practices

1. **Always validate** campaigns before deployment
2. **Use website-specific methods** when deploying to individual sites
3. **Handle validation errors** gracefully
4. **Log deployment activities** for auditing
5. **Test with small batches** before full deployment
6. **Monitor campaign performance** after deployment

## Error Handling

The service includes comprehensive validation:

-   Missing triggers or websites
-   Invalid date ranges
-   Expired campaigns
-   Malformed data

Always check validation results before proceeding with deployment!
