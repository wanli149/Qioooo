/* 背景特效脚本 */

// 创建粒子效果
function createParticles() {
	const container = document.querySelector( '.bg-effect-particles' );
	if ( ! container ) {
		return;
	}

	const particleCount = 50;
	for ( let i = 0; i < particleCount; i++ ) {
		const particle = document.createElement( 'div' );
		particle.className = 'particle';

		// 随机位置
		particle.style.left = Math.random() * 100 + '%';
		particle.style.top = Math.random() * 100 + '%';

		// 随机动画延迟
		particle.style.animationDelay = Math.random() * 15 + 's';

		container.appendChild( particle );
	}
}

// 检查节日并应用黑白效果
function checkHoliday() {
	const today = new Date();
	const month = today.getMonth() + 1;
	const day = today.getDate();

	// 重大节日日期
	const holidays = [
		{ month: 4, day: 4 }, // 清明节
		{ month: 5, day: 12 }, // 汶川地震纪念日
		{ month: 7, day: 7 }, // 七七事变纪念日
		{ month: 9, day: 18 }, // 九一八事变纪念日
		{ month: 12, day: 13 }, // 南京大屠杀纪念日
	];

	const isHoliday = holidays.some(
		( holiday ) => holiday.month === month && holiday.day === day
	);

	if ( isHoliday ) {
		document.body.classList.add( 'holiday-mode' );
	}
}

// 初始化背景特效
function initBackgroundEffect() {
	// 获取主题设置
	const settings = qiooooSettings.themeSettings || {};
	const bgEffect = settings.backgroundEffect || 'none';

	// 移除所有背景效果
	document.body.classList.remove(
		'bg-effect-gradient-wave',
		'bg-effect-grid',
		'bg-effect-particles'
	);

	// 应用选中的背景效果
	if ( bgEffect !== 'none' ) {
		document.body.classList.add( bgEffect );

		// 如果是粒子效果，创建粒子
		if ( bgEffect === 'bg-effect-particles' ) {
			createParticles();
		}
	}

	// 检查节日
	checkHoliday();
}

// 页面加载完成后初始化
document.addEventListener( 'DOMContentLoaded', initBackgroundEffect );
