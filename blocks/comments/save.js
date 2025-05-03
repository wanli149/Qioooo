import React from 'react';
import { useBlockProps } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const {
		postId,
		showForm,
		showAvatar,
		showDate,
		showReply,
		commentsPerPage,
		order,
	} = attributes;

	const blockProps = useBlockProps.save( {
		className: 'comments',
	} );

	return (
		<div { ...blockProps }>
			<h3 className="comments__title">
				{ wp.i18n.__( '评论', 'qioooo' ) }
			</h3>
			{ showForm && (
				<div className="comments__form">
					<textarea
						className="comments__textarea"
						placeholder={ wp.i18n.__( '写下你的评论…', 'qioooo' ) }
					/>
					<button className="comments__submit">
						{ wp.i18n.__( '发表评论', 'qioooo' ) }
					</button>
				</div>
			) }
			<div className="comments__list">
				{ wp.data
					.select( 'core' )
					.getEntityRecords( 'postType', 'comment', {
						post: postId,
						per_page: commentsPerPage,
						order,
					} )
					?.map( ( comment ) => (
						<div key={ comment.id } className="comment">
							{ showAvatar && (
								<div className="comment__avatar">
									<img
										src={
											comment.author_avatar_urls[ '96' ]
										}
										alt={ comment.author_name }
									/>
								</div>
							) }
							<div className="comment__content">
								<div className="comment__header">
									<span className="comment__author">
										{ comment.author_name }
									</span>
									{ showDate && (
										<span className="comment__date">
											{ comment.date }
										</span>
									) }
								</div>
								<div className="comment__text">
									{ comment.content.raw }
								</div>
								{ showReply && (
									<button className="comment__reply">
										{ wp.i18n.__( '回复', 'qioooo' ) }
									</button>
								) }
							</div>
						</div>
					) ) }
			</div>
		</div>
	);
}
