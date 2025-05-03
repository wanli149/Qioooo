import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

// 导入所有区块
import './resource-card';
import './novel-display';
import './chapter-navigation';
import './related-posts';
import './comments';

// 注册区块类别
registerBlockType( 'qioooo/block-category', {
	title: __( 'QIoooo', 'qioooo' ),
	icon: 'book',
	category: 'qioooo',
	attributes: {},
	edit: () => null,
	save: () => null,
} );
