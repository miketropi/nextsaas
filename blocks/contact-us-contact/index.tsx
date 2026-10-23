import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { ContactUsContactAttributes } from './types';

registerBlockType<ContactUsContactAttributes>(metadata as BlockConfiguration<ContactUsContactAttributes>, {
	edit: Edit,
	save: () => null,
});
