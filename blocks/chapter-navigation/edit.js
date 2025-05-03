import React from 'react';
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import {
	PanelBody,
	ToggleControl,
	SelectControl,
	RangeControl,
	Spinner,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreDataStore } from '@wordpress/core-data';

export default function Edit( { attributes, setAttributes } ) {
	const { novelId, showChapterList, showProgress, chaptersPerPage, layout } =
		attributes;

	const { novel, chapters, isLoading } = useSelect(
		( select ) => {
			const { getEntityRecord, getEntityRecords, isResolving } =
				select( coreDataStore );
			return {
				novel: novelId
					? getEntityRecord( 'postType', 'novel', novelId )
					: null,
				chapters: novelId
					? getEntityRecords( 'postType', 'novel_chapter', {
							novel_id: novelId,
							per_page: chaptersPerPage,
							orderby: 'meta_value_num',
							meta_key: 'chapter_number',
							order: 'ASC',
					  } )
					: null,
				isLoading:
					isResolving( 'postType', 'novel', novelId ) ||
					isResolving( 'postType', 'novel_chapter', {
						novel_id: novelId,
					} ),
			};
		},
		[ novelId, chaptersPerPage ]
	);

	return (
		<div { ...useBlockProps() }>
			<div className="chapter-navigation-editor">
				<PanelBody title={ __( '小说选择', 'qioooo' ) }>
					<SelectControl
						label={ __( '选择小说', 'qioooo' ) }
						value={ novelId }
						options={ [
							{ label: __( '请选择小说', 'qioooo' ), value: 0 },
							...( novel
								? [
										{
											label: novel.title.raw,
											value: novel.id,
										},
								  ]
								: [] ),
						] }
						onChange={ ( value ) =>
							setAttributes( { novelId: parseInt( value ) } )
						}
					/>
				</PanelBody>

				{ isLoading ? (
					<Spinner />
				) : novel ? (
					<>
						<PanelBody title={ __( '显示选项', 'qioooo' ) }>
							<ToggleControl
								label={ __( '显示章节列表', 'qioooo' ) }
								checked={ showChapterList }
								onChange={ ( value ) =>
									setAttributes( { showChapterList: value } )
								}
							/>
							<ToggleControl
								label={ __( '显示阅读进度', 'qioooo' ) }
								checked={ showProgress }
								onChange={ ( value ) =>
									setAttributes( { showProgress: value } )
								}
							/>
							<RangeControl
								label={ __( '每页显示章节数', 'qioooo' ) }
								value={ chaptersPerPage }
								onChange={ ( value ) =>
									setAttributes( { chaptersPerPage: value } )
								}
								min={ 5 }
								max={ 50 }
								step={ 5 }
							/>
						</PanelBody>

						<PanelBody title={ __( '布局选项', 'qioooo' ) }>
							<SelectControl
								label={ __( '布局样式', 'qioooo' ) }
								value={ layout }
								options={ [
									{
										label: __( '默认', 'qioooo' ),
										value: 'default',
									},
									{
										label: __( '紧凑', 'qioooo' ),
										value: 'compact',
									},
									{
										label: __( '展开', 'qioooo' ),
										value: 'expanded',
									},
								] }
								onChange={ ( value ) =>
									setAttributes( { layout: value } )
								}
							/>
						</PanelBody>

						<div className="chapter-navigation-preview">
							{ showProgress && (
								<div className="chapter-navigation__progress">
									<div className="progress-bar">
										<div
											className="progress-bar__fill"
											style={ { width: '60%' } }
										></div>
									</div>
									<div className="progress-text">
										{ __( '已阅读：60%', 'qioooo' ) }
									</div>
								</div>
							) }

							{ showChapterList && chapters && (
								<div className="chapter-navigation__list">
									<h3 className="chapter-navigation__title">
										{ __( '章节列表', 'qioooo' ) }
									</h3>
									<ul className="chapter-list">
										{ chapters.map( ( chapter ) => (
											<li
												key={ chapter.id }
												className="chapter-item"
											>
												<a
													href="#"
													className="chapter-link"
												>
													{
														chapter.meta
															.chapter_title
													}
												</a>
											</li>
										) ) }
									</ul>
								</div>
							) }
						</div>
					</>
				) : (
					<p>{ __( '请选择要展示的小说', 'qioooo' ) }</p>
				) }
			</div>
		</div>
	);
}
