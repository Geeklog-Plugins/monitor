<?php

// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.5.0                                                      |
// +---------------------------------------------------------------------------+
// | chinese_traditional_utf-8.php                                                               |
// +---------------------------------------------------------------------------+

/**
 * @package Monitor
 */

global $LANG32;
global $LANG_configsections, $LANG_confignames, $LANG_configsubgroups, $LANG_tab, $LANG_fs;

$LANG_MONITOR_1 = array(
    'plugin_name'           => 'Monitor',
    'home'                  => '概覽',
    'health'                => '狀態',
    'security'              => '安全',
    'file'                  => '檔案：',
    'log_file'              => '日誌檔案：',
    'view_logs'             => '檢視目前日誌',
    'clear_logs'            => '清除日誌',
    'configuration'         => '設定',
    'main'                  => '網站狀態概覽',
    'logs'                  => '日誌檔案',
    'updates'               => '外掛',
    'status'                => '狀態',
    'check'                 => '檢查',
    'value'                 => '值',
    'recommendation'        => '建議',
    'health_ok'             => 'OK',
    'health_info'           => '資訊',
    'health_warning'        => '警告',
    'health_error'          => '錯誤',
    'security_observations' => '安全觀察',
    'ban_integration'       => 'Ban 外掛',
    'legacy_ban_notice'     => 'Monitor 記錄有限的安全觀察。可選的 Ban 外掛可提供集中式封鎖功能，而不會把 Monitor 變成第二套封鎖引擎。',
    'security_status' => '安全狀態',
    'security_no_issue' => '可用的 Monitor 安全檢查未偵測到问题。',
    'ban_not_installed' => '未安裝',
    'ban_installed' => '已安裝',
    'ban_optional_intro' => 'Ban 為可選項。即使未安裝，Monitor 仍會继续記錄有限的安全觀察。',
    'ban_optional_capability' => '安裝 Ban 後會增加集中式封鎖功能，Monitor 可在其可用時使用。',
    'ban_view_plugin' => '檢視 Ban 外掛資訊',
    'ban_version' => '版本',
    'ban_ip_capability' => 'IP 封鎖請求能力',
    'ban_capability_available' => '可用',
    'ban_capability_unavailable' => '不可用',
    'ban_direct_sql' => '與 Ban 的直接 SQL 耦合',
    'ban_direct_sql_no' => '否',
    'security_no_legacy_table' => '不存在舊版 Monitor 安全表。',
    'security_no_observations' => '沒有最近的安全觀察。',
    'read_only_advice'      => 'Monitor 默認只進行觀察和建議。任何變更都需要管理員明确执行操作。',
    'config_tooltip_emails' => '可選的 Monitor 通知收件人，多個地址以逗號分隔。留空可停用電子郵件通知。',
    'config_tooltip_repository' => 'Monitor 用於檢查外掛程式中繼資料和版本的 GitHub 公開儲存庫擁有者。留空可停用遠端檢查。',
    'config_tooltip_github_token' => '可選的細粒度唯讀 GitHub 權杖。MONITOR_GITHUB_TOKEN 優先。已驗證的檢查具有較高的 API 配額並使用較短的標籤快取。',

    // Media diagnostics
    'media_oversized_single' => '1 张圖片超出建議限制。',
    'media_oversized_multiple' => '%d 张圖片超出建議限制。',
    'media_show_files' => '顯示檔案（%d）',
    'media_hide_files' => '隱藏檔案（%d）',
    'media_open_file_manager' => '打開檔案管理器',
    'media_view_image' => '檢視圖片',
    'media_more_files' => '還有其他超大圖片；列表已限制顯示數量。',
    'media_partial_scan' => '檔案系統掃描已达到安全限制。',

    // Daily log archives
    'log_archive_title' => '日誌封存',
    'log_archive_intro' => 'Monitor 每日輪替 Geeklog 的 .log 檔案，並保留最近 %d 天的封存。',
    'log_archive_safety' => '活动日誌僅在封存副本成功寫入後才會被截断。封存儲存在 path_data 下方，位於公開 Web 目錄之外。',
    'log_archive_empty' => '目前還沒有每日日誌封存。第一次排程工作运行會建立輪替基準；封存將在下一個自然日之後出現。',
    'log_archive_date' => '日期',
    'log_archive_log' => '日誌',
    'log_archive_size' => '大小',
    'log_archive_actions' => '操作',
    'log_archive_view' => '檢視',
    'log_archive_download' => '下載',
    'log_archive_back' => '返回日誌封存',
    'log_archive_preview_limited' => '此预覽僅顯示封存中最新的 512 KiB。下載檔案可獲取完整的每日日誌。',
    'log_email_title' => '每日日誌摘要',
    'log_email_lines' => '掃描行數',
    'log_email_issue_lines' => '錯誤/警告行',
    'log_email_top_patterns' => '主要 error.log 模式',
    'log_email_no_activity' => '當天沒有封存任何非空 Geeklog 日誌。',

    // Changes monitor
    'changes' => '變更',
    'changes_page_title' => 'Monitor 變更',
    'changes_intro' => '比較網站的轻量快照，以檢視两次檢查之间發生了什麼變化。Monitor 僅記錄狀態，不修改網站。',
    'changes_capture' => '擷取目前狀態',
    'changes_capture_ok' => '目前狀態已儲存。',
    'changes_capture_failed' => 'Monitor 無法儲存快照。請檢查 path_data 是否可寫。',
    'changes_baseline_created' => '已创建基準。稍後再擷取一個狀態以檢視發生了哪些變化。',
    'changes_waiting' => '在比較變更之前需要第二個快照。',
    'changes_period' => '比較期间：',
    'changes_previous' => '上一個',
    'changes_current' => '目前',
    'changes_summary_changes' => '變更',
    'changes_summary_plugins' => '外掛變更',
    'changes_summary_log' => '新的日誌模式',
    'changes_summary_snapshots' => '快照',
    'changes_none' => '這两個快照之间未偵測到有意义的變化。',
    'changes_detected' => '偵測到的變更',
    'changes_environment' => '環境',
    'changes_plugins' => '外掛',
    'changes_storage' => '儲存',
    'changes_logs' => '新的 error.log 活动',
    'changes_before' => '之前',
    'changes_after' => '之後',
    'changes_occurrences' => '次',
    'changes_log_none' => '此期间的 error.log 中未偵測到新的錯誤、警告或异常模式。',
    'changes_log_rotated' => '在两個快照之间，error.log 已輪替或被截断，因此無法可靠比較新部分。',
    'changes_log_truncated' => '新的日誌數據超過分析限制。Monitor 僅分析了最新的 512 KiB。',
    'changes_current_state' => '目前狀態',
    'changes_geeklog' => 'Geeklog',
    'changes_php' => 'PHP',
    'changes_plugins_count' => '已安裝外掛',
    'changes_disk_free' => '可用磁碟空間',
    'changes_error_log_size' => 'error.log 大小',
    'changes_unknown' => '未知',
    'changes_enabled' => '已啟用',
    'changes_disabled' => '已停用',
    'changes_code_geeklog_changed' => 'Geeklog 版本已變更',
    'changes_code_php_changed' => 'PHP 版本已變更',
    'changes_code_plugin_installed' => '外掛已安裝',
    'changes_code_plugin_removed' => '外掛已移除',
    'changes_code_plugin_version_changed' => '外掛版本已變更',
    'changes_code_plugin_enabled' => '外掛已啟用',
    'changes_code_plugin_disabled' => '外掛已停用',
    'changes_code_disk_free_decreased' => '可用磁碟空間减少',

    // Configuration audit
    'config_audit_title' => '設定審計',
    'config_audit_page_title' => 'Monitor 設定審計',
    'config_audit_quick_description' => '檢查 siteconfig.php 與資料庫中對應 Core 值之间的差异。',
    'config_audit_back' => 'Monitor 概覽',
    'config_audit_access_denied' => '访问被拒绝',
    'config_audit_root_only' => '僅 Root 管理員可访问。',
    'config_audit_intro_title' => '唯讀設定審計。',
    'config_audit_intro' => '將 siteconfig.php 中明确設定的 Core 值與 conf_values 中對應值進行比較。只有差异或無效值需要注意。',
    'config_audit_issues' => '问题',
    'config_audit_review' => '复核',
    'config_audit_normal' => '正常',
    'config_audit_active_host' => '主机：',
    'config_audit_siteconfig' => 'siteconfig.php：',
    'config_audit_unreadable_title' => '警告：',
    'config_audit_unreadable' => 'Monitor 無法讀取目前生效的 siteconfig.php，因此比較結果不完整。',
    'config_audit_items_review' => '需要注意',
    'config_audit_no_issues' => '設定一致。沒有冲突的 Core 值需要注意。',
    'config_audit_secondary' => '顯示 %d 個正常或資訊性值',
    'config_audit_footer' => '敏感值已隱藏。可選的資料庫對齐 SQL 永远不會自动执行。',
    'config_audit_source_siteconfig' => 'siteconfig.php',
    'config_audit_source_database' => '資料庫',
    'config_audit_priority' => '生效來源：',
    'config_audit_effective_value' => '生效值',
    'config_audit_details' => '詳细資訊',
    'config_audit_recommendation' => '建議',
    'config_audit_path' => '路徑：',
    'config_audit_path_exists' => '存在',
    'config_audit_path_missing' => '缺失',
    'config_audit_optional_sql' => '可選資料庫對齐',
    'config_audit_absent' => '不存在',
    'config_audit_redacted' => '[已隱藏]',
    'config_audit_value_true' => '真',
    'config_audit_value_false' => '假',
    'config_audit_value_null' => 'NULL',
    'config_audit_value_object' => '[OBJECT]',

    'config_audit_level_ok' => '符合预期',
    'config_audit_level_info' => '資訊',
    'config_audit_level_review' => '复核',
    'config_audit_level_warning' => '警告',

    'config_audit_status_identical' => '相同',
    'config_audit_status_core_file' => '應在 siteconfig.php 中',
    'config_audit_status_file_only' => '僅 siteconfig.php',
    'config_audit_status_db_unset' => '資料庫值未設置',
    'config_audit_status_different' => '值不同',
    'config_audit_status_decode_error' => '資料庫值無法讀取',
    'config_audit_status_invalid_path' => '無效路徑',

    'config_audit_why_identical' => 'siteconfig.php 和 conf_values 中存在相同值。',
    'config_audit_why_core_file' => '此 Core 键通常直接在 siteconfig.php 中定义。',
    'config_audit_why_file_only' => 'conf_values 中不存在對應 Core 值。將使用 siteconfig.php 中的值。',
    'config_audit_why_db_unset' => 'conf_values 中存在對應 Core 行，但值未設置。siteconfig.php 仍然生效。',
    'config_audit_why_different' => 'siteconfig.php 與 conf_values 包含不同值。运行時以 siteconfig.php 為准。',
    'config_audit_why_decode_error' => '無法可靠解码 conf_values 中對應的 Core 值。',
    'config_audit_why_invalid_path' => '目前生效的檔案系統路徑不存在。',

    'config_audit_action_none' => '無需操作。',
    'config_audit_action_file_only' => '除非該值也需要在資料庫中管理，否則無需操作。',
    'config_audit_action_db_unset' => '確認資料庫值是否有意保持未設置狀態。',
    'config_audit_action_different' => '確認此覆寫是否有意為之。僅當儲存值已過時時才對齐資料庫值。',
    'config_audit_action_decode_error' => '進行任何變更前，請檢查 conf_values 中對應的 Core 行。',
    'config_audit_action_invalid_path' => '檢查設定的路徑以及檔案系統是否可用。',

    // Plugin catalog
    'plugin_catalog_intro' => '已安裝外掛會與設定的 GitHub 擁有者下的公開儲存庫進行比較。Monitor 會报告可用版本和探索候选項，但绝不會安裝或更新程式碼。',
    'plugin_catalog_owner' => 'GitHub 來源：',
    'plugin_catalog_refresh' => '刷新 GitHub 數據',
    'plugin_catalog_installed' => '已安裝外掛',
    'plugin_catalog_discover' => '探索外掛',
    'plugin_catalog_discover_compatible' => '與此網站相容',
    'plugin_catalog_discover_incompatible' => '與此網站不相容',
    'plugin_catalog_discover_unknown' => '相容性未知',
    'plugin_catalog_discover_requirements_missing' => '未声明需求',
    'plugin_catalog_discover_metadata_unavailable' => '中繼資料不可用',
    'plugin_catalog_discover_intro' => '此網站尚未安裝的近期公開儲存庫。安裝任何内容前請檢查相容性和文檔。',
    'plugin_catalog_legacy_discover' => '較舊的儲存庫',
    'plugin_catalog_legacy_intro' => '較舊的公開儲存庫可能仍有用，但其與近期 Geeklog 和 PHP 的相容性未知。',
    'plugin_catalog_plugin' => '外掛',
    'plugin_catalog_core_plugin' => 'Core 外掛',
    'plugin_catalog_core_update' => '随 Geeklog 更新提供',
    'plugin_catalog_latest_core_version' => '最新 Core 版本',
    'plugin_catalog_current_geeklog' => '目前 Geeklog',
    'plugin_catalog_latest_geeklog_baseline' => '最新 Geeklog 基準',
    'plugin_catalog_open_core_plugin' => '打開 Core 外掛',
    'plugin_catalog_version' => '版本',
    'plugin_catalog_bundled_with_geeklog' => '随 Geeklog 提供',
    'plugin_catalog_available_with_geeklog' => '可随 Geeklog 使用',
    'plugin_catalog_installed_version' => '已安裝',
    'plugin_catalog_code_version' => '程式碼',
    'plugin_catalog_latest_release' => '最新發行版',
    'plugin_catalog_latest_version' => '最新 GitHub 版本',
    'plugin_catalog_version_source_release' => '發行版',
    'plugin_catalog_version_source_tag' => '標籤',
    'plugin_catalog_state' => '狀態',
    'plugin_catalog_enabled' => '已啟用',
    'plugin_catalog_geeklog' => 'Geeklog 需求',
    'plugin_catalog_php_requirement' => 'PHP 需求',
    'plugin_catalog_update_compatible' => '更新相容',
    'plugin_catalog_update_incompatible' => '更新不相容',
    'plugin_catalog_compatibility_unknown' => '相容性未知',
    'plugin_catalog_repository' => '儲存庫',
    'plugin_catalog_yes' => '是',
    'plugin_catalog_no' => '否',
    'plugin_catalog_unknown' => '未知',
    'plugin_catalog_no_release' => '沒有發佈中繼資料',
    'plugin_catalog_no_version' => '未找到發行版或版本標籤',
    'plugin_catalog_no_repository' => '沒有符合的儲存庫',
    'plugin_catalog_catalog_unavailable' => 'GitHub 目錄不可用',
    'plugin_catalog_current_compatible' => '對於此 Geeklog 已是最新',
    'plugin_catalog_latest_compatible_version' => '最新相容版本',
    'plugin_catalog_newer_release' => '有更新的發行版',
    'plugin_catalog_requires_geeklog' => '需要 Geeklog %s',
    'plugin_catalog_requires_php' => '需要 PHP %s',
    'plugin_catalog_current' => '已是最新',
    'plugin_catalog_update' => '有可用更新',
    'plugin_catalog_ahead' => '已安裝版本更新',
    'plugin_catalog_remote_unavailable' => 'GitHub 中繼資料不可用。仍會顯示本地外掛資訊。',
    'plugin_catalog_remote_disabled' => '由於未設定有效的 GitHub 擁有者，远程中繼資料檢查已停用。',
    'plugin_catalog_none_discoverable' => '未找到属於此 GitHub 擁有者的其他近期外掛儲存庫。',
    'plugin_catalog_open_repository' => '打開儲存庫',
    'plugin_catalog_open_release' => '打開發行版',
    'plugin_catalog_open_version' => '打開版本',
    'plugin_catalog_updated' => '已更新',
    'plugin_catalog_summary_installed' => '已安裝',
    'plugin_catalog_summary_updates' => '有可用更新',
    'plugin_catalog_summary_discover' => '近期候选項',
    'plugin_catalog_summary_unmatched' => '無 GitHub 符合');

$PLG_monitor_MESSAGE3002 = $LANG32[9];
$PLG_monitor_MESSAGE3003 = 'Monitor 无法完成数据库迁移。已安装版本未更改；请检查 error.log 后重试升级。';

$GLOBALS['LANG_configsections']['monitor'] = array(
    'label' => 'Monitor',
    'title' => 'Monitor 設定'
);

$GLOBALS['LANG_configsubgroups']['monitor'] = array(
    'sg_main' => '主要設定'
);

$GLOBALS['LANG_tab']['monitor'] = array(
    'tab_main' => '主要'
);

$GLOBALS['LANG_fs']['monitor'] = array(
    'fs_main' => '一般設定'
);

$GLOBALS['LANG_confignames']['monitor'] = array(
    'emails' => '用於可選 Monitor 通知的電子郵件地址列表（用逗号分隔）',
    'repository' => '用於外掛發佈中繼資料的 GitHub 儲存庫擁有者（默認：Geeklog-Plugins）。留空可停用远程中繼資料檢查。',
    'github_token' => '用於 API 中繼資料請求的可選 GitHub 權杖。建議使用细粒度唯讀權杖。環境變量 MONITOR_GITHUB_TOKEN 優先。'
);

$LANG_configsections =& $GLOBALS['LANG_configsections'];
$LANG_configsubgroups =& $GLOBALS['LANG_configsubgroups'];
$LANG_tab =& $GLOBALS['LANG_tab'];
$LANG_fs =& $GLOBALS['LANG_fs'];
$LANG_confignames =& $GLOBALS['LANG_confignames'];
