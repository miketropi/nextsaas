import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { AboutUsRoomToThinkAttributes } from './types';

registerBlockType<AboutUsRoomToThinkAttributes>(metadata as BlockConfiguration<AboutUsRoomToThinkAttributes>, {
	edit: Edit,
	save: () => null,
});
