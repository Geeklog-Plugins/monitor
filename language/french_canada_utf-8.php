<?php

// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.5.0                                                      |
// +---------------------------------------------------------------------------+
// | french_canada_utf-8.php                                                               |
// +---------------------------------------------------------------------------+

/**
 * @package Monitor
 */

global $LANG32;
global $LANG_configsections, $LANG_confignames, $LANG_configsubgroups, $LANG_tab, $LANG_fs;

$LANG_MONITOR_1 = array(
    'plugin_name'           => 'Monitor',
    'home'                  => 'Vue d’ensemble',
    'health'                => 'Santé',
    'security'              => 'Sécurité',
    'file'                  => 'Fichier :',
    'log_file'              => 'Fichier journal :',
    'view_logs'             => 'Voir les journaux actuels',
    'clear_logs'            => 'Effacer les journaux',
    'configuration'         => 'Configuration',
    'main'                  => 'Vue d’ensemble de l’état du site',
    'logs'                  => 'Fichiers journaux',
    'updates'               => 'Plugins',
    'status'                => 'État',
    'check'                 => 'Vérification',
    'value'                 => 'Valeur',
    'recommendation'        => 'Recommandation',
    'health_ok'             => 'OK',
    'health_info'           => 'Information',
    'health_warning'        => 'Avertissement',
    'health_error'          => 'Erreur',
    'security_observations' => 'Observations de sécurité',
    'ban_integration'       => 'Plugin Ban',
    'legacy_ban_notice'     => 'Monitor enregistre des observations de sécurité limitées. Le plugin Ban facultatif peut fournir des fonctions de blocage centralisées sans transformer Monitor en second moteur de bannissement.',
    'security_status' => 'État de la sécurité',
    'security_no_issue' => 'Aucun problème détecté par les contrôles de sécurité disponibles de Monitor.',
    'ban_not_installed' => 'Non installé',
    'ban_installed' => 'Installé',
    'ban_optional_intro' => 'Ban est facultatif. Monitor continue d’enregistrer ses observations de sécurité limitées sans lui.',
    'ban_optional_capability' => 'L’installation de Ban ajoute des fonctions de blocage centralisées que Monitor peut utiliser lorsqu’elles sont disponibles.',
    'ban_view_plugin' => 'Voir les informations du plugin Ban',
    'ban_version' => 'Version',
    'ban_ip_capability' => 'Capacité de demande de bannissement IP',
    'ban_capability_available' => 'Disponible',
    'ban_capability_unavailable' => 'Indisponible',
    'ban_direct_sql' => 'Couplage SQL direct avec Ban',
    'ban_direct_sql_no' => 'Non',
    'security_no_legacy_table' => 'Aucune ancienne table de sécurité Monitor n’est présente.',
    'security_no_observations' => 'Aucune observation de sécurité récente.',
    'read_only_advice'      => 'Par défaut, Monitor observe et recommande. Les modifications nécessitent une action explicite d’un administrateur.',
    'config_tooltip_emails' => 'Destinataires facultatifs des notifications Monitor, séparés par des virgules. Laissez vide pour désactiver les notifications par courriel.',
    'config_tooltip_repository' => 'Propriétaire GitHub dont Monitor vérifie les dépôts publics pour les métadonnées et versions des plugins. Laissez vide pour désactiver les vérifications distantes.',
    'config_tooltip_github_token' => 'Jeton GitHub facultatif, à granularité fine et en lecture seule. MONITOR_GITHUB_TOKEN est prioritaire. Les vérifications authentifiées disposent d’un quota API supérieur et utilisent un cache de tags plus court.',

    // Media diagnostics
    'media_oversized_single' => '1 image dépasse les limites recommandées.',
    'media_oversized_multiple' => '%d images dépassent les limites recommandées.',
    'media_show_files' => 'Afficher les fichiers (%d)',
    'media_hide_files' => 'Masquer les fichiers (%d)',
    'media_open_file_manager' => 'Ouvrir le gestionnaire de fichiers',
    'media_view_image' => 'Voir l’image',
    'media_more_files' => 'D’autres images surdimensionnées existent ; la liste est limitée.',
    'media_partial_scan' => 'L’analyse du système de fichiers a atteint sa limite de sécurité.',

    // Daily log archives
    'log_archive_title' => 'Archives des journaux',
    'log_archive_intro' => 'Monitor effectue une rotation quotidienne des fichiers .log de Geeklog et conserve les %d derniers jours d’archives.',
    'log_archive_safety' => 'Un journal actif n’est tronqué qu’après l’écriture réussie de sa copie d’archive. Les archives sont stockées sous path_data, hors du répertoire web public.',
    'log_archive_empty' => 'Aucune archive quotidienne n’est encore disponible. La première exécution planifiée crée la référence de rotation ; les archives apparaissent après le jour calendaire suivant.',
    'log_archive_date' => 'Date',
    'log_archive_log' => 'Journal',
    'log_archive_size' => 'Taille',
    'log_archive_actions' => 'Actions',
    'log_archive_view' => 'Voir',
    'log_archive_download' => 'Télécharger',
    'log_archive_back' => 'Retour aux archives des journaux',
    'log_archive_preview_limited' => 'Cet aperçu affiche uniquement les 512 Kio les plus récents de l’archive. Téléchargez le fichier pour récupérer le journal quotidien complet.',
    'log_email_title' => 'Résumé quotidien des journaux',
    'log_email_lines' => 'Lignes analysées',
    'log_email_issue_lines' => 'Lignes d’erreur/d’avertissement',
    'log_email_top_patterns' => 'Principaux motifs d’error.log',
    'log_email_no_activity' => 'Aucun journal Geeklog non vide n’a été archivé pour cette journée.',

    // Changes monitor
    'changes' => 'Modifications',
    'changes_page_title' => 'Modifications Monitor',
    'changes_intro' => 'Comparez des instantanés légers du site pour voir ce qui a changé entre deux contrôles. Monitor enregistre uniquement l’état ; il ne modifie pas le site.',
    'changes_capture' => 'Capturer l’état actuel',
    'changes_capture_ok' => 'État actuel enregistré.',
    'changes_capture_failed' => 'Monitor n’a pas pu enregistrer l’instantané. Vérifiez que path_data est accessible en écriture.',
    'changes_baseline_created' => 'Référence créée. Capturez un autre état plus tard pour voir ce qui a changé.',
    'changes_waiting' => 'Un deuxième instantané est nécessaire avant de pouvoir comparer les modifications.',
    'changes_period' => 'Période comparée :',
    'changes_previous' => 'Précédent',
    'changes_current' => 'Actuel',
    'changes_summary_changes' => 'Modifications',
    'changes_summary_plugins' => 'Modifications des plugins',
    'changes_summary_log' => 'Nouveaux motifs de journal',
    'changes_summary_snapshots' => 'Instantanés',
    'changes_none' => 'Aucune modification significative n’a été détectée entre ces deux instantanés.',
    'changes_detected' => 'Modifications détectées',
    'changes_environment' => 'Environnement',
    'changes_plugins' => 'Plugins',
    'changes_storage' => 'Stockage',
    'changes_logs' => 'Nouvelle activité dans error.log',
    'changes_before' => 'Avant',
    'changes_after' => 'Après',
    'changes_occurrences' => 'occurrence(s)',
    'changes_log_none' => 'Aucun nouveau motif d’erreur, d’avertissement ou d’exception n’a été détecté dans error.log pour cette période.',
    'changes_log_rotated' => 'error.log a été soumis à une rotation ou tronqué entre les deux instantanés ; la nouvelle partie ne peut donc pas être comparée de manière fiable.',
    'changes_log_truncated' => 'Les nouvelles données du journal ont dépassé la limite d’analyse. Monitor n’a analysé que les 512 Kio les plus récents.',
    'changes_current_state' => 'État actuel',
    'changes_geeklog' => 'Geeklog',
    'changes_php' => 'PHP',
    'changes_plugins_count' => 'Plugins installés',
    'changes_disk_free' => 'Espace disque libre',
    'changes_error_log_size' => 'Taille d’error.log',
    'changes_unknown' => 'Inconnu',
    'changes_enabled' => 'activé',
    'changes_disabled' => 'désactivé',
    'changes_code_geeklog_changed' => 'Version de Geeklog modifiée',
    'changes_code_php_changed' => 'Version de PHP modifiée',
    'changes_code_plugin_installed' => 'Plugin installé',
    'changes_code_plugin_removed' => 'Plugin supprimé',
    'changes_code_plugin_version_changed' => 'Version du plugin modifiée',
    'changes_code_plugin_enabled' => 'Plugin activé',
    'changes_code_plugin_disabled' => 'Plugin désactivé',
    'changes_code_disk_free_decreased' => 'Espace disque libre réduit',

    // Configuration audit
    'config_audit_title' => 'Audit de configuration',
    'config_audit_page_title' => 'Audit de configuration Monitor',
    'config_audit_quick_description' => 'Vérifier les différences entre siteconfig.php et les valeurs Core correspondantes dans la base de données.',
    'config_audit_back' => 'Vue d’ensemble de Monitor',
    'config_audit_access_denied' => 'Accès refusé',
    'config_audit_root_only' => 'Accès réservé aux administrateurs Root.',
    'config_audit_intro_title' => 'Audit de configuration en lecture seule.',
    'config_audit_intro' => 'Compare les valeurs Core explicitement définies dans siteconfig.php aux valeurs correspondantes de conf_values. Seules les différences ou valeurs invalides nécessitent une attention.',
    'config_audit_issues' => 'Problèmes',
    'config_audit_review' => 'À vérifier',
    'config_audit_normal' => 'Normal',
    'config_audit_active_host' => 'Hôte :',
    'config_audit_siteconfig' => 'siteconfig.php :',
    'config_audit_unreadable_title' => 'Avertissement :',
    'config_audit_unreadable' => 'Monitor n’a pas pu lire le siteconfig.php actif ; la comparaison est donc incomplète.',
    'config_audit_items_review' => 'Nécessite une attention',
    'config_audit_no_issues' => 'La configuration est cohérente. Aucune valeur Core conflictuelle ne nécessite d’attention.',
    'config_audit_secondary' => 'Afficher %d valeur(s) normale(s) ou informative(s)',
    'config_audit_footer' => 'Les valeurs sensibles sont masquées. Le SQL facultatif d’alignement de la base n’est jamais exécuté automatiquement.',
    'config_audit_source_siteconfig' => 'siteconfig.php',
    'config_audit_source_database' => 'Base de données',
    'config_audit_priority' => 'Source effective :',
    'config_audit_effective_value' => 'Valeur effective',
    'config_audit_details' => 'Détails',
    'config_audit_recommendation' => 'Recommandation',
    'config_audit_path' => 'Chemin :',
    'config_audit_path_exists' => 'existe',
    'config_audit_path_missing' => 'absent',
    'config_audit_optional_sql' => 'Alignement facultatif de la base de données',
    'config_audit_absent' => 'ABSENT',
    'config_audit_redacted' => '[MASQUÉ]',
    'config_audit_value_true' => 'vrai',
    'config_audit_value_false' => 'faux',
    'config_audit_value_null' => 'NULL',
    'config_audit_value_object' => '[OBJECT]',

    'config_audit_level_ok' => 'Attendu',
    'config_audit_level_info' => 'Information',
    'config_audit_level_review' => 'À vérifier',
    'config_audit_level_warning' => 'Avertissement',

    'config_audit_status_identical' => 'Identique',
    'config_audit_status_core_file' => 'Attendu dans siteconfig.php',
    'config_audit_status_file_only' => 'siteconfig.php uniquement',
    'config_audit_status_db_unset' => 'Valeur de base de données non définie',
    'config_audit_status_different' => 'Valeurs différentes',
    'config_audit_status_decode_error' => 'Valeur de base de données illisible',
    'config_audit_status_invalid_path' => 'Chemin invalide',

    'config_audit_why_identical' => 'La même valeur existe dans siteconfig.php et conf_values.',
    'config_audit_why_core_file' => 'Cette clé Core est normalement définie directement dans siteconfig.php.',
    'config_audit_why_file_only' => 'Aucune valeur Core correspondante n’existe dans conf_values. La valeur de siteconfig.php est utilisée.',
    'config_audit_why_db_unset' => 'Une ligne Core correspondante existe dans conf_values mais sa valeur n’est pas définie. siteconfig.php reste effectif.',
    'config_audit_why_different' => 'siteconfig.php et conf_values contiennent des valeurs différentes. siteconfig.php est effectif à l’exécution.',
    'config_audit_why_decode_error' => 'La valeur Core correspondante dans conf_values n’a pas pu être décodée de manière fiable.',
    'config_audit_why_invalid_path' => 'Le chemin effectif du système de fichiers n’existe pas actuellement.',

    'config_audit_action_none' => 'Aucune action requise.',
    'config_audit_action_file_only' => 'Aucune action requise sauf si cette valeur doit également être gérée dans la base de données.',
    'config_audit_action_db_unset' => 'Vérifiez que la valeur de base de données est volontairement non définie.',
    'config_audit_action_different' => 'Vérifiez que cette surcharge est intentionnelle. N’alignez la valeur de base de données que si la valeur enregistrée est obsolète.',
    'config_audit_action_decode_error' => 'Inspectez la ligne Core correspondante dans conf_values avant toute modification.',
    'config_audit_action_invalid_path' => 'Vérifiez le chemin configuré et la disponibilité du système de fichiers.',

    // Plugin catalog
    'plugin_catalog_intro' => 'Les plugins installés sont comparés aux dépôts publics du propriétaire GitHub configuré. Monitor indique les versions disponibles et les candidats découverts, mais n’installe ni ne met jamais à jour le code.',
    'plugin_catalog_owner' => 'Source GitHub :',
    'plugin_catalog_refresh' => 'Actualiser les données GitHub',
    'plugin_catalog_installed' => 'Plugins installés',
    'plugin_catalog_discover' => 'Découvrir des plugins',
    'plugin_catalog_discover_compatible' => 'Compatible avec ce site',
    'plugin_catalog_discover_incompatible' => 'Non compatible avec ce site',
    'plugin_catalog_discover_unknown' => 'Compatibilité inconnue',
    'plugin_catalog_discover_requirements_missing' => 'Prérequis non déclarés',
    'plugin_catalog_discover_metadata_unavailable' => 'Métadonnées indisponibles',
    'plugin_catalog_discover_intro' => 'Dépôts publics récents non installés sur ce site. Vérifiez la compatibilité et la documentation avant toute installation.',
    'plugin_catalog_legacy_discover' => 'Dépôts plus anciens',
    'plugin_catalog_legacy_intro' => 'Les anciens dépôts publics peuvent encore être utiles, mais leur compatibilité avec les versions récentes de Geeklog et PHP est inconnue.',
    'plugin_catalog_plugin' => 'Plugin',
    'plugin_catalog_core_plugin' => 'Plugin Core',
    'plugin_catalog_core_update' => 'Disponible avec la mise à jour de Geeklog',
    'plugin_catalog_latest_core_version' => 'Dernière version Core',
    'plugin_catalog_current_geeklog' => 'Geeklog actuel',
    'plugin_catalog_latest_geeklog_baseline' => 'Dernière référence Geeklog',
    'plugin_catalog_open_core_plugin' => 'Ouvrir le plugin Core',
    'plugin_catalog_version' => 'Version',
    'plugin_catalog_bundled_with_geeklog' => 'Fourni avec Geeklog',
    'plugin_catalog_available_with_geeklog' => 'Disponible avec Geeklog',
    'plugin_catalog_installed_version' => 'Installé',
    'plugin_catalog_code_version' => 'Code',
    'plugin_catalog_latest_release' => 'Dernière version publiée',
    'plugin_catalog_latest_version' => 'Dernière version GitHub',
    'plugin_catalog_version_source_release' => 'Version publiée',
    'plugin_catalog_version_source_tag' => 'Tag',
    'plugin_catalog_state' => 'État',
    'plugin_catalog_enabled' => 'Activé',
    'plugin_catalog_geeklog' => 'Prérequis Geeklog',
    'plugin_catalog_php_requirement' => 'Prérequis PHP',
    'plugin_catalog_update_compatible' => 'Mise à jour compatible',
    'plugin_catalog_update_incompatible' => 'Mise à jour non compatible',
    'plugin_catalog_compatibility_unknown' => 'Compatibilité inconnue',
    'plugin_catalog_repository' => 'Dépôt',
    'plugin_catalog_yes' => 'Oui',
    'plugin_catalog_no' => 'Non',
    'plugin_catalog_unknown' => 'Inconnu',
    'plugin_catalog_no_release' => 'Aucune métadonnée de version',
    'plugin_catalog_no_version' => 'Aucune version publiée ni balise de version trouvée',
    'plugin_catalog_no_repository' => 'Aucun dépôt correspondant',
    'plugin_catalog_catalog_unavailable' => 'Catalogue GitHub indisponible',
    'plugin_catalog_current_compatible' => 'À jour pour ce Geeklog',
    'plugin_catalog_latest_compatible_version' => 'Dernière version compatible',
    'plugin_catalog_newer_release' => 'Version plus récente',
    'plugin_catalog_requires_geeklog' => 'nécessite Geeklog %s',
    'plugin_catalog_requires_php' => 'nécessite PHP %s',
    'plugin_catalog_current' => 'À jour',
    'plugin_catalog_update' => 'Mise à jour disponible',
    'plugin_catalog_ahead' => 'Version installée plus récente',
    'plugin_catalog_remote_unavailable' => 'Les métadonnées GitHub sont indisponibles. Les informations locales du plugin restent affichées.',
    'plugin_catalog_remote_disabled' => 'Les vérifications de métadonnées distantes sont désactivées car aucun propriétaire GitHub valide n’est configuré.',
    'plugin_catalog_none_discoverable' => 'Aucun autre dépôt de plugin récent n’a été trouvé pour ce propriétaire GitHub.',
    'plugin_catalog_open_repository' => 'Ouvrir le dépôt',
    'plugin_catalog_open_release' => 'Ouvrir la version publiée',
    'plugin_catalog_open_version' => 'Ouvrir la version',
    'plugin_catalog_updated' => 'Mis à jour',
    'plugin_catalog_summary_installed' => 'Installé',
    'plugin_catalog_summary_updates' => 'Mises à jour disponibles',
    'plugin_catalog_summary_discover' => 'Candidats récents',
    'plugin_catalog_summary_unmatched' => 'Sans correspondance GitHub');

$PLG_monitor_MESSAGE3002 = $LANG32[9];
$PLG_monitor_MESSAGE3003 = 'Monitor n’a pas pu terminer la migration de la base de données. La version installée n’a pas été modifiée ; consultez error.log et relancez la mise à niveau.';

$GLOBALS['LANG_configsections']['monitor'] = array(
    'label' => 'Monitor',
    'title' => 'Configuration de Monitor'
);

$GLOBALS['LANG_configsubgroups']['monitor'] = array(
    'sg_main' => 'Paramètres principaux'
);

$GLOBALS['LANG_tab']['monitor'] = array(
    'tab_main' => 'Principal'
);

$GLOBALS['LANG_fs']['monitor'] = array(
    'fs_main' => 'Paramètres généraux'
);

$GLOBALS['LANG_confignames']['monitor'] = array(
    'emails' => 'Liste des adresses courriel pour les notifications facultatives de Monitor (séparées par des virgules)',
    'repository' => 'Propriétaire du dépôt GitHub réservé aux métadonnées de versions des plugins (par défaut : Geeklog-Plugins). Laissez vide pour désactiver les vérifications de métadonnées distantes.',
    'github_token' => 'Jeton GitHub facultatif pour les requêtes de métadonnées API. Préférez un jeton à granularité fine en lecture seule. La variable d’environnement MONITOR_GITHUB_TOKEN est prioritaire.'
);

$LANG_configsections =& $GLOBALS['LANG_configsections'];
$LANG_configsubgroups =& $GLOBALS['LANG_configsubgroups'];
$LANG_tab =& $GLOBALS['LANG_tab'];
$LANG_fs =& $GLOBALS['LANG_fs'];
$LANG_confignames =& $GLOBALS['LANG_confignames'];
