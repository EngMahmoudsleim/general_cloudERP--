<?php

namespace App\Http;

use Nwidart\Menus\Presenters\Presenter;

class AdminlteCustomPresenter extends Presenter
{
    /**
     * {@inheritdoc}.
     */
    public function getOpenTagWrapper()
    {
        return '<div class="tw-flex-1 tw-p-3 tw-space-y-3 tw-overflow-y-auto tw-bg-white tw-border-r tw-border-gray-200 tw-rounded-r-2xl" id="side-bar">' . PHP_EOL;
    }

    /**
     * {@inheritdoc}.
     */
    public function getCloseTagWrapper()
    {
        return '</div>' . PHP_EOL;
    }

    /**
     * {@inheritdoc}.
     */
    public function getMenuWithoutDropdownWrapper($item)
    {
        return '<a href="' . $item->getUrl() . '" title="" class="sidebar-link tw-flex tw-items-center tw-gap-4 tw-px-3 tw-py-2.5 tw-text-sm tw-font-semibold tw-tracking-tight tw-text-slate-700 tw-transition-all tw-duration-200 tw-whitespace-nowrap hover:tw-text-primary-700 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-primary-100' . $this->getActiveState($item) . '" ' . $item->getAttributes() . '>' .
        $this->formatIcon($item->icon) . ' <span class="tw-truncate">' . $item->title . '</span>' .
            '</a>' . PHP_EOL;
    }

    /**
     * {@inheritdoc}.
     */
    public function getActiveState($item, $state = ' active')
    {
        return $item->isActive() ? $state : null;
    }

    /**
     * Get active state on child items.
     *
     * @param $item
     * @param  string  $state
     * @return null|string
     */
    public function getActiveStateOnChild($item, $state = 'tw-pb-1 tw-rounded-xl tw-bg-indigo-50 tw-text-indigo-800 tw-shadow-sm tw-border tw-border-indigo-100')
    {
        return $item->hasActiveOnChild() ? $state : null;
    }

    /**
     * {@inheritdoc}.
     */
    public function getDividerWrapper()
    {
        // Assuming a divider is just a visual space in this design
        return '<div class="tw-my-2"></div>';
    }

    /**
     * {@inheritdoc}.
     */
    public function getHeaderWrapper($item)
    {
        return '<div class="tw-px-3 tw-pt-3 tw-text-xs tw-font-semibold tw-uppercase tw-tracking-wider tw-text-slate-500">' . $item->title . '</div>';
    }

    /**
     * {@inheritdoc}.
     */
    public function getMenuWithDropDownWrapper($item)
    {
        $dropdownToggle = '<a href="#" title="" class="drop_down sidebar-link tw-flex tw-items-center tw-gap-4 tw-px-3 tw-py-2.5 tw-text-sm tw-font-semibold tw-tracking-tight tw-text-slate-700 tw-transition-all tw-duration-200 tw-whitespace-nowrap hover:tw-text-primary-700 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-primary-100' . $this->getActiveStateOnChild($item) . '" ' . $item->getAttributes() . '>' .
        $this->formatIcon($item->icon) . ' <span class="tw-truncate">' . $item->title . '</span>' .
        '<span class="tw-ml-auto tw-inline-flex tw-items-center tw-justify-center tw-size-7 tw-rounded-xl tw-bg-slate-100 tw-text-slate-500 tw-shrink-0">' .
        '<svg aria-hidden="true" class="svg tw-size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">' . $this->getArray($item) .
            '</svg>' .
            '</span>' .
            '</a>';

        $childItemsContainerStart = '';

        $childItemsContainerEnd = '';

        // Compile child menu items
        $childItems = $this->getChildMenuItems($item);

        // echo "here";
        // print_r($dropdownToggle);exit;

        return '<div class="' . $this->getActiveStateOnChild($item) . '">' . $dropdownToggle . $childItemsContainerStart . $childItems . $childItemsContainerEnd . '</div>' . PHP_EOL;
    }

    /**
     * Get multi-level dropdown wrapper.
     *
     * Note: This example doesn't directly implement a multi-level dropdown, as it wasn't specified, but you could extend
     * the functionality similarly to `getMenuWithDropDownWrapper`, adjusting for deeper nesting.
     *
     * @param  \Nwidart\Menus\MenuItem  $item
     * @return string
     */
    public function getMultiLevelDropdownWrapper($item)
    {
        // Placeholder for multi-level dropdown functionality if needed
        return '';
    }

    /**
     * Get child menu items.
     *
     * @param  \Nwidart\Menus\MenuItem  $item
     * @return string
     */
    public function getChildMenuItems($item)
    {

        $children = '';
        $displayStyle = $item->hasActiveOnChild() ? 'block' : 'none';

        


        if (count($item->getChilds()) > 0) {

            $children .= '<div class=" chiled tw-relative tw-mt-3 tw-mb-5 tw-pl-10" style="display:' . $displayStyle . '">
            <div class="tw-absolute tw-inset-y-0 tw-w-px tw-h-full tw-bg-slate-200 tw-left-4"></div>
            <div class="tw-space-y-1.5">';

            foreach ($item->getChilds() as $child) {

                $isActive = $child->isActive() ? 'tw-text-primary-800 tw-font-semibold' : '';

                $children .= '<a href="' . $child->getUrl() . '" title="" class="tw-flex tw-items-center tw-gap-3 tw-text-sm tw-font-medium tw-tracking-tight tw-text-gray-700 tw-truncate tw-transition-all tw-duration-200 tw-rounded-lg tw-px-3 tw-py-1.5 hover:tw-text-primary-800 hover:tw-bg-primary-50 ' . $isActive . '" ' . $child->getAttributes() . '>' .
                $this->formatChildIcon($child->getIcon()) . ' <span class="tw-truncate">' . $child->title . '</span>' .
                    '</a>' . PHP_EOL;
            }

            $children .= '</div></div>';
        }

        return $children;
    }

    /**
     * Returns the icon HTML. If the icon is SVG, it returns directly; otherwise, it assumes it's a FontAwesome class and wraps it in an <i> tag.
     *
     * @param string $icon
     * @return string
     */
    protected function formatIcon($icon)
    {
        // Check if the icon string contains "<svg" or an <i> tag, indicating raw markup
        if (strpos($icon, '<svg') !== false || strpos($icon, '<i') !== false) {
            $iconMarkup = $icon; // Return the SVG icon directly
        } else {
            // Assume it's a FontAwesome icon and return it wrapped in an <i> tag
            $iconMarkup = '<i class="' . $icon . '"></i>';
        }

        return '<span class="sidebar-nav-icon">' . $iconMarkup . '</span>';
    }

    /**
     * Returns the icon HTML for child items with a subtle accent.
     *
     * @param string|null $icon
     * @return string
     */
    protected function formatChildIcon($icon)
    {
        return '<span class="sidebar-child-bullet"></span>';
    }

    public function getArray($item)
    {
        if ($item->hasActiveOnChild()) {
            return '<path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M6 9l6 6l6 -6" />';
        } else {
            return '<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M15 6l-6 6l6 6" />';
        }
    }
}