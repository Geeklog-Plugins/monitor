<?php

// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.5.0                                                      |
// +---------------------------------------------------------------------------+
// | german_formal_utf-8.php                                                               |
// +---------------------------------------------------------------------------+

/**
 * @package Monitor
 */

global $LANG32;
global $LANG_configsections, $LANG_confignames, $LANG_configsubgroups, $LANG_tab, $LANG_fs;

$LANG_MONITOR_1 = array(
    'plugin_name'           => 'Monitor',
    'home'                  => 'Übersicht',
    'health'                => 'Zustand',
    'security'              => 'Sicherheit',
    'file'                  => 'Datei:',
    'log_file'              => 'Protokolldatei:',
    'view_logs'             => 'Aktuelle Protokolle anzeigen',
    'clear_logs'            => 'Protokolle löschen',
    'configuration'         => 'Konfiguration',
    'main'                  => 'Übersicht zum Website-Zustand',
    'logs'                  => 'Protokolldateien',
    'updates'               => 'Plugins',
    'status'                => 'Status',
    'check'                 => 'Prüfung',
    'value'                 => 'Wert',
    'recommendation'        => 'Empfehlung',
    'health_ok'             => 'OK',
    'health_info'           => 'Information',
    'health_warning'        => 'Warnung',
    'health_error'          => 'Fehler',
    'security_observations' => 'Sicherheitsbeobachtungen',
    'ban_integration'       => 'Ban-Plugin',
    'legacy_ban_notice'     => 'Monitor zeichnet begrenzte Sicherheitsbeobachtungen auf. Das optionale Ban-Plugin kann zentrale Sperrfunktionen bereitstellen, ohne Monitor zu einer zweiten Sperr-Engine zu machen.',
    'security_status' => 'Sicherheitsstatus',
    'security_no_issue' => 'Die verfügbaren Monitor-Sicherheitsprüfungen haben kein Problem erkannt.',
    'ban_not_installed' => 'Nicht installiert',
    'ban_installed' => 'Installiert',
    'ban_optional_intro' => 'Ban ist optional. Monitor zeichnet seine begrenzten Sicherheitsbeobachtungen auch ohne Ban weiterhin auf.',
    'ban_optional_capability' => 'Die Installation von Ban fügt zentrale Sperrfunktionen hinzu, die Monitor bei Verfügbarkeit nutzen kann.',
    'ban_view_plugin' => 'Informationen zum Ban-Plugin anzeigen',
    'ban_version' => 'Version',
    'ban_ip_capability' => 'Funktion für IP-Sperranfragen',
    'ban_capability_available' => 'Verfügbar',
    'ban_capability_unavailable' => 'Nicht verfügbar',
    'ban_direct_sql' => 'Direkte SQL-Kopplung mit Ban',
    'ban_direct_sql_no' => 'Nein',
    'security_no_legacy_table' => 'Es ist keine alte Monitor-Sicherheitstabelle vorhanden.',
    'security_no_observations' => 'Keine aktuellen Sicherheitsbeobachtungen.',
    'read_only_advice'      => 'Monitor beobachtet und empfiehlt standardmäßig. Änderungen erfordern eine ausdrückliche Administratoraktion.',
    'config_tooltip_emails' => 'Optionale, durch Kommas getrennte Empfänger für Monitor-Benachrichtigungen. Leer lassen, um E-Mail-Benachrichtigungen zu deaktivieren.',
    'config_tooltip_repository' => 'GitHub-Eigentümer, dessen öffentliche Repositories Monitor auf Plugin-Metadaten und Versionen prüft. Leer lassen, um Remote-Prüfungen zu deaktivieren.',
    'config_tooltip_github_token' => 'Optionales fein abgestuftes, schreibgeschütztes GitHub-Token. MONITOR_GITHUB_TOKEN hat Vorrang. Authentifizierte Prüfungen haben ein höheres API-Kontingent und verwenden einen kürzeren Tag-Cache.',

    // Media diagnostics
    'media_oversized_single' => '1 Bild überschreitet die empfohlenen Grenzwerte.',
    'media_oversized_multiple' => '%d Bilder überschreiten die empfohlenen Grenzwerte.',
    'media_show_files' => 'Dateien anzeigen (%d)',
    'media_hide_files' => 'Dateien ausblenden (%d)',
    'media_open_file_manager' => 'Dateimanager öffnen',
    'media_view_image' => 'Bild anzeigen',
    'media_more_files' => 'Weitere übergroße Bilder sind vorhanden; die Liste ist begrenzt.',
    'media_partial_scan' => 'Die Dateisystemprüfung hat ihr Sicherheitslimit erreicht.',

    // Daily log archives
    'log_archive_title' => 'Protokollarchive',
    'log_archive_intro' => 'Monitor rotiert die .log-Dateien von Geeklog täglich und bewahrt die Archive der letzten %d Tage auf.',
    'log_archive_safety' => 'Ein aktives Protokoll wird erst gekürzt, nachdem seine Archivkopie erfolgreich geschrieben wurde. Archive werden unter path_data außerhalb des öffentlichen Webverzeichnisses gespeichert.',
    'log_archive_empty' => 'Noch ist kein tägliches Protokollarchiv verfügbar. Der erste geplante Lauf erstellt die Rotationsbasis; Archive erscheinen nach dem nächsten Kalendertag.',
    'log_archive_date' => 'Datum',
    'log_archive_log' => 'Protokoll',
    'log_archive_size' => 'Größe',
    'log_archive_actions' => 'Aktionen',
    'log_archive_view' => 'Anzeigen',
    'log_archive_download' => 'Herunterladen',
    'log_archive_back' => 'Zurück zu den Protokollarchiven',
    'log_archive_preview_limited' => 'Diese Vorschau zeigt nur die neuesten 512 KiB des Archivs. Laden Sie die Datei herunter, um das vollständige Tagesprotokoll zu erhalten.',
    'log_email_title' => 'Tägliche Protokollzusammenfassung',
    'log_email_lines' => 'Analysierte Zeilen',
    'log_email_issue_lines' => 'Fehler-/Warnzeilen',
    'log_email_top_patterns' => 'Häufigste error.log-Muster',
    'log_email_no_activity' => 'Für diesen Tag wurde kein nicht leeres Geeklog-Protokoll archiviert.',

    // Changes monitor
    'changes' => 'Änderungen',
    'changes_page_title' => 'Monitor-Änderungen',
    'changes_intro' => 'Vergleichen Sie leichte Momentaufnahmen der Website, um Änderungen zwischen zwei Prüfungen zu sehen. Monitor zeichnet nur den Zustand auf und verändert die Website nicht.',
    'changes_capture' => 'Aktuellen Zustand erfassen',
    'changes_capture_ok' => 'Aktueller Zustand gespeichert.',
    'changes_capture_failed' => 'Monitor konnte die Momentaufnahme nicht speichern. Prüfen Sie, ob path_data beschreibbar ist.',
    'changes_baseline_created' => 'Basis erstellt. Erfassen Sie später einen weiteren Zustand, um Änderungen zu sehen.',
    'changes_waiting' => 'Vor dem Vergleich der Änderungen ist eine zweite Momentaufnahme erforderlich.',
    'changes_period' => 'Vergleichszeitraum:',
    'changes_previous' => 'Vorherig',
    'changes_current' => 'Aktuell',
    'changes_summary_changes' => 'Änderungen',
    'changes_summary_plugins' => 'Plugin-Änderungen',
    'changes_summary_log' => 'Neue Protokollmuster',
    'changes_summary_snapshots' => 'Momentaufnahmen',
    'changes_none' => 'Zwischen diesen beiden Momentaufnahmen wurde keine wesentliche Änderung erkannt.',
    'changes_detected' => 'Erkannte Änderungen',
    'changes_environment' => 'Umgebung',
    'changes_plugins' => 'Plugins',
    'changes_storage' => 'Speicher',
    'changes_logs' => 'Neue error.log-Aktivität',
    'changes_before' => 'Vorher',
    'changes_after' => 'Nachher',
    'changes_occurrences' => 'Vorkommen',
    'changes_log_none' => 'Für diesen Zeitraum wurde in error.log kein neues Fehler-, Warn- oder Ausnahmemuster erkannt.',
    'changes_log_rotated' => 'error.log wurde zwischen den beiden Momentaufnahmen rotiert oder gekürzt, daher kann der neue Teil nicht zuverlässig verglichen werden.',
    'changes_log_truncated' => 'Die neuen Protokolldaten überschritten das Analyselimit. Monitor analysierte nur die neuesten 512 KiB.',
    'changes_current_state' => 'Aktueller Zustand',
    'changes_geeklog' => 'Geeklog',
    'changes_php' => 'PHP',
    'changes_plugins_count' => 'Installierte Plugins',
    'changes_disk_free' => 'Freier Speicherplatz',
    'changes_error_log_size' => 'Größe von error.log',
    'changes_unknown' => 'Unbekannt',
    'changes_enabled' => 'aktiviert',
    'changes_disabled' => 'deaktiviert',
    'changes_code_geeklog_changed' => 'Geeklog-Version geändert',
    'changes_code_php_changed' => 'PHP-Version geändert',
    'changes_code_plugin_installed' => 'Plugin installiert',
    'changes_code_plugin_removed' => 'Plugin entfernt',
    'changes_code_plugin_version_changed' => 'Plugin-Version geändert',
    'changes_code_plugin_enabled' => 'Plugin aktiviert',
    'changes_code_plugin_disabled' => 'Plugin deaktiviert',
    'changes_code_disk_free_decreased' => 'Freier Speicherplatz verringert',

    // Configuration audit
    'config_audit_title' => 'Konfigurationsprüfung',
    'config_audit_page_title' => 'Monitor-Konfigurationsprüfung',
    'config_audit_quick_description' => 'Prüft Unterschiede zwischen siteconfig.php und den entsprechenden Core-Werten in der Datenbank.',
    'config_audit_back' => 'Monitor-Übersicht',
    'config_audit_access_denied' => 'Zugriff verweigert',
    'config_audit_root_only' => 'Zugriff ist Root-Administratoren vorbehalten.',
    'config_audit_intro_title' => 'Schreibgeschützte Konfigurationsprüfung.',
    'config_audit_intro' => 'Vergleicht explizit in siteconfig.php definierte Core-Werte mit den entsprechenden Werten in conf_values. Nur Unterschiede oder ungültige Werte erfordern Aufmerksamkeit.',
    'config_audit_issues' => 'Probleme',
    'config_audit_review' => 'Prüfen',
    'config_audit_normal' => 'Normal',
    'config_audit_active_host' => 'Host:',
    'config_audit_siteconfig' => 'siteconfig.php:',
    'config_audit_unreadable_title' => 'Warnung:',
    'config_audit_unreadable' => 'Monitor konnte die aktive siteconfig.php nicht lesen, daher ist der Vergleich unvollständig.',
    'config_audit_items_review' => 'Erfordert Aufmerksamkeit',
    'config_audit_no_issues' => 'Die Konfiguration ist konsistent. Kein widersprüchlicher Core-Wert erfordert Aufmerksamkeit.',
    'config_audit_secondary' => '%d normale oder informative Werte anzeigen',
    'config_audit_footer' => 'Sensible Werte werden geschwärzt. Optionales SQL zur Datenbankangleichung wird niemals automatisch ausgeführt.',
    'config_audit_source_siteconfig' => 'siteconfig.php',
    'config_audit_source_database' => 'Datenbank',
    'config_audit_priority' => 'Wirksame Quelle:',
    'config_audit_effective_value' => 'Wirksamer Wert',
    'config_audit_details' => 'Details',
    'config_audit_recommendation' => 'Empfehlung',
    'config_audit_path' => 'Pfad:',
    'config_audit_path_exists' => 'vorhanden',
    'config_audit_path_missing' => 'fehlt',
    'config_audit_optional_sql' => 'Optionale Datenbankangleichung',
    'config_audit_absent' => 'FEHLT',
    'config_audit_redacted' => '[GESCHWÄRZT]',
    'config_audit_value_true' => 'wahr',
    'config_audit_value_false' => 'falsch',
    'config_audit_value_null' => 'NULL',
    'config_audit_value_object' => '[OBJECT]',

    'config_audit_level_ok' => 'Erwartet',
    'config_audit_level_info' => 'Information',
    'config_audit_level_review' => 'Prüfen',
    'config_audit_level_warning' => 'Warnung',

    'config_audit_status_identical' => 'Identisch',
    'config_audit_status_core_file' => 'In siteconfig.php erwartet',
    'config_audit_status_file_only' => 'Nur siteconfig.php',
    'config_audit_status_db_unset' => 'Datenbankwert nicht gesetzt',
    'config_audit_status_different' => 'Unterschiedliche Werte',
    'config_audit_status_decode_error' => 'Datenbankwert nicht lesbar',
    'config_audit_status_invalid_path' => 'Ungültiger Pfad',

    'config_audit_why_identical' => 'Derselbe Wert ist in siteconfig.php und conf_values vorhanden.',
    'config_audit_why_core_file' => 'Dieser Core-Schlüssel wird normalerweise direkt in siteconfig.php definiert.',
    'config_audit_why_file_only' => 'In conf_values existiert kein entsprechender Core-Wert. Der Wert aus siteconfig.php wird verwendet.',
    'config_audit_why_db_unset' => 'In conf_values existiert eine passende Core-Zeile, ihr Wert ist jedoch nicht gesetzt. siteconfig.php bleibt wirksam.',
    'config_audit_why_different' => 'siteconfig.php und conf_values enthalten unterschiedliche Werte. Zur Laufzeit ist siteconfig.php wirksam.',
    'config_audit_why_decode_error' => 'Der entsprechende Core-Wert in conf_values konnte nicht zuverlässig dekodiert werden.',
    'config_audit_why_invalid_path' => 'Der wirksame Dateisystempfad existiert derzeit nicht.',

    'config_audit_action_none' => 'Keine Aktion erforderlich.',
    'config_audit_action_file_only' => 'Keine Aktion erforderlich, sofern dieser Wert nicht auch in der Datenbank verwaltet werden soll.',
    'config_audit_action_db_unset' => 'Prüfen Sie, ob der Datenbankwert absichtlich nicht gesetzt ist.',
    'config_audit_action_different' => 'Prüfen Sie, ob diese Überschreibung beabsichtigt ist. Gleichen Sie den Datenbankwert nur an, wenn der gespeicherte Wert veraltet ist.',
    'config_audit_action_decode_error' => 'Prüfen Sie die passende Core-Zeile in conf_values, bevor Sie Änderungen vornehmen.',
    'config_audit_action_invalid_path' => 'Prüfen Sie den konfigurierten Pfad und die Verfügbarkeit des Dateisystems.',

    // Plugin catalog
    'plugin_catalog_intro' => 'Installierte Plugins werden mit öffentlichen Repositories des konfigurierten GitHub-Eigentümers verglichen. Monitor meldet verfügbare Versionen und Entdeckungskandidaten, installiert oder aktualisiert jedoch niemals Code.',
    'plugin_catalog_owner' => 'GitHub-Quelle:',
    'plugin_catalog_refresh' => 'GitHub-Daten aktualisieren',
    'plugin_catalog_installed' => 'Installierte Plugins',
    'plugin_catalog_discover' => 'Plugins entdecken',
    'plugin_catalog_discover_compatible' => 'Mit dieser Website kompatibel',
    'plugin_catalog_discover_incompatible' => 'Nicht mit dieser Website kompatibel',
    'plugin_catalog_discover_unknown' => 'Kompatibilität unbekannt',
    'plugin_catalog_discover_requirements_missing' => 'Anforderungen nicht angegeben',
    'plugin_catalog_discover_metadata_unavailable' => 'Metadaten nicht verfügbar',
    'plugin_catalog_discover_intro' => 'Aktuelle öffentliche Repositories, die auf dieser Website nicht installiert sind. Prüfen Sie Kompatibilität und Dokumentation vor einer Installation.',
    'plugin_catalog_legacy_discover' => 'Ältere Repositories',
    'plugin_catalog_legacy_intro' => 'Ältere öffentliche Repositories können weiterhin nützlich sein, ihre Kompatibilität mit aktuellen Geeklog- und PHP-Versionen ist jedoch unbekannt.',
    'plugin_catalog_plugin' => 'Plugin',
    'plugin_catalog_core_plugin' => 'Core-Plugin',
    'plugin_catalog_core_update' => 'Mit Geeklog-Update verfügbar',
    'plugin_catalog_latest_core_version' => 'Neueste Core-Version',
    'plugin_catalog_current_geeklog' => 'Aktuelles Geeklog',
    'plugin_catalog_latest_geeklog_baseline' => 'Neueste Geeklog-Basis',
    'plugin_catalog_open_core_plugin' => 'Core-Plugin öffnen',
    'plugin_catalog_version' => 'Version',
    'plugin_catalog_bundled_with_geeklog' => 'Mit Geeklog gebündelt',
    'plugin_catalog_available_with_geeklog' => 'Mit Geeklog verfügbar',
    'plugin_catalog_installed_version' => 'Installiert',
    'plugin_catalog_code_version' => 'Code',
    'plugin_catalog_latest_release' => 'Neueste Veröffentlichung',
    'plugin_catalog_latest_version' => 'Neueste GitHub-Version',
    'plugin_catalog_version_source_release' => 'Veröffentlichung',
    'plugin_catalog_version_source_tag' => 'Tag',
    'plugin_catalog_state' => 'Status',
    'plugin_catalog_enabled' => 'Aktiviert',
    'plugin_catalog_geeklog' => 'Geeklog-Anforderung',
    'plugin_catalog_php_requirement' => 'PHP-Anforderung',
    'plugin_catalog_update_compatible' => 'Update kompatibel',
    'plugin_catalog_update_incompatible' => 'Update nicht kompatibel',
    'plugin_catalog_compatibility_unknown' => 'Kompatibilität unbekannt',
    'plugin_catalog_repository' => 'Repository',
    'plugin_catalog_yes' => 'Ja',
    'plugin_catalog_no' => 'Nein',
    'plugin_catalog_unknown' => 'Unbekannt',
    'plugin_catalog_no_release' => 'Keine Veröffentlichungsmetadaten',
    'plugin_catalog_no_version' => 'Keine Veröffentlichung oder Versionsmarke gefunden',
    'plugin_catalog_no_repository' => 'Kein passendes Repository',
    'plugin_catalog_catalog_unavailable' => 'GitHub-Katalog nicht verfügbar',
    'plugin_catalog_current_compatible' => 'Für dieses Geeklog aktuell',
    'plugin_catalog_latest_compatible_version' => 'Neueste kompatible Version',
    'plugin_catalog_newer_release' => 'Neuere Veröffentlichung',
    'plugin_catalog_requires_geeklog' => 'erfordert Geeklog %s',
    'plugin_catalog_requires_php' => 'erfordert PHP %s',
    'plugin_catalog_current' => 'Aktuell',
    'plugin_catalog_update' => 'Update verfügbar',
    'plugin_catalog_ahead' => 'Installierte Version neuer',
    'plugin_catalog_remote_unavailable' => 'GitHub-Metadaten sind nicht verfügbar. Lokale Plugin-Informationen werden weiterhin angezeigt.',
    'plugin_catalog_remote_disabled' => 'Remote-Metadatenprüfungen sind deaktiviert, da kein gültiger GitHub-Eigentümer konfiguriert ist.',
    'plugin_catalog_none_discoverable' => 'Für diesen GitHub-Eigentümer wurde kein weiteres aktuelles Plugin-Repository gefunden.',
    'plugin_catalog_open_repository' => 'Repository öffnen',
    'plugin_catalog_open_release' => 'Veröffentlichung öffnen',
    'plugin_catalog_open_version' => 'Version öffnen',
    'plugin_catalog_updated' => 'Aktualisiert',
    'plugin_catalog_summary_installed' => 'Installiert',
    'plugin_catalog_summary_updates' => 'Updates verfügbar',
    'plugin_catalog_summary_discover' => 'Aktuelle Kandidaten',
    'plugin_catalog_summary_unmatched' => 'Ohne GitHub-Treffer');

$PLG_monitor_MESSAGE3002 = $LANG32[9];
$PLG_monitor_MESSAGE3003 = 'Monitor konnte die Datenbankmigration nicht abschließen. Die installierte Version wurde nicht geändert; prüfen Sie error.log und versuchen Sie das Upgrade erneut.';

$GLOBALS['LANG_configsections']['monitor'] = array(
    'label' => 'Monitor',
    'title' => 'Monitor-Konfiguration'
);

$GLOBALS['LANG_configsubgroups']['monitor'] = array(
    'sg_main' => 'Haupteinstellungen'
);

$GLOBALS['LANG_tab']['monitor'] = array(
    'tab_main' => 'Hauptbereich'
);

$GLOBALS['LANG_fs']['monitor'] = array(
    'fs_main' => 'Allgemeine Einstellungen'
);

$GLOBALS['LANG_confignames']['monitor'] = array(
    'emails' => 'Liste von E-Mail-Adressen für optionale Monitor-Benachrichtigungen (durch Kommas getrennt)',
    'repository' => 'GitHub-Repository-Eigentümer für Plugin-Veröffentlichungsmetadaten (Standard: Geeklog-Plugins). Leer lassen, um Remote-Metadatenprüfungen zu deaktivieren.',
    'github_token' => 'Optionales GitHub-Token für API-Metadatenanfragen. Bevorzugen Sie ein fein abgestuftes Nur-Lese-Token. Die Umgebungsvariable MONITOR_GITHUB_TOKEN hat Vorrang.'
);

$LANG_configsections =& $GLOBALS['LANG_configsections'];
$LANG_configsubgroups =& $GLOBALS['LANG_configsubgroups'];
$LANG_tab =& $GLOBALS['LANG_tab'];
$LANG_fs =& $GLOBALS['LANG_fs'];
$LANG_confignames =& $GLOBALS['LANG_confignames'];
