( function ( $ ) {
	'use strict';

	$( document ).ready( function () {
		// 初始化导航切换
		initNavigation();

		// 初始化表单提交
		initFormSubmit();
	} );

	// 初始化导航切换
	function initNavigation() {
		$( '.profile-nav a' ).on( 'click', function ( e ) {
			e.preventDefault();

			const target = $( this ).attr( 'href' );

			// 更新导航状态
			$( '.profile-nav li' ).removeClass( 'active' );
			$( this ).parent().addClass( 'active' );

			// 切换内容区域
			$( '.profile-section' ).removeClass( 'active' );
			$( target ).addClass( 'active' );
		} );
	}

	// 初始化表单提交
	function initFormSubmit() {
		// 个人信息表单
		$( '.profile-form' ).on( 'submit', function ( e ) {
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

		// 账号设置表单
		$( '.account-form' ).on( 'submit', function ( e ) {
			e.preventDefault();

			const $form = $( this );
			const $submitButton = $form.find( 'button[type="submit"]' );
			const formData = new FormData( $form[ 0 ] );

			// 验证密码
			const newPassword = $( '#new_password' ).val();
			const confirmPassword = $( '#confirm_password' ).val();

			if ( newPassword !== confirmPassword ) {
				showMessage( 'error', '两次输入的密码不一致' );
				return;
			}

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
						$form[ 0 ].reset();
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

		$( '.profile-content' ).prepend( $message );

		// 3秒后自动隐藏消息
		setTimeout( function () {
			$message.fadeOut( function () {
				$( this ).remove();
			} );
		}, 3000 );
	}
} )( jQuery );
