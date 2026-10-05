<?php

// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.5.0                                                      |
// +---------------------------------------------------------------------------+
// | italian_utf-8.php                                                               |
// +---------------------------------------------------------------------------+

/**
 * @package Monitor
 */

global $LANG32;
global $LANG_configsections, $LANG_confignames, $LANG_configsubgroups, $LANG_tab, $LANG_fs;

$LANG_MONITOR_1 = array(
    'plugin_name'           => 'Monitor',
    'home'                  => 'Panoramica',
    'health'                => 'Stato',
    'security'              => 'Sicurezza',
    'file'                  => 'File:',
    'log_file'              => 'File di log:',
    'view_logs'             => 'Visualizza log correnti',
    'clear_logs'            => 'Cancella log',
    'configuration'         => 'Configurazione',
    'main'                  => 'Panoramica dello stato del sito',
    'logs'                  => 'File di log',
    'updates'               => 'Plugin',
    'status'                => 'Stato',
    'check'                 => 'Controllo',
    'value'                 => 'Valore',
    'recommendation'        => 'Raccomandazione',
    'health_ok'             => 'OK',
    'health_info'           => 'Informazioni',
    'health_warning'        => 'Avviso',
    'health_error'          => 'Errore',
    'security_observations' => 'Osservazioni di sicurezza',
    'ban_integration'       => 'Plugin Ban',
    'legacy_ban_notice'     => 'Monitor registra osservazioni di sicurezza limitate. Il plugin opzionale Ban può fornire funzioni di blocco centralizzate senza trasformare Monitor in un secondo motore di ban.',
    'security_status' => 'Stato della sicurezza',
    'security_no_issue' => 'Nessun problema rilevato dai controlli di sicurezza disponibili di Monitor.',
    'ban_not_installed' => 'Non installato',
    'ban_installed' => 'Installato',
    'ban_optional_intro' => 'Ban è opzionale. Monitor continua a registrare le proprie osservazioni di sicurezza limitate anche senza di esso.',
    'ban_optional_capability' => 'L\'installazione di Ban aggiunge funzioni di blocco centralizzate che Monitor può usare quando disponibili.',
    'ban_view_plugin' => 'Visualizza informazioni sul plugin Ban',
    'ban_version' => 'Versione',
    'ban_ip_capability' => 'Capacità di richiesta di blocco IP',
    'ban_capability_available' => 'Disponibile',
    'ban_capability_unavailable' => 'Non disponibile',
    'ban_direct_sql' => 'Accoppiamento SQL diretto con Ban',
    'ban_direct_sql_no' => 'No',
    'security_no_legacy_table' => 'Non è presente alcuna tabella di sicurezza legacy di Monitor.',
    'security_no_observations' => 'Nessuna osservazione di sicurezza recente.',
    'read_only_advice'      => 'Per impostazione predefinita Monitor osserva e consiglia. Le modifiche richiedono un\'azione esplicita dell\'amministratore.',
    'config_tooltip_emails' => 'Destinatari opzionali delle notifiche Monitor, separati da virgole. Lascia vuoto per disattivare le notifiche e-mail.',
    'config_tooltip_repository' => 'Proprietario GitHub i cui repository pubblici vengono controllati da Monitor per metadati e versioni dei plugin. Lascia vuoto per disattivare i controlli remoti.',
    'config_tooltip_github_token' => 'Token GitHub opzionale, granulare e di sola lettura. MONITOR_GITHUB_TOKEN ha la precedenza. I controlli autenticati dispongono di una quota API maggiore e usano una cache dei tag più breve.',

    // Media diagnostics
    'media_oversized_single' => '1 immagine supera i limiti consigliati.',
    'media_oversized_multiple' => '%d immagini superano i limiti consigliati.',
    'media_show_files' => 'Mostra file (%d)',
    'media_hide_files' => 'Nascondi file (%d)',
    'media_open_file_manager' => 'Apri Gestione file',
    'media_view_image' => 'Visualizza immagine',
    'media_more_files' => 'Esistono altre immagini sovradimensionate; l\'elenco è limitato.',
    'media_partial_scan' => 'La scansione del file system ha raggiunto il limite di sicurezza.',

    // Daily log archives
    'log_archive_title' => 'Archivi dei log',
    'log_archive_intro' => 'Monitor ruota quotidianamente i file .log di Geeklog e conserva gli archivi degli ultimi %d giorni.',
    'log_archive_safety' => 'Un log attivo viene troncato solo dopo che la copia d\'archivio è stata scritta correttamente. Gli archivi sono memorizzati sotto path_data, fuori dalla directory web pubblica.',
    'log_archive_empty' => 'Non è ancora disponibile alcun archivio giornaliero. La prima esecuzione pianificata crea la base di rotazione; gli archivi compaiono dopo il giorno di calendario successivo.',
    'log_archive_date' => 'Data',
    'log_archive_log' => 'Log',
    'log_archive_size' => 'Dimensione',
    'log_archive_actions' => 'Azioni',
    'log_archive_view' => 'Visualizza',
    'log_archive_download' => 'Scarica',
    'log_archive_back' => 'Torna agli archivi dei log',
    'log_archive_preview_limited' => 'Questa anteprima mostra solo i 512 KiB più recenti dell\'archivio. Scarica il file per recuperare il log giornaliero completo.',
    'log_email_title' => 'Riepilogo giornaliero dei log',
    'log_email_lines' => 'Righe analizzate',
    'log_email_issue_lines' => 'Righe di errore/avviso',
    'log_email_top_patterns' => 'Principali pattern di error.log',
    'log_email_no_activity' => 'Nessun log Geeklog non vuoto è stato archiviato per questo giorno.',

    // Changes monitor
    'changes' => 'Modifiche',
    'changes_page_title' => 'Modifiche di Monitor',
    'changes_intro' => 'Confronta snapshot leggeri del sito per vedere cosa è cambiato tra due controlli. Monitor registra solo lo stato; non modifica il sito.',
    'changes_capture' => 'Acquisisci stato corrente',
    'changes_capture_ok' => 'Stato corrente salvato.',
    'changes_capture_failed' => 'Monitor non ha potuto salvare lo snapshot. Verifica che path_data sia scrivibile.',
    'changes_baseline_created' => 'Base creata. Acquisisci un altro stato più avanti per vedere cosa è cambiato.',
    'changes_waiting' => 'È necessario un secondo snapshot prima di poter confrontare le modifiche.',
    'changes_period' => 'Periodo confrontato:',
    'changes_previous' => 'Precedente',
    'changes_current' => 'Corrente',
    'changes_summary_changes' => 'Modifiche',
    'changes_summary_plugins' => 'Modifiche dei plugin',
    'changes_summary_log' => 'Nuovi pattern di log',
    'changes_summary_snapshots' => 'Snapshot',
    'changes_none' => 'Nessuna modifica significativa rilevata tra questi due snapshot.',
    'changes_detected' => 'Modifiche rilevate',
    'changes_environment' => 'Ambiente',
    'changes_plugins' => 'Plugin',
    'changes_storage' => 'Archiviazione',
    'changes_logs' => 'Nuova attività in error.log',
    'changes_before' => 'Prima',
    'changes_after' => 'Dopo',
    'changes_occurrences' => 'occorrenza/e',
    'changes_log_none' => 'Nessun nuovo pattern di errore, avviso o eccezione è stato rilevato in error.log per questo periodo.',
    'changes_log_rotated' => 'error.log è stato ruotato o troncato tra i due snapshot, quindi la nuova parte non può essere confrontata in modo affidabile.',
    'changes_log_truncated' => 'I nuovi dati del log hanno superato il limite di analisi. Monitor ha analizzato solo i 512 KiB più recenti.',
    'changes_current_state' => 'Stato corrente',
    'changes_geeklog' => 'Geeklog',
    'changes_php' => 'PHP',
    'changes_plugins_count' => 'Plugin installati',
    'changes_disk_free' => 'Spazio libero su disco',
    'changes_error_log_size' => 'Dimensione di error.log',
    'changes_unknown' => 'Sconosciuto',
    'changes_enabled' => 'attivato',
    'changes_disabled' => 'disattivato',
    'changes_code_geeklog_changed' => 'Versione di Geeklog modificata',
    'changes_code_php_changed' => 'Versione di PHP modificata',
    'changes_code_plugin_installed' => 'Plugin installato',
    'changes_code_plugin_removed' => 'Plugin rimosso',
    'changes_code_plugin_version_changed' => 'Versione del plugin modificata',
    'changes_code_plugin_enabled' => 'Plugin attivato',
    'changes_code_plugin_disabled' => 'Plugin disattivato',
    'changes_code_disk_free_decreased' => 'Spazio libero su disco diminuito',

    // Configuration audit
    'config_audit_title' => 'Audit della configurazione',
    'config_audit_page_title' => 'Audit della configurazione di Monitor',
    'config_audit_quick_description' => 'Controlla le differenze tra siteconfig.php e i valori Core corrispondenti nel database.',
    'config_audit_back' => 'Panoramica di Monitor',
    'config_audit_access_denied' => 'Accesso negato',
    'config_audit_root_only' => 'Accesso riservato agli amministratori Root.',
    'config_audit_intro_title' => 'Audit della configurazione in sola lettura.',
    'config_audit_intro' => 'Confronta i valori Core definiti esplicitamente in siteconfig.php con i valori corrispondenti in conf_values. Solo differenze o valori non validi richiedono attenzione.',
    'config_audit_issues' => 'Problemi',
    'config_audit_review' => 'Da verificare',
    'config_audit_normal' => 'Normale',
    'config_audit_active_host' => 'Host:',
    'config_audit_siteconfig' => 'siteconfig.php:',
    'config_audit_unreadable_title' => 'Avviso:',
    'config_audit_unreadable' => 'Monitor non ha potuto leggere il siteconfig.php attivo, quindi il confronto è incompleto.',
    'config_audit_items_review' => 'Richiede attenzione',
    'config_audit_no_issues' => 'La configurazione è coerente. Nessun valore Core in conflitto richiede attenzione.',
    'config_audit_secondary' => 'Mostra %d valore/i normale/i o informativo/i',
    'config_audit_footer' => 'I valori sensibili sono oscurati. L\'SQL opzionale di allineamento del database non viene mai eseguito automaticamente.',
    'config_audit_source_siteconfig' => 'siteconfig.php',
    'config_audit_source_database' => 'Database',
    'config_audit_priority' => 'Origine effettiva:',
    'config_audit_effective_value' => 'Valore effettivo',
    'config_audit_details' => 'Dettagli',
    'config_audit_recommendation' => 'Raccomandazione',
    'config_audit_path' => 'Percorso:',
    'config_audit_path_exists' => 'esiste',
    'config_audit_path_missing' => 'manca',
    'config_audit_optional_sql' => 'Allineamento opzionale del database',
    'config_audit_absent' => 'ASSENTE',
    'config_audit_redacted' => '[OSCURATO]',
    'config_audit_value_true' => 'vero',
    'config_audit_value_false' => 'falso',
    'config_audit_value_null' => 'NULL',
    'config_audit_value_object' => '[OBJECT]',

    'config_audit_level_ok' => 'Previsto',
    'config_audit_level_info' => 'Informazioni',
    'config_audit_level_review' => 'Da verificare',
    'config_audit_level_warning' => 'Avviso',

    'config_audit_status_identical' => 'Identico',
    'config_audit_status_core_file' => 'Previsto in siteconfig.php',
    'config_audit_status_file_only' => 'Solo siteconfig.php',
    'config_audit_status_db_unset' => 'Valore del database non impostato',
    'config_audit_status_different' => 'Valori diversi',
    'config_audit_status_decode_error' => 'Valore del database illeggibile',
    'config_audit_status_invalid_path' => 'Percorso non valido',

    'config_audit_why_identical' => 'Lo stesso valore esiste in siteconfig.php e conf_values.',
    'config_audit_why_core_file' => 'Questa chiave Core è normalmente definita direttamente in siteconfig.php.',
    'config_audit_why_file_only' => 'Nessun valore Core corrispondente esiste in conf_values. Viene usato il valore di siteconfig.php.',
    'config_audit_why_db_unset' => 'Esiste una riga Core corrispondente in conf_values, ma il suo valore non è impostato. siteconfig.php rimane effettivo.',
    'config_audit_why_different' => 'siteconfig.php e conf_values contengono valori diversi. siteconfig.php è effettivo durante l\'esecuzione.',
    'config_audit_why_decode_error' => 'Il valore Core corrispondente in conf_values non può essere decodificato in modo affidabile.',
    'config_audit_why_invalid_path' => 'Il percorso effettivo del file system attualmente non esiste.',

    'config_audit_action_none' => 'Nessuna azione richiesta.',
    'config_audit_action_file_only' => 'Nessuna azione richiesta, a meno che questo valore debba essere gestito anche nel database.',
    'config_audit_action_db_unset' => 'Verifica che il valore del database sia intenzionalmente non impostato.',
    'config_audit_action_different' => 'Verifica che questa sovrascrittura sia intenzionale. Allinea il valore del database solo se quello memorizzato è obsoleto.',
    'config_audit_action_decode_error' => 'Controlla la riga Core corrispondente in conf_values prima di apportare modifiche.',
    'config_audit_action_invalid_path' => 'Controlla il percorso configurato e la disponibilità del file system.',

    // Plugin catalog
    'plugin_catalog_intro' => 'I plugin installati vengono confrontati con i repository pubblici del proprietario GitHub configurato. Monitor segnala le versioni disponibili e i candidati scoperti, ma non installa né aggiorna mai il codice.',
    'plugin_catalog_owner' => 'Origine GitHub:',
    'plugin_catalog_refresh' => 'Aggiorna dati GitHub',
    'plugin_catalog_installed' => 'Plugin installati',
    'plugin_catalog_discover' => 'Scopri plugin',
    'plugin_catalog_discover_compatible' => 'Compatibile con questo sito',
    'plugin_catalog_discover_incompatible' => 'Non compatibile con questo sito',
    'plugin_catalog_discover_unknown' => 'Compatibilità sconosciuta',
    'plugin_catalog_discover_requirements_missing' => 'Requisiti non dichiarati',
    'plugin_catalog_discover_metadata_unavailable' => 'Metadati non disponibili',
    'plugin_catalog_discover_intro' => 'Repository pubblici recenti non installati su questo sito. Verifica compatibilità e documentazione prima di installare qualsiasi cosa.',
    'plugin_catalog_legacy_discover' => 'Repository meno recenti',
    'plugin_catalog_legacy_intro' => 'I repository pubblici meno recenti possono essere ancora utili, ma la loro compatibilità con versioni recenti di Geeklog e PHP è sconosciuta.',
    'plugin_catalog_plugin' => 'Plugin',
    'plugin_catalog_core_plugin' => 'Plugin Core',
    'plugin_catalog_core_update' => 'Disponibile con l\'aggiornamento di Geeklog',
    'plugin_catalog_latest_core_version' => 'Ultima versione Core',
    'plugin_catalog_current_geeklog' => 'Geeklog corrente',
    'plugin_catalog_latest_geeklog_baseline' => 'Ultima base Geeklog',
    'plugin_catalog_open_core_plugin' => 'Apri plugin Core',
    'plugin_catalog_version' => 'Versione',
    'plugin_catalog_bundled_with_geeklog' => 'Incluso con Geeklog',
    'plugin_catalog_available_with_geeklog' => 'Disponibile con Geeklog',
    'plugin_catalog_installed_version' => 'Installato',
    'plugin_catalog_code_version' => 'Codice',
    'plugin_catalog_latest_release' => 'Ultima release',
    'plugin_catalog_latest_version' => 'Ultima versione GitHub',
    'plugin_catalog_version_source_release' => 'Release',
    'plugin_catalog_version_source_tag' => 'Tag',
    'plugin_catalog_state' => 'Stato',
    'plugin_catalog_enabled' => 'Attivato',
    'plugin_catalog_geeklog' => 'Requisito Geeklog',
    'plugin_catalog_php_requirement' => 'Requisito PHP',
    'plugin_catalog_update_compatible' => 'Aggiornamento compatibile',
    'plugin_catalog_update_incompatible' => 'Aggiornamento non compatibile',
    'plugin_catalog_compatibility_unknown' => 'Compatibilità sconosciuta',
    'plugin_catalog_repository' => 'Repository',
    'plugin_catalog_yes' => 'Sì',
    'plugin_catalog_no' => 'No',
    'plugin_catalog_unknown' => 'Sconosciuto',
    'plugin_catalog_no_release' => 'Nessun metadato di release',
    'plugin_catalog_no_version' => 'Nessuna release o tag di versione trovato',
    'plugin_catalog_no_repository' => 'Nessun repository corrispondente',
    'plugin_catalog_catalog_unavailable' => 'Catalogo GitHub non disponibile',
    'plugin_catalog_current_compatible' => 'Aggiornato per questo Geeklog',
    'plugin_catalog_latest_compatible_version' => 'Ultima versione compatibile',
    'plugin_catalog_newer_release' => 'Release più recente',
    'plugin_catalog_requires_geeklog' => 'richiede Geeklog %s',
    'plugin_catalog_requires_php' => 'richiede PHP %s',
    'plugin_catalog_current' => 'Aggiornato',
    'plugin_catalog_update' => 'Aggiornamento disponibile',
    'plugin_catalog_ahead' => 'Versione installata più recente',
    'plugin_catalog_remote_unavailable' => 'I metadati GitHub non sono disponibili. Le informazioni locali del plugin vengono comunque mostrate.',
    'plugin_catalog_remote_disabled' => 'I controlli remoti dei metadati sono disattivati perché non è configurato alcun proprietario GitHub valido.',
    'plugin_catalog_none_discoverable' => 'Nessun altro repository recente di plugin è stato trovato per questo proprietario GitHub.',
    'plugin_catalog_open_repository' => 'Apri repository',
    'plugin_catalog_open_release' => 'Apri release',
    'plugin_catalog_open_version' => 'Apri versione',
    'plugin_catalog_updated' => 'Aggiornato',
    'plugin_catalog_summary_installed' => 'Installato',
    'plugin_catalog_summary_updates' => 'Aggiornamenti disponibili',
    'plugin_catalog_summary_discover' => 'Candidati recenti',
    'plugin_catalog_summary_unmatched' => 'Senza corrispondenza GitHub');

$PLG_monitor_MESSAGE3002 = $LANG32[9];
$PLG_monitor_MESSAGE3003 = 'Monitor non ha potuto completare la migrazione del database. La versione installata non è stata modificata; controlla error.log e riprova l\'aggiornamento.';

$GLOBALS['LANG_configsections']['monitor'] = array(
    'label' => 'Monitor',
    'title' => 'Configurazione di Monitor'
);

$GLOBALS['LANG_configsubgroups']['monitor'] = array(
    'sg_main' => 'Impostazioni principali'
);

$GLOBALS['LANG_tab']['monitor'] = array(
    'tab_main' => 'Principale'
);

$GLOBALS['LANG_fs']['monitor'] = array(
    'fs_main' => 'Impostazioni generali'
);

$GLOBALS['LANG_confignames']['monitor'] = array(
    'emails' => 'Elenco di indirizzi email per le notifiche opzionali di Monitor (separati da virgole)',
    'repository' => 'Proprietario del repository GitHub riservato ai metadati delle release dei plugin (predefinito: Geeklog-Plugins). Lascia vuoto per disattivare i controlli remoti dei metadati.',
    'github_token' => 'Token GitHub opzionale per richieste di metadati API. Preferisci un token granulare di sola lettura. La variabile d\'ambiente MONITOR_GITHUB_TOKEN ha la priorità.'
);

$LANG_configsections =& $GLOBALS['LANG_configsections'];
$LANG_configsubgroups =& $GLOBALS['LANG_configsubgroups'];
$LANG_tab =& $GLOBALS['LANG_tab'];
$LANG_fs =& $GLOBALS['LANG_fs'];
$LANG_confignames =& $GLOBALS['LANG_confignames'];
