import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { HomeFaqAttributes } from './types';

registerBlockType<HomeFaqAttributes>(metadata as BlockConfiguration<HomeFaqAttributes>, {
	edit: Edit,
	save: () => null,
});
