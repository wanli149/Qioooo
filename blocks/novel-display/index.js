import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import Edit from './edit';
import save from './save';
import './style.scss';

registerBlockType( 'qioooo/novel-display', {
	title: __( '小说展示', 'qioooo' ),
	description: __( '展示小说信息的区块', 'qioooo' ),
	icon: 'book',
	category: 'widgets',
	attributes: {
		novelId: {
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
		showStatus: {
			type: 'boolean',
			default: true,
		},
		showAuthor: {
			type: 'boolean',
			default: true,
		},
		layout: {
			type: 'string',
			default: 'default',
		},
	},
	edit: Edit,
	save,
} );
