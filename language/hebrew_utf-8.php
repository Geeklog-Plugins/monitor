<?php

// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.5.0                                                      |
// +---------------------------------------------------------------------------+
// | hebrew_utf-8.php                                                               |
// +---------------------------------------------------------------------------+

/**
 * @package Monitor
 */

global $LANG32;
global $LANG_configsections, $LANG_confignames, $LANG_configsubgroups, $LANG_tab, $LANG_fs;

$LANG_MONITOR_1 = array(
    'plugin_name'           => 'Monitor',
    'home'                  => 'סקירה',
    'health'                => 'מצב',
    'security'              => 'אבטחה',
    'file'                  => 'קובץ:',
    'log_file'              => 'קובץ יומן:',
    'view_logs'             => 'הצגת יומנים נוכחיים',
    'clear_logs'            => 'ניקוי יומנים',
    'configuration'         => 'הגדרות',
    'main'                  => 'סקירת מצב האתר',
    'logs'                  => 'קובצי יומן',
    'updates'               => 'תוספים',
    'status'                => 'מצב',
    'check'                 => 'בדיקה',
    'value'                 => 'ערך',
    'recommendation'        => 'המלצה',
    'health_ok'             => 'OK',
    'health_info'           => 'מידע',
    'health_warning'        => 'אזהרה',
    'health_error'          => 'שגיאה',
    'security_observations' => 'תצפיות אבטחה',
    'ban_integration'       => 'תוסף Ban',
    'legacy_ban_notice'     => 'Monitor מתעד תצפיות אבטחה מוגבלות. התוסף האופציונלי Ban יכול לספק יכולות חסימה מרכזיות מבלי להפוך את Monitor למנוע חסימה נוסף.',
    'security_status' => 'מצב אבטחה',
    'security_no_issue' => 'לא זוהתה בעיה בבדיקות האבטחה הזמינות של Monitor.',
    'ban_not_installed' => 'לא מותקן',
    'ban_installed' => 'מותקן',
    'ban_optional_intro' => 'Ban הוא אופציונלי. Monitor ממשיך לתעד את תצפיות האבטחה המוגבלות גם בלעדיו.',
    'ban_optional_capability' => 'התקנת Ban מוסיפה יכולות חסימה מרכזיות ש-Monitor יכול להשתמש בהן כאשר הן זמינות.',
    'ban_view_plugin' => 'הצגת מידע על תוסף Ban',
    'ban_version' => 'גרסה',
    'ban_ip_capability' => 'יכולת בקשת חסימת IP',
    'ban_capability_available' => 'זמין',
    'ban_capability_unavailable' => 'לא זמין',
    'ban_direct_sql' => 'קישור SQL ישיר ל-Ban',
    'ban_direct_sql_no' => 'לא',
    'security_no_legacy_table' => 'לא קיימת טבלת אבטחה ישנה של Monitor.',
    'security_no_observations' => 'אין תצפיות אבטחה אחרונות.',
    'read_only_advice'      => 'כברירת מחדל Monitor צופה וממליץ בלבד. שינויים דורשים פעולה מפורשת של מנהל.',
    'config_tooltip_emails' => 'נמענים אופציונליים להתראות Monitor, מופרדים בפסיקים. השאירו ריק כדי להשבית התראות בדוא״ל.',
    'config_tooltip_repository' => 'בעל חשבון GitHub שמאגריו הציבוריים נבדקים על ידי Monitor לצורך מטא-נתונים וגרסאות של תוספים. השאירו ריק כדי להשבית בדיקות מרוחקות.',
    'config_tooltip_github_token' => 'אסימון GitHub אופציונלי, מוגבל והרשאת קריאה בלבד. MONITOR_GITHUB_TOKEN מקבל עדיפות. בדיקות מאומתות נהנות ממכסת API גבוהה יותר ומשתמשות במטמון תגיות קצר יותר.',

    // Media diagnostics
    'media_oversized_single' => 'תמונה אחת חורגת מהמגבלות המומלצות.',
    'media_oversized_multiple' => '%d תמונות חורגות מהמגבלות המומלצות.',
    'media_show_files' => 'הצגת קבצים (%d)',
    'media_hide_files' => 'הסתרת קבצים (%d)',
    'media_open_file_manager' => 'פתיחת מנהל הקבצים',
    'media_view_image' => 'הצגת תמונה',
    'media_more_files' => 'קיימות תמונות גדולות נוספות; הרשימה מוגבלת.',
    'media_partial_scan' => 'סריקת מערכת הקבצים הגיעה למגבלת הבטיחות.',

    // Daily log archives
    'log_archive_title' => 'ארכיוני יומן',
    'log_archive_intro' => 'Monitor מבצע סבב יומי לקובצי .log של Geeklog ושומר ארכיונים של %d הימים האחרונים.',
    'log_archive_safety' => 'יומן פעיל נקצץ רק לאחר שעותק הארכיון נכתב בהצלחה. הארכיונים נשמרים מתחת ל-path_data, מחוץ לתיקיית האינטרנט הציבורית.',
    'log_archive_empty' => 'עדיין אין ארכיון יומן יומי זמין. ההרצה המתוזמנת הראשונה יוצרת את בסיס הסבב; הארכיונים יופיעו לאחר יום הלוח הבא.',
    'log_archive_date' => 'תאריך',
    'log_archive_log' => 'יומן',
    'log_archive_size' => 'גודל',
    'log_archive_actions' => 'פעולות',
    'log_archive_view' => 'הצגה',
    'log_archive_download' => 'הורדה',
    'log_archive_back' => 'חזרה לארכיוני היומן',
    'log_archive_preview_limited' => 'תצוגה מקדימה זו מציגה רק את 512 KiB האחרונים של הארכיון. הורידו את הקובץ לקבלת היומן היומי המלא.',
    'log_email_title' => 'סיכום יומן יומי',
    'log_email_lines' => 'שורות שנסרקו',
    'log_email_issue_lines' => 'שורות שגיאה/אזהרה',
    'log_email_top_patterns' => 'דפוסי error.log המובילים',
    'log_email_no_activity' => 'לא נשמר ארכיון של יומן Geeklog שאינו ריק עבור יום זה.',

    // Changes monitor
    'changes' => 'שינויים',
    'changes_page_title' => 'שינויים ב-Monitor',
    'changes_intro' => 'השוואת תמונות מצב קלות של האתר כדי לראות מה השתנה בין שתי בדיקות. Monitor מתעד רק מצב ואינו משנה את האתר.',
    'changes_capture' => 'לכידת המצב הנוכחי',
    'changes_capture_ok' => 'המצב הנוכחי נשמר.',
    'changes_capture_failed' => 'Monitor לא הצליח לשמור את תמונת המצב. בדקו שניתן לכתוב אל path_data.',
    'changes_baseline_created' => 'נוצר בסיס. לכדו מצב נוסף מאוחר יותר כדי לראות מה השתנה.',
    'changes_waiting' => 'נדרשת תמונת מצב שנייה לפני שניתן להשוות שינויים.',
    'changes_period' => 'תקופה מושווית:',
    'changes_previous' => 'קודם',
    'changes_current' => 'נוכחי',
    'changes_summary_changes' => 'שינויים',
    'changes_summary_plugins' => 'שינויים בתוספים',
    'changes_summary_log' => 'דפוסי יומן חדשים',
    'changes_summary_snapshots' => 'תמונות מצב',
    'changes_none' => 'לא זוהה שינוי משמעותי בין שתי תמונות המצב.',
    'changes_detected' => 'שינויים שזוהו',
    'changes_environment' => 'סביבה',
    'changes_plugins' => 'תוספים',
    'changes_storage' => 'אחסון',
    'changes_logs' => 'פעילות חדשה ב-error.log',
    'changes_before' => 'לפני',
    'changes_after' => 'אחרי',
    'changes_occurrences' => 'מופע(ים)',
    'changes_log_none' => 'לא זוהה ב-error.log דפוס חדש של שגיאה, אזהרה או חריגה בתקופה זו.',
    'changes_log_rotated' => 'error.log עבר סבב או קוצץ בין שתי תמונות המצב, ולכן לא ניתן להשוות את החלק החדש באופן מהימן.',
    'changes_log_truncated' => 'נתוני היומן החדשים עברו את מגבלת הניתוח. Monitor ניתח רק את 512 KiB האחרונים.',
    'changes_current_state' => 'מצב נוכחי',
    'changes_geeklog' => 'Geeklog',
    'changes_php' => 'PHP',
    'changes_plugins_count' => 'תוספים מותקנים',
    'changes_disk_free' => 'שטח דיסק פנוי',
    'changes_error_log_size' => 'גודל error.log',
    'changes_unknown' => 'לא ידוע',
    'changes_enabled' => 'מופעל',
    'changes_disabled' => 'מושבת',
    'changes_code_geeklog_changed' => 'גרסת Geeklog השתנתה',
    'changes_code_php_changed' => 'גרסת PHP השתנתה',
    'changes_code_plugin_installed' => 'תוסף הותקן',
    'changes_code_plugin_removed' => 'תוסף הוסר',
    'changes_code_plugin_version_changed' => 'גרסת תוסף השתנתה',
    'changes_code_plugin_enabled' => 'תוסף הופעל',
    'changes_code_plugin_disabled' => 'תוסף הושבת',
    'changes_code_disk_free_decreased' => 'שטח הדיסק הפנוי הצטמצם',

    // Configuration audit
    'config_audit_title' => 'בדיקת הגדרות',
    'config_audit_page_title' => 'בדיקת הגדרות Monitor',
    'config_audit_quick_description' => 'בדיקת הבדלים בין siteconfig.php לערכי Core תואמים במסד הנתונים.',
    'config_audit_back' => 'סקירת Monitor',
    'config_audit_access_denied' => 'הגישה נדחתה',
    'config_audit_root_only' => 'הגישה שמורה למנהלי Root.',
    'config_audit_intro_title' => 'בדיקת הגדרות לקריאה בלבד.',
    'config_audit_intro' => 'השוואת ערכי Core שהוגדרו במפורש ב-siteconfig.php לערכים תואמים ב-conf_values. רק הבדלים או ערכים לא תקינים דורשים תשומת לב.',
    'config_audit_issues' => 'בעיות',
    'config_audit_review' => 'לבדיקה',
    'config_audit_normal' => 'תקין',
    'config_audit_active_host' => 'מארח:',
    'config_audit_siteconfig' => 'siteconfig.php:',
    'config_audit_unreadable_title' => 'אזהרה:',
    'config_audit_unreadable' => 'Monitor לא הצליח לקרוא את siteconfig.php הפעיל, ולכן ההשוואה אינה מלאה.',
    'config_audit_items_review' => 'דורש תשומת לב',
    'config_audit_no_issues' => 'ההגדרות עקביות. אין ערך Core סותר שדורש תשומת לב.',
    'config_audit_secondary' => 'הצגת %d ערכים תקינים או מידעיים',
    'config_audit_footer' => 'ערכים רגישים מוסתרים. SQL אופציונלי ליישור מסד הנתונים אינו מופעל אוטומטית לעולם.',
    'config_audit_source_siteconfig' => 'siteconfig.php',
    'config_audit_source_database' => 'מסד נתונים',
    'config_audit_priority' => 'מקור אפקטיבי:',
    'config_audit_effective_value' => 'ערך אפקטיבי',
    'config_audit_details' => 'פרטים',
    'config_audit_recommendation' => 'המלצה',
    'config_audit_path' => 'נתיב:',
    'config_audit_path_exists' => 'קיים',
    'config_audit_path_missing' => 'חסר',
    'config_audit_optional_sql' => 'יישור אופציונלי של מסד הנתונים',
    'config_audit_absent' => 'חסר',
    'config_audit_redacted' => '[מוסתר]',
    'config_audit_value_true' => 'אמת',
    'config_audit_value_false' => 'שקר',
    'config_audit_value_null' => 'NULL',
    'config_audit_value_object' => '[OBJECT]',

    'config_audit_level_ok' => 'צפוי',
    'config_audit_level_info' => 'מידע',
    'config_audit_level_review' => 'לבדיקה',
    'config_audit_level_warning' => 'אזהרה',

    'config_audit_status_identical' => 'זהה',
    'config_audit_status_core_file' => 'צפוי ב-siteconfig.php',
    'config_audit_status_file_only' => 'siteconfig.php בלבד',
    'config_audit_status_db_unset' => 'ערך מסד הנתונים לא מוגדר',
    'config_audit_status_different' => 'ערכים שונים',
    'config_audit_status_decode_error' => 'ערך מסד הנתונים אינו קריא',
    'config_audit_status_invalid_path' => 'נתיב לא תקין',

    'config_audit_why_identical' => 'אותו ערך קיים ב-siteconfig.php וב-conf_values.',
    'config_audit_why_core_file' => 'מפתח Core זה מוגדר בדרך כלל ישירות ב-siteconfig.php.',
    'config_audit_why_file_only' => 'לא קיים ערך Core תואם ב-conf_values. נעשה שימוש בערך מ-siteconfig.php.',
    'config_audit_why_db_unset' => 'קיימת שורת Core תואמת ב-conf_values אך הערך שלה לא מוגדר. siteconfig.php נשאר אפקטיבי.',
    'config_audit_why_different' => 'siteconfig.php ו-conf_values מכילים ערכים שונים. בזמן ריצה siteconfig.php הוא האפקטיבי.',
    'config_audit_why_decode_error' => 'לא ניתן היה לפענח באופן מהימן את ערך ה-Core התואם ב-conf_values.',
    'config_audit_why_invalid_path' => 'נתיב מערכת הקבצים האפקטיבי אינו קיים כרגע.',

    'config_audit_action_none' => 'אין צורך בפעולה.',
    'config_audit_action_file_only' => 'אין צורך בפעולה אלא אם ערך זה אמור להיות מנוהל גם במסד הנתונים.',
    'config_audit_action_db_unset' => 'ודאו שערך מסד הנתונים לא הוגדר בכוונה.',
    'config_audit_action_different' => 'ודאו שהעקיפה מכוונת. ישרו את ערך מסד הנתונים רק אם הערך השמור מיושן.',
    'config_audit_action_decode_error' => 'בדקו את שורת ה-Core התואמת ב-conf_values לפני כל שינוי.',
    'config_audit_action_invalid_path' => 'בדקו את הנתיב המוגדר ואת זמינות מערכת הקבצים.',

    // Plugin catalog
    'plugin_catalog_intro' => 'התוספים המותקנים מושווים למאגרים ציבוריים של בעלי GitHub שהוגדרו. Monitor מדווח על גרסאות זמינות ומועמדים שנמצאו, אך לעולם אינו מתקין או מעדכן קוד.',
    'plugin_catalog_owner' => 'מקור GitHub:',
    'plugin_catalog_refresh' => 'רענון נתוני GitHub',
    'plugin_catalog_installed' => 'תוספים מותקנים',
    'plugin_catalog_discover' => 'גילוי תוספים',
    'plugin_catalog_discover_compatible' => 'תואם לאתר זה',
    'plugin_catalog_discover_incompatible' => 'לא תואם לאתר זה',
    'plugin_catalog_discover_unknown' => 'תאימות לא ידועה',
    'plugin_catalog_discover_requirements_missing' => 'דרישות לא הוצהרו',
    'plugin_catalog_discover_metadata_unavailable' => 'מטא-נתונים לא זמינים',
    'plugin_catalog_discover_intro' => 'מאגרים ציבוריים עדכניים שאינם מותקנים באתר זה. בדקו תאימות ותיעוד לפני התקנה.',
    'plugin_catalog_legacy_discover' => 'מאגרים ישנים יותר',
    'plugin_catalog_legacy_intro' => 'מאגרים ציבוריים ישנים יותר עדיין עשויים להיות שימושיים, אך תאימותם לגרסאות Geeklog ו-PHP עדכניות אינה ידועה.',
    'plugin_catalog_plugin' => 'תוסף',
    'plugin_catalog_core_plugin' => 'תוסף Core',
    'plugin_catalog_core_update' => 'זמין עם עדכון Geeklog',
    'plugin_catalog_latest_core_version' => 'גרסת Core האחרונה',
    'plugin_catalog_current_geeklog' => 'Geeklog נוכחי',
    'plugin_catalog_latest_geeklog_baseline' => 'בסיס Geeklog האחרון',
    'plugin_catalog_open_core_plugin' => 'פתיחת תוסף Core',
    'plugin_catalog_version' => 'גרסה',
    'plugin_catalog_bundled_with_geeklog' => 'מצורף ל-Geeklog',
    'plugin_catalog_available_with_geeklog' => 'זמין עם Geeklog',
    'plugin_catalog_installed_version' => 'מותקן',
    'plugin_catalog_code_version' => 'קוד',
    'plugin_catalog_latest_release' => 'מהדורה אחרונה',
    'plugin_catalog_latest_version' => 'גרסת GitHub האחרונה',
    'plugin_catalog_version_source_release' => 'מהדורה',
    'plugin_catalog_version_source_tag' => 'תג',
    'plugin_catalog_state' => 'מצב',
    'plugin_catalog_enabled' => 'מופעל',
    'plugin_catalog_geeklog' => 'דרישת Geeklog',
    'plugin_catalog_php_requirement' => 'דרישת PHP',
    'plugin_catalog_update_compatible' => 'עדכון תואם',
    'plugin_catalog_update_incompatible' => 'עדכון לא תואם',
    'plugin_catalog_compatibility_unknown' => 'תאימות לא ידועה',
    'plugin_catalog_repository' => 'מאגר',
    'plugin_catalog_yes' => 'כן',
    'plugin_catalog_no' => 'לא',
    'plugin_catalog_unknown' => 'לא ידוע',
    'plugin_catalog_no_release' => 'אין מטא-נתוני מהדורה',
    'plugin_catalog_no_version' => 'לא נמצאו מהדורה או תג גרסה',
    'plugin_catalog_no_repository' => 'לא נמצא מאגר תואם',
    'plugin_catalog_catalog_unavailable' => 'קטלוג GitHub לא זמין',
    'plugin_catalog_current_compatible' => 'מעודכן עבור Geeklog זה',
    'plugin_catalog_latest_compatible_version' => 'הגרסה התואמת האחרונה',
    'plugin_catalog_newer_release' => 'מהדורה חדשה יותר',
    'plugin_catalog_requires_geeklog' => 'דורש Geeklog %s',
    'plugin_catalog_requires_php' => 'דורש PHP %s',
    'plugin_catalog_current' => 'מעודכן',
    'plugin_catalog_update' => 'עדכון זמין',
    'plugin_catalog_ahead' => 'הגרסה המותקנת חדשה יותר',
    'plugin_catalog_remote_unavailable' => 'מטא-נתוני GitHub אינם זמינים. מידע מקומי על התוסף עדיין מוצג.',
    'plugin_catalog_remote_disabled' => 'בדיקות מטא-נתונים מרוחקות מושבתות כי לא הוגדר בעל GitHub תקין.',
    'plugin_catalog_none_discoverable' => 'לא נמצא מאגר תוסף עדכני נוסף עבור בעל GitHub זה.',
    'plugin_catalog_open_repository' => 'פתיחת מאגר',
    'plugin_catalog_open_release' => 'פתיחת מהדורה',
    'plugin_catalog_open_version' => 'פתיחת גרסה',
    'plugin_catalog_updated' => 'עודכן',
    'plugin_catalog_summary_installed' => 'מותקן',
    'plugin_catalog_summary_updates' => 'עדכונים זמינים',
    'plugin_catalog_summary_discover' => 'מועמדים עדכניים',
    'plugin_catalog_summary_unmatched' => 'ללא התאמת GitHub');

$PLG_monitor_MESSAGE3002 = $LANG32[9];
$PLG_monitor_MESSAGE3003 = 'Monitor לא הצליח להשלים את העברת מסד הנתונים. הגרסה המותקנת לא שונתה; בדקו את error.log ונסו שוב את השדרוג.';

$GLOBALS['LANG_configsections']['monitor'] = array(
    'label' => 'Monitor',
    'title' => 'הגדרות Monitor'
);

$GLOBALS['LANG_configsubgroups']['monitor'] = array(
    'sg_main' => 'הגדרות ראשיות'
);

$GLOBALS['LANG_tab']['monitor'] = array(
    'tab_main' => 'ראשי'
);

$GLOBALS['LANG_fs']['monitor'] = array(
    'fs_main' => 'הגדרות כלליות'
);

$GLOBALS['LANG_confignames']['monitor'] = array(
    'emails' => 'רשימת כתובות דוא״ל להתראות אופציונליות של Monitor (מופרדות בפסיקים)',
    'repository' => 'בעל מאגר GitHub המשמש למטא-נתוני מהדורות תוספים (ברירת מחדל: Geeklog-Plugins). השאירו ריק כדי להשבית בדיקות מטא-נתונים מרוחקות.',
    'github_token' => 'אסימון GitHub אופציונלי לבקשות מטא-נתונים דרך API. מומלץ אסימון קריאה בלבד עם הרשאות מדויקות. משתנה הסביבה MONITOR_GITHUB_TOKEN מקבל עדיפות.'
);

$LANG_configsections =& $GLOBALS['LANG_configsections'];
$LANG_configsubgroups =& $GLOBALS['LANG_configsubgroups'];
$LANG_tab =& $GLOBALS['LANG_tab'];
$LANG_fs =& $GLOBALS['LANG_fs'];
$LANG_confignames =& $GLOBALS['LANG_confignames'];
