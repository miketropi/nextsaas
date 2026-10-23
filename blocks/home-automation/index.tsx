import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { HomeAutomationAttributes } from './types';

registerBlockType<HomeAutomationAttributes>(metadata as BlockConfiguration<HomeAutomationAttributes>, {
	edit: Edit,
	save: () => null,
});
