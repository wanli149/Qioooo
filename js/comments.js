( function ( $ ) {
	'use strict';

	// 评论系统配置
	const config = {
		formId: '#commentform',
		submitButton: '.submit',
		commentField: 'textarea[name="comment"]',
		postIdField: 'input[name="comment_post_ID"]',
		messageContainer: '.comment-message',
		floodInterval: 30000, // 30秒评论间隔
		maxRetries: 3,
		retryDelay: 1000,
	};

	// 评论状态
	const commentState = {
		isSubmitting: false,
		lastSubmitTime: 0,
		retryCount: 0,
	};

	// 初始化
	$( document ).ready( function () {
		initCommentForm();
		initCommentReply();
	} );

	// 初始化评论表单
	function initCommentForm() {
		const $form = $( config.formId );
		if ( ! $form.length ) {
			return;
		}

		$form.on( 'submit', handleCommentSubmit );
	}

	// 处理评论提交
	function handleCommentSubmit( e ) {
		e.preventDefault();

		if ( commentState.isSubmitting ) {
			return;
		}

		const $form = $( this );
		const $submit = $form.find( config.submitButton );
		const postId = $form.find( config.postIdField ).val();
		const comment = $form.find( config.commentField ).val();

		// 检查评论内容
		if ( ! comment.trim() ) {
			showMessage( 'error', '评论内容不能为空' );
			return;
		}

		// 检查评论频率
		const now = Date.now();
		if ( now - commentState.lastSubmitTime < config.floodInterval ) {
			showMessage( 'error', '评论提交过于频繁，请稍后再试' );
			return;
		}

		// 更新状态
		commentState.isSubmitting = true;
		$submit.prop( 'disabled', true );

		// 发送评论请求
		submitComment( postId, comment, $form, $submit );
	}

	// 提交评论
	function submitComment( postId, comment, $form, $submit ) {
		$.ajax( {
			url: qiooooSettings.ajaxurl,
			type: 'POST',
			data: {
				action: 'qioooo_submit_comment',
				nonce: qiooooSettings.nonce,
				post_id: postId,
				comment,
			},
			success( response ) {
				if ( response.success ) {
					handleSuccess( response, $form );
				} else {
					handleError( response.data.message );
				}
			},
			error( xhr, status, error ) {
				handleError( '评论提交失败，请稍后重试' );
				console.error( '评论提交错误:', error );
			},
			complete() {
				resetFormState( $submit );
			},
		} );
	}

	// 处理成功响应
	function handleSuccess( response, $form ) {
		showMessage( 'success', response.data.message );
		$form.find( config.commentField ).val( '' );
		commentState.lastSubmitTime = Date.now();

		// 延迟刷新页面，让用户看到成功消息
		setTimeout( () => {
			location.reload();
		}, 1500 );
	}

	// 处理错误
	function handleError( message ) {
		showMessage( 'error', message );

		// 重试逻辑
		if ( commentState.retryCount < config.maxRetries ) {
			commentState.retryCount++;
			setTimeout( () => {
				submitComment( ...arguments );
			}, config.retryDelay );
		}
	}

	// 重置表单状态
	function resetFormState( $submit ) {
		commentState.isSubmitting = false;
		$submit.prop( 'disabled', false );
	}

	// 显示消息
	function showMessage( type, message ) {
		let $container = $( config.messageContainer );
		if ( ! $container.length ) {
			$container = $(
				'<div class="comment-message"></div>'
			).insertBefore( config.formId );
		}

		$container
			.removeClass( 'success error' )
			.addClass( type )
			.text( message )
			.fadeIn()
			.delay( 3000 )
			.fadeOut();
	}

	// 初始化评论回复
	function initCommentReply() {
		const $replyLinks = $( '.comment-reply-link' );

		$replyLinks.on( 'click', function ( e ) {
			e.preventDefault();

			const $link = $( this );
			const commentId = $link.data( 'commentid' );
			const postId = $link.data( 'postid' );

			if ( ! commentId || ! postId ) {
				return;
			}

			// 加载回复表单
			loadReplyForm( commentId, postId, $link );
		} );
	}

	// 加载回复表单
	function loadReplyForm( commentId, postId, $link ) {
		$.ajax( {
			url: qiooooSettings.ajaxurl,
			type: 'POST',
			data: {
				action: 'qioooo_get_reply_form',
				nonce: qiooooSettings.nonce,
				comment_id: commentId,
				post_id: postId,
			},
			success( response ) {
				if ( response.success ) {
					insertReplyForm( response.data.form, $link );
				} else {
					showMessage( 'error', '加载回复表单失败' );
				}
			},
			error() {
				showMessage( 'error', '加载回复表单失败' );
			},
		} );
	}

	// 插入回复表单
	function insertReplyForm( formHtml, $link ) {
		const $replyContainer = $( '<div class="reply-form-container"></div>' );
		$replyContainer
			.html( formHtml )
			.insertAfter( $link.closest( '.reply' ) );

		// 初始化新表单
		initCommentForm();
	}
} )( jQuery );
