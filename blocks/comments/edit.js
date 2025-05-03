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
		postId,
		showForm,
		showAvatar,
		showDate,
		showReply,
		commentsPerPage,
		order,
	} = attributes;

	const { post, comments, isLoading } = useSelect(
		( select ) => {
			const { getEntityRecord, getEntityRecords, isResolving } =
				select( coreDataStore );
			return {
				post: postId
					? getEntityRecord( 'postType', 'post', postId )
					: null,
				comments: postId
					? getEntityRecords( 'postType', 'comment', {
							post: postId,
							per_page: commentsPerPage,
							order,
					  } )
					: null,
				isLoading: isResolving( 'postType', 'post', postId ),
			};
		},
		[ postId, commentsPerPage, order ]
	);

	return (
		<div { ...useBlockProps() }>
			<div className="comments-editor">
				<PanelBody title={ __( '评论设置', 'qioooo' ) }>
					<ToggleControl
						label={ __( '显示评论表单', 'qioooo' ) }
						checked={ showForm }
						onChange={ ( value ) =>
							setAttributes( { showForm: value } )
						}
					/>
					<ToggleControl
						label={ __( '显示头像', 'qioooo' ) }
						checked={ showAvatar }
						onChange={ ( value ) =>
							setAttributes( { showAvatar: value } )
						}
					/>
					<ToggleControl
						label={ __( '显示日期', 'qioooo' ) }
						checked={ showDate }
						onChange={ ( value ) =>
							setAttributes( { showDate: value } )
						}
					/>
					<ToggleControl
						label={ __( '显示回复按钮', 'qioooo' ) }
						checked={ showReply }
						onChange={ ( value ) =>
							setAttributes( { showReply: value } )
						}
					/>
					<RangeControl
						label={ __( '每页显示评论数', 'qioooo' ) }
						value={ commentsPerPage }
						onChange={ ( value ) =>
							setAttributes( { commentsPerPage: value } )
						}
						min={ 5 }
						max={ 50 }
						step={ 5 }
					/>
					<SelectControl
						label={ __( '评论排序', 'qioooo' ) }
						value={ order }
						options={ [
							{
								label: __( '最新优先', 'qioooo' ),
								value: 'desc',
							},
							{ label: __( '最早优先', 'qioooo' ), value: 'asc' },
						] }
						onChange={ ( value ) =>
							setAttributes( { order: value } )
						}
					/>
				</PanelBody>

				{ isLoading ? (
					<Spinner />
				) : post ? (
					<div className="comments-preview">
						<h3 className="comments__title">
							{ __( '评论', 'qioooo' ) }
						</h3>
						{ showForm && (
							<div className="comments__form">
								<textarea
									className="comments__textarea"
									placeholder={ __(
										'写下你的评论…',
										'qioooo'
									) }
								/>
								<button className="comments__submit">
									{ __( '发表评论', 'qioooo' ) }
								</button>
							</div>
						) }
						<div className="comments__list">
							{ comments?.map( ( comment ) => (
								<div key={ comment.id } className="comment">
									{ showAvatar && (
										<div className="comment__avatar">
											<img
												src={
													comment.author_avatar_urls[
														'96'
													]
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
												{ __( '回复', 'qioooo' ) }
											</button>
										) }
									</div>
								</div>
							) ) }
						</div>
					</div>
				) : (
					<p>{ __( '请选择要显示评论的文章', 'qioooo' ) }</p>
				) }
			</div>
		</div>
	);
}
