import React from 'react';
import { useBlockProps } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { title, description, layout } = attributes;
	const blockProps = useBlockProps.save( {
		className: `resource-card resource-card--${ layout }`,
	} );

	return (
		<div { ...blockProps }>
			<div className="resource-card__content">
				{ title && <h3 className="resource-card__title">{ title }</h3> }
				{ description && (
					<div className="resource-card__description">
						{ description }
					</div>
				) }
			</div>
		</div>
	);
}
