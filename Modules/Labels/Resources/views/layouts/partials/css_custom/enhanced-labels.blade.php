<style type="text/css">
/* Enhanced Labels CSS - Clean Version */

/* =================================================================
   BASE LAYOUT & STRUCTURE
   ================================================================= */

.enhanced-labels-container {
    min-height: 100vh;
    background-color: #f9fafb;
}

.enhanced-header {
    background-color: white;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    border-bottom: 1px solid #e5e7eb;
}

.enhanced-header-content {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 1rem;
}

.enhanced-header-inner {
    padding: 1rem 0;
}

.enhanced-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
}

.enhanced-subtitle {
    font-size: 0.875rem;
    color: #6b7280;
    margin: 0.25rem 0 0 0;
}

.enhanced-main {
    max-width: 1280px;
    margin: 0 auto;
    padding: 1.5rem 1rem;
}

/* =================================================================
   CARD SYSTEM
   ================================================================= */

.enhanced-card {
    background-color: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    margin-bottom: 1.5rem;
}

.enhanced-card-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.enhanced-card-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
    margin: 0;
}

.enhanced-card-content {
    padding: 1.5rem;
}

/* =================================================================
   GRID LAYOUT
   ================================================================= */

.enhanced-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}

@media (min-width: 1024px) {
    .enhanced-grid {
        grid-template-columns: 350px 1fr; /* Give preview more space */
    }
}

/* =================================================================
   FORM ELEMENTS
   ================================================================= */

.enhanced-input-group {
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0 auto 20px auto;
    max-width: 500px;
}

.enhanced-input {
    display: block;
    width: 100%;
    max-width: 400px;
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.enhanced-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
}

.enhanced-input-icon {
    position: absolute;
    top: 50%;
    left: 0.75rem;
    transform: translateY(-50%);
    color: #9ca3af;
    pointer-events: none;
    z-index: 1;
}

.enhanced-select {
    display: block;
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    background-color: white;
    font-size: 0.875rem;
}

.enhanced-select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
}

/* =================================================================
   TABLE STYLES
   ================================================================= */

.enhanced-table-container {
    margin-top: 1.5rem;
    overflow: hidden;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    border-radius: 0.5rem;
}

.enhanced-table {
    min-width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.enhanced-table thead {
    background-color: #f9fafb;
}

.enhanced-table th {
    padding: 0.75rem 1.5rem;
    text-align: left;
    font-size: 0.75rem;
    font-weight: 500;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 1px solid #e5e7eb;
}

.enhanced-table tbody {
    background-color: white;
}

.enhanced-table td {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.enhanced-table tbody tr:hover {
    background-color: #f9fafb;
}

/* =================================================================
   TOGGLE SWITCHES
   ================================================================= */

.toggle-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f3f4f6;
}

.toggle-label-wrapper {
    display: flex;
    align-items: center;
    cursor: pointer;
}

.toggle-switch {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
    background-color: #e5e7eb;
    border-radius: 12px;
    cursor: pointer;
    transition: background-color 0.2s ease;
    border: none;
    outline: none;
}

.toggle-switch::after {
    content: '';
    position: absolute;
    top: 2px;
    left: 2px;
    width: 20px;
    height: 20px;
    background-color: white;
    border-radius: 50%;
    transition: transform 0.2s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.toggle-switch.active {
    background-color: #3b82f6;
}

.toggle-switch.active::after {
    transform: translateX(20px);
}

.toggle-label-text {
    margin-left: 0.75rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
}

/* =================================================================
   SIZE CONTROLS
   ================================================================= */

.size-controls {
    display: flex;
    align-items: center;
    gap: 0;
}

.size-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border: 1px solid #d1d5db;
    background-color: white;
    color: #374151;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    user-select: none;
}

.size-btn:hover {
    background-color: #f9fafb;
    border-color: #9ca3af;
}

.size-btn:active {
    background-color: #f3f4f6;
}

.size-btn-minus {
    border-radius: 4px 0 0 4px;
}

.size-btn-plus {
    border-radius: 0 4px 4px 0;
}

.size-input {
    width: 60px;
    height: 28px;
    border: 1px solid #d1d5db;
    border-left: 0;
    border-right: 0;
    text-align: center;
    font-size: 12px;
    outline: none;
    background-color: white;
}

.size-input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 1px #3b82f6;
}

.size-unit {
    font-size: 0.75rem;
    color: #6b7280;
    margin-left: 0.5rem;
}

/* =================================================================
   LABEL ELEMENTS SECTION
   ================================================================= */

.label-elements {
    margin-top: 1rem;
}

.label-elements-title {
    font-size: 0.875rem;
    font-weight: 500;
    color: #111827;
    margin-bottom: 1rem;
}

.nested-controls {
    padding-left: 2rem;
    margin-top: 0.75rem;
}

.nested-controls.hidden {
    display: none;
}

.nested-controls.block {
    display: block;
}

.nested-input-group {
    margin-bottom: 0.75rem;
}

.nested-label {
    display: block;
    font-size: 0.75rem;
    font-weight: 500;
    color: #6b7280;
    margin-bottom: 0.25rem;
}

.nested-input,
.nested-select {
    display: block;
    width: 100%;
    font-size: 0.875rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    padding: 0.5rem 0.75rem;
}

.nested-input:focus,
.nested-select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
}

/* =================================================================
   BUTTON STYLES
   ================================================================= */

.preview-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
}

.preview-buttons {
    display: flex;
    gap: 6px;
    align-items: center;
}

.preview-buttons button {
    min-width: auto;
    white-space: nowrap;
    padding: 4px 8px;
    font-size: 11px;
    font-weight: 500;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
    line-height: 1.2;
    height: 26px;
    min-width: 80px;
    justify-content: center;
}

.btn-primary {
    color: white;
    background-color: #3b82f6;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.btn-primary:hover {
    background-color: #2563eb;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
}

.btn-success {
    color: white;
    background: linear-gradient(135deg, #10b981, #059669);
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.btn-success:hover {
    background: linear-gradient(135deg, #059669, #047857);
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);
}

.btn-secondary {
    color: #374151;
    background-color: #f9fafb;
    border: 1px solid #d1d5db;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.btn-secondary:hover {
    background-color: #f3f4f6;
    border-color: #9ca3af;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* =================================================================
   PREVIEW CONTAINER
   ================================================================= */

#preview_box {
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 15px;
    background-color: #f9fafb;
    min-height: 400px;
    max-height: 600px;
    overflow: auto;
    position: relative;
    width: 100%;
    box-sizing: border-box;
}

#preview_box table {
    transform-origin: top left;
    margin: 0;
    border-collapse: separate;
    table-layout: auto;
}

#preview_box td {
    padding: 2px !important;
    box-sizing: border-box;
    vertical-align: top;
}

#preview_box img {
    max-width: 100%;
    height: auto;
}

/* =================================================================
   PREVIEW STATES
   ================================================================= */

.preview-empty {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #6b7280;
}

.preview-empty-content {
    text-align: center;
}

.preview-empty-icon {
    font-size: 2.5rem;
    margin-bottom: 0.75rem;
}

.loading-spinner {
    display: inline-block;
    width: 2rem;
    height: 2rem;
    border: 2px solid #e5e7eb;
    border-radius: 50%;
    border-top-color: #3b82f6;
    animation: spin 1s ease-in-out infinite;
    margin: 0 auto 0.75rem;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* =================================================================
   UTILITY CLASSES
   ================================================================= */

.hidden {
    display: none !important;
}

.block {
    display: block !important;
}

.text-center {
    text-align: center !important;
}

.text-red-500 {
    color: #ef4444;
}

.text-blue-600 {
    color: #2563eb;
}

.text-gray-500 {
    color: #6b7280;
}

/* Hide elements during printing */
.no-print,
.scroll-hint,
.preview-info {
    display: block;
}

@media print {
    .no-print,
    .scroll-hint,
    .preview-info {
        display: none !important;
        visibility: hidden !important;
    }

    /* Hide info message backgrounds by color */
    div[style*='rgba(34, 197, 94'] {
        display: none !important;
        visibility: hidden !important;
    }

    div[style*='rgba(59, 130, 246'] {
        display: none !important;
        visibility: hidden !important;
    }
}

/* =================================================================
   RESPONSIVE DESIGN
   ================================================================= */

@media (max-width: 1023px) {
    .enhanced-grid {
        grid-template-columns: 1fr;
    }

    #preview_box {
        max-height: 400px;
    }

    .preview-buttons {
        flex-direction: column;
        gap: 0.5rem;
    }
}

@media (max-width: 640px) {
    .enhanced-main {
        padding: 1rem 0.5rem;
    }

    .enhanced-card-content {
        padding: 1rem;
    }

    .toggle-container {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }

    .size-controls {
        margin-top: 0.5rem;
        flex-wrap: wrap;
    }

    .preview-buttons {
        width: 100%;
    }

    .preview-buttons button {
        flex: 1;
        justify-content: center;
    }
}

#label_info_container {
    margin-bottom: 10px;
}

#label_info_container:empty {
    display: none;
}

.example {
	position: relative;
	border: 1px solid #ccc;
	padding: 1em 1em 0.5em 1em;
	border-radius: 4px 4px 0 0;
}

.example:after {
	content: "Example";
	position: absolute;
	top: 0px;
	right: 0px;
	padding: 3px 7px;
	font-size: 12px;
	font-weight: bold;
	background-color: #f5f5f5;
	border: 1px solid #ccc;
	color: #9da0a4;
	border-radius: 0px 4px 0px 4px;
	border-width: 0px 0px 1px 1px;
}

.example + code.hljs {
	border-top: 0;
	border-radius: 0px 0px 4px 4px;
}

.example > * {
	margin-bottom: 10px;
}

.example > div.toggle {
	margin-right: 10px;
}

.table-striped code {
	background-color: inherit;
}
</style>