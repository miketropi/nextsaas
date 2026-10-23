import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { Error404NotFoundAttributes } from './types';

registerBlockType<Error404NotFoundAttributes>(metadata as BlockConfiguration<Error404NotFoundAttributes>, {
	edit: Edit,
	save: () => null,
});
