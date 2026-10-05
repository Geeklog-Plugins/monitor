<?php

// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.5.0                                                      |
// +---------------------------------------------------------------------------+
// | chinese_simplified_utf-8.php                                                               |
// +---------------------------------------------------------------------------+

/**
 * @package Monitor
 */

global $LANG32;
global $LANG_configsections, $LANG_confignames, $LANG_configsubgroups, $LANG_tab, $LANG_fs;

$LANG_MONITOR_1 = array(
    'plugin_name'           => 'Monitor',
    'home'                  => '概览',
    'health'                => '状态',
    'security'              => '安全',
    'file'                  => '文件：',
    'log_file'              => '日志文件：',
    'view_logs'             => '查看当前日志',
    'clear_logs'            => '清除日志',
    'configuration'         => '配置',
    'main'                  => '站点状态概览',
    'logs'                  => '日志文件',
    'updates'               => '插件',
    'status'                => '状态',
    'check'                 => '检查',
    'value'                 => '值',
    'recommendation'        => '建议',
    'health_ok'             => 'OK',
    'health_info'           => '信息',
    'health_warning'        => '警告',
    'health_error'          => '错误',
    'security_observations' => '安全观察',
    'ban_integration'       => 'Ban 插件',
    'legacy_ban_notice'     => 'Monitor 记录有限的安全观察。可选的 Ban 插件可提供集中式阻止功能，而不会把 Monitor 变成第二套封禁引擎。',
    'security_status' => '安全状态',
    'security_no_issue' => '可用的 Monitor 安全检查未检测到问题。',
    'ban_not_installed' => '未安装',
    'ban_installed' => '已安装',
    'ban_optional_intro' => 'Ban 为可选项。即使未安装，Monitor 仍会继续记录有限的安全观察。',
    'ban_optional_capability' => '安装 Ban 后会增加集中式阻止功能，Monitor 可在其可用时使用。',
    'ban_view_plugin' => '查看 Ban 插件信息',
    'ban_version' => '版本',
    'ban_ip_capability' => 'IP 封禁请求能力',
    'ban_capability_available' => '可用',
    'ban_capability_unavailable' => '不可用',
    'ban_direct_sql' => '与 Ban 的直接 SQL 耦合',
    'ban_direct_sql_no' => '否',
    'security_no_legacy_table' => '不存在旧版 Monitor 安全表。',
    'security_no_observations' => '没有最近的安全观察。',
    'read_only_advice'      => 'Monitor 默认只进行观察和建议。任何更改都需要管理员明确执行操作。',
    'config_tooltip_emails' => '可选的 Monitor 通知收件人，多个地址用逗号分隔。留空可禁用电子邮件通知。',
    'config_tooltip_repository' => 'Monitor 用于检查插件元数据和版本的 GitHub 公共仓库所有者。留空可禁用远程检查。',
    'config_tooltip_github_token' => '可选的细粒度只读 GitHub 令牌。MONITOR_GITHUB_TOKEN 优先。经过身份验证的检查具有更高的 API 配额并使用更短的标签缓存。',

    // Media diagnostics
    'media_oversized_single' => '1 张图片超出建议限制。',
    'media_oversized_multiple' => '%d 张图片超出建议限制。',
    'media_show_files' => '显示文件（%d）',
    'media_hide_files' => '隐藏文件（%d）',
    'media_open_file_manager' => '打开文件管理器',
    'media_view_image' => '查看图片',
    'media_more_files' => '还有其他超大图片；列表已限制显示数量。',
    'media_partial_scan' => '文件系统扫描已达到安全限制。',

    // Daily log archives
    'log_archive_title' => '日志归档',
    'log_archive_intro' => 'Monitor 每天轮换 Geeklog 的 .log 文件，并保留最近 %d 天的归档。',
    'log_archive_safety' => '活动日志仅在归档副本成功写入后才会被截断。归档存储在 path_data 下方，位于公共 Web 目录之外。',
    'log_archive_empty' => '目前还没有每日日志归档。第一次计划任务运行会建立轮换基线；归档将在下一个自然日之后出现。',
    'log_archive_date' => '日期',
    'log_archive_log' => '日志',
    'log_archive_size' => '大小',
    'log_archive_actions' => '操作',
    'log_archive_view' => '查看',
    'log_archive_download' => '下载',
    'log_archive_back' => '返回日志归档',
    'log_archive_preview_limited' => '此预览仅显示归档中最新的 512 KiB。下载文件可获取完整的每日日志。',
    'log_email_title' => '每日日志摘要',
    'log_email_lines' => '扫描行数',
    'log_email_issue_lines' => '错误/警告行',
    'log_email_top_patterns' => '主要 error.log 模式',
    'log_email_no_activity' => '当天没有归档任何非空 Geeklog 日志。',

    // Changes monitor
    'changes' => '变更',
    'changes_page_title' => 'Monitor 变更',
    'changes_intro' => '比较站点的轻量快照，以查看两次检查之间发生了什么变化。Monitor 仅记录状态，不修改站点。',
    'changes_capture' => '捕获当前状态',
    'changes_capture_ok' => '当前状态已保存。',
    'changes_capture_failed' => 'Monitor 无法保存快照。请检查 path_data 是否可写。',
    'changes_baseline_created' => '已创建基线。稍后再捕获一个状态以查看发生了哪些变化。',
    'changes_waiting' => '在比较变更之前需要第二个快照。',
    'changes_period' => '比较期间：',
    'changes_previous' => '上一个',
    'changes_current' => '当前',
    'changes_summary_changes' => '变更',
    'changes_summary_plugins' => '插件变更',
    'changes_summary_log' => '新的日志模式',
    'changes_summary_snapshots' => '快照',
    'changes_none' => '这两个快照之间未检测到有意义的变化。',
    'changes_detected' => '检测到的变更',
    'changes_environment' => '环境',
    'changes_plugins' => '插件',
    'changes_storage' => '存储',
    'changes_logs' => '新的 error.log 活动',
    'changes_before' => '之前',
    'changes_after' => '之后',
    'changes_occurrences' => '次',
    'changes_log_none' => '此期间的 error.log 中未检测到新的错误、警告或异常模式。',
    'changes_log_rotated' => '在两个快照之间，error.log 已轮换或被截断，因此无法可靠比较新部分。',
    'changes_log_truncated' => '新的日志数据超过分析限制。Monitor 仅分析了最新的 512 KiB。',
    'changes_current_state' => '当前状态',
    'changes_geeklog' => 'Geeklog',
    'changes_php' => 'PHP',
    'changes_plugins_count' => '已安装插件',
    'changes_disk_free' => '可用磁盘空间',
    'changes_error_log_size' => 'error.log 大小',
    'changes_unknown' => '未知',
    'changes_enabled' => '已启用',
    'changes_disabled' => '已禁用',
    'changes_code_geeklog_changed' => 'Geeklog 版本已更改',
    'changes_code_php_changed' => 'PHP 版本已更改',
    'changes_code_plugin_installed' => '插件已安装',
    'changes_code_plugin_removed' => '插件已移除',
    'changes_code_plugin_version_changed' => '插件版本已更改',
    'changes_code_plugin_enabled' => '插件已启用',
    'changes_code_plugin_disabled' => '插件已禁用',
    'changes_code_disk_free_decreased' => '可用磁盘空间减少',

    // Configuration audit
    'config_audit_title' => '配置审计',
    'config_audit_page_title' => 'Monitor 配置审计',
    'config_audit_quick_description' => '检查 siteconfig.php 与数据库中对应 Core 值之间的差异。',
    'config_audit_back' => 'Monitor 概览',
    'config_audit_access_denied' => '访问被拒绝',
    'config_audit_root_only' => '仅 Root 管理员可访问。',
    'config_audit_intro_title' => '只读配置审计。',
    'config_audit_intro' => '将 siteconfig.php 中明确配置的 Core 值与 conf_values 中对应值进行比较。只有差异或无效值需要关注。',
    'config_audit_issues' => '问题',
    'config_audit_review' => '复核',
    'config_audit_normal' => '正常',
    'config_audit_active_host' => '主机：',
    'config_audit_siteconfig' => 'siteconfig.php：',
    'config_audit_unreadable_title' => '警告：',
    'config_audit_unreadable' => 'Monitor 无法读取当前生效的 siteconfig.php，因此比较结果不完整。',
    'config_audit_items_review' => '需要关注',
    'config_audit_no_issues' => '配置一致。没有冲突的 Core 值需要关注。',
    'config_audit_secondary' => '显示 %d 个正常或信息性值',
    'config_audit_footer' => '敏感值已隐藏。可选的数据库对齐 SQL 永远不会自动执行。',
    'config_audit_source_siteconfig' => 'siteconfig.php',
    'config_audit_source_database' => '数据库',
    'config_audit_priority' => '生效来源：',
    'config_audit_effective_value' => '生效值',
    'config_audit_details' => '详细信息',
    'config_audit_recommendation' => '建议',
    'config_audit_path' => '路径：',
    'config_audit_path_exists' => '存在',
    'config_audit_path_missing' => '缺失',
    'config_audit_optional_sql' => '可选数据库对齐',
    'config_audit_absent' => '不存在',
    'config_audit_redacted' => '[已隐藏]',
    'config_audit_value_true' => '真',
    'config_audit_value_false' => '假',
    'config_audit_value_null' => 'NULL',
    'config_audit_value_object' => '[OBJECT]',

    'config_audit_level_ok' => '符合预期',
    'config_audit_level_info' => '信息',
    'config_audit_level_review' => '复核',
    'config_audit_level_warning' => '警告',

    'config_audit_status_identical' => '相同',
    'config_audit_status_core_file' => '应在 siteconfig.php 中',
    'config_audit_status_file_only' => '仅 siteconfig.php',
    'config_audit_status_db_unset' => '数据库值未设置',
    'config_audit_status_different' => '值不同',
    'config_audit_status_decode_error' => '数据库值无法读取',
    'config_audit_status_invalid_path' => '无效路径',

    'config_audit_why_identical' => 'siteconfig.php 和 conf_values 中存在相同值。',
    'config_audit_why_core_file' => '此 Core 键通常直接在 siteconfig.php 中定义。',
    'config_audit_why_file_only' => 'conf_values 中不存在对应 Core 值。将使用 siteconfig.php 中的值。',
    'config_audit_why_db_unset' => 'conf_values 中存在对应 Core 行，但值未设置。siteconfig.php 仍然生效。',
    'config_audit_why_different' => 'siteconfig.php 与 conf_values 包含不同值。运行时以 siteconfig.php 为准。',
    'config_audit_why_decode_error' => '无法可靠解码 conf_values 中对应的 Core 值。',
    'config_audit_why_invalid_path' => '当前生效的文件系统路径不存在。',

    'config_audit_action_none' => '无需操作。',
    'config_audit_action_file_only' => '除非该值也需要在数据库中管理，否则无需操作。',
    'config_audit_action_db_unset' => '确认数据库值是否有意保持未设置状态。',
    'config_audit_action_different' => '确认此覆盖是否有意为之。仅当存储值已过时时才对齐数据库值。',
    'config_audit_action_decode_error' => '进行任何更改前，请检查 conf_values 中对应的 Core 行。',
    'config_audit_action_invalid_path' => '检查配置的路径以及文件系统是否可用。',

    // Plugin catalog
    'plugin_catalog_intro' => '已安装插件会与配置的 GitHub 所有者下的公共仓库进行比较。Monitor 会报告可用版本和发现候选项，但绝不会安装或更新代码。',
    'plugin_catalog_owner' => 'GitHub 来源：',
    'plugin_catalog_refresh' => '刷新 GitHub 数据',
    'plugin_catalog_installed' => '已安装插件',
    'plugin_catalog_discover' => '发现插件',
    'plugin_catalog_discover_compatible' => '与此站点兼容',
    'plugin_catalog_discover_incompatible' => '与此站点不兼容',
    'plugin_catalog_discover_unknown' => '兼容性未知',
    'plugin_catalog_discover_requirements_missing' => '未声明要求',
    'plugin_catalog_discover_metadata_unavailable' => '元数据不可用',
    'plugin_catalog_discover_intro' => '此站点尚未安装的近期公共仓库。安装任何内容前请检查兼容性和文档。',
    'plugin_catalog_legacy_discover' => '较旧的仓库',
    'plugin_catalog_legacy_intro' => '较旧的公共仓库可能仍有用，但其与近期 Geeklog 和 PHP 的兼容性未知。',
    'plugin_catalog_plugin' => '插件',
    'plugin_catalog_core_plugin' => 'Core 插件',
    'plugin_catalog_core_update' => '随 Geeklog 更新提供',
    'plugin_catalog_latest_core_version' => '最新 Core 版本',
    'plugin_catalog_current_geeklog' => '当前 Geeklog',
    'plugin_catalog_latest_geeklog_baseline' => '最新 Geeklog 基线',
    'plugin_catalog_open_core_plugin' => '打开 Core 插件',
    'plugin_catalog_version' => '版本',
    'plugin_catalog_bundled_with_geeklog' => '随 Geeklog 提供',
    'plugin_catalog_available_with_geeklog' => '可随 Geeklog 使用',
    'plugin_catalog_installed_version' => '已安装',
    'plugin_catalog_code_version' => '代码',
    'plugin_catalog_latest_release' => '最新发布版',
    'plugin_catalog_latest_version' => '最新 GitHub 版本',
    'plugin_catalog_version_source_release' => '发布版',
    'plugin_catalog_version_source_tag' => '标签',
    'plugin_catalog_state' => '状态',
    'plugin_catalog_enabled' => '已启用',
    'plugin_catalog_geeklog' => 'Geeklog 要求',
    'plugin_catalog_php_requirement' => 'PHP 要求',
    'plugin_catalog_update_compatible' => '更新兼容',
    'plugin_catalog_update_incompatible' => '更新不兼容',
    'plugin_catalog_compatibility_unknown' => '兼容性未知',
    'plugin_catalog_repository' => '仓库',
    'plugin_catalog_yes' => '是',
    'plugin_catalog_no' => '否',
    'plugin_catalog_unknown' => '未知',
    'plugin_catalog_no_release' => '没有发布元数据',
    'plugin_catalog_no_version' => '未找到发布版或版本标签',
    'plugin_catalog_no_repository' => '没有匹配的仓库',
    'plugin_catalog_catalog_unavailable' => 'GitHub 目录不可用',
    'plugin_catalog_current_compatible' => '对于此 Geeklog 已是最新',
    'plugin_catalog_latest_compatible_version' => '最新兼容版本',
    'plugin_catalog_newer_release' => '有更新的发布版',
    'plugin_catalog_requires_geeklog' => '需要 Geeklog %s',
    'plugin_catalog_requires_php' => '需要 PHP %s',
    'plugin_catalog_current' => '已是最新',
    'plugin_catalog_update' => '有可用更新',
    'plugin_catalog_ahead' => '已安装版本更新',
    'plugin_catalog_remote_unavailable' => 'GitHub 元数据不可用。仍会显示本地插件信息。',
    'plugin_catalog_remote_disabled' => '由于未配置有效的 GitHub 所有者，远程元数据检查已禁用。',
    'plugin_catalog_none_discoverable' => '未找到属于此 GitHub 所有者的其他近期插件仓库。',
    'plugin_catalog_open_repository' => '打开仓库',
    'plugin_catalog_open_release' => '打开发布版',
    'plugin_catalog_open_version' => '打开版本',
    'plugin_catalog_updated' => '已更新',
    'plugin_catalog_summary_installed' => '已安装',
    'plugin_catalog_summary_updates' => '有可用更新',
    'plugin_catalog_summary_discover' => '近期候选项',
    'plugin_catalog_summary_unmatched' => '无 GitHub 匹配');

$PLG_monitor_MESSAGE3002 = $LANG32[9];
$PLG_monitor_MESSAGE3003 = 'Monitor 无法完成数据库迁移。已安装版本未更改；请检查 error.log 后重试升级。';

$GLOBALS['LANG_configsections']['monitor'] = array(
    'label' => 'Monitor',
    'title' => 'Monitor 配置'
);

$GLOBALS['LANG_configsubgroups']['monitor'] = array(
    'sg_main' => '主要设置'
);

$GLOBALS['LANG_tab']['monitor'] = array(
    'tab_main' => '主要'
);

$GLOBALS['LANG_fs']['monitor'] = array(
    'fs_main' => '常规设置'
);

$GLOBALS['LANG_confignames']['monitor'] = array(
    'emails' => '用于可选 Monitor 通知的电子邮件地址列表（用逗号分隔）',
    'repository' => '用于插件发布元数据的 GitHub 仓库所有者（默认：Geeklog-Plugins）。留空可禁用远程元数据检查。',
    'github_token' => '用于 API 元数据请求的可选 GitHub 令牌。建议使用细粒度只读令牌。环境变量 MONITOR_GITHUB_TOKEN 优先。'
);

$LANG_configsections =& $GLOBALS['LANG_configsections'];
$LANG_configsubgroups =& $GLOBALS['LANG_configsubgroups'];
$LANG_tab =& $GLOBALS['LANG_tab'];
$LANG_fs =& $GLOBALS['LANG_fs'];
$LANG_confignames =& $GLOBALS['LANG_confignames'];
