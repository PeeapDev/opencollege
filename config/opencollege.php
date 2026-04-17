<?php

/*
|--------------------------------------------------------------------------
| OpenCollege — localisation & branding
|--------------------------------------------------------------------------
|
| OpenCollege is a country-agnostic higher-education management system.
| Deployers customise branding, localisation, and country-specific
| labels here (or via .env). Leave defaults for a neutral install.
|
*/

return [

    // Country or jurisdiction this deployment serves. Shown in the HEMIS
    // portal header, sidebar, and title tag.
    // Examples: "Sierra Leone", "Kenya", "India", "Fiji", "National"
    'country' => env('OPENCOLLEGE_COUNTRY', 'National'),

    // Ministry / government agency that operates this deployment. Shown
    // in the HEMIS sidebar footer.
    // Examples: "Ministry of Higher Education", "Ministry of Education & Sports"
    'ministry' => env('OPENCOLLEGE_MINISTRY', 'Ministry of Higher Education'),

    // Default currency used in fees, invoices, payments. Override per
    // institution in Settings → Finance.
    // ISO 4217 code. Examples: "USD", "EUR", "SLL", "NGN", "KES"
    'default_currency' => env('OPENCOLLEGE_DEFAULT_CURRENCY', 'USD'),

    // Default country to prefill in admission forms.
    'default_country' => env('OPENCOLLEGE_DEFAULT_COUNTRY', ''),

    // National student identifier — whether the country has a national
    // student ID scheme this platform integrates with (e.g. the NSI
    // pattern used in Sierra Leone). If 'none', fields are hidden.
    //
    //   'none'     — no national ID integration (default for open source)
    //   'custom'   — enable a custom format per-institution
    //   'sl_nsi'   — Sierra Leone NSI (SL-YYYY-MM-NNNNN)
    //
    'national_id_mode' => env('OPENCOLLEGE_NATIONAL_ID_MODE', 'none'),
];
