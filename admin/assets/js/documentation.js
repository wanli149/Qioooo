jQuery( document ).ready( function ( $ ) {
	// 初始化标签页
	initTabs();

	// 初始化常见问题折叠
	initFAQAccordion();

	// 初始化平滑滚动
	initSmoothScroll();
} );

/**
 * 初始化标签页
 */
function initTabs() {
	const tabs = document.querySelectorAll( '.documentation-tabs .tab' );
	const tabContents = document.querySelectorAll( '.tab-content' );

	// 默认显示第一个标签页
	if ( tabs.length > 0 ) {
		tabs[ 0 ].classList.add( 'active' );
		tabContents[ 0 ].style.display = 'block';
	}

	// 添加标签页切换事件
	tabs.forEach( ( tab ) => {
		tab.addEventListener( 'click', function () {
			// 移除所有标签页的 active 类
			tabs.forEach( ( t ) => t.classList.remove( 'active' ) );

			// 隐藏所有内容
			tabContents.forEach( ( content ) => {
				content.style.display = 'none';
			} );

			// 添加当前标签页的 active 类
			this.classList.add( 'active' );

			// 显示当前内容
			const content = this.querySelector( '.tab-content' );
			if ( content ) {
				content.style.display = 'block';
			}
		} );
	} );
}

/**
 * 初始化常见问题折叠
 */
function initFAQAccordion() {
	const faqItems = document.querySelectorAll( '.faq-item' );

	faqItems.forEach( ( item ) => {
		const title = item.querySelector( 'h4' );
		const content = item.querySelector( 'p' );

		if ( title && content ) {
			// 默认折叠所有内容
			content.style.display = 'none';

			// 添加点击事件
			title.addEventListener( 'click', function () {
				// 切换当前内容的显示状态
				if ( content.style.display === 'none' ) {
					content.style.display = 'block';
					this.classList.add( 'active' );
				} else {
					content.style.display = 'none';
					this.classList.remove( 'active' );
				}
			} );
		}
	} );
}

/**
 * 初始化平滑滚动
 */
function initSmoothScroll() {
	document.querySelectorAll( 'a[href^="#"]' ).forEach( ( anchor ) => {
		anchor.addEventListener( 'click', function ( e ) {
			e.preventDefault();

			const targetId = this.getAttribute( 'href' );
			const targetElement = document.querySelector( targetId );

			if ( targetElement ) {
				targetElement.scrollIntoView( {
					behavior: 'smooth',
					block: 'start',
				} );
			}
		} );
	} );
}

/**
 * 添加错误处理
 * @param msg
 * @param url
 * @param line
 */
window.onerror = function ( msg, url, line ) {
	console.error(
		'文档页面错误: ' + msg + '\nURL: ' + url + '\nLine: ' + line
	);
	return false;
};
