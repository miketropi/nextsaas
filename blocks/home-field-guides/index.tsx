import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { HomeFieldGuidesAttributes } from './types';

registerBlockType<HomeFieldGuidesAttributes>(metadata as BlockConfiguration<HomeFieldGuidesAttributes>, {
	edit: Edit,
	save: () => null,
});
