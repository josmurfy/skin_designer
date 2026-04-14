<?php
// Heading
$_['heading_title']       = 'Debug Logger';

// Text
$_['text_extension']      = 'Extensiones';
$_['text_success']        = 'Configuración del Debug Logger guardada correctamente.';
$_['text_success_clear']  = 'Todos los informes han sido eliminados.';
$_['text_enabled']        = 'Habilitado';
$_['text_general']        = 'General';
$_['text_display']        = 'Visualización';
$_['text_capture']        = 'Opciones de captura';
$_['text_severity']       = 'Niveles de severidad';
$_['text_data']           = 'Gestión de datos';
$_['text_stats']          = 'Estadísticas';
$_['text_total_reports']  = 'Total de informes';
$_['text_open_reports']   = 'Abiertos / Sin resolver';
$_['text_from_admin']     = 'Desde Admin';
$_['text_from_catalog']   = 'Desde Catálogo';
$_['text_show_admin']     = 'Mostrar en el panel Admin';
$_['text_show_catalog']   = 'Mostrar en la Tienda';
$_['text_info_title']     = 'Cómo funciona';
$_['text_info_1']         = 'Instala el módulo para crear la tabla DB y activar los eventos.';
$_['text_info_2']         = 'Tras guardar, el botón Debug aparece en las áreas activadas.';
$_['text_info_3']         = 'Haz clic en el botón para enviar un informe con errores de consola y comentario.';

$_['text_active_title']           = 'Resumen de actividad';
$_['text_not_enabled']            = 'El módulo está deshabilitado. Active el estado y configure los ajustes de captura para comenzar.';
$_['text_notifications_pro_only'] = 'Las notificaciones por correo y webhook son funciones exclusivas de la versión Pro.';
$_['text_license_active']         = 'Licencia Pro activa';

// Entries
$_['entry_status']           = 'Estado';
$_['entry_admin_enable']     = 'Panel de administración';
$_['entry_catalog_enable']   = 'Catálogo (Tienda)';
$_['entry_capture_console']  = 'Capturar errores de consola';
$_['entry_capture_network']  = 'Capturar AJAX fallidos';
$_['entry_require_comment']  = 'Comentario obligatorio';
$_['entry_max_reports']      = 'Máximo de informes';
$_['entry_severity_bug']     = 'Error';
$_['entry_severity_warning'] = 'Advertencia';
$_['entry_severity_info']    = 'Información';

// Help
$_['help_admin_enable']      = 'Muestra el botón Debug en la barra de navegación del admin.';
$_['help_catalog_enable']    = 'Muestra un botón flotante Debug en todas las páginas de la tienda.';
$_['help_capture_console']   = 'Captura automáticamente console.error() y excepciones JS no controladas.';
$_['help_capture_network']   = 'Intercepta y registra peticiones AJAX/fetch fallidas.';
$_['help_require_comment']   = 'Obliga al usuario a escribir un comentario antes de enviar el informe.';
$_['help_max_reports']       = 'Número máximo de informes a conservar. Los más antiguos se eliminan automáticamente (0 = ilimitado).';
$_['help_severity']          = 'Elige qué niveles aparecen en el desplegable del formulario de informe.';

// Buttons
$_['button_save']         = 'Guardar';
$_['button_cancel']       = 'Cancelar';
$_['button_view_reports'] = 'Ver informes';
$_['button_clear_all']    = 'Borrar todos los informes';

// Popup modal (injected via event)
$_['popup_title']          = 'Reportar un problema';
$_['popup_btn_trigger']    = 'Reportar un problema';
$_['popup_label_page']     = 'Página';
$_['popup_label_severity'] = 'Severidad';
$_['popup_label_comment']  = 'Comentario';
$_['popup_label_console']  = 'Errores de consola';
$_['popup_placeholder']    = 'Describe el problema...';
$_['popup_severity_bug']   = 'Error';
$_['popup_severity_warn']  = 'Advertencia';
$_['popup_severity_info']  = 'Información';
$_['popup_btn_cancel']     = 'Cancelar';
$_['popup_btn_save']       = 'Enviar';
$_['popup_btn_reports']    = 'Ver informes';
$_['popup_tip_severity']   = 'Elija el nivel de impacto del problema observado.';
$_['popup_tip_comment']    = 'Describa qué estaba haciendo y qué salió mal. Obligatorio si "Comentario requerido" está activado.';
$_['popup_tip_console']    = 'Errores de JavaScript capturados automáticamente en esta página.';
$_['popup_tip_reports']    = 'Acceder a la lista de informes (solo administradores).';

// Confirm
$_['text_confirm_clear']  = '¿Eliminar TODOS los informes? Esta acción no se puede deshacer.';

// Error
$_['error_permission']    = '¡Atención: no tienes permiso para modificar el Debug Logger!';

// Pro features
$_['text_license']           = 'Licencia';
$_['entry_license_key']      = 'Clave de licencia';
$_['help_license_key']       = 'Introduce tu clave Pro (XXXX-XXXX-XXXX-XXXX) para desbloquear todas las funciones.';
$_['text_free_limit']        = 'Versión gratuita: limitada a 50 informes. Actualiza a Pro para informes ilimitados y funciones avanzadas.';
$_['text_disabled']          = 'Deshabilitado';

$_['entry_capture_screenshot'] = 'Captura de pantalla';
$_['help_capture_screenshot']  = 'Toma automáticamente una captura de pantalla al reportar (html2canvas).';
$_['popup_label_screenshot']   = 'Captura de pantalla';

$_['text_email']             = 'Notificaciones por correo';
$_['entry_email_enable']     = 'Activar correo';
$_['entry_email_to']         = 'Destinatario';
$_['help_email_to']          = 'Dirección de correo para recibir alertas de informes de errores.';
$_['text_email_severity']    = 'Notificar para';
$_['entry_email_bug']        = 'Error';
$_['entry_email_warning']    = 'Advertencia';
$_['entry_email_info']       = 'Información';

$_['text_webhook']           = 'Notificaciones Webhook';
$_['entry_webhook_type']     = 'Servicio';
$_['entry_webhook_url']      = 'URL Webhook';
$_['help_webhook_url']       = 'Pega tu URL del Webhook de Slack o Discord aquí.';
$_['text_test_email_title']  = 'Probar conexión';
$_['button_test_email']      = 'Enviar correo de prueba';
$_['help_test_email']        = 'Envía un correo de prueba al destinatario usando la configuración de correo de la tienda.';
$_['text_test_email_sent']   = 'Correo de prueba enviado a %s — revise su bandeja de entrada (y spam).';
$_['text_test_email_failed'] = 'Error al enviar correo de prueba: %s';
$_['text_test_email_invalid'] = 'Por favor ingrese una dirección de correo válida arriba.';

$_['text_pro_title']         = 'Funciones Pro';
$_['text_pro_1']             = 'Informes ilimitados';
$_['text_pro_1_desc']        = 'Sin límite de almacenamiento de informes. La versión gratuita está limitada a 50.';
$_['text_pro_2']             = 'Captura de pantalla';
$_['text_pro_2_desc']        = 'Captura automática de pantalla en cada informe usando html2canvas.';
$_['text_pro_3']             = 'Alertas por correo';
$_['text_pro_3_desc']        = 'Reciba alertas por correo cuando se reporten nuevos errores.';
$_['text_pro_4']             = 'Webhook Slack / Discord';
$_['text_pro_4_desc']        = 'Envíe informes a su canal de Slack o Discord al instante.';
$_['text_pro_5']             = 'Exportar e Informes';
$_['text_pro_5_desc']        = 'Exporte informes en CSV/JSON. Asigne errores a miembros del equipo.';
$_['text_pro_6']             = 'Anotador de pantalla';
$_['text_pro_6_desc']        = 'Editor a pantalla completa para anotar y resaltar las capturas de pantalla.';

// Tabs
$_['tab_general']            = 'General';
$_['tab_capture']            = 'Captura';
$_['tab_notifications']      = 'Notificaciones';
$_['tab_appearance']         = 'Apariencia';
$_['tab_updates']            = 'Actualizaciones';
$_['tab_permissions']        = 'Permisos';
$_['tab_license']            = 'Licencia y Pro';

// Updates tab
$_['text_current_version']   = 'Versión actual';
$_['text_latest_version']    = 'Última versión';
$_['text_update_available']  = '¡Una nueva versión está disponible!';
$_['text_up_to_date']        = 'Usted tiene la última versión.';
$_['text_checking_update']   = 'Verificando actualizaciones...';
$_['text_update_error']      = 'No se pudo verificar actualizaciones. Inténtelo más tarde.';
$_['text_changelog']         = 'Notas de la versión';
$_['text_update_source']     = 'Las actualizaciones se verifican desde GitHub:';
$_['button_check_update']    = 'Buscar actualizaciones';
$_['button_download_update'] = 'Descargar';
$_['button_view_release']    = 'Ver release';
$_['button_install_update']  = 'Instalar actualización';
$_['text_installing']        = 'Descargando e instalando actualización...';
$_['text_install_success']   = '¡Actualización instalada con éxito! La versión %s está ahora activa. Por favor, recargue la página.';
$_['text_install_download_error'] = 'Error al descargar el archivo de actualización.';
$_['text_install_extract_error']  = 'Error al extraer el archivo de actualización.';
$_['text_version_history']       = 'Historial de versiones';
$_['text_version_history_hint']  = 'Haga clic en la pestaña Actualizaciones para cargar el historial.';
$_['text_version_installed']     = 'INSTALADA';
$_['text_version_newer']         = 'NUEVO';
$_['text_version_downgrade']     = 'Instalar esta versión';
$_['text_confirm_downgrade']     = '¿Está seguro de que desea instalar una versión anterior? Esto sobrescribirá la versión actual.';
$_['button_refresh']             = 'Actualizar';

// Appearance tab (Pro)
$_['text_appearance_pro_only'] = 'La personalización de la apariencia requiere licencia Pro.';
$_['text_appearance_colors'] = 'Colores';
$_['text_appearance_layout'] = 'Disposición del botón';
$_['text_appearance_preview'] = 'Vista previa';
$_['entry_btn_color']        = 'Color del botón';
$_['help_btn_color']         = 'Color de fondo del botón de reporte.';
$_['entry_header_color']     = 'Color del encabezado modal';
$_['help_header_color']      = 'Color de fondo del encabezado del popup.';
$_['entry_accent_color']     = 'Color de acento';
$_['help_accent_color']      = 'Color usado para bordes, enlaces y resaltado.';
$_['entry_btn_position']     = 'Posición del botón';
$_['help_btn_position']      = 'Dónde aparece el botón de reporte en la página.';
$_['entry_btn_size']         = 'Tamaño del botón';
$_['text_pos_navbar']        = 'Barra de navegación (por defecto)';
$_['text_pos_bottom_right']  = 'Abajo derecha';
$_['text_pos_bottom_left']   = 'Abajo izquierda';
$_['text_pos_top_right']     = 'Arriba derecha';
$_['text_pos_top_left']      = 'Arriba izquierda';
$_['text_size_small']        = 'Pequeño';
$_['text_size_medium']       = 'Mediano';
$_['text_size_large']        = 'Grande';
$_['button_reset_defaults']  = 'Restablecer valores predeterminados';

// Permissions tab
$_['text_permissions_info']  = 'Seleccione qué grupos de usuarios pueden ver y usar el botón Debug Logger. Si no se selecciona ninguno, todos los grupos tienen acceso.';
$_['text_allowed_groups']    = 'Grupos de usuarios permitidos';
$_['help_allowed_groups']    = 'Marque los grupos que deben ver el botón de reporte Debug Logger en el panel de administración.';
$_['text_group_name']        = 'Nombre del grupo';

// v3.0.0 — Tags, Resolución, Acciones masivas
$_['text_tags']              = 'Etiquetas';
$_['text_add_tag']           = 'Agregar etiqueta';
$_['text_resolution']        = 'Resolución';
$_['text_resolution_hint']   = 'Documente la corrección, solución alternativa o causa raíz aquí.';
$_['text_bulk_selected']     = '%d seleccionado(s)';
$_['text_bulk_close']        = 'Cerrar seleccionados';
$_['text_bulk_open']         = 'Reabrir seleccionados';
$_['text_bulk_delete']       = 'Eliminar seleccionados';
$_['text_bulk_confirm_delete'] = '¿Eliminar los reportes seleccionados?';
$_['text_assignment_email']  = 'Notificación de asignación enviada a %s.';
$_['text_filter_tag']        = 'Filtrar por etiqueta';

// v3.1.0 — Análisis & Modo oscuro
$_['text_analytics']         = 'Análisis';
$_['text_analytics_title']   = 'Panel de análisis';
$_['text_total_reports_stat'] = 'Total de reportes';
$_['text_open_stat']         = 'Abierto';
$_['text_closed_stat']       = 'Cerrado';
$_['text_avg_resolution']    = 'Tiempo promedio de resolución';
$_['text_reports_per_day']   = 'Reportes / Día (Últimos 30 días)';
$_['text_severity_dist']     = 'Distribución por severidad';
$_['text_activity_by_hour']  = 'Actividad por hora (Últimos 30 días)';
$_['text_source_dist']       = 'Distribución por fuente';
$_['text_top_pages']         = 'Páginas con más errores';
$_['text_recurring_issues']  = 'Problemas recurrentes (misma URL + severidad)';
$_['text_recent_activity']   = 'Actividad reciente';
$_['text_no_data']           = 'No hay datos aún.';
$_['text_no_recurring']      = 'No se detectaron patrones recurrentes.';
$_['button_dark_mode']       = 'Modo oscuro';
$_['button_light_mode']      = 'Modo claro';
$_['text_settings']          = 'Configuración';
$_['text_reports']           = 'Reportes';

// v3.2.0 — Menú Admin (column_left)
$_['text_menu_title']        = 'Debug Logger';
$_['text_menu_dashboard']    = 'Panel de control';
$_['text_menu_reports']      = 'Reportes de log';
$_['text_menu_settings']     = 'Configuración';

// v3.3.0
$_['popup_label_files']      = 'Archivos cargados';
$_['popup_tip_files']        = 'Archivos PHP, Twig, JS y CSS cargados en esta página.';

// v3.3.2 — toast messages
$_['popup_toast_saved']      = 'Reporte #%s guardado';
$_['popup_toast_error']      = 'Error';

// v3.3.2 — screenshot editor
$_['popup_ss_edit']          = 'Editar captura';
$_['popup_ss_done']          = 'Listo';
$_['popup_ss_cancel']        = 'Cancelar';
$_['popup_ss_draw']          = 'Dibujar';
$_['popup_ss_arrow']         = 'Flecha';
$_['popup_ss_rect']          = 'Rectángulo';
$_['popup_ss_text']          = 'Texto';
$_['popup_ss_undo']          = 'Deshacer';
$_['popup_ss_reset']         = 'Restablecer';
$_['popup_ss_thin']          = 'Fino';
$_['popup_ss_normal']        = 'Normal';
$_['popup_ss_thick']         = 'Grueso';
$_['popup_ss_prompt']        = 'Texto:';
