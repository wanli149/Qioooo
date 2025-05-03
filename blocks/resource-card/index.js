import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import Edit from './edit';
import save from './save';
import './style.scss';

registerBlockType( 'qioooo/resource-card', {
	title: __( '资源卡片', 'qioooo' ),
	description: __( '展示资源的卡片区块', 'qioooo' ),
	icon: 'admin-page',
	category: 'widgets',
	attributes: {
		title: {
			type: 'string',
			default: '',
		},
		description: {
			type: 'string',
			default: '',
		},
		layout: {
			type: 'string',
			default: 'default',
		},
	},
	edit: Edit,
	save,
} );
