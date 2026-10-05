<?php

// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.5.0                                                      |
// +---------------------------------------------------------------------------+
// | spanish_argentina_utf-8.php                                                               |
// +---------------------------------------------------------------------------+

/**
 * @package Monitor
 */

global $LANG32;
global $LANG_configsections, $LANG_confignames, $LANG_configsubgroups, $LANG_tab, $LANG_fs;

$LANG_MONITOR_1 = array(
    'plugin_name'           => 'Monitor',
    'home'                  => 'Resumen',
    'health'                => 'Salud',
    'security'              => 'Seguridad',
    'file'                  => 'Archivo:',
    'log_file'              => 'Archivo de registro:',
    'view_logs'             => 'Ver registros actuales',
    'clear_logs'            => 'Borrar registros',
    'configuration'         => 'Configuración',
    'main'                  => 'Resumen del estado del sitio',
    'logs'                  => 'Archivos de registro',
    'updates'               => 'Plugins',
    'status'                => 'Estado',
    'check'                 => 'Comprobación',
    'value'                 => 'Valor',
    'recommendation'        => 'Recomendación',
    'health_ok'             => 'OK',
    'health_info'           => 'Información',
    'health_warning'        => 'Advertencia',
    'health_error'          => 'Error',
    'security_observations' => 'Observaciones de seguridad',
    'ban_integration'       => 'Plugin Ban',
    'legacy_ban_notice'     => 'Monitor registra observaciones de seguridad limitadas. El plugin opcional Ban puede proporcionar funciones de bloqueo centralizadas sin convertir Monitor en un segundo motor de bloqueo.',
    'security_status' => 'Estado de seguridad',
    'security_no_issue' => 'No se detectó ningún problema con las comprobaciones de seguridad disponibles de Monitor.',
    'ban_not_installed' => 'No instalado',
    'ban_installed' => 'Instalado',
    'ban_optional_intro' => 'Ban es opcional. Monitor continúa registrando sus observaciones de seguridad limitadas sin él.',
    'ban_optional_capability' => 'Instalar Ban añade funciones de bloqueo centralizadas que Monitor puede utilizar cuando estén disponibles.',
    'ban_view_plugin' => 'Ver información del plugin Ban',
    'ban_version' => 'Versión',
    'ban_ip_capability' => 'Capacidad de solicitud de bloqueo de IP',
    'ban_capability_available' => 'Disponible',
    'ban_capability_unavailable' => 'No disponible',
    'ban_direct_sql' => 'Acoplamiento SQL directo con Ban',
    'ban_direct_sql_no' => 'No',
    'security_no_legacy_table' => 'No existe ninguna tabla de seguridad heredada de Monitor.',
    'security_no_observations' => 'No hay observaciones de seguridad recientes.',
    'read_only_advice'      => 'De forma predeterminada, Monitor observa y recomienda. Los cambios requieren una acción explícita de un administrador.',
    'config_tooltip_emails' => 'Destinatarios opcionales de las notificaciones de Monitor, separados por comas. Dejalo vacío para desactivar las notificaciones por correo electrónico.',
    'config_tooltip_repository' => 'Propietario de GitHub cuyos repositorios públicos revisa Monitor para obtener metadatos y versiones de plugins. Dejalo vacío para desactivar las comprobaciones remotas.',
    'config_tooltip_github_token' => 'Token de GitHub opcional, detallado y de solo lectura. MONITOR_GITHUB_TOKEN tiene prioridad. Las comprobaciones autenticadas disponen de una cuota de API mayor y usan una caché de etiquetas más corta.',

    // Media diagnostics
    'media_oversized_single' => '1 imagen supera los límites recomendados.',
    'media_oversized_multiple' => '%d imágenes superan los límites recomendados.',
    'media_show_files' => 'Mostrar archivos (%d)',
    'media_hide_files' => 'Ocultar archivos (%d)',
    'media_open_file_manager' => 'Abrir gestor de archivos',
    'media_view_image' => 'Ver imagen',
    'media_more_files' => 'Hay más imágenes sobredimensionadas; la lista está limitada.',
    'media_partial_scan' => 'El análisis del sistema de archivos alcanzó su límite de seguridad.',

    // Daily log archives
    'log_archive_title' => 'Archivos de registros',
    'log_archive_intro' => 'Monitor rota diariamente los archivos .log de Geeklog y conserva los últimos %d días de archivos.',
    'log_archive_safety' => 'Un registro activo solo se trunca después de que su copia de archivo se haya escrito correctamente. Los archivos se guardan bajo path_data, fuera del directorio web público.',
    'log_archive_empty' => 'Todavía no hay ningún archivo diario de registro disponible. La primera ejecución programada crea la referencia de rotación; los archivos aparecen después del siguiente día natural.',
    'log_archive_date' => 'Fecha',
    'log_archive_log' => 'Registro',
    'log_archive_size' => 'Tamaño',
    'log_archive_actions' => 'Acciones',
    'log_archive_view' => 'Ver',
    'log_archive_download' => 'Descargar',
    'log_archive_back' => 'Volver a los archivos de registros',
    'log_archive_preview_limited' => 'Esta vista previa muestra solo los 512 KiB más recientes del archivo. Descargá el archivo para obtener el registro diario completo.',
    'log_email_title' => 'Resumen diario de registros',
    'log_email_lines' => 'Líneas analizadas',
    'log_email_issue_lines' => 'Líneas de error/advertencia',
    'log_email_top_patterns' => 'Principales patrones de error.log',
    'log_email_no_activity' => 'No se archivó ningún registro de Geeklog no vacío para este día.',

    // Changes monitor
    'changes' => 'Cambios',
    'changes_page_title' => 'Cambios de Monitor',
    'changes_intro' => 'Compare instantáneas ligeras del sitio para ver qué cambió entre dos comprobaciones. Monitor solo registra el estado; no modifica el sitio.',
    'changes_capture' => 'Capturar estado actual',
    'changes_capture_ok' => 'Estado actual guardado.',
    'changes_capture_failed' => 'Monitor no pudo guardar la instantánea. Revisá que path_data permite escritura.',
    'changes_baseline_created' => 'Referencia creada. Capturá otro estado más adelante para ver qué cambió.',
    'changes_waiting' => 'Se necesita una segunda instantánea antes de poder comparar los cambios.',
    'changes_period' => 'Periodo comparado:',
    'changes_previous' => 'Anterior',
    'changes_current' => 'Actual',
    'changes_summary_changes' => 'Cambios',
    'changes_summary_plugins' => 'Cambios de plugins',
    'changes_summary_log' => 'Nuevos patrones de registro',
    'changes_summary_snapshots' => 'Instantáneas',
    'changes_none' => 'No se detectó ningún cambio significativo entre estas dos instantáneas.',
    'changes_detected' => 'Cambios detectados',
    'changes_environment' => 'Entorno',
    'changes_plugins' => 'Plugins',
    'changes_storage' => 'Almacenamiento',
    'changes_logs' => 'Nueva actividad de error.log',
    'changes_before' => 'Antes',
    'changes_after' => 'Después',
    'changes_occurrences' => 'ocurrencia(s)',
    'changes_log_none' => 'No se detectó ningún patrón nuevo de error, advertencia o excepción en error.log durante este periodo.',
    'changes_log_rotated' => 'error.log se rotó o truncó entre las dos instantáneas, por lo que la nueva parte no puede compararse de forma fiable.',
    'changes_log_truncated' => 'Los nuevos datos del registro superaron el límite de análisis. Monitor analizó únicamente los 512 KiB más recientes.',
    'changes_current_state' => 'Estado actual',
    'changes_geeklog' => 'Geeklog',
    'changes_php' => 'PHP',
    'changes_plugins_count' => 'Plugins instalados',
    'changes_disk_free' => 'Espacio libre en disco',
    'changes_error_log_size' => 'Tamaño de error.log',
    'changes_unknown' => 'Desconocido',
    'changes_enabled' => 'activado',
    'changes_disabled' => 'desactivado',
    'changes_code_geeklog_changed' => 'Versión de Geeklog modificada',
    'changes_code_php_changed' => 'Versión de PHP modificada',
    'changes_code_plugin_installed' => 'Plugin instalado',
    'changes_code_plugin_removed' => 'Plugin eliminado',
    'changes_code_plugin_version_changed' => 'Versión del plugin modificada',
    'changes_code_plugin_enabled' => 'Plugin activado',
    'changes_code_plugin_disabled' => 'Plugin desactivado',
    'changes_code_disk_free_decreased' => 'Disminuyó el espacio libre en disco',

    // Configuration audit
    'config_audit_title' => 'Auditoría de configuración',
    'config_audit_page_title' => 'Auditoría de configuración de Monitor',
    'config_audit_quick_description' => 'Revisá las diferencias entre siteconfig.php y los valores Core correspondientes de la base de datos.',
    'config_audit_back' => 'Resumen de Monitor',
    'config_audit_access_denied' => 'Acceso denegado',
    'config_audit_root_only' => 'Acceso reservado a administradores Root.',
    'config_audit_intro_title' => 'Auditoría de configuración de solo lectura.',
    'config_audit_intro' => 'Compara los valores Core definidos explícitamente en siteconfig.php con los valores correspondientes de conf_values. Solo las diferencias o los valores no válidos requieren atención.',
    'config_audit_issues' => 'Problemas',
    'config_audit_review' => 'Revisar',
    'config_audit_normal' => 'Normal',
    'config_audit_active_host' => 'Host:',
    'config_audit_siteconfig' => 'siteconfig.php:',
    'config_audit_unreadable_title' => 'Advertencia:',
    'config_audit_unreadable' => 'Monitor no pudo leer el siteconfig.php activo, por lo que la comparación está incompleta.',
    'config_audit_items_review' => 'Requiere atención',
    'config_audit_no_issues' => 'La configuración es coherente. Ningún valor Core en conflicto requiere atención.',
    'config_audit_secondary' => 'Mostrar %d valor(es) normal(es) o informativo(s)',
    'config_audit_footer' => 'Los valores sensibles se ocultan. El SQL opcional de alineación de la base de datos nunca se ejecuta automáticamente.',
    'config_audit_source_siteconfig' => 'siteconfig.php',
    'config_audit_source_database' => 'Base de datos',
    'config_audit_priority' => 'Fuente efectiva:',
    'config_audit_effective_value' => 'Valor efectivo',
    'config_audit_details' => 'Detalles',
    'config_audit_recommendation' => 'Recomendación',
    'config_audit_path' => 'Ruta:',
    'config_audit_path_exists' => 'existe',
    'config_audit_path_missing' => 'falta',
    'config_audit_optional_sql' => 'Alineación opcional de la base de datos',
    'config_audit_absent' => 'AUSENTE',
    'config_audit_redacted' => '[OCULTO]',
    'config_audit_value_true' => 'verdadero',
    'config_audit_value_false' => 'falso',
    'config_audit_value_null' => 'NULL',
    'config_audit_value_object' => '[OBJECT]',

    'config_audit_level_ok' => 'Esperado',
    'config_audit_level_info' => 'Información',
    'config_audit_level_review' => 'Revisar',
    'config_audit_level_warning' => 'Advertencia',

    'config_audit_status_identical' => 'Idéntico',
    'config_audit_status_core_file' => 'Esperado en siteconfig.php',
    'config_audit_status_file_only' => 'Solo siteconfig.php',
    'config_audit_status_db_unset' => 'Valor de base de datos sin definir',
    'config_audit_status_different' => 'Valores diferentes',
    'config_audit_status_decode_error' => 'Valor de base de datos ilegible',
    'config_audit_status_invalid_path' => 'Ruta no válida',

    'config_audit_why_identical' => 'El mismo valor existe en siteconfig.php y conf_values.',
    'config_audit_why_core_file' => 'Esta clave Core se define normalmente directamente en siteconfig.php.',
    'config_audit_why_file_only' => 'No existe ningún valor Core correspondiente en conf_values. Se utiliza el valor de siteconfig.php.',
    'config_audit_why_db_unset' => 'Existe una fila Core correspondiente en conf_values, pero su valor no está definido. siteconfig.php sigue siendo efectivo.',
    'config_audit_why_different' => 'siteconfig.php y conf_values contienen valores diferentes. siteconfig.php es efectivo en tiempo de ejecución.',
    'config_audit_why_decode_error' => 'El valor Core correspondiente en conf_values no pudo decodificarse de forma fiable.',
    'config_audit_why_invalid_path' => 'La ruta efectiva del sistema de archivos no existe actualmente.',

    'config_audit_action_none' => 'No se requiere ninguna acción.',
    'config_audit_action_file_only' => 'No se requiere ninguna acción salvo que este valor también deba gestionarse en la base de datos.',
    'config_audit_action_db_unset' => 'Verificá que el valor de la base de datos esté sin definir intencionadamente.',
    'config_audit_action_different' => 'Verificá que esta sobrescritura sea intencionada. Alinee el valor de la base de datos solo si el valor almacenado está obsoleto.',
    'config_audit_action_decode_error' => 'Revisá la fila Core correspondiente en conf_values antes de realizar cambios.',
    'config_audit_action_invalid_path' => 'Revisá la ruta configurada y la disponibilidad del sistema de archivos.',

    // Plugin catalog
    'plugin_catalog_intro' => 'Los plugins instalados se comparan con los repositorios públicos del propietario de GitHub configurado. Monitor informa de versiones disponibles y candidatos descubiertos, pero nunca instala ni actualiza código.',
    'plugin_catalog_owner' => 'Fuente de GitHub:',
    'plugin_catalog_refresh' => 'Actualizar datos de GitHub',
    'plugin_catalog_installed' => 'Plugins instalados',
    'plugin_catalog_discover' => 'Descubrir plugins',
    'plugin_catalog_discover_compatible' => 'Compatible con este sitio',
    'plugin_catalog_discover_incompatible' => 'No compatible con este sitio',
    'plugin_catalog_discover_unknown' => 'Compatibilidad desconocida',
    'plugin_catalog_discover_requirements_missing' => 'Requisitos no declarados',
    'plugin_catalog_discover_metadata_unavailable' => 'Metadatos no disponibles',
    'plugin_catalog_discover_intro' => 'Repositorios públicos recientes no instalados en este sitio. Revise la compatibilidad y la documentación antes de instalar nada.',
    'plugin_catalog_legacy_discover' => 'Repositorios antiguos',
    'plugin_catalog_legacy_intro' => 'Los repositorios públicos antiguos aún pueden ser útiles, pero se desconoce su compatibilidad con versiones recientes de Geeklog y PHP.',
    'plugin_catalog_plugin' => 'Plugin',
    'plugin_catalog_core_plugin' => 'Plugin Core',
    'plugin_catalog_core_update' => 'Disponible con la actualización de Geeklog',
    'plugin_catalog_latest_core_version' => 'Última versión Core',
    'plugin_catalog_current_geeklog' => 'Geeklog actual',
    'plugin_catalog_latest_geeklog_baseline' => 'Última referencia de Geeklog',
    'plugin_catalog_open_core_plugin' => 'Abrir plugin Core',
    'plugin_catalog_version' => 'Versión',
    'plugin_catalog_bundled_with_geeklog' => 'Incluido con Geeklog',
    'plugin_catalog_available_with_geeklog' => 'Disponible con Geeklog',
    'plugin_catalog_installed_version' => 'Instalado',
    'plugin_catalog_code_version' => 'Código',
    'plugin_catalog_latest_release' => 'Última versión publicada',
    'plugin_catalog_latest_version' => 'Última versión de GitHub',
    'plugin_catalog_version_source_release' => 'Versión publicada',
    'plugin_catalog_version_source_tag' => 'Etiqueta',
    'plugin_catalog_state' => 'Estado',
    'plugin_catalog_enabled' => 'Activado',
    'plugin_catalog_geeklog' => 'Requisito de Geeklog',
    'plugin_catalog_php_requirement' => 'Requisito de PHP',
    'plugin_catalog_update_compatible' => 'Actualización compatible',
    'plugin_catalog_update_incompatible' => 'Actualización no compatible',
    'plugin_catalog_compatibility_unknown' => 'Compatibilidad desconocida',
    'plugin_catalog_repository' => 'Repositorio',
    'plugin_catalog_yes' => 'Sí',
    'plugin_catalog_no' => 'No',
    'plugin_catalog_unknown' => 'Desconocido',
    'plugin_catalog_no_release' => 'Sin metadatos de versión',
    'plugin_catalog_no_version' => 'No se encontró ninguna versión publicada ni etiqueta de versión',
    'plugin_catalog_no_repository' => 'No hay repositorio coincidente',
    'plugin_catalog_catalog_unavailable' => 'Catálogo de GitHub no disponible',
    'plugin_catalog_current_compatible' => 'Actualizado para este Geeklog',
    'plugin_catalog_latest_compatible_version' => 'Última versión compatible',
    'plugin_catalog_newer_release' => 'Versión más reciente',
    'plugin_catalog_requires_geeklog' => 'requiere Geeklog %s',
    'plugin_catalog_requires_php' => 'requiere PHP %s',
    'plugin_catalog_current' => 'Actualizado',
    'plugin_catalog_update' => 'Actualización disponible',
    'plugin_catalog_ahead' => 'Versión instalada más reciente',
    'plugin_catalog_remote_unavailable' => 'Los metadatos de GitHub no están disponibles. La información local del plugin sigue mostrándose.',
    'plugin_catalog_remote_disabled' => 'Las comprobaciones remotas de metadatos están desactivadas porque no hay ningún propietario de GitHub válido configurado.',
    'plugin_catalog_none_discoverable' => 'No se encontró ningún repositorio adicional reciente de plugins para este propietario de GitHub.',
    'plugin_catalog_open_repository' => 'Abrir repositorio',
    'plugin_catalog_open_release' => 'Abrir versión publicada',
    'plugin_catalog_open_version' => 'Abrir versión',
    'plugin_catalog_updated' => 'Actualizado',
    'plugin_catalog_summary_installed' => 'Instalado',
    'plugin_catalog_summary_updates' => 'Actualizaciones disponibles',
    'plugin_catalog_summary_discover' => 'Candidatos recientes',
    'plugin_catalog_summary_unmatched' => 'Sin coincidencia en GitHub');

$PLG_monitor_MESSAGE3002 = $LANG32[9];
$PLG_monitor_MESSAGE3003 = 'Monitor no pudo completar la migración de la base de datos. La versión instalada no cambió; revise error.log y vuelva a intentar la actualización.';

$GLOBALS['LANG_configsections']['monitor'] = array(
    'label' => 'Monitor',
    'title' => 'Configuración de Monitor'
);

$GLOBALS['LANG_configsubgroups']['monitor'] = array(
    'sg_main' => 'Configuración principal'
);

$GLOBALS['LANG_tab']['monitor'] = array(
    'tab_main' => 'Principal'
);

$GLOBALS['LANG_fs']['monitor'] = array(
    'fs_main' => 'Configuración general'
);

$GLOBALS['LANG_confignames']['monitor'] = array(
    'emails' => 'Lista de direcciones de correo electrónico para notificaciones opcionales de Monitor (separadas por comas)',
    'repository' => 'Propietario del repositorio de GitHub reservado para metadatos de versiones de plugins (predeterminado: Geeklog-Plugins). Dejalo vacío para desactivar las comprobaciones remotas de metadatos.',
    'github_token' => 'Token de GitHub opcional para solicitudes de metadatos de la API. Se recomienda un token de solo lectura con permisos específicos. La variable de entorno MONITOR_GITHUB_TOKEN tiene prioridad.'
);

$LANG_configsections =& $GLOBALS['LANG_configsections'];
$LANG_configsubgroups =& $GLOBALS['LANG_configsubgroups'];
$LANG_tab =& $GLOBALS['LANG_tab'];
$LANG_fs =& $GLOBALS['LANG_fs'];
$LANG_confignames =& $GLOBALS['LANG_confignames'];
