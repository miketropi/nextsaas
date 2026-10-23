import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { AboutUsPrinciplesAttributes } from './types';

registerBlockType<AboutUsPrinciplesAttributes>(metadata as BlockConfiguration<AboutUsPrinciplesAttributes>, {
	edit: Edit,
	save: () => null,
});
