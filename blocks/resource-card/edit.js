import React from 'react';
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl, SelectControl } from '@wordpress/components';

export default function Edit( { attributes, setAttributes } ) {
	const { title, description, layout } = attributes;

	return (
		<div { ...useBlockProps() }>
			<div className="resource-card-editor">
				<TextControl
					label={ __( '标题', 'qioooo' ) }
					value={ title }
					onChange={ ( value ) => setAttributes( { title: value } ) }
				/>
				<TextControl
					label={ __( '描述', 'qioooo' ) }
					value={ description }
					onChange={ ( value ) =>
						setAttributes( { description: value } )
					}
				/>
				<SelectControl
					label={ __( '布局', 'qioooo' ) }
					value={ layout }
					options={ [
						{ label: __( '默认', 'qioooo' ), value: 'default' },
						{ label: __( '卡片', 'qioooo' ), value: 'card' },
						{ label: __( '列表', 'qioooo' ), value: 'list' },
					] }
					onChange={ ( value ) => setAttributes( { layout: value } ) }
				/>
			</div>
		</div>
	);
}
