import { bootBlocks } from '../../resources/ts/runtime/blocks';
import { initCarousel } from '../../resources/ts/runtime/carousel';
bootBlocks<HTMLElement>('[data-nextsaas-blog-carousel]', initCarousel);
