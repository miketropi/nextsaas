import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { AboutUsBuiltWithIntentionAttributes } from './types';

registerBlockType<AboutUsBuiltWithIntentionAttributes>(metadata as BlockConfiguration<AboutUsBuiltWithIntentionAttributes>, {
	edit: Edit,
	save: () => null,
});
