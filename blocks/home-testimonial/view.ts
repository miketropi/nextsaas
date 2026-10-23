import { bootBlocks } from '../../resources/ts/runtime/blocks';
bootBlocks<HTMLElement>('[data-nextsaas-testimonial]', (root) => {
 const slides=Array.from(root.querySelectorAll<HTMLElement>('.testimonial-slide')); const prev=root.querySelector<HTMLButtonElement>('[data-nextsaas-testimonial-prev]'); const next=root.querySelector<HTMLButtonElement>('[data-nextsaas-testimonial-next]'); const count=root.querySelector<HTMLElement>('[data-nextsaas-testimonial-count]'); let index=0;
 const show=(value:number):void=>{ index=(value+slides.length)%slides.length; slides.forEach((slide,i)=>{slide.hidden=i!==index}); if(count) count.textContent=`${String(index+1).padStart(2,'0')} / ${String(slides.length).padStart(2,'0')}`; };
 const back=():void=>show(index-1); const forward=():void=>show(index+1); const keys=(event:KeyboardEvent):void=>{if('ArrowLeft'===event.key||'ArrowRight'===event.key){event.preventDefault();show(index+('ArrowRight'===event.key?1:-1));}};
 prev?.addEventListener('click',back); next?.addEventListener('click',forward); root.addEventListener('keydown',keys); return()=>{prev?.removeEventListener('click',back);next?.removeEventListener('click',forward);root.removeEventListener('keydown',keys)};
});
