// 文章页面功能
const article = {
	// 初始化
	init() {
		this.loadArticle();
		this.initAuthorBox();
		this.initComments();
		this.initShare();
		this.initReward();
		this.initScrollToTop();
	},

	// 加载文章内容
	async loadArticle() {
		const articleId = new URLSearchParams( window.location.search ).get(
			'id'
		);
		if ( ! articleId ) {
			window.location.href = '/';
			return;
		}

		try {
			const response = await api.get( `/articles/${ articleId }` );
			this.renderArticle( response.article );
		} catch ( error ) {
			console.error( '加载文章失败:', error );
		}
	},

	// 渲染文章内容
	renderArticle( article ) {
		// 更新页面标题
		document.title = `${ article.title } - QIoooo`;

		// 更新文章头部
		const header = document.querySelector( '.article-header' );
		if ( header ) {
			header.querySelector( 'h1' ).textContent = article.title;
			header.querySelector( '.author' ).textContent = article.author;
			header.querySelector( '.date' ).textContent = utils.formatDate(
				article.date
			);
			header.querySelector( '.views' ).textContent = utils.formatNumber(
				article.views
			);

			const tags = header.querySelector( '.tags' );
			tags.innerHTML = article.tags
				.map( ( tag ) => `<span class="tag">${ tag }</span>` )
				.join( '' );
		}

		// 更新文章内容
		const content = document.querySelector( '.article-body' );
		if ( content ) {
			content.innerHTML = article.content;
		}

		// 更新作者信息
		const authorBox = document.querySelector( '.author-box' );
		if ( authorBox ) {
			authorBox.querySelector( '.avatar' ).src = article.authorAvatar;
			authorBox.querySelector( '.name' ).textContent = article.author;
			authorBox.querySelector( '.bio' ).textContent = article.authorBio;

			const socialLinks = authorBox.querySelector( '.social-links' );
			if ( socialLinks ) {
				socialLinks.innerHTML = `
                    <a href="${ article.authorWeibo }" target="_blank"><i class="fab fa-weibo"></i></a>
                    <a href="${ article.authorWechat }" target="_blank"><i class="fab fa-weixin"></i></a>
                    <a href="${ article.authorQQ }" target="_blank"><i class="fab fa-qq"></i></a>
                `;
			}
		}
	},

	// 初始化作者信息框
	initAuthorBox() {
		const authorBox = document.querySelector( '.author-box' );
		if ( ! authorBox ) {
			return;
		}

		// 关注按钮
		const followBtn = authorBox.querySelector( '.btn-follow' );
		if ( followBtn ) {
			followBtn.addEventListener( 'click', async () => {
				try {
					const response = await api.post( '/follow', {
						authorId: followBtn.dataset.authorId,
					} );

					if ( response.success ) {
						followBtn.textContent = response.isFollowing
							? '已关注'
							: '关注';
						followBtn.classList.toggle(
							'active',
							response.isFollowing
						);
					}
				} catch ( error ) {
					console.error( '关注操作失败:', error );
				}
			} );
		}
	},

	// 初始化评论区
	initComments() {
		const commentsSection = document.querySelector( '.comments-section' );
		if ( ! commentsSection ) {
			return;
		}

		// 加载评论
		this.loadComments();

		// 评论表单提交
		const commentForm = commentsSection.querySelector( '.comment-form' );
		if ( commentForm ) {
			commentForm.addEventListener( 'submit', async ( e ) => {
				e.preventDefault();

				const content = commentForm
					.querySelector( 'textarea' )
					.value.trim();
				if ( ! content ) {
					return;
				}

				try {
					const response = await api.post( '/comments', {
						articleId: new URLSearchParams(
							window.location.search
						).get( 'id' ),
						content,
					} );

					if ( response.success ) {
						commentForm.reset();
						this.loadComments();
					}
				} catch ( error ) {
					console.error( '发表评论失败:', error );
				}
			} );
		}
	},

	// 加载评论
	async loadComments() {
		const commentsList = document.querySelector( '.comments-list' );
		if ( ! commentsList ) {
			return;
		}

		try {
			const response = await api.get( '/comments', {
				articleId: new URLSearchParams( window.location.search ).get(
					'id'
				),
			} );

			commentsList.innerHTML = response.comments
				.map(
					( comment ) => `
                <div class="comment" data-id="${ comment.id }">
                    <div class="comment-header">
                        <img class="avatar" src="${
							comment.userAvatar
						}" alt="${ comment.userName }">
                        <span class="username">${ comment.userName }</span>
                        <span class="date">${ utils.formatDate(
							comment.date
						) }</span>
                    </div>
                    <div class="comment-content">${ comment.content }</div>
                    <div class="comment-actions">
                        <button class="btn-reply" data-comment-id="${
							comment.id
						}">回复</button>
                        <button class="btn-like" data-comment-id="${
							comment.id
						}">
                            <i class="far fa-heart"></i>
                            <span>${ utils.formatNumber(
								comment.likes
							) }</span>
                        </button>
                    </div>
                </div>
            `
				)
				.join( '' );

			// 绑定评论操作事件
			this.bindCommentActions();
		} catch ( error ) {
			console.error( '加载评论失败:', error );
		}
	},

	// 绑定评论操作事件
	bindCommentActions() {
		// 回复按钮
		const replyButtons = document.querySelectorAll( '.btn-reply' );
		replyButtons.forEach( ( btn ) => {
			btn.addEventListener( 'click', () => {
				const commentId = btn.dataset.commentId;
				const commentForm = document.querySelector( '.comment-form' );
				if ( commentForm ) {
					commentForm.dataset.replyTo = commentId;
					commentForm.querySelector( 'textarea' ).focus();
				}
			} );
		} );

		// 点赞按钮
		const likeButtons = document.querySelectorAll( '.btn-like' );
		likeButtons.forEach( ( btn ) => {
			btn.addEventListener( 'click', async () => {
				const commentId = btn.dataset.commentId;
				try {
					const response = await api.post( '/comments/like', {
						commentId,
					} );
					if ( response.success ) {
						const icon = btn.querySelector( 'i' );
						const count = btn.querySelector( 'span' );

						icon.classList.toggle( 'far' );
						icon.classList.toggle( 'fas' );
						count.textContent = utils.formatNumber(
							response.likes
						);
					}
				} catch ( error ) {
					console.error( '点赞操作失败:', error );
				}
			} );
		} );
	},

	// 初始化分享功能
	initShare() {
		const shareButtons = document.querySelectorAll( '.btn-share' );
		shareButtons.forEach( ( btn ) => {
			btn.addEventListener( 'click', () => {
				const platform = btn.dataset.platform;
				const url = window.location.href;
				const title = document.title;

				switch ( platform ) {
					case 'wechat':
						this.showWechatQRCode( url );
						break;
					case 'weibo':
						window.open(
							`http://service.weibo.com/share/share.php?url=${ encodeURIComponent(
								url
							) }&title=${ encodeURIComponent( title ) }`
						);
						break;
					case 'qq':
						window.open(
							`http://connect.qq.com/widget/shareqq/index.html?url=${ encodeURIComponent(
								url
							) }&title=${ encodeURIComponent( title ) }`
						);
						break;
				}
			} );
		} );
	},

	// 显示微信二维码
	showWechatQRCode( url ) {
		const modal = document.querySelector( '.modal-wechat' );
		if ( modal ) {
			modal.classList.add( 'show' );

			// 生成二维码
			const qrcode = modal.querySelector( '.qrcode' );
			if ( qrcode ) {
				// 这里可以使用第三方库生成二维码
				// 例如: new QRCode(qrcode, url);
			}
		}
	},

	// 初始化打赏功能
	initReward() {
		const rewardBtn = document.querySelector( '.btn-reward' );
		if ( rewardBtn ) {
			rewardBtn.addEventListener( 'click', () => {
				const modal = document.querySelector( '.modal-reward' );
				if ( modal ) {
					modal.classList.add( 'show' );
				}
			} );
		}
	},

	// 初始化返回顶部按钮
	initScrollToTop() {
		const scrollTopBtn = document.querySelector( '.scroll-top' );
		if ( ! scrollTopBtn ) {
			return;
		}

		window.addEventListener(
			'scroll',
			utils.throttle( () => {
				if ( window.scrollY > 300 ) {
					scrollTopBtn.classList.add( 'show' );
				} else {
					scrollTopBtn.classList.remove( 'show' );
				}
			}, 200 )
		);

		scrollTopBtn.addEventListener( 'click', () => {
			window.scrollTo( {
				top: 0,
				behavior: 'smooth',
			} );
		} );
	},
};

// 初始化
document.addEventListener( 'DOMContentLoaded', () => {
	article.init();
} );
