<?php

/** Auto-generate a slug for microblogging posts */
function silorider_set_microblogging_slug( $data, $postarr, $unsanitized_postarr, $update ) {
	$do_generate_slugs = get_option( 'generate_micropost_slugs', true );
	$do_generate_titles = get_option( 'generate_micropost_titles', true );
	if ( ! $do_generate_slugs && ! $do_generate_titles ) {
		error_log( "Not generating slugs or titles, leaving data untouched" );
		return $data;
	}

	$post_format = get_post_format( $postarr['ID'] );
	if ( ( $post_format || empty( $data['post_title'] ) ) &&
		!in_array( $data['post_status'], array( 'draft', 'pending', 'auto-draft' ) )
	) {
		if ( empty( $data['post_title'] ) && $do_generate_titles ) {
			$raw_content = wp_strip_all_tags( $postarr['post_content'], true );
			if ( !empty( $raw_content ) ) {
				$length = get_option( 'micropost_title_word_count', 10 );
				$words = preg_split( "/\s+/", $raw_content, $length, PREG_SPLIT_NO_EMPTY );
				if ( count( $words ) >= $length ) {
					array_pop( $words );
				}
				$new_title = implode( ' ', $words );
				$data['post_title'] = $new_title;
				//error_log( "wp_insert_post_data: generated title: {$new_title}" );
			}
		}
		if ( empty( $data['post_name'] ) && $do_generate_slugs ) {
			$format = get_option( 'micropost_slug_format', 'His' );
			$datetimestr = $postarr['post_date'];
			$now = new DateTimeImmutable( $datetimestr, wp_timezone() );
			$new_slug = wp_unique_post_slug(
				$now->format( $format ),
				$postarr['ID'],
				$data['post_status'],
				$data['post_type'],
				$data['post_parent'] );
			$data['post_name'] = $new_slug;
			//error_log( "wp_insert_post_data: generated slug: {$new_slug}" );
		}
	}
	else
	{
		error_log( "Post status not right: {$data['post_status']}, leaving data untouched" );
	}
	return $data;
}
add_filter( 'wp_insert_post_data', 'silorider_set_microblogging_slug', 100, 4 );

/** Add microformats to feature images on microblogging posts */
function silorider_add_featured_image_microformats( $attr, $attachment, $size ) {
	$post_format = get_post_format();
	if ( $post_format == 'status' || $post_format == 'image' ) {
		if ( isset( $attr['class'] ) ) {
			$attr['class'] = $attr['class'] . ' u-photo';
		} else {
			$attr['class'] = 'u-photo';
		}
	}
	//error_log( "wp_get_attachment_image_attributes! src = {$attr['src']} class = {$attr['class']}" );
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'silorider_add_featured_image_microformats', 100, 3  );

/** Add microformats to all other images on microblogging posts */
function silorider_add_image_microformats( $filtered_image, $context, $attachment_id ) {
	$post_format = get_post_format();
	if ( $post_format ) {
		$filtered_image = str_replace( 'class="', 'class="u-photo ', $filtered_image );
	}
	//error_log( "wp_content_img_tag! post_format = {$post_format} filtered_image = {$filtered_image}" );
	return $filtered_image;
}
add_filter( 'wp_content_img_tag', 'silorider_add_image_microformats', 100, 3 );

//function silorider_add_photo_microformats( $block_content, $block, $instance ) {
//	$post_format = get_post_format();
//	if ( $post_format == 'image' || $post_format == 'gallery' ) {
//		$block_content = str_replace('<img class="', '<img class="u-photo ', $block_content);
//	}
//	error_log( "render_block_core/image!" );
//	return $block_content;
//}
//add_filter( 'render_block_core/image', 'silorider_add_photo_microformats', 10, 3 );
//
//function silorider_add_gallery_microformats( $block_content, $block, $instance ) {
//	$post_format = get_post_format();
//	if ( $post_format == 'image' || $post_format == 'gallery' ) {
//		//$class   = wp_unique_id( 'wp-block-gallery-' );
//		//$content = preg_replace(
//		//	'/' . preg_quote( 'class="', '/' ) . '/',
//		//	'class="' . $class . ' ',
//		//	$content,
//		//	1
//		//);
//	}
//	error_log( 'render_block_core/gallery!' );
//	return $block_content;
//}
//add_filter( 'render_block_core/gallery', 'silorider_add_gallery_microformats', 10, 3 );

//function silorider_get_attachment_image( $html, $attachment_id, $size, $icon, $attr ) {
//	$html = str_replace('class="', 'class="u-photo ', $html);
//	error_log( "wp_get_attachment_image! attr = {$attr} html = {$html}" );
//	return $html;
//}
//add_filter( 'wp_get_attachment_image', 'silorider_get_attachment_image', 100, 5  );

//function silorider_get_image_tag( $html, $id, $alt, $title ) {
//	error_log( "get_image_tag!" );
//	return $html;
//}
//add_filter('get_image_tag', 'silorider_get_image_tag', 10, 4);

//function silorider_add_image_microformats( $class ) {
//	error_log( "get_image_tag_class! class = {$class}" );
//	return $class .= ' u-photo';
//}
//add_filter( 'get_image_tag_class', 'silorider_add_image_microformats' );


