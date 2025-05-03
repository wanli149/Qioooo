( function ( $ ) {
	'use strict';

	$( document ).ready( function () {
		// 初始化社交登录按钮
		initSocialLogin();

		// 初始化表单验证
		initFormValidation();
	} );

	// 初始化社交登录按钮
	function initSocialLogin() {
		$( '.social-button' ).on( 'click', function ( e ) {
			e.preventDefault();
			const platform = $( this ).data( 'platform' );

			switch ( platform ) {
				case 'wechat':
					handleWechatLogin();
					break;
				case 'qq':
					handleQQLogin();
					break;
			}
		} );
	}

	// 处理微信登录
	function handleWechatLogin() {
		// 这里需要替换为实际的微信登录API
		const wechatLoginUrl =
			'https://open.weixin.qq.com/connect/qrconnect?appid=YOUR_APPID&redirect_uri=YOUR_REDIRECT_URI&response_type=code&scope=snsapi_login&state=STATE#wechat_redirect';

		// 打开微信登录窗口
		window.open( wechatLoginUrl, 'WechatLogin', 'width=500,height=600' );
	}

	// 处理QQ登录
	function handleQQLogin() {
		// 这里需要替换为实际的QQ登录API
		const qqLoginUrl =
			'https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=YOUR_CLIENT_ID&redirect_uri=YOUR_REDIRECT_URI&state=STATE';

		// 打开QQ登录窗口
		window.open( qqLoginUrl, 'QQLogin', 'width=500,height=600' );
	}

	// 初始化表单验证
	function initFormValidation() {
		$( '.login-form, .register-form' ).on( 'submit', function ( e ) {
			e.preventDefault();

			const $form = $( this );
			const $submitButton = $form.find( 'button[type="submit"]' );
			const formData = new FormData( $form[ 0 ] );

			// 禁用提交按钮
			$submitButton.prop( 'disabled', true );

			// 发送AJAX请求
			$.ajax( {
				url: qiooooSettings.ajaxurl,
				type: 'POST',
				data: formData,
				processData: false,
				contentType: false,
				success( response ) {
					if ( response.success ) {
						showMessage( 'success', response.data.message );
						if ( response.data.redirect ) {
							window.location.href = response.data.redirect;
						}
					} else {
						showMessage( 'error', response.data.message );
					}
				},
				error() {
					showMessage( 'error', '发生错误，请稍后重试' );
				},
				complete() {
					// 启用提交按钮
					$submitButton.prop( 'disabled', false );
				},
			} );
		} );
	}

	// 显示消息
	function showMessage( type, message ) {
		const $message = $( '<div>' )
			.addClass( type + '-message' )
			.text( message );

		$( '.form-messages' ).html( $message );

		// 3秒后自动隐藏消息
		setTimeout( function () {
			$message.fadeOut( function () {
				$( this ).remove();
			} );
		}, 3000 );
	}
} )( jQuery );
