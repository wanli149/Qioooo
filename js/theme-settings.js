/**
 * 主题设置页面交互优化
 * @param $
 */
( function ( $ ) {
	'use strict';

	// 检测设备类型
	const isMobile =
		/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
			navigator.userAgent
		);
	const isTouchDevice =
		'ontouchstart' in window || navigator.maxTouchPoints > 0;

	// 初始化
	$( document ).ready( function () {
		// 初始化主题设置
		initThemeSettings();

		// 初始化主题预设
		initThemePresets();

		// 初始化导入/导出
		initImportExport();

		// 初始化交互动画
		initAnimations();

		// 初始化主题预览
		initThemePreview();

		// 初始化语言设置
		initLanguageSettings();

		// 移动端特定初始化
		if ( isMobile ) {
			initMobileFeatures();
		}

		// 初始化主题设置优化
		optimizeThemeSettings();

		// 优化按钮无障碍与交互反馈
		enhanceButtonAccessibility();

		// RTL支持优化
		enhanceRTL();
	} );

	/**
	 * 初始化移动端特定功能
	 */
	function initMobileFeatures() {
		// 优化触摸事件
		optimizeTouchEvents();

		// 优化滚动体验
		optimizeScrolling();

		// 优化表单输入
		optimizeFormInputs();

		// 添加移动端手势支持
		initGestures();
	}

	/**
	 * 优化触摸事件
	 */
	function optimizeTouchEvents() {
		// 移除点击延迟
		FastClick.attach( document.body );

		// 优化按钮触摸反馈
		$( '.btn, .option-group, .preset-option' )
			.on( 'touchstart', function () {
				$( this ).addClass( 'active' );
			} )
			.on( 'touchend', function () {
				$( this ).removeClass( 'active' );
			} );

		// 优化长按事件
		let longPressTimer;
		$( '.preset-option' )
			.on( 'touchstart', function () {
				longPressTimer = setTimeout( () => {
					showPresetDetails( $( this ) );
				}, 500 );
			} )
			.on( 'touchend', function () {
				clearTimeout( longPressTimer );
			} );
	}

	/**
	 * 优化滚动体验
	 */
	function optimizeScrolling() {
		// 使用平滑滚动
		$( 'html, body' ).css( {
			'scroll-behavior': 'smooth',
			'-webkit-overflow-scrolling': 'touch',
		} );

		// 优化滚动性能
		$( window ).on( 'scroll', function () {
			requestAnimationFrame( handleScroll );
		} );
	}

	/**
	 * 处理滚动事件
	 */
	function handleScroll() {
		// 实现视差滚动效果
		$( '.settings-section' ).each( function () {
			const $section = $( this );
			const scrollTop = $( window ).scrollTop();
			const sectionTop = $section.offset().top;
			const windowHeight = $( window ).height();

			if ( scrollTop + windowHeight > sectionTop ) {
				$section.addClass( 'visible' );
			}
		} );
	}

	/**
	 * 优化表单输入
	 */
	function optimizeFormInputs() {
		// 防止iOS自动缩放
		$( 'input, select, textarea' ).on( 'focus', function () {
			$( this ).css( 'font-size', '16px' );
		} );

		// 优化选择器
		$( 'select' ).on( 'change', function () {
			$( this ).blur();
		} );

		// 优化复选框和单选框
		$( 'input[type="checkbox"], input[type="radio"]' ).on(
			'change',
			function () {
				const $label = $( this ).next( 'label' );
				$label.addClass( 'active' );
				setTimeout( () => $label.removeClass( 'active' ), 200 );
			}
		);
	}

	/**
	 * 初始化手势支持
	 */
	function initGestures() {
		// 使用Hammer.js添加手势支持
		const hammer = new Hammer( document.body );

		// 左滑返回
		hammer.on( 'swipeleft', function () {
			if ( window.history.length > 1 ) {
				window.history.back();
			}
		} );

		// 右滑前进
		hammer.on( 'swiperight', function () {
			window.history.forward();
		} );

		// 双击刷新
		hammer.on( 'doubletap', function () {
			refreshThemePreview();
		} );
	}

	/**
	 * 显示预设详情
	 * @param $preset
	 */
	function showPresetDetails( $preset ) {
		const presetName = $preset.find( '.preset-name' ).text();
		const presetDesc = $preset.find( '.preset-desc' ).text();

		// 创建详情弹窗
		const $modal = $( '<div class="mobile-modal">' ).append(
			$( '<div class="modal-content">' )
				.append( $( '<h3>' ).text( presetName ) )
				.append( $( '<p>' ).text( presetDesc ) )
				.append(
					$( '<button class="btn">' )
						.text( '应用预设' )
						.on( 'click', function () {
							applyThemePreset( $preset.find( 'input' ).val() );
							$modal.remove();
						} )
				)
				.append(
					$( '<button class="btn btn-secondary">' )
						.text( '取消' )
						.on( 'click', function () {
							$modal.remove();
						} )
				)
		);

		$( 'body' ).append( $modal );
	}

	/**
	 * 初始化主题设置
	 */
	function initThemeSettings() {
		// 设置项悬停效果
		$( '.settings-section' ).hover(
			function () {
				$( this ).addClass( 'hover' );
			},
			function () {
				$( this ).removeClass( 'hover' );
			}
		);

		// 表单提交动画
		$( '#theme-settings-form' ).on( 'submit', function ( e ) {
			e.preventDefault();

			const $form = $( this );
			const $submitBtn = $form.find( 'button[type="submit"]' );

			// 添加加载动画
			$submitBtn.addClass( 'loading' );
			$submitBtn.html( '<span class="spinner"></span> 保存中...' );

			// 模拟保存过程
			setTimeout( function () {
				$submitBtn.removeClass( 'loading' );
				$submitBtn.html( '保存设置' );

				// 显示成功提示
				showNotification( '设置已保存', 'success' );
			}, 1000 );
		} );
	}

	/**
	 * 初始化主题预设
	 */
	function initThemePresets() {
		$( '.preset-option' ).hover(
			function () {
				$( this ).addClass( 'hover' );
				$( this ).find( '.preset-preview' ).addClass( 'hover' );
			},
			function () {
				$( this ).removeClass( 'hover' );
				$( this ).find( '.preset-preview' ).removeClass( 'hover' );
			}
		);

		$( '.preset-option input[type="radio"]' ).on( 'change', function () {
			const preset = $( this ).val();

			// 添加切换动画
			$( '.preset-option' ).removeClass( 'active' );
			$( this ).closest( '.preset-option' ).addClass( 'active' );

			// 应用预设
			applyThemePreset( preset );
		} );
	}

	/**
	 * 应用主题预设
	 * @param preset
	 */
	function applyThemePreset( preset ) {
		// 移除所有预设类
		$( 'body' ).removeClass(
			'theme-preset-default theme-preset-minimal theme-preset-dark theme-preset-colorful'
		);

		// 添加新预设类
		$( 'body' ).addClass( 'theme-preset-' + preset );

		// 更新预览
		refreshThemePreview();

		// 显示提示
		showNotification(
			'已应用' + $( '.preset-option.active .preset-name' ).text(),
			'success'
		);
	}

	/**
	 * 初始化导入/导出
	 */
	function initImportExport() {
		// 导入文件拖放
		const $importSection = $( '.import-section' );

		$importSection.on( 'dragover', function ( e ) {
			e.preventDefault();
			$( this ).addClass( 'dragover' );
		} );

		$importSection.on( 'dragleave', function () {
			$( this ).removeClass( 'dragover' );
		} );

		$importSection.on( 'drop', function ( e ) {
			e.preventDefault();
			$( this ).removeClass( 'dragover' );

			const file = e.originalEvent.dataTransfer.files[ 0 ];
			if ( file ) {
				handleImportFile( file );
			}
		} );

		// 导入按钮点击
		$( '#import-settings' ).on( 'click', function () {
			const fileInput = $( '#theme-import' )[ 0 ];
			if ( fileInput.files.length > 0 ) {
				handleImportFile( fileInput.files[ 0 ] );
			}
		} );

		// 导出按钮点击
		$( '#export-settings' ).on( 'click', function () {
			exportThemeSettings();
		} );
	}

	/**
	 * 处理导入文件
	 * @param file
	 */
	function handleImportFile( file ) {
		const reader = new FileReader();

		reader.onload = function ( e ) {
			try {
				const settings = JSON.parse( e.target.result );
				importThemeSettings( settings );
			} catch ( error ) {
				showNotification( '导入失败：文件格式错误', 'error' );
			}
		};

		reader.readAsText( file );
	}

	/**
	 * 导入主题设置
	 * @param settings
	 */
	function importThemeSettings( settings ) {
		// 显示加载动画
		showNotification( '正在导入设置...', 'info' );

		// 模拟导入过程
		setTimeout( function () {
			// 更新设置
			updateThemeSettings( settings );

			// 显示成功提示
			showNotification( '设置导入成功', 'success' );

			// 刷新页面
			location.reload();
		}, 1000 );
	}

	/**
	 * 导出主题设置
	 */
	function exportThemeSettings() {
		const settings = getCurrentSettings();
		const blob = new Blob( [ JSON.stringify( settings, null, 2 ) ], {
			type: 'application/json',
		} );
		const url = URL.createObjectURL( blob );

		const a = document.createElement( 'a' );
		a.href = url;
		a.download = 'qioooo-theme-settings.json';
		document.body.appendChild( a );
		a.click();
		document.body.removeChild( a );
		URL.revokeObjectURL( url );

		showNotification( '设置已导出', 'success' );
	}

	/**
	 * 初始化交互动画
	 */
	function initAnimations() {
		// 动画选项切换
		$( '.animation-options input[type="checkbox"]' ).on(
			'change',
			function () {
				const $option = $( this ).closest( '.option-group' );

				if ( $( this ).is( ':checked' ) ) {
					$option.addClass( 'active' );
					showNotification(
						'已启用' + $( this ).next( 'label' ).text(),
						'success'
					);
				} else {
					$option.removeClass( 'active' );
					showNotification(
						'已禁用' + $( this ).next( 'label' ).text(),
						'info'
					);
				}
			}
		);

		// 滚动动画
		$( window ).on( 'scroll', function () {
			$( '.scroll-animations .option-group' ).each( function () {
				const $option = $( this );
				const position = $option.offset().top;
				const scroll = $( window ).scrollTop();
				const windowHeight = $( window ).height();

				if ( position < scroll + windowHeight - 100 ) {
					$option.addClass( 'visible' );
				}
			} );
		} );
	}

	/**
	 * 初始化主题预览
	 */
	function initThemePreview() {
		// 刷新预览
		$( '#refresh-preview' ).on( 'click', function () {
			refreshThemePreview();
			showNotification( '预览已刷新', 'info' );
		} );

		// 应用预览
		$( '#apply-preview' ).on( 'click', function () {
			const settings = getCurrentSettings();
			applyThemeSettings( settings );
			showNotification( '预览设置已应用', 'success' );
		} );
	}

	/**
	 * 刷新主题预览
	 */
	function refreshThemePreview() {
		const $previewFrame = $( '#theme-preview-frame' );
		const currentSrc = $previewFrame.attr( 'src' );
		$previewFrame.attr( 'src', currentSrc );
	}

	/**
	 * 初始化语言设置
	 */
	function initLanguageSettings() {
		// 语言切换
		$( '#theme_language' ).on( 'change', function () {
			const language = $( this ).val();
			applyLanguage( language );
		} );

		// RTL切换
		$( '#rtl_support' ).on( 'change', function () {
			const isRTL = $( this ).is( ':checked' );
			toggleRTL( isRTL );
		} );
	}

	/**
	 * 应用语言设置
	 * @param language
	 */
	function applyLanguage( language ) {
		$( 'html' ).attr( 'lang', language );
		showNotification(
			'语言已切换为' + $( '#theme_language option:selected' ).text(),
			'success'
		);
	}

	/**
	 * 切换RTL支持
	 * @param isRTL
	 */
	function toggleRTL( isRTL ) {
		if ( isRTL ) {
			$( 'html' ).attr( 'dir', 'rtl' );
			$( 'body' ).addClass( 'rtl' );
		} else {
			$( 'html' ).attr( 'dir', 'ltr' );
			$( 'body' ).removeClass( 'rtl' );
		}

		showNotification( isRTL ? '已启用RTL支持' : '已禁用RTL支持', 'info' );
	}

	/**
	 * 显示通知
	 * @param message
	 * @param type
	 */
	function showNotification( message, type ) {
		const $notification = $(
			'<div class="notification ' + type + '">' + message + '</div>'
		);

		$( 'body' ).append( $notification );

		setTimeout( function () {
			$notification.addClass( 'show' );
		}, 100 );

		setTimeout( function () {
			$notification.removeClass( 'show' );
			setTimeout( function () {
				$notification.remove();
			}, 300 );
		}, 3000 );
	}

	/**
	 * 获取当前设置
	 */
	function getCurrentSettings() {
		const settings = {};

		// 收集表单数据
		$( '#theme-settings-form' )
			.serializeArray()
			.forEach( function ( item ) {
				settings[ item.name ] = item.value;
			} );

		return settings;
	}

	/**
	 * 更新主题设置
	 * @param settings
	 */
	function updateThemeSettings( settings ) {
		// 更新表单数据
		Object.keys( settings ).forEach( function ( key ) {
			const $input = $( '[name="' + key + '"]' );

			if ( $input.is( ':checkbox' ) ) {
				$input.prop( 'checked', settings[ key ] );
			} else {
				$input.val( settings[ key ] );
			}
		} );

		// 应用设置
		applyThemeSettings( settings );
	}

	/**
	 * 应用主题设置
	 * @param settings
	 */
	function applyThemeSettings( settings ) {
		// 更新body类
		Object.keys( settings ).forEach( function ( key ) {
			if ( key === 'theme_preset' ) {
				$( 'body' )
					.removeClass(
						'theme-preset-default theme-preset-minimal theme-preset-dark theme-preset-colorful'
					)
					.addClass( 'theme-preset-' + settings[ key ] );
			} else if ( key === 'rtl_support' ) {
				toggleRTL( settings[ key ] );
			}
		} );

		// 刷新预览
		refreshThemePreview();
	}

	/**
	 * 主题设置优化
	 */
	function optimizeThemeSettings() {
		// 实时预览
		const settingsForm = document.querySelector( '.theme-settings-form' );
		if ( settingsForm ) {
			settingsForm.addEventListener( 'change', function ( e ) {
				const target = e.target;
				const settingName = target.name
					.replace( 'qioooo_options[', '' )
					.replace( ']', '' );
				const settingValue = target.value;

				// 应用设置预览
				applySettingPreview( settingName, settingValue );
			} );
		}

		// 设置导入导出
		const importBtn = document.querySelector( '#import-settings' );
		const exportBtn = document.querySelector( '#export-settings' );

		if ( importBtn ) {
			importBtn.addEventListener( 'click', function () {
				const fileInput = document.createElement( 'input' );
				fileInput.type = 'file';
				fileInput.accept = '.json';

				fileInput.addEventListener( 'change', function ( e ) {
					const file = e.target.files[ 0 ];
					if ( file ) {
						const reader = new FileReader();
						reader.onload = function ( e ) {
							try {
								const settings = JSON.parse( e.target.result );
								importSettings( settings );
							} catch ( error ) {
								showNotification(
									'导入失败：无效的设置文件',
									'error'
								);
							}
						};
						reader.readAsText( file );
					}
				} );

				fileInput.click();
			} );
		}

		if ( exportBtn ) {
			exportBtn.addEventListener( 'click', function () {
				const settings = getCurrentSettings();
				const blob = new Blob(
					[ JSON.stringify( settings, null, 2 ) ],
					{ type: 'application/json' }
				);
				const url = URL.createObjectURL( blob );

				const a = document.createElement( 'a' );
				a.href = url;
				a.download =
					'qioooo-settings-' +
					new Date().toISOString().split( 'T' )[ 0 ] +
					'.json';
				document.body.appendChild( a );
				a.click();
				document.body.removeChild( a );
				URL.revokeObjectURL( url );
			} );
		}

		// 设置备份
		const backupBtn = document.querySelector( '#backup-settings' );
		if ( backupBtn ) {
			backupBtn.addEventListener( 'click', function () {
				const settings = getCurrentSettings();
				localStorage.setItem(
					'qioooo_settings_backup',
					JSON.stringify( settings )
				);
				showNotification( '设置已备份', 'success' );
			} );
		}

		// 设置恢复
		const restoreBtn = document.querySelector( '#restore-settings' );
		if ( restoreBtn ) {
			restoreBtn.addEventListener( 'click', function () {
				const backup = localStorage.getItem( 'qioooo_settings_backup' );
				if ( backup ) {
					try {
						const settings = JSON.parse( backup );
						restoreSettings( settings );
						showNotification( '设置已恢复', 'success' );
					} catch ( error ) {
						showNotification( '恢复失败：无效的备份文件', 'error' );
					}
				} else {
					showNotification( '未找到备份文件', 'error' );
				}
			} );
		}
	}

	/**
	 * 应用设置预览
	 * @param settingName
	 * @param settingValue
	 */
	function applySettingPreview( settingName, settingValue ) {
		switch ( settingName ) {
			case 'color_scheme':
				document.body.classList.remove(
					'color-scheme-light',
					'color-scheme-dark'
				);
				document.body.classList.add( 'color-scheme-' + settingValue );
				break;

			case 'layout':
				document.body.classList.remove( 'layout-wide', 'layout-boxed' );
				document.body.classList.add( 'layout-' + settingValue );
				break;

			case 'sidebar_position':
				document.body.classList.remove(
					'sidebar-left',
					'sidebar-right',
					'sidebar-none'
				);
				document.body.classList.add( 'sidebar-' + settingValue );
				break;

			case 'enable_animations':
				document.body.classList.toggle(
					'enable-animations',
					settingValue === '1'
				);
				break;

			case 'rtl_support':
				document.body.classList.toggle( 'rtl', settingValue === '1' );
				break;
		}
	}

	/**
	 * 导入设置
	 * @param settings
	 */
	function importSettings( settings ) {
		const form = document.querySelector( '.theme-settings-form' );
		Object.keys( settings ).forEach( ( key ) => {
			const input = form.querySelector(
				`[name="qioooo_options[${ key }]"]`
			);
			if ( input ) {
				input.value = settings[ key ];
				applySettingPreview( key, settings[ key ] );
			}
		} );

		showNotification( '设置导入成功', 'success' );
	}

	/**
	 * 恢复设置
	 * @param settings
	 */
	function restoreSettings( settings ) {
		importSettings( settings );
	}

	/**
	 * 优化按钮无障碍与交互反馈
	 */
	function enhanceButtonAccessibility() {
		// 按钮键盘可达性
		$( document ).on( 'keydown', '.btn', function ( e ) {
			if (
				( e.key === 'Enter' || e.key === ' ' ) &&
				! $( this ).is( ':disabled' )
			) {
				$( this ).trigger( 'click' );
			}
		} );
		// ARIA属性
		$( '.btn' ).attr( 'tabindex', 0 ).attr( 'role', 'button' );
	}

	/**
	 * RTL支持优化
	 */
	function enhanceRTL() {
		if ( $( 'html' ).attr( 'dir' ) === 'rtl' ) {
			$( 'body' ).addClass( 'rtl-enabled' );
		}
	}
} )( jQuery );
