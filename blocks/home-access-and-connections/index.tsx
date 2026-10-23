import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { HomeAccessAndConnectionsAttributes } from './types';

registerBlockType<HomeAccessAndConnectionsAttributes>(metadata as BlockConfiguration<HomeAccessAndConnectionsAttributes>, {
	edit: Edit,
	save: () => null,
});
