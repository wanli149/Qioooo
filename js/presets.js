/**
 * 主题预设交互功能
 * @param $
 */
( function ( $ ) {
	'use strict';

	// 初始化
	$( document ).ready( function () {
		initPresetSelector();
		initPresetPreview();
	} );

	/**
	 * 初始化预设选择器
	 */
	function initPresetSelector() {
		const $presetSelect = $( '#theme_preset' );

		$presetSelect.on( 'change', function () {
			const presetId = $( this ).val();

			// 显示加载动画
			showLoading();

			// 发送AJAX请求
			$.ajax( {
				url: qioooo.ajax_url,
				type: 'POST',
				data: {
					action: 'qioooo_change_preset',
					theme_preset: presetId,
					nonce: qioooo.nonce,
				},
				success( response ) {
					if ( response.success ) {
						// 刷新页面
						location.reload();
					} else {
						showError( '应用预设失败' );
					}
				},
				error() {
					showError( '网络错误' );
				},
				complete() {
					hideLoading();
				},
			} );
		} );
	}

	/**
	 * 初始化预设预览
	 */
	function initPresetPreview() {
		const $presetSelect = $( '#theme_preset' );
		const $previewContainer = $( '<div class="preset-preview"></div>' );

		// 添加预览容器
		$presetSelect.after( $previewContainer );

		// 更新预览
		function updatePreview() {
			const presetId = $presetSelect.val();
			const preset = qioooo.presets[ presetId ];

			if ( ! preset ) {
				return;
			}

			// 创建预览元素
			const $preview = $( '<div class="preset-preview-item"></div>' );

			// 添加颜色预览
			const $colors = $( '<div class="preset-colors"></div>' );
			Object.entries( preset.colors ).forEach( ( [ key, value ] ) => {
				$colors.append(
					`<div class="color-item" style="background-color: ${ value }" title="${ key }"></div>`
				);
			} );

			// 添加排版预览
			const $typography = $( '<div class="preset-typography"></div>' );
			$typography
				.css( {
					'font-family': preset.typography.font_family,
					'font-size': preset.typography.font_size,
					'line-height': preset.typography.line_height,
				} )
				.text( '示例文本' );

			// 添加布局预览
			const $layout = $( '<div class="preset-layout"></div>' );
			$layout.append( `<div class="layout-header">页眉</div>` );
			$layout.append( `<div class="layout-content">内容</div>` );
			$layout.append( `<div class="layout-sidebar">侧边栏</div>` );

			// 更新预览容器
			$previewContainer
				.empty()
				.append( $colors )
				.append( $typography )
				.append( $layout );
		}

		// 监听选择器变化
		$presetSelect.on( 'change', updatePreview );

		// 初始预览
		updatePreview();
	}

	/**
	 * 显示加载动画
	 */
	function showLoading() {
		const $loading = $( '<div class="preset-loading">应用预设中...</div>' );
		$( 'body' ).append( $loading );
	}

	/**
	 * 隐藏加载动画
	 */
	function hideLoading() {
		$( '.preset-loading' ).remove();
	}

	/**
	 * 显示错误信息
	 * @param message
	 */
	function showError( message ) {
		const $error = $( '<div class="preset-error">' + message + '</div>' );
		$( 'body' ).append( $error );
		setTimeout( () => $error.remove(), 3000 );
	}
} )( jQuery );
