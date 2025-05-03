import React from 'react';
import { useBlockProps } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const {
		novelId,
		showCover,
		showDescription,
		showStatus,
		showAuthor,
		layout,
	} = attributes;

	const blockProps = useBlockProps.save( {
		className: `novel-display novel-display--${ layout }`,
	} );

	return (
		<div { ...blockProps }>
			<div className="novel-display__wrapper">
				{ showCover && (
					<div className="novel-display__cover">
						<img
							src={
								wp.data.select( 'core' ).getMedia( novelId )
									?.source_url
							}
							alt={
								wp.data.select( 'core' ).getPost( novelId )
									?.title.raw
							}
						/>
					</div>
				) }
				<div className="novel-display__content">
					<h3 className="novel-display__title">
						{
							wp.data.select( 'core' ).getPost( novelId )?.title
								.raw
						}
					</h3>
					{ showAuthor && (
						<div className="novel-display__author">
							{ wp.i18n.__( '作者：', 'qioooo' ) }
							{
								wp.data
									.select( 'core' )
									.getUser(
										wp.data
											.select( 'core' )
											.getPost( novelId )?.author
									)?.name
							}
						</div>
					) }
					{ showStatus && (
						<div className="novel-display__status">
							{ wp.i18n.__( '状态：', 'qioooo' ) }
							{
								wp.data.select( 'core' ).getPost( novelId )
									?.meta.novel_status
							}
						</div>
					) }
					{ showDescription && (
						<div className="novel-display__description">
							{
								wp.data.select( 'core' ).getPost( novelId )
									?.excerpt.raw
							}
						</div>
					) }
				</div>
			</div>
		</div>
	);
}
