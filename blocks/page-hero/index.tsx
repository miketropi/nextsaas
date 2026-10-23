import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { PageHeroAttributes } from './types';

registerBlockType<PageHeroAttributes>(metadata as BlockConfiguration<PageHeroAttributes>, {
	edit: Edit,
	save: () => null,
});
