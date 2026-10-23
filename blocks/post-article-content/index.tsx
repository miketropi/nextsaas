import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { PostArticleContentAttributes } from './types';

registerBlockType<PostArticleContentAttributes>(metadata as BlockConfiguration<PostArticleContentAttributes>, {
	edit: Edit,
	save: () => null,
});
