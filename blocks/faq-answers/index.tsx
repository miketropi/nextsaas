import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { FaqAnswersAttributes } from './types';

registerBlockType<FaqAnswersAttributes>(metadata as BlockConfiguration<FaqAnswersAttributes>, {
	edit: Edit,
	save: () => null,
});
