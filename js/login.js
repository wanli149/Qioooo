// 登录/注册页面功能
const auth = {
	// 初始化
	init() {
		this.initForms();
		this.initSocialLogin();
		this.initVerificationCode();
	},

	// 初始化表单
	initForms() {
		const loginForm = document.querySelector( '.login-form' );
		const registerForm = document.querySelector( '.register-form' );
		const switchLinks = document.querySelectorAll( '.switch-form' );

		if ( switchLinks.length > 0 ) {
			switchLinks.forEach( ( link ) => {
				link.addEventListener( 'click', ( e ) => {
					e.preventDefault();
					loginForm.classList.toggle( 'active' );
					registerForm.classList.toggle( 'active' );
				} );
			} );
		}

		// 登录表单提交
		if ( loginForm ) {
			loginForm.addEventListener( 'submit', async ( e ) => {
				e.preventDefault();

				const username = loginForm
					.querySelector( 'input[name="username"]' )
					.value.trim();
				const password = loginForm
					.querySelector( 'input[name="password"]' )
					.value.trim();
				const remember = loginForm.querySelector(
					'input[name="remember"]'
				).checked;

				if ( ! username || ! password ) {
					this.showError( '请填写完整的登录信息' );
					return;
				}

				try {
					const response = await api.post( '/login', {
						username,
						password,
						remember,
					} );

					if ( response.success ) {
						// 保存登录状态
						localStorage.setItem( 'token', response.token );
						localStorage.setItem(
							'user',
							JSON.stringify( response.user )
						);

						// 跳转到首页
						window.location.href = '/';
					} else {
						this.showError( response.message );
					}
				} catch ( error ) {
					console.error( '登录失败:', error );
					this.showError( '登录失败，请稍后重试' );
				}
			} );
		}

		// 注册表单提交
		if ( registerForm ) {
			registerForm.addEventListener( 'submit', async ( e ) => {
				e.preventDefault();

				const username = registerForm
					.querySelector( 'input[name="username"]' )
					.value.trim();
				const phone = registerForm
					.querySelector( 'input[name="phone"]' )
					.value.trim();
				const code = registerForm
					.querySelector( 'input[name="code"]' )
					.value.trim();
				const password = registerForm
					.querySelector( 'input[name="password"]' )
					.value.trim();
				const confirmPassword = registerForm
					.querySelector( 'input[name="confirm-password"]' )
					.value.trim();
				const agree = registerForm.querySelector(
					'input[name="agree"]'
				).checked;

				// 表单验证
				if (
					! username ||
					! phone ||
					! code ||
					! password ||
					! confirmPassword
				) {
					this.showError( '请填写完整的注册信息' );
					return;
				}

				if ( password !== confirmPassword ) {
					this.showError( '两次输入的密码不一致' );
					return;
				}

				if ( ! agree ) {
					this.showError( '请同意用户协议和隐私政策' );
					return;
				}

				try {
					const response = await api.post( '/register', {
						username,
						phone,
						code,
						password,
					} );

					if ( response.success ) {
						// 保存登录状态
						localStorage.setItem( 'token', response.token );
						localStorage.setItem(
							'user',
							JSON.stringify( response.user )
						);

						// 跳转到首页
						window.location.href = '/';
					} else {
						this.showError( response.message );
					}
				} catch ( error ) {
					console.error( '注册失败:', error );
					this.showError( '注册失败，请稍后重试' );
				}
			} );
		}
	},

	// 初始化社交登录
	initSocialLogin() {
		const wechatBtn = document.querySelector( '.btn-social.wechat' );
		const qqBtn = document.querySelector( '.btn-social.qq' );

		if ( wechatBtn ) {
			wechatBtn.addEventListener( 'click', () => {
				this.showWechatQRCode();
			} );
		}

		if ( qqBtn ) {
			qqBtn.addEventListener( 'click', () => {
				this.qqLogin();
			} );
		}
	},

	// 显示微信二维码
	showWechatQRCode() {
		const modal = document.querySelector( '.modal-wechat' );
		if ( modal ) {
			modal.classList.add( 'show' );

			// 生成二维码
			const qrcode = modal.querySelector( '.qrcode' );
			if ( qrcode ) {
				// 这里可以使用第三方库生成二维码
				// 例如: new QRCode(qrcode, 'wechat-login-url');
			}
		}
	},

	// QQ登录
	qqLogin() {
		// 这里可以使用QQ登录SDK
		// 例如: QC.Login.showPopup();
	},

	// 初始化验证码
	initVerificationCode() {
		const sendCodeBtn = document.querySelector( '.btn-send-code' );
		if ( ! sendCodeBtn ) {
			return;
		}

		sendCodeBtn.addEventListener( 'click', async () => {
			const phone = document
				.querySelector( 'input[name="phone"]' )
				.value.trim();
			if ( ! phone ) {
				this.showError( '请输入手机号码' );
				return;
			}

			// 验证手机号格式
			if ( ! /^1[3-9]\d{9}$/.test( phone ) ) {
				this.showError( '请输入正确的手机号码' );
				return;
			}

			try {
				// 禁用发送按钮
				sendCodeBtn.disabled = true;
				sendCodeBtn.textContent = '60秒后重试';

				// 发送验证码
				const response = await api.post( '/send-code', { phone } );

				if ( response.success ) {
					// 开始倒计时
					let countdown = 60;
					const timer = setInterval( () => {
						countdown--;
						sendCodeBtn.textContent = `${ countdown }秒后重试`;

						if ( countdown <= 0 ) {
							clearInterval( timer );
							sendCodeBtn.disabled = false;
							sendCodeBtn.textContent = '发送验证码';
						}
					}, 1000 );
				} else {
					this.showError( response.message );
					sendCodeBtn.disabled = false;
					sendCodeBtn.textContent = '发送验证码';
				}
			} catch ( error ) {
				console.error( '发送验证码失败:', error );
				this.showError( '发送验证码失败，请稍后重试' );
				sendCodeBtn.disabled = false;
				sendCodeBtn.textContent = '发送验证码';
			}
		} );
	},

	// 显示错误信息
	showError( message ) {
		const errorElement = document.createElement( 'div' );
		errorElement.className = 'error-message';
		errorElement.textContent = message;

		const container = document.querySelector( '.auth-container' );
		if ( container ) {
			// 移除旧的错误信息
			const oldError = container.querySelector( '.error-message' );
			if ( oldError ) {
				oldError.remove();
			}

			// 添加新的错误信息
			container.insertBefore( errorElement, container.firstChild );

			// 3秒后自动移除
			setTimeout( () => {
				errorElement.remove();
			}, 3000 );
		}
	},
};

// 初始化
document.addEventListener( 'DOMContentLoaded', () => {
	auth.init();
} );
