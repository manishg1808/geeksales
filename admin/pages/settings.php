<?php
/* =====================================================================
   SETTINGS — All sections + fields definition
   ===================================================================== */
$settingSections = [

    /* ── 1. Store Information ──────────────────────────────────────── */
    'Store Information' => [
        'icon'   => 'ri-store-2-line',
        'color'  => '#2563EB',
        'badge'  => 'bg-blue-50 text-blue-600',
        'desc'   => 'Basic store identity, address & branding.',
        'fields' => [
            ['store_name',        'Store Name',           'text',     'geeksupportllc',                                    'ri-store-2-line'],
            ['tagline',           'Tagline',              'text',     'Your Printer Experts',                                'ri-price-tag-3-line'],
            ['store_url',         'Store URL',            'url',      'https://geeksupportllc.com',                        'ri-global-line'],
            ['store_description', 'Store Description',   'textarea', 'Shop printers, ink, and toner with expert support.',  'ri-file-text-line'],
            ['store_address',     'Store Address',        'textarea', '4307 Vineland Road, Suite H-12 Orlando, FL 3281',     'ri-map-pin-line'],
            ['store_country',     'Country',              'text',     'United States',                                       'ri-earth-line'],
            ['store_city',        'City',                 'text',     'Orlando',                                             'ri-building-line'],
            ['store_zip',         'ZIP / Postal Code',    'text',     '3281',                                                'ri-map-2-line'],
            ['store_logo_url',    'Logo URL',             'url',      '',                                                    'ri-image-line'],
            ['store_favicon_url', 'Favicon URL',          'url',      '',                                                    'ri-image-2-line'],
        ],
    ],

    /* ── 2. Contact & Support ─────────────────────────────────────── */
    'Contact & Support' => [
        'icon'   => 'ri-customer-service-2-line',
        'color'  => '#059669',
        'badge'  => 'bg-emerald-50 text-emerald-600',
        'desc'   => 'Customer-facing contact details and support channels.',
        'fields' => [
            ['store_email',         'Support Email',              'email',  'support@geeksupportllc.com', 'ri-mail-line'],
            ['phone',               'Primary Phone',              'text',   '407-246-9887',                  'ri-phone-line'],
            ['whatsapp_number',     'WhatsApp Number',            'text',   '',                            'ri-whatsapp-line'],
            ['support_hours',       'Support Hours',              'text',   '24/7 Tech Support',           'ri-customer-service-2-line'],
            ['contact_form_email',  'Contact Form Recipient',     'email',  '',                            'ri-mail-send-line'],
            ['support_ticket_email','Support Ticket Email',       'email',  '',                            'ri-customer-service-line'],
            ['support_chat_enabled','Live Chat Widget',           'toggle', 'disabled',                    'ri-chat-3-line'],
            ['live_chat_code',      'Live Chat Embed Code',       'textarea','',                           'ri-code-s-slash-line'],
        ],
    ],

    /* ── 3. Localization ──────────────────────────────────────────── */
    'Localization' => [
        'icon'   => 'ri-global-line',
        'color'  => '#0284C7',
        'badge'  => 'bg-sky-50 text-sky-600',
        'desc'   => 'Currency, timezone, date format and units.',
        'fields' => [
            ['currency',          'Currency Name',           'text',   'USD ($)',       'ri-money-dollar-circle-line'],
            ['currency_symbol',   'Currency Symbol',         'text',   '$',             'ri-coins-line'],
            ['currency_position', 'Symbol Position',        'select', 'before',        'ri-swap-line',    ['before'=>'Before ($99)', 'after'=>'After (99$)']],
            ['timezone',          'Timezone',               'text',   'Asia/Kolkata',  'ri-time-line'],
            ['date_format',       'Date Format',            'select', 'MM/DD/YYYY',    'ri-calendar-line',['MM/DD/YYYY'=>'MM/DD/YYYY','DD/MM/YYYY'=>'DD/MM/YYYY','YYYY-MM-DD'=>'YYYY-MM-DD']],
            ['tax_rate',          'Tax Rate (%)',           'number', '0',             'ri-percent-line'],
            ['tax_display',       'Tax Display',            'select', 'inclusive',     'ri-bill-line',    ['inclusive'=>'Inclusive','exclusive'=>'Exclusive (added at checkout)','hidden'=>'Hidden']],
            ['order_prefix',      'Order ID Prefix',        'text',   'GSS',           'ri-hashtag'],
            ['weight_unit',       'Weight Unit',            'select', 'lbs',           'ri-scales-line',  ['lbs'=>'Pounds (lbs)','kg'=>'Kilograms (kg)','g'=>'Grams (g)']],
            ['dimension_unit',    'Dimension Unit',         'select', 'in',            'ri-ruler-line',   ['in'=>'Inches','cm'=>'Centimeters','mm'=>'Millimeters']],
        ],
    ],

    /* ── 4. Shipping & Checkout ───────────────────────────────────── */
    'Shipping & Checkout' => [
        'icon'   => 'ri-truck-line',
        'color'  => '#D97706',
        'badge'  => 'bg-amber-50 text-amber-600',
        'desc'   => 'Shipping rates, return policy and checkout rules.',
        'fields' => [
            ['free_shipping_min',      'Free Shipping Minimum ($)',  'number', '99',     'ri-truck-line'],
            ['standard_shipping_fee',  'Standard Shipping Fee ($)',  'number', '9.99',   'ri-box-3-line'],
            ['express_shipping_fee',   'Express Shipping Fee ($)',   'number', '19.99',  'ri-flight-takeoff-line'],
            ['same_day_shipping_fee',  'Same-Day Shipping Fee ($)',  'number', '29.99',  'ri-speed-line'],
            ['return_window_days',     'Return Window (Days)',       'number', '30',     'ri-refresh-line'],
            ['warranty_years',         'Warranty (Years)',           'number', '2',      'ri-shield-check-line'],
            ['order_min_amount',       'Min Order Amount ($)',       'number', '0',      'ri-shopping-cart-line'],
            ['shipping_origin',        'Shipping Origin Address',    'textarea','',      'ri-map-pin-2-line'],
            ['cod_enabled',            'Cash on Delivery (COD)',     'toggle', 'disabled','ri-hand-coin-line'],
            ['pickup_enabled',         'In-Store Pickup',            'toggle', 'disabled','ri-store-line'],
            ['out_of_stock_checkout',  'Out-of-Stock Checkout',      'select', 'disabled','ri-error-warning-line',['disabled'=>'Block','enabled'=>'Allow Backorders']],
            ['signature_required',     'Signature on Delivery',      'toggle', 'disabled','ri-quill-pen-line'],
        ],
    ],

    /* ── 5. Payment Gateway ───────────────────────────────────────── */
    'Payment Gateway' => [
        'icon'   => 'ri-bank-card-line',
        'color'  => '#7C3AED',
        'badge'  => 'bg-purple-50 text-purple-600',
        'desc'   => 'Configure payment providers, API keys and modes.',
        'fields' => [
            ['payment_gateway',      'Active Gateway',           'select',   'stripe',    'ri-bank-card-line', ['stripe'=>'Stripe','paypal'=>'PayPal','razorpay'=>'Razorpay','square'=>'Square','manual'=>'Manual / Bank Transfer']],
            ['payment_test_mode',    'Test Mode',                'toggle',   'enabled',   'ri-flask-line'],
            ['stripe_publishable_key','Stripe Publishable Key',  'text',     '',          'ri-key-line'],
            ['stripe_secret_key',    'Stripe Secret Key',        'password', '',          'ri-lock-password-line'],
            ['paypal_client_id',     'PayPal Client ID',         'text',     '',          'ri-paypal-line'],
            ['paypal_secret',        'PayPal Secret',            'password', '',          'ri-lock-line'],
            ['paypal_mode',          'PayPal Mode',              'select',   'sandbox',   'ri-globe-line',     ['sandbox'=>'Sandbox (Test)','live'=>'Live']],
            ['razorpay_key_id',      'Razorpay Key ID',          'text',     '',          'ri-key-2-line'],
            ['razorpay_key_secret',  'Razorpay Key Secret',      'password', '',          'ri-lock-2-line'],
            ['square_access_token',  'Square Access Token',      'password', '',          'ri-shield-line'],
            ['bank_account_details', 'Bank Transfer Details',    'textarea', '',          'ri-bank-line'],
            ['currency_conversion',  'Multi-Currency Support',   'toggle',   'disabled',  'ri-exchange-dollar-line'],
        ],
    ],

    /* ── 6. Email & SMTP ──────────────────────────────────────────── */
    'Email & SMTP' => [
        'icon'   => 'ri-mail-send-line',
        'color'  => '#DB2777',
        'badge'  => 'bg-pink-50 text-pink-600',
        'desc'   => 'SMTP server config and transactional email toggles.',
        'fields' => [
            ['smtp_host',              'SMTP Host',                  'text',     'smtp.gmail.com',                'ri-server-line'],
            ['smtp_port',              'SMTP Port',                  'number',   '587',                           'ri-plug-line'],
            ['smtp_username',          'SMTP Username',              'email',    '',                              'ri-user-line'],
            ['smtp_password',          'SMTP Password',              'password', '',                              'ri-lock-password-line'],
            ['smtp_encryption',        'Encryption',                 'select',   'tls',                           'ri-shield-keyhole-line', ['tls'=>'TLS','ssl'=>'SSL','none'=>'None']],
            ['email_from_name',        'From Name',                  'text',     'geeksupportllc',              'ri-account-circle-line'],
            ['email_from_address',     'From Email',                 'email',    'noreply@geeksupportllc.com',  'ri-mail-open-line'],
            ['order_confirm_email',    'Order Confirmation',         'toggle',   'enabled',                       'ri-mail-check-line'],
            ['shipping_notify_email',  'Shipping Notification',      'toggle',   'enabled',                       'ri-truck-line'],
            ['refund_notify_email',    'Refund Notification',        'toggle',   'enabled',                       'ri-refund-2-line'],
            ['welcome_email',          'Welcome Email on Register',  'toggle',   'enabled',                       'ri-mail-heart-line'],
            ['abandoned_cart_email',   'Abandoned Cart Email',       'toggle',   'disabled',                      'ri-shopping-cart-2-line'],
            ['newsletter_enabled',     'Newsletter Signup',          'toggle',   'disabled',                      'ri-newspaper-line'],
            ['email_footer_text',      'Email Footer Text',          'textarea', '© 2025 geeksupportllc',       'ri-text-snippet'],
        ],
    ],

    /* ── 7. Marketing & SEO ───────────────────────────────────────── */
    'Marketing & SEO' => [
        'icon'   => 'ri-megaphone-line',
        'color'  => '#EA580C',
        'badge'  => 'bg-orange-50 text-orange-600',
        'desc'   => 'Announcements, meta tags, analytics & social links.',
        'fields' => [
            ['announcement_text',      'Top Announcement Bar',        'textarea', 'Free Shipping on orders over $99 | Free Expert Setup | 24/7 Tech Support', 'ri-megaphone-line'],
            ['default_meta_title',     'Default Meta Title',          'text',     'geeksupportllc - Printer Sales & Setup Support in Orlando', 'ri-search-eye-line'],
            ['default_meta_description','Default Meta Description',   'textarea', 'Shop printers, ink, and toner with expert setup support in Orlando, FL.', 'ri-file-text-line'],
            ['default_meta_keywords',  'Default Meta Keywords',       'textarea', 'Printers in Orlando, Printer Repair Orlando FL, Geek Support Sales Orlando, Printer Setup Orlando Florida, Ink and Toner Orlando', 'ri-hashtag'],
            ['google_analytics_id',    'Google Analytics ID (GA4)',   'text',     '',          'ri-google-line'],
            ['google_tag_manager_id',  'Google Tag Manager ID',       'text',     '',          'ri-code-s-slash-line'],
            ['google_site_verification','Google Search Console ID',     'text',     '',          'ri-google-line'],
            ['facebook_pixel_id',      'Facebook Pixel ID',           'text',     '',          'ri-facebook-circle-line'],
            ['facebook_url',           'Facebook Page URL',           'url',      '',          'ri-facebook-circle-line'],
            ['instagram_url',          'Instagram URL',               'url',      '',          'ri-instagram-line'],
            ['youtube_url',            'YouTube Channel URL',         'url',      '',          'ri-youtube-line'],
            ['twitter_url',            'Twitter / X URL',             'url',      '',          'ri-twitter-x-line'],
            ['linkedin_url',           'LinkedIn URL',                'url',      '',          'ri-linkedin-box-line'],
            ['tiktok_url',             'TikTok URL',                  'url',      '',          'ri-tiktok-line'],
            ['pinterest_url',          'Pinterest URL',               'url',      '',          'ri-pinterest-line'],
            ['sitemap_enabled',        'Auto-Generate Sitemap',       'toggle',   'enabled',   'ri-map-line'],
            ['robots_txt',             'Robots.txt Content',          'textarea', "User-agent: *\nAllow: /", 'ri-robot-line'],
            ['open_graph_enabled',     'Open Graph / Social Cards',   'toggle',   'enabled',   'ri-share-line'],
        ],
    ],

    /* ── 8. Notifications ─────────────────────────────────────────── */
    'Notifications' => [
        'icon'   => 'ri-notification-3-line',
        'color'  => '#DC2626',
        'badge'  => 'bg-red-50 text-red-600',
        'desc'   => 'Email and SMS alert preferences for admin events.',
        'fields' => [
            ['notify_new_order',      'New Order Alerts',       'toggle', 'enabled',  'ri-shopping-bag-3-line'],
            ['notify_new_lead',       'New Lead Alerts',        'toggle', 'enabled',  'ri-contacts-line'],
            ['notify_low_stock',      'Low Stock Alerts',       'toggle', 'enabled',  'ri-alert-line'],
            ['notify_refund_request', 'Refund Request Alerts',  'toggle', 'enabled',  'ri-refund-2-line'],
            ['notify_new_review',     'New Review Alerts',      'toggle', 'enabled',  'ri-star-line'],
            ['notify_new_customer',   'New Customer Signup',    'toggle', 'disabled', 'ri-user-add-line'],
            ['notify_payment_failed', 'Payment Failed Alerts',  'toggle', 'enabled',  'ri-error-warning-line'],
            ['low_stock_threshold',   'Low Stock Threshold',    'number', '5',        'ri-stack-line'],
            ['admin_email',           'Admin Notification Email','email', 'admin@geek.com','ri-admin-line'],
            ['sms_alerts_enabled',    'SMS Alerts',             'toggle', 'disabled', 'ri-message-2-line'],
            ['sms_phone_number',      'SMS Alert Phone',        'text',   '',         'ri-smartphone-line'],
            ['sms_provider',          'SMS Provider',           'select', 'twilio',   'ri-sim-card-line', ['twilio'=>'Twilio','nexmo'=>'Vonage/Nexmo','aws_sns'=>'AWS SNS']],
            ['push_notifications',    'Browser Push Notifications','toggle','disabled','ri-notification-badge-line'],
        ],
    ],

    /* ── 9. Inventory & Products ──────────────────────────────────── */
    'Inventory & Products' => [
        'icon'   => 'ri-inbox-archive-line',
        'color'  => '#0D9488',
        'badge'  => 'bg-teal-50 text-teal-600',
        'desc'   => 'Stock tracking, badges, display settings and reviews.',
        'fields' => [
            ['inventory_tracking',   'Inventory Tracking',          'toggle', 'enabled',  'ri-inbox-archive-line'],
            ['allow_backorders',     'Allow Backorders',            'toggle', 'disabled', 'ri-time-line'],
            ['show_stock_quantity',  'Show Stock Qty on Frontend',  'toggle', 'enabled',  'ri-eye-line'],
            ['sold_out_badge',       '"Sold Out" Badge',            'toggle', 'enabled',  'ri-price-tag-line'],
            ['new_badge_days',       '"New" Badge Duration (Days)', 'number', '30',       'ri-star-smile-line'],
            ['hot_badge_threshold',  '"Hot" Badge Min Sales',       'number', '50',       'ri-fire-line'],
            ['default_product_sort', 'Default Sort Order',          'select', 'newest',   'ri-sort-desc', ['newest'=>'Newest First','price_asc'=>'Price Low→High','price_desc'=>'Price High→Low','popular'=>'Most Popular','name_asc'=>'Name A-Z','rating'=>'Highest Rated']],
            ['products_per_page',    'Products Per Page',           'number', '12',       'ri-grid-line'],
            ['enable_reviews',       'Product Reviews',             'toggle', 'enabled',  'ri-star-half-line'],
            ['review_approval',      'Manual Review Approval',      'toggle', 'enabled',  'ri-checkbox-circle-line'],
            ['enable_wishlist',      'Wishlist Feature',            'toggle', 'enabled',  'ri-heart-line'],
            ['enable_compare',       'Product Compare Feature',     'toggle', 'disabled', 'ri-scales-2-line'],
            ['enable_zoom',          'Product Image Zoom',          'toggle', 'enabled',  'ri-zoom-in-line'],
            ['sku_required',         'SKU Required on Products',    'toggle', 'enabled',  'ri-barcode-line'],
        ],
    ],

    /* ── 10. Customer Accounts ────────────────────────────────────── */
    'Customer Accounts' => [
        'icon'   => 'ri-group-line',
        'color'  => '#4F46E5',
        'badge'  => 'bg-indigo-50 text-indigo-600',
        'desc'   => 'Registration, login, session and loyalty settings.',
        'fields' => [
            ['guest_checkout',           'Guest Checkout',              'toggle', 'enabled',  'ri-user-shared-line'],
            ['customer_registration',    'Customer Registration',       'select', 'enabled',  'ri-user-add-line', ['enabled'=>'Open','disabled'=>'Admin Only','invite'=>'Invite Only']],
            ['email_verification',       'Email Verification',          'toggle', 'disabled', 'ri-mail-check-line'],
            ['auto_login_after_register','Auto Login After Register',   'toggle', 'enabled',  'ri-login-box-line'],
            ['password_min_length',      'Min Password Length',         'number', '8',        'ri-lock-password-line'],
            ['session_timeout_minutes',  'Session Timeout (Minutes)',   'number', '60',       'ri-timer-line'],
            ['order_history_visible',    'Order History on Dashboard',  'toggle', 'enabled',  'ri-history-line'],
            ['address_book_enabled',     'Address Book',                'toggle', 'enabled',  'ri-map-pin-user-line'],
            ['loyalty_points_enabled',   'Loyalty Points Program',      'toggle', 'disabled', 'ri-gift-line'],
            ['points_per_dollar',        'Points Per $1 Spent',         'number', '10',       'ri-copper-coin-line'],
            ['referral_program',         'Referral Program',            'toggle', 'disabled', 'ri-share-forward-line'],
            ['referral_credit',          'Referral Credit ($)',         'number', '5',        'ri-coupon-line'],
        ],
    ],

    /* ── 11. Coupons & Discounts ──────────────────────────────────── */
    'Coupons & Discounts' => [
        'icon'   => 'ri-coupon-3-line',
        'color'  => '#16A34A',
        'badge'  => 'bg-green-50 text-green-600',
        'desc'   => 'Discount rules, coupon behavior and promotion settings.',
        'fields' => [
            ['coupons_enabled',           'Coupons System',              'toggle', 'enabled',  'ri-coupon-3-line'],
            ['coupon_one_per_order',      'One Coupon Per Order',         'toggle', 'enabled',  'ri-ticket-line'],
            ['coupon_stack_allowed',      'Allow Stacking Coupons',       'toggle', 'disabled', 'ri-stack-line'],
            ['flash_sale_enabled',        'Flash Sale Mode',              'toggle', 'disabled', 'ri-flashlight-line'],
            ['flash_sale_badge_text',     'Flash Sale Badge Text',        'text',   '🔥 Hot Deal', 'ri-price-tag-2-line'],
            ['bulk_discount_enabled',     'Bulk / Quantity Discounts',    'toggle', 'disabled', 'ri-shopping-basket-line'],
            ['bulk_discount_threshold',   'Bulk Discount Qty Threshold',  'number', '5',        'ri-stack-overflow-line'],
            ['bulk_discount_percent',     'Bulk Discount (%)',            'number', '10',       'ri-percent-line'],
            ['auto_coupon_new_customer',  'Auto Coupon – New Customers',  'toggle', 'disabled', 'ri-user-star-line'],
            ['new_customer_coupon_code',  'New Customer Coupon Code',     'text',   'WELCOME10', 'ri-key-line'],
            ['new_customer_discount',     'New Customer Discount (%)',    'number', '10',       'ri-percent-line'],
        ],
    ],

    /* ── 12. Appearance & Theme ───────────────────────────────────── */
    'Appearance & Theme' => [
        'icon'   => 'ri-palette-line',
        'color'  => '#E11D48',
        'badge'  => 'bg-rose-50 text-rose-600',
        'desc'   => 'Colors, fonts, homepage hero and custom code.',
        'fields' => [
            ['theme_primary_color',    'Primary Color',           'color',    '#2563EB',  'ri-palette-line'],
            ['theme_secondary_color',  'Secondary Color',         'color',    '#0F172A',  'ri-contrast-2-line'],
            ['theme_accent_color',     'Accent Color',            'color',    '#F97316',  'ri-drop-line'],
            ['theme_font',             'Body Font',               'select',   'Inter',    'ri-text',         ['Inter'=>'Inter','Roboto'=>'Roboto','Poppins'=>'Poppins','Open Sans'=>'Open Sans','Nunito'=>'Nunito','Lato'=>'Lato']],
            ['theme_dark_mode',        'Dark Mode Support',       'toggle',   'disabled', 'ri-moon-line'],
            ['homepage_hero_title',    'Hero Title',              'text',     'Your Printer Experts',                             'ri-h-1'],
            ['homepage_hero_subtitle', 'Hero Subtitle',           'textarea', 'Shop printers, ink, toner & get free expert setup.','ri-text-wrap'],
            ['homepage_hero_bg_image', 'Hero Background Image URL','url',     '',         'ri-image-fill'],
            ['homepage_hero_cta_text', 'Hero Button Text',        'text',     'Shop Now', 'ri-cursor-line'],
            ['homepage_hero_cta_url',  'Hero Button URL',         'url',      '/products','ri-links-line'],
            ['footer_text',            'Footer Copyright Text',   'text',     '© 2025 geeksupportllc. All rights reserved.','ri-copyright-line'],
            ['products_grid_columns',  'Product Grid Columns',    'select',   '4',        'ri-layout-grid-line',['3'=>'3 Columns','4'=>'4 Columns','5'=>'5 Columns']],
            ['custom_css',             'Custom CSS',              'textarea', '',         'ri-css3-line'],
            ['custom_js',              'Custom JS (Head)',         'textarea', '',         'ri-javascript-line'],
        ],
    ],

    /* ── 13. Analytics & Tracking ────────────────────────────────── */
    'Analytics & Tracking' => [
        'icon'   => 'ri-bar-chart-box-line',
        'color'  => '#0891B2',
        'badge'  => 'bg-cyan-50 text-cyan-600',
        'desc'   => 'Conversion tracking, heatmaps and analytics integrations.',
        'fields' => [
            ['hotjar_id',            'Hotjar Site ID',             'text',   '',         'ri-fire-line'],
            ['clarity_id',           'Microsoft Clarity ID',       'text',   '',         'ri-eye-line'],
            ['mixpanel_token',       'Mixpanel Token',             'text',   '',         'ri-flask-line'],
            ['intercom_app_id',      'Intercom App ID',            'text',   '',         'ri-chat-heart-line'],
            ['crisp_website_id',     'Crisp Chat Website ID',      'text',   '',         'ri-message-3-line'],
            ['conversion_tracking',  'Conversion Tracking',        'toggle', 'disabled', 'ri-line-chart-line'],
            ['track_add_to_cart',    'Track Add-to-Cart Events',   'toggle', 'enabled',  'ri-shopping-cart-line'],
            ['track_checkout',       'Track Checkout Events',      'toggle', 'enabled',  'ri-secure-payment-line'],
            ['track_search',         'Track Search Events',        'toggle', 'enabled',  'ri-search-line'],
            ['session_recording',    'Session Recording',          'toggle', 'disabled', 'ri-vidicon-line'],
            ['cookie_consent',       'Cookie Consent Banner',      'toggle', 'enabled',  'ri-cookie-line'],
            ['gdpr_enabled',         'GDPR Compliance Mode',       'toggle', 'disabled', 'ri-file-shield-2-line'],
        ],
    ],

    /* ── 14. Security ─────────────────────────────────────────────── */
    'Security' => [
        'icon'   => 'ri-shield-keyhole-line',
        'color'  => '#475569',
        'badge'  => 'bg-slate-100 text-slate-600',
        'desc'   => 'Authentication, access control and security hardening.',
        'fields' => [
            ['two_factor_auth',        'Admin 2FA',                  'toggle', 'disabled', 'ri-shield-keyhole-line'],
            ['admin_login_attempts',   'Max Failed Login Attempts',  'number', '5',        'ri-error-warning-line'],
            ['admin_lockout_minutes',  'Lockout Duration (Minutes)', 'number', '30',       'ri-lock-2-line'],
            ['ssl_forced',             'Force HTTPS / SSL',          'toggle', 'enabled',  'ri-secure-payment-line'],
            ['captcha_on_login',       'CAPTCHA on Login',           'toggle', 'disabled', 'ri-robot-line'],
            ['recaptcha_site_key',     'reCAPTCHA Site Key',         'text',   '',         'ri-key-line'],
            ['recaptcha_secret_key',   'reCAPTCHA Secret Key',       'password','',        'ri-key-2-line'],
            ['ip_whitelist_admin',     'Admin IP Whitelist',         'textarea','',        'ri-shield-star-line'],
            ['brute_force_protection', 'Brute-Force Protection',     'toggle', 'enabled',  'ri-spam-2-line'],
            ['xss_protection',         'XSS Protection Header',      'toggle', 'enabled',  'ri-code-view'],
            ['activity_log_enabled',   'Admin Activity Log',         'toggle', 'enabled',  'ri-file-list-3-line'],
            ['auto_logout_idle',       'Auto-Logout on Idle',        'toggle', 'enabled',  'ri-logout-box-r-line'],
        ],
    ],

    /* ── 15. System ───────────────────────────────────────────────── */
    'System' => [
        'icon'   => 'ri-server-line',
        'color'  => '#64748B',
        'badge'  => 'bg-zinc-100 text-zinc-600',
        'desc'   => 'Cache, backups, debug mode and system performance.',
        'fields' => [
            ['maintenance_mode',       'Maintenance Mode',           'toggle', 'disabled', 'ri-tools-line'],
            ['allow_guest_checkout',   'Guest Checkout (legacy)',     'select', 'enabled',  'ri-user-shared-line', ['enabled'=>'Enabled','disabled'=>'Disabled']],
            ['items_per_page',         'Admin Items Per Page',        'number', '10',       'ri-list-check-2'],
            ['backup_email',           'Backup Notification Email',   'email',  '',         'ri-database-2-line'],
            ['auto_backup',            'Auto DB Backup',              'select', 'disabled', 'ri-database-line',    ['disabled'=>'Disabled','daily'=>'Daily','weekly'=>'Weekly','monthly'=>'Monthly']],
            ['backup_retention_days',  'Backup Retention (Days)',     'number', '30',       'ri-archive-line'],
            ['cache_enabled',          'Page Caching',                'toggle', 'disabled', 'ri-speed-line'],
            ['cache_duration_minutes', 'Cache Duration (Minutes)',    'number', '60',       'ri-timer-2-line'],
            ['error_reporting',        'Error Reporting',             'select', 'disabled', 'ri-bug-line',         ['disabled'=>'Off (Production)','enabled'=>'On (Development)']],
            ['debug_mode',             'Debug Mode',                  'toggle', 'disabled', 'ri-code-s-slash-line'],
            ['api_rate_limit',         'API Rate Limit / Minute',     'number', '60',       'ri-git-branch-line'],
            ['queue_system',           'Background Job Queue',        'toggle', 'disabled', 'ri-stack-overflow-line'],
            ['log_retention_days',     'Log Retention (Days)',        'number', '90',       'ri-file-list-line'],
        ],
    ],
];

/* ─── Build flat map ───────────────────────────────────────────────── */
$flatSettings = [];
foreach ($settingSections as $section) {
    foreach ($section['fields'] as $field) {
        $flatSettings[$field[0]] = $field[3];
    }
}

/* ─── Handle POST ──────────────────────────────────────────────────── */
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (($_POST['form_action'] ?? '') === 'save_settings') {
        $stmt = $pdo->prepare(
            'INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
        );
        foreach ($flatSettings as $key => $default) {
            $raw = $_POST[$key] ?? null;
            // Toggles that are unchecked won't appear in POST → treat as 'disabled'
            $val = ($raw === null) ? 'disabled' : trim((string)$raw);
            $stmt->execute([$key, $val]);
        }
        set_flash('Settings saved successfully.');
        redirect_admin('settings');
    }
}

/* ─── Load from DB ─────────────────────────────────────────────────── */
$settings = $flatSettings;
$rows = $pdo->query('SELECT setting_key, setting_value FROM settings')->fetchAll();
foreach ($rows as $row) {
    if (array_key_exists($row['setting_key'], $settings)) {
        $settings[$row['setting_key']] = (string)$row['setting_value'];
    }
}

/* ─── Upsert defaults ──────────────────────────────────────────────── */
$upsertStmt = $pdo->prepare(
    'INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
     ON DUPLICATE KEY UPDATE setting_key = setting_key'
);
foreach ($flatSettings as $key => $default) {
    $upsertStmt->execute([$key, $settings[$key]]);
}

/* ─── Helper: render one field ─────────────────────────────────────── */
function render_setting_field(array $field, array $settings): void
{
    [$key, $label, $type, $default, $icon] = $field;
    $value    = $settings[$key] ?? $default;
    $inputCls = 'mt-1.5 w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white transition placeholder-slate-400';

    echo '<div class="field-wrap">';
    echo '<label class="block">';
    echo '<span class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-0.5">';
    echo '<i class="' . e($icon) . ' text-[13px]" style="color:var(--sec-color,#2563EB)"></i>' . e($label);
    echo '</span>';

    switch ($type) {
        case 'textarea':
            echo '<textarea name="' . e($key) . '" rows="3" class="' . $inputCls . ' resize-y" placeholder="' . e($label) . '">' . e($value) . '</textarea>';
            break;

        case 'select':
            $options = $field[5] ?? [];
            echo '<select name="' . e($key) . '" class="' . $inputCls . ' cursor-pointer">';
            foreach ($options as $ov => $ol) {
                echo '<option value="' . e($ov) . '"' . ($value === (string)$ov ? ' selected' : '') . '>' . e($ol) . '</option>';
            }
            echo '</select>';
            break;

        case 'toggle':
            $on = ($value === 'enabled');
            echo '<div class="mt-2 flex items-center justify-between bg-slate-50 border border-slate-200 rounded-xl px-4 py-3" id="toggle-wrap-' . e($key) . '">';
            echo '<span class="toggle-label text-sm ' . ($on ? 'text-slate-700 font-medium' : 'text-slate-400') . '">' . ($on ? 'Enabled' : 'Disabled') . '</span>';
            echo '<label class="toggle-switch">';
            // Hidden fallback so unchecked = 'disabled'
            echo '<input type="hidden" name="' . e($key) . '" value="disabled">';
            echo '<input type="checkbox" name="' . e($key) . '" value="enabled"' . ($on ? ' checked' : '') . '>';
            echo '<span class="slider"></span>';
            echo '</label>';
            echo '</div>';
            break;

        case 'password':
            echo '<div class="relative mt-1.5">';
            echo '<input type="password" name="' . e($key) . '" value="' . e($value) . '" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white transition pr-11 placeholder-slate-400" placeholder="••••••••">';
            echo '<button type="button" onclick="togglePwd(this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-blue-500 transition" title="Show/hide">';
            echo '<i class="ri-eye-off-line text-base"></i></button>';
            echo '</div>';
            break;

        case 'color':
            echo '<div class="mt-1.5 flex items-center gap-3">';
            echo '<input type="color" name="' . e($key) . '_picker" value="' . e($value) . '" class="w-12 h-10 rounded-lg border border-slate-200 cursor-pointer p-1 bg-white" oninput="document.getElementById(\'txt_' . e($key) . '\').value=this.value">';
            echo '<input type="text" id="txt_' . e($key) . '" name="' . e($key) . '" value="' . e($value) . '" class="' . $inputCls . ' flex-1 font-mono" maxlength="7" placeholder="#000000" oninput="syncColor(this)">';
            echo '</div>';
            break;

        default:
            $step = $type === 'number' ? ' step="any"' : '';
            echo '<input type="' . e($type) . '" name="' . e($key) . '" value="' . e($value) . '"' . $step . ' class="' . $inputCls . '" placeholder="' . e($label) . '">';
    }

    echo '</label></div>';
}
?>

<?php /* ════════════ HTML OUTPUT ════════════ */ ?>
<div class="settings-root animate-slide">

    <!-- ── Page Header ───────────────────────────────────────────────── -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-black text-slate-900 leading-tight">Store Settings</h1>
            <p class="text-sm text-slate-400 mt-0.5">Manage every aspect of your geeksupportllc store.</p>
        </div>
        <!-- Search box -->
        <div class="relative w-full sm:w-72">
            <i class="ri-search-line absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" id="settingsSearch" placeholder="Search settings…"
                   class="w-full border border-slate-200 rounded-xl pl-9 pr-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white"
                   oninput="filterSettings(this.value)">
        </div>
    </div>

    <!-- ── Stats Bar ──────────────────────────────────────────────────── -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <?php
        $statCards = [
            ['Store', $settings['store_name'], 'ri-store-2-line', '#2563EB', '#EFF6FF'],
            ['Gateway', ucfirst($settings['payment_gateway'] ?? 'stripe'), 'ri-bank-card-line', '#7C3AED', '#F5F3FF'],
            ['Free Ship ≥', ($settings['currency_symbol']??'$').($settings['free_shipping_min']??'99'), 'ri-truck-line', '#D97706', '#FFFBEB'],
            ['Maintenance', ucfirst($settings['maintenance_mode']??'disabled'), 'ri-tools-line', '#DC2626', '#FEF2F2'],
        ];
        foreach ($statCards as [$lbl, $val, $ico, $clr, $bg]):
        ?>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:<?= $bg ?>;color:<?= $clr ?>">
                <i class="<?= $ico ?> text-lg"></i>
            </div>
            <div class="min-w-0">
                <div class="text-[11px] text-slate-400 font-medium"><?= e($lbl) ?></div>
                <div class="font-black text-slate-800 text-sm truncate"><?= e($val) ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- ── Layout: Sidebar + Content ─────────────────────────────────── -->
    <div class="flex gap-5 items-start">

        <!-- Sidebar nav -->
        <aside class="w-56 flex-shrink-0 bg-white border border-slate-200 rounded-2xl overflow-hidden sticky top-4">
            <div class="px-4 py-3 border-b border-slate-100">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sections</p>
            </div>
            <nav class="py-2 max-h-[calc(100vh-260px)] overflow-y-auto" id="sidebarNav">
                <?php $idx = 0; foreach ($settingSections as $sTitle => $sData): ?>
                    <?php $tabId = 'tab-' . preg_replace('/[^a-z0-9]+/','-', strtolower($sTitle)); ?>
                    <button type="button"
                        onclick="switchTab('<?= $tabId ?>')"
                        id="nav-<?= $tabId ?>"
                        data-section="<?= e(strtolower($sTitle)) ?>"
                        class="nav-btn w-full flex items-center gap-2.5 px-4 py-2.5 text-left text-sm text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition <?= $idx === 0 ? 'nav-active' : '' ?>">
                        <i class="<?= $sData['icon'] ?> text-base flex-shrink-0" style="color:<?= $sData['color'] ?>"></i>
                        <span class="truncate font-medium"><?= e($sTitle) ?></span>
                        <span class="ml-auto text-[10px] font-bold px-1.5 py-0.5 rounded-full flex-shrink-0 <?= $sData['badge'] ?>"><?= count($sData['fields']) ?></span>
                    </button>
                <?php $idx++; endforeach; ?>
            </nav>
        </aside>

        <!-- Main content -->
        <div class="flex-1 min-w-0">
            <form method="POST" id="settingsForm" class="space-y-5" novalidate>
                <input type="hidden" name="form_action" value="save_settings">

                <?php $idx = 0; foreach ($settingSections as $sTitle => $sData): ?>
                    <?php $tabId = 'tab-' . preg_replace('/[^a-z0-9]+/','-', strtolower($sTitle)); ?>
                    <div id="<?= $tabId ?>" class="setting-panel <?= $idx > 0 ? 'hidden' : '' ?>">
                        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                            <!-- Panel header -->
                            <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-4">
                                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                                     style="background:<?= $sData['color'] ?>18;color:<?= $sData['color'] ?>">
                                    <i class="<?= $sData['icon'] ?> text-xl"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h2 class="font-black text-slate-800 text-base"><?= e($sTitle) ?></h2>
                                    <p class="text-xs text-slate-400 mt-0.5"><?= e($sData['desc']) ?></p>
                                </div>
                                <span class="text-xs font-bold px-3 py-1.5 rounded-full flex-shrink-0 <?= $sData['badge'] ?>">
                                    <?= count($sData['fields']) ?> options
                                </span>
                            </div>
                            <!-- Fields grid -->
                            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5" id="fields-<?= $tabId ?>">
                                <?php foreach ($sData['fields'] as $field): ?>
                                    <div class="field-item <?= in_array($field[2], ['textarea','color']) ? 'md:col-span-2' : '' ?>"
                                         data-label="<?= e(strtolower($field[1])) ?>"
                                         data-key="<?= e(strtolower($field[0])) ?>">
                                        <?php render_setting_field($field, $settings); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php $idx++; endforeach; ?>

                <!-- ── Save Bar ────────────────────────────────────────── -->
                <div class="sticky bottom-0 z-10 bg-white/90 backdrop-blur-sm border border-slate-200 rounded-2xl px-5 py-4 flex flex-wrap items-center justify-between gap-3 shadow-lg shadow-slate-200/60">
                    <div class="flex items-center gap-2 text-sm text-slate-500">
                        <i class="ri-information-line text-blue-400"></i>
                        Saved to database · effective immediately.
                    </div>
                    <div class="flex gap-3">
                        <a href="?page=dashboard"
                           class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm transition">
                            Cancel
                        </a>
                        <button type="submit"
                                class="save-btn px-6 py-2.5 rounded-xl text-white font-bold text-sm flex items-center gap-2 transition shadow-md"
                                style="background:#2563EB">
                            <i class="ri-save-3-line"></i> Save Settings
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div><!-- /layout -->
</div><!-- /settings-root -->

<!-- ── Styles ─────────────────────────────────────────────────────────── -->
<style>
/* Toggle switch */
.toggle-switch { position:relative; display:inline-flex; width:46px; height:26px; cursor:pointer; }
.toggle-switch input[type="checkbox"] { opacity:0; width:0; height:0; position:absolute; }
.toggle-switch input[type="hidden"] { display:none; }
.toggle-switch .slider {
    position:absolute; inset:0; border-radius:999px;
    background:#E2E8F0; transition:.25s;
}
.toggle-switch .slider::before {
    content:''; position:absolute;
    width:20px; height:20px; left:3px; top:3px;
    background:white; border-radius:50%;
    transition:.25s; box-shadow:0 1px 4px rgba(0,0,0,.2);
}
.toggle-switch input[type="checkbox"]:checked ~ .slider { background:#2563EB; }
.toggle-switch input[type="checkbox"]:checked ~ .slider::before { transform:translateX(20px); }

/* Nav active state */
.nav-active {
    background:#EFF6FF !important;
    color:#2563EB !important;
    font-weight:700;
    border-right:3px solid #2563EB;
}
.nav-active i { color:#2563EB !important; }
.nav-active span:first-of-type { color:#2563EB !important; }

/* Field focus glow */
.field-wrap input:focus,
.field-wrap select:focus,
.field-wrap textarea:focus { box-shadow:0 0 0 3px rgba(37,99,235,.12); }

/* Save button hover */
.save-btn:hover { background:#1D4ED8 !important; box-shadow:0 4px 16px rgba(37,99,235,.4) !important; }

/* Search highlight */
.field-item.search-highlight { background:#FFF9C4; border-radius:12px; }
.field-item.search-hidden { display:none !important; }

/* Smooth slide in */
.setting-panel { animation: fadeUp .2s ease; }
@keyframes fadeUp { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
</style>

<!-- ── Scripts ────────────────────────────────────────────────────────── -->
<script>
/* ── Tab switching ── */
function switchTab(tabId) {
    document.querySelectorAll('.setting-panel').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('nav-active'));
    const panel = document.getElementById(tabId);
    const nav   = document.getElementById('nav-' + tabId);
    if (panel) { panel.classList.remove('hidden'); }
    if (nav)   { nav.classList.add('nav-active'); nav.scrollIntoView({ block:'nearest', behavior:'smooth' }); }
}

/* ── Toggle label update ── */
document.querySelectorAll('.toggle-switch input[type="checkbox"]').forEach(cb => {
    cb.addEventListener('change', function () {
        const wrap  = this.closest('[id^="toggle-wrap-"]');
        const label = wrap ? wrap.querySelector('.toggle-label') : null;
        if (label) {
            label.textContent = this.checked ? 'Enabled' : 'Disabled';
            label.className   = 'toggle-label text-sm ' + (this.checked ? 'text-slate-700 font-medium' : 'text-slate-400');
        }
    });
});

/* ── Password toggle ── */
function togglePwd(btn) {
    const inp  = btn.previousElementSibling;
    const icon = btn.querySelector('i');
    if (inp.type === 'password') { inp.type = 'text';     icon.className = 'ri-eye-line text-base'; }
    else                         { inp.type = 'password'; icon.className = 'ri-eye-off-line text-base'; }
}

/* ── Color picker sync ── */
function syncColor(txtInput) {
    const picker = txtInput.closest('.flex').querySelector('input[type="color"]');
    if (picker && /^#[0-9A-Fa-f]{6}$/.test(txtInput.value)) picker.value = txtInput.value;
}

/* ── Search / filter ── */
function filterSettings(query) {
    const q = query.trim().toLowerCase();

    if (!q) {
        // restore all
        document.querySelectorAll('.field-item').forEach(el => {
            el.classList.remove('search-hidden', 'search-highlight');
        });
        document.querySelectorAll('.setting-panel').forEach(p => p.classList.add('hidden'));
        // re-activate first active nav
        const activeNav = document.querySelector('.nav-btn.nav-active');
        if (activeNav) {
            const tid = activeNav.id.replace('nav-', '');
            const firstPanel = document.getElementById(tid);
            if (firstPanel) firstPanel.classList.remove('hidden');
        } else {
            const firstPanel = document.querySelector('.setting-panel');
            if (firstPanel) firstPanel.classList.remove('hidden');
        }
        return;
    }

    // Show all panels while searching
    document.querySelectorAll('.setting-panel').forEach(p => p.classList.remove('hidden'));
    document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('nav-active'));

    document.querySelectorAll('.field-item').forEach(el => {
        const text = (el.dataset.label || '') + ' ' + (el.dataset.key || '');
        if (text.includes(q)) {
            el.classList.remove('search-hidden');
            el.classList.add('search-highlight');
        } else {
            el.classList.add('search-hidden');
            el.classList.remove('search-highlight');
        }
    });
}

/* ── Save btn loading state ── */
document.getElementById('settingsForm').addEventListener('submit', function () {
    const btn = this.querySelector('.save-btn');
    btn.innerHTML = '<i class="ri-loader-4-line animate-spin"></i> Saving…';
    btn.disabled  = true;
});
</script>

