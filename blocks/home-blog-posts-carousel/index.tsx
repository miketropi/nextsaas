import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { HomeBlogPostsCarouselAttributes } from './types';

registerBlockType<HomeBlogPostsCarouselAttributes>(metadata as BlockConfiguration<HomeBlogPostsCarouselAttributes>, {
	edit: Edit,
	save: () => null,
});
