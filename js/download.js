( function ( $ ) {
	'use strict';

	$( document ).ready( function () {
		// 初始化下载按钮
		initDownloadButtons();
	} );

	// 初始化下载按钮
	function initDownloadButtons() {
		$( '.download-button' ).on( 'click', function ( e ) {
			e.preventDefault();

			const $button = $( this );
			const resourceId = $button.data( 'resource-id' );

			// 禁用按钮
			$button.prop( 'disabled', true );

			// 发送下载请求
			$.ajax( {
				url: qiooooSettings.ajaxurl,
				type: 'POST',
				data: {
					action: 'qioooo_download',
					nonce: qiooooSettings.nonce,
					resource_id: resourceId,
				},
				success( response ) {
					if ( response.success ) {
						// 开始下载
						window.location.href = response.data.download_url;

						// 更新下载次数
						const $count = $button
							.closest( '.download-info' )
							.find( '.download-count' );
						const currentCount = parseInt(
							$count.text().replace( /,/g, '' )
						);
						$count.html(
							'<i class="fas fa-download"></i> ' +
								( currentCount + 1 ).toLocaleString()
						);
					} else {
						showMessage( 'error', response.data.message );
					}
				},
				error() {
					showMessage( 'error', '下载失败，请稍后重试' );
				},
				complete() {
					// 启用按钮
					$button.prop( 'disabled', false );
				},
			} );
		} );
	}

	// 显示消息
	function showMessage( type, message ) {
		const $message = $( '<div>' )
			.addClass( type + '-message' )
			.text( message );

		$( '.download-info' ).prepend( $message );

		// 3秒后自动隐藏消息
		setTimeout( function () {
			$message.fadeOut( function () {
				$( this ).remove();
			} );
		}, 3000 );
	}
} )( jQuery );
