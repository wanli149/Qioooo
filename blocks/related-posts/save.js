import React from 'react';
import { useBlockProps } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const {
		postType,
		currentPostId,
		showCover,
		showDescription,
		showAuthor,
		showDate,
		postsPerPage,
		layout,
	} = attributes;

	const blockProps = useBlockProps.save( {
		className: `related-posts related-posts--${ layout }`,
	} );

	return (
		<div { ...blockProps }>
			<h3 className="related-posts__title">
				{ wp.i18n.__( '相关推荐', 'qioooo' ) }
			</h3>
			<div
				className={ `related-posts__list related-posts__list--${ layout }` }
			>
				{ wp.data
					.select( 'core' )
					.getEntityRecords( 'postType', postType, {
						per_page: postsPerPage,
						exclude: [ currentPostId ],
						orderby: 'rand',
					} )
					?.map( ( post ) => (
						<div key={ post.id } className="related-post">
							{ showCover && (
								<div className="related-post__cover">
									<img
										src={ post.featured_media_url }
										alt={ post.title.raw }
									/>
								</div>
							) }
							<div className="related-post__content">
								<h4 className="related-post__title">
									<a href={ post.link }>{ post.title.raw }</a>
								</h4>
								{ showAuthor && (
									<div className="related-post__author">
										{ post.author_name }
									</div>
								) }
								{ showDate && (
									<div className="related-post__date">
										{ post.date }
									</div>
								) }
								{ showDescription && (
									<div className="related-post__description">
										{ post.excerpt.raw }
									</div>
								) }
							</div>
						</div>
					) ) }
			</div>
		</div>
	);
}
