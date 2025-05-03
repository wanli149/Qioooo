import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import Edit from './edit';
import save from './save';
import './style.scss';

registerBlockType( 'qioooo/comments', {
	title: __( '评论', 'qioooo' ),
	description: __( '显示评论列表和评论表单的区块', 'qioooo' ),
	icon: 'admin-comments',
	category: 'qioooo',
	attributes: {
		postId: {
			type: 'number',
			default: 0,
		},
		showForm: {
			type: 'boolean',
			default: true,
		},
		showAvatar: {
			type: 'boolean',
			default: true,
		},
		showDate: {
			type: 'boolean',
			default: true,
		},
		showReply: {
			type: 'boolean',
			default: true,
		},
		commentsPerPage: {
			type: 'number',
			default: 10,
		},
		order: {
			type: 'string',
			default: 'desc',
		},
	},
	edit: Edit,
	save,
} );
