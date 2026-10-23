import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { HomeImpactFeaturesAttributes } from './types';

registerBlockType<HomeImpactFeaturesAttributes>(metadata as BlockConfiguration<HomeImpactFeaturesAttributes>, {
	edit: Edit,
	save: () => null,
});
