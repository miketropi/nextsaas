export interface PostRelatedReadingAttributes extends Record<string, unknown> {
	sectionId: string;
	eyebrow: string;
	heading: string;
	postsPerPage: number;
	/** Term ids. Empty means "use the post's own categories". */
	categories: number[];
	/** Term ids. Empty means "use the post's own tags". */
	tags: number[];
	readLabel: string;
	artLabel: string;
}
