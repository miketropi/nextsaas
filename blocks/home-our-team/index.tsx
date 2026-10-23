import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { HomeOurTeamAttributes } from './types';

registerBlockType<HomeOurTeamAttributes>(metadata as BlockConfiguration<HomeOurTeamAttributes>, {
	edit: Edit,
	save: () => null,
});
