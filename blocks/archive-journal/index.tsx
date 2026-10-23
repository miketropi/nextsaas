import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { ArchiveJournalAttributes } from './types';

registerBlockType<ArchiveJournalAttributes>(metadata as BlockConfiguration<ArchiveJournalAttributes>, {
	edit: Edit,
	save: () => null,
});
