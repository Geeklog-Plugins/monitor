<?php

// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.5.0                                                      |
// +---------------------------------------------------------------------------+
// | persian_utf-8.php                                                               |
// +---------------------------------------------------------------------------+

/**
 * @package Monitor
 */

global $LANG32;
global $LANG_configsections, $LANG_confignames, $LANG_configsubgroups, $LANG_tab, $LANG_fs;

$LANG_MONITOR_1 = array(
    'plugin_name'           => 'Monitor',
    'home'                  => 'نمای کلی',
    'health'                => 'وضعیت',
    'security'              => 'امنیت',
    'file'                  => 'فایل:',
    'log_file'              => 'فایل گزارش:',
    'view_logs'             => 'مشاهده گزارش‌های فعلی',
    'clear_logs'            => 'پاک‌کردن گزارش‌ها',
    'configuration'         => 'پیکربندی',
    'main'                  => 'نمای کلی وضعیت سایت',
    'logs'                  => 'فایل‌های گزارش',
    'updates'               => 'افزونه‌ها',
    'status'                => 'وضعیت',
    'check'                 => 'بررسی',
    'value'                 => 'مقدار',
    'recommendation'        => 'پیشنهاد',
    'health_ok'             => 'OK',
    'health_info'           => 'اطلاعات',
    'health_warning'        => 'هشدار',
    'health_error'          => 'خطا',
    'security_observations' => 'مشاهدات امنیتی',
    'ban_integration'       => 'افزونه Ban',
    'legacy_ban_notice'     => 'Monitor مشاهدات امنیتی محدودی را ثبت می‌کند. افزونه اختیاری Ban می‌تواند قابلیت‌های مسدودسازی متمرکز فراهم کند، بدون اینکه Monitor به یک موتور مسدودسازی دوم تبدیل شود.',
    'security_status' => 'وضعیت امنیت',
    'security_no_issue' => 'هیچ مشکلی توسط بررسی‌های امنیتی موجود Monitor شناسایی نشد.',
    'ban_not_installed' => 'نصب نشده',
    'ban_installed' => 'نصب شده',
    'ban_optional_intro' => 'Ban اختیاری است. Monitor بدون آن نیز به ثبت مشاهدات امنیتی محدود خود ادامه می‌دهد.',
    'ban_optional_capability' => 'نصب Ban قابلیت‌های مسدودسازی متمرکز را اضافه می‌کند که Monitor می‌تواند در صورت وجود از آن‌ها استفاده کند.',
    'ban_view_plugin' => 'مشاهده اطلاعات افزونه Ban',
    'ban_version' => 'نسخه',
    'ban_ip_capability' => 'قابلیت درخواست مسدودسازی IP',
    'ban_capability_available' => 'در دسترس',
    'ban_capability_unavailable' => 'در دسترس نیست',
    'ban_direct_sql' => 'اتصال مستقیم SQL به Ban',
    'ban_direct_sql_no' => 'خیر',
    'security_no_legacy_table' => 'هیچ جدول امنیتی قدیمی Monitor وجود ندارد.',
    'security_no_observations' => 'مشاهده امنیتی جدیدی وجود ندارد.',
    'read_only_advice'      => 'Monitor به‌طور پیش‌فرض مشاهده و پیشنهاد می‌کند. تغییرات نیازمند اقدام صریح مدیر هستند.',
    'config_tooltip_emails' => 'گیرندگان اختیاری اعلان‌های Monitor را با ویرگول جدا کنید. برای غیرفعال کردن اعلان‌های ایمیلی خالی بگذارید.',
    'config_tooltip_repository' => 'مالک GitHub که Monitor مخزن‌های عمومی او را برای فراداده و نسخه‌های افزونه بررسی می‌کند. برای غیرفعال کردن بررسی‌های راه‌دور خالی بگذارید.',
    'config_tooltip_github_token' => 'توکن اختیاری GitHub با دسترسی دقیق و فقط‌خواندنی. MONITOR_GITHUB_TOKEN اولویت دارد. بررسی‌های احراز هویت‌شده سهمیه API بیشتری دارند و از کش کوتاه‌تری برای تگ‌ها استفاده می‌کنند.',

    // Media diagnostics
    'media_oversized_single' => '۱ تصویر از محدودیت‌های پیشنهادی بیشتر است.',
    'media_oversized_multiple' => '%d تصویر از محدودیت‌های پیشنهادی بیشتر هستند.',
    'media_show_files' => 'نمایش فایل‌ها (%d)',
    'media_hide_files' => 'پنهان‌کردن فایل‌ها (%d)',
    'media_open_file_manager' => 'بازکردن مدیر فایل',
    'media_view_image' => 'مشاهده تصویر',
    'media_more_files' => 'تصاویر بزرگ دیگری نیز وجود دارند؛ فهرست محدود شده است.',
    'media_partial_scan' => 'اسکن سیستم فایل به حد ایمنی خود رسید.',

    // Daily log archives
    'log_archive_title' => 'بایگانی گزارش‌ها',
    'log_archive_intro' => 'Monitor فایل‌های .log مربوط به Geeklog را روزانه چرخش می‌دهد و بایگانی %d روز اخیر را نگه می‌دارد.',
    'log_archive_safety' => 'گزارش فعال فقط پس از نوشته‌شدن موفق نسخه بایگانی کوتاه می‌شود. بایگانی‌ها زیر path_data و خارج از پوشه عمومی وب ذخیره می‌شوند.',
    'log_archive_empty' => 'هنوز بایگانی روزانه‌ای در دسترس نیست. نخستین اجرای زمان‌بندی‌شده خط مبنای چرخش را ایجاد می‌کند؛ بایگانی‌ها پس از روز تقویمی بعدی ظاهر می‌شوند.',
    'log_archive_date' => 'تاریخ',
    'log_archive_log' => 'گزارش',
    'log_archive_size' => 'اندازه',
    'log_archive_actions' => 'عملیات',
    'log_archive_view' => 'مشاهده',
    'log_archive_download' => 'دانلود',
    'log_archive_back' => 'بازگشت به بایگانی گزارش‌ها',
    'log_archive_preview_limited' => 'این پیش‌نمایش فقط جدیدترین ۵۱۲ KiB بایگانی را نشان می‌دهد. برای دریافت گزارش روزانه کامل، فایل را دانلود کنید.',
    'log_email_title' => 'خلاصه روزانه گزارش',
    'log_email_lines' => 'خطوط اسکن‌شده',
    'log_email_issue_lines' => 'خطوط خطا/هشدار',
    'log_email_top_patterns' => 'الگوهای پرتکرار error.log',
    'log_email_no_activity' => 'برای این روز هیچ گزارش غیرخالی Geeklog بایگانی نشد.',

    // Changes monitor
    'changes' => 'تغییرات',
    'changes_page_title' => 'تغییرات Monitor',
    'changes_intro' => 'تصاویر لحظه‌ای سبک سایت را مقایسه کنید تا تغییرات بین دو بررسی مشخص شوند. Monitor فقط وضعیت را ثبت می‌کند و سایت را تغییر نمی‌دهد.',
    'changes_capture' => 'ثبت وضعیت فعلی',
    'changes_capture_ok' => 'وضعیت فعلی ذخیره شد.',
    'changes_capture_failed' => 'Monitor نتوانست تصویر لحظه‌ای را ذخیره کند. بررسی کنید path_data قابل نوشتن باشد.',
    'changes_baseline_created' => 'خط مبنا ایجاد شد. بعداً وضعیت دیگری ثبت کنید تا تغییرات مشخص شوند.',
    'changes_waiting' => 'برای مقایسه تغییرات به تصویر لحظه‌ای دوم نیاز است.',
    'changes_period' => 'دوره مقایسه‌شده:',
    'changes_previous' => 'قبلی',
    'changes_current' => 'فعلی',
    'changes_summary_changes' => 'تغییرات',
    'changes_summary_plugins' => 'تغییرات افزونه',
    'changes_summary_log' => 'الگوهای جدید گزارش',
    'changes_summary_snapshots' => 'تصاویر لحظه‌ای',
    'changes_none' => 'هیچ تغییر معناداری بین این دو تصویر لحظه‌ای شناسایی نشد.',
    'changes_detected' => 'تغییرات شناسایی‌شده',
    'changes_environment' => 'محیط',
    'changes_plugins' => 'افزونه‌ها',
    'changes_storage' => 'فضای ذخیره‌سازی',
    'changes_logs' => 'فعالیت جدید error.log',
    'changes_before' => 'قبل',
    'changes_after' => 'بعد',
    'changes_occurrences' => 'مورد',
    'changes_log_none' => 'در این دوره هیچ الگوی جدید خطا، هشدار یا استثنا در error.log شناسایی نشد.',
    'changes_log_rotated' => 'error.log بین دو تصویر لحظه‌ای چرخش داده یا کوتاه شده است، بنابراین بخش جدید قابل مقایسه مطمئن نیست.',
    'changes_log_truncated' => 'داده‌های جدید گزارش از حد تحلیل عبور کردند. Monitor فقط جدیدترین ۵۱۲ KiB را تحلیل کرد.',
    'changes_current_state' => 'وضعیت فعلی',
    'changes_geeklog' => 'Geeklog',
    'changes_php' => 'PHP',
    'changes_plugins_count' => 'افزونه‌های نصب‌شده',
    'changes_disk_free' => 'فضای آزاد دیسک',
    'changes_error_log_size' => 'اندازه error.log',
    'changes_unknown' => 'نامشخص',
    'changes_enabled' => 'فعال',
    'changes_disabled' => 'غیرفعال',
    'changes_code_geeklog_changed' => 'نسخه Geeklog تغییر کرد',
    'changes_code_php_changed' => 'نسخه PHP تغییر کرد',
    'changes_code_plugin_installed' => 'افزونه نصب شد',
    'changes_code_plugin_removed' => 'افزونه حذف شد',
    'changes_code_plugin_version_changed' => 'نسخه افزونه تغییر کرد',
    'changes_code_plugin_enabled' => 'افزونه فعال شد',
    'changes_code_plugin_disabled' => 'افزونه غیرفعال شد',
    'changes_code_disk_free_decreased' => 'فضای آزاد دیسک کاهش یافت',

    // Configuration audit
    'config_audit_title' => 'ممیزی پیکربندی',
    'config_audit_page_title' => 'ممیزی پیکربندی Monitor',
    'config_audit_quick_description' => 'تفاوت‌های بین siteconfig.php و مقادیر متناظر Core در پایگاه داده را بررسی کنید.',
    'config_audit_back' => 'نمای کلی Monitor',
    'config_audit_access_denied' => 'دسترسی رد شد',
    'config_audit_root_only' => 'دسترسی فقط برای مدیران Root محفوظ است.',
    'config_audit_intro_title' => 'ممیزی پیکربندی فقط‌خواندنی.',
    'config_audit_intro' => 'مقادیر Core که صراحتاً در siteconfig.php تعریف شده‌اند با مقادیر متناظر در conf_values مقایسه می‌شوند. فقط تفاوت‌ها یا مقادیر نامعتبر نیازمند توجه‌اند.',
    'config_audit_issues' => 'مشکلات',
    'config_audit_review' => 'بازبینی',
    'config_audit_normal' => 'عادی',
    'config_audit_active_host' => 'میزبان:',
    'config_audit_siteconfig' => 'siteconfig.php:',
    'config_audit_unreadable_title' => 'هشدار:',
    'config_audit_unreadable' => 'Monitor نتوانست siteconfig.php فعال را بخواند، بنابراین مقایسه ناقص است.',
    'config_audit_items_review' => 'نیازمند توجه',
    'config_audit_no_issues' => 'پیکربندی سازگار است. هیچ مقدار Core متعارضی نیازمند توجه نیست.',
    'config_audit_secondary' => 'نمایش %d مقدار عادی یا اطلاعاتی',
    'config_audit_footer' => 'مقادیر حساس پوشانده می‌شوند. SQL اختیاری برای هم‌ترازسازی پایگاه داده هرگز به‌طور خودکار اجرا نمی‌شود.',
    'config_audit_source_siteconfig' => 'siteconfig.php',
    'config_audit_source_database' => 'پایگاه داده',
    'config_audit_priority' => 'منبع مؤثر:',
    'config_audit_effective_value' => 'مقدار مؤثر',
    'config_audit_details' => 'جزئیات',
    'config_audit_recommendation' => 'پیشنهاد',
    'config_audit_path' => 'مسیر:',
    'config_audit_path_exists' => 'وجود دارد',
    'config_audit_path_missing' => 'وجود ندارد',
    'config_audit_optional_sql' => 'هم‌ترازسازی اختیاری پایگاه داده',
    'config_audit_absent' => 'غایب',
    'config_audit_redacted' => '[پوشانده‌شده]',
    'config_audit_value_true' => 'درست',
    'config_audit_value_false' => 'نادرست',
    'config_audit_value_null' => 'NULL',
    'config_audit_value_object' => '[OBJECT]',

    'config_audit_level_ok' => 'مورد انتظار',
    'config_audit_level_info' => 'اطلاعات',
    'config_audit_level_review' => 'بازبینی',
    'config_audit_level_warning' => 'هشدار',

    'config_audit_status_identical' => 'یکسان',
    'config_audit_status_core_file' => 'مورد انتظار در siteconfig.php',
    'config_audit_status_file_only' => 'فقط siteconfig.php',
    'config_audit_status_db_unset' => 'مقدار پایگاه داده تنظیم نشده',
    'config_audit_status_different' => 'مقادیر متفاوت',
    'config_audit_status_decode_error' => 'مقدار پایگاه داده قابل خواندن نیست',
    'config_audit_status_invalid_path' => 'مسیر نامعتبر',

    'config_audit_why_identical' => 'همان مقدار در siteconfig.php و conf_values وجود دارد.',
    'config_audit_why_core_file' => 'این کلید Core معمولاً مستقیماً در siteconfig.php تعریف می‌شود.',
    'config_audit_why_file_only' => 'هیچ مقدار Core متناظری در conf_values وجود ندارد. مقدار siteconfig.php استفاده می‌شود.',
    'config_audit_why_db_unset' => 'یک ردیف Core متناظر در conf_values وجود دارد اما مقدار آن تنظیم نشده است. siteconfig.php همچنان مؤثر است.',
    'config_audit_why_different' => 'siteconfig.php و conf_values مقادیر متفاوتی دارند. در زمان اجرا siteconfig.php مؤثر است.',
    'config_audit_why_decode_error' => 'مقدار Core متناظر در conf_values به‌طور قابل اعتماد قابل رمزگشایی نبود.',
    'config_audit_why_invalid_path' => 'مسیر مؤثر سیستم فایل در حال حاضر وجود ندارد.',

    'config_audit_action_none' => 'اقدامی لازم نیست.',
    'config_audit_action_file_only' => 'اقدامی لازم نیست مگر اینکه این مقدار باید در پایگاه داده نیز مدیریت شود.',
    'config_audit_action_db_unset' => 'بررسی کنید که مقدار پایگاه داده عمداً تنظیم نشده باشد.',
    'config_audit_action_different' => 'بررسی کنید که این بازنویسی عمدی باشد. فقط در صورت منسوخ‌بودن مقدار ذخیره‌شده، مقدار پایگاه داده را هم‌تراز کنید.',
    'config_audit_action_decode_error' => 'پیش از هر تغییری، ردیف Core متناظر در conf_values را بررسی کنید.',
    'config_audit_action_invalid_path' => 'مسیر پیکربندی‌شده و دسترس‌پذیری سیستم فایل را بررسی کنید.',

    // Plugin catalog
    'plugin_catalog_intro' => 'افزونه‌های نصب‌شده با مخازن عمومی مالک GitHub پیکربندی‌شده مقایسه می‌شوند. Monitor نسخه‌های موجود و گزینه‌های کشف‌شده را گزارش می‌کند، اما هرگز کد نصب یا به‌روزرسانی نمی‌کند.',
    'plugin_catalog_owner' => 'منبع GitHub:',
    'plugin_catalog_refresh' => 'به‌روزرسانی داده‌های GitHub',
    'plugin_catalog_installed' => 'افزونه‌های نصب‌شده',
    'plugin_catalog_discover' => 'کشف افزونه‌ها',
    'plugin_catalog_discover_compatible' => 'سازگار با این سایت',
    'plugin_catalog_discover_incompatible' => 'ناسازگار با این سایت',
    'plugin_catalog_discover_unknown' => 'سازگاری نامشخص',
    'plugin_catalog_discover_requirements_missing' => 'نیازمندی‌ها اعلام نشده‌اند',
    'plugin_catalog_discover_metadata_unavailable' => 'فراداده در دسترس نیست',
    'plugin_catalog_discover_intro' => 'مخازن عمومی جدیدی که روی این سایت نصب نشده‌اند. پیش از نصب هر چیزی، سازگاری و مستندات را بررسی کنید.',
    'plugin_catalog_legacy_discover' => 'مخازن قدیمی‌تر',
    'plugin_catalog_legacy_intro' => 'مخازن عمومی قدیمی‌تر ممکن است هنوز مفید باشند، اما سازگاری آن‌ها با نسخه‌های جدید Geeklog و PHP نامشخص است.',
    'plugin_catalog_plugin' => 'افزونه',
    'plugin_catalog_core_plugin' => 'افزونه Core',
    'plugin_catalog_core_update' => 'همراه به‌روزرسانی Geeklog در دسترس',
    'plugin_catalog_latest_core_version' => 'جدیدترین نسخه Core',
    'plugin_catalog_current_geeklog' => 'Geeklog فعلی',
    'plugin_catalog_latest_geeklog_baseline' => 'جدیدترین خط مبنای Geeklog',
    'plugin_catalog_open_core_plugin' => 'بازکردن افزونه Core',
    'plugin_catalog_version' => 'نسخه',
    'plugin_catalog_bundled_with_geeklog' => 'همراه Geeklog',
    'plugin_catalog_available_with_geeklog' => 'همراه Geeklog در دسترس',
    'plugin_catalog_installed_version' => 'نصب شده',
    'plugin_catalog_code_version' => 'کد',
    'plugin_catalog_latest_release' => 'آخرین انتشار',
    'plugin_catalog_latest_version' => 'آخرین نسخه GitHub',
    'plugin_catalog_version_source_release' => 'انتشار',
    'plugin_catalog_version_source_tag' => 'برچسب',
    'plugin_catalog_state' => 'وضعیت',
    'plugin_catalog_enabled' => 'فعال',
    'plugin_catalog_geeklog' => 'نیازمندی Geeklog',
    'plugin_catalog_php_requirement' => 'نیازمندی PHP',
    'plugin_catalog_update_compatible' => 'به‌روزرسانی سازگار',
    'plugin_catalog_update_incompatible' => 'به‌روزرسانی ناسازگار',
    'plugin_catalog_compatibility_unknown' => 'سازگاری نامشخص',
    'plugin_catalog_repository' => 'مخزن',
    'plugin_catalog_yes' => 'بله',
    'plugin_catalog_no' => 'خیر',
    'plugin_catalog_unknown' => 'نامشخص',
    'plugin_catalog_no_release' => 'فراداده انتشار وجود ندارد',
    'plugin_catalog_no_version' => 'هیچ انتشار یا برچسب نسخه‌ای یافت نشد',
    'plugin_catalog_no_repository' => 'مخزن متناظری یافت نشد',
    'plugin_catalog_catalog_unavailable' => 'فهرست GitHub در دسترس نیست',
    'plugin_catalog_current_compatible' => 'برای این Geeklog به‌روز است',
    'plugin_catalog_latest_compatible_version' => 'جدیدترین نسخه سازگار',
    'plugin_catalog_newer_release' => 'انتشار جدیدتر',
    'plugin_catalog_requires_geeklog' => 'نیازمند Geeklog %s',
    'plugin_catalog_requires_php' => 'نیازمند PHP %s',
    'plugin_catalog_current' => 'به‌روز',
    'plugin_catalog_update' => 'به‌روزرسانی موجود است',
    'plugin_catalog_ahead' => 'نسخه نصب‌شده جدیدتر است',
    'plugin_catalog_remote_unavailable' => 'فراداده GitHub در دسترس نیست. اطلاعات محلی افزونه همچنان نمایش داده می‌شود.',
    'plugin_catalog_remote_disabled' => 'بررسی فراداده راه دور غیرفعال است زیرا مالک معتبر GitHub پیکربندی نشده است.',
    'plugin_catalog_none_discoverable' => 'هیچ مخزن افزونه جدید دیگری برای این مالک GitHub یافت نشد.',
    'plugin_catalog_open_repository' => 'بازکردن مخزن',
    'plugin_catalog_open_release' => 'بازکردن انتشار',
    'plugin_catalog_open_version' => 'بازکردن نسخه',
    'plugin_catalog_updated' => 'به‌روزرسانی‌شده',
    'plugin_catalog_summary_installed' => 'نصب شده',
    'plugin_catalog_summary_updates' => 'به‌روزرسانی‌ها موجودند',
    'plugin_catalog_summary_discover' => 'گزینه‌های جدید',
    'plugin_catalog_summary_unmatched' => 'بدون تطابق GitHub');

$PLG_monitor_MESSAGE3002 = $LANG32[9];
$PLG_monitor_MESSAGE3003 = 'Monitor نتوانست مهاجرت پایگاه داده را کامل کند. نسخه نصب‌شده تغییر نکرد؛ error.log را بررسی کرده و ارتقا را دوباره امتحان کنید.';

$GLOBALS['LANG_configsections']['monitor'] = array(
    'label' => 'Monitor',
    'title' => 'پیکربندی Monitor'
);

$GLOBALS['LANG_configsubgroups']['monitor'] = array(
    'sg_main' => 'تنظیمات اصلی'
);

$GLOBALS['LANG_tab']['monitor'] = array(
    'tab_main' => 'اصلی'
);

$GLOBALS['LANG_fs']['monitor'] = array(
    'fs_main' => 'تنظیمات عمومی'
);

$GLOBALS['LANG_confignames']['monitor'] = array(
    'emails' => 'فهرست نشانی‌های ایمیل برای اعلان‌های اختیاری Monitor (جداشده با ویرگول)',
    'repository' => 'مالک مخزن GitHub برای فراداده انتشار افزونه‌ها (پیش‌فرض: Geeklog-Plugins). برای غیرفعال‌کردن بررسی فراداده راه دور، خالی بگذارید.',
    'github_token' => 'توکن اختیاری GitHub برای درخواست‌های فراداده API. توکن فقط‌خواندنی با دسترسی ریزدانه ترجیح داده می‌شود. متغیر محیطی MONITOR_GITHUB_TOKEN اولویت دارد.'
);

$LANG_configsections =& $GLOBALS['LANG_configsections'];
$LANG_configsubgroups =& $GLOBALS['LANG_configsubgroups'];
$LANG_tab =& $GLOBALS['LANG_tab'];
$LANG_fs =& $GLOBALS['LANG_fs'];
$LANG_confignames =& $GLOBALS['LANG_confignames'];
