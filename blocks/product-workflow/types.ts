import type { SectionAppearanceAttributes } from '../../resources/ts/editor/types';

export interface WorkflowStep extends Record<string, unknown> {
	meta: string;
	heading: string;
	body: string;
}

export interface ProductWorkflowAttributes extends Record<string, unknown>, SectionAppearanceAttributes {
	sectionId: string;
	eyebrow: string;
	/** Rich value: the approved line break and emphasis are structural. */
	heading: string;
	lead: string;
	steps: WorkflowStep[];
	note: string;
}
