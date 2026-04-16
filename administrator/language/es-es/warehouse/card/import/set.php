<?php
// Original: warehouse/card/import/set.php
// Heading
$_['heading_title']          = 'Importación Bruta de Cartas';

// Text
$_['text_home']              = 'Inicio';
$_['text_card_import']       = 'Importación Precios de Cartas';
$_['text_upload_instructions'] = 'Suba un archivo CSV para importar datos brutos de cartas a la base de datos.';
$_['text_csv_format']        = 'Columnas requeridas: title, category, year, brand, set, subset, player, card_number, attributes, team, variation, ungraded, grade_9, grade_10, front_image, ebay_sales';
$_['text_upload_success']    = '¡CSV subido e importado exitosamente!';
$_['text_uploading']         = 'Subiendo...';
$_['text_success']           = 'Éxito';
$_['text_error']             = 'Error';
$_['text_import_results']    = 'Resultados de la importación';
$_['text_import_summary']    = '¡El archivo CSV fue importado exitosamente! Aquí un resumen:';
$_['text_preview_title']     = 'Vista previa (primeras filas)';
$_['text_records_list']      = 'Registros en la base de datos';
$_['text_total_records']     = 'Total de registros';
$_['text_no_records']        = 'Sin registros. Suba un CSV para comenzar.';
$_['text_truncate_confirm']  = 'ADVERTENCIA: Esto eliminará TODOS los registros de la tabla card_raw. ¿Está seguro?';
$_['text_delete_confirm']    = '¿Eliminar los registros seleccionados?';
$_['text_confirm_cancel']    = '¿Está seguro de que desea cancelar?';

// Statistics
$_['text_total_cards']       = 'Total de cartas';
$_['text_inserted']          = 'Insertados';
$_['text_skipped']           = 'Omitidos (errores)';
$_['text_total_in_file']     = 'Total en el archivo';
$_['text_in_database']       = 'En la base de datos';

// Column headers
$_['column_card_raw_id']     = 'ID';
$_['column_title']           = 'Título';
$_['column_category']        = 'Categoría';
$_['column_year']            = 'Año';
$_['column_brand']           = 'Marca';
$_['column_set']             = 'Set';
$_['column_subset']          = 'Subconjunto';
$_['column_player']          = 'Jugador';
$_['column_card_number']     = 'No. carta';
$_['column_attributes']      = 'Atributos';
$_['column_team']            = 'Equipo';
$_['column_variation']       = 'Variación';
$_['column_ungraded']        = 'Sin calificar';
$_['column_grade_9']         = 'Grado 9';
$_['column_grade_10']        = 'Grado 10';
$_['column_front_image']     = 'Imagen';
$_['column_ebay_sold_raw']     = 'Auction Raw';
$_['column_ebay_sold_graded']  = 'Auction Graded';
$_['column_ebay_list_raw']     = 'Buy Now Raw';
$_['column_ebay_list_graded']  = 'Buy Now Graded';
$_['column_ebay_checked_at']   = 'Verificado eBay';
$_['column_ebay_sales']         = 'Ventas eBay';
$_['column_actions']           = 'Acciones';
$_['column_status']          = 'Estado';
$_['column_date_added']      = 'Fecha añadida';

// Buttons
$_['button_upload']          = 'Subir CSV';
$_['button_save_to_db']       = 'Guardar en la base de datos';
$_['button_delete_selected'] = 'Eliminar seleccionados';
$_['button_truncate']        = 'Borrar todos los registros';
$_['button_cancel']          = 'Cancelar';
$_['button_yes']             = 'Sí';
$_['button_no']              = 'No';
$_['button_ok']              = 'OK';
$_['button_close']           = 'Cerrar';
$_['button_fetch_ebay']        = 'Buscar precios eBay';
$_['button_sold_graded']       = 'Sold Graded';
$_['button_merge_preview']     = 'Fusionar filas';

// Errors
$_['error_permission']       = 'Advertencia: ¡No tiene permiso para realizar esta acción!';
$_['error_no_file']          = 'No se subió ningún archivo.';
$_['error_invalid_file']     = 'Formato de archivo inválido. Por favor suba un archivo CSV.';
$_['error_empty_file']       = 'El archivo CSV está vacío o tiene un formato inválido.';
$_['error_no_data']          = 'No hay registros seleccionados.';
$_['error_ajax']             = 'Ocurrió un error AJAX.';

// Filtros
$_['text_filter_title']           = 'Título';
$_['text_filter_category']        = 'Categoría';
$_['text_filter_year']            = 'Año';
$_['text_filter_brand']           = 'Marca';
$_['text_filter_set']             = 'Set';
$_['text_filter_player']          = 'Jugador';
$_['text_filter_card_number']     = 'No. carta';
$_['text_filter_min_price']       = 'Min $';
$_['text_filter_max_price']       = 'Max $';
$_['button_filter']               = 'Buscar';
$_['button_reset_filter']         = 'Reiniciar';
$_['text_limit']                  = 'Por página';
$_['text_pagination_showing']     = 'Mostrando %d a %d de %d';
$_['text_already_imported']       = 'Ya en la base de datos';
$_['text_already_imported_msg']   = 'Advertencia: la base de datos ya contiene %d registros. Importar añadirá más registros.';
$_['text_market_fetch_done']        = 'Búsqueda completada';
$_['text_market_cached']            = 'en caché';
$_['text_market_rate_limit']        = 'Límite API de eBay alcanzado';
$_['text_market_updated']           = 'Precios de mercado actualizados';
$_['text_use_filters']               = 'Utilice los filtros para mostrar las tarjetas con datos de ventas.';
$_['text_bid_singular']              = 'puja';
$_['text_bid_plural']                = 'pujas';
