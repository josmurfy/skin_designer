<?php
// Original: warehouse/marketplace/ebay/sync.php
// Encabezado
$_['heading_title'] = 'Sincronización y problemas';

// Texto
$_['text_overview'] = 'Resumen';
$_['text_inventory_health'] = 'Estado del inventario';
$_['text_sales_performance'] = 'Rendimiento de ventas';
$_['text_marketplace_performance'] = 'Rendimiento del Marketplace';
$_['text_top_products'] = 'Productos principales';
$_['text_bottom_products'] = 'Productos de movimiento lento (No sincronizados recientemente)';
$_['text_alerts'] = 'Alertas y advertencias';
$_['text_refresh'] = 'Actualizar';
$_['text_period'] = 'Período';
$_['text_today'] = 'Hoy';
$_['text_week'] = 'Últimos 7 días';
$_['text_month'] = 'Últimos 30 días';
$_['text_year'] = 'Último año';
$_['text_export'] = 'Exportar CSV';
$_['text_loading'] = 'Cargando datos...';
$_['text_no_data'] = 'No hay datos disponibles';
$_['text_category_performance'] = 'Rendimiento por categoría';
$_['text_location_analysis'] = 'Análisis de ubicación';

// Columna
$_['column_metric'] = 'Métrica';
$_['column_value'] = 'Valor';
$_['column_product'] = 'Producto';
$_['column_sku'] = 'SKU';
$_['column_sales'] = 'Unidades vendidas';
$_['column_revenue'] = 'Ingresos';
$_['column_stock'] = 'Stock';
$_['column_category'] = 'Categoría';
$_['column_location'] = 'Ubicación';
$_['column_count'] = 'Recuento';
$_['column_quantity'] = 'Cantidad';
$_['column_status'] = 'Estado';
$_['column_product_id'] = 'ID producto';
$_['column_ebay_id'] = 'ID artículo eBay';
$_['column_local_qty'] = 'Cant. local';
$_['column_unallocated'] = 'No asignado';
$_['column_total'] = 'Total';
$_['column_ebay_available'] = 'Disponible en eBay';
$_['column_difference'] = 'Diferencia';
$_['column_actions'] = 'Acciones';

// Botones
$_['button_sync_ebay'] = 'Sincronizar con eBay';
$_['button_print_report'] = 'Imprimir informe';
$_['button_update_quantity'] = 'Actualizar cantidad';
$_['button_import_from_ebay'] = 'Importar desde eBay';
$_['button_refresh_item'] = 'Actualizar';
$_['button_refresh'] = 'Actualizar';
$_['button_import_non_selected'] = 'Importar no seleccionados';

// Tooltips
$_['tooltip_import_from_ebay'] = 'Importar TODOS los datos de productos de eBay (5000+ artículos, tarda varios minutos)';
$_['tooltip_refresh_item'] = 'Actualizar TODO desde eBay (precio, cantidad, especificidades, fechas)';

// Mensajes
$_['text_update_confirm'] = '¿Actualizar cantidad en eBay a %s?';
$_['text_update_success'] = 'Éxito: Cantidad actualizada a %s en eBay';
$_['text_update_error'] = 'Error: %s';
$_['text_updating'] = 'Actualizando...';
$_['text_refresh_confirm'] = '¿Actualizar TODOS los datos desde eBay (precio, cantidad, especificidades, fechas)?';
$_['text_refresh_success'] = '¡Artículo actualizado correctamente desde eBay!';
$_['text_import_confirm'] = 'Esto importará todos los datos de productos desde eBay. Puede tardar varios minutos para 5000+ productos. ¿Continuar?';

// Entrada
$_['entry_period'] = 'Seleccionar período';

// Estadísticas
$_['stat_total_products'] = 'Total de productos';
$_['stat_active_products'] = 'Productos activos';
$_['stat_inventory_value'] = 'Valor del inventario';
$_['stat_orders_count'] = 'Pedidos';
$_['stat_revenue'] = 'Ingresos';
$_['stat_avg_order_value'] = 'Valor promedio de pedido';
$_['stat_new_products'] = 'Nuevos productos';
$_['stat_low_stock'] = 'Stock bajo';
$_['stat_out_of_stock'] = 'Sin existencias';
$_['stat_without_location'] = 'Sin ubicación';
$_['stat_without_image'] = 'Sin imagen';
$_['stat_unallocated'] = 'Inventario no asignado';
$_['stat_ebay_listed'] = 'Listado en eBay';
$_['stat_ready_to_list'] = 'Listo para listar';
$_['stat_with_errors'] = 'Con errores en Marketplace';
$_['stat_avg_listing_price'] = 'Precio promedio de listado';
$_['stat_completed_orders'] = 'Pedidos completados';
$_['stat_completed_revenue'] = 'Ingresos completados';
$_['stat_avg_stock_level'] = 'Nivel promedio de stock';

$_['text_success'] = '¡Éxito: Datos de análisis actualizados!';
$_['error_permission'] = 'Advertencia: ¡No tiene permiso para acceder a los análisis!';

// Panel de sincronización
$_['text_sync_progress'] = 'Progreso de sincronización';
$_['text_starting_sync'] = 'Iniciando sincronización...';
$_['text_listed_ebay'] = 'Listado en eBay';
$_['text_not_listed_qty'] = 'NO listado (cant. > 0)';
$_['text_marketplace_errors'] = 'Errores en Marketplace';
$_['text_not_synced'] = 'No sincronizado';
$_['text_not_imported'] = 'No importado';
$_['text_to_update_ebay'] = 'Por actualizar en eBay';
$_['text_quantity_mismatch'] = 'Discrepancia de cantidad';
$_['text_price_mismatch'] = 'Discrepancia de precio';
$_['text_specifics_mismatch'] = 'Discrepancia de especificidades';
$_['text_sync_ebay'] = 'Sincronizar eBay';
$_['text_refresh_data'] = 'Actualizar datos';

// Pestañas
$_['tab_errors'] = 'Errores';
$_['tab_not_listed'] = 'No listado';
$_['tab_not_synced'] = 'No sincronizado';
$_['tab_mismatch'] = 'Discrepancia';
$_['tab_price_mismatch'] = 'Discrepancia de precio';
$_['tab_qty_mismatch'] = 'Discrepancia de cant.';
$_['tab_specifics_mismatch'] = 'Discrepancia de especificidades';
$_['tab_condition_mismatch'] = 'Discrepancia de condición';
$_['tab_category_mismatch'] = 'Discrepancia de categoría';
$_['tab_not_imported']    = 'No importado';
$_['tab_to_update']       = 'Por actualizar';
$_['tab_slow_moving'] = 'Movimiento lento';
$_['text_not_imported_info'] = 'Estos productos están listados en eBay pero nunca han sido importados (sin fecha last_import).';
$_['text_to_update_info']   = 'Estos productos tienen cambios locales pendientes de enviar a eBay (to_update = 1).';
$_['text_no_not_imported']  = '¡No hay productos esperando importación!';
$_['text_no_to_update']     = '¡No hay productos pendientes de actualización en eBay!';
$_['button_update_all_ebay'] = 'Actualizar TODO en eBay';
$_['text_no_not_synced']    = '¡Todos los productos están al día!';

$_['text_products_errors'] = 'Productos con errores en Marketplace';
$_['text_products_not_listed'] = 'Productos NO listados en eBay (cantidad > 0)';
$_['text_products_not_synced'] = 'Productos no sincronizados con eBay';
$_['text_quantity_mismatches'] = 'Discrepancias de cantidad (solo phoenixliquidation)';
$_['text_slow_moving_items'] = 'Artículos de movimiento lento (No sincronizados en 90+ días)';
$_['text_no_products'] = 'No se encontraron productos.';
$_['text_error_details'] = 'Detalles del error';
$_['text_ebay_error'] = 'Error eBay';
$_['text_error_code'] = 'Código de error';
$_['text_error_count'] = 'Recuento de errores';
$_['text_last_sync'] = 'Última sincronización';
$_['text_days_ago'] = 'Hace %s días';
$_['text_never'] = 'Nunca';
$_['text_edit_product'] = 'Editar producto';
$_['text_no_errors'] = '¡No se encontraron errores en el Marketplace!';
$_['text_all_listed'] = '¡Todos los productos con stock están listados en eBay!';
$_['text_all_synced'] = '¡Todos los productos están sincronizados!';
$_['text_no_mismatch'] = '¡No se encontraron discrepancias de cantidad!';
$_['text_never_synced'] = 'Nunca sincronizado';
$_['text_not_listed'] = 'No listado';
$_['text_solutions'] = 'Soluciones rápidas:';
$_['text_error_stats'] = 'Estadísticas de errores (%s tipos encontrados)';
$_['text_products_info'] = 'Estos productos tienen cantidades diferentes en eBay vs inventario local (Cantidad + No asignado)';
$_['text_print_all'] = 'Imprimir todo (%s)';
$_['text_not_listed_info'] = 'Estos productos tienen stock pero NO están listados en el Marketplace eBay';
$_['text_deselect_all'] = 'Deseleccionar todo';

$_['text_mismatch_found'] = '%s discrepancia(s) encontrada(s)';
$_['text_no_mismatch_found'] = 'No se encontraron discrepancias de %s.';
$_['text_price'] = 'Precio';
$_['text_quantity'] = 'Cantidad';
$_['text_specifics'] = 'Especificidades';
$_['text_condition'] = 'Condición';
$_['text_category'] = 'Categoría';
$_['text_local'] = 'Local';
$_['text_ebay'] = 'eBay';
$_['text_diff'] = 'Dif.';
$_['text_sync_to_ebay'] = 'Exportar a eBay';
$_['text_sync_from_ebay'] = 'Importar desde eBay';
$_['column_price'] = 'Precio';
$_['column_quantity'] = 'Cantidad';
$_['column_specifics'] = 'Especificidades';
$_['column_condition'] = 'Condición';
$_['column_category'] = 'Categoría';
$_['text_no_leaf_category'] = 'El producto no tiene categoría hoja (leaf=1)';
$_['text_category_values_differ'] = 'Las categorías local y eBay son diferentes';

$_['button_edit'] = 'Editar';
// Claves JS
$_['text_confirm_sync_all']     = '¿Esto sincronizará todos los productos con eBay. Puede tardar varios minutos para 5000+ productos. ¿Continuar?';
$_['text_error_sync_url']       = 'Error: URL de sincronización no configurada';
$_['text_confirm_sync_product'] = '¿Sincronizar el producto "%s" en el marketplace de eBay?';
$_['text_confirm_refresh_all']  = '¿Actualizar TODOS los datos desde eBay (precio, cantidad, especificaciones, fechas)?';
$_['text_confirm_import_non_selected'] = '¿Importar ahora los productos no seleccionados? GetItem se ejecutará solo para los productos seleccionados.';

// Discrepancia de imágenes
$_['tab_image_mismatch']       = 'Discrepancia Imágenes';
$_['column_oc_images']         = 'Imágenes OC';
$_['column_ebay_images']       = 'Imágenes eBay';
$_['column_diff']              = 'Dif';
$_['text_image_mismatch_info'] = 'Productos donde el número de imágenes en OpenCart difiere de lo publicado en eBay. Ejecute una sincronización para actualizar los contadores.';

// Force refresh
$_['button_force_refresh']        = 'Forzar Actualización Completa';
$_['tooltip_force_refresh']       = 'Reimportar TODOS los datos desde eBay (categoría, condición, específicos, imágenes) aunque ya estén en BD. Más lento — llama GetItem para cada producto.';
$_['text_confirm_force_refresh']  = '¿Esto llamará GetItem en TODOS los productos para actualizar categoría, condición, específicos e imágenes. Mucho más lento y consume más cuota de API de eBay. ¿Continuar?';

// Corrección masiva de imágenes
$_['button_close']                   = 'Cerrar';
$_['button_bulk_fix_images']         = 'Corregir todas las imágenes';
$_['button_fix_single_image']        = 'Importar imágenes eBay para este producto';
$_['text_bulk_fix_tooltip']          = 'Importar imágenes eBay para TODOS los productos con discrepancia, luego restablecer su contador de imágenes a 0 para revalidar en la próxima sincronización.';
$_['text_bulk_fix_confirm']          = 'Esta acción descargará imágenes de eBay para todos los productos con discrepancia y reemplazará sus imágenes actuales de OC. El contador ebay_image_count se restablecerá a 0 y se revalidará en la próxima importación de eBay. ¿Continuar?';
$_['text_bulk_fix_modal_title']      = 'Importación masiva de imágenes eBay';
$_['text_bulk_fix_processing']       = 'Importando imágenes eBay para todos los productos con discrepancia… Por favor, espere.';
$_['text_bulk_fix_imported']         = 'Importados';
$_['text_bulk_fix_skipped']          = 'Omitidos';
$_['text_bulk_fix_errors']           = 'Errores';
$_['text_bulk_fix_reset_info']       = "Tras la importación, el contador eBay (ebay_image_count) se restableció a 0 para cada producto. Ejecute 'Importar desde eBay' o 'Forzar Actualización Completa' para obtener el recuento real de eBay.";
$_['text_bulk_fix_error_details']    = 'Productos con errores:';

// Image Backup Scan
$_['button_scan_image_backup']       = 'Escanear image_backup';
$_['tooltip_scan_image_backup']      = 'Cuenta los archivos de imagen en image_backup/data/product/ para cada producto y guarda el total en la base de datos.';
$_['text_scan_backup_confirm']       = 'Se escaneará el directorio image_backup y se contarán las imágenes para todos los productos. Tardará unos segundos. ¿Continuar?';
$_['text_scan_backup_complete']      = 'Escaneo de respaldo completado';
$_['column_backup_images']           = 'Copia';
$_['text_backup_not_scanned']        = 'N/A';

// Tabla mismatch backup & popup
$_['text_backup_table_title']        = 'OC vs Respaldo — Productos con más imágenes en respaldo que en OC';
$_['text_backup_table_info']         = 'Estos productos tienen más imágenes en image_backup que en OpenCart. Use el botón para revisarlas y transferirlas.';
$_['column_backup_extra']            = 'Extra en Respaldo';
$_['button_open_backup_popup']       = 'Revisar'; // 'Ver Respaldo' --- IGNORE ---
$_['text_popup_backup_title']        = 'Imágenes Respaldo — Producto #%s';
$_['button_transfer_to_oc']          = 'Transferir a OC';
$_['button_delete_from_backup']      = 'Eliminar del Respaldo';
$_['text_backup_select_all']         = 'Seleccionar todo';
$_['text_backup_no_files']           = 'No se encontraron imágenes de respaldo para este producto.';
$_['text_backup_already_in_oc']      = 'Ya en OC';
$_['text_backup_type_primary']       = 'Principal';
$_['text_backup_type_secondary']     = 'Secundaria';
$_['text_backup_transferred']        = '%d imagen(es) transferida(s) a OC.';
$_['text_backup_deleted']            = '%d imagen(es) eliminada(s) del respaldo.';
$_['text_backup_confirm_delete']     = '¿Está seguro de eliminar permanentemente las imágenes de respaldo seleccionadas?';
