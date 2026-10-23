import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { SearchSiteSearchAttributes } from './types';

registerBlockType<SearchSiteSearchAttributes>(metadata as BlockConfiguration<SearchSiteSearchAttributes>, {
	edit: Edit,
	save: () => null,
});
