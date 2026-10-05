<?php

// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.5.0                                                      |
// +---------------------------------------------------------------------------+
// | russian_utf-8.php                                                               |
// +---------------------------------------------------------------------------+

/**
 * @package Monitor
 */

global $LANG32;
global $LANG_configsections, $LANG_confignames, $LANG_configsubgroups, $LANG_tab, $LANG_fs;

$LANG_MONITOR_1 = array(
    'plugin_name'           => 'Monitor',
    'home'                  => 'Обзор',
    'health'                => 'Состояние',
    'security'              => 'Безопасность',
    'file'                  => 'Файл:',
    'log_file'              => 'Файл журнала:',
    'view_logs'             => 'Просмотреть текущие журналы',
    'clear_logs'            => 'Очистить журналы',
    'configuration'         => 'Конфигурация',
    'main'                  => 'Обзор состояния сайта',
    'logs'                  => 'Файлы журналов',
    'updates'               => 'Плагины',
    'status'                => 'Статус',
    'check'                 => 'Проверка',
    'value'                 => 'Значение',
    'recommendation'        => 'Рекомендация',
    'health_ok'             => 'OK',
    'health_info'           => 'Информация',
    'health_warning'        => 'Предупреждение',
    'health_error'          => 'Ошибка',
    'security_observations' => 'Наблюдения безопасности',
    'ban_integration'       => 'Плагин Ban',
    'legacy_ban_notice'     => 'Monitor записывает ограниченные наблюдения безопасности. Необязательный плагин Ban может предоставить централизованные функции блокировки, не превращая Monitor во второй механизм блокировок.',
    'security_status' => 'Статус безопасности',
    'security_no_issue' => 'Доступные проверки безопасности Monitor не обнаружили проблем.',
    'ban_not_installed' => 'Не установлен',
    'ban_installed' => 'Установлен',
    'ban_optional_intro' => 'Ban необязателен. Monitor продолжает записывать ограниченные наблюдения безопасности и без него.',
    'ban_optional_capability' => 'Установка Ban добавляет централизованные функции блокировки, которые Monitor может использовать при наличии.',
    'ban_view_plugin' => 'Просмотреть информацию о плагине Ban',
    'ban_version' => 'Версия',
    'ban_ip_capability' => 'Возможность запроса блокировки IP',
    'ban_capability_available' => 'Доступно',
    'ban_capability_unavailable' => 'Недоступно',
    'ban_direct_sql' => 'Прямая SQL-связь с Ban',
    'ban_direct_sql_no' => 'Нет',
    'security_no_legacy_table' => 'Устаревшая таблица безопасности Monitor отсутствует.',
    'security_no_observations' => 'Нет недавних наблюдений безопасности.',
    'read_only_advice'      => 'По умолчанию Monitor наблюдает и рекомендует. Для изменений требуется явное действие администратора.',
    'config_tooltip_emails' => 'Необязательные получатели уведомлений Monitor, разделённые запятыми. Оставьте поле пустым, чтобы отключить уведомления по электронной почте.',
    'config_tooltip_repository' => 'Владелец GitHub, чьи публичные репозитории Monitor проверяет для получения метаданных и версий плагинов. Оставьте поле пустым, чтобы отключить удалённые проверки.',
    'config_tooltip_github_token' => 'Необязательный GitHub-токен с детальными правами только на чтение. MONITOR_GITHUB_TOKEN имеет приоритет. Аутентифицированные проверки имеют более высокий лимит API и используют более короткий кэш тегов.',

    // Media diagnostics
    'media_oversized_single' => '1 изображение превышает рекомендуемые пределы.',
    'media_oversized_multiple' => '%d изображений превышают рекомендуемые пределы.',
    'media_show_files' => 'Показать файлы (%d)',
    'media_hide_files' => 'Скрыть файлы (%d)',
    'media_open_file_manager' => 'Открыть файловый менеджер',
    'media_view_image' => 'Просмотреть изображение',
    'media_more_files' => 'Есть дополнительные изображения слишком большого размера; список ограничен.',
    'media_partial_scan' => 'Сканирование файловой системы достигло безопасного предела.',

    // Daily log archives
    'log_archive_title' => 'Архивы журналов',
    'log_archive_intro' => 'Monitor ежедневно ротирует файлы .log Geeklog и хранит архивы за последние %d дней.',
    'log_archive_safety' => 'Активный журнал обрезается только после успешной записи архивной копии. Архивы хранятся под path_data, вне публичного веб-каталога.',
    'log_archive_empty' => 'Ежедневный архив журнала пока недоступен. Первый запланированный запуск создаёт базовую точку ротации; архивы появятся после следующего календарного дня.',
    'log_archive_date' => 'Дата',
    'log_archive_log' => 'Журнал',
    'log_archive_size' => 'Размер',
    'log_archive_actions' => 'Действия',
    'log_archive_view' => 'Просмотр',
    'log_archive_download' => 'Скачать',
    'log_archive_back' => 'Назад к архивам журналов',
    'log_archive_preview_limited' => 'Этот предварительный просмотр показывает только последние 512 КиБ архива. Скачайте файл, чтобы получить полный ежедневный журнал.',
    'log_email_title' => 'Ежедневная сводка журнала',
    'log_email_lines' => 'Просканировано строк',
    'log_email_issue_lines' => 'Строки ошибок/предупреждений',
    'log_email_top_patterns' => 'Основные шаблоны error.log',
    'log_email_no_activity' => 'За этот день не было архивировано ни одного непустого журнала Geeklog.',

    // Changes monitor
    'changes' => 'Изменения',
    'changes_page_title' => 'Изменения Monitor',
    'changes_intro' => 'Сравнивает лёгкие снимки сайта, чтобы увидеть изменения между двумя проверками. Monitor записывает только состояние и не изменяет сайт.',
    'changes_capture' => 'Сохранить текущее состояние',
    'changes_capture_ok' => 'Текущее состояние сохранено.',
    'changes_capture_failed' => 'Monitor не смог сохранить снимок. Проверьте, доступен ли path_data для записи.',
    'changes_baseline_created' => 'Базовая точка создана. Позже сохраните ещё одно состояние, чтобы увидеть изменения.',
    'changes_waiting' => 'Для сравнения изменений требуется второй снимок.',
    'changes_period' => 'Сравниваемый период:',
    'changes_previous' => 'Предыдущее',
    'changes_current' => 'Текущее',
    'changes_summary_changes' => 'Изменения',
    'changes_summary_plugins' => 'Изменения плагинов',
    'changes_summary_log' => 'Новые шаблоны журнала',
    'changes_summary_snapshots' => 'Снимки',
    'changes_none' => 'Между этими двумя снимками не обнаружено существенных изменений.',
    'changes_detected' => 'Обнаруженные изменения',
    'changes_environment' => 'Среда',
    'changes_plugins' => 'Плагины',
    'changes_storage' => 'Хранилище',
    'changes_logs' => 'Новая активность error.log',
    'changes_before' => 'До',
    'changes_after' => 'После',
    'changes_occurrences' => 'случай(ев)',
    'changes_log_none' => 'За этот период в error.log не обнаружено новых шаблонов ошибок, предупреждений или исключений.',
    'changes_log_rotated' => 'Между двумя снимками error.log был ротирован или обрезан, поэтому новый фрагмент нельзя надёжно сравнить.',
    'changes_log_truncated' => 'Новые данные журнала превысили предел анализа. Monitor проанализировал только последние 512 КиБ.',
    'changes_current_state' => 'Текущее состояние',
    'changes_geeklog' => 'Geeklog',
    'changes_php' => 'PHP',
    'changes_plugins_count' => 'Установленные плагины',
    'changes_disk_free' => 'Свободное место на диске',
    'changes_error_log_size' => 'Размер error.log',
    'changes_unknown' => 'Неизвестно',
    'changes_enabled' => 'включено',
    'changes_disabled' => 'выключено',
    'changes_code_geeklog_changed' => 'Версия Geeklog изменена',
    'changes_code_php_changed' => 'Версия PHP изменена',
    'changes_code_plugin_installed' => 'Плагин установлен',
    'changes_code_plugin_removed' => 'Плагин удалён',
    'changes_code_plugin_version_changed' => 'Версия плагина изменена',
    'changes_code_plugin_enabled' => 'Плагин включён',
    'changes_code_plugin_disabled' => 'Плагин выключен',
    'changes_code_disk_free_decreased' => 'Свободное место на диске уменьшилось',

    // Configuration audit
    'config_audit_title' => 'Аудит конфигурации',
    'config_audit_page_title' => 'Аудит конфигурации Monitor',
    'config_audit_quick_description' => 'Проверяет различия между siteconfig.php и соответствующими значениями Core в базе данных.',
    'config_audit_back' => 'Обзор Monitor',
    'config_audit_access_denied' => 'Доступ запрещён',
    'config_audit_root_only' => 'Доступ разрешён только администраторам Root.',
    'config_audit_intro_title' => 'Аудит конфигурации только для чтения.',
    'config_audit_intro' => 'Сравнивает значения Core, явно заданные в siteconfig.php, с соответствующими значениями в conf_values. Внимания требуют только различия или недопустимые значения.',
    'config_audit_issues' => 'Проблемы',
    'config_audit_review' => 'Проверить',
    'config_audit_normal' => 'Норма',
    'config_audit_active_host' => 'Хост:',
    'config_audit_siteconfig' => 'siteconfig.php:',
    'config_audit_unreadable_title' => 'Предупреждение:',
    'config_audit_unreadable' => 'Monitor не смог прочитать активный siteconfig.php, поэтому сравнение неполное.',
    'config_audit_items_review' => 'Требует внимания',
    'config_audit_no_issues' => 'Конфигурация согласована. Конфликтующих значений Core, требующих внимания, нет.',
    'config_audit_secondary' => 'Показать %d нормальных или информационных значений',
    'config_audit_footer' => 'Чувствительные значения скрыты. Необязательный SQL для выравнивания базы никогда не выполняется автоматически.',
    'config_audit_source_siteconfig' => 'siteconfig.php',
    'config_audit_source_database' => 'База данных',
    'config_audit_priority' => 'Действующий источник:',
    'config_audit_effective_value' => 'Действующее значение',
    'config_audit_details' => 'Подробности',
    'config_audit_recommendation' => 'Рекомендация',
    'config_audit_path' => 'Путь:',
    'config_audit_path_exists' => 'существует',
    'config_audit_path_missing' => 'отсутствует',
    'config_audit_optional_sql' => 'Необязательное выравнивание базы данных',
    'config_audit_absent' => 'ОТСУТСТВУЕТ',
    'config_audit_redacted' => '[СКРЫТО]',
    'config_audit_value_true' => 'истина',
    'config_audit_value_false' => 'ложь',
    'config_audit_value_null' => 'NULL',
    'config_audit_value_object' => '[OBJECT]',

    'config_audit_level_ok' => 'Ожидается',
    'config_audit_level_info' => 'Информация',
    'config_audit_level_review' => 'Проверить',
    'config_audit_level_warning' => 'Предупреждение',

    'config_audit_status_identical' => 'Идентично',
    'config_audit_status_core_file' => 'Ожидается в siteconfig.php',
    'config_audit_status_file_only' => 'Только siteconfig.php',
    'config_audit_status_db_unset' => 'Значение базы не задано',
    'config_audit_status_different' => 'Разные значения',
    'config_audit_status_decode_error' => 'Значение базы не читается',
    'config_audit_status_invalid_path' => 'Недопустимый путь',

    'config_audit_why_identical' => 'Одинаковое значение существует в siteconfig.php и conf_values.',
    'config_audit_why_core_file' => 'Этот ключ Core обычно определяется непосредственно в siteconfig.php.',
    'config_audit_why_file_only' => 'В conf_values нет соответствующего значения Core. Используется значение siteconfig.php.',
    'config_audit_why_db_unset' => 'В conf_values существует соответствующая строка Core, но её значение не задано. siteconfig.php остаётся действующим.',
    'config_audit_why_different' => 'siteconfig.php и conf_values содержат разные значения. Во время выполнения действует siteconfig.php.',
    'config_audit_why_decode_error' => 'Соответствующее значение Core в conf_values не удалось надёжно декодировать.',
    'config_audit_why_invalid_path' => 'Действующий путь файловой системы сейчас не существует.',

    'config_audit_action_none' => 'Действия не требуются.',
    'config_audit_action_file_only' => 'Действия не требуются, если это значение не должно также управляться в базе данных.',
    'config_audit_action_db_unset' => 'Проверьте, что значение базы данных намеренно не задано.',
    'config_audit_action_different' => 'Проверьте, что переопределение сделано намеренно. Выравнивайте значение базы только если сохранённое значение устарело.',
    'config_audit_action_decode_error' => 'Перед изменениями проверьте соответствующую строку Core в conf_values.',
    'config_audit_action_invalid_path' => 'Проверьте настроенный путь и доступность файловой системы.',

    // Plugin catalog
    'plugin_catalog_intro' => 'Установленные плагины сравниваются с публичными репозиториями настроенного владельца GitHub. Monitor сообщает о доступных версиях и кандидатах, но никогда не устанавливает и не обновляет код.',
    'plugin_catalog_owner' => 'Источник GitHub:',
    'plugin_catalog_refresh' => 'Обновить данные GitHub',
    'plugin_catalog_installed' => 'Установленные плагины',
    'plugin_catalog_discover' => 'Найти плагины',
    'plugin_catalog_discover_compatible' => 'Совместимо с этим сайтом',
    'plugin_catalog_discover_incompatible' => 'Несовместимо с этим сайтом',
    'plugin_catalog_discover_unknown' => 'Совместимость неизвестна',
    'plugin_catalog_discover_requirements_missing' => 'Требования не указаны',
    'plugin_catalog_discover_metadata_unavailable' => 'Метаданные недоступны',
    'plugin_catalog_discover_intro' => 'Недавние публичные репозитории, не установленные на этом сайте. Перед установкой проверьте совместимость и документацию.',
    'plugin_catalog_legacy_discover' => 'Старые репозитории',
    'plugin_catalog_legacy_intro' => 'Старые публичные репозитории могут быть полезны, но их совместимость с современными версиями Geeklog и PHP неизвестна.',
    'plugin_catalog_plugin' => 'Плагин',
    'plugin_catalog_core_plugin' => 'Плагин Core',
    'plugin_catalog_core_update' => 'Доступно с обновлением Geeklog',
    'plugin_catalog_latest_core_version' => 'Последняя версия Core',
    'plugin_catalog_current_geeklog' => 'Текущий Geeklog',
    'plugin_catalog_latest_geeklog_baseline' => 'Последняя базовая версия Geeklog',
    'plugin_catalog_open_core_plugin' => 'Открыть плагин Core',
    'plugin_catalog_version' => 'Версия',
    'plugin_catalog_bundled_with_geeklog' => 'Поставляется с Geeklog',
    'plugin_catalog_available_with_geeklog' => 'Доступно с Geeklog',
    'plugin_catalog_installed_version' => 'Установлен',
    'plugin_catalog_code_version' => 'Код',
    'plugin_catalog_latest_release' => 'Последний релиз',
    'plugin_catalog_latest_version' => 'Последняя версия GitHub',
    'plugin_catalog_version_source_release' => 'Релиз',
    'plugin_catalog_version_source_tag' => 'Тег',
    'plugin_catalog_state' => 'Состояние',
    'plugin_catalog_enabled' => 'Включено',
    'plugin_catalog_geeklog' => 'Требование Geeklog',
    'plugin_catalog_php_requirement' => 'Требование PHP',
    'plugin_catalog_update_compatible' => 'Обновление совместимо',
    'plugin_catalog_update_incompatible' => 'Обновление несовместимо',
    'plugin_catalog_compatibility_unknown' => 'Совместимость неизвестна',
    'plugin_catalog_repository' => 'Репозиторий',
    'plugin_catalog_yes' => 'Да',
    'plugin_catalog_no' => 'Нет',
    'plugin_catalog_unknown' => 'Неизвестно',
    'plugin_catalog_no_release' => 'Нет метаданных релиза',
    'plugin_catalog_no_version' => 'Релиз или тег версии не найден',
    'plugin_catalog_no_repository' => 'Соответствующий репозиторий не найден',
    'plugin_catalog_catalog_unavailable' => 'Каталог GitHub недоступен',
    'plugin_catalog_current_compatible' => 'Актуально для этого Geeklog',
    'plugin_catalog_latest_compatible_version' => 'Последняя совместимая версия',
    'plugin_catalog_newer_release' => 'Более новый релиз',
    'plugin_catalog_requires_geeklog' => 'требуется Geeklog %s',
    'plugin_catalog_requires_php' => 'требуется PHP %s',
    'plugin_catalog_current' => 'Актуально',
    'plugin_catalog_update' => 'Доступно обновление',
    'plugin_catalog_ahead' => 'Установленная версия новее',
    'plugin_catalog_remote_unavailable' => 'Метаданные GitHub недоступны. Локальная информация о плагине всё равно отображается.',
    'plugin_catalog_remote_disabled' => 'Удалённые проверки метаданных отключены, поскольку не настроен допустимый владелец GitHub.',
    'plugin_catalog_none_discoverable' => 'Для этого владельца GitHub не найдено дополнительных недавних репозиториев плагинов.',
    'plugin_catalog_open_repository' => 'Открыть репозиторий',
    'plugin_catalog_open_release' => 'Открыть релиз',
    'plugin_catalog_open_version' => 'Открыть версию',
    'plugin_catalog_updated' => 'Обновлено',
    'plugin_catalog_summary_installed' => 'Установлен',
    'plugin_catalog_summary_updates' => 'Доступны обновления',
    'plugin_catalog_summary_discover' => 'Недавние кандидаты',
    'plugin_catalog_summary_unmatched' => 'Без совпадения GitHub');

$PLG_monitor_MESSAGE3002 = $LANG32[9];
$PLG_monitor_MESSAGE3003 = 'Monitor не смог завершить миграцию базы данных. Установленная версия не была изменена; проверьте error.log и повторите обновление.';

$GLOBALS['LANG_configsections']['monitor'] = array(
    'label' => 'Monitor',
    'title' => 'Конфигурация Monitor'
);

$GLOBALS['LANG_configsubgroups']['monitor'] = array(
    'sg_main' => 'Основные настройки'
);

$GLOBALS['LANG_tab']['monitor'] = array(
    'tab_main' => 'Основное'
);

$GLOBALS['LANG_fs']['monitor'] = array(
    'fs_main' => 'Общие настройки'
);

$GLOBALS['LANG_confignames']['monitor'] = array(
    'emails' => 'Список адресов электронной почты для необязательных уведомлений Monitor (через запятую)',
    'repository' => 'Владелец репозитория GitHub для метаданных релизов плагинов (по умолчанию: Geeklog-Plugins). Оставьте пустым, чтобы отключить удалённые проверки метаданных.',
    'github_token' => 'Необязательный токен GitHub для запросов метаданных API. Предпочтителен токен только для чтения с точными разрешениями. Переменная окружения MONITOR_GITHUB_TOKEN имеет приоритет.'
);

$LANG_configsections =& $GLOBALS['LANG_configsections'];
$LANG_configsubgroups =& $GLOBALS['LANG_configsubgroups'];
$LANG_tab =& $GLOBALS['LANG_tab'];
$LANG_fs =& $GLOBALS['LANG_fs'];
$LANG_confignames =& $GLOBALS['LANG_confignames'];
