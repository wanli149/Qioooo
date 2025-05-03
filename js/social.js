/**
 * 社交媒体按钮交互功能
 * @param $
 */
( function ( $ ) {
	'use strict';

	// 初始化
	$( document ).ready( function () {
		initSocialShare();
		initSocialFollow();
		initWechatModal();
	} );

	/**
	 * 初始化社交媒体分享功能
	 */
	function initSocialShare() {
		// 微信分享按钮点击事件
		$( '.wechat-share-btn' ).on( 'click', function ( e ) {
			e.preventDefault();
			const title = $( this ).data( 'title' );
			const url = $( this ).data( 'url' );
			showWechatModal( title, url );
		} );

		// 微博分享
		$( '.weibo-share' ).on( 'click', function ( e ) {
			e.preventDefault();
			const url = $( this ).find( 'a' ).attr( 'href' );
			window.open( url, '_blank' );
		} );

		// QQ空间分享
		$( '.qzone-share' ).on( 'click', function ( e ) {
			e.preventDefault();
			const url = $( this ).find( 'a' ).attr( 'href' );
			window.open( url, '_blank' );
		} );

		// 豆瓣分享
		$( '.douban-share' ).on( 'click', function ( e ) {
			e.preventDefault();
			const url = $( this ).find( 'a' ).attr( 'href' );
			window.open( url, '_blank' );
		} );
	}

	/**
	 * 初始化社交媒体关注功能
	 */
	function initSocialFollow() {
		$( '.social-follow-buttons a' ).on( 'click', function ( e ) {
			e.preventDefault();
			const url = $( this ).attr( 'href' );
			window.open( url, '_blank' );
		} );
	}

	/**
	 * 初始化微信二维码弹窗
	 */
	function initWechatModal() {
		// 关闭按钮点击事件
		$( '.close-modal' ).on( 'click', function () {
			$( '.wechat-qrcode-modal' ).fadeOut();
		} );

		// 点击弹窗外部关闭
		$( '.wechat-qrcode-modal' ).on( 'click', function ( e ) {
			if ( $( e.target ).hasClass( 'wechat-qrcode-modal' ) ) {
				$( this ).fadeOut();
			}
		} );

		// ESC键关闭
		$( document ).on( 'keydown', function ( e ) {
			if ( e.key === 'Escape' ) {
				$( '.wechat-qrcode-modal' ).fadeOut();
			}
		} );
	}

	/**
	 * 显示微信二维码弹窗
	 * @param title
	 * @param url
	 */
	function showWechatModal( title, url ) {
		// 生成二维码
		const qrcode = new QRCode( 'qrcode', {
			text: url,
			width: 200,
			height: 200,
			colorDark: '#000000',
			colorLight: '#ffffff',
			correctLevel: QRCode.CorrectLevel.H,
		} );

		// 显示弹窗
		$( '.wechat-qrcode-modal' ).fadeIn();
	}

	/**
	 * 分享统计
	 * @param platform
	 */
	function trackShare( platform ) {
		$.ajax( {
			url: qioooo.ajax_url,
			type: 'POST',
			data: {
				action: 'track_share',
				platform,
				post_id: qioooo.post_id,
				nonce: qioooo.nonce,
			},
			success( response ) {
				if ( response.success ) {
					console.log( '分享统计成功' );
				}
			},
		} );
	}

	/**
	 * 关注统计
	 * @param platform
	 */
	function trackFollow( platform ) {
		$.ajax( {
			url: qioooo.ajax_url,
			type: 'POST',
			data: {
				action: 'track_follow',
				platform,
				nonce: qioooo.nonce,
			},
			success( response ) {
				if ( response.success ) {
					console.log( '关注统计成功' );
				}
			},
		} );
	}
} )( jQuery );
