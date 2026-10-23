export interface HomeBlogPostsCarouselAttributes extends Record<string, unknown> {
	sectionId: string;
	eyebrow: string;
	heading: string;
	lead: string;
	readLabel: string;
	help: string;
	emptyNotice: string;
	postsPerPage: number;
	categories: number[];
	tags: number[];
}
