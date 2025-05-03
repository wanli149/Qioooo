import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import Edit from './edit';
import save from './save';
import './style.scss';

registerBlockType( 'qioooo/chapter-navigation', {
	title: __( '章节导航', 'qioooo' ),
	description: __( '显示小说章节导航的区块', 'qioooo' ),
	icon: 'list-view',
	category: 'qioooo',
	attributes: {
		novelId: {
			type: 'number',
			default: 0,
		},
		showChapterList: {
			type: 'boolean',
			default: true,
		},
		showProgress: {
			type: 'boolean',
			default: true,
		},
		chaptersPerPage: {
			type: 'number',
			default: 10,
		},
		layout: {
			type: 'string',
			default: 'default',
		},
	},
	edit: Edit,
	save,
} );
