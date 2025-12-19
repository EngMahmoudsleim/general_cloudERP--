<?php

namespace Modules\InventoryReset\Http\ViewComposers;

use Illuminate\View\View;

/**
 * View composer to enhance stock history data in blade templates
 * This approach intercepts view data without modifying core classes
 */
class StockHistoryComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $data = $view->getData();

        // Look for stock history data in various possible variable names
        $stockHistoryVariables = [
            'stock_history',
            'stockHistory',
            'history',
            'stock_details',
            'variations'
        ];

        $enhanced = false;
        foreach ($stockHistoryVariables as $varName) {
            if (isset($data[$varName])) {
                $originalData = $data[$varName];
                $enhancedData = $this->enhanceStockHistoryData($originalData);

                if ($enhancedData !== $originalData) {
                    $view->with($varName, $enhancedData);
                    $enhanced = true;
                }
            }
        }

        // Also add a flag to indicate inventory reset enhancement is active
        if ($enhanced) {
            $view->with('inventory_reset_enhanced', true);
        }
    }

    /**
     * Enhanced stock history data with inventory reset detection
     *
     * @param mixed $data
     * @return mixed
     */
    private function enhanceStockHistoryData($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => &$item) {
                if (is_array($item)) {
                    $item = $this->enhanceStockHistoryData($item);
                } elseif (is_object($item)) {
                    // Handle collections and objects
                    if (method_exists($item, 'toArray')) {
                        $array = $item->toArray();
                        $enhanced = $this->enhanceStockHistoryData($array);
                        if ($enhanced !== $array) {
                            // Create enhanced version for display
                            $item->enhanced_data = $enhanced;
                        }
                    }
                }
            }
        } elseif (is_object($data)) {
            // Handle single objects or collections
            if (method_exists($data, 'map')) {
                // This is a collection
                $data = $data->map(function ($item) {
                    return $this->enhanceStockHistoryItem($item);
                });
            } elseif (method_exists($data, 'toArray')) {
                $array = $data->toArray();
                $enhanced = $this->enhanceStockHistoryData($array);
                if ($enhanced !== $array) {
                    $data->enhanced_data = $enhanced;
                }
            }
        }

        // Check for individual stock history items
        if (is_array($data) && $this->isStockHistoryItem($data)) {
            return $this->enhanceStockHistoryItem($data);
        }

        return $data;
    }

    /**
     * Check if an array item looks like a stock history record
     *
     * @param array|object $item
     * @return bool
     */
    private function isStockHistoryItem($item)
    {
        if (is_array($item)) {
            return isset($item['type']) &&
                   isset($item['type_label']) &&
                   $item['type'] === 'stock_adjustment';
        }

        if (is_object($item)) {
            return isset($item->type) &&
                   isset($item->type_label) &&
                   $item->type === 'stock_adjustment';
        }

        return false;
    }

    /**
     * Enhance a stock history item with inventory reset detection
     *
     * @param array|object $item
     * @return array|object
     */
    private function enhanceStockHistoryItem($item)
    {
        $isArray = is_array($item);
        $ref_no = $isArray ? ($item['ref_no'] ?? '') : ($item->ref_no ?? '');
        $additional_notes = $isArray ? ($item['additional_notes'] ?? '') : ($item->additional_notes ?? '');

        $isInventoryReset = $this->isInventoryResetTransaction($ref_no, $additional_notes);

        if ($isInventoryReset) {
            if ($isArray) {
                $item['type_label'] = 'Inventory Reset';
                $item['is_inventory_reset'] = true;
                if (!empty($additional_notes)) {
                    $item['reset_reason'] = $this->extractResetReason($additional_notes);
                }
            } else {
                $item->type_label = 'Inventory Reset';
                $item->is_inventory_reset = true;
                if (!empty($additional_notes)) {
                    $item->reset_reason = $this->extractResetReason($additional_notes);
                }
            }
        }

        return $item;
    }

    /**
     * Check if transaction is an inventory reset
     *
     * @param string $ref_no
     * @param string $additional_notes
     * @return bool
     */
    private function isInventoryResetTransaction($ref_no, $additional_notes)
    {
        // Check reference number pattern (IR2025/XXXX or IRCQ2025/XXXX)
        if (!empty($ref_no) && preg_match('/^IR(CQ)?\d{4}\/\d{4,}$/', $ref_no)) {
            return true;
        }

        // Check additional notes for inventory reset marker
        if (!empty($additional_notes) && strpos($additional_notes, '[INVENTORY_RESET]') !== false) {
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