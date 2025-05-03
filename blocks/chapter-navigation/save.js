import React from 'react';
import { useBlockProps } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { novelId, showChapterList, showProgress, chaptersPerPage, layout } =
		attributes;

	const blockProps = useBlockProps.save( {
		className: `chapter-navigation chapter-navigation--${ layout }`,
	} );

	return (
		<div { ...blockProps }>
			{ showProgress && (
				<div className="chapter-navigation__progress">
					<div className="progress-bar">
						<div
							className="progress-bar__fill"
							style={ { width: '60%' } }
						></div>
					</div>
					<div className="progress-text">
						{ wp.i18n.__( '已阅读：60%', 'qioooo' ) }
					</div>
				</div>
			) }

			{ showChapterList && (
				<div className="chapter-navigation__list">
					<h3 className="chapter-navigation__title">
						{ wp.i18n.__( '章节列表', 'qioooo' ) }
					</h3>
					<ul className="chapter-list">
						{ wp.data
							.select( 'core' )
							.getEntityRecords( 'postType', 'novel_chapter', {
								novel_id: novelId,
								per_page: chaptersPerPage,
								orderby: 'meta_value_num',
								meta_key: 'chapter_number',
								order: 'ASC',
							} )
							?.map( ( chapter ) => (
								<li key={ chapter.id } className="chapter-item">
									<a
										href={ chapter.link }
										className="chapter-link"
									>
										{ chapter.meta.chapter_title }
									</a>
								</li>
							) ) }
					</ul>
				</div>
			) }
		</div>
	);
}
