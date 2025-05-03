jQuery( document ).ready( function ( $ ) {
	// Logo 上传
	$( '#upload_logo_button' ).click( function ( e ) {
		e.preventDefault();

		var custom_uploader = wp
			.media( {
				title: '选择 Logo',
				button: {
					text: '使用此图片',
				},
				multiple: false,
			} )
			.on( 'select', function () {
				const attachment = custom_uploader
					.state()
					.get( 'selection' )
					.first()
					.toJSON();
				$( '#qioooo_logo' ).val( attachment.url );
			} )
			.open();
	} );

	// 网站图标上传
	$( '#upload_favicon_button' ).click( function ( e ) {
		e.preventDefault();

		var custom_uploader = wp
			.media( {
				title: '选择网站图标',
				button: {
					text: '使用此图片',
				},
				multiple: false,
			} )
			.on( 'select', function () {
				const attachment = custom_uploader
					.state()
					.get( 'selection' )
					.first()
					.toJSON();
				$( '#qioooo_favicon' ).val( attachment.url );
			} )
			.open();
	} );

	// 显示预览
	function showPreview( input, previewId ) {
		if ( input.files && input.files[ 0 ] ) {
			const reader = new FileReader();
			reader.onload = function ( e ) {
				$( '#' + previewId ).attr( 'src', e.target.result );
			};
			reader.readAsDataURL( input.files[ 0 ] );
		}
	}

	// 表单验证
	$( 'form' ).on( 'submit', function ( e ) {
		const logo = $( '#qioooo_logo' ).val();
		const favicon = $( '#qioooo_favicon' ).val();

		if ( ! logo || ! favicon ) {
			alert( '请上传所有必需的图片！' );
			e.preventDefault();
		}
	} );

	/**
	 * 初始化实时预览
	 */
	function initLivePreview() {
		// 监听设置变化
		$( '.theme-settings input, .theme-settings select' ).on(
			'change',
			function () {
				const setting = $( this ).attr( 'name' );
				const value = $( this ).val();

				// 更新预览
				updatePreview( setting, value );

				// 检查依赖关系
				checkDependencies( setting, value );
			}
		);
	}

	/**
	 * 更新预览
	 * @param setting
	 * @param value
	 */
	function updatePreview( setting, value ) {
		switch ( setting ) {
			case 'color_scheme':
				updateColorSchemePreview( value );
				break;
			case 'layout':
				updateLayoutPreview( value );
				break;
			case 'background_effect':
				updateBackgroundEffectPreview( value );
				break;
			// 添加其他设置的预览更新
		}
	}

	/**
	 * 检查依赖关系
	 * @param setting
	 * @param value
	 */
	function checkDependencies( setting, value ) {
		const dependencies = {
			background_effect: {
				'gradient-wave': [ 'enable_animations' ],
				particles: [ 'enable_animations', 'enable_compression' ],
			},
			enable_compression: {
				true: [ 'optimize_database' ],
			},
			// 添加其他依赖关系
		};

		if ( dependencies[ setting ] && dependencies[ setting ][ value ] ) {
			dependencies[ setting ][ value ].forEach( ( dependent ) => {
				const $dependent = $( `[name="${ dependent }"]` );
				if ( $dependent.length ) {
					$dependent.prop( 'checked', true );
					updatePreview( dependent, true );
				}
			} );
		}
	}

	/**
	 * 更新颜色方案预览
	 * @param scheme
	 */
	function updateColorSchemePreview( scheme ) {
		const $preview = $( '.color-scheme-preview' );
		$preview.removeClass( 'light dark auto' ).addClass( scheme );
	}

	/**
	 * 更新布局预览
	 * @param layout
	 */
	function updateLayoutPreview( layout ) {
		const $preview = $( '.layout-preview' );
		$preview.removeClass( 'wide boxed full-width' ).addClass( layout );
	}

	/**
	 * 更新背景特效预览
	 * @param effect
	 */
	function updateBackgroundEffectPreview( effect ) {
		const $preview = $( '.background-effect-preview' );
		$preview
			.removeClass( 'none gradient-wave grid particles' )
			.addClass( effect );
	}

	// 初始化
	initLivePreview();
} );
