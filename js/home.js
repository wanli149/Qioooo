// 首页功能
const home = {
	// 初始化
	init() {
		this.initHeroSlider();
		this.initArticleGrid();
		this.initSidebar();
		this.initScrollToTop();
	},

	// 初始化Hero轮播
	initHeroSlider() {
		const slider = document.querySelector( '.hero-slider' );
		if ( ! slider ) {
			return;
		}

		let currentSlide = 0;
		const slides = slider.querySelectorAll( '.slide' );
		const totalSlides = slides.length;

		// 自动轮播
		setInterval( () => {
			currentSlide = ( currentSlide + 1 ) % totalSlides;
			this.updateSlider( slider, slides, currentSlide );
		}, 5000 );

		// 手动切换
		const prevBtn = slider.querySelector( '.prev-slide' );
		const nextBtn = slider.querySelector( '.next-slide' );

		if ( prevBtn ) {
			prevBtn.addEventListener( 'click', () => {
				currentSlide = ( currentSlide - 1 + totalSlides ) % totalSlides;
				this.updateSlider( slider, slides, currentSlide );
			} );
		}

		if ( nextBtn ) {
			nextBtn.addEventListener( 'click', () => {
				currentSlide = ( currentSlide + 1 ) % totalSlides;
				this.updateSlider( slider, slides, currentSlide );
			} );
		}
	},

	// 更新轮播状态
	updateSlider( slider, slides, currentSlide ) {
		slides.forEach( ( slide, index ) => {
			slide.style.transform = `translateX(${
				( index - currentSlide ) * 100
			}%)`;
		} );
	},

	// 初始化文章网格
	initArticleGrid() {
		const grid = document.querySelector( '.article-grid' );
		if ( ! grid ) {
			return;
		}

		// 加载更多文章
		const loadMoreBtn = document.querySelector( '.load-more' );
		if ( loadMoreBtn ) {
			loadMoreBtn.addEventListener( 'click', async () => {
				try {
					const currentPage =
						parseInt( loadMoreBtn.dataset.page ) || 1;
					const response = await api.get( '/articles', {
						page: currentPage + 1,
					} );

					if ( response.articles.length > 0 ) {
						response.articles.forEach( ( article ) => {
							const articleCard =
								this.createArticleCard( article );
							grid.appendChild( articleCard );
						} );

						loadMoreBtn.dataset.page = currentPage + 1;

						if ( ! response.hasMore ) {
							loadMoreBtn.style.display = 'none';
						}
					}
				} catch ( error ) {
					console.error( '加载更多文章失败:', error );
				}
			} );
		}

		// 文章卡片点击事件
		grid.addEventListener( 'click', ( e ) => {
			const articleCard = e.target.closest( '.article-card' );
			if ( articleCard ) {
				const articleId = articleCard.dataset.id;
				window.location.href = `/article.html?id=${ articleId }`;
			}
		} );
	},

	// 创建文章卡片
	createArticleCard( article ) {
		const card = document.createElement( 'div' );
		card.className = 'article-card';
		card.dataset.id = article.id;

		card.innerHTML = `
            <div class="article-image">
                <img src="${ article.cover }" alt="${ article.title }">
            </div>
            <div class="article-content">
                <h3>${ article.title }</h3>
                <div class="article-meta">
                    <span class="author">${ article.author }</span>
                    <span class="date">${ utils.formatDate(
						article.date
					) }</span>
                    <span class="views">${ utils.formatNumber(
						article.views
					) } 阅读</span>
                </div>
                <p class="excerpt">${ article.excerpt }</p>
                <div class="article-tags">
                    ${ article.tags
						.map( ( tag ) => `<span class="tag">${ tag }</span>` )
						.join( '' ) }
                </div>
            </div>
        `;

		return card;
	},

	// 初始化侧边栏
	initSidebar() {
		// 加载热门文章
		this.loadHotArticles();

		// 加载标签云
		this.loadTagCloud();
	},

	// 加载热门文章
	async loadHotArticles() {
		const hotArticles = document.querySelector( '.hot-articles' );
		if ( ! hotArticles ) {
			return;
		}

		try {
			const response = await api.get( '/articles/hot' );
			const list = hotArticles.querySelector( 'ul' );

			response.articles.forEach( ( article ) => {
				const li = document.createElement( 'li' );
				li.innerHTML = `
                    <a href="/article.html?id=${ article.id }">
                        <span class="title">${ article.title }</span>
                        <span class="views">${ utils.formatNumber(
							article.views
						) }</span>
                    </a>
                `;
				list.appendChild( li );
			} );
		} catch ( error ) {
			console.error( '加载热门文章失败:', error );
		}
	},

	// 加载标签云
	async loadTagCloud() {
		const tagCloud = document.querySelector( '.tag-cloud' );
		if ( ! tagCloud ) {
			return;
		}

		try {
			const response = await api.get( '/tags' );
			const container = tagCloud.querySelector( '.tags' );

			response.tags.forEach( ( tag ) => {
				const span = document.createElement( 'span' );
				span.className = 'tag';
				span.style.fontSize = `${ 12 + tag.count * 0.5 }px`;
				span.textContent = tag.name;
				span.addEventListener( 'click', () => {
					window.location.href = `/articles.html?tag=${ tag.name }`;
				} );
				container.appendChild( span );
			} );
		} catch ( error ) {
			console.error( '加载标签云失败:', error );
		}
	},

	// 初始化返回顶部按钮
	initScrollToTop() {
		const scrollTopBtn = document.querySelector( '.scroll-top' );
		if ( ! scrollTopBtn ) {
			return;
		}

		// 监听滚动
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

		// 点击返回顶部
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
	home.init();
} );
