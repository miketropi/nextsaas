import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { HomePointOfViewAttributes } from './types';

registerBlockType<HomePointOfViewAttributes>(metadata as BlockConfiguration<HomePointOfViewAttributes>, {
	edit: Edit,
	save: () => null,
});
