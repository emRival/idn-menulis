<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "{{ config('seo.organization.name', config('app.name')) }}",
    "url": "{{ config('seo.site_url', url('/')) }}",
    "logo": "{{ url(config('seo.logo', '/images/logo.png')) }}",
    @if(config('seo.organization.legal_name'))
    "legalName": "{{ config('seo.organization.legal_name') }}",
    @endif
    @if(config('seo.organization.founding_date'))
    "foundingDate": "{{ config('seo.organization.founding_date') }}",
    @endif
    @if(config('seo.organization.contact.email'))
    "contactPoint": {
        "@type": "ContactPoint",
        "contactType": "customer service",
        "email": "{{ config('seo.organization.contact.email') }}"
        @if(config('seo.organization.contact.phone'))
        ,"telephone": "{{ config('seo.organization.contact.phone') }}"
        @endif
    },
    @endif
    "sameAs": [
        @php
            $socials = collect(config('seo.social', []))
                ->filter(fn($url) => !empty($url) && !str_starts_with($url, '@'))
                ->values();
        @endphp
        @foreach($socials as $url)
        "{{ $url }}"@if(!$loop->last),@endif
        @endforeach
    ]
}
</script>

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "{{ config('seo.site_name', config('app.name')) }}",
    "url": "{{ config('seo.site_url', url('/')) }}",
    "description": "{{ config('seo.site_description') }}",
    "potentialAction": {
        "@type": "SearchAction",
        "target": {
            "@type": "EntryPoint",
            "urlTemplate": "{{ config('seo.site_url', url('/')) }}/cari?q={search_term_string}"
        },
        "query-input": "required name=search_term_string"
    }
}
</script>
