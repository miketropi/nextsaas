import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { HomeTestimonialAttributes } from './types';

registerBlockType<HomeTestimonialAttributes>(metadata as BlockConfiguration<HomeTestimonialAttributes>, {
	edit: Edit,
	save: () => null,
});
