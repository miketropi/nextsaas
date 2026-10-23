import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { PostArticleHeroAttributes } from './types';

registerBlockType<PostArticleHeroAttributes>(metadata as BlockConfiguration<PostArticleHeroAttributes>, {
	edit: Edit,
	save: () => null,
});
