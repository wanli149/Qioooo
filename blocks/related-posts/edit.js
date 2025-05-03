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

	const { currentPost, relatedPosts, isLoading } = useSelect(
		( select ) => {
			const { getEntityRecord, getEntityRecords, isResolving } =
				select( coreDataStore );
			return {
				currentPost: currentPostId
					? getEntityRecord( 'postType', postType, currentPostId )
					: null,
				relatedPosts: currentPostId
					? getEntityRecords( 'postType', postType, {
							per_page: postsPerPage,
							exclude: [ currentPostId ],
							orderby: 'rand',
					  } )
					: null,
				isLoading: isResolving( 'postType', postType, currentPostId ),
			};
		},
		[ currentPostId, postType, postsPerPage ]
	);

	return (
		<div { ...useBlockProps() }>
			<div className="related-posts-editor">
				<PanelBody title={ __( '内容类型', 'qioooo' ) }>
					<SelectControl
						label={ __( '推荐类型', 'qioooo' ) }
						value={ postType }
						options={ [
							{ label: __( '小说', 'qioooo' ), value: 'novel' },
							{
								label: __( '资源', 'qioooo' ),
								value: 'resource',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { postType: value } )
						}
					/>
				</PanelBody>

				{ isLoading ? (
					<Spinner />
				) : currentPost ? (
					<>
						<PanelBody title={ __( '显示选项', 'qioooo' ) }>
							<ToggleControl
								label={ __( '显示封面', 'qioooo' ) }
								checked={ showCover }
								onChange={ ( value ) =>
									setAttributes( { showCover: value } )
								}
							/>
							<ToggleControl
								label={ __( '显示描述', 'qioooo' ) }
								checked={ showDescription }
								onChange={ ( value ) =>
									setAttributes( { showDescription: value } )
								}
							/>
							<ToggleControl
								label={ __( '显示作者', 'qioooo' ) }
								checked={ showAuthor }
								onChange={ ( value ) =>
									setAttributes( { showAuthor: value } )
								}
							/>
							<ToggleControl
								label={ __( '显示日期', 'qioooo' ) }
								checked={ showDate }
								onChange={ ( value ) =>
									setAttributes( { showDate: value } )
								}
							/>
							<RangeControl
								label={ __( '显示数量', 'qioooo' ) }
								value={ postsPerPage }
								onChange={ ( value ) =>
									setAttributes( { postsPerPage: value } )
								}
								min={ 1 }
								max={ 6 }
							/>
						</PanelBody>

						<PanelBody title={ __( '布局选项', 'qioooo' ) }>
							<SelectControl
								label={ __( '布局样式', 'qioooo' ) }
								value={ layout }
								options={ [
									{
										label: __( '网格', 'qioooo' ),
										value: 'grid',
									},
									{
										label: __( '列表', 'qioooo' ),
										value: 'list',
									},
									{
										label: __( '卡片', 'qioooo' ),
										value: 'card',
									},
								] }
								onChange={ ( value ) =>
									setAttributes( { layout: value } )
								}
							/>
						</PanelBody>

						<div className="related-posts-preview">
							<h3 className="related-posts__title">
								{ __( '相关推荐', 'qioooo' ) }
							</h3>
							<div
								className={ `related-posts__list related-posts__list--${ layout }` }
							>
								{ relatedPosts?.map( ( post ) => (
									<div
										key={ post.id }
										className="related-post"
									>
										{ showCover && (
											<div className="related-post__cover">
												<img
													src={
														post.featured_media_url
													}
													alt={ post.title.raw }
												/>
											</div>
										) }
										<div className="related-post__content">
											<h4 className="related-post__title">
												{ post.title.raw }
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
					</>
				) : (
					<p>{ __( '请选择要展示的内容', 'qioooo' ) }</p>
				) }
			</div>
		</div>
	);
}
