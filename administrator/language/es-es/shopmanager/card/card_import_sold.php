<?php
// Heading
$_['heading_title']          = 'Importador de Precios Vendidos';

// Breadcrumb / nav
$_['text_home']              = 'Inicio';

// Instructions
$_['text_upload_instructions'] = 'Suba un archivo CSV para importar los precios de cartas vendidas a la base de datos. Cada fila del CSV se convierte en un registro.';
$_['text_csv_format']          = 'Columnas requeridas: title, category, year, brand, set_name, subset, player, card_number, attributes, team, variation, grader, grade, price, currency, type_listing, bids, total_sold, ebay_item_id, front_image, status, date_sold';
$_['text_uploading']           = 'Subiendo...';
$_['text_preview_title']       = 'Vista previa — agrupado por número de carta';
$_['text_records_list']        = 'Registros Vendidos en la Base de Datos';
$_['text_no_records']          = 'Sin registros. Suba un CSV para comenzar.';
$_['text_group']               = 'Grupo';
$_['text_missing_card_number'] = 'Número de carta faltante';
$_['text_enabled']             = 'Activado';
$_['text_disabled']            = 'Desactivado';
$_['text_success']             = 'Éxito';
$_['text_error']               = 'Error';
$_['text_pagination']          = 'Mostrando %d-%d de %d';

// Import results
$_['text_import_results']    = 'Resultados de Importación';
$_['text_import_summary']    = '¡CSV importado con éxito!';
$_['text_total_in_file']     = 'Total en Archivo';
$_['text_inserted']          = 'Insertados';
$_['text_skipped']           = 'Omitidos (errores)';
$_['text_in_database']       = 'En Base de Datos';

// Confirm dialogs
$_['text_truncate_confirm']  = 'ADVERTENCIA: Esto eliminará TODOS los registros de precios vendidos. ¿Está seguro?';
$_['text_delete_confirm']    = '¿Eliminar los registros seleccionados?';

// Column headers
$_['column_card_price_sold_id'] = 'ID';
$_['column_title']           = 'Título';
$_['column_category']        = 'Categoría';
$_['column_year']            = 'Año';
$_['column_brand']           = 'Marca';
$_['column_set']             = 'Set';
$_['column_subset']          = 'Sub-set';
$_['column_player']          = 'Jugador';
$_['column_card_number']     = 'No. Carta';
$_['column_attributes']      = 'Atributos';
$_['column_team']            = 'Equipo';
$_['column_variation']       = 'Variación';
$_['column_grader']          = 'Gradificador';
$_['column_grade']           = 'Grado';
$_['column_price']           = 'Precio';
$_['column_currency']        = 'Moneda';
$_['column_type_listing']    = 'Tipo';
$_['column_bids']            = 'Pujas';
$_['column_total_sold']      = 'Total Vendido';
$_['column_ebay_item_id']    = 'ID eBay';
$_['column_front_image']     = 'Imagen';
$_['column_status']          = 'Estado';
$_['column_date_sold']       = 'Fecha Venta';
$_['column_date_added']      = 'Fecha Añadido';
$_['column_actions']         = 'Acciones';

// Buttons
$_['button_upload']          = 'Subir CSV';
$_['button_save_to_db']      = 'Guardar en BD';
$_['button_delete_selected'] = 'Eliminar Selección';
$_['button_truncate']        = 'Limpiar Todo';
$_['button_cancel']          = 'Cancelar';
$_['button_yes']             = 'Sí';
$_['button_no']              = 'No';
$_['button_ok']              = 'OK';
$_['button_close']           = 'Cerrar';
$_['button_filter']          = 'Filtrar';
$_['button_reset_filter']    = 'Restablecer';
$_['button_remove']          = 'Eliminar fila';

// Filters
$_['text_filter_title']               = 'Título';
$_['text_filter_category']            = 'Categoría';
$_['text_filter_year']                = 'Año';
$_['text_filter_brand']               = 'Marca';
$_['text_filter_set']                 = 'Set';
$_['text_filter_player']              = 'Jugador';
$_['text_filter_card_number']         = 'No. Carta';
$_['text_filter_grader']              = 'Gradificador';
$_['text_filter_min_price']           = 'Precio Mín.';
$_['text_filter_max_price']           = 'Precio Máx.';
$_['text_filter_missing_card_number'] = 'Sin No. Carta';
$_['text_limit']                      = 'Por página';

// Errors
$_['error_permission']       = 'Advertencia: ¡No tiene permiso para realizar esta acción!';
$_['error_no_file']          = 'No se subió ningún archivo.';
$_['error_invalid_file']     = 'Formato de archivo inválido. Por favor suba un archivo CSV.';
$_['error_empty_file']       = 'El archivo CSV está vacío o tiene un formato inválido.';
$_['error_no_data']          = 'No hay registros seleccionados.';
$_['error_ajax']             = 'Ocurrió un error AJAX.';
$_['text_use_filters']       = 'Utilice los filtros para mostrar ventas con tarjetas en base de datos.';
$_['text_bid_singular']      = 'puja';
$_['text_bid_plural']        = 'pujas';
$_['column_ungraded']        = 'Sin gradar';
