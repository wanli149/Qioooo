import React from 'react';
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import {
	PanelBody,
	ToggleControl,
	SelectControl,
	Spinner,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreDataStore } from '@wordpress/core-data';

export default function Edit( { attributes, setAttributes } ) {
	const {
		novelId,
		showCover,
		showDescription,
		showStatus,
		showAuthor,
		layout,
	} = attributes;

	const { novel, isLoading } = useSelect(
		( select ) => {
			const { getEntityRecord, isResolving } = select( coreDataStore );
			return {
				novel: novelId
					? getEntityRecord( 'postType', 'novel', novelId )
					: null,
				isLoading: isResolving( 'postType', 'novel', novelId ),
			};
		},
		[ novelId ]
	);

	return (
		<div { ...useBlockProps() }>
			<div className="novel-display-editor">
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
								label={ __( '显示状态', 'qioooo' ) }
								checked={ showStatus }
								onChange={ ( value ) =>
									setAttributes( { showStatus: value } )
								}
							/>
							<ToggleControl
								label={ __( '显示作者', 'qioooo' ) }
								checked={ showAuthor }
								onChange={ ( value ) =>
									setAttributes( { showAuthor: value } )
								}
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
										label: __( '卡片', 'qioooo' ),
										value: 'card',
									},
									{
										label: __( '列表', 'qioooo' ),
										value: 'list',
									},
								] }
								onChange={ ( value ) =>
									setAttributes( { layout: value } )
								}
							/>
						</PanelBody>

						<div className="novel-display-preview">
							{ showCover && novel.featured_media && (
								<div className="novel-display__cover">
									<img
										src={ novel.featured_media_url }
										alt={ novel.title.raw }
									/>
								</div>
							) }
							<div className="novel-display__content">
								<h3 className="novel-display__title">
									{ novel.title.raw }
								</h3>
								{ showAuthor && (
									<div className="novel-display__author">
										{ __( '作者：', 'qioooo' ) }
										{ novel.author_name }
									</div>
								) }
								{ showStatus && (
									<div className="novel-display__status">
										{ __( '状态：', 'qioooo' ) }
										{ novel.meta.novel_status }
									</div>
								) }
								{ showDescription && (
									<div className="novel-display__description">
										{ novel.excerpt.raw }
									</div>
								) }
							</div>
						</div>
					</>
				) : (
					<p>{ __( '请选择要展示的小说', 'qioooo' ) }</p>
				) }
			</div>
		</div>
	);
}
