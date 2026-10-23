import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { AboutUsOurPerspectiveAttributes } from './types';

registerBlockType<AboutUsOurPerspectiveAttributes>(metadata as BlockConfiguration<AboutUsOurPerspectiveAttributes>, {
	edit: Edit,
	save: () => null,
});
