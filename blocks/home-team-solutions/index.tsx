import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { HomeTeamSolutionsAttributes } from './types';

registerBlockType<HomeTeamSolutionsAttributes>(metadata as BlockConfiguration<HomeTeamSolutionsAttributes>, {
	edit: Edit,
	save: () => null,
});
