( function ( $ ) {
	'use strict';

	$( document ).ready( function () {
		initRecentPosts();
		initPopularPosts();
		initCategories();
		initTags();
		initSearch();
		initAbout();
		initSocialLinks();
		initNewsletter();
		initArchives();
		initCalendar();
		initRecentComments();
		initRandomPosts();
	} );

	// 最近文章小工具
	function initRecentPosts() {
		$( '.recent-post-item' ).each( function () {
			const $item = $( this );
			const $thumbnail = $item.find( '.post-thumbnail' );
			const $title = $item.find( '.post-title a' );

			// 添加悬停效果
			$item.hover(
				function () {
					$thumbnail.addClass( 'hover' );
					$title.addClass( 'hover' );
				},
				function () {
					$thumbnail.removeClass( 'hover' );
					$title.removeClass( 'hover' );
				}
			);
		} );
	}

	// 热门文章小工具
	function initPopularPosts() {
		$( '.popular-post-item' ).each( function () {
			const $item = $( this );
			const $thumbnail = $item.find( '.post-thumbnail' );
			const $title = $item.find( '.post-title a' );

			// 添加悬停效果
			$item.hover(
				function () {
					$thumbnail.addClass( 'hover' );
					$title.addClass( 'hover' );
				},
				function () {
					$thumbnail.removeClass( 'hover' );
					$title.removeClass( 'hover' );
				}
			);
		} );
	}

	// 分类目录小工具
	function initCategories() {
		$( '.category-item a' ).each( function () {
			const $link = $( this );

			// 添加点击效果
			$link.on( 'click', function ( e ) {
				e.preventDefault();
				const url = $( this ).attr( 'href' );
				window.location.href = url;
			} );
		} );
	}

	// 标签云小工具
	function initTags() {
		$( '.tagcloud a' ).each( function () {
			const $tag = $( this );

			// 添加点击效果
			$tag.on( 'click', function ( e ) {
				e.preventDefault();
				const url = $( this ).attr( 'href' );
				window.location.href = url;
			} );
		} );
	}

	// 搜索小工具
	function initSearch() {
		const $searchForm = $( '.search-form' );
		const $searchField = $searchForm.find( '.search-field input' );
		const $searchSubmit = $searchForm.find( '.search-submit' );

		// 添加搜索功能
		$searchForm.on( 'submit', function ( e ) {
			e.preventDefault();
			const searchTerm = $searchField.val().trim();

			if ( searchTerm ) {
				const searchUrl =
					$searchForm.attr( 'action' ) +
					'?s=' +
					encodeURIComponent( searchTerm );
				window.location.href = searchUrl;
			}
		} );

		// 添加输入提示
		$searchField
			.on( 'focus', function () {
				$( this ).addClass( 'focused' );
			} )
			.on( 'blur', function () {
				$( this ).removeClass( 'focused' );
			} );
	}

	// 关于我们小工具
	function initAbout() {
		const $aboutWidget = $( '.about-widget' );
		const $avatar = $aboutWidget.find( '.about-avatar img' );

		// 添加头像悬停效果
		$avatar.hover(
			function () {
				$( this ).addClass( 'hover' );
			},
			function () {
				$( this ).removeClass( 'hover' );
			}
		);
	}

	// 社交媒体链接小工具
	function initSocialLinks() {
		$( '.social-link' ).each( function () {
			const $link = $( this );

			// 添加点击效果
			$link.on( 'click', function ( e ) {
				e.preventDefault();
				const url = $( this ).attr( 'href' );
				window.open( url, '_blank' );
			} );
		} );
	}

	// 订阅小工具
	function initNewsletter() {
		const $newsletterForm = $( '.newsletter-form' );
		const $newsletterInput = $newsletterForm.find(
			'.newsletter-input input'
		);
		const $newsletterSubmit = $newsletterForm.find( '.newsletter-submit' );

		// 添加订阅功能
		$newsletterForm.on( 'submit', function ( e ) {
			e.preventDefault();
			const email = $newsletterInput.val().trim();

			if ( email ) {
				// 显示加载动画
				$newsletterSubmit.addClass( 'loading' );

				// 发送AJAX请求
				$.ajax( {
					url: qioooo_ajax.ajax_url,
					type: 'POST',
					data: {
						action: 'qioooo_newsletter_subscribe',
						nonce: $newsletterForm
							.find( 'input[name="newsletter_nonce"]' )
							.val(),
						email,
					},
					success( response ) {
						// 移除加载动画
						$newsletterSubmit.removeClass( 'loading' );

						if ( response.success ) {
							// 显示成功消息
							$newsletterForm.append(
								'<div class="newsletter-success">' +
									response.data +
									'</div>'
							);

							// 清空输入框
							$newsletterInput.val( '' );

							// 3秒后移除成功消息
							setTimeout( function () {
								$( '.newsletter-success' ).remove();
							}, 3000 );
						} else {
							// 显示错误消息
							$newsletterForm.append(
								'<div class="newsletter-error">' +
									response.data +
									'</div>'
							);

							// 3秒后移除错误消息
							setTimeout( function () {
								$( '.newsletter-error' ).remove();
							}, 3000 );
						}
					},
					error() {
						// 移除加载动画
						$newsletterSubmit.removeClass( 'loading' );

						// 显示错误消息
						$newsletterForm.append(
							'<div class="newsletter-error">' +
								qioooo_ajax.error_message +
								'</div>'
						);

						// 3秒后移除错误消息
						setTimeout( function () {
							$( '.newsletter-error' ).remove();
						}, 3000 );
					},
				} );
			}
		} );

		// 添加输入提示
		$newsletterInput
			.on( 'focus', function () {
				$( this ).addClass( 'focused' );
			} )
			.on( 'blur', function () {
				$( this ).removeClass( 'focused' );
			} );
	}

	// 归档小工具
	function initArchives() {
		$( '.archives-list a' ).each( function () {
			const $link = $( this );

			// 添加点击效果
			$link.on( 'click', function ( e ) {
				e.preventDefault();
				const url = $( this ).attr( 'href' );
				window.location.href = url;
			} );
		} );
	}

	// 日历小工具
	function initCalendar() {
		const $calendar = $( '.calendar-widget' );
		const $calendarLinks = $calendar.find( 'a' );

		// 添加点击效果
		$calendarLinks.on( 'click', function ( e ) {
			e.preventDefault();
			const url = $( this ).attr( 'href' );
			window.location.href = url;
		} );

		// 添加悬停效果
		$calendarLinks.hover(
			function () {
				$( this ).addClass( 'hover' );
			},
			function () {
				$( this ).removeClass( 'hover' );
			}
		);
	}

	// 最新评论小工具
	function initRecentComments() {
		$( '.recent-comment-item' ).each( function () {
			const $item = $( this );
			const $avatar = $item.find( '.comment-avatar img' );
			const $excerpt = $item.find( '.comment-excerpt' );
			const $meta = $item.find( '.comment-meta a' );

			// 添加头像悬停效果
			$avatar.hover(
				function () {
					$( this ).addClass( 'hover' );
				},
				function () {
					$( this ).removeClass( 'hover' );
				}
			);

			// 添加评论内容悬停效果
			$excerpt.hover(
				function () {
					$( this ).addClass( 'hover' );
				},
				function () {
					$( this ).removeClass( 'hover' );
				}
			);

			// 添加元数据点击效果
			$meta.on( 'click', function ( e ) {
				e.preventDefault();
				const url = $( this ).attr( 'href' );
				window.location.href = url;
			} );
		} );
	}

	// 随机文章小工具
	function initRandomPosts() {
		$( '.random-post-item' ).each( function () {
			const $item = $( this );
			const $thumbnail = $item.find( '.post-thumbnail' );
			const $title = $item.find( '.post-title a' );

			// 添加悬停效果
			$item.hover(
				function () {
					$thumbnail.addClass( 'hover' );
					$title.addClass( 'hover' );
				},
				function () {
					$thumbnail.removeClass( 'hover' );
					$title.removeClass( 'hover' );
				}
			);
		} );
	}
} )( jQuery );
