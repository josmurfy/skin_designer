<?php
// Heading
$_['heading_title']    = 'Importador Masivo de Cartas';

// Text
$_['text_home']        = 'Inicio';
$_['text_upload_instructions'] = 'Suba un archivo CSV para crear listados multi-variación de eBay';
$_['text_csv_file']    = 'Archivo CSV';
$_['text_csv_format']  = 'Columnas requeridas: title, sale_price. Opcional: year, brand, condition, front_image, back_image';
$_['text_preview_title'] = 'Vista Previa y Edición de Tarjetas';
$_['text_listing_configuration'] = 'Configuración del Listado';
$_['text_listing_type'] = 'Tipo de Listado';
$_['text_multi_variation'] = 'Multi-variación (todas las tarjetas en un listado)';
$_['text_single_listings'] = 'Listados individuales (una tarjeta por listado)';
$_['text_upload_success'] = '¡CSV subido con éxito!';
$_['text_generate_success'] = '¡Archivo CSV de eBay generado con éxito!';
$_['text_generation_complete'] = 'Generación Completa';
$_['text_ebay_file_ready'] = '¡Su archivo CSV de eBay está listo para descargar!';
$_['text_uploading']   = 'Subiendo';
$_['text_upload_modal_title'] = 'Subiendo su CSV';
$_['text_upload_modal_subtitle'] = 'Preparando la vista previa y verificando sus cartas.';
$_['text_upload_modal_hint'] = 'Esto puede tardar unos segundos con archivos más grandes.';
$_['text_grading_potential_detected'] = 'Potencial de grading detectado.';
$_['text_grading_listing_menu_hint'] = 'Las opciones del menú de listado ya están disponibles para esta importación.';
$_['text_grading_group_badge'] = 'Potencial grading';
$_['text_generating']  = 'Generando';
$_['text_saving']      = 'Guardando...';
$_['text_error']       = 'Error';
$_['text_brand_mismatch_block_save'] = 'Corrija los conflictos de marca en la vista previa antes de guardar en la base de datos.';
$_['text_brand_title_mismatch_block_save'] = 'Conflicto marca/título detectado: %s. La marca debe estar presente en cada título de carta.';

// Preview
$_['text_card_title']  = 'Título de la Tarjeta';
$_['text_price']       = 'Precio';
$_['text_condition']   = 'Condición';
$_['text_year_brand']  = 'Año / Marca';
$_['text_front_image'] = 'Imagen Frontal';
$_['text_back_image']  = 'Imagen Trasera';
$_['text_group_title'] = 'Título del grupo';
$_['text_cards_in_group'] = 'Cartas en el grupo';

// Statistics
$_['text_total_cards']   = 'Total de cartas';
$_['text_groups_count']  = 'Listados agrupados';
$_['text_with_images']   = 'Con Imágenes';
$_['text_without_images'] = 'Sin Imágenes';
$_['text_price_range']   = 'Rango de Precio';

// Entry
$_['entry_listing_title'] = 'Título del Listado';
$_['entry_category']      = 'ID de Categoría eBay';
$_['entry_condition']     = 'Condición';
$_['entry_shipping_price'] = 'Precio de Envío';
$_['entry_handling_time'] = 'Tiempo de Manejo';

// Column
$_['column_row']        = '#';
$_['column_card_title'] = 'Título de la Tarjeta';
$_['column_price']      = 'Precio';
$_['column_condition']  = 'Condición';
$_['column_year']       = 'Año';
$_['column_brand']      = 'Marca';
$_['column_images']     = 'Imágenes';

// Button
$_['button_upload']    = 'Subir CSV';
$_['button_generate']  = 'Generar CSV eBay';
$_['button_download']  = 'Descargar Archivo eBay';
$_['button_cancel']    = 'Cancelar';

// Help
$_['help_listing_type']  = 'Multi-variación: Todas las tarjetas en un listado con selector desplegable. Individual: Cada tarjeta se convierte en un listado separado.';
$_['help_listing_title'] = 'Título del listado de eBay (máx 80 caracteres). Solo se usa para listados multi-variación.';
$_['help_category']      = 'ID de categoría de eBay (ej: 261328 para Sports Trading Cards)';

// Placeholder
$_['text_placeholder_title']       = 'Título de la tarjeta (requerido)';
$_['text_placeholder_price']       = '9.99';
$_['text_placeholder_condition']   = 'Near Mint or Better';
$_['text_placeholder_year']        = 'Año';
$_['text_placeholder_brand']       = 'Marca/Fabricante';
$_['text_placeholder_front_image'] = 'https://...front.jpg';
$_['text_placeholder_back_image']  = 'https://...back.jpg';
$_['text_placeholder_listing_title'] = 'Tarjetas Deportivas - Múltiples Tarjetas Disponibles';

// Confirm
$_['text_confirm_cancel'] = '¿Está seguro de que desea cancelar? Todos los cambios no guardados se perderán.';

// Error
$_['error_no_file']          = 'No se subió ningún archivo';
$_['error_invalid_file']     = 'Formato de archivo no válido. Por favor suba un archivo CSV.';
$_['error_empty_file']       = 'El archivo CSV está vacío o tiene un formato no válido';
$_['error_no_data']          = 'No hay datos de tarjetas disponibles';
$_['error_generation_failed'] = 'Error al generar el archivo CSV de eBay';
$_['error_permission']       = '¡Advertencia: No tiene permiso para realizar esta acción!';
$_['error_ajax']             = 'Error AJAX ocurrido';

// Brand/Manufacturer validation
$_['text_brand_not_found']   = 'Marca no encontrada';
$_['text_brand_not_found_message'] = 'La marca "%s" no existe en la base de datos.<br>¿Desea agregarla?';
$_['button_add_brand']       = 'Agregar marca';
$_['button_cancel_brand']    = 'Cancelar';
$_['text_brand_added']       = '¡La marca "%s" se ha agregado exitosamente!';
$_['text_brand_validating']  = 'Validando marca...';
$_['error_brand_failed']     = 'Error al agregar la marca. Por favor, inténtelo de nuevo.';

// Import Results Modal
$_['text_import_results']    = 'Resultados de importación';
$_['text_import_summary']    = '¡Archivo CSV importado exitosamente! Aquí hay un resumen de sus datos:';
// Modal Dialogs
$_['text_success']           = 'Éxito';
$_['text_view_listing']      = '¿Ver el listado guardado?';
$_['text_view_all_listings'] = '¿Ver todos los listados?';
$_['text_upload_error']      = 'Error de carga';
$_['text_save_error']        = 'Error al guardar';
$_['text_save_success']      = '¡Guardado exitosamente en la base de datos!';
$_['text_save_success_reload'] = 'Todo está listo. Haga clic en OK para iniciar una nueva importación.';
$_['text_no_data_error']     = 'No hay datos de tarjetas disponibles. Por favor, cargue primero un archivo CSV.';
$_['button_yes']             = 'Sí';
$_['button_no']              = 'No';
$_['button_ok']              = 'OK';$_['button_close']           = 'Cerrar';

// Zona de carga & aviso de auto-agrupamiento
$_['text_drop_here']          = 'Haga clic o arrastre y suelte su CSV aquí';
$_['text_auto_grouped']       = 'Anuncios auto-agrupados';
$_['text_auto_grouped_desc']  = 'Las tarjetas se organizan automáticamente por SET con clasificación inteligente. Las tarjetas idénticas se combinan con la cantidad.';

// Sección Políticas eBay
$_['text_ebay_policies']      = 'Políticas comerciales de eBay';
$_['text_configured_auto']    = 'Configurado automáticamente';

// Modal confirmación guardar
$_['text_save_confirm_title'] = '¿Guardar listados en la base de datos?';
$_['text_save_confirm_desc']  = 'Esto creará listados multi-variación en su base de datos.';
$_['text_ebay_disabled']      = 'La publicación en eBay está actualmente deshabilitada para depuración.';
$_['button_confirm_save']     = 'Confirmar guardado';
$_['button_save_to_db']       = 'Guardar en BD';

// Tabla de vista previa
$_['text_already_exists']         = 'YA EXISTE';
$_['text_placeholder_location']   = 'ubicación...';
$_['text_total_prefix']           = 'Total de';
$_['text_cards']                  = 'tarjetas';
$_['text_unique']                 = 'único';
$_['text_ebay_title_label']       = 'Título eBay';
$_['column_qty']                  = 'Ctd';
$_['button_remove_line']            = 'Eliminar línea';
$_['button_remove_listing']         = 'Eliminar listado';
$_['text_remove_card_line_confirm'] = '¿Eliminar esta línea de carta de la vista previa?';
$_['text_remove_listing_confirm']   = '¿Eliminar este listado completo de la vista previa?';
$_['text_remaining_listings']         = 'Listados restantes';
$_['text_remaining_cards']            = 'Cartas restantes';
$_['button_fetch_market_prices']        = 'Verificar precios eBay';
$_['text_market_fetch_progress_done']   = 'precios actualizados';
$_['text_market_column_auction']        = 'Subasta';
$_['text_market_column_buy_now']        = 'Compra inmediata';
$_['text_market_url_missing']          = 'URL no configurada.';
$_['text_market_no_rows']              = 'No hay cartas en la vista previa.';
$_['text_market_checking']             = 'Verificando precios de eBay...';
$_['text_market_api_limit_reached']     = 'Límite de API de eBay alcanzado. Se detienen las consultas y se mantienen los precios actuales.';
$_['text_market_fallback_kept']         = 'Se conservan los precios actuales de las cartas como respaldo.';
$_['text_market_manual_raw']            = 'Sin graduar';
$_['text_market_manual_graded']         = 'Graduada';
$_['text_market_manual_sold_graded']    = 'Vendidas graduadas';
$_['text_market_apply_raw_buy_now']       = 'Aplicar precio Compra inmediata sin graduar';
