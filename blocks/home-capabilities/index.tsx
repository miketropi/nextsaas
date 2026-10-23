import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { HomeCapabilitiesAttributes } from './types';

registerBlockType<HomeCapabilitiesAttributes>(metadata as BlockConfiguration<HomeCapabilitiesAttributes>, {
	edit: Edit,
	save: () => null,
});
