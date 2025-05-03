import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import Edit from './edit';
import save from './save';
import './style.scss';

registerBlockType( 'qioooo/related-posts', {
	title: __( '相关推荐', 'qioooo' ),
	description: __( '显示相关小说和资源的区块', 'qioooo' ),
	icon: 'star-filled',
	category: 'qioooo',
	attributes: {
		postType: {
			type: 'string',
			default: 'novel',
		},
		currentPostId: {
			type: 'number',
			default: 0,
		},
		showCover: {
			type: 'boolean',
			default: true,
		},
		showDescription: {
			type: 'boolean',
			default: true,
		},
		showAuthor: {
			type: 'boolean',
			default: true,
		},
		showDate: {
			type: 'boolean',
			default: true,
		},
		postsPerPage: {
			type: 'number',
			default: 3,
		},
		layout: {
			type: 'string',
			default: 'grid',
		},
	},
	edit: Edit,
	save,
} );
