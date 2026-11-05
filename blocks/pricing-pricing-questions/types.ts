import type { SectionAppearanceAttributes } from '../../resources/ts/editor/types';

export interface PricingQuestion extends Record<string,unknown>{question:string;answer:string;category:string;} export interface PricingQuestionsAttributes extends Record<string, unknown>, SectionAppearanceAttributes{sectionId:string;eyebrow:string;heading:string;ctaLabel:string;ctaUrl:string;questions:PricingQuestion[];}
