export interface PrincipleItem extends Record<string, unknown> { meta: string; heading: string; body: string; }
export interface AboutUsPrinciplesAttributes extends Record<string, unknown> { sectionId: string; eyebrow: string; heading: string; lead: string; items: PrincipleItem[]; }
