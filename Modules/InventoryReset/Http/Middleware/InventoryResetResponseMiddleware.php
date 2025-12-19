<?php

namespace Modules\InventoryReset\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware to enhance API responses with inventory reset detection
 * This approach doesn't modify core classes but intercepts responses
 */
class InventoryResetResponseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only process JSON responses that might contain stock history
        if ($response->headers->get('content-type') === 'application/json' ||
            strpos($response->headers->get('content-type'), 'application/json') !== false) {

            $content = $response->getContent();
            $data = json_decode($content, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                $enhanced = $this->enhanceStockHistoryData($data);
                if ($enhanced !== $data) {
                    $response->setContent(json_encode($enhanced));
                }
            }
        }

        return $response;
    }

    /**
     * Recursively enhance stock history data with inventory reset detection
     *
     * @param array $data
     * @return array
     */
    private function enhanceStockHistoryData($data)
    {
        if (!is_array($data)) {
            return $data;
        }

        foreach ($data as $key => &$item) {
            if (is_array($item)) {
                // Check if this looks like a stock history item
                if ($this->isStockHistoryItem($item)) {
                    $item = $this->enhanceStockHistoryItem($item);
                } else {
                    // Recursively process nested arrays
                    $item = $this->enhanceStockHistoryData($item);
                }
            }
        }

        return $data;
    }

    /**
     * Check if an array item looks like a stock history record
     *
     * @param array $item
     * @return bool
     */
    private function isStockHistoryItem($item)
    {
        return is_array($item) &&
               isset($item['type']) &&
               isset($item['type_label']) &&
               isset($item['ref_no']) &&
               $item['type'] === 'stock_adjustment';
    }

    /**
     * Enhance a stock history item with inventory reset detection
     *
     * @param array $item
     * @return array
     */
    private function enhanceStockHistoryItem($item)
    {
        if ($this->isInventoryResetTransaction($item)) {
            $item['type_label'] = 'Inventory Reset';
            $item['is_inventory_reset'] = true;

            if (!empty($item['additional_notes'])) {
                $item['reset_reason'] = $this->extractResetReason($item['additional_notes']);
            }
        }

        return $item;
    }

    /**
     * Check if a stock history item is an inventory reset transaction
     *
     * @param array $item
     * @return bool
     */
    private function isInventoryResetTransaction($item)
    {
        // Check reference number pattern (IR2025/XXXX)
        if (!empty($item['ref_no']) && preg_match('/^IR\d{4}\/\d{4,}$/', $item['ref_no'])) {
            return true;
        }

        // Check additional notes for inventory reset marker
        if (!empty($item['additional_notes']) && strpos($item['additional_notes'], '[INVENTORY_RESET]') !== false) {
            return true;
        }

        return false;
    }

    /**
     * Extract reset reason from additional notes
     *
     * @param string $additional_notes
     * @return string|null
     */
    private function extractResetReason($additional_notes)
    {
        // Pattern to extract reason from: [INVENTORY_RESET] Reason here (Reset ID: X)
        if (preg_match('/\[INVENTORY_RESET\]\s+(.+?)\s+\(Reset ID:/', $additional_notes, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }
}