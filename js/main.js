import { themeManager } from './theme/manager';
import { api } from './core/api';
import { isMobile } from './utils';
import { security } from './utils/security';

// 全局变量
const config = {
	apiBaseUrl: '/api',
	theme: {
		colorScheme: security.secureStorage.get( 'colorScheme' ) || 'default',
		darkMode: security.secureStorage.get( 'darkMode' ) === 'true',
	},
};

// 工具函数
const utils = {
	// 格式化日期
	formatDate( date ) {
		return new Date( date ).toLocaleDateString( 'zh-CN', {
			year: 'numeric',
			month: '2-digit',
			day: '2-digit',
			hour: '2-digit',
			minute: '2-digit',
		} );
	},

	// 格式化数字
	formatNumber( num ) {
		if ( num >= 10000 ) {
			return ( num / 10000 ).toFixed( 1 ) + '万';
		}
		return num.toString();
	},

	// 防抖函数
	debounce( fn, delay ) {
		let timer = null;
		return function ( ...args ) {
			if ( timer ) {
				clearTimeout( timer );
			}
			timer = setTimeout( () => {
				fn.apply( this, args );
			}, delay );
		};
	},

	// 节流函数
	throttle( fn, delay ) {
		let last = 0;
		return function ( ...args ) {
			const now = Date.now();
			if ( now - last > delay ) {
				fn.apply( this, args );
				last = now;
			}
		};
	},

	// 安全的剪贴板操作
	async copyToClipboard( text ) {
		try {
			await navigator.clipboard.writeText( text );
			return true;
		} catch ( err ) {
			console.error( '复制失败:', err );
			return false;
		}
	},
};

// 按钮无障碍与交互反馈
function enhanceButtonAccessibility() {
	document.querySelectorAll( '.btn' ).forEach( ( btn ) => {
		btn.setAttribute( 'tabindex', '0' );
		btn.setAttribute( 'role', 'button' );
		btn.addEventListener( 'keydown', ( e ) => {
			if ( ( e.key === 'Enter' || e.key === ' ' ) && ! btn.disabled ) {
				btn.click();
			}
		} );
	} );
}

// RTL支持
function enhanceRTL() {
	if ( document.documentElement.getAttribute( 'dir' ) === 'rtl' ) {
		document.body.classList.add( 'rtl-enabled' );
	}
}

// 错误处理
window.onerror = function ( message, source, lineno, colno, error ) {
	console.error( '全局错误:', {
		message,
		source,
		lineno,
		colno,
		error,
	} );
	return true;
};

// 初始化应用
document.addEventListener( 'DOMContentLoaded', () => {
	// 初始化主题
	themeManager.init();

	// 初始化移动端菜单
	if ( isMobile() ) {
		const menuToggle = document.querySelector( '.menu-toggle' );
		if ( menuToggle ) {
			menuToggle.addEventListener( 'click', () => {
				document
					.querySelector( '.nav-menu' )
					.classList.toggle( 'active' );
			} );
		}
	}

	// 初始化按钮无障碍
	enhanceButtonAccessibility();

	// 初始化RTL支持
	enhanceRTL();
} );

// 导出模块
export { themeManager, api };
