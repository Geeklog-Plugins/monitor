<?php

// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.5.0                                                      |
// +---------------------------------------------------------------------------+
// | japanese_utf-8.php                                                               |
// +---------------------------------------------------------------------------+

/**
 * @package Monitor
 */

global $LANG32;
global $LANG_configsections, $LANG_confignames, $LANG_configsubgroups, $LANG_tab, $LANG_fs;

$LANG_MONITOR_1 = array(
    'plugin_name'           => 'Monitor',
    'home'                  => '概要',
    'health'                => '状態',
    'security'              => 'セキュリティ',
    'file'                  => 'ファイル:',
    'log_file'              => 'ログファイル:',
    'view_logs'             => '現在のログを表示',
    'clear_logs'            => 'ログを消去',
    'configuration'         => '設定',
    'main'                  => 'サイト状態の概要',
    'logs'                  => 'ログファイル',
    'updates'               => 'プラグイン',
    'status'                => '状態',
    'check'                 => 'チェック',
    'value'                 => '値',
    'recommendation'        => '推奨事項',
    'health_ok'             => 'OK',
    'health_info'           => '情報',
    'health_warning'        => '警告',
    'health_error'          => 'エラー',
    'security_observations' => 'セキュリティ観察',
    'ban_integration'       => 'Ban プラグイン',
    'legacy_ban_notice'     => 'Monitor は限定的なセキュリティ観察を記録します。オプションの Ban プラグインを使うと、Monitor を別の禁止エンジンにすることなく集中型のブロック機能を利用できます。',
    'security_status' => 'セキュリティ状態',
    'security_no_issue' => '利用可能な Monitor のセキュリティチェックでは問題は検出されませんでした。',
    'ban_not_installed' => '未インストール',
    'ban_installed' => 'インストール済み',
    'ban_optional_intro' => 'Ban はオプションです。なくても Monitor は限定的なセキュリティ観察を記録し続けます。',
    'ban_optional_capability' => 'Ban をインストールすると、Monitor が利用できる集中型ブロック機能が追加されます。',
    'ban_view_plugin' => 'Ban プラグイン情報を表示',
    'ban_version' => 'バージョン',
    'ban_ip_capability' => 'IP 禁止要求機能',
    'ban_capability_available' => '利用可能',
    'ban_capability_unavailable' => '利用不可',
    'ban_direct_sql' => 'Ban との直接 SQL 連携',
    'ban_direct_sql_no' => 'いいえ',
    'security_no_legacy_table' => '旧 Monitor セキュリティテーブルは存在しません。',
    'security_no_observations' => '最近のセキュリティ観察はありません。',
    'read_only_advice'      => 'Monitor は既定で観察と推奨のみを行います。変更には管理者の明示的な操作が必要です。',
    'config_tooltip_emails' => 'Monitor 通知の送信先をカンマ区切りで指定します。空欄にするとメール通知は無効になります。',
    'config_tooltip_repository' => 'Monitor がプラグインのメタデータとバージョンを確認する公開リポジトリの GitHub 所有者です。空欄にするとリモート確認は無効になります。',
    'config_tooltip_github_token' => '任意の細粒度・読み取り専用 GitHub トークンです。MONITOR_GITHUB_TOKEN が優先されます。認証済みの確認では API 上限が高くなり、タグキャッシュの保持時間が短くなります。',

    // Media diagnostics
    'media_oversized_single' => '1 個の画像が推奨上限を超えています。',
    'media_oversized_multiple' => '%d 個の画像が推奨上限を超えています。',
    'media_show_files' => 'ファイルを表示 (%d)',
    'media_hide_files' => 'ファイルを非表示 (%d)',
    'media_open_file_manager' => 'ファイルマネージャーを開く',
    'media_view_image' => '画像を表示',
    'media_more_files' => 'ほかにも大きすぎる画像があります。表示件数は制限されています。',
    'media_partial_scan' => 'ファイルシステムのスキャンが安全上限に達しました。',

    // Daily log archives
    'log_archive_title' => 'ログアーカイブ',
    'log_archive_intro' => 'Monitor は Geeklog の .log ファイルを毎日ローテーションし、直近 %d 日分のアーカイブを保持します。',
    'log_archive_safety' => 'アクティブなログは、アーカイブコピーが正常に書き込まれた後でのみ切り詰められます。アーカイブは公開 Web ディレクトリ外の path_data 配下に保存されます。',
    'log_archive_empty' => '日次ログアーカイブはまだありません。最初の定期実行でローテーション基準が作成され、次の暦日以降にアーカイブが表示されます。',
    'log_archive_date' => '日付',
    'log_archive_log' => 'ログ',
    'log_archive_size' => 'サイズ',
    'log_archive_actions' => '操作',
    'log_archive_view' => '表示',
    'log_archive_download' => 'ダウンロード',
    'log_archive_back' => 'ログアーカイブに戻る',
    'log_archive_preview_limited' => 'このプレビューにはアーカイブの最新 512 KiB のみが表示されます。完全な日次ログを取得するにはファイルをダウンロードしてください。',
    'log_email_title' => '日次ログ概要',
    'log_email_lines' => 'スキャンした行数',
    'log_email_issue_lines' => 'エラー/警告行',
    'log_email_top_patterns' => 'error.log の主要パターン',
    'log_email_no_activity' => 'この日は内容のある Geeklog ログがアーカイブされませんでした。',

    // Changes monitor
    'changes' => '変更',
    'changes_page_title' => 'Monitor の変更',
    'changes_intro' => 'サイトの軽量スナップショットを比較して、2 回のチェック間の変更を確認します。Monitor は状態だけを記録し、サイトは変更しません。',
    'changes_capture' => '現在の状態を取得',
    'changes_capture_ok' => '現在の状態を保存しました。',
    'changes_capture_failed' => 'Monitor はスナップショットを保存できませんでした。path_data が書き込み可能か確認してください。',
    'changes_baseline_created' => '基準を作成しました。後で別の状態を取得して変更を確認してください。',
    'changes_waiting' => '変更を比較するには 2 つ目のスナップショットが必要です。',
    'changes_period' => '比較期間:',
    'changes_previous' => '前回',
    'changes_current' => '現在',
    'changes_summary_changes' => '変更',
    'changes_summary_plugins' => 'プラグインの変更',
    'changes_summary_log' => '新しいログパターン',
    'changes_summary_snapshots' => 'スナップショット',
    'changes_none' => '2 つのスナップショット間で重要な変更は検出されませんでした。',
    'changes_detected' => '検出された変更',
    'changes_environment' => '環境',
    'changes_plugins' => 'プラグイン',
    'changes_storage' => 'ストレージ',
    'changes_logs' => '新しい error.log アクティビティ',
    'changes_before' => '変更前',
    'changes_after' => '変更後',
    'changes_occurrences' => '件',
    'changes_log_none' => 'この期間の error.log では新しいエラー、警告、例外パターンは検出されませんでした。',
    'changes_log_rotated' => '2 つのスナップショット間で error.log がローテーションまたは切り詰められたため、新しい部分を確実に比較できません。',
    'changes_log_truncated' => '新しいログデータが解析上限を超えました。Monitor は最新 512 KiB のみを解析しました。',
    'changes_current_state' => '現在の状態',
    'changes_geeklog' => 'Geeklog',
    'changes_php' => 'PHP',
    'changes_plugins_count' => 'インストール済みプラグイン',
    'changes_disk_free' => '空きディスク容量',
    'changes_error_log_size' => 'error.log のサイズ',
    'changes_unknown' => '不明',
    'changes_enabled' => '有効',
    'changes_disabled' => '無効',
    'changes_code_geeklog_changed' => 'Geeklog のバージョンが変更されました',
    'changes_code_php_changed' => 'PHP のバージョンが変更されました',
    'changes_code_plugin_installed' => 'プラグインがインストールされました',
    'changes_code_plugin_removed' => 'プラグインが削除されました',
    'changes_code_plugin_version_changed' => 'プラグインのバージョンが変更されました',
    'changes_code_plugin_enabled' => 'プラグインが有効になりました',
    'changes_code_plugin_disabled' => 'プラグインが無効になりました',
    'changes_code_disk_free_decreased' => '空きディスク容量が減少しました',

    // Configuration audit
    'config_audit_title' => '設定監査',
    'config_audit_page_title' => 'Monitor 設定監査',
    'config_audit_quick_description' => 'siteconfig.php とデータベース内の対応する Core 値の差異を確認します。',
    'config_audit_back' => 'Monitor 概要',
    'config_audit_access_denied' => 'アクセス拒否',
    'config_audit_root_only' => 'アクセスは Root 管理者に限定されています。',
    'config_audit_intro_title' => '読み取り専用の設定監査です。',
    'config_audit_intro' => 'siteconfig.php で明示的に定義された Core 値と conf_values の対応値を比較します。差異または無効な値のみ注意が必要です。',
    'config_audit_issues' => '問題',
    'config_audit_review' => '確認',
    'config_audit_normal' => '正常',
    'config_audit_active_host' => 'ホスト:',
    'config_audit_siteconfig' => 'siteconfig.php:',
    'config_audit_unreadable_title' => '警告:',
    'config_audit_unreadable' => 'Monitor は有効な siteconfig.php を読み取れなかったため、比較は不完全です。',
    'config_audit_items_review' => '要確認',
    'config_audit_no_issues' => '設定は整合しています。競合する Core 値はありません。',
    'config_audit_secondary' => '正常または情報値を %d 件表示',
    'config_audit_footer' => '機密値は伏せられます。任意のデータベース整合 SQL が自動実行されることはありません。',
    'config_audit_source_siteconfig' => 'siteconfig.php',
    'config_audit_source_database' => 'データベース',
    'config_audit_priority' => '有効なソース:',
    'config_audit_effective_value' => '有効な値',
    'config_audit_details' => '詳細',
    'config_audit_recommendation' => '推奨事項',
    'config_audit_path' => 'パス:',
    'config_audit_path_exists' => '存在',
    'config_audit_path_missing' => '欠落',
    'config_audit_optional_sql' => '任意のデータベース整合',
    'config_audit_absent' => 'なし',
    'config_audit_redacted' => '[伏字]',
    'config_audit_value_true' => '真',
    'config_audit_value_false' => '偽',
    'config_audit_value_null' => 'NULL',
    'config_audit_value_object' => '[OBJECT]',

    'config_audit_level_ok' => '期待値',
    'config_audit_level_info' => '情報',
    'config_audit_level_review' => '確認',
    'config_audit_level_warning' => '警告',

    'config_audit_status_identical' => '同一',
    'config_audit_status_core_file' => 'siteconfig.php に存在する想定',
    'config_audit_status_file_only' => 'siteconfig.php のみ',
    'config_audit_status_db_unset' => 'データベース値が未設定',
    'config_audit_status_different' => '異なる値',
    'config_audit_status_decode_error' => 'データベース値を読み取れません',
    'config_audit_status_invalid_path' => '無効なパス',

    'config_audit_why_identical' => 'siteconfig.php と conf_values に同じ値があります。',
    'config_audit_why_core_file' => 'この Core キーは通常 siteconfig.php で直接定義されます。',
    'config_audit_why_file_only' => 'conf_values に対応する Core 値がありません。siteconfig.php の値が使用されます。',
    'config_audit_why_db_unset' => 'conf_values に対応する Core 行がありますが値は未設定です。siteconfig.php が引き続き有効です。',
    'config_audit_why_different' => 'siteconfig.php と conf_values の値が異なります。実行時には siteconfig.php が有効です。',
    'config_audit_why_decode_error' => 'conf_values の対応する Core 値を確実にデコードできませんでした。',
    'config_audit_why_invalid_path' => '有効なファイルシステムパスは現在存在しません。',

    'config_audit_action_none' => '対応は不要です。',
    'config_audit_action_file_only' => 'この値をデータベースでも管理する必要がない限り対応は不要です。',
    'config_audit_action_db_unset' => 'データベース値が意図的に未設定か確認してください。',
    'config_audit_action_different' => 'この上書きが意図したものか確認してください。保存値が古い場合のみデータベース値を合わせてください。',
    'config_audit_action_decode_error' => '変更前に conf_values の対応する Core 行を確認してください。',
    'config_audit_action_invalid_path' => '設定されたパスとファイルシステムの可用性を確認してください。',

    // Plugin catalog
    'plugin_catalog_intro' => 'インストール済みプラグインを、設定された GitHub 所有者の公開リポジトリと比較します。Monitor は利用可能なバージョンと候補を報告しますが、コードのインストールや更新は行いません。',
    'plugin_catalog_owner' => 'GitHub ソース:',
    'plugin_catalog_refresh' => 'GitHub データを更新',
    'plugin_catalog_installed' => 'インストール済みプラグイン',
    'plugin_catalog_discover' => 'プラグインを探す',
    'plugin_catalog_discover_compatible' => 'このサイトと互換',
    'plugin_catalog_discover_incompatible' => 'このサイトと非互換',
    'plugin_catalog_discover_unknown' => '互換性不明',
    'plugin_catalog_discover_requirements_missing' => '要件未定義',
    'plugin_catalog_discover_metadata_unavailable' => 'メタデータ利用不可',
    'plugin_catalog_discover_intro' => 'このサイトに未インストールの最近の公開リポジトリです。インストール前に互換性とドキュメントを確認してください。',
    'plugin_catalog_legacy_discover' => '古いリポジトリ',
    'plugin_catalog_legacy_intro' => '古い公開リポジトリも役立つ場合がありますが、最近の Geeklog と PHP との互換性は不明です。',
    'plugin_catalog_plugin' => 'プラグイン',
    'plugin_catalog_core_plugin' => 'Core プラグイン',
    'plugin_catalog_core_update' => 'Geeklog 更新で利用可能',
    'plugin_catalog_latest_core_version' => '最新 Core バージョン',
    'plugin_catalog_current_geeklog' => '現在の Geeklog',
    'plugin_catalog_latest_geeklog_baseline' => '最新 Geeklog 基準',
    'plugin_catalog_open_core_plugin' => 'Core プラグインを開く',
    'plugin_catalog_version' => 'バージョン',
    'plugin_catalog_bundled_with_geeklog' => 'Geeklog に同梱',
    'plugin_catalog_available_with_geeklog' => 'Geeklog で利用可能',
    'plugin_catalog_installed_version' => 'インストール済み',
    'plugin_catalog_code_version' => 'コード',
    'plugin_catalog_latest_release' => '最新リリース',
    'plugin_catalog_latest_version' => '最新 GitHub バージョン',
    'plugin_catalog_version_source_release' => 'リリース',
    'plugin_catalog_version_source_tag' => 'タグ',
    'plugin_catalog_state' => '状態',
    'plugin_catalog_enabled' => '有効',
    'plugin_catalog_geeklog' => 'Geeklog 要件',
    'plugin_catalog_php_requirement' => 'PHP 要件',
    'plugin_catalog_update_compatible' => '更新互換',
    'plugin_catalog_update_incompatible' => '更新非互換',
    'plugin_catalog_compatibility_unknown' => '互換性不明',
    'plugin_catalog_repository' => 'リポジトリ',
    'plugin_catalog_yes' => 'はい',
    'plugin_catalog_no' => 'いいえ',
    'plugin_catalog_unknown' => '不明',
    'plugin_catalog_no_release' => 'リリースメタデータなし',
    'plugin_catalog_no_version' => 'リリースまたはバージョンタグが見つかりません',
    'plugin_catalog_no_repository' => '対応するリポジトリなし',
    'plugin_catalog_catalog_unavailable' => 'GitHub カタログ利用不可',
    'plugin_catalog_current_compatible' => 'この Geeklog では最新',
    'plugin_catalog_latest_compatible_version' => '最新互換バージョン',
    'plugin_catalog_newer_release' => 'より新しいリリース',
    'plugin_catalog_requires_geeklog' => 'Geeklog %s が必要',
    'plugin_catalog_requires_php' => 'PHP %s が必要',
    'plugin_catalog_current' => '最新',
    'plugin_catalog_update' => '更新あり',
    'plugin_catalog_ahead' => 'インストール済みバージョンの方が新しい',
    'plugin_catalog_remote_unavailable' => 'GitHub メタデータは利用できません。ローカルのプラグイン情報は引き続き表示されます。',
    'plugin_catalog_remote_disabled' => '有効な GitHub 所有者が設定されていないため、リモートメタデータ確認は無効です。',
    'plugin_catalog_none_discoverable' => 'この GitHub 所有者について追加の最近のプラグインリポジトリは見つかりませんでした。',
    'plugin_catalog_open_repository' => 'リポジトリを開く',
    'plugin_catalog_open_release' => 'リリースを開く',
    'plugin_catalog_open_version' => 'バージョンを開く',
    'plugin_catalog_updated' => '更新済み',
    'plugin_catalog_summary_installed' => 'インストール済み',
    'plugin_catalog_summary_updates' => '更新あり',
    'plugin_catalog_summary_discover' => '最近の候補',
    'plugin_catalog_summary_unmatched' => 'GitHub 対応なし');

$PLG_monitor_MESSAGE3002 = $LANG32[9];
$PLG_monitor_MESSAGE3003 = 'Monitor はデータベース移行を完了できませんでした。インストール済みバージョンは変更されていません。error.log を確認してアップグレードを再試行してください。';

$GLOBALS['LANG_configsections']['monitor'] = array(
    'label' => 'Monitor',
    'title' => 'Monitor 設定'
);

$GLOBALS['LANG_configsubgroups']['monitor'] = array(
    'sg_main' => '主設定'
);

$GLOBALS['LANG_tab']['monitor'] = array(
    'tab_main' => 'メイン'
);

$GLOBALS['LANG_fs']['monitor'] = array(
    'fs_main' => '一般設定'
);

$GLOBALS['LANG_confignames']['monitor'] = array(
    'emails' => 'Monitor の任意通知に使用するメールアドレス一覧（カンマ区切り）',
    'repository' => 'プラグインのリリースメタデータに使用する GitHub リポジトリ所有者（既定: Geeklog-Plugins）。空欄にするとリモートメタデータ確認を無効にします。',
    'github_token' => 'API メタデータ要求用の任意の GitHub トークン。細粒度の読み取り専用トークンを推奨します。環境変数 MONITOR_GITHUB_TOKEN が優先されます。'
);

$LANG_configsections =& $GLOBALS['LANG_configsections'];
$LANG_configsubgroups =& $GLOBALS['LANG_configsubgroups'];
$LANG_tab =& $GLOBALS['LANG_tab'];
$LANG_fs =& $GLOBALS['LANG_fs'];
$LANG_confignames =& $GLOBALS['LANG_confignames'];
