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
$_['text_info_2']         = 'Tras guardar, el botón 🐛 Debug aparece en las áreas activadas.';
$_['text_info_3']         = 'Haz clic en el botón para enviar un informe con errores de consola y comentario.';

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
$_['help_admin_enable']      = 'Muestra el botón 🐛 en la barra de navegación del admin.';
$_['help_catalog_enable']    = 'Muestra un botón flotante 🐛 en todas las páginas de la tienda.';
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
$_['popup_severity_bug']   = '🐛 Error';
$_['popup_severity_warn']  = '⚠ Advertencia';
$_['popup_severity_info']  = 'ℹ Información';
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

$_['text_pro_title']         = 'Funciones Pro';
$_['text_pro_1']             = 'Informes ilimitados';
$_['text_pro_2']             = 'Captura de pantalla (html2canvas)';
$_['text_pro_3']             = 'Notificaciones por correo en nuevos informes';
$_['text_pro_4']             = 'Integración Webhook Slack / Discord';
$_['text_pro_5']             = 'Exportar CSV / JSON + asignación de informes';
