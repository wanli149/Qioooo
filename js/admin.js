// 后台主题设置功能
const admin = {
	// 初始化
	init() {
		this.initSidebar();
		this.initThemeSettings();
		this.initFileUpload();
	},

	// 初始化侧边栏
	initSidebar() {
		const sidebar = document.querySelector( '.admin-sidebar' );
		const toggleBtn = document.querySelector( '.btn-toggle-sidebar' );

		if ( toggleBtn ) {
			toggleBtn.addEventListener( 'click', () => {
				sidebar.classList.toggle( 'active' );
			} );
		}

		// 侧边栏菜单项点击
		const menuItems = sidebar.querySelectorAll( '.sidebar-nav a' );
		menuItems.forEach( ( item ) => {
			item.addEventListener( 'click', () => {
				// 移除其他菜单项的active类
				menuItems.forEach( ( i ) =>
					i.parentElement.classList.remove( 'active' )
				);
				// 添加当前菜单项的active类
				item.parentElement.classList.add( 'active' );
			} );
		} );
	},

	// 初始化主题设置
	initThemeSettings() {
		const form = document.querySelector( '.theme-settings' );
		if ( ! form ) {
			return;
		}

		// 加载当前主题设置
		this.loadThemeSettings();

		// 表单提交
		form.addEventListener( 'submit', async ( e ) => {
			e.preventDefault();

			try {
				const formData = new FormData( form );
				const data = Object.fromEntries( formData.entries() );

				const response = await api.post(
					'/admin/theme-settings',
					data
				);

				if ( response.success ) {
					this.showSuccess( '设置保存成功' );
				} else {
					this.showError( response.message );
				}
			} catch ( error ) {
				console.error( '保存设置失败:', error );
				this.showError( '保存设置失败，请稍后重试' );
			}
		} );

		// 重置按钮
		const resetBtn = form.querySelector( '.btn-reset' );
		if ( resetBtn ) {
			resetBtn.addEventListener( 'click', () => {
				if ( confirm( '确定要重置所有设置吗？' ) ) {
					this.resetThemeSettings();
				}
			} );
		}

		// 颜色方案选择
		const colorSchemes = form.querySelectorAll( '.color-scheme' );
		colorSchemes.forEach( ( scheme ) => {
			scheme.addEventListener( 'click', () => {
				// 移除其他方案的active类
				colorSchemes.forEach( ( s ) => s.classList.remove( 'active' ) );
				// 添加当前方案的active类
				scheme.classList.add( 'active' );

				// 更新表单值
				form.querySelector( 'input[name="color_scheme"]' ).value =
					scheme.dataset.scheme;
			} );
		} );

		// 自定义颜色
		const customColor = form.querySelector(
			'input[name="custom_primary_color"]'
		);
		if ( customColor ) {
			customColor.addEventListener( 'change', () => {
				const preview = form.querySelector( '.color-preview' );
				if ( preview ) {
					preview.style.backgroundColor = customColor.value;
				}
			} );
		}
	},

	// 加载主题设置
	async loadThemeSettings() {
		try {
			const response = await api.get( '/admin/theme-settings' );
			const form = document.querySelector( '.theme-settings' );

			if ( response.success && form ) {
				// 填充表单数据
				Object.entries( response.settings ).forEach(
					( [ key, value ] ) => {
						const input = form.querySelector( `[name="${ key }"]` );
						if ( input ) {
							if ( input.type === 'checkbox' ) {
								input.checked = value;
							} else {
								input.value = value;
							}
						}
					}
				);

				// 设置颜色方案
				const colorScheme = response.settings.color_scheme;
				const schemeElement = form.querySelector(
					`.color-scheme[data-scheme="${ colorScheme }"]`
				);
				if ( schemeElement ) {
					schemeElement.classList.add( 'active' );
				}

				// 更新自定义颜色预览
				const customColor = form.querySelector(
					'input[name="custom_primary_color"]'
				);
				const preview = form.querySelector( '.color-preview' );
				if ( customColor && preview ) {
					preview.style.backgroundColor = customColor.value;
				}
			}
		} catch ( error ) {
			console.error( '加载主题设置失败:', error );
			this.showError( '加载主题设置失败，请稍后重试' );
		}
	},

	// 重置主题设置
	async resetThemeSettings() {
		try {
			const response = await api.post( '/admin/theme-settings/reset' );

			if ( response.success ) {
				this.showSuccess( '设置已重置' );
				this.loadThemeSettings();
			} else {
				this.showError( response.message );
			}
		} catch ( error ) {
			console.error( '重置设置失败:', error );
			this.showError( '重置设置失败，请稍后重试' );
		}
	},

	// 初始化文件上传
	initFileUpload() {
		const uploadBoxes = document.querySelectorAll( '.upload-box' );

		uploadBoxes.forEach( ( box ) => {
			const input = box.querySelector( 'input[type="file"]' );
			const preview = box.querySelector( 'img' );
			const uploadBtn = box.querySelector( '.btn-upload' );

			if ( input && preview && uploadBtn ) {
				// 预览图片
				input.addEventListener( 'change', () => {
					const file = input.files[ 0 ];
					if ( file ) {
						const reader = new FileReader();
						reader.onload = ( e ) => {
							preview.src = e.target.result;
						};
						reader.readAsDataURL( file );
					}
				} );

				// 上传按钮点击
				uploadBtn.addEventListener( 'click', async () => {
					const file = input.files[ 0 ];
					if ( ! file ) {
						this.showError( '请选择要上传的文件' );
						return;
					}

					try {
						const formData = new FormData();
						formData.append( 'file', file );
						formData.append( 'type', input.dataset.type );

						const response = await api.post(
							'/admin/upload',
							formData
						);

						if ( response.success ) {
							this.showSuccess( '上传成功' );
							// 更新隐藏的输入框值
							const hiddenInput = box.querySelector(
								'input[type="hidden"]'
							);
							if ( hiddenInput ) {
								hiddenInput.value = response.url;
							}
						} else {
							this.showError( response.message );
						}
					} catch ( error ) {
						console.error( '上传失败:', error );
						this.showError( '上传失败，请稍后重试' );
					}
				} );
			}
		} );
	},

	// 显示成功消息
	showSuccess( message ) {
		const successElement = document.createElement( 'div' );
		successElement.className = 'success-message';
		successElement.textContent = message;

		const container = document.querySelector( '.admin-main' );
		if ( container ) {
			// 移除旧的消息
			const oldMessage = container.querySelector(
				'.success-message, .error-message'
			);
			if ( oldMessage ) {
				oldMessage.remove();
			}

			// 添加新的消息
			container.insertBefore( successElement, container.firstChild );

			// 3秒后自动移除
			setTimeout( () => {
				successElement.remove();
			}, 3000 );
		}
	},

	// 显示错误消息
	showError( message ) {
		const errorElement = document.createElement( 'div' );
		errorElement.className = 'error-message';
		errorElement.textContent = message;

		const container = document.querySelector( '.admin-main' );
		if ( container ) {
			// 移除旧的消息
			const oldMessage = container.querySelector(
				'.success-message, .error-message'
			);
			if ( oldMessage ) {
				oldMessage.remove();
			}

			// 添加新的消息
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
	admin.init();
} );
