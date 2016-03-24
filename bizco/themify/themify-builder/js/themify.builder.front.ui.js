;(function($, window, document, undefined) {
	// Serialize Object Function
	if ( 'undefined' === typeof $.fn.serializeObject ) {
		$.fn.serializeObject = function() {
			var o = {};
			var a = this.serializeArray();
			$.each(a, function() {
				if (o[this.name] !== undefined) {
					if (!o[this.name].push) {
						o[this.name] = [o[this.name]];
					}
					o[this.name].push(this.value || '');
				} else {
					o[this.name] = this.value || '';
				}
			});
			return o;
		};
	}

	// Builder Function
	var ThemifyPageBuilder = {
		init: function() {
			this.tfb_hidden_editor_object = tinyMCEPreInit.mceInit['tfb_lb_hidden_editor'];
			this.alertLoader = $('<div/>', {id: 'themify_builder_alert', class: 'themify-builder-alert'});
			this.builder_content_selector = '#themify_builder_content-' + themifyBuilder.post_ID;

			// define step rule
			this.rule_cols_4 = ['[4-1][4-1][4-1][4-1]', '[4-2][4-1][4-1]', '[4-3][4-1]', '[4-1][4-3]', '[4-1][4-2][4-1]', '[4-1][4-1][4-2]'];
			this.rule_cols_full = ['[col-full]'];
			this.rule_cols_3 = ['[3-1][3-1][3-1]', '[3-2][3-1]', '[3-1][3-2]'];
			this.rule_cols_2 = ['[4-2][4-2]'];

			// cols width
			this.col4_w = 0;
			this.col3_w = 0;
			this.col2_w = 0;

			// status
			this.editing = false;

			this.bindEvents();
			this.setupLightbox();
			this.mediaUploader();
			this.openGallery();
			this.saveByKeyInput();
		},

		bindEvents: function() {
			var self = ThemifyPageBuilder, $body = $('body'), $tfBuilderAFirst = $('.toggle_tf_builder a:first'), resizeId;

			/* rows */
			$body.on('click', '.themify_builder_row_panel .add_new a', this.addRow)
			.on('click', '.toggle_row', this.toggleRow)
			.on('click', '.themify_builder_option_row', this.optionRow)

			.on('click', '.themify_builder_content .themify_builder_delete_row', this.deleteRowBuilder)
			.on('click', '.themify_builder_content .themify_builder_duplicate_row', this.duplicateRowBuilder)
			.on('click', '#tfb_module_settings .themify_builder_delete_row', this.deleteRow)
			.on('click', '#tfb_module_settings .themify_builder_duplicate_row', this.duplicateRow)

			.on('hover', '.themify_builder_row .row_menu', this.MenuHover)
			.on('hover', '.themify_builder_module_front', this.ModHover);
			$('.themify_builder_row_panel').on('hover', '.module_menu, .module_menu .themify_builder_dropdown', this.MenuHover);

			/* module */
			$body.on('click', '.themify_module_options', this.optionsModule)
			.on('dblclick', '.active_module', this.dblOptionModule)
			.on('click', '.themify_module_duplicate', this.duplicateModule)
			.on('click', '.themify_module_delete', this.deleteModule)
			.on('click', '.add_module', this.addModule)

			/* clear styling */
			.on('click', '.reset-module-styling', this.resetModuleStyling)

			/* panel */
			.on('click', '.themify-builder-front-save', this.panelSave)
			.on('click', '.themify-builder-front-close', this.panelClose);

			// add support click mobile device
			if(this.is_touch_device()) {
				$body.on('touchstart', '.themify_module_options', this.optionsModule)
				.on('touchstart', '.themify_module_duplicate', this.duplicateModule)
				.on('touchstart', '.themify_module_delete', this.deleteModule);
			}

			/* lightbox */
			$body.on('click', '.close_lightbox, #themify_builder_overlay', this.closeLightBox)
			.on('click', '.tf-option-checkbox-js', this.clickCheckBoxOption)
			.on('click', '#tfb_module_settings input[type="text"], #tfb_module_settings textarea', function(){
				$(this).focus();
			});

			/* save module option */
			$body.on('submit', '#tfb_module_settings', this.moduleSave)
			.on('click', '#tfb_module_settings .add_new a', this.moduleOptAddRow)

			/* save row option */
			.on('submit', '#tfb_row_settings', this.rowSaving);

			$(window).load(function(){
				if ( typeof switchEditors !== 'undefined' && typeof tinyMCE !== 'undefined' ) {
					//make sure the hidden WordPress Editor is in Visual mode
					switchEditors.go('tfb_lb_hidden_editor','tmce');
				}
			});

			// module events
			$(window).resize(function() {
				clearTimeout(resizeId);
				resizeId = setTimeout(function(){
					self.moduleEvents();
				}, 500);
			});

			// add loader to body
			self.alertLoader.appendTo('body').hide();

			// equal height
			self.equalHeight();

			// layout icon selected
			$body.on('click', '.tfl-icon', function(e){
				$(this).addClass('selected').siblings().removeClass('selected');
				e.preventDefault();
			});

			// Front builder
			$('#wp-admin-bar-themify_builder .ab-item:first').on('click', function(e) {
				e.preventDefault();
			});
			$tfBuilderAFirst.on('click', this.toggleFrontEdit);
			$('.themify_builder_dup_link a').on('click', this.duplicatePage);
			$('.slide_builder_module_panel').on('click', this.slidePanel);

			// Grab hash url #builder_active then activate frontend edit
			if( window.location.hash === "#builder_active" ) {
				$tfBuilderAFirst.trigger('click');
			}

			if(this.is_touch_device()){
				$body.addClass('touch');
				$body.on('touchstart', '.themify_builder_module_front .module', function(e){
					$(self.builder_content_selector + ' .themify_builder_module_front').removeClass('tap');
					$(this).parent().addClass('tap');
				}).on('touchend', '.themify_builder_module_front_overlay', function(e){
					$(this).parent().removeClass('tap');
				});
			}

			// lightbox form fields
			$body.on('change', '#tfb_module_settings .query_category_single', function(){
				$(this).parent().find('.query_category_multiple').val($(this).val());
			});

			// Import links
			$('.themify_builder_import_page > a').on('click', this.builderImportPage);
			$('.themify_builder_import_post > a').on('click', this.builderImportPost);
			$('.themify_builder_import_file > a').on('click', this.builderImportFile);
			$body.on('submit', '#themify_builder_import_form', this.builderImportSubmit)

			// Styling
			.on('click', '.themify_builder_options_tab li', function(e){
				e.preventDefault();
				var activeTab = $(this).find('a').attr('href');
				$(this).addClass('current').siblings().removeClass('current');
				$(activeTab).show().siblings('.themify_builder_options_tab_content').hide();
			}).on('editing_module_option', function(){
				$('.themify_builder_options_tab_content').hide().first().show();
				$('ul.themify_builder_options_tab li:first').addClass('current');
			});

			// Module actions
			self.moduleActions();
		},

		checkUnload: function(){
			/* unload event */
			if($('body').hasClass('themify_builder_active')) {
				window.onbeforeunload = function() {
				  return themifyBuilder.confirm_on_unload;
				}
			}
		},

		saveByKeyInput: function() {
			// key event save
			$(document).on('keydown', function(event){
				if (83 == event.which && (true == event.ctrlKey || true == event.metaKey)) {
					event.preventDefault();
					var $moduleSettings = $('#tfb_module_settings'),
						$rowSetting = $('#tfb_row_settings'),
						$panelSave = $('.themify_builder_active').find('.themify-builder-front-save');
					if($moduleSettings.length > 0){
						$moduleSettings.trigger('submit');
					} else if($rowSetting.length > 0){
						$rowSetting.trigger('submit');
					} else if($panelSave.length > 0){
						$panelSave.trigger('click');
					}
				}
			});
		},

		setColorPicker: function() {
			$('.builderColorSelect').each(function(){
				var input = $(this).parent().parent().find('.builderColorSelectInput'),
						set_color = (input.val() == '') ? '#ffffff' : input.val(),
						minicolors = $(this);
				
				minicolors.minicolors({
					defaultValue: set_color,
					textfield: false,
					change: function(hex, opacity) {
						// Generate text to show in console
						var text = hex ? hex : '';
						this.parent().parent().find('.builderColorSelectInput').val(text.replace('#', ''));
					}
				});
			});

			$('body').on('blur', '.builderColorSelectInput', function(){
				var temp_color = ($(this).val() == '') ? '#ffffff' : '#' + $(this).val();
				$(this).parent().find(".builderColorSelect").minicolors('value', temp_color);
			});
		},

		moduleEvents: function() {
			var self = ThemifyPageBuilder, gridOpt;

			$('.row_menu .themify_builder_dropdown, .module_menu .themify_builder_dropdown').hide();
			$('.themify_module_holder').each(function(){
				if($(this).find('.themify_builder_module_front').length > 0) {
					$(this).find('.empty_holder_text').hide();
				}else{
					$(this).find('.empty_holder_text').show();
				}
			});

			$( ".themify_builder_module_panel .themify_builder_module" ).draggable({
				appendTo: "body",
				helper: "clone",
				revert: 'invalid',
				zIndex: 20000,
				connectToSortable: ".themify_module_holder"
			});
			$( ".themify_module_holder" ).sortable({
				placeholder: 'themify_builder_ui_state_highlight',
				items: '.themify_builder_module_front',
				connectWith: '.themify_module_holder',
				cursor: 'move',
				revert: 100,
				handle: '.themify_builder_module_front_overlay',
				sort: function( event, ui ){
					var placeholder_h = 35;
					$('.themify_module_holder .themify_builder_ui_state_highlight').height(placeholder_h);
				},
				receive: function( event, ui ){
					self.PlaceHoldDragger();
					$( this ).parent().find( '.empty_holder_text' ).hide();
				},
				stop: function(event, ui) {
					var parent = ui.item.parent();

					if(!ui.item.hasClass('active_module')){
						var module_name = ui.item.data('module-name');
						var _this = this;
						$( this ).parent().find( ".empty_holder_text" ).hide();
						ui.item.addClass('active_module').find('.add_module').hide();
						$.ajax({
							type: "POST",
							url: themifyBuilder.ajaxurl,
							data:
							{
								action : 'tfb_add_element',
								tfb_load_nonce : themifyBuilder.tfb_load_nonce,
								tfb_template_name : 'module_front',
								tfb_module_name : module_name
							},
							success: function( data ){
								var $newElems = $(data);
								ui.item.replaceWith($newElems);
								self.moduleEvents();
								$newElems.find('.themify_builder_module_front_overlay').show();
								$newElems.find('.themify_module_options').trigger('click');
								$newElems.find('.module').hide();
							}
						});
					} else{
						self.newRowAvailable();
						self.moduleEvents();
					}
					self.equalHeight();
					self.editing = true;
				}
			}).disableSelection();

			$('.themify_builder_content').sortable({
				items: '.themify_builder_row',
				handle: '.themify_builder_row_top',
				axis: 'y',
				placeholder: 'themify_builder_ui_state_highlight',
				sort: function( event, ui ){
					var placeholder_h = ui.item.height();
					$('.themify_builder_ui_state_highlight').height(35);
				},
				stop: function( event, ui ){
					self.editing = true;
				}
			}).disableSelection();
			
			$( '.themify_builder_col' ).resizable({
				grid: 221,
				distance: 30,
				maxWidth: $('.themify_builder_row').width(),
				handles: {'e' : '.col_dragger', 'w':'.col_dragger'},
				helper: 'ui-resizable-helper',
				start: function( event, ui ){
					self.col4_w = 0;
					self.col3_w = 0;
					self.col2_w = 0;

					// col width detection
					var col_i = 0,
						$builderContainer = ui.element.closest('.themify_builder_content'),
						$colDetection4 = $builderContainer.find('.themify_builder_col_detection_4'),
						$colDetection3 = $builderContainer.find('.themify_builder_col_detection_3');
					$colDetection4.show();
					$builderContainer.find('.themify_builder_col_detection_4 .themify_builder_row_content').children().each(function(){

						if(col_i < 1){
							self.col4_w += $(this).width();
						}

						if(col_i < 2){
							self.col2_w += $(this).width();
						}

						col_i += 1;
					});
					$colDetection4.hide();

					var col_ii = 0;
					$colDetection3.show();
					$builderContainer.find('.themify_builder_col_detection_3 .themify_builder_row_content').children().each(function(){

						if( col_ii < 1 && $(this).hasClass('col3-1') ) {
							self.col3_w += $(this).width();
						}

						col_ii += 1;
					});
					$colDetection3.hide();

				},
				resize: function( event, ui ){
					var parent = ui.element.parent(),
						gridFormat = self.detectGridFormat(parent),
						gridString = gridFormat.join('');

					// snap control
					if($.inArray(gridString, self.rule_cols_full) > -1){
						gridOpt = self.col4_w;
					}
					else if($.inArray(gridString, self.rule_cols_2) > -1){
						gridOpt = self.col4_w;
					}
					else if($.inArray(gridString, self.rule_cols_3) > -1){
						gridOpt = self.col3_w;
					}
					else{
						gridOpt = self.col4_w;
					}

					// 3 column grid indicator
					if(self.getDirection(ui) == 'right'){
						if( gridString == '[4-1][4-2][4-1]' && ui.originalSize.width < self.col3_w ) {
							var s_temp = ui.originalSize.width + (self.col3_w - ui.originalSize.width + 30);
							gridOpt = s_temp > ui.size.width ? self.col3_w - ui.originalSize.width : self.col3_w + 30;
						}
					}
					if(self.getDirection(ui) == 'left'){
						if( gridString == '[4-2][4-1][4-1]' && ui.originalSize.width > self.col3_w ) {
							var s_temp = ui.originalSize.width - (ui.originalSize.width - self.col3_w - 30);
							gridOpt = s_temp < ui.size.width ? ui.originalSize.width - self.col3_w - 30 : self.col2_w;
						}
					}

					$(this).resizable( 'option', 'grid', gridOpt );

					// restrict border
					if(self.getDirection(ui) == 'right'){
						var cont = 0;
						ui.element.nextAll().addClass('next-all');
						parent.find('.next-all').each(function(){
							cont += $(this).width();
						});

						var containtment = (cont + ui.originalSize.width) + 30; 
						$(this).resizable( 'option', 'maxWidth', containtment);
						parent.children().removeClass('next-all');

						if(ui.element.hasClass('last')){
							$(this).resizable( 'option', 'grid', 9999 ); // prevent border on right
						}

					}
					
				},
				stop: function( event, ui ){

					// determine resize deltas
					var self = ThemifyPageBuilder,
					delta_x = ui.element.width() - ui.originalSize.width,
					parent = ui.element.parent(),
					elem_w = parseInt(ui.element.width() / parent.width() * 100),
					temp_class,
					dir = '',
					gridFormat = self.detectGridFormat(parent),
					gridString = gridFormat.join('');

					if (delta_x > 0) { 
						dir += 'right';
					} else if (delta_x < 0) { 
						dir += 'left';
					}

					// define class
					if( ui.element.hasClass('col3-1') || ui.element.hasClass('col3-2') ){
						if(elem_w <= 33 + 5){
							temp_class = 'col3-1';
						}
						else if(elem_w <= 66 + 5){
							temp_class = 'col3-2';
						}
						else if(elem_w <= 100 + 5){
							temp_class = 'col-full';
						}
					}
					else{
						if(elem_w <= 25 + 5) {
							temp_class = 'col4-1';
						}
						else if(elem_w <= 50 + 5) {
							temp_class = 'col4-2';
						}
						else if(elem_w <= 75 + 5){
							temp_class = 'col4-3';
						}
						else if(elem_w >= 80 + 5){
							temp_class = 'col-full';
						}  
					}

					if( dir == 'right' ){

						// cols 2 grid
						if($.inArray(gridFormat.join(''), self.rule_cols_2) > -1){
							var new_w = ui.originalSize.width + ui.element.next().width();
								
							if( new_w < ui.element.width() + 5 ){
								self.moveActiveModule(ui.element.next(), ui.element, 'next');
								ui.element.next().remove();
							}
							else{
								 parent.children()
								.removeClass('col4-1 col4-2 col4-3 col3-1 col3-2 col2-1 col-full')
								.addClass('col4-1');
								temp_class = 'col4-3';
							}
						}

						// rule 3 columns move to right
						else if($.inArray(gridString, self.rule_cols_3) > -1){

							if( gridString == '[3-1][3-2]' && ui.element.index() == 0 ){
								var new_w = ui.originalSize.width + ui.element.next().width();
								
								if( new_w == ui.element.width() ){
									self.moveActiveModule(ui.element.next(), ui.element, 'next');
									ui.element.next().remove();
								}
								else{
									 parent.children()
									.removeClass('col4-1 col4-2 col4-3 col3-1 col3-2 col2-1 col-full')
									.addClass('col4-2');
									temp_class = 'col4-2';
								}

							}
							else{
								var iterate = parseInt((ui.element.width() - ui.originalSize.width) / self.col3_w);
								var w3 = 0;

								for (i = 0; i < iterate; i++) {
									self.moveActiveModule(ui.element.next(), ui.element, 'next');
									ui.element.next().remove();
								};

								parent.children().each(function(){
									w3 += $(this).width();
								});

								// add new col
								var it2 = parseInt( (parent.width() - w3) / self.col3_w );
								for (i=0; i < it2; i++){
									self.addCols(ui, 'col3-1');
								}
							}

						}

						// rule 4 columns move to right
						else if($.inArray(gridString, self.rule_cols_4) > -1){

							if( (gridString == '[4-1][4-2][4-1]' && ui.element.index() == 0) || ( gridString == '[4-1][4-1][4-2]' ) && temp_class != 'col-full' ){
								
								if( (ui.element.width() + 5) >= parent.width() ){
									var iterate = parseInt((ui.element.width() - ui.originalSize.width) / self.col4_w);
									for (i = 0; i < iterate; i++) {
										self.moveActiveModule(ui.element.next(), ui.element, 'next');
										ui.element.next().remove();
									};
								}
								else{
									parent.children()
									.removeClass('col4-1 col4-2 col4-3 col3-1 col3-2 col2-1 col-full')
									.addClass('col3-1');
									temp_class = 'col3-1';
								}
							}
							else if( (gridString == '[4-1][4-3]' && ui.element.index() == 0 && temp_class != 'col-full') ){
								var con_w = ui.originalSize.width + ui.element.next().width();
								var new_w = con_w - ui.element.width();

								ui.element.next()
								.removeClass('col4-1 col4-2 col4-3 col3-1 col3-2 col2-1 col-full')
								.addClass(self.returnGridWidth(new_w, ui, 'col4'));

							}
							else{
								var iterate = parseInt((ui.element.width() - ui.originalSize.width) / self.col4_w);
								var w3 = 0;
								
								for (i = 0; i < iterate; i++) {
									self.moveActiveModule(ui.element.next(), ui.element, 'next');
									ui.element.next().remove();
								}

								parent.children().each(function(){
									w3 += $(this).width();
								});

								// add new col
								var it2 = parseInt( (parent.width() - w3) / self.col4_w );
								for (i=0; i < it2; i++){
									self.addCols(ui, 'col4-1');
								}
							}
							
						}


					} // dir right

					if( dir == 'left' ){
						
						// cols full
						if($.inArray(gridString, self.rule_cols_full) > -1){
							var iterate = parseInt((ui.originalSize.width - ui.element.width()) / self.col4_w);
							if(iterate > 3) { iterate = 3;}
							for (i = 0; i < iterate; i++) {
								self.addCols(ui, 'col4-1');
							}
						}

						// cols 2
						else if($.inArray(gridFormat.join(''), self.rule_cols_2) > -1){
							
							if( ui.element.hasClass('last') ){
								parent.children()
								.removeClass('col4-1 col4-2 col4-3 col3-1 col3-2 col2-1 col-full')
								.addClass('col3-1');
								temp_class = 'col3-1';
								
								if( parent.children().length == 2){
									self.addCols(ui, 'col3-1');
								}  
							}
							else {

								var new_w = ui.element.width();
								if( new_w > 0){
									ui.element.next()
									.removeClass('col4-1 col4-2 col4-3 col3-1 col3-2 col2-1 col-full')
									.addClass('col4-3');
									temp_class = 'col4-1';
								}
								else{
									ui.element.next()
									.removeClass('col4-1 col4-2 col4-3 col3-1 col3-2 col2-1 col-full')
									.addClass('col-full');
									self.moveActiveModule(ui, ui.element.next(), 'prev');
									ui.element.remove();
								}
								
							}
							
						}

						// cols 3
						else if($.inArray(gridString, self.rule_cols_3) > -1){
							
							if( (gridString == '[3-2][3-1]' && ui.element.hasClass('last')) || (gridString == '[3-1][3-2]' && ui.element.hasClass('last')) ){
								parent.children()
								.removeClass('col4-1 col4-2 col4-3 col3-1 col3-2 col2-1 col-full')
								.addClass('col3-1');
								
								self.addCols(ui, 'col3-1');
							}
							else if(gridString == '[3-1][3-1][3-1]' && ui.element.hasClass('last')){
								parent.children()
								.removeClass('col4-1 col4-2 col4-3 col3-1 col3-2 col2-1 col-full')
								.addClass('col4-1');
								temp_class = 'col4-1';
								
								self.addCols(ui, 'col4-1');
							}
							else if(ui.element.hasClass('col3-1')){
								var new_w = ui.originalSize.width + ui.element.next().width();
								ui.element.next()
								.removeClass('col4-1 col4-2 col4-3 col3-1 col3-2 col2-1 col-full')
								.addClass(self.returnGridWidth(new_w, ui, 'col3'));

								self.moveActiveModule(ui, ui.element.next(), 'prev');
								ui.element.remove();
							}
							else{
								var new_w = ui.element.width();
								
								if( new_w > 0){
									ui.element.next()
									.removeClass('col4-1 col4-2 col4-3 col3-1 col3-2 col2-1 col-full')
									.addClass('col4-2');
									temp_class = 'col4-2';
								}
								else{
									ui.element.next()
									.removeClass('col4-1 col4-2 col4-3 col3-1 col3-2 col2-1 col-full')
									.addClass('col-full');
									self.moveActiveModule(ui, ui.element.next(), 'prev');
									ui.element.remove();
								}

							}

						}

						// cols 4
						else if($.inArray(gridString, self.rule_cols_4) > -1){

							if( (gridString == '[4-2][4-1][4-1]' && ui.element.index() == 0) || ( gridString == '[4-1][4-2][4-1]' && ui.element.index() == 1 ) ){
								if( ui.element.width() > 1){
									parent.children()
									.removeClass('col4-1 col4-2 col4-3 col3-1 col3-2 col2-1 col-full')
									.addClass('col3-1');
									temp_class = 'col3-1';
								}
								else{
									ui.element.next()
									.removeClass('col4-1 col4-2 col4-3 col3-1 col3-2 col2-1 col-full')
									.addClass('col4-3');
									self.moveActiveModule(ui, ui.element.next(), 'prev');
									ui.element.remove();
								}
							}
							else if( (ui.element.hasClass('col4-3') && ui.element.index() == 0 ) || ( ui.element.hasClass('col4-2') && !ui.element.hasClass('last') ) ){
								var con_w = ui.originalSize.width + ui.element.next().width();
								var new_w = con_w - ui.element.width();

								ui.element.next()
									.removeClass('col4-1 col4-2 col4-3 col3-1 col3-2 col2-1 col-full')
									.addClass(self.returnGridWidth(new_w, ui, 'col4'));

								if( ui.element.width() <= 1 ){
									self.moveActiveModule(ui, ui.element.next(), 'prev');
									ui.element.remove(); 
								}
								
							}
							else if( (gridString == '[4-3][4-1]' && ui.element.hasClass('last')) || (gridString == '[4-1][4-2][4-1]' && ui.element.hasClass('last')) ){
								var new_w = ui.element.prev().width() - ui.originalSize.width;
								ui.element.prev()
								.removeClass('col4-1 col4-2 col4-3 col3-1 col3-2 col2-1 col-full')
								.addClass(self.returnGridWidth(new_w, ui, 'col4'));

								self.addCols(ui, 'col4-1');
							}
							else if( gridString == '[4-2][4-1][4-1]' && ui.element.hasClass('last')){
								var new_w = ui.element.prev().prev().width() - ui.originalSize.width;
								ui.element.prev().prev()
								.removeClass('col4-1 col4-2 col4-3 col3-1 col3-2 col2-1 col-full')
								.addClass(self.returnGridWidth(new_w, ui, 'col4'));

								self.addCols(ui, 'col4-1');
							}
							else{
								if(ui.element.next().length >0){
									var new_w = ui.originalSize.width + ui.element.next().width();
									ui.element.next()
									.removeClass('col4-1 col4-2 col4-3 col3-1 col3-2 col2-1 col-full')
									.addClass(self.returnGridWidth(new_w, ui, 'col4'));

									self.moveActiveModule(ui, ui.element.next(), 'prev');
									ui.element.remove(); 
								}

								// add column
								if(parent.children().length < 4) {
									var iterate = parseInt((ui.originalSize.width - ui.element.width()) / self.col4_w);
									if(iterate > 3) { iterate = 3;}
									for (i = 0; i < iterate; i++) {
										self.addCols(ui, 'col4-1');
									}
								}

							}
						}

					} // dir left

					// clear class cols
					ui.element.removeClass('col4-1 col4-2 col4-3 col3-1 col3-2 col2-1 col-full');
					ui.element.addClass(temp_class);

					ui.element.css({height: '', width: '', left: ''});
					self.gridRefresh( ui );

					// equalheight
					self.equalHeight();
					self.colDraggerTitle();

					self.editing = true;
				}
			});

			self.colDraggerUpdate(); // update dragger

		},

		getDocHeight: function(){
			var D = document;
			return Math.max(
				Math.max(D.body.scrollHeight, D.documentElement.scrollHeight),
				Math.max(D.body.offsetHeight, D.documentElement.offsetHeight),
				Math.max(D.body.clientHeight, D.documentElement.clientHeight)
			);
		},

		setupLightbox: function() {
			var markup = '<div id="themify_builder_lightbox_parent" class="themify_builder builder-lightbox themify_builder_admin clearfix">' +

				'<h3 class="themify_builder_lightbox_title"></h3>' +

				'<a href="#" class="close_lightbox"><span class="themify_builder_icon delete"></span></a>' +

				'<div id="themify_builder_lightbox_container"></div>' +

			'</div>' +

			'<div id="themify_builder_overlay"></div>';

			$(markup).hide().appendTo('body');

		},

		addRow: function(e) {
			var self = ThemifyPageBuilder;
			$('#themify_builder_row_wrapper').append(themifyBuilder.newRowTemplate);
			self.moduleEvents();
			self.editing = true;
			
			e.preventDefault();
		},

		toggleRow: function(e) {
			e.preventDefault();
			$(this).parents('.themify_builder_row').toggleClass('collapsed').find('.themify_builder_row_content').slideToggle();
		},

		deleteRow: function(e) {
			e.preventDefault();
			var row_length = $(this).closest('.themify_builder_row_js_wrapper').find('.themify_builder_row:visible').length;
			if(row_length > 1) {
				$(this).closest('.themify_builder_row').remove();
			}
			else {
				$(this).closest('.themify_builder_row').hide();
			}
			self.editing = true;
		},

		deleteRowBuilder: function(e) {
			e.preventDefault();
			var row_length = $(this).closest('.themify_builder_content').find('.themify_builder_row:visible').length,
				self = ThemifyPageBuilder;

			if(row_length > 1) {
				$(this).closest('.themify_builder_row').remove();
			}
			else {
				$(this).closest('.themify_builder_row').hide();
			}
			self.editing = true;
		},

		duplicateRow: function(e) {
			e.preventDefault();

			var self = ThemifyPageBuilder,
					wrapper = $(this).parents('.themify_builder_row_js_wrapper'),
					oriElems = $(this).closest('.themify_builder_row'),
					newElems = $(this).closest('.themify_builder_row').clone(),
					row_count = $('#tfb_module_settings .themify_builder_row_js_wrapper').find('.themify_builder_row:visible').length + 1,
					number = row_count + Math.floor(Math.random() * 9);

			// fix wpeditor empty textarea
			newElems.find('.tfb_lb_wp_editor.tfb_lb_option_child').each(function(){
				var this_option_id = $(this).attr('id'), element_val;
				if ( typeof tinyMCE !== 'undefined' ) {
					element_val = $(this).is(':hidden') ? tinyMCE.get( this_option_id ).getContent() : switchEditors.wpautop( tinymce.DOM.get( this_option_id ).value );
				} else {
					element_val = $('#' + this_option_id).val();
				}
				$(this).val(element_val);
				$(this).addClass('clone');
			});

			// fix textarea field clone
			newElems.find('textarea:not(.tfb_lb_wp_editor)').each(function(i){
				var insertTo = oriElems.find('textarea').eq(i).val();
				$(this).val(insertTo);
			});

			// fix radio button clone
			newElems.find('.themify-builder-radio-dnd').each(function(i){
				var oriname = $(this).attr('name');
				$(this).attr('name', oriname + '_' + row_count);
				$(this).attr('id', oriname + '_' + row_count + '_' + i);
				$(this).next('label').attr('for', oriname + '_' + row_count + '_' + i);
			});

			newElems.find('.themify-builder-plupload-upload-uic').each(function(i){
				$(this).attr('id', 'pluploader_' + row_count + number + i + 'themify-builder-plupload-upload-ui');
				$(this).find('input[type=button]').attr('id', 'pluploader_' + row_count + number + i + 'themify-builder-plupload-browse-button');
				$(this).addClass('plupload-clone');
			});

			newElems.insertAfter(oriElems).find('.themify_builder_dropdown').hide();

			$('#tfb_module_settings').find('.tfb_lb_wp_editor.tfb_lb_option_child.clone').each(function(i){
				var element = $(this),
						element_val = element.val(),
						parent_child = element.closest('.themify_builder_input');

				$(this).closest('.wp-editor-wrap').remove();
				
				var oriname = element.attr('name');
				element.attr('id', oriname + '_' + row_count + number + '_' + i);
				element.attr('class').replace('wp-editor-area', '');

				element.appendTo(parent_child).wrap('<div class="wp-editor-wrap"/>');

			});

			self.addNewWPEditor();
			self.builderPlupload('new_elemn');
			self.moduleEvents();
			self.editing = true;
		},

		duplicateRowBuilder: function(e) {
			e.preventDefault();
			var self = ThemifyPageBuilder,
				oriElems = $(this).closest('.themify_builder_row'),
				newElems = $(this).closest('.themify_builder_row').clone();

			newElems.insertAfter(oriElems).find('.themify_builder_dropdown').hide();
			self.moduleEvents();
			self.editing = true;
		},

		MenuHover: function(e) {
			if(e.type=='mouseenter')
			{
				$(this).find('.themify_builder_dropdown').stop(false,true).show();
			}
			else if(e.type=='mouseleave')
			{
				$(this).find('.themify_builder_dropdown').stop(false,true).hide();
			}
		},

		ModHover: function(e) {
			if(e.type=='mouseenter')
			{
				$(this).find('.themify_builder_module_front_overlay').stop(false,true).show();
				$(this).find('.themify_builder_dropdown_front').stop(false,true).show();
			}
			else if(e.type=='mouseleave')
			{
				$(this).find('.themify_builder_module_front_overlay').stop(false,true).hide();
				$(this).find('.themify_builder_dropdown_front').stop(false,true).hide();
			}
		},

		optionsModule: function(e) {
			e.preventDefault();

			var self = ThemifyPageBuilder,
				module_name = $(this).data('module-name'),
				set_elems = $(this).closest('.module_menu_front').find('.front_mod_settings').data('settings'),
				is_settings_exist = set_elems.length > 2 ? true : false,
				el_settings = typeof set_elems !== 'undefined' ? set_elems : '';

			$('.module_menu .themify_builder_dropdown').hide();
			$('#themify_builder_lightbox_container').empty();
			$('#themify_builder_overlay').show();
			self.showLoader('show');

			// assigned selected module class
			$('.themify_builder_content .module_menu_front').removeClass('current_selected_module');
			var $tfb_active_module = $(this).closest('.module_menu_front');
			$tfb_active_module.addClass('current_selected_module');

			$.ajax({
				type: "POST",
				url: themifyBuilder.ajaxurl,
				data:
				{
					action : 'tfb_lightbox_options',
					tfb_load_nonce : themifyBuilder.tfb_load_nonce,
					tfb_module_name : module_name
				},
				success: function( data ){
					var top = $(document).scrollTop() + 80,
							$newElems = $(data);

					$("#themify_builder_lightbox_parent")
					.show()
					.css('top', self.getDocHeight())
					.animate({
						top: top
					}, 800 );

					self.showLoader('spinhide');
					
					$('#themify_builder_lightbox_container').html($newElems);
					$('#tfb_module_settings .tfb_lb_option').each( function(){
						var $this_option = $(this),
							this_option_id = $this_option.attr('id'),
							$check_found_element = ( typeof el_settings[this_option_id] === 'undefined' ) ? false : true,
							$found_element = el_settings[this_option_id];
						
						if ( $found_element ){
							if ( $this_option.hasClass('select_menu_field') ){
								if ( !isNaN( $found_element ) ) {
									$this_option.find("option[data-termid='" + $found_element + "']").attr('selected','selected');
								} else {
									$this_option.find("option[value='" + $found_element + "']").attr('selected','selected');
								}
							} else if ( $this_option.is('select') ){
								$this_option.find("option[value='" + $found_element + "']").attr('selected','selected').siblings().removeAttr('selected');
							} else if( $this_option.hasClass('themify-builder-uploader-input') ) {
								var img_field = $found_element,
										img_thumb = $('<img/>', {src: img_field, width: 50, height: 50});

								if( img_field != '' ){
									$this_option.val(img_field);
									$this_option.parent().find('.img-placeholder').empty().html(img_thumb);
								}
								else{
									$this_option.parent().find('.thumb_preview').hide();
								}

							} else if($this_option.hasClass('themify-option-query-cat')){
								var parent = $this_option.parent(),
										single_cat = parent.find('.query_category_single'),
										multiple_cat  = parent.find('.query_category_multiple'),
										elems = $found_element,
										value = elems.split('|'),
										cat_type = value[1],
										cat_val = value[0];

								multiple_cat.val( cat_val );
								parent.find("option[value='" + cat_val + "']").attr('selected','selected');

							} else if( $this_option.hasClass('themify_builder_row_js_wrapper') ) {
								var row_append = 0;
								if($found_element.length > 0){
									row_append = $found_element.length - 1;
								}

								// add new row
								for (var i = 0; i < row_append; i++) {
									$this_option.parent().find('.add_new a').first().trigger('click');
								}

								$this_option.find('.themify_builder_row').each(function(r){
									$(this).find('.tfb_lb_option_child').each(function(i){
										var $this_option_child = $(this),
										this_option_id_real = $this_option_child.attr('id'),
										this_option_id_child = $this_option_child.hasClass('tfb_lb_wp_editor') ? $this_option_child.attr('name') : $this_option_child.data('input-id'),
										$found_element_child = $found_element[r][''+ this_option_id_child +''];
										
										if( $this_option_child.hasClass('themify-builder-uploader-input') ) {
											var img_field = $found_element_child,
													img_thumb = $('<img/>', {src: img_field, width: 50, height: 50});

											if( img_field != '' ){
												$this_option_child.val(img_field);
												$this_option_child.parent().find('.img-placeholder').empty().html(img_thumb).parent().show();
											}
											else{
												$this_option_child.parent().find('.thumb_preview').hide();
											}

										}
										else if( $this_option_child.hasClass('tf-radio-choice') ){
											$this_option_child.find("input[value='" + $found_element_child + "']").attr('checked','checked');  
										}
										else if( $this_option_child.is('input, textarea') ){
											$this_option_child.val($found_element_child);
										}

										if ( $this_option_child.hasClass('tfb_lb_wp_editor') && !$this_option_child.hasClass('clone') ) {
											self.initQuickTags(this_option_id_real);
											if ( typeof tinyMCE !== 'undefined' ) {
												self.initNewEditor( this_option_id_real );
											}
										}

									});
								});

							} else if ( $this_option.hasClass('tf-radio-input-container') ){
								$this_option.find("input[value='" + $found_element + "']").attr('checked', 'checked');  
								var selected_group = $this_option.find('input[name="'+this_option_id+'"]:checked').val();
								
								// has group element enable
								if($this_option.hasClass('tf-option-checkbox-enable')){
									$('.tf-group-element').hide();
									$('.tf-group-element-' + selected_group ).show();
								}

							} else if ( $this_option.is('input, textarea') ){
								$this_option.val( $found_element );
							} else if ( $this_option.hasClass('themify-checkbox') ){
								var cselected = $found_element;
								cselected = cselected.split('|');

								$this_option.find('.tf-checkbox').each(function(){
									if($.inArray($(this).val(), cselected) > -1){
										$(this).prop('checked', true);
									}
									else{
										$(this).prop('checked', false);
									}
								});

							} else if ( $this_option.hasClass('themify-layout-icon') ) {
									$this_option.find('#' + $found_element).addClass('selected');
							} else { 
								$this_option.html( $found_element );
							}
						}
						else{
							if ( $this_option.hasClass('themify-layout-icon') ){
								$this_option.children().first().addClass('selected');
							}
							else if ( $this_option.hasClass('themify-builder-uploader-input') ) {
								$this_option.parent().find('.thumb_preview').hide();
							}
							else if ( $this_option.hasClass('tf-radio-input-container') ) {
								$this_option.find('input[type=radio]').first().prop('checked');
								var selected_group = $this_option.find('input[name="'+this_option_id+'"]:checked').val();
								
								// has group element enable
								if($this_option.hasClass('tf-option-checkbox-enable')){
									$('.tf-group-element').hide();
									$('.tf-group-element-' + selected_group ).show();
								}
							}
							else if( $this_option.hasClass('themify_builder_row_js_wrapper') ){
								$this_option.find('.themify_builder_row').each(function(r){
									$(this).find('.tfb_lb_option_child').each(function(i){
										var $this_option_child = $(this),
										this_option_id_real = $this_option_child.attr('id');

										if ( $this_option_child.hasClass('tfb_lb_wp_editor') ) {
											
											var this_option_id_child = $this_option_child.data('input-id');

											self.initQuickTags(this_option_id_real);
											if ( typeof tinyMCE !== 'undefined' ) {
												self.initNewEditor( this_option_id_real );
											}
										}

									});
								});
							}
							else if( $this_option.hasClass('themify-checkbox') && is_settings_exist ) {
								$this_option.find('.tf-checkbox').each(function(){
									$(this).prop('checked', false);
								});
							}
							else if( $this_option.is('input, textarea') && is_settings_exist ) {
								$this_option.val('');
							}
						}

						if ( $this_option.hasClass('tfb_lb_wp_editor') ) {
							self.initQuickTags(this_option_id);
							if ( typeof tinyMCE !== 'undefined' ) {
								self.initNewEditor( this_option_id );
							}
						}
					} );

					// Trigger event
					$('body').trigger( 'editing_module_option', [ el_settings ] );
					
					// add new wp editor
					self.addNewWPEditor();

					// set touch element
					self.touchElement();

					// colorpicker
					self.setColorPicker();

					// plupload init
					self.builderPlupload('normal');

					// builder drag n drop init
					self.moduleOptionBuilder();

					$('#themify_builder_lightbox_parent').show();

				}
			});
		},

		dblOptionModule: function(e) {
			e.preventDefault();
			$(this).find('.themify_module_options').trigger('click');
		},

		duplicateModule: function(e) {
			e.preventDefault();
			var holder = $(this).closest('.themify_builder_module_front'),
					self = ThemifyPageBuilder;
			$(this).closest('.themify_builder_module_front').clone().insertAfter(holder);
			self.equalHeight();
			self.editing = true;
		},

		deleteModule: function(e) {
			e.preventDefault();

			var self = ThemifyPageBuilder,
				_this = $(this);
			
			if (confirm(themifyBuilder.moduleDeleteConfirm)) {
				self.switchPlaceholdModule(_this);
				_this.parents('.themify_builder_module_front').remove();
				self.newRowAvailable();
				self.equalHeight();
				self.moduleEvents();
				self.editing = true;
			}
		},

		addModule: function(e) {
			e.preventDefault();

			var self = ThemifyPageBuilder;
			var module_name = $(this).data('module-name');

			$.ajax({
				type: "POST",
				url: themifyBuilder.ajaxurl,
				data:
				{
					action : 'tfb_add_element',
					tfb_load_nonce : themifyBuilder.tfb_load_nonce,
					tfb_template_name : 'module_front',
					tfb_module_name : module_name
				},
				success: function( data ){
					var dest = $('.themify_builder_row:visible').first().find('.themify_module_holder').first(),
							$newElems = $(data);
							$newElems.appendTo(dest);
					
					self.moduleEvents();
					self.equalHeight();
					$newElems.find('.themify_builder_module_front_overlay').show();
					$newElems.find('.themify_module_options').trigger('click');
					$newElems.find('.module').hide();
					self.editing = true;
				}
			});
		},

		closeLightBox: function(e) {
			e.preventDefault();
			var self = ThemifyPageBuilder,
				$tfb_dialog_form = $('form#tfb_module_settings');
			
			if ( typeof tinyMCE !== 'undefined' ) {
				$tfb_dialog_form.find('.tfb_lb_wp_editor').each( function(){
					var $id = $(this).prop('id');
					switchEditors.go($id, 'tmce');
				});
			}
			
			$('#themify_builder_lightbox_parent').animate({
				top: self.getDocHeight()
			}, 800)
			.promise().done(function(){
				// Animation complete.
				$('#themify_builder_lightbox_container').empty();
				$('.themify_builder_lightbox_title').text('');
				$( "#themify_builder_lightbox_parent" ).removeAttr('style');
				self.deleteEmptyModule(); // clear empty module
				$('#themify_builder_overlay, #themify_builder_lightbox_parent').hide();
			});
		},

		initNewEditor: function(editor_id) {
			var self = ThemifyPageBuilder;
			if ( typeof tinyMCEPreInit.mceInit[editor_id] !== "undefined" ) {
				self.initMCEv4( editor_id, tinyMCEPreInit.mceInit[editor_id] );
				return;
			}
			var tfb_new_editor_object = self.tfb_hidden_editor_object;
			
			tfb_new_editor_object['elements'] = editor_id;
			tinyMCEPreInit.mceInit[editor_id] = tfb_new_editor_object;

			// v4 compatibility
			self.initMCEv4( editor_id, tinyMCEPreInit.mceInit[editor_id] );
		},

		initMCEv4: function( editor_id, $settings ){
			// v4 compatibility
			if( parseInt( tinyMCE.majorVersion) > 3 ) {
				// Creates a new editor instance
				var ed = new tinyMCE.Editor(editor_id, $settings, tinyMCE.EditorManager);	
				ed.render();
			}
		},

		initQuickTags: function(editor_id) {
			// add quicktags
			if ( typeof(QTags) == 'function' ) {
				quicktags( {id: editor_id} );
				QTags._buttonsInit();
			}
		},

		switchPlaceholdModule: function(obj) {
			var check = obj.parents('.themify_module_holder');
			if(check.find('.themify_builder_module_front').length == 1) {
				check.find('.empty_holder_text').show();
			}
		},

		PlaceHoldDragger: function(){
			$('.themify_module_holder').each(function(){
				if($(this).find('.themify_builder_module_front').length == 0){
					$(this).find('.empty_holder_text').show();
				}
			});
		},

		addCols: function(ui, cols_class){
			var self = ThemifyPageBuilder;
			var dest = ui.element.parent();
			var newElems = '<div class="themify_builder_col">' +
					'<div class="themify_module_holder">' +
						'<div class="empty_holder_text">' + themifyBuilder.dropPlaceHolder +'</div>' +
					'</div>' +
					'<div class="col_dragger ui-resizable-handle ui-resizable-e"></div>' +
				'</div>';

			var $elems = $(newElems).wrap('<div />');
			$elems.parent().find('.themify_builder_col').addClass(cols_class);
			$elems.unwrap().insertAfter(ui.element);

			// remove last element when more than 4 element
			if(dest.children().length > 4){
				dest.children().last().remove();
			}

			self.moduleEvents();
		},

		gridRefresh: function( ui ){
			var self = ThemifyPageBuilder,
				parent = ui.element.parents('.themify_builder_row_content');
			parent.children().removeClass('first last');
			parent.children().first().addClass('first');
			parent.children().last().addClass('last');
			self.colDraggerUpdate();
		},

		returnGridWidth: function( width, ui, col ){
			var temp_class,
			parent = ui.element.parent(),
			elem_w = parseInt(width / parent.width() * 100);

			if(col == 'col4'){
				if(elem_w <= 25 + 5) {
					temp_class = 'col4-1';
				}
				else if(elem_w <= 50 + 5) {
					temp_class = 'col4-2';
				}
				else if(elem_w <= 75 + 5){
					temp_class = 'col4-3';
				}
				else if(elem_w >= 80 + 5){
					temp_class = 'col-full';
				}
			}
			else if( col == 'col3'){
				if(elem_w <=33 + 5){
					temp_class = 'col3-1';
				}
				else if(elem_w <= 66 + 5){
					temp_class = 'col3-2';
				}
				else if(elem_w <= 100 + 5){
					temp_class = 'col-full';
				}
			}

			return temp_class;
		},

		moveActiveModule: function( ui, to , dir){
			if( dir == 'prev'){
				var modules = ui.element.find('.themify_module_holder').clone();
				modules.find('.empty_holder_text').remove();
				modules.children().appendTo(to.find('.themify_module_holder'));
			}

			if( dir == 'next'){
				var modules = ui.find('.themify_module_holder').clone();
				modules.find('.empty_holder_text').remove();
				modules.children().appendTo(to.find('.themify_module_holder'));
			}
		},

		equalHeight: function(){
			$('.themify_builder_row_content:visible').each(function(){
				var t=0; // the height of the highest element (after the function runs)
				$(this).find('.themify_module_holder').each(function () {
					$this = $(this);
					$this.css('min-height', '');
					if ( $this.height() > t ) {
						t=$this.height();
					}
				});

				$(this).find('.themify_module_holder').each(function() {
					$(this).css('min-height', t + 'px');
				});
			});
		},

		getDirection: function(ui) {
			var elem_w = ui.size.width - ui.originalSize.width,
					dir = '';
			if(elem_w > 0){
				dir += 'right';
			}
			else{
				dir += 'left';
			}
			return dir;
		},

		detectGridFormat: function(parent){
			var cols = [];
			parent.children().each(function(){
				if($(this).hasClass('col4-1')){
					cols.push('[4-1]');
				}
				else if($(this).hasClass('col4-2')){
					cols.push('[4-2]');
				}
				else if($(this).hasClass('col4-3')){
					cols.push('[4-3]');
				}
				else if($(this).hasClass('col3-1')){
					cols.push('[3-1]');
				}
				else if($(this).hasClass('col3-2')){
					cols.push('[3-2]');
				}
				else if($(this).hasClass('col3-3')){
					cols.push('[3-3]');
				}
				else if($(this).hasClass('col-full')){
					cols.push('[col-full]');
				}
			});
			return cols;
		},

		saveData: function(loader, callback, saveto){
			saveto = saveto || 'main';
			var self = ThemifyPageBuilder;
			
			$('.themify_builder_content').each(function(){
				var builder_id = $(this).data('postid'),
					dataSend = self.retrieveData(this);

				$.ajax({
					type: "POST",
					url: themifyBuilder.ajaxurl,
					data:
					{
						action : 'tfb_save_data',
						tfb_load_nonce : themifyBuilder.tfb_load_nonce,
						tfb_post_id : builder_id,
						post_data : dataSend,
						tfb_saveto : saveto
					},
					cache: false,
					beforeSend: function ( xhr ){
						if(loader){
							self.showLoader('show');
						}
					},
					success: function( data ){
						if(loader){
							self.showLoader('hide');
						}
						// load callback
						if( $.isFunction(callback) ){
							callback.call(this, data);
						}
					}
				});
			});

			if(saveto == 'main') {
				self.editing = false;
			}
		},

		moduleSave: function(e){
			var self = ThemifyPageBuilder,
					$active_module_settings = $('.current_selected_module .front_mod_settings'),
					parent_active_mod = $active_module_settings.parent(),
					temp_appended_data = {};
			
			$('#tfb_module_settings .tfb_lb_option').each(function(iterate){
				var option_value,
					this_option_id = $(this).attr('id');

				if ( $(this).hasClass('tfb_lb_wp_editor') ){
					if ( typeof tinyMCE !== 'undefined' ) {
						option_value = $(this).is(':hidden') ? tinyMCE.get( this_option_id ).getContent() : switchEditors.wpautop( tinymce.DOM.get( this_option_id ).value );
					} else {
						option_value = $(this).val();
					}
				}
				else if ( $(this).hasClass('themify-checkbox') ) {
					var cselected = [];
					$(this).find('.tf-checkbox:checked').each(function(i){
						cselected.push($(this).val());
					});
					if ( cselected.length > 0 ) {
						option_value = cselected.join('|');
					} else {
						option_value = '|';
					}
				}
				else if ( $(this).hasClass('themify-layout-icon') ) {
					if( $(this).find('.selected').length > 0 ){
						option_value = $(this).find('.selected').attr('id');
					}
					else{
						option_value = $(this).children().first().attr('id');
					}
				}
				else if ( $(this).hasClass('themify-option-query-cat') ) {
					var parent = $(this).parent(),
							single_cat = parent.find('.query_category_single'),
							multiple_cat  = parent.find('.query_category_multiple');

					if( multiple_cat.val() != '' ) {
						option_value = multiple_cat.val() + '|multiple';
					} else {
						option_value = single_cat.val() + '|single';
					}
				}
				else if( $(this).hasClass('themify_builder_row_js_wrapper') ){
					var row_items = [];
					$(this).find('.themify_builder_row').each(function(){
						var temp_rows = {};
						$(this).find('.tfb_lb_option_child').each(function(){
							var option_value_child,
							this_option_id_child = $(this).data('input-id');

							if( $(this).hasClass('tf-radio-choice') ){
								option_value_child = ($(this).find(':checked').length > 0) ? $(this).find(':checked').val() : '';
							}
							else if ($(this).hasClass('tfb_lb_wp_editor')){
								var text_id = $(this).attr('id');
								this_option_id_child = $(this).attr('name');
								if( typeof tinyMCE !== 'undefined' ) {
									option_value_child = $(this).is(':hidden') ? tinyMCE.get( text_id ).getContent() : switchEditors.wpautop( tinymce.DOM.get( text_id ).value );
								} else {
									option_value_child = $(this).val();
								}
							}
							else{
								option_value_child = $(this).val();
							}

							if( option_value_child ) {
								temp_rows[this_option_id_child] = option_value_child;
							}
						});
						row_items.push(temp_rows);
					});
					option_value = row_items;
				}
				else if ( $(this).hasClass('tf-radio-input-container') ) {
					option_value = $(this).find('input[name="'+this_option_id+'"]:checked').val();
				}
				else if ( $(this).hasClass('module-widget-form-container') ) {
					option_value = $(this).find(':input').serializeObject();
				}
				else if ( $(this).is('select, input, textarea') ) {
					option_value = $(this).val();
				}

				if(option_value){
					temp_appended_data[this_option_id] = option_value;
				}
			});
			
			$active_module_settings.data('settings', temp_appended_data );

			// clear empty module
			self.deleteEmptyModule();

			// Save data
			self.saveData(true, function(){
				var hilite = $('.current_selected_module').parents('.themify_builder_module_front'),
				class_hilite = self.getHighlightClass(hilite),
				hilite_obj = self.getHighlightObject(hilite),
				mod_name = hilite.data('module-name');

				$('#themify_builder_lightbox_parent').hide();
				$('.close_lightbox').trigger('click');
				hilite.wrap('<div class="temp_placeholder '+ class_hilite +'" />').find('.themify_builder_module_front_overlay').show();
				self.updateContent(class_hilite, hilite_obj);
			}, 'cache');

			self.editing = true;
			e.preventDefault();
		},

		retrieveData: function(obj){
			var self = ThemifyPageBuilder,
				option_data = {},
				cols = {},
				modules = {},
				$builder_selector = $(obj);

			// rows
			$builder_selector.find('.themify_builder_row:visible').each(function(r){
				var row_order = r,
						cols = {};

				// get module settings
				if($(this).find('.themify_builder_module_front').length > 0){
					// cols
					$(this).find('.themify_builder_col').each(function(c){
						var grid_class = self.filterClass($(this).attr('class')),
								modules = {};
						// mods
						$(this).find('.themify_builder_module_front').each(function(m){
							var mod_name = $(this).find('.themify_builder_data_mod_name').text();
							mod_elems = $(this).find('.front_mod_settings'),
							mod_settings = mod_elems.data('settings'),
							modules[m] = {'mod_name': mod_name, 'mod_settings': mod_settings};
						});

						cols[c] = {'grid_class': grid_class, 'modules': modules};

					});

					option_data[r] = {'row_order': r, 'cols': cols };
				} else {
					option_data[r] = {};
				}

				// get row styling
				if ( $(this).find('.row-data-styling').length > 0 ){
					var $data_styling = $(this).find('.row-data-styling').data('styling');
					if( 'object' === typeof $data_styling ) 
						option_data[r].styling = $data_styling;
				}

			});

			return option_data;
		},

		filterClass: function(str){
			var grid = ['col-full', 'col4-1', 'col4-2', 'col4-3', 'col3-1', 'col3-2', 'first', 'last'];
				n = str.split(' '),
				new_arr = [];

				for (var i = 0; i < n.length; i++) {
					if($.inArray(n[i], grid) > -1){
						new_arr.push(n[i]);
					}
				}

			return new_arr.join(' ');
		},

		limitString: function(str, limit){
			var new_str;

			if($(str).text().length > limit ){
				new_str = $(str).text().substr(0, limit);
			}
			else{
				new_str = $(str).text();
			}

			return new_str;
		},

		mediaUploader: function() {
			
			// Uploading files
			var $body = $('body'); // Set this
			
			// Field Uploader
			$body.on('click', '.themify-builder-media-uploader', function( event ){
				var $el = $(this);
		 
				file_frame = wp.media.frames.file_frame = wp.media({
					title: $(this).data('uploader-title'),
					library: {
						type: 'image'
					},
					button: {
						text: $(this).data('uploader-button-text')
					},
					multiple: false  // Set to true to allow multiple files to be selected
				});
		 
				// When an image is selected, run a callback.
				file_frame.on( 'select', function() {
					// We set multiple to false so only get one image from the uploader
					attachment = file_frame.state().get('selection').first().toJSON();
		 
					// Do something with attachment.id and/or attachment.url here
					$el.closest('.themify_builder_input').find('.themify-builder-uploader-input').val(attachment.url)
					.parent().find('.img-placeholder').empty()
					.html($('<img/>', {src: attachment.url, width: 50, height:50}))
					.parent().show();
				});
		 
				// Finally, open the modal
				file_frame.open();
				event.preventDefault();
			});

			// delete button
			$body.on('click', '.themify-builder-delete-thumb', function(e){
				$(this).prev().empty().parent().hide();
				$(this).parents('.themify_builder_input').find('.themify-builder-uploader-input').val('');
				e.preventDefault();
			});

			// Media Buttons
			$body.on('click', '.insert-media', function(e) {
				window.wpActiveEditor = $(this).data('editor');
			});
		},

		builderPlupload: function(action_text) {
			var self = ThemifyPageBuilder,
				class_new = action_text == 'new_elemn' ? '.plupload-clone' : '',
				$builderPluploadUpload = $(".themify-builder-plupload-upload-uic" + class_new);

			if($builderPluploadUpload.length > 0) {
				var pconfig = false;
				$builderPluploadUpload.each(function() {
					var $this = $(this),
						id1 = $this.attr("id"),
						imgId = id1.replace("themify-builder-plupload-upload-ui", "");

					pconfig=JSON.parse(JSON.stringify(themify_builder_plupload_init));

					pconfig["browse_button"] = imgId + pconfig["browse_button"];
					pconfig["container"] = imgId + pconfig["container"];
					pconfig["drop_element"] = imgId + pconfig["drop_element"];
					pconfig["file_data_name"] = imgId + pconfig["file_data_name"];
					pconfig["multipart_params"]["imgid"] = imgId;
					pconfig["multipart_params"]["_ajax_nonce"] = $this.find(".ajaxnonceplu").attr("id").replace("ajaxnonceplu", "");
					pconfig["multipart_params"]['topost'] = themifyBuilder.post_ID;

					var uploader = new plupload.Uploader(pconfig);

					uploader.bind('Init', function(up){});

					uploader.init();

					// a file was added in the queue
					uploader.bind('FilesAdded', function(up, files){
						up.refresh();
						up.start();
						self.showLoader('show');
					});

					uploader.bind('Error', function(up, error){
						var $promptError = $('.prompt-box .show-error');
						$('.prompt-box .show-login').hide();
						$promptError.show();
						
						if($promptError.length > 0){
							$promptError.html('<p class="prompt-error">' + error.message + '</p>');
						}
						$(".overlay, .prompt-box").fadeIn(500);
					});

					// a file was uploaded
					uploader.bind('FileUploaded', function(up, file, response) {
						var json = JSON.parse(response['response']), status;
						
						if('200' == response['status'] && !json.error) {
							status = 'done';
						} else {
							status = 'error';
						}
						
						$("#themify_builder_alert").removeClass("busy").addClass(status).delay(800).fadeOut(800, function() {
							$(this).removeClass(status);
						});
						
						if(json.error){
							alert(json.error);
							return;
						}

						var response_file = json.file,
							response_url = json.large_url,
							thumb_url = json.thumb;

						$this.parents('.themify_builder_input').find('.themify-builder-uploader-input').val(response_url)
						.parent().find('.img-placeholder').empty()
						.html($('<img/>', {src: thumb_url, width: 50, height:50}))
						.parent().show();

					});

					$this.removeClass('plupload-clone');
 
				});
			}
		},

		moduleOptionBuilder: function() {

			// sortable accordion builder
			$( ".themify_builder_module_opt_builder_wrap" ).sortable({
				items: '.themify_builder_row',
				handle: '.themify_builder_row_top',
				axis: 'y',
				placeholder: 'themify_builder_ui_state_highlight',
				start: function( event, ui ) {
					if ( typeof tinyMCE !== 'undefined' ) {
						$('#tfb_module_settings').find('.tfb_lb_wp_editor.tfb_lb_option_child').each(function(){
							var id = $(this).attr('id');
							tinyMCE.execCommand('mceRemoveControl', false, id);
						});
					}
				},
				stop: function( event, ui ) {
					if ( typeof tinyMCE !== 'undefined' ) {
						$('#tfb_module_settings').find('.tfb_lb_wp_editor.tfb_lb_option_child').each(function(){
							var id = $(this).attr('id');
							tinyMCE.execCommand('mceAddControl', false, id);
						});
					}
				},
				sort: function( event, ui ){
					var placeholder_h = ui.item.height();
					$('.themify_builder_module_opt_builder_wrap .themify_builder_ui_state_highlight').height(placeholder_h);
				}
			}).disableSelection();
		},

		moduleOptAddRow: function(e) {
			var self = ThemifyPageBuilder,
				parent = $(this).parent().prev(),
				template = parent.find('.themify_builder_row').first().clone(),
				row_count = $('.themify_builder_row_js_wrapper').find('.themify_builder_row:visible').length + 1,
				number = row_count + Math.floor(Math.random() * 9);

			// clear form data
			template.removeClass('collapsed').find('.themify_builder_row_content').show();
			template.find('.themify-builder-radio-dnd').each(function(i){
				var oriname = $(this).attr('name');
				$(this).attr('name', oriname + '_' + row_count).prop('checked', false);
				$(this).attr('id', oriname + '_' + row_count + '_' + i);
				$(this).next('label').attr('for', oriname + '_' + row_count + '_' + i);
			});
			template.find('.thumb_preview').each(function(){
				$(this).find('.img-placeholder').html('').parent().hide();
			});
			template.find('input[type=text], textarea').each(function(){
				$(this).val('');
			});
			template.find('.tfb_lb_wp_editor.tfb_lb_option_child').each(function(){
				$(this).addClass('clone');
			});
			template.find('.themify-builder-plupload-upload-uic').each(function(i){
				$(this).attr('id', 'pluploader_' + row_count + number + i + 'themify-builder-plupload-upload-ui');
				$(this).find('input[type=button]').attr('id', 'pluploader_' + row_count + number + i + 'themify-builder-plupload-browse-button');
				$(this).addClass('plupload-clone');
			});

			$(template).appendTo(parent).show();

			$('#tfb_module_settings').find('.tfb_lb_wp_editor.tfb_lb_option_child.clone').each(function(i){
				var element = $(this),
					parent_child = element.closest('.themify_builder_input');

				$(this).closest('.wp-editor-wrap').remove();
				
				var oriname = element.attr('name');
				element.attr('id', oriname + '_' + row_count + number + '_' + i);
				element.attr('class').replace('wp-editor-area', '');

				element.appendTo(parent_child).wrap('<div class="wp-editor-wrap"/>');

			});

			if( e.which ) {
				self.addNewWPEditor();
				self.builderPlupload('new_elemn');
			}

			e.preventDefault();
		},

		clickCheckBoxOption: function(e) {
			var selected_group = $(this).data('selected');
			$('.tf-group-element').hide();
			$('.'+selected_group).show();
			$('.thumb_preview').each(function(){
				if($(this).find('img').length == 0) {
					$(this).hide();
				}
			});
		},

		updateContent: function(class_hilite, hilite_obj) {
			var self = ThemifyPageBuilder,
				$builder_selector = $('.current_selected_module').closest('.themify_builder_content'),
				builder_id = $builder_selector.data('postid');

			$.ajax({
				type: "POST",
				url: themifyBuilder.ajaxurl,
				dataType: 'json',
				data:
				{
					action : 'tfb_load_module_partial',
					tfb_post_id : builder_id,
					tfb_w_class : class_hilite,
					tfb_mod_selector : hilite_obj,
					tfb_load_nonce : themifyBuilder.tfb_load_nonce,
					builder_grid_activate : 1
				},
				beforeSend: function( xhr ){
					self.showLoader('show');
				},
				success: function( data ){
					var $newElems = $(data.html),
						parent = $builder_selector.find('.temp_placeholder.' + class_hilite);

					// goto mod element
					if(parent.length > 0){
						$('html,body').animate({ scrollTop: parent.offset().top - 150 }, 500);
						parent.empty();
					}
					$newElems.find('.module_menu_front').addClass('current_selected_module');
					$newElems.appendTo(parent).unwrap()
					.find('.themify_builder_module_front_overlay')
					.show().delay(1000).fadeOut(1000);
					
					self.equalHeight();
					self.newRowAvailable();
					self.moduleEvents();
					self.loadContentJs();

					// Load google font style
					if ( 'undefined' !== WebFont && data.gfonts.length > 0 ) {
						WebFont.load({
							google: {
								families: data.gfonts
							}
						});
					}

					// Hook
					$('body').trigger('builder_load_module_partial', $newElems);
				}
			});
		},

		toggleFrontEdit: function(e) {
			var self = ThemifyPageBuilder,
				is_edit = 0;

			// remove lightbox if any
			if($('#themify_builder_lightbox_parent').is(':visible')){
				$('.close_lightbox').trigger('click');
			}

			var location_url = window.location.pathname + window.location.search;
			// remove hash
			if ( window.history && window.history.replaceState ) { 
				window.history.replaceState('', '', location_url); 
			} else { 
				window.location.href = window.location.href.replace(/#.*$/, '#'); 
			}

			var bids = [];
			$('.themify_builder_content').each(function(){
				var bid = $(this).data('postid');
				if ( typeof bid !== 'undefined' && bid !== '' ) {
					bids.push(bid);
				}
			});

			// add body class
			if(!$('body').hasClass('themify_builder_active')){
				is_edit = 1;
				$(this).text(themifyBuilder.toggleOff);
				$('.themify_builder_front_panel').slideDown();
			}else{
				$('.themify_builder_front_panel').slideUp();
				$(this).text(themifyBuilder.toggleOn);
				is_edit = 0;
			}

			if ( is_edit == 0 && self.editing ) {
				// confirm
				var reply = confirm(themifyBuilder.confirm_on_turn_off);
				if(reply){
					self.saveData(true, function(){
						self.toggleFrontEditAjax(is_edit, bids);
					});
				} else {
					self.toggleFrontEditAjax(is_edit, bids);
				}
			} else {
				self.toggleFrontEditAjax(is_edit, bids);
				self.editing = false;
			}

			e.preventDefault();
		},

		toggleFrontEditAjax: function(is_edit, bids) {
			var self = ThemifyPageBuilder;
			$.ajax({
				type: "POST",
				url: themifyBuilder.ajaxurl,
				dataType: 'json',
				data:
				{
					action : 'tfb_toggle_frontend',
					tfb_post_id : themifyBuilder.post_ID,
					tfb_post_ids : bids,
					tfb_load_nonce : themifyBuilder.tfb_load_nonce,
					builder_grid_activate : is_edit
				},
				beforeSend: function( xhr ){
					self.showLoader('show');
				},
				success: function( data ){
					if ( data.length > 0 ) {
						$('.themify_builder_content').empty();
						$.each(data, function(i,v) {
							var $target = $('#themify_builder_content-' + data[i].builder_id);
							$(data[i].markup).appendTo($target).unwrap();
						});
					}
					if(is_edit){
						$('body').addClass('themify_builder_active');
						self.newRowAvailable();
						self.moduleEvents();
						self.colDraggerTitle();

						setTimeout(function(){
							self.equalHeight();
						},1000);
						self.checkUnload();
					}
					else {
						$('body').removeClass('themify_builder_active');
						window.onbeforeunload = null;
					}
					self.loadContentJs();
					self.showLoader('spinhide');

					$('body').trigger('builder_toggle_frontend', is_edit);
				}
			});
		},

		newRowAvailable: function() {
			var self = ThemifyPageBuilder;

			$('.themify_builder_content').each(function(){
				var $container = $(this),
					$parent = $container.find('.themify_builder_row:visible'),
					$template = $container.find('.themify_builder_col_detection_1').clone().removeClass('themify_builder_col_detection_1').removeAttr('style');
				$parent.each(function(){
					if( $(this).find('.themify_builder_module_front').length == 0 ){
						$(this).remove();
					}
				});

				if( $parent.find('.themify_builder_module_front').length > 0 || $container.find('.themify_builder_row:visible').length == 0){
					$template.insertBefore($container.find('.themify_builder_col_detection_4'));
				}
			});
		},

		loadContentJs: function() {
			ThemifyBuilderModuleJs.loadOnAjax(); // load module js ajax
		},

		colDraggerTitle: function() {
			var self = ThemifyPageBuilder;
			$(self.builder_content_selector).find('.themify_builder_row').each(function(){
				$(this).find('.col_dragger').attr('title', themifyBuilder.draggerTitleMiddle);
			});

			$('.themify_builder_col').each(function(){
				if($(this).hasClass('last')){
					$(this).find('.col_dragger').attr('title', themifyBuilder.draggerTitleLast);
				}
			});
		},

		showLoader: function(stats) {
			if(stats == 'show'){
				$('#themify_builder_alert').addClass('busy').show();
			}
			else if(stats == 'spinhide'){
				$("#themify_builder_alert").delay(800).fadeOut(800, function() {
					$(this).removeClass('busy');
				});
			}
			else{
				$("#themify_builder_alert").removeClass("busy").addClass('done').delay(800).fadeOut(800, function() {
					$(this).removeClass('done');
				});
			}
		},

		duplicatePage: function(e) {
			var self = ThemifyPageBuilder;

			if($('body').hasClass('themify_builder_active')){
				var reply = confirm(themifyBuilder.confirm_on_duplicate_page);
				if(reply) {
					self.saveData(true, function(){
						self.duplicatePageAjax();
					});
				} else {
					self.duplicatePageAjax();
				}
			} else {
				self.duplicatePageAjax();
			}
			e.preventDefault();
		},

		duplicatePageAjax: function(){
			var self = ThemifyPageBuilder;
			$.ajax({
				type: "POST",
				url: themifyBuilder.ajaxurl,
				dataType: 'json',
				data:
				{
					action : 'tfb_duplicate_page',
					tfb_load_nonce : themifyBuilder.tfb_load_nonce,
					tfb_post_id : themifyBuilder.post_ID,
					tfb_is_admin: 0
				},
				beforeSend: function( xhr ){
					self.showLoader('show');
				},
				success: function( data ){
					self.showLoader('hide');
					var new_url = data.new_url.replace( /\&amp;/g, '&' );
					window.onbeforeunload = null;
					window.location.href = new_url;
				}
			});
		},

		getHighlightClass: function(obj) {
			var mod = obj.index() - 1,
				col = obj.closest('.themify_builder_col').index(),
				row = obj.closest('.themify_builder_row').index();

			return 'r' + row + 'c' + col + 'm' + mod;
		},

		getHighlightObject: function(obj) {
			var mod = obj.index() - 1,
					col = obj.closest('.themify_builder_col').index(),
					row = obj.closest('.themify_builder_row').index();

			return {row: row, col: col, mod: mod};

		},

		deleteEmptyModule: function() {
			var self = ThemifyPageBuilder;
			$(self.builder_content_selector).find('.themify_builder_module_front').each(function(){
				if($.trim($(this).find('.front_mod_settings').data('settings')).length <= 2){
					$(this).remove();
				}
			});
		},

		is_touch_device: function() {
			return 'true' == themifyBuilder.isTouch;
		},

		touchElement: function() {
			$('input, textarea').each(function(){
				$(this).addClass('touchInput');
			});
		},

		slidePanel: function(e) {
			$(this).parent().toggleClass('slide_builder_module_state_down');
			$(this).next().slideToggle();
			e.preventDefault();
		},

		openGallery: function() {
			
			var clone = wp.media.gallery.shortcode,
				file_frame;
			
			$('body').on('click', '.tf-gallery-btn', function( event ){
				var shortcode_val = $(this).closest('.themify_builder_input').find('.tf-shortcode-input');
				
				// Create the media frame.
				file_frame = wp.media.frames.file_frame = wp.media({
					frame:     'post',
					state:     'gallery-edit',
					title:     wp.media.view.l10n.editGalleryTitle,
					editing:   true,
					multiple:  true,
					selection: false
				});

				wp.media.gallery.shortcode = function(attachments) {
					var props = attachments.props.toJSON(),
					attrs = _.pick( props, 'orderby', 'order' );

					if ( attachments.gallery )
						_.extend( attrs, attachments.gallery.toJSON() );

					attrs.ids = attachments.pluck('id');

					// Copy the `uploadedTo` post ID.
					if ( props.uploadedTo )
						attrs.id = props.uploadedTo;

					// Check if the gallery is randomly ordered.
					if ( attrs._orderbyRandom )
						attrs.orderby = 'rand';
					delete attrs._orderbyRandom;

					// If the `ids` attribute is set and `orderby` attribute
					// is the default value, clear it for cleaner output.
					if ( attrs.ids && 'post__in' === attrs.orderby )
						delete attrs.orderby;

					// Remove default attributes from the shortcode.
					_.each( wp.media.gallery.defaults, function( value, key ) {
						if ( value === attrs[ key ] )
							delete attrs[ key ];
					});

					shortcode = new wp.shortcode({
						tag:    'gallery',
						attrs:  attrs,
						type:   'single'
					});

					shortcode_val.val(shortcode.string());

					wp.media.gallery.shortcode = clone;
					return shortcode;
				}

				file_frame.on( 'update', function( selection ) {
					var shortcode = wp.media.gallery.shortcode( selection ).string().slice( 1, -1 );
					shortcode_val.val('[' + shortcode + ']');
				});
			
				if($.trim(shortcode_val.val()).length > 0) {
					file_frame = wp.media.gallery.edit($.trim(shortcode_val.val()));
					file_frame.state('gallery-edit').on( 'update', function( selection ) {
						var shortcode = wp.media.gallery.shortcode( selection ).string().slice( 1, -1 );
						shortcode_val.val('[' + shortcode + ']');
					});
				} else {
					file_frame.open();
					$('.media-menu').find('.media-menu-item').last().trigger('click');
				}
				event.preventDefault();
			});
			
		},

		addNewWPEditor: function() {
			var self = ThemifyPageBuilder;

			$('#tfb_module_settings').find('.tfb_lb_wp_editor.clone').each(function(i){
				var element = $(this),
						element_val = element.val(),
						parent = element.closest('.themify_builder_input');
				
				$(this).closest('.wp-editor-wrap').remove();
				
				var oriname = element.attr('name'),
						this_option_id_temp = element.attr('id'),
						this_class = element.attr('class').replace('wp-editor-area', '').replace('clone', '');

				$.ajax({
					type: "POST",
					url: themifyBuilder.ajaxurl,
					dataType: 'html',
					data:
					{
						action : 'tfb_add_wp_editor',
						tfb_load_nonce : themifyBuilder.tfb_load_nonce,
						txt_id : this_option_id_temp,
						txt_class : this_class,
						txt_name : oriname,
						txt_val : element_val
					},
					success: function( data ){
						var $newElems = $(data),
								this_option_id_clone = $newElems.find('.tfb_lb_wp_editor').attr('id');
						$newElems.appendTo(parent);
						
						self.initQuickTags(this_option_id_clone);
						if ( typeof tinyMCE !== 'undefined' ) {
							self.initNewEditor( this_option_id_clone );
						}
					}
				});

			});
		},

		colDraggerUpdate: function(){
			var self = ThemifyPageBuilder;

			$('.themify_builder_content .themify_builder_row').each(function(){
				var $obj = $(this);
				if ( $obj.is(':visible')){
					var parent = $obj.find('.themify_builder_row_content'),
						gridFormat = self.detectGridFormat(parent),
						gridString = gridFormat.join('');
						parent.children().each(function(){
							$(this).find('.col_dragger').show();
						});
					if( gridString == '[4-1][4-1][4-1][4-1]' ) {
						parent.find('.col4-1.last .col_dragger').hide();
					} else {
						parent.find('.col4-1.last .col_dragger').show();
					}
				}
			});
		},

		moduleActions: function(){
			var $body = $('body');
			$body.on('change', '.module-widget-select-field', function(){
				var $seclass = $(this).val(),
					id_base = $(this).find(':selected').data('idbase');
				
				$.ajax({
					type: "POST",
					url: themifyBuilder.ajaxurl,
					dataType: 'html',
					data:
					{
						action : 'module_widget_get_form',
						tfb_load_nonce : themifyBuilder.tfb_load_nonce,
						load_class : $seclass,
						id_base : id_base
					},
					success: function( data ){
						var $newElems = $(data);

						$('.module-widget-form-placeholder').html($newElems);
					}
				});
			});

			$body.on('editing_module_option', function(e, settings){
				var $field = $('#tfb_module_settings .tfb_lb_option.module-widget-select-field');
				if ( $field.length == 0 ) return;

				var $seclass = $field.val(),
					id_base = $field.find(':selected').data('idbase'),
					$instance = settings.instance_widget;

				$.ajax({
					type: "POST",
					url: themifyBuilder.ajaxurl,
					dataType: 'html',
					data:
					{
						action : 'module_widget_get_form',
						tfb_load_nonce : themifyBuilder.tfb_load_nonce,
						load_class : $seclass,
						id_base : id_base,
						widget_instance: $instance
					},
					success: function( data ){
						var $newElems = $(data);
						$('.module-widget-form-placeholder').html($newElems);
					}
				});
			});
		},

		panelSave: function(e){
			e.preventDefault();
			var self = ThemifyPageBuilder;
			self.saveData(true);
		},

		panelClose: function(e){
			e.preventDefault();
			$('.toggle_tf_builder a:first').trigger('click');
		},

		builderImportPage: function(e){
			e.preventDefault();
			ThemifyPageBuilder.builderImport('page');
		},

		builderImportPost: function(e){
			e.preventDefault();
			ThemifyPageBuilder.builderImport('post');
		},

		builderImportSubmit: function(e){
			e.preventDefault();
			var postData = $(this).serialize();
			$.ajax({
				type: "POST",
				url: themifyBuilder.ajaxurl,
				dataType: 'json',
				data:
				{
					action : 'builder_import_submit',
					nonce : themifyBuilder.tfb_load_nonce,
					data : postData,
					importTo : themifyBuilder.post_ID
				},
				success: function( data ){
					$('.close_lightbox').trigger('click');
					window.location.reload();						
				}
			});
		},

		builderImport: function(imType){
			var self = ThemifyPageBuilder;
			$('#themify_builder_lightbox_container').empty();
			$('#themify_builder_overlay').show();
			self.showLoader('show');

			$.ajax({
				type: "POST",
				url: themifyBuilder.ajaxurl,
				dataType: 'html',
				data:
				{
					action : 'builder_import',
					nonce : themifyBuilder.tfb_load_nonce,
					type : imType
				},
				success: function( data ){
					var top = $(document).scrollTop() + 80;

					$("#themify_builder_lightbox_parent")
					.show()
					.css('top', self.getDocHeight())
					.animate({
						top: top
					}, 800 );

					self.showLoader('spinhide');
					
					$('.themify_builder_lightbox_title').text(themifyBuilder.textImportBuilder);
					$('#themify_builder_lightbox_container').html(data);

					$('#themify_builder_lightbox_parent').show();
				}
			});
		},

		optionRow: function(e) {
			e.preventDefault();

			var self = ThemifyPageBuilder,
				$this = $(this),
				$options = $this.closest('.themify_builder_row').find('.row-data-styling').data('styling');

			$('#themify_builder_lightbox_container').empty();
			$('#themify_builder_overlay').show();
			self.showLoader('show');

			// highlight current selected row
			$('.themify_builder_row').removeClass('current_selected_row');
			$this.closest('.themify_builder_row').addClass('current_selected_row');

			$.ajax({
				type: "POST",
				url: themifyBuilder.ajaxurl,
				data:
				{
					action : 'row_lightbox_options',
					nonce : themifyBuilder.tfb_load_nonce
				},
				success: function( data ){
					var top = $(document).scrollTop() + 80;

					$("#themify_builder_lightbox_parent")
					.show()
					.css('top', self.getDocHeight())
					.animate({
						top: top
					}, 800 );

					self.showLoader('spinhide');
					
					$('.themify_builder_lightbox_title').text(themifyBuilder.textRowStyling);
					$('#themify_builder_lightbox_container').html(data);

					if ( 'object' === typeof $options ) {
						$.each($options, function(id, val){
							$('#tfb_row_settings').find('#' + id).val(val);
						 });
						
						$('#tfb_row_settings').find('.tfb_lb_option[type=radio]').each(function(){
							var id = $(this).prop('name');
							if ('undefined' !== typeof $options[id]) {
								if ( $(this).val() === $options[id] ) {
									$(this).prop('checked', true);
								}
							}
						});
					}

					// image field
					$('#tfb_row_settings').find('.themify-builder-uploader-input').each(function(){
						var img_field = $(this).val(),
							img_thumb = $('<img/>', {src: img_field, width: 50, height: 50});

						if( img_field != '' ){
							$(this).parent().find('.img-placeholder').empty().html(img_thumb);
						}
						else{
							$(this).parent().find('.thumb_preview').hide();
						}
					});

					// set touch element
					self.touchElement();

					// colorpicker
					self.setColorPicker();

					// plupload init
					self.builderPlupload('normal');

					$('#themify_builder_lightbox_parent').show();
				}
			});
		},

		rowSaving: function(e) {
			e.preventDefault();
			var self = ThemifyPageBuilder,
				builder_id = $('.current_selected_row').closest('.themify_builder_content').data('postid'),
				$active_row_settings = $('.current_selected_row .row-data-styling'),
				temp_appended_data = $('#tfb_row_settings .tfb_lb_option').serializeObject();
			
			$active_row_settings.data('styling', temp_appended_data );

			// Save data
			self.saveData(true, function(){
				$('#themify_builder_lightbox_parent').hide();
				$('.close_lightbox').trigger('click');
				self.updateRowStyle( temp_appended_data, builder_id );
			}, 'cache');

			self.editing = true;
		},

		updateRowStyle: function( $postData, builder_id ) {
			$.ajax({
				type: "POST",
				url: themifyBuilder.ajaxurl,
				dataType: 'json',
				data:
				{
					action : 'update_row_style',
					nonce : themifyBuilder.tfb_load_nonce,
					postStyle: $postData,
					builder_id: builder_id
				},
				success: function( data ){

					// Apply bg repeat class
					if ( '' !== data.background_repeat ) {
						$('.current_selected_row').removeClass('repeat repeat-x repeat-y repeat-none fullcover').addClass(data.background_repeat);
					}

					// Apply animation class
					if ( '' !== data.animation_effect ) {
						$('.current_selected_row').removeClass('fly-in fade-in slide-up').addClass(data.animation_effect);
					}
					// row width class
					$('.current_selected_row').removeClass('fullwidth').addClass(data.row_width);
					
					$.each(data.styles, function(selector, properties) {
						var props = [];
						$.each(properties, function(i, val){
							var split = val.split(/:(.+)?/)[1];
							$(selector).css(i, split);
						});
					});

					// Load google font style
					if ( 'undefined' !== WebFont && data.gfonts.length > 0 ) {
						WebFont.load({
							google: {
								families: data.gfonts
							}
						});
					}

					$('html,body').animate({ scrollTop: $('.current_selected_row').offset().top - 150 }, 500);
				}
			});
		},

		resetModuleStyling: function(e){
			e.preventDefault();
			var dataReset = $(this).data('reset'),
				$context = dataReset == 'module' ? $('#themify_builder_options_styling') : $('#tfb_row_settings');
			
			$('.tfb_lb_option:not(.exclude-from-reset-field)', $context).each(function(){
				var $this = $(this);
				$this.val('').prop('checked', false).prop('selected', false);
				if( $this.hasClass('themify-builder-uploader-input') ) {
					$this.parent().find('.img-placeholder').html('').parent().hide();
				} else if ( $this.hasClass('font-family-select') ) {
					$this.val('default');
				} else if( $this.hasClass('builderColorSelectInput') ) {
					$this.parent().find('.builderColorSelect').minicolors('value', 'ffffff');
				}
			});
		},

		builderImportFile: function(e) {
			e.preventDefault();
			var self = ThemifyPageBuilder;
			
			if ( confirm( themifyBuilder.importFileConfirm ) ) {
				$('#themify_builder_lightbox_container').empty();
				$('#themify_builder_overlay').show();
				self.showLoader('show');

				$.ajax({
					type: "POST",
					url: themifyBuilder.ajaxurl,
					dataType: 'html',
					data:
					{
						action : 'builder_import_file',
						nonce : themifyBuilder.tfb_load_nonce
					},
					success: function( data ){
						var top = $(document).scrollTop() + 80;

						$("#themify_builder_lightbox_parent")
						.show()
						.css('top', self.getDocHeight())
						.animate({
							top: top
						}, 800 );

						self.showLoader('spinhide');
						
						$('#themify_builder_lightbox_container').html(data);

						self.builderImportPlupload();

						$('#themify_builder_lightbox_parent').show();
					}
				});
			}
		},

		builderImportPlupload: function() {
			var self = ThemifyPageBuilder,
				$builderPluploadUpload = $(".themify-builder-plupload-upload-uic");

			if($builderPluploadUpload.length > 0) {
				var pconfig = false;
				$builderPluploadUpload.each(function() {
					var $this = $(this),
						id1 = $this.attr("id"),
						imgId = id1.replace("themify-builder-plupload-upload-ui", "");

					pconfig = JSON.parse( JSON.stringify( themify_builder_plupload_init ) );

					pconfig["browse_button"] = imgId + pconfig["browse_button"];
					pconfig["container"] = imgId + pconfig["container"];
					pconfig["drop_element"] = imgId + pconfig["drop_element"];
					pconfig["file_data_name"] = imgId + pconfig["file_data_name"];
					pconfig["multipart_params"]["imgid"] = imgId;
					pconfig["multipart_params"]["_ajax_nonce"] = $this.find(".ajaxnonceplu").attr("id").replace("ajaxnonceplu", "");
					pconfig["multipart_params"]['topost'] = themifyBuilder.post_ID;

					var uploader = new plupload.Uploader( pconfig );

					uploader.bind('Init', function(up){});

					uploader.init();

					// a file was added in the queue
					uploader.bind('FilesAdded', function(up, files){
						up.refresh();
						up.start();
						self.showLoader('show');
					});

					uploader.bind('Error', function(up, error){
						var $promptError = $('.prompt-box .show-error');
						$('.prompt-box .show-login').hide();
						$promptError.show();
						
						if($promptError.length > 0){
							$promptError.html('<p class="prompt-error">' + error.message + '</p>');
						}
						$(".overlay, .prompt-box").fadeIn(500);
					});

					// a file was uploaded
					uploader.bind('FileUploaded', function(up, file, response) {
						var json = JSON.parse(response['response']), status;

						if('200' == response['status'] && !json.error) {
							status = 'done';
						} else {
							status = 'error';
						}
						
						$("#themify_builder_alert").removeClass("busy").addClass(status).delay(800).fadeOut(800, function() {
							$(this).removeClass(status);
						});
						
						if(json.error){
							alert(json.error);
							return;
						}

						$('#themify_builder_alert').promise().done(function(){
							$('.close_lightbox').trigger('click');
							window.location.reload();
						});

					});
 
				});
			}
		}
	};

	// Initialize Builder
	$(function(){
		ThemifyPageBuilder.init();
	});
}(jQuery, window, document));