import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { Error404FindAWayAttributes } from './types';

registerBlockType<Error404FindAWayAttributes>(metadata as BlockConfiguration<Error404FindAWayAttributes>, {
	edit: Edit,
	save: () => null,
});
