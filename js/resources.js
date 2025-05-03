// 资源页面功能
const resources = {
	// 初始化
	init() {
		this.initFilters();
		this.initResourceGrid();
		this.initScrollToTop();
	},

	// 初始化筛选器
	initFilters() {
		const filters = document.querySelector( '.resource-filters' );
		if ( ! filters ) {
			return;
		}

		// 排序方式
		const sortSelect = filters.querySelector( '.sort-select' );
		if ( sortSelect ) {
			sortSelect.addEventListener( 'change', () => {
				this.loadResources();
			} );
		}

		// 格式标签
		const formatTags = filters.querySelectorAll( '.format-tag' );
		formatTags.forEach( ( tag ) => {
			tag.addEventListener( 'click', () => {
				tag.classList.toggle( 'active' );
				this.loadResources();
			} );
		} );
	},

	// 初始化资源网格
	initResourceGrid() {
		const grid = document.querySelector( '.resource-grid' );
		if ( ! grid ) {
			return;
		}

		// 加载资源
		this.loadResources();

		// 加载更多按钮
		const loadMoreBtn = document.querySelector( '.load-more' );
		if ( loadMoreBtn ) {
			loadMoreBtn.addEventListener( 'click', () => {
				this.loadMoreResources();
			} );
		}
	},

	// 加载资源
	async loadResources() {
		const grid = document.querySelector( '.resource-grid' );
		if ( ! grid ) {
			return;
		}

		try {
			// 获取筛选条件
			const sortBy =
				document.querySelector( '.sort-select' )?.value || 'latest';
			const formats = Array.from(
				document.querySelectorAll( '.format-tag.active' )
			).map( ( tag ) => tag.dataset.format );

			// 加载资源
			const response = await api.get( '/resources', {
				sortBy,
				formats: formats.join( ',' ),
			} );

			// 清空网格
			grid.innerHTML = '';

			// 添加资源卡片
			response.resources.forEach( ( resource ) => {
				const card = this.createResourceCard( resource );
				grid.appendChild( card );
			} );

			// 更新加载更多按钮
			const loadMoreBtn = document.querySelector( '.load-more' );
			if ( loadMoreBtn ) {
				loadMoreBtn.style.display = response.hasMore ? 'block' : 'none';
				loadMoreBtn.dataset.page = '1';
			}
		} catch ( error ) {
			console.error( '加载资源失败:', error );
		}
	},

	// 加载更多资源
	async loadMoreResources() {
		const loadMoreBtn = document.querySelector( '.load-more' );
		if ( ! loadMoreBtn ) {
			return;
		}

		try {
			const currentPage = parseInt( loadMoreBtn.dataset.page ) || 1;
			const sortBy =
				document.querySelector( '.sort-select' )?.value || 'latest';
			const formats = Array.from(
				document.querySelectorAll( '.format-tag.active' )
			).map( ( tag ) => tag.dataset.format );

			const response = await api.get( '/resources', {
				page: currentPage + 1,
				sortBy,
				formats: formats.join( ',' ),
			} );

			const grid = document.querySelector( '.resource-grid' );
			if ( grid ) {
				response.resources.forEach( ( resource ) => {
					const card = this.createResourceCard( resource );
					grid.appendChild( card );
				} );
			}

			loadMoreBtn.dataset.page = currentPage + 1;
			loadMoreBtn.style.display = response.hasMore ? 'block' : 'none';
		} catch ( error ) {
			console.error( '加载更多资源失败:', error );
		}
	},

	// 创建资源卡片
	createResourceCard( resource ) {
		const card = document.createElement( 'div' );
		card.className = 'resource-card';
		card.dataset.id = resource.id;

		card.innerHTML = `
            <div class="resource-image">
                <img src="${ resource.cover }" alt="${ resource.title }">
            </div>
            <div class="resource-content">
                <h3>${ resource.title }</h3>
                <div class="resource-meta">
                    <span class="uploader">${ resource.uploader }</span>
                    <span class="date">${ utils.formatDate(
						resource.date
					) }</span>
                    <span class="downloads">${ utils.formatNumber(
						resource.downloads
					) } 下载</span>
                </div>
                <p class="description">${ resource.description }</p>
                <div class="resource-info">
                    <span class="size">${ resource.size }</span>
                    <span class="format">${ resource.format }</span>
                    <div class="rating">
                        ${ this.createRatingStars( resource.rating ) }
                    </div>
                </div>
                <div class="resource-actions">
                    <button class="btn-download" data-resource-id="${
						resource.id
					}">
                        <i class="fas fa-download"></i> 下载
                    </button>
                    <button class="btn-preview" data-resource-id="${
						resource.id
					}">
                        <i class="fas fa-eye"></i> 预览
                    </button>
                </div>
            </div>
        `;

		// 绑定下载和预览事件
		const downloadBtn = card.querySelector( '.btn-download' );
		if ( downloadBtn ) {
			downloadBtn.addEventListener( 'click', () => {
				this.downloadResource( resource.id );
			} );
		}

		const previewBtn = card.querySelector( '.btn-preview' );
		if ( previewBtn ) {
			previewBtn.addEventListener( 'click', () => {
				this.previewResource( resource.id );
			} );
		}

		return card;
	},

	// 创建评分星星
	createRatingStars( rating ) {
		const stars = [];
		for ( let i = 1; i <= 5; i++ ) {
			const starClass = i <= rating ? 'fas fa-star' : 'far fa-star';
			stars.push( `<i class="${ starClass }"></i>` );
		}
		return stars.join( '' );
	},

	// 下载资源
	async downloadResource( resourceId ) {
		try {
			const response = await api.post(
				`/resources/${ resourceId }/download`
			);
			if ( response.success ) {
				window.location.href = response.downloadUrl;
			}
		} catch ( error ) {
			console.error( '下载资源失败:', error );
		}
	},

	// 预览资源
	async previewResource( resourceId ) {
		try {
			const response = await api.get(
				`/resources/${ resourceId }/preview`
			);
			if ( response.success ) {
				// 显示预览模态框
				const modal = document.querySelector( '.modal-preview' );
				if ( modal ) {
					modal.classList.add( 'show' );

					// 根据资源类型显示不同的预览内容
					const previewContent =
						modal.querySelector( '.preview-content' );
					if ( previewContent ) {
						switch ( response.type ) {
							case 'image':
								previewContent.innerHTML = `<img src="${ response.url }" alt="预览">`;
								break;
							case 'video':
								previewContent.innerHTML = `
                                    <video controls>
                                        <source src="${ response.url }" type="video/mp4">
                                    </video>
                                `;
								break;
							case 'document':
								previewContent.innerHTML = `
                                    <iframe src="${ response.url }"></iframe>
                                `;
								break;
						}
					}
				}
			}
		} catch ( error ) {
			console.error( '预览资源失败:', error );
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
	resources.init();
} );
