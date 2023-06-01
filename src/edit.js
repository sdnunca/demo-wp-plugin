/**
 * WordPress components that create the necessary UI elements for the block
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-components/
 */
import { __ } from '@wordpress/i18n';

import { select, dispatch } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { TextControl, PanelBody, Button } from '@wordpress/components';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @param {Object}   props               Properties passed to the function.
 * @param {Object}   props.attributes    Available block attributes.
 * @param {Function} props.setAttributes Function that updates individual attributes.
 *
 * @return {WPElement} Element to render.
 */
export default function Edit( { attributes, setAttributes } ) {
	const blockProps = useBlockProps();
	const content =
		attributes.content || __( 'Click to get started!', 'demo-wp-plugin' );

	// Force sidebar open when clicking the block
	const openSidebar = () => {
		const isSidebarActive =
			select( 'core/edit-post' ).isEditorSidebarOpened();
		if ( ! isSidebarActive ) {
			dispatch( 'core/edit-post' ).openGeneralSidebar(
				'edit-post/block'
			);
		}
	};
	const generateContent = () => {
		apiFetch( {
			path: '/demo-wp-plugin/openai/',
			method: 'POST',
			data: { prompt: attributes.prompt },
		} ).then( ( data ) => {
			setAttributes( {
				content: data.content,
			} );
		} );
	};

	return (
		<div { ...blockProps }>
			<InspectorControls key="setting">
				<PanelBody title={ __( 'AI content' ) }>
					<fieldset>
						<legend className="blocks-base-control__label">
							{ __( 'Prompt', 'demo-wp-plugin' ) }
						</legend>
						<TextControl
							value={ attributes.prompt }
							onChange={ ( val ) =>
								setAttributes( { prompt: val } )
							}
						></TextControl>
					</fieldset>
					<fieldset>
						<Button variant="primary" onClick={ generateContent }>
							Generate
						</Button>
					</fieldset>
				</PanelBody>
			</InspectorControls>
			<div role="presentation" onClick={ openSidebar }>
				{ content }
			</div>
		</div>
	);
}
