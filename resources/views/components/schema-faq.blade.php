@props([
    'faqs' => []
])

@if(count($faqs) > 0)
{{-- Visible FAQ Section --}}
<div class="faq-section space-y-4" itemscope itemtype="https://schema.org/FAQPage">
    @foreach($faqs as $faq)
    <div class="faq-item border border-gray-200 rounded-lg overflow-hidden" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
        <button
            type="button"
            class="w-full px-4 py-3 text-left bg-gray-50 hover:bg-gray-100 transition-colors flex justify-between items-center"
            onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('svg').classList.toggle('rotate-180')"
        >
            <h3 class="font-medium text-gray-900" itemprop="name">{{ $faq['question'] }}</h3>
            <svg class="w-5 h-5 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div class="hidden px-4 py-3 bg-white" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text" class="text-gray-700 prose prose-sm">
                {!! $faq['answer'] !!}
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- JSON-LD Schema --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        @foreach($faqs as $faq)
        {
            "@type": "Question",
            "name": "{{ e($faq['question']) }}",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "{{ e(strip_tags($faq['answer'])) }}"
            }
        }@if(!$loop->last),@endif
        @endforeach
    ]
}
</script>
@endif
